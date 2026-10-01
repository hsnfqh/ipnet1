<?php

namespace App\Services;

use App\Models\ActivityDocumentSignature;
use App\Models\EngineerActivityLog;

class DocumentDiscrepancyService
{
    /**
     * Ekstrak seluruh teks dari konten binary PDF tanpa dependensi luar
     */
    public function extractTextFromPdf(string $pdfContent): string
    {
        $text = '';
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
        return trim($text);
    }

    /**
     * Deteksi nomor dokumen dari teks PDF
     */
    public function detectDocumentNumber(string $pdfText): ?string
    {
        if (preg_match('/IPNET-ACT-\d{6}-[A-Za-z0-9]+/i', $pdfText, $matches)) {
            return strtoupper($matches[0]);
        }
        return null;
    }

    /**
     * Inspeksi selisih (discrepancy detection) antara file PDF yang diunggah vs arsip resmi server
     */
    public function inspect(string $pdfContent, ?string $manualDocNumber = null): array
    {
        $uploadedHash = hash('sha256', $pdfContent);
        $extractedText = $this->extractTextFromPdf($pdfContent);

        $docNumber = $manualDocNumber ?: $this->detectDocumentNumber($extractedText);

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
                'status'         => 'not_found',
                'is_authentic'   => false,
                'message'        => "Nomor dokumen [{$docNumber}] tidak terdaftar pada basis data resmi PT IP Network Solusindo.",
                'document_number'=> $docNumber,
                'uploaded_hash'  => $uploadedHash,
                'document'       => null,
                'discrepancies'  => [],
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

        $officialActivities = $query->orderBy('activity_date', 'asc')->orderBy('start_time', 'asc')->get();

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
                'desc'           => 'Nama proyek di file tidak cocok dengan data penugasan.',
            ];
        }

        // 3. Cek Baris Aktivitas Pekerjaan
        $activityIndex = 1;
        foreach ($officialActivities as $act) {
            $desc = trim($act->description ?? '');
            if (preg_match('/^\[(.+?)\]\s*(.*)$/s', $desc, $m)) {
                $desc = $m[2] ?: $m[1];
            }

            // Normalisasi teks untuk pencarian substring
            $normalizedDesc = trim(preg_replace('/\s+/', ' ', $desc));

            if (!empty($normalizedDesc) && !str_contains($extractedText, $normalizedDesc)) {
                // Ambil sebagian kata jika terlalu panjang
                $words = explode(' ', $normalizedDesc);
                $searchSnippet = implode(' ', array_slice($words, 0, min(4, count($words))));

                if (!str_contains($extractedText, $searchSnippet)) {
                    $discrepancies[] = [
                        'field'          => "Catatan Aktivitas No. {$activityIndex}",
                        'expected'       => $normalizedDesc,
                        'found_in_file'  => 'Teks Berbeda / Terhapus',
                        'severity'       => 'critical',
                        'desc'           => "Deskripsi pekerjaan pada baris No. {$activityIndex} diubah atau tidak sesuai dengan data asli.",
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
                : 'PERINGATAN: Terdeteksi ketidaksesuaian (Discrepancy) antara file PDF yang diunggah dengan arsip resmi bertanda tangan digital di server.',
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
