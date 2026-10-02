<?php

namespace App\Services;

use App\Models\ActivityDocumentSignature;
use App\Models\EngineerActivityLog;
use Illuminate\Support\Str;
use Smalot\PdfParser\Parser;

class DocumentDiscrepancyService
{
    /**
     * Ekstrak seluruh teks dari konten binary PDF menggunakan Smalot PDF Parser
     * dengan fallback ke regex stream extraction jika parser gagal.
     */
    public function extractTextFromPdf(string $pdfContent): string
    {
        $text = '';

        try {
            $parser = new Parser();
            $pdf = $parser->parseContent($pdfContent);
            $text = $pdf->getText();
        } catch (\Throwable $e) {
            // Abaikan error dan lanjut ke fallback parser
        }

        // Fallback jika parser smalot menghasilkan string kosong
        if (empty(trim($text))) {
            if (preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $pdfContent, $matches)) {
                foreach ($matches[1] as $stream) {
                    $data = @gzuncompress($stream);
                    if ($data === false) {
                        $data = $stream;
                    }
                    if (preg_match_all('/\((.*?)\)\s*(?:Tj|\'|\")/s', $data, $textMatches)) {
                        $text .= ' ' . implode(' ', $textMatches[1]);
                    }
                    if (preg_match_all('/\[(.*?)\]\s*TJ/s', $data, $tjMatches)) {
                        foreach ($tjMatches[1] as $tj) {
                            if (preg_match_all('/\((.*?)\)/s', $tj, $subMatches)) {
                                $text .= ' ' . implode('', $subMatches[1]);
                            }
                        }
                    }
                }
            }
        }

