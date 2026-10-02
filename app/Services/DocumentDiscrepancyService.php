<?php

namespace App\Services;

use App\Models\ActivityDocumentSignature;
use App\Models\EngineerActivityLog;
use Illuminate\Support\Str;

class DocumentDiscrepancyService
{
    /**
     * Ekstrak seluruh teks dari konten binary PDF menggunakan Smalot PDF Parser
     * dengan fallback ke internal stream parser jika parser luar tidak tersedia/gagal.
     */
    public function extractTextFromPdf(string $pdfContent): string
    {
        $text = '';

        if (class_exists(\Smalot\PdfParser\Parser::class)) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseContent($pdfContent);
                $text = $pdf->getText();
            } catch (\Throwable $e) {
                // Lanjut ke fallback internal jika parser error
            }
        }

        // Fallback internal jika parser smalot tidak ada atau menghasilkan string kosong
        if (empty(trim($text))) {
            if (preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $pdfContent, $matches)) {
                // Bangun CMap internal sederhana untuk stream heksadesimal
                $cmaps = [];
                foreach ($matches[1] as $idx => $stream) {
                    $uncomp = @gzuncompress($stream);
                    if ($uncomp && strpos($uncomp, '/Adobe-Identity-UCS') !== false) {
                        $cmaps[$idx] = $this->parseInternalCMap($uncomp);
                    }
                }

                foreach ($matches[1] as $idx => $stream) {
                    $data = @gzuncompress($stream);
                    if ($data === false) {
                        $data = $stream;
                    }

                    // 1. Literal ASCII
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

                    // 2. Hexadecimal Unicode (<00410042> Tj)
                    if (!empty($cmaps) && preg_match_all('/<([0-9a-fA-F]+)>\s*(?:Tj|TJ)/', $data, $hexMatches)) {
                        foreach ($hexMatches[1] as $hex) {
                            $decodedWord = '';
                            for ($i = 0; $i < strlen($hex); $i += 4) {
                                $charHex = substr($hex, $i, 4);
                                $val = hexdec($charHex);
                                $ch = null;
                                foreach ($cmaps as $map) {
                                    if (isset($map[$val])) {
                                        $ch = $map[$val];
                                        break;
                                    }
                                }
                                $decodedWord .= $ch ?? ' ';
                            }
                            $text .= ' ' . $decodedWord;
                        }
                    }
                }
            }
        }

        return trim(preg_replace('/\s+/', ' ', $text));
    }

    /**
     * Parser sederhana untuk Adobe-Identity-UCS CMap
     */
    protected function parseInternalCMap(string $cmapText): array
    {
        $map = [];
        if (preg_match_all('/beginbfchar\s*(.*?)\s*endbfchar/s', $cmapText, $charSections)) {
            foreach ($charSections[1] as $section) {
                if (preg_match_all('/<([0-9a-fA-F]+)>\s*<([0-9a-fA-F]+)>/', $section, $m, PREG_SET_ORDER)) {
                    foreach ($m as $match) {
                        $src = hexdec($match[1]);
                        $dstCode = hexdec($match[2]);
                        $map[$src] = mb_chr($dstCode, 'UTF-8');
                    }
                }
            }
        }
        if (preg_match_all('/beginbfrange\s*(.*?)\s*endbfrange/s', $cmapText, $rangeSections)) {
            foreach ($rangeSections[1] as $section) {
                if (preg_match_all('/<([0-9a-fA-F]+)>\s*<([0-9a-fA-F]+)>\s*<([0-9a-fA-F]+)>/', $section, $m, PREG_SET_ORDER)) {
                    foreach ($m as $match) {
                        $start = hexdec($match[1]);
                        $end = hexdec($match[2]);
                        $destStart = hexdec($match[3]);
                        for ($c = $start; $c <= $end; $c++) {
                            $map[$c] = mb_chr($destStart + ($c - $start), 'UTF-8');
                        }
                    }
                }
            }
        }
        return $map;
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
                'total_discrepancies' => 0,
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
                'total_discrepancies' => 0,
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

        // 3. Uji Keabsahan Baris demi Baris Tabel Aktivitas secara Fleksibel
        $uploadedRows = [];
        preg_match_all('/(?:^|\n|\s)\s*(\d{1,3})\s+(\d{2}[\/\-]\d{2}[\/\-]\d{4})\s+(.+?)(?=(?:\s+\d{1,3}\s+\d{2}[\/\-]\d{2}[\/\-]\d{4})|\s*(?:TOTAL|Diperiksa|Mengetahui|Jakarta|Dibuat)|$)/si', $extractedText, $rMatches, PREG_SET_ORDER);

        if (!empty($rMatches)) {
            foreach ($rMatches as $rm) {
                $uploadedRows[(int)$rm[1]] = [
                    'date' => trim($rm[2]),
                    'raw'  => trim(preg_replace('/\s+/', ' ', $rm[3])),
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
                // Fallback jika baris individual tidak terdeteksi
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
            'is_hash_identical'   => ($uploadedHash === $document->verification_hash),
            'discrepancies'       => $discrepancies,
            'total_discrepancies' => count($discrepancies),
            'extracted_text_snippet' => substr($extractedText, 0, 300) . '...',
        ];
    }
}
