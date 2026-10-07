<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Laporan Aktivitas Engineer - PT IP Network Solusindo</title>
    <style>
        @page {
            margin: 9mm 12mm 9mm 12mm;
            size: a4 portrait;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 7.4pt;
            color: #000000;
            line-height: 1.28;
            margin: 0;
            padding: 0;
        }
        .page-container {
            width: 100%;
            height: 275mm;
            position: relative;
        }
        .page-break {
            page-break-after: always;
        }

        /* ── Header Dokumen ── */
        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 2px;
            margin-bottom: 6px;
        }
        .doc-header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .doc-title {
            font-size: 13.5pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: center;
        }
        .doc-intro {
            font-size: 7.2pt;
            color: #1E293B;
            margin-top: 5px;
            margin-bottom: 14px;
            line-height: 1.25;
        }

        /* ── Judul Section ── */
        .section-title {
            font-size: 8.4pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            margin-top: 8px;
            margin-bottom: 3px;
            letter-spacing: 0.2px;
        }

        /* ── Tabel Standar ── */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            table-layout: fixed;
        }
        .report-table th {
            background-color: #EBF3FB;
            color: #000000;
            font-weight: bold;
            font-size: 7.2pt;
            padding: 3.5px 3.5px;
            border: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
        }
        .report-table td {
            font-size: 7.2pt;
            padding: 3.2px 4.2px;
            border: 1px solid #000000;
            vertical-align: middle;
            color: #000000;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.25;
        }
        .report-table td.label-cell {
            font-weight: bold;
            background-color: #FFFFFF;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .align-top { vertical-align: top !important; }

        /* ── Checkbox Resmi ── */
        .check-box {
            display: inline-block;
            font-family: 'DejaVu Sans', sans-serif;
            width: 9.5px;
            height: 9.5px;
            border: 1px solid #000000;
            text-align: center;
            line-height: 9px;
            font-size: 8pt;
            font-weight: bold;
            vertical-align: middle;
            margin-right: 3px;
            margin-bottom: 1px;
        }

        /* ── Footer Dokumen ── */
        .doc-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            padding-top: 4px;
            border-top: 1px solid #000000;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        .footer-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
            font-size: 6pt;
            color: #000000;
            line-height: 1.2;
        }

        /* ── Kotak Foto Dokumentasi ── */
        .photo-placeholder {
            width: 100%;
            height: 82px;
            border: 1px dashed #94A3B8;
            background-color: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #64748B;
            font-size: 7pt;
            font-weight: bold;
            padding: 4px;
        }
        .photo-img {
            max-width: 100%;
            max-height: 82px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* ── Kotak Tanda Tangan ── */
        .sig-container {
            height: 48px;
            text-align: center;
            vertical-align: middle;
        }
        .sig-img {
            max-height: 44px;
            max-width: 110px;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
@php
    // Inisialisasi Data & Fallback
    $docSig = $documentSignature ?? null;
    $rData  = $docSig?->report_data ?? [];

    $firstAct = !empty($activities) ? (is_array($activities) ? ($activities[0] ?? null) : $activities->first()) : null;
    $lastAct  = !empty($activities) ? (is_array($activities) ? (end($activities) ?: null) : $activities->last()) : null;
    $firstActObj = is_object($firstAct) ? $firstAct : (object) ($firstAct ?? []);
    $lastActObj  = is_object($lastAct) ? $lastAct : (object) ($lastAct ?? []);
    $project  = (is_object($firstAct) && isset($firstAct->project)) ? $firstAct->project : null;

    // Normalisasi parsedActivities
    $actItems = [];
    if (!empty($parsedActivities)) {
        if (is_array($parsedActivities)) {
            $actItems = $parsedActivities;
        } elseif (is_object($parsedActivities) && method_exists($parsedActivities, 'toArray')) {
            $actItems = $parsedActivities->toArray();
        } else {
            $actItems = (array) $parsedActivities;
        }
    } elseif (!empty($activities) && (is_array($activities) || is_iterable($activities))) {
        $actItems = collect($activities)->map(function($a, $idx) {
            $aObj = is_object($a) ? $a : (object) $a;
            $rawNotes  = $aObj->notes ?? '';
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

            $desc = $aObj->description ?? ($aObj->activity ?? '-');
            if (preg_match('/^\[(.+?)\]\s*(.*)$/s', $desc, $m)) {
                $desc = $m[2] ?: $m[1];
            }

            return [
                'no'         => $idx + 1,
                'activity'   => $desc,
                'date'       => !empty($aObj->activity_date) ? \Carbon\Carbon::parse($aObj->activity_date)->format('d/m/Y') : (!empty($aObj->date) ? $aObj->date : '-'),
                'time'       => !empty($aObj->start_time) ? \Carbon\Carbon::parse($aObj->start_time)->format('H:i') : (!empty($aObj->time) ? $aObj->time : '-'),
                'location'   => $aObj->location ?? '-',
                'status'     => $aObj->status ?? 'Selesai',
                'client_pic' => $clientPic ?: ($aObj->client_pic ?? '-'),
                'ipnet_pic'  => $ipnetPic ?: (is_object($aObj->engineer ?? null) ? $aObj->engineer->name : ($aObj->ipnet_pic ?? '-')),
                'notes'      => $notedOnly ?: ($aObj->notes ?? '-'),
            ];
        })->toArray();
    }

    // Logo Base64
    if (empty($logoBase64)) {
        $logoPath = public_path('images/ipnet1.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('images/ipnet.png');
        }
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
    }

    // Nilai Bagian A (Identitas Pekerjaan)
    $rawHariTgl = $rData['identitas']['hari_tanggal'] ?? null;
    if (!empty($rawHariTgl)) {
        try {
            $hariTanggal = \Carbon\Carbon::parse($rawHariTgl)->locale('id')->isoFormat('dddd, D MMMM Y');
        } catch (\Throwable $e) {
            $hariTanggal = $rawHariTgl;
        }
    } else {
        $actDateVal = $firstActObj->activity_date ?? ($firstActObj->date ?? null);
        $actDate = $actDateVal ? \Carbon\Carbon::parse($actDateVal) : now();
        $hariTanggal = $actDate->locale('id')->isoFormat('dddd, D MMMM Y');
    }

    $noLaporan   = !empty($rData['identitas']['no_laporan']) ? $rData['identitas']['no_laporan'] : (($verifyDocNumber ?? null) ?: ($docSig?->document_number ?? 'IPNET-ACT-' . date('Ym') . '-0001'));
    $namaProject = !empty($rData['identitas']['nama_project']) ? $rData['identitas']['nama_project'] : (($projectName ?? null) ?: ($project?->name ?? 'Project Technical Support'));
    $noSoSpk     = isset($rData['identitas']['no_so_spk']) ? ($rData['identitas']['no_so_spk'] ?: '-') : ($project?->po_number ?? '-');
    $lokasiSite  = isset($rData['identitas']['lokasi_site']) ? ($rData['identitas']['lokasi_site'] ?: '-') : ($project?->location ?? ($firstActObj->location ?? '-'));
    $workOrder   = isset($rData['identitas']['work_order']) ? ($rData['identitas']['work_order'] ?: '-') : '-';
    $namaEngineer= !empty($rData['identitas']['nama_engineer']) ? $rData['identitas']['nama_engineer'] : (($engineerName ?? null) ?: ($docSig?->pic_name ?? (auth()->user()?->name ?? 'PIC Engineer')));
    $customer    = isset($rData['identitas']['customer']) ? ($rData['identitas']['customer'] ?: '-') : ($project?->client ?? '-');
    $jenisPekerjaan = isset($rData['identitas']['jenis_pekerjaan']) ? ($rData['identitas']['jenis_pekerjaan'] ?: '-') : ($firstActObj->activity_type ?? 'Implementasi / Troubleshooting');
    $picCustomer = isset($rData['identitas']['pic_customer']) ? ($rData['identitas']['pic_customer'] ?: '-') : ($project?->customer_pic_technical ?? (!empty($actItems[0]['client_pic']) && $actItems[0]['client_pic'] !== '-' ? $actItems[0]['client_pic'] : '-'));

    $isImplement = !empty($rData['identitas']['kategori_implementasi'])
        || in_array('implement', (array)($rData['identitas']['kategori_pekerjaan'] ?? []))
        || ($rData['identitas']['kategori_pekerjaan'] ?? '') === 'implement'
        || str_contains(strtolower($rData['identitas']['kategori_pekerjaan'] ?? ''), 'implement');

    $isManagedService = !empty($rData['identitas']['kategori_managed_service'])
        || in_array('managed_service', (array)($rData['identitas']['kategori_pekerjaan'] ?? []))
        || ($rData['identitas']['kategori_pekerjaan'] ?? '') === 'managed_service'
        || str_contains(strtolower($rData['identitas']['kategori_pekerjaan'] ?? ''), 'managed')
        || str_contains(strtolower($rData['identitas']['kategori_pekerjaan'] ?? ''), 'maintenance');

    $jabatanEngineer = isset($rData['identitas']['jabatan']) ? ($rData['identitas']['jabatan'] ?: '-') : ($docSig?->pic_title ?? (auth()->user()?->position ?: 'Network Leader'));

    $rawJamMulai = $rData['identitas']['jam_mulai'] ?? null;
    if (!empty($rawJamMulai)) {
        $jamMulai = $rawJamMulai . (!str_contains(strtolower($rawJamMulai), 'wib') ? ' WIB' : '');
    } else {
        $jamMulai = !empty($firstActObj->start_time) ? \Carbon\Carbon::parse($firstActObj->start_time)->format('H:i') . ' WIB' : (!empty($firstActObj->time) ? $firstActObj->time . ' WIB' : '09:00 WIB');
    }

    $rawJamSelesai = $rData['identitas']['jam_selesai'] ?? null;
    if (!empty($rawJamSelesai)) {
        $jamSelesai = $rawJamSelesai . (!str_contains(strtolower($rawJamSelesai), 'wib') ? ' WIB' : '');
    } else {
        $jamSelesai = !empty($lastActObj->end_time) ? \Carbon\Carbon::parse($lastActObj->end_time)->format('H:i') . ' WIB' : (!empty($lastActObj->start_time) ? \Carbon\Carbon::parse($lastActObj->start_time)->format('H:i') . ' WIB' : '17:00 WIB');
    }

    // Nilai Bagian B (Komposisi Tenaga Kerja)
    $manpowerList = is_array($rData['manpower'] ?? null) ? $rData['manpower'] : [];
    if (empty($manpowerList)) {
        $collectedNames = collect();
        if (!empty($activities)) {
            foreach ($activities as $a) {
                $aObj = is_object($a) ? $a : (object) $a;
                $engName = is_object($aObj->engineer ?? null) ? $aObj->engineer->name : ($aObj->ipnet_pic ?? ($aObj->engineer_name ?? null));
                if (!empty($engName) && $engName !== '-') {
                    $collectedNames->push([
                        'nama'       => $engName,
                        'unit_kerja' => (is_object($aObj->engineer ?? null) ? $aObj->engineer->division?->name : null) ?? 'Technical Support',
                        'jabatan'    => (is_object($aObj->engineer ?? null) ? $aObj->engineer->position : null) ?? 'Field Engineer',
                        'keterangan' => 'PIC Kontributor',
                    ]);
                }
            }
        }
        if ($collectedNames->isEmpty()) {
            $collectedNames->push([
                'nama'       => $namaEngineer,
                'unit_kerja' => auth()->user()?->division?->name ?? 'Technical Support',
                'jabatan'    => $jabatanEngineer,
                'keterangan' => 'PIC Utama',
            ]);
        }
        $manpowerList = $collectedNames->unique('nama')->values()->toArray();
    }
    $totalTenagaKerja = $rData['total_tenaga_kerja'] ?? count($manpowerList);

    // Nilai Bagian C (Ruang Lingkup / Target Pekerjaan)
    $scopeC = $rData['ruang_lingkup'] ?? [];
    $hasScopeC = isset($rData['ruang_lingkup']);
    $firstDesc = $firstActObj->description ?? ($firstActObj->activity ?? '');
    $targetHariIni   = $hasScopeC ? ($scopeC['target_hari_ini'] ?: '-') : (!empty($firstDesc) ? (preg_match('/^\[(.+?)\]\s*(.*)$/s', $firstDesc, $m) ? $m[1] : $firstDesc) : ($namaProject . ' - Kegiatan Lapangan'));
    $durasiProject   = $hasScopeC ? ($scopeC['durasi_project'] ?: '-') : '-';
    $scopePekerjaan  = $hasScopeC ? ($scopeC['scope_pekerjaan'] ?: '-') : ($project?->description ?: '-');
    $perangkatSistem = $hasScopeC ? ($scopeC['perangkat_sistem'] ?: '-') : '-';
    $kriteriaSelesai = $hasScopeC ? ($scopeC['kriteria_selesai'] ?: '-') : '-';

    // Nilai Bagian D (Rincian Aktivitas)
    $dActivities = $rData['rincian_aktivitas'] ?? [];
    if (is_object($dActivities) && method_exists($dActivities, 'toArray')) {
        $dActivities = $dActivities->toArray();
    } elseif (!is_array($dActivities)) {
        $dActivities = (array) $dActivities;
    }

    if (empty($dActivities)) {
        if (!empty($actItems)) {
            $dActivities = [];
            foreach (array_values($actItems) as $i => $it) {
                $it = (array) $it;
                $dActivities[] = [
                    'no'           => $i + 1,
                    'waktu'        => $it['time'] ?? ($it['date'] ?? '-'),
                    'aktivitas'    => $it['activity'] ?? ($it['description'] ?? '-'),
                    'perangkat'    => $it['location'] ?? '-',
                    'hasil'        => '-',
                    'status'       => $it['status'] ?? 'Selesai',
                    'kendala'      => '-',
                    'tindak_lanjut'=> !empty($it['notes']) && $it['notes'] !== '-' ? $it['notes'] : '-',
                ];
            }
        } else {
            $dActivities = [[
                'no' => 1, 'waktu' => '09:00', 'aktivitas' => '-',
                'perangkat' => '-', 'hasil' => '-', 'status' => 'Selesai', 'kendala' => '-', 'tindak_lanjut' => '-'
            ]];
        }
    }

    // Nilai Bagian E, F, G
    $materials   = is_array($rData['materials'] ?? null) ? $rData['materials'] : [];
    $testResults = is_array($rData['test_results'] ?? null) ? $rData['test_results'] : [];
    $incidents   = is_array($rData['incidents'] ?? null) ? $rData['incidents'] : [];

    // Nilai Bagian H (Hasil Akhir Pekerjaan)
    $hasilAkhir = $rData['hasil_akhir'] ?? [];
    $hasHasilAkhir = isset($rData['hasil_akhir']);
    $statusPekerjaan = strtolower($hasilAkhir['status_pekerjaan'] ?? 'selesai');
    $progressPercent = $hasilAkhir['progress_percent'] ?? ($project?->progress ?? '100');
    $kondisiSistem   = $hasHasilAkhir ? ($hasilAkhir['kondisi_sistem'] ?: '-') : '-';
    $outstanding     = $hasHasilAkhir ? ($hasilAkhir['outstanding'] ?: '-') : '-';
    $rekomendasi     = $hasHasilAkhir ? ($hasilAkhir['rekomendasi'] ?: '-') : '-';
    $eskalasiPic     = $hasHasilAkhir ? ($hasilAkhir['eskalasi_pic'] ?: '-') : '-';

    // Nilai Bagian I (Dokumentasi Foto)
    $photos = $rData['foto_dokumentasi'] ?? [];
    $beforePhoto   = $photos['before'] ?? null;
    $progressPhoto = $photos['progress'] ?? null;
    $afterPhoto    = $photos['after'] ?? null;

    // Nilai Bagian K (Catatan Administrasi Dokumen)
    $adminK = $rData['administrasi'] ?? [];
    $nomorWoTicket = $adminK['nomor_wo'] ?? ($workOrder !== '-' ? $workOrder : ($project?->id ? 'WO-IPNET-' . str_pad($project->id, 5, '0', STR_PAD_LEFT) : '-'));
    $nomorBaBast   = $adminK['nomor_ba'] ?? '-';
    $lampiranExtra = $adminK['lampiran'] ?? 'Dokumentasi Foto Fisik & Checklist';
    $namaFileFolder= $adminK['folder'] ?? ('DOK-' . \Illuminate\Support\Str::slug($namaProject, '_'));

    // Data TTD / Verifikasi Bagian J
    $picSignerName  = !empty($rData['identitas']['nama_engineer']) ? $rData['identitas']['nama_engineer'] : ($docSig?->pic_name ?? $namaEngineer);
    $leadSignerName = !empty($rData['identitas']['nama_leader']) ? $rData['identitas']['nama_leader'] : ($docSig?->lead_name ?? ($docSig?->leadUser?->name ?? 'Nugraha Pratama'));
    $custSignerName = (!empty($rData['identitas']['pic_customer']) && $rData['identitas']['pic_customer'] !== '-') ? $rData['identitas']['pic_customer'] : ($picCustomer !== '-' ? $picCustomer : 'PIC Site / Representative');

    $picSignedAt  = $docSig?->pic_signed_at ? $docSig->pic_signed_at->format('d/m/Y') : ($hariTanggal ? date('d/m/Y') : '-');
    $leadSignedAt = $docSig?->lead_signed_at ? $docSig->lead_signed_at->format('d/m/Y') : ($docSig?->pic_signed_at ? $docSig->pic_signed_at->format('d/m/Y') : '-');
    $custSignedAt = date('d/m/Y');
@endphp

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- ═════════════════════════ HALAMAN 1 ════════════════════════════ --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div class="page-container page-break">

    {{-- KOP SURAT / HEADER --}}
    <table class="doc-header-table">
        <tr>
            <td style="width: 15%; text-align: left; vertical-align: middle;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo" style="height: 38px; width: auto; max-width: 80px; display: block;">
                @endif
            </td>
            <td style="width: 85%; text-align: center; vertical-align: middle;">
                <div class="doc-title">FORM LAPORAN AKTIVITAS ENGINEER – PROJECT</div>
            </td>
        </tr>
    </table>

    <div class="doc-intro">
        Dokumen ini digunakan sebagai laporan aktivitas engineer di lokasi pekerjaan dan sebagai bukti pelaksanaan pekerjaan lapangan.
    </div>

    {{-- A. IDENTITAS PEKERJAAN --}}
    <div class="section-title">A. IDENTITAS PEKERJAAN</div>
    <table class="report-table">
        <tr>
            <td class="label-cell" style="width: 20%;">Hari/Tanggal</td>
            <td style="width: 30%;">{{ $hariTanggal }}</td>
            <td class="label-cell" style="width: 20%;">No. Laporan</td>
            <td style="width: 30%;">{{ $noLaporan }}</td>
        </tr>
        <tr>
            <td class="label-cell">Nama Project</td>
            <td>{{ $namaProject }}</td>
            <td class="label-cell">No. SO / SPK / Contract</td>
            <td>{{ $noSoSpk }}</td>
        </tr>
        <tr>
            <td class="label-cell">Lokasi / Site</td>
            <td>{{ $lokasiSite }}</td>
            <td class="label-cell">Work Order / WO</td>
            <td>{{ $workOrder }}</td>
        </tr>
        <tr>
            <td class="label-cell">Nama Engineer</td>
            <td>{{ $namaEngineer }}</td>
            <td class="label-cell">Customer</td>
            <td>{{ $customer }}</td>
        </tr>
        <tr>
            <td class="label-cell">Jenis Pekerjaan</td>
            <td>{{ $jenisPekerjaan }}</td>
            <td class="label-cell">PIC Customer</td>
            <td>{{ $picCustomer }}</td>
        </tr>
        <tr>
            <td class="label-cell">Kategori Pekerjaan</td>
            <td>
                <span class="check-box">{!! $isImplement ? '&#10003;' : '' !!}</span> Implementasi
                &nbsp;&nbsp;&nbsp;
                <span class="check-box">{!! $isManagedService ? '&#10003;' : '' !!}</span> Managed Service
            </td>
            <td class="label-cell">Jabatan</td>
            <td>{{ $jabatanEngineer }}</td>
        </tr>
        <tr>
            <td class="label-cell">Jam Mulai</td>
            <td>{{ $jamMulai }}</td>
            <td class="label-cell">Jam Selesai</td>
            <td>{{ $jamSelesai }}</td>
        </tr>
    </table>

    {{-- B. KOMPOSISI TENAGA KERJA --}}
    <div class="section-title">B. KOMPOSISI TENAGA KERJA</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 6%;">No.</th>
                <th style="width: 34%;">Nama Personel</th>
                <th style="width: 20%;">Unit Kerja</th>
                <th style="width: 20%;">Jabatan</th>
                <th style="width: 20%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($manpowerList as $idx => $mp)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong>{{ $mp['nama'] ?? '-' }}</strong></td>
                    <td>{{ $mp['unit_kerja'] ?? '-' }}</td>
                    <td>{{ $mp['jabatan'] ?? '-' }}</td>
                    <td>{{ $mp['keterangan'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">1</td>
                    <td>{{ $namaEngineer }}</td>
                    <td>Technical Support</td>
                    <td>{{ $jabatanEngineer }}</td>
                    <td>PIC Utama</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div style="font-size: 6.8pt; margin-top: 1px; margin-bottom: 4px;">
        Total tenaga kerja: <u>&nbsp;&nbsp;<strong>{{ $totalTenagaKerja }}</strong>&nbsp;&nbsp;</u> orang
    </div>

    {{-- C. RUANG LINGKUP / TARGET PEKERJAAN --}}
    <div class="section-title">C. RUANG LINGKUP / TARGET PEKERJAAN</div>
    <table class="report-table">
        <tr>
            <td class="label-cell" style="width: 38%;">1. Target pekerjaan hari ini</td>
            <td style="width: 62%;">{{ $targetHariIni }}</td>
        </tr>
        <tr>
            <td class="label-cell">2. Durasi waktu Project yang ditugaskan</td>
            <td>{{ $durasiProject }}</td>
        </tr>
        <tr>
            <td class="label-cell">3. Scope / pekerjaan yang ditugaskan</td>
            <td>{{ $scopePekerjaan }}</td>
        </tr>
        <tr>
            <td class="label-cell">4. Perangkat / sistem yang ditangani</td>
            <td>{{ $perangkatSistem }}</td>
        </tr>
        <tr>
            <td class="label-cell">5. Kriteria pekerjaan dinyatakan selesai</td>
            <td>{{ $kriteriaSelesai }}</td>
        </tr>
    </table>

    {{-- D. RINCIAN AKTIVITAS ENGINEER --}}
    <div class="section-title">D. RINCIAN AKTIVITAS ENGINEER</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 9%;">Waktu</th>
                <th style="width: 26%;">Aktivitas / Tindakan</th>
                <th style="width: 15%;">Perangkat / Area</th>
                <th style="width: 15%;">Hasil / Kondisi</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Kendala</th>
                <th style="width: 10%;">Tindak Lanjut</th>
            </tr>
        </thead>
        <tbody>
            @foreach(array_slice($dActivities, 0, 7) as $act)
                <tr>
                    <td class="text-center">{{ $act['no'] ?? $loop->iteration }}</td>
                    <td class="text-center">{{ $act['waktu'] ?? '-' }}</td>
                    <td>{{ $act['aktivitas'] ?? '-' }}</td>
                    <td>{{ $act['perangkat'] ?? '-' }}</td>
                    <td>{{ $act['hasil'] ?? '-' }}</td>
                    <td class="text-center"><strong>{{ $act['status'] ?? 'Selesai' }}</strong></td>
                    <td>{{ $act['kendala'] ?? '-' }}</td>
                    <td>{{ $act['tindak_lanjut'] ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- E. MATERIAL, PERALATAN & SPARE PART --}}
    <div class="section-title">E. MATERIAL, PERALATAN &amp; SPARE PART</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 25%;">Nama Item</th>
                <th style="width: 24%;">Spesifikasi/Type</th>
                <th style="width: 8%;">Qty</th>
                <th style="width: 10%;">Satuan</th>
                <th style="width: 12%;">Kondisi</th>
                <th style="width: 16%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($materials as $idx => $mat)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $mat['nama'] ?? '-' }}</td>
                    <td>{{ $mat['spesifikasi'] ?? '-' }}</td>
                    <td class="text-center">{{ $mat['qty'] ?? '-' }}</td>
                    <td class="text-center">{{ $mat['satuan'] ?? '-' }}</td>
                    <td class="text-center">{{ $mat['kondisi'] ?? '-' }}</td>
                    <td>{{ $mat['keterangan'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- F. HASIL PENGUJIAN / PENGUKURAN --}}
    <div class="section-title">F. HASIL PENGUJIAN / PENGUKURAN</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 25%;">Parameter</th>
                <th style="width: 14%;">Sebelum</th>
                <th style="width: 14%;">Sesudah</th>
                <th style="width: 12%;">Satuan</th>
                <th style="width: 15%;">Metode/Alat</th>
                <th style="width: 15%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($testResults as $idx => $test)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $test['parameter'] ?? '-' }}</td>
                    <td class="text-center">{{ $test['sebelum'] ?? '-' }}</td>
                    <td class="text-center">{{ $test['sesudah'] ?? '-' }}</td>
                    <td class="text-center">{{ $test['satuan'] ?? '-' }}</td>
                    <td>{{ $test['metode'] ?? '-' }}</td>
                    <td>{{ $test['keterangan'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- G. KENDALA / INCIDENT / DEVIASI --}}
    <div class="section-title">G. KENDALA / INCIDENT / DEVIASI</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">No.</th>
                <th style="width: 10%;">Waktu</th>
                <th style="width: 24%;">Kendala / Incident</th>
                <th style="width: 23%;">Dampak</th>
                <th style="width: 26%;">Tindakan Penanganan</th>
                <th style="width: 12%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($incidents as $idx => $inc)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $inc['waktu'] ?? '-' }}</td>
                    <td>{{ $inc['kendala'] ?? '-' }}</td>
                    <td>{{ $inc['dampak'] ?? '-' }}</td>
                    <td>{{ $inc['tindakan'] ?? '-' }}</td>
                    <td class="text-center">{{ $inc['status'] ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- H. HASIL AKHIR PEKERJAAN (Baris Status pada Halaman 1) --}}
    <div class="section-title">H. HASIL AKHIR PEKERJAAN</div>
    <table class="report-table">
        <tr>
            <td class="label-cell" style="width: 30%;">Status pekerjaan</td>
            <td style="width: 70%;">
                <span class="check-box">{!! in_array($statusPekerjaan, ['selesai', 'closed', 'done']) ? '&#10003;' : '' !!}</span> Selesai
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="check-box">{!! in_array($statusPekerjaan, ['selesai sebagian', 'partial']) ? '&#10003;' : '' !!}</span> Selesai Sebagian
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="check-box">{!! in_array($statusPekerjaan, ['belum selesai', 'pending', 'open']) ? '&#10003;' : '' !!}</span> Belum Selesai
            </td>
        </tr>
    </table>

    {{-- FOOTER HALAMAN 1 --}}
    <div class="doc-footer">
        <table class="footer-table">
            <tr>
                <td style="width: 65%;">
                    <div style="font-weight: bold; color: #000000; font-size: 6.2pt;">Dokumen ini merupakan milik PT.IP Network Solusindo</div>
                    <div style="color: #334155; margin-top: 1px;">
                        Seluruh isi dokumen hanya dipergunakan untuk kepentingan penyelenggaraan tata kelola perusahaan dan tidak diperkenankan diperbanyak, disalin, dipublikasikan ataupun didistribusikan kepada pihak lain, baik sebagian maupun seluruhnya, tanpa persetujuan tertulis dari Direksi PT.IP Network Solusindo
                    </div>
                </td>
                <td style="width: 35%; text-align: right; vertical-align: top;">
                    <div style="font-weight: bold; color: #000000; font-size: 6.2pt;">&copy;PT.IP Network Solusindo, Seluruh Hak Dilindungi.</div>
                    <div style="margin-top: 3px;">
                        <strong style="font-size: 7.4pt; color: #000000;">Hal 1</strong>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- ═════════════════════════ HALAMAN 2 ════════════════════════════ --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div class="page-container">

    {{-- KOP SURAT / HEADER HALAMAN 2 --}}
    <table class="doc-header-table" style="margin-top: 2px; margin-bottom: 10px;">
        <tr>
            <td style="width: 15%; text-align: left; vertical-align: middle;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo" style="height: 38px; width: auto; max-width: 80px; display: block;">
                @endif
            </td>
            <td style="width: 85%; text-align: center; vertical-align: middle;">
                <div class="doc-title">FORM LAPORAN AKTIVITAS ENGINEER – PROJECT</div>
            </td>
        </tr>
    </table>

    {{-- KELANJUTAN BAGIAN H --}}
    <table class="report-table" style="margin-top: 4px;">
        <tr>
            <td class="label-cell" style="width: 42%;">Persentase progress</td>
            <td style="width: 58%;"><u>&nbsp;&nbsp;<strong>{{ $progressPercent }}</strong>&nbsp;&nbsp;</u> %</td>
        </tr>
        <tr>
            <td class="label-cell">Kondisi sistem/perangkat setelah pekerjaan</td>
            <td>{{ $kondisiSistem }}</td>
        </tr>
        <tr>
            <td class="label-cell">Outstanding / pekerjaan tersisa</td>
            <td>{{ $outstanding }}</td>
        </tr>
        <tr>
            <td class="label-cell">Rekomendasi / kebutuhan tindak lanjut</td>
            <td>{{ $rekomendasi }}</td>
        </tr>
        <tr>
            <td class="label-cell">Dilakukan Eskalasi pekerjaan (PIC)</td>
            <td>{{ $eskalasiPic }}</td>
        </tr>
    </table>

    {{-- I. REKAP DOKUMENTASI FOTO --}}
    <div class="section-title" style="margin-top: 7px;">I. REKAP DOKUMENTASI FOTO</div>
    <div style="font-size: 6.8pt; color: #1E293B; margin-bottom: 3px;">
        Lampirkan foto yang menunjukkan kondisi aktual. Minimal: Before, Progress (bila ada), dan After. Cantumkan waktu/lokasi singkat pada caption.
    </div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 6%;">No.</th>
                <th style="width: 15%;">Tahap</th>
                <th style="width: 25%;">Area / Objek</th>
                <th style="width: 34%;">Foto / Tempat Menempel Foto</th>
                <th style="width: 20%;">Keterangan / Caption</th>
            </tr>
        </thead>
        <tbody>
            {{-- BEFORE --}}
            <tr>
                <td class="text-center">1</td>
                <td class="text-center"><strong>BEFORE</strong></td>
                <td>{{ $beforePhoto['area'] ?? 'Perangkat sebelum pengerjaan' }}</td>
                <td style="text-align: center; padding: 3px;">
                    @if(!empty($beforePhoto['url']))
                        <img src="{{ $beforePhoto['url'] }}" alt="Before" class="photo-img">
                    @else
                        <div class="photo-placeholder">[TEMPEL FOTO DI SINI]</div>
                    @endif
                </td>
                <td>{{ $beforePhoto['caption'] ?? 'Kondisi awal sebelum tindakan' }}</td>
            </tr>
            {{-- PROGRESS --}}
            <tr>
                <td class="text-center">2</td>
                <td class="text-center"><strong>PROGRESS</strong></td>
                <td>{{ $progressPhoto['area'] ?? 'Proses implementasi / maintenance' }}</td>
                <td style="text-align: center; padding: 3px;">
                    @if(!empty($progressPhoto['url']))
                        <img src="{{ $progressPhoto['url'] }}" alt="Progress" class="photo-img">
                    @else
                        <div class="photo-placeholder">[TEMPEL FOTO DI SINI]</div>
                    @endif
                </td>
                <td>{{ $progressPhoto['caption'] ?? 'Aktivitas teknis berjalan' }}</td>
            </tr>
            {{-- AFTER --}}
            <tr>
                <td class="text-center">3</td>
                <td class="text-center"><strong>AFTER</strong></td>
                <td>{{ $afterPhoto['area'] ?? 'Perangkat setelah pengerjaan selesai' }}</td>
                <td style="text-align: center; padding: 3px;">
                    @if(!empty($afterPhoto['url']))
                        <img src="{{ $afterPhoto['url'] }}" alt="After" class="photo-img">
                    @else
                        <div class="photo-placeholder">[TEMPEL FOTO DI SINI]</div>
                    @endif
                </td>
                <td>{{ $afterPhoto['caption'] ?? 'Kondisi akhir perangkat beroperasi' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- J. VERIFIKASI & PENGESAHAN --}}
    <div class="section-title" style="margin-top: 7px;">J. VERIFIKASI &amp; PENGESAHAN</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 28%;">Pihak</th>
                <th style="width: 26%;">Nama</th>
                <th style="width: 18%;">Tanggal</th>
                <th style="width: 28%;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            {{-- 1. Engineer --}}
            <tr>
                <td class="label-cell">Engineer</td>
                <td><strong>{{ $picSignerName }}</strong></td>
                <td class="text-center">{{ $picSignedAt }}</td>
                <td class="sig-container">
                    @if(!empty($docSig?->pic_signature))
                        <img src="{{ $docSig->pic_signature }}" alt="TTD PIC" class="sig-img">
                    @elseif(!empty($docSig?->pic_signed_at) && !empty($qrCodeBase64))
                        <img src="{{ $qrCodeBase64 }}" alt="QR" style="height: 38px; width: 38px; display: block; margin: 0 auto;">
                    @else
                        &nbsp;
                    @endif
                </td>
            </tr>
            {{-- 2. Project Manager / Team Leader --}}
            <tr>
                <td class="label-cell">Project Manager / Team Leader</td>
                <td><strong>{{ $leadSignerName }}</strong></td>
                <td class="text-center">{{ $leadSignedAt }}</td>
                <td class="sig-container">
                    @if(!empty($docSig?->lead_signature))
                        <img src="{{ $docSig->lead_signature }}" alt="TTD Lead" class="sig-img">
                    @elseif(!empty($qrCodeBase64) && !empty($docSig?->lead_signed_at))
                        <img src="{{ $qrCodeBase64 }}" alt="QR" style="height: 38px; width: 38px; display: block; margin: 0 auto;">
                    @else
                        &nbsp;
                    @endif
                </td>
            </tr>
            {{-- 3. Customer / Site Representative --}}
            <tr>
                <td class="label-cell">Customer / Site Representative</td>
                <td><strong>{{ $custSignerName }}</strong></td>
                <td class="text-center">{{ $custSignedAt }}</td>
                <td class="sig-container">
                    @if(!empty($docSig?->head_signature))
                        <img src="{{ $docSig->head_signature }}" alt="TTD Head/Customer" class="sig-img">
                    @else
                        &nbsp;
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{-- K. CATATAN ADMINISTRASI DOKUMEN --}}
    <div class="section-title" style="margin-top: 7px;">K. CATATAN ADMINISTRASI DOKUMEN</div>
    <table class="report-table">
        <tr>
            <td class="label-cell" style="width: 38%;">Nomor WO / Ticket</td>
            <td style="width: 62%;">{{ $nomorWoTicket }}</td>
        </tr>
        <tr>
            <td class="label-cell">Nomor BA / BAST / Checklist terkait</td>
            <td>{{ $nomorBaBast }}</td>
        </tr>
        <tr>
            <td class="label-cell">Lampiran tambahan</td>
            <td>{{ $lampiranExtra }}</td>
        </tr>
        <tr>
            <td class="label-cell">Nama file / folder dokumentasi</td>
            <td>{{ $namaFileFolder }}</td>
        </tr>
    </table>

    <div style="font-size: 6.6pt; line-height: 1.25; margin-top: 4px; color: #000000;">
        <strong>Catatan penggunaan:</strong> Form diisi oleh engineer/PIC berdasarkan aktivitas aktual. Setiap pekerjaan utama harus memiliki bukti aktivitas dan dokumentasi foto yang dapat ditelusuri ke project/WO. Jika pekerjaan berlangsung lebih dari satu hari, gunakan satu laporan per hari.
    </div>

    {{-- FOOTER HALAMAN 2 --}}
    <div class="doc-footer">
        <table class="footer-table">
            <tr>
                <td style="width: 65%;">
                    <div style="font-weight: bold; color: #000000; font-size: 6.2pt;">Dokumen ini merupakan milik PT.IP Network Solusindo</div>
                    <div style="color: #334155; margin-top: 1px;">
                        Seluruh isi dokumen hanya dipergunakan untuk kepentingan penyelenggaraan tata kelola perusahaan dan tidak diperkenankan diperbanyak, disalin, dipublikasikan ataupun didistribusikan kepada pihak lain, baik sebagian maupun seluruhnya, tanpa persetujuan tertulis dari Direksi PT.IP Network Solusindo
                    </div>
                </td>
                <td style="width: 35%; text-align: right; vertical-align: top;">
                    <div style="font-weight: bold; color: #000000; font-size: 6.2pt;">&copy;PT.IP Network Solusindo, Seluruh Hak Dilindungi.</div>
                    <div style="margin-top: 3px;">
                        <strong style="font-size: 7.4pt; color: #000000;">Hal 2</strong>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</div>

</body>
</html>