        return trim($text);
    }

    /**
     * Deteksi nomor dokumen secara cerdas dari berbagai sumber:
     * 1. Regex langsung di teks PDF
     * 2. Regex pada teks setelah normalisasi whitespace
     * 3. Regex pada raw content PDF (metadata/streams)
     * 4. Regex pada nama file yang diunggah
     * 5. Pencocokan nama proyek / PIC terhadap arsip database
     */
    public function detectDocumentNumber(string $pdfText, string $pdfContent = '', string $originalFilename = ''): ?string
    {
        // 1. Direct match di teks PDF
        if (preg_match('/IPNET-ACT-\d{6}-[A-Za-z0-9]+/i', $pdfText, $matches)) {
            return strtoupper(trim($matches[0]));
        }

        // 2. Normalisasi whitespace (misal: "IPNET-ACT-202610- 0001")
        $collapsed = preg_replace('/[\s\r\n]+/', '', $pdfText);
        if (preg_match('/IPNET[-_]?ACT[-_]?\d{6}[-_]?[A-Za-z0-9]+/i', $collapsed, $matches)) {
            if (preg_match('/IPNET[-_]?ACT[-_]?(\d{6})[-_]?([A-Za-z0-9]+)/i', $collapsed, $m)) {
                return "IPNET-ACT-{$m[1]}-{$m[2]}";
            }
        }

        // 3. Pencarian pada raw PDF content / metadata
        if (preg_match('/IPNET-ACT-\d{6}-[A-Za-z0-9]+/i', $pdfContent, $matches)) {
            return strtoupper(trim($matches[0]));
        }

        // 4. Pencarian pada nama file yang diunggah
        if ($originalFilename && preg_match('/IPNET-ACT-\d{6}-[A-Za-z0-9]+/i', $originalFilename, $matches)) {
            return strtoupper(trim($matches[0]));
        }

        // 5. Pencocokan cerdas berdasarkan Nama Proyek & PIC yang tertulis di PDF
        $signatures = ActivityDocumentSignature::all();
        foreach ($signatures as $sig) {
            $projName = trim($sig->project_name ?? '');
            $picName  = trim($sig->pic_name ?? '');

            if ($projName && stripos($pdfText, $projName) !== false) {
                return $sig->document_number;
            }

            if ($originalFilename && $projName) {
                $slug = Str::slug($projName, '_');
                if (stripos($originalFilename, $slug) !== false) {
                    return $sig->document_number;
                }
            }

            if ($picName && stripos($pdfText, $picName) !== false) {
                return $sig->document_number;
            }
        }

        return null;
    }

    /**
     * Inspeksi selisih (discrepancy detection) antara file PDF yang diunggah vs arsip resmi server
     */
    public function inspect(string $pdfContent, ?string $manualDocNumber = null, string $originalFilename = ''): array
    {
        $uploadedHash = hash('sha256', $pdfContent);
        $extractedText = $this->extractTextFromPdf($pdfContent);

        $docNumber = $manualDocNumber ?: $this->detectDocumentNumber($extractedText, $pdfContent, $originalFilename);

        if (!$docNumber) {
            return [
                'status'         => 'invalid_format',
                'is_authentic'   => false,
                'message'        => 'Format dokumen tidak dikenali. Nomor dokumen IP-Net tidak ditemukan di dalam berkas PDF.',
                'uploaded_hash'  => $uploadedHash,
                'document'       => null,
                'discrepancies'  => [],
            ];
        }

        $document = ActivityDocumentSignature::with(['project', 'picUser', 'leadUser'])
            ->where('document_number', $docNumber)
            ->first();

        if (!$document) {
            return [
                'status'          => 'not_found',
                'is_authentic'    => false,
                'message'         => "Nomor dokumen [{$docNumber}] tidak terdaftar pada basis data resmi PT IP Network Solusindo.",
                'document_number' => $docNumber,
                'uploaded_hash'   => $uploadedHash,
                'document'        => null,
                'discrepancies'   => [],
            ];
        }

        // Ambil aktivitas resmi dari database
        $query = EngineerActivityLog::with(['engineer', 'project']);
        if (!empty($document->log_ids)) {
            $query->whereIn('id', $document->log_ids);
        } elseif ($document->project_id) {
            $query->where('project_id', $document->project_id);
        } elseif ($document->scope_key && str_starts_with($document->scope_key, 'proj_')) {
            $pId = (int) substr($document->scope_key, 5);
            $query->where('project_id', $pId);
        } elseif ($document->pic_user_id) {
            $query->where('user_id', $document->pic_user_id);
        }

        $officialActivities = $query->orderBy('activity_date', 'asc')->orderBy('start_time', 'asc')->orderBy('id', 'asc')->get();

        $discrepancies = [];

        // 1. Cek Kredensial & Nama Penandatangan
        if (!empty($document->pic_name) && !str_contains($extractedText, $document->pic_name)) {
            $discrepancies[] = [
                'field'          => 'PIC Lapangan',
                'expected'       => $document->pic_name,
                'found_in_file'  => 'Tidak ditemukan / Berbeda',
                'severity'       => 'high',
                'desc'           => 'Nama penandatangan PIC Lapangan tidak sesuai dengan rekaman resmi.',
            ];
        }

        if (!empty($document->lead_name) && !str_contains($extractedText, $document->lead_name)) {
            $discrepancies[] = [
                'field'          => 'Lead Engineer',
                'expected'       => $document->lead_name,
                'found_in_file'  => 'Tidak ditemukan / Berbeda',
                'severity'       => 'high',
                'desc'           => 'Nama pemeriksa teknis (Lead) tidak sesuai dengan rekaman resmi.',
            ];
        }

        if (!empty($document->head_name) && !str_contains($extractedText, $document->head_name)) {
            $discrepancies[] = [
                'field'          => 'Head of Division',
                'expected'       => $document->head_name,
                'found_in_file'  => 'Tidak ditemukan / Berbeda',
                'severity'       => 'high',
                'desc'           => 'Nama otorisasi manajemen tidak sesuai dengan rekaman resmi.',
            ];
        }

        // 2. Cek Kesesuaian Nama Proyek
        if (!empty($document->project_name) && !str_contains($extractedText, $document->project_name)) {
            $discrepancies[] = [
                'field'          => 'Nama Proyek / Scope',
                'expected'       => $document->project_name,
                'found_in_file'  => 'Berbeda / Dimodifikasi',
                'severity'       => 'medium',
                'desc'           => 'Nama proyek di berkas tidak cocok dengan data penugasan.',
            ];
        }

        // 3. Uji Keabsahan Baris demi Baris Tabel Aktivitas
        $uploadedRows = [];
        if (preg_match('/No\s+Tanggal\s+Uraian\s+Aktivitas.*?Notes\s+(.*?)\s+TOTAL AKTIVITAS/si', $extractedText, $tableMatch)) {
            preg_match_all('/(\d+)\s+(\d{2}\/\d{2}\/\d{4})\s+(.+?)(?=(?:\r?\n\s*\d+\s+\d{2}\/\d{2}\/\d{4})|$)/s', trim($tableMatch[1]), $rMatches, PREG_SET_ORDER);
            foreach ($rMatches as $rm) {
                $uploadedRows[(int)$rm[1]] = [
                    'date' => trim($rm[2]),
                    'raw'  => trim($rm[3]),
                ];
            }
        }

        $activityIndex = 1;
        foreach ($officialActivities as $act) {
            $desc = trim($act->description ?? '');
            if (preg_match('/^\[(.+?)\]\s*(.*)$/s', $desc, $m)) {
                $desc = $m[2] ?: $m[1];
            }
            $normalizedDesc = trim(preg_replace('/\s+/', ' ', $desc));

            // Uraikan notes resmi
            $rawNotes  = $act->notes ?? '';
            $clientPic = '';
            $ipnetPic  = '';
            $notedOnly = $rawNotes;
            if ($rawNotes) {
                $parts = array_map('trim', explode('|', $rawNotes));
                $notedParts = [];
                foreach ($parts as $part) {
                    if (str_starts_with($part, 'PIC Klien:')) {
                        $clientPic = trim(substr($part, strlen('PIC Klien:')));
                    } elseif (str_starts_with($part, 'PIC IPNET:')) {
                        $ipnetPic = trim(substr($part, strlen('PIC IPNET:')));
                    } else {
                        $notedParts[] = $part;
                    }
                }
                $notedOnly = implode(' | ', array_filter($notedParts));
            }

            if (isset($uploadedRows[$activityIndex])) {
                $upRow = $uploadedRows[$activityIndex];
                $upRaw = $upRow['raw'];

                // 3a. Cek deskripsi aktivitas
                if (!empty($normalizedDesc) && !str_contains($upRaw, $normalizedDesc)) {
                    $tokens = preg_split('/\s+/', $upRaw);
                    $foundSnippet = $tokens[0] ?? 'Berbeda';

                    $discrepancies[] = [
                        'field'         => "Baris {$activityIndex}: Uraian Aktivitas",
                        'expected'      => $normalizedDesc,
                        'found_in_file' => $foundSnippet,
                        'severity'      => 'critical',
                        'desc'          => "Deskripsi pekerjaan pada baris No. {$activityIndex} diubah dari arsip asli.",
                    ];
                }

                // 3b. Cek catatan (Notes)
                if (!empty($notedOnly) && !str_contains($upRaw, $notedOnly)) {
                    $tokens = preg_split('/\s+/', $upRaw);
                    $foundNotes = end($tokens) ?: 'Berbeda';

                    $discrepancies[] = [
                        'field'         => "Baris {$activityIndex}: Catatan (Notes)",
                        'expected'      => $notedOnly,
                        'found_in_file' => $foundNotes,
                        'severity'      => 'critical',
                        'desc'          => "Catatan pekerjaan pada baris No. {$activityIndex} dimanipulasi.",
                    ];
                }

                // 3c. Cek PIC Klien
                if (!empty($clientPic) && !str_contains($upRaw, $clientPic)) {
                    $discrepancies[] = [
                        'field'         => "Baris {$activityIndex}: PIC Klien",
                        'expected'      => $clientPic,
                        'found_in_file' => 'Berbeda',
                        'severity'      => 'medium',
                        'desc'          => "Nama PIC Klien pada baris No. {$activityIndex} tidak sesuai.",
                    ];
                }
            } else {
                // Fallback jika pemisahan baris tabel tidak terurai spesifik
                if (!empty($normalizedDesc) && !str_contains($extractedText, $normalizedDesc)) {
                    $discrepancies[] = [
                        'field'         => "Catatan Aktivitas No. {$activityIndex}",
                        'expected'      => $normalizedDesc,
                        'found_in_file' => 'Teks Berbeda / Terhapus',
                        'severity'      => 'critical',
                        'desc'          => "Deskripsi pekerjaan pada baris No. {$activityIndex} diubah atau tidak sesuai dengan data asli.",
                    ];
                }
            }

            $activityIndex++;
        }

        $isAuthentic = empty($discrepancies) && ($document->status === 'fully_approved');

        return [
            'status'              => $isAuthentic ? 'authentic' : 'discrepancy_detected',
            'is_authentic'        => $isAuthentic,
            'message'             => $isAuthentic 
                ? 'Dokumen dinyatakan ASLI, OTENTIK, dan 100% SESUAI dengan arsip resmi PT IP Network Solusindo.' 
                : 'PERINGATAN: Terdeteksi ketidaksesuaian (Discrepancy) antara berkas PDF yang diunggah dengan arsip resmi bertanda tangan digital di server.',
            'document_number'     => $docNumber,
            'document'            => $document,
            'official_activities' => $officialActivities,
            'official_hash'       => $document->verification_hash,
            'uploaded_hash'       => $uploadedHash,
            'discrepancies'       => $discrepancies,
            'total_discrepancies' => count($discrepancies),
            'extracted_text_snippet' => substr($extractedText, 0, 300) . '...',
        ];
    }
}
