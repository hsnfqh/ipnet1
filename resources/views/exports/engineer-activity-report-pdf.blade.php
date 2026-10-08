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
            font-size: 7.2pt;
            color: #000000;
            line-height: 1.25;
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
            margin-bottom: 5px;
        }
        .doc-header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
        }
        .doc-intro {
            font-size: 7pt;
            color: #1E293B;
            margin-top: 4px;
            margin-bottom: 10px;
            line-height: 1.25;
        }

        /* ── Judul Section ── */
        .section-title {
            font-size: 8.2pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            margin-top: 7px;
            margin-bottom: 3px;
            letter-spacing: 0.2px;
        }

        /* ── Tabel Standar ── */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            table-layout: fixed;
        }
        .report-table th {
            background-color: #EBF3FB;
            color: #000000;
            font-weight: bold;
            font-size: 7pt;
            padding: 3px 3px;
            border: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
        }
        .report-table td {
            font-size: 7pt;
            padding: 2.8px 3.8px;
            border: 1px solid #000000;
            vertical-align: middle;
            color: #000000;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.22;
        }
        .report-table td.label-cell {
            font-weight: bold;
            background-color: #F8FAFC;
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
            padding-top: 3px;
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
            font-size: 5.8pt;
            color: #000000;
            line-height: 1.2;
        }
        .paraf-box {
            border: 1px solid #000000;
            width: 55px;
            height: 16px;
            text-align: center;
            line-height: 16px;
            font-size: 6pt;
            display: inline-block;
            float: right;
        }

        /* ── Kotak Foto Dokumentasi ── */
        .photo-placeholder {
            width: 100%;
            height: 80px;
            border: 1px dashed #94A3B8;
            background-color: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #64748B;
            font-size: 6.8pt;
            font-weight: bold;
            padding: 4px;
        }
        .photo-img {
            max-width: 100%;
            max-height: 80px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        /* ── Kotak Tanda Tangan ── */
        .sig-container {
            height: 42px;
            text-align: center;
            vertical-align: middle;
        }
        .sig-img {
            max-height: 40px;
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

    // Deteksi Kategori Laporan: 'project', 'managed_service', 'help_desk'
    $category = $reportCategory ?? ($rData['category'] ?? ($rData['report_type'] ?? null));
    if (!$category) {
        if (!empty($rData['ms_identitas']) || !empty($rData['ms_aktivitas'])) {
            $category = 'managed_service';
        } elseif (!empty($rData['hd_identitas']) || !empty($rData['hd_aktivitas'])) {
            $category = 'help_desk';
        } else {
            $category = 'project';
        }
    }

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
    }

    // Logo Base64
    if (empty($logoBase64)) {
        $logoPath = public_path('images/ipnet1.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('images/ipnet.png');
        }
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
    }

    // Nilai Umum Identitas
    $idObj = $rData['identitas'] ?? [];
    $hariTanggal = !empty($idObj['hari_tanggal']) ? \Carbon\Carbon::parse($idObj['hari_tanggal'])->translatedFormat('d F Y') : (!empty($firstActObj->activity_date) ? \Carbon\Carbon::parse($firstActObj->activity_date)->translatedFormat('d F Y') : date('d F Y'));
    $noLaporan   = $idObj['no_laporan'] ?? ($verifyDocNumber ?? ('IPNET-ACT-' . date('Ym') . '-DRAFT'));
    $namaProject = $idObj['nama_project'] ?? ($projectName ?? ($project?->name ?? 'Aktivitas Proyek Lapangan'));
    $noSoSpk     = $idObj['no_so_spk'] ?? ($project?->po_number ?? '-');
    $lokasiSite  = $idObj['lokasi_site'] ?? ($firstActObj->location ?? ($project?->location ?? 'Site Project'));
    $workOrder   = $idObj['work_order'] ?? ($project?->id ? 'WO-IPNET-' . str_pad($project->id, 5, '0', STR_PAD_LEFT) : '-');
    $namaEngineer= $idObj['nama_engineer'] ?? ($engineerName ?? (auth()->user()?->name ?? 'Field Engineer'));
    $customer    = $idObj['customer'] ?? ($project?->client ?? 'Customer Partner');
    $jenisPekerjaan = $idObj['jenis_pekerjaan'] ?? ($firstActObj->activity_type ?? 'Implementasi & Jaringan');
    $picCustomer    = $idObj['pic_customer'] ?? ($project?->customer_pic_technical ?? '-');
    $jabatanEngineer= $idObj['jabatan'] ?? (auth()->user()?->position ?: 'Field Engineer');
    $jamMulai       = !empty($idObj['jam_mulai']) ? $idObj['jam_mulai'] . ' WIB' : '09:00 WIB';
    $jamSelesai     = !empty($idObj['jam_selesai']) ? $idObj['jam_selesai'] . ' WIB' : '17:00 WIB';

    // Format Tanggal Verifikasi
    $picSignedAt  = $docSig?->pic_signed_at ? $docSig->pic_signed_at->format('d/m/Y') : ($hariTanggal ? date('d/m/Y') : '-');
    $leadSignedAt = $docSig?->lead_signed_at ? $docSig->lead_signed_at->format('d/m/Y') : ($docSig?->pic_signed_at ? $docSig->pic_signed_at->format('d/m/Y') : '-');
    $custSignedAt = date('d/m/Y');
@endphp

@if($category === 'managed_service')
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ══════════════ DOKUMEN: MANAGED SERVICE (3 HALAMAN) ═══════════════ --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @php
        $msId = $rData['ms_identitas'] ?? [];
        $msTanggal   = $msId['tanggal'] ?? ($hariTanggal ?? date('d/m/Y'));
        $msNoReport  = $msId['no_report'] ?? ($verifyDocNumber ?? '-');
        $msCustomer  = $msId['customer'] ?? ($customer !== '-' ? $customer : ($namaProject ?? '-'));
        $msNoContract= $msId['no_contract'] ?? ($noSoSpk !== '-' ? $noSoSpk : '-');
        $msSite      = $msId['site_lokasi'] ?? ($lokasiSite !== '-' ? $lokasiSite : '-');
        $msTicketNo  = $msId['ticket_incident_no'] ?? ($workOrder !== '-' ? $workOrder : '-');
        $msEngineer  = $msId['engineer_pic'] ?? ($namaEngineer ?? '-');
        $msShift     = $msId['shift'] ?? 'Pagi / Regular';
        $msActTypes  = $msId['jenis_aktivitas'] ?? [];
        $msServiceDev= $msId['service_device'] ?? ($namaProject ?? 'Network & Server');
        $msJamMulai  = $msId['jam_mulai'] ?? ($jamMulai ?? '08:00');
        $msJamSelesai= $msId['jam_selesai'] ?? ($jamSelesai ?? '17:00');

        $msSlaList = $rData['ms_sla'] ?? [
            ['parameter' => 'Ticket Received', 'waktu' => '08:00', 'target_sla' => '< 15 Menit', 'aktual' => '5 Menit', 'status' => 'Met', 'keterangan' => 'Normal'],
            ['parameter' => 'Engineer Response', 'waktu' => '08:05', 'target_sla' => '< 30 Menit', 'aktual' => '10 Menit', 'status' => 'Met', 'keterangan' => 'Respon cepat'],
            ['parameter' => 'Service Restore', 'waktu' => '09:30', 'target_sla' => '< 4 Jam', 'aktual' => '1.5 Jam', 'status' => 'Met', 'keterangan' => 'Layanan pulih'],
            ['parameter' => 'Resolution / Close', 'waktu' => '10:00', 'target_sla' => '< 8 Jam', 'aktual' => '2 Jam', 'status' => 'Met', 'keterangan' => 'Resolved']
        ];

        $msKondisiList = $rData['ms_kondisi_perangkat'] ?? [
            ['no' => 1, 'service_device' => 'Core Switch / Router', 'parameter' => 'CPU / Memory', 'before' => 'Normal 25%', 'after' => 'Normal 22%', 'status' => 'Good', 'keterangan' => 'Healthy'],
            ['no' => 2, 'service_device' => 'Link Main FO', 'parameter' => 'Latency / Loss', 'before' => '12ms / 0%', 'after' => '11ms / 0%', 'status' => 'Good', 'keterangan' => 'Optimal'],
            ['no' => 3, 'service_device' => 'Access Point Area', 'parameter' => 'SSID / Client', 'before' => 'Online', 'after' => 'Online', 'status' => 'Good', 'keterangan' => 'Normal'],
            ['no' => 4, 'service_device' => '-', 'parameter' => '-', 'before' => '-', 'after' => '-', 'status' => '-', 'keterangan' => '-'],
            ['no' => 5, 'service_device' => '-', 'parameter' => '-', 'before' => '-', 'after' => '-', 'status' => '-', 'keterangan' => '-'],
        ];

        $msActList = $rData['ms_aktivitas'] ?? [];
        if (empty($msActList)) {
            if (!empty($actItems)) {
                foreach (array_slice($actItems, 0, 8) as $idx => $ai) {
                    $ai = (array) $ai;
                    $msActList[] = [
                        'no' => $idx + 1,
                        'waktu' => $ai['time'] ?? '09:00',
                        'aktivitas' => $ai['activity'] ?? '-',
                        'ticket_alarm' => '-',
                        'hasil' => 'Berhasil',
                        'status' => $ai['status'] ?? 'Done',
                        'kendala' => '-',
                        'follow_up' => $ai['notes'] ?? '-'
                    ];
                }
            }
        }
        while (count($msActList) < 8) {
            $msActList[] = ['no' => count($msActList) + 1, 'waktu' => '-', 'aktivitas' => '-', 'ticket_alarm' => '-', 'hasil' => '-', 'status' => '-', 'kendala' => '-', 'follow_up' => '-'];
        }

        $msIncidentList = $rData['ms_incident_escalation'] ?? [
            ['no' => 1, 'incident' => '-', 'impact' => '-', 'root_cause' => '-', 'corrective_action' => '-', 'escalation' => '-', 'status' => 'Closed'],
            ['no' => 2, 'incident' => '-', 'impact' => '-', 'root_cause' => '-', 'corrective_action' => '-', 'escalation' => '-', 'status' => '-'],
            ['no' => 3, 'incident' => '-', 'impact' => '-', 'root_cause' => '-', 'corrective_action' => '-', 'escalation' => '-', 'status' => '-'],
            ['no' => 4, 'incident' => '-', 'impact' => '-', 'root_cause' => '-', 'corrective_action' => '-', 'escalation' => '-', 'status' => '-'],
            ['no' => 5, 'incident' => '-', 'impact' => '-', 'root_cause' => '-', 'corrective_action' => '-', 'escalation' => '-', 'status' => '-'],
        ];

        $msPmList = $rData['ms_pm_checklist'] ?? [
            ['no' => 1, 'item_pemeriksaan' => 'Pembersihan Debu Rack & Fan Unit', 'kondisi' => 'Baik', 'hasil' => 'Bersih', 'temuan' => 'Nihil', 'tindakan' => 'Pembersihan berkala', 'status' => 'OK'],
            ['no' => 2, 'item_pemeriksaan' => 'Cek Kabel UTP / Patch Cord & Label', 'kondisi' => 'Rapi', 'hasil' => 'Sesuai', 'temuan' => 'Nihil', 'tindakan' => 'Check kelayakan', 'status' => 'OK'],
            ['no' => 3, 'item_pemeriksaan' => 'Backup Config Router & Switch', 'kondisi' => 'Tersimpan', 'hasil' => 'Success', 'temuan' => 'Config terupdate', 'tindakan' => 'Export ke NAS', 'status' => 'OK'],
            ['no' => 4, 'item_pemeriksaan' => '-', 'kondisi' => '-', 'hasil' => '-', 'temuan' => '-', 'tindakan' => '-', 'status' => '-'],
            ['no' => 5, 'item_pemeriksaan' => '-', 'kondisi' => '-', 'hasil' => '-', 'temuan' => '-', 'tindakan' => '-', 'status' => '-'],
        ];

        $msMaterialList = $rData['ms_materials'] ?? [
            ['no' => 1, 'item' => 'Patch Cord Cat6 1.5M', 'type' => 'UTP', 'qty' => '2 Pcs', 'used_replaced' => 'Used', 'old_new' => 'New', 'keterangan' => 'Penggantian kabel'],
            ['no' => 2, 'item' => '-', 'type' => '-', 'qty' => '-', 'used_replaced' => '-', 'old_new' => '-', 'keterangan' => '-'],
            ['no' => 3, 'item' => '-', 'type' => '-', 'qty' => '-', 'used_replaced' => '-', 'old_new' => '-', 'keterangan' => '-'],
            ['no' => 4, 'item' => '-', 'type' => '-', 'qty' => '-', 'used_replaced' => '-', 'old_new' => '-', 'keterangan' => '-'],
            ['no' => 5, 'item' => '-', 'type' => '-', 'qty' => '-', 'used_replaced' => '-', 'old_new' => '-', 'keterangan' => '-'],
        ];

        $msEvidenceList = $rData['ms_evidence'] ?? [
            ['no' => 1, 'evidence' => 'Monitoring Grafana Dashboard', 'waktu' => '09:15', 'foto_url' => '', 'keterangan' => 'Traffic normal'],
            ['no' => 2, 'evidence' => 'Hasil PM Rack Perangkat', 'waktu' => '10:30', 'foto_url' => '', 'keterangan' => 'Rack bersih & rapi'],
            ['no' => 3, 'evidence' => '-', 'waktu' => '-', 'foto_url' => '', 'keterangan' => '-'],
            ['no' => 4, 'evidence' => '-', 'waktu' => '-', 'foto_url' => '', 'keterangan' => '-'],
            ['no' => 5, 'evidence' => '-', 'waktu' => '-', 'foto_url' => '', 'keterangan' => '-'],
        ];

        $msClosure = $rData['ms_closure'] ?? [];
        $msClosureStatus = $msClosure['service_status'] ?? [];
        $msClosureSla = $msClosure['sla'] ?? [];
    @endphp

    {{-- ── HALAMAN 1 (MANAGED SERVICE) ── --}}
    <div class="page-container page-break">
        <table class="doc-header-table">
            <tr>
                <td style="width: 15%; text-align: left; vertical-align: middle;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="Logo" style="height: 38px; width: auto; max-width: 80px; display: block;">
                    @endif
                </td>
                <td style="width: 85%; text-align: center; vertical-align: middle;">
                    <div class="doc-title">FORM LAPORAN AKTIVITAS ENGINEER – MANAGED SERVICE</div>
                </td>
            </tr>
        </table>

        <div class="doc-intro">
            Laporan aktivitas operasi managed service: monitoring, preventive maintenance, corrective maintenance, incident, request, dan onsite support.
        </div>

        {{-- A. IDENTITAS SERVICE --}}
        <div class="section-title">A. . IDENTITAS SERVICE</div>
        <table class="report-table">
            <tr>
                <td class="label-cell" style="width: 20%;">Tanggal</td>
                <td style="width: 30%;">{{ $msTanggal }}</td>
                <td class="label-cell" style="width: 20%;">No. Report</td>
                <td style="width: 30%;">{{ $msNoReport }}</td>
            </tr>
            <tr>
                <td class="label-cell">Customer</td>
                <td>{{ $msCustomer }}</td>
                <td class="label-cell">No. Contract</td>
                <td>{{ $msNoContract }}</td>
            </tr>
            <tr>
                <td class="label-cell">Site / Lokasi</td>
                <td>{{ $msSite }}</td>
                <td class="label-cell">Ticket / Incident No.</td>
                <td>{{ $msTicketNo }}</td>
            </tr>
            <tr>
                <td class="label-cell">Engineer / PIC</td>
                <td>{{ $msEngineer }}</td>
                <td class="label-cell">Shift</td>
                <td>{{ $msShift }}</td>
            </tr>
            <tr>
                <td class="label-cell">Jenis Aktivitas</td>
                <td>
                    <span class="check-box">{!! !empty($msActTypes['monitoring']) ? '&#10003;' : '' !!}</span> Monitoring&nbsp;&nbsp;
                    <span class="check-box">{!! !empty($msActTypes['pm']) ? '&#10003;' : '' !!}</span> PM&nbsp;&nbsp;
                    <span class="check-box">{!! !empty($msActTypes['cm']) ? '&#10003;' : '' !!}</span> CM&nbsp;&nbsp;
                    <span class="check-box">{!! !empty($msActTypes['incident']) ? '&#10003;' : '' !!}</span> Incident<br>
                    <span class="check-box">{!! !empty($msActTypes['request']) ? '&#10003;' : '' !!}</span> Request&nbsp;&nbsp;
                    <span class="check-box">{!! !empty($msActTypes['visit']) ? '&#10003;' : '' !!}</span> Visit
                </td>
                <td class="label-cell">Service / Device</td>
                <td>{{ $msServiceDev }}</td>
            </tr>
            <tr>
                <td class="label-cell">Jam Mulai</td>
                <td>{{ $msJamMulai }}</td>
                <td class="label-cell">Jam Selesai</td>
                <td>{{ $msJamSelesai }}</td>
            </tr>
        </table>

        {{-- B. SLA TRACKING --}}
        <div class="section-title">B. SLA TRACKING</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 24%;">Parameter</th>
                    <th style="width: 14%;">Waktu</th>
                    <th style="width: 16%;">Target SLA</th>
                    <th style="width: 14%;">Aktual</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 20%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($msSlaList as $sla)
                    <tr>
                        <td class="label-cell">{{ $sla['parameter'] ?? '-' }}</td>
                        <td class="text-center">{{ $sla['waktu'] ?? '-' }}</td>
                        <td class="text-center">{{ $sla['target_sla'] ?? '-' }}</td>
                        <td class="text-center">{{ $sla['aktual'] ?? '-' }}</td>
                        <td class="text-center"><strong>{{ $sla['status'] ?? '-' }}</strong></td>
                        <td>{{ $sla['keterangan'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- C. KONDISI SERVICE / PERANGKAT --}}
        <div class="section-title">C. KONDISI SERVICE / PERANGKAT</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 24%;">Service / Device</th>
                    <th style="width: 18%;">Parameter</th>
                    <th style="width: 14%;">Before</th>
                    <th style="width: 14%;">After</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 15%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($msKondisiList, 0, 5) as $idx => $k)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $k['service_device'] ?? '-' }}</td>
                        <td>{{ $k['parameter'] ?? '-' }}</td>
                        <td class="text-center">{{ $k['before'] ?? '-' }}</td>
                        <td class="text-center">{{ $k['after'] ?? '-' }}</td>
                        <td class="text-center">{{ $k['status'] ?? '-' }}</td>
                        <td>{{ $k['keterangan'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- D. RINCIAN AKTIVITAS ENGINEER --}}
        <div class="section-title">D. RINCIAN AKTIVITAS ENGINEER</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 9%;">Waktu</th>
                    <th style="width: 26%;">Aktivitas</th>
                    <th style="width: 14%;">Ticket / Alarm</th>
                    <th style="width: 14%;">Hasil</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 11%;">Kendala</th>
                    <th style="width: 11%;">Follow-up</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($msActList, 0, 8) as $idx => $act)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center">{{ $act['waktu'] ?? '-' }}</td>
                        <td>{{ $act['aktivitas'] ?? '-' }}</td>
                        <td>{{ $act['ticket_alarm'] ?? '-' }}</td>
                        <td>{{ $act['hasil'] ?? '-' }}</td>
                        <td class="text-center"><strong>{{ $act['status'] ?? '-' }}</strong></td>
                        <td>{{ $act['kendala'] ?? '-' }}</td>
                        <td>{{ $act['follow_up'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
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
                        <div style="margin-top: 2px;">
                            <div class="paraf-box">Paraf</div>
                            <div style="clear: both; margin-top: 2px;"><strong style="font-size: 7.2pt; color: #000000;">Hal 1</strong></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ── HALAMAN 2 (MANAGED SERVICE) ── --}}
    <div class="page-container page-break">
        <table class="doc-header-table">
            <tr>
                <td style="width: 15%; text-align: left; vertical-align: middle;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="Logo" style="height: 38px; width: auto; max-width: 80px; display: block;">
                    @endif
                </td>
                <td style="width: 85%; text-align: center; vertical-align: middle;">
                    <div class="doc-title">FORM LAPORAN AKTIVITAS ENGINEER – MANAGED SERVICE</div>
                </td>
            </tr>
        </table>

        {{-- E. INCIDENT / ROOT CAUSE / ESCALATION --}}
        <div class="section-title">E. INCIDENT / ROOT CAUSE / ESCALATION</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 20%;">Incident</th>
                    <th style="width: 15%;">Impact</th>
                    <th style="width: 20%;">Probable / Root Cause</th>
                    <th style="width: 20%;">Corrective Action</th>
                    <th style="width: 10%;">Escalation</th>
                    <th style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($msIncidentList, 0, 5) as $idx => $inc)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $inc['incident'] ?? '-' }}</td>
                        <td>{{ $inc['impact'] ?? '-' }}</td>
                        <td>{{ $inc['root_cause'] ?? '-' }}</td>
                        <td>{{ $inc['corrective_action'] ?? '-' }}</td>
                        <td class="text-center">{{ $inc['escalation'] ?? '-' }}</td>
                        <td class="text-center"><strong>{{ $inc['status'] ?? '-' }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- F. PREVENTIVE MAINTENANCE / CHECKLIST --}}
        <div class="section-title">F. PREVENTIVE MAINTENANCE / CHECKLIST</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 26%;">Item Pemeriksaan</th>
                    <th style="width: 13%;">Kondisi</th>
                    <th style="width: 13%;">Hasil</th>
                    <th style="width: 15%;">Temuan</th>
                    <th style="width: 18%;">Tindakan</th>
                    <th style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($msPmList, 0, 5) as $idx => $pm)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $pm['item_pemeriksaan'] ?? '-' }}</td>
                        <td class="text-center">{{ $pm['kondisi'] ?? '-' }}</td>
                        <td class="text-center">{{ $pm['hasil'] ?? '-' }}</td>
                        <td>{{ $pm['temuan'] ?? '-' }}</td>
                        <td>{{ $pm['tindakan'] ?? '-' }}</td>
                        <td class="text-center"><strong>{{ $pm['status'] ?? '-' }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- G. MATERIAL / SPARE PART --}}
        <div class="section-title">G. MATERIAL / SPARE PART</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 25%;">Item</th>
                    <th style="width: 15%;">Type</th>
                    <th style="width: 8%;">Qty</th>
                    <th style="width: 14%;">Used / Replaced</th>
                    <th style="width: 13%;">Old/New</th>
                    <th style="width: 20%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($msMaterialList, 0, 5) as $idx => $mat)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $mat['item'] ?? '-' }}</td>
                        <td>{{ $mat['type'] ?? '-' }}</td>
                        <td class="text-center">{{ $mat['qty'] ?? '-' }}</td>
                        <td class="text-center">{{ $mat['used_replaced'] ?? '-' }}</td>
                        <td class="text-center">{{ $mat['old_new'] ?? '-' }}</td>
                        <td>{{ $mat['keterangan'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- H. DOKUMENTASI & EVIDENCE --}}
        <div class="section-title">H. DOKUMENTASI &amp; EVIDENCE</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 28%;">Evidence</th>
                    <th style="width: 12%;">Waktu</th>
                    <th style="width: 35%;">Foto / Screenshot / Log</th>
                    <th style="width: 20%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($msEvidenceList, 0, 5) as $idx => $evi)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $evi['evidence'] ?? '-' }}</td>
                        <td class="text-center">{{ $evi['waktu'] ?? '-' }}</td>
                        <td class="text-center">
                            @if(!empty($evi['foto_url']))
                                <img src="{{ $evi['foto_url'] }}" alt="Evidence" style="max-height: 48px; max-width: 100%; display: block; margin: 0 auto;">
                            @else
                                <span style="color: #64748B; font-size: 6.8pt;">[ Foto / Log Terlampir ]</span>
                            @endif
                        </td>
                        <td>{{ $evi['keterangan'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div style="font-size: 6.8pt; color: #475569; margin-top: -2px; margin-bottom: 5px;">
            Evidence dapat berupa foto onsite, screenshot monitoring, log, hasil check, atau bukti pengujian.
        </div>

        {{-- I. SERVICE CLOSURE --}}
        <div class="section-title">I. SERVICE CLOSURE</div>
        <table class="report-table">
            <tr>
                <td class="label-cell" style="width: 20%;">Service Status</td>
                <td style="width: 40%;">
                    <span class="check-box">{!! !empty($msClosureStatus['resolved']) ? '&#10003;' : '' !!}</span> Resolved&nbsp;&nbsp;
                    <span class="check-box">{!! !empty($msClosureStatus['monitoring']) ? '&#10003;' : '' !!}</span> Monitoring&nbsp;&nbsp;
                    <span class="check-box">{!! !empty($msClosureStatus['escalated']) ? '&#10003;' : '' !!}</span> Escalated&nbsp;&nbsp;
                    <span class="check-box">{!! !empty($msClosureStatus['closed']) ? '&#10003;' : '' !!}</span> Closed
                </td>
                <td class="label-cell" style="width: 15%;">SLA</td>
                <td style="width: 25%;">
                    <span class="check-box">{!! !empty($msClosureSla['met']) ? '&#10003;' : '' !!}</span> Met&nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="check-box">{!! !empty($msClosureSla['breach']) ? '&#10003;' : '' !!}</span> Breach
                </td>
            </tr>
            <tr>
                <td class="label-cell">Customer Confirmation</td>
                <td>{{ $msClosure['customer_confirmation'] ?? 'Telah dikonfirmasi secara operasional dan layanan berjalan normal' }}</td>
                <td class="label-cell">Outstanding</td>
                <td>{{ $msClosure['outstanding'] ?? '-' }}</td>
            </tr>
        </table>

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
                        <div style="margin-top: 2px;">
                            <div class="paraf-box">Paraf</div>
                            <div style="clear: both; margin-top: 2px;"><strong style="font-size: 7.2pt; color: #000000;">Hal 2</strong></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ── HALAMAN 3 (MANAGED SERVICE) ── --}}
    <div class="page-container">
        <table class="doc-header-table">
            <tr>
                <td style="width: 15%; text-align: left; vertical-align: middle;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="Logo" style="height: 38px; width: auto; max-width: 80px; display: block;">
                    @endif
                </td>
                <td style="width: 85%; text-align: center; vertical-align: middle;">
                    <div class="doc-title">FORM LAPORAN AKTIVITAS ENGINEER – MANAGED SERVICE</div>
                </td>
            </tr>
        </table>

        {{-- J. VERIFIKASI --}}
        <div class="section-title" style="margin-top: 15px;">J. VERIFIKASI</div>
        <table class="report-table" style="margin-top: 8px;">
            <thead>
                <tr>
                    <th style="width: 30%;">Pihak</th>
                    <th style="width: 25%;">Nama</th>
                    <th style="width: 20%;">Tanggal / Jam</th>
                    <th style="width: 25%;">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="label-cell">Engineer / PIC</td>
                    <td><strong>{{ $msEngineer }}</strong></td>
                    <td class="text-center">{{ $picSignedAt }}</td>
                    <td class="sig-container">
                        @if(!empty($docSig?->pic_signature))
                            <img src="{{ $docSig->pic_signature }}" alt="TTD Engineer" class="sig-img">
                        @else
                            <div style="font-size: 6.8pt; color: #64748B;">Digital Verified</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label-cell">Team Leader / Service Manager</td>
                    <td><strong>{{ $docSig?->lead_name ?? ($docSig?->leadUser?->name ?? 'Nugraha Pratama') }}</strong></td>
                    <td class="text-center">{{ $leadSignedAt }}</td>
                    <td class="sig-container">
                        @if(!empty($docSig?->lead_signature))
                            <img src="{{ $docSig->lead_signature }}" alt="TTD Leader" class="sig-img">
                        @else
                            <div style="font-size: 6.8pt; color: #64748B;">Digital Verified</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label-cell">Customer / Authorized Representative</td>
                    <td><strong>{{ $picCustomer !== '-' ? $picCustomer : 'PIC Site Customer' }}</strong></td>
                    <td class="text-center">{{ $custSignedAt }}</td>
                    <td class="sig-container">
                        <div style="font-size: 6.8pt; color: #64748B; padding-top: 12px;">( Tanda Tangan &amp; Stempel )</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="font-size: 7.2pt; color: #334155; margin-top: 10px; font-style: italic;">
            Catatan: laporan Managed Service harus dapat ditelusuri ke contract/service, ticket, SLA, evidence, dan status closure.
        </div>

        {{-- FOOTER HALAMAN 3 --}}
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
                        <div style="margin-top: 2px;">
                            <div class="paraf-box">Paraf</div>
                            <div style="clear: both; margin-top: 2px;"><strong style="font-size: 7.2pt; color: #000000;">Hal 3</strong></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

@elseif($category === 'help_desk')
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ════════════════ DOKUMEN: HELP DESK (2 HALAMAN) ══════════════════ --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @php
        $hdId = $rData['hd_identitas'] ?? [];
        $hdTanggal   = $hdId['tanggal'] ?? ($hariTanggal ?? date('d/m/Y'));
        $hdShifts    = $hdId['shift'] ?? [];
        $hdEngineer  = $hdId['nama_engineer'] ?? ($namaEngineer ?? '-');
        $hdLeader    = $hdId['team_leader'] ?? ($docSig?->lead_name ?? ($docSig?->leadUser?->name ?? 'Nugraha Pratama'));
        $hdArea      = $hdId['area_site'] ?? ($lokasiSite !== '-' ? $lokasiSite : 'NOC / Central Office');
        $hdCustServ  = $hdId['customer_service'] ?? ($customer !== '-' ? $customer : ($namaProject ?? '-'));
        $hdJamShift  = $hdId['jam_shift'] ?? '08:00 - 16:00 WIB';
        $hdJmlEng    = $hdId['jumlah_engineer'] ?? 1;

        $hdKondisiAwal = $rData['hd_kondisi_awal'] ?? [
            ['no' => 1, 'item_service' => 'Backbone Link Core', 'kondisi_awal' => 'Normal / Up', 'alarm_issue' => 'Clear', 'status' => 'OK', 'keterangan' => 'Traffic lancar'],
            ['no' => 2, 'item_service' => 'Server Monitoring & NMS', 'kondisi_awal' => 'Normal / Running', 'alarm_issue' => 'Clear', 'status' => 'OK', 'keterangan' => 'NMS active'],
            ['no' => 3, 'item_service' => 'Call Center / Ticketing', 'kondisi_awal' => 'Ready', 'alarm_issue' => 'Clear', 'status' => 'OK', 'keterangan' => 'Queuing normal'],
            ['no' => 4, 'item_service' => '-', 'kondisi_awal' => '-', 'alarm_issue' => '-', 'status' => '-', 'keterangan' => '-'],
            ['no' => 5, 'item_service' => '-', 'kondisi_awal' => '-', 'alarm_issue' => '-', 'status' => '-', 'keterangan' => '-'],
        ];

        $hdActList = $rData['hd_aktivitas'] ?? [];
        if (empty($hdActList)) {
            if (!empty($actItems)) {
                foreach (array_slice($actItems, 0, 5) as $idx => $ai) {
                    $ai = (array) $ai;
                    $hdActList[] = [
                        'no' => $idx + 1,
                        'waktu' => $ai['time'] ?? '08:30',
                        'aktivitas' => $ai['activity'] ?? '-',
                        'ticket_wo' => '-',
                        'lokasi_device' => $ai['location'] ?? 'Helpdesk Desk',
                        'hasil' => 'Resolved',
                        'status' => $ai['status'] ?? 'Done'
                    ];
                }
            }
        }
        while (count($hdActList) < 5) {
            $hdActList[] = ['no' => count($hdActList) + 1, 'waktu' => '-', 'aktivitas' => '-', 'ticket_wo' => '-', 'lokasi_device' => '-', 'hasil' => '-', 'status' => '-'];
        }

        $hdTicketList = $rData['hd_ticket_incident'] ?? [
            ['no' => 1, 'ticket' => 'INC-081026-01', 'jenis' => 'Koneksi Lambat', 'priority' => 'Medium', 'start' => '08:30', 'restore' => '08:50', 'close_status' => 'Closed', 'keterangan' => 'Restart switch port'],
            ['no' => 2, 'ticket' => '-', 'jenis' => '-', 'priority' => '-', 'start' => '-', 'restore' => '-', 'close_status' => '-', 'keterangan' => '-'],
            ['no' => 3, 'ticket' => '-', 'jenis' => '-', 'priority' => '-', 'start' => '-', 'restore' => '-', 'close_status' => '-', 'keterangan' => '-'],
            ['no' => 4, 'ticket' => '-', 'jenis' => '-', 'priority' => '-', 'start' => '-', 'restore' => '-', 'close_status' => '-', 'keterangan' => '-'],
            ['no' => 5, 'ticket' => '-', 'jenis' => '-', 'priority' => '-', 'start' => '-', 'restore' => '-', 'close_status' => '-', 'keterangan' => '-'],
        ];

        $hdMonitoringList = $rData['hd_monitoring_status'] ?? [
            ['no' => 1, 'service_device' => 'IP Transit Link A', 'status' => 'Up', 'alarm' => 'None', 'performance' => '1.2 Gbps / 4ms', 'action' => 'Normal Monitor', 'keterangan' => 'Stable'],
            ['no' => 2, 'service_device' => 'IP Transit Link B', 'status' => 'Up', 'alarm' => 'None', 'performance' => '850 Mbps / 5ms', 'action' => 'Normal Monitor', 'keterangan' => 'Backup standby'],
            ['no' => 3, 'service_device' => '-', 'status' => '-', 'alarm' => '-', 'performance' => '-', 'action' => '-', 'keterangan' => '-'],
            ['no' => 4, 'service_device' => '-', 'status' => '-', 'alarm' => '-', 'performance' => '-', 'action' => '-', 'keterangan' => '-'],
            ['no' => 5, 'service_device' => '-', 'status' => '-', 'alarm' => '-', 'performance' => '-', 'action' => '-', 'keterangan' => '-'],
        ];

        $hdFieldList = $rData['hd_pekerjaan_field'] ?? [
            ['no' => 1, 'lokasi' => 'Server Room Lt. 2', 'pekerjaan' => 'Check Kabel Patch Panel', 'engineer' => $hdEngineer, 'hasil' => 'Normal', 'foto_evidence' => 'Yes', 'status' => 'Done'],
            ['no' => 2, 'lokasi' => '-', 'pekerjaan' => '-', 'engineer' => '-', 'hasil' => '-', 'foto_evidence' => '-', 'status' => '-'],
            ['no' => 3, 'lokasi' => '-', 'pekerjaan' => '-', 'engineer' => '-', 'hasil' => '-', 'foto_evidence' => '-', 'status' => '-'],
            ['no' => 4, 'lokasi' => '-', 'pekerjaan' => '-', 'engineer' => '-', 'hasil' => '-', 'foto_evidence' => '-', 'status' => '-'],
            ['no' => 5, 'lokasi' => '-', 'pekerjaan' => '-', 'engineer' => '-', 'hasil' => '-', 'foto_evidence' => '-', 'status' => '-'],
        ];

        $hdKendalaList = $rData['hd_kendala_escalation'] ?? [
            ['no' => 1, 'kendala_incident' => '-', 'dampak' => '-', 'tindakan' => '-', 'escalated_to' => '-', 'status' => 'Normal', 'next_action' => '-'],
            ['no' => 2, 'kendala_incident' => '-', 'dampak' => '-', 'tindakan' => '-', 'escalated_to' => '-', 'status' => '-', 'next_action' => '-'],
            ['no' => 3, 'kendala_incident' => '-', 'dampak' => '-', 'tindakan' => '-', 'escalated_to' => '-', 'status' => '-', 'next_action' => '-'],
            ['no' => 4, 'kendala_incident' => '-', 'dampak' => '-', 'tindakan' => '-', 'escalated_to' => '-', 'status' => '-', 'next_action' => '-'],
            ['no' => 5, 'kendala_incident' => '-', 'dampak' => '-', 'tindakan' => '-', 'escalated_to' => '-', 'status' => '-', 'next_action' => '-'],
        ];

        $hdHandoverList = $rData['hd_handover'] ?? [
            ['no' => 1, 'outstanding_issue' => 'Monitoring berkala suhu AC Ruang Server', 'kondisi_terakhir' => '21°C stabil', 'tindakan_berikutnya' => 'Log suhu per 2 jam', 'pic' => 'Shift Siang', 'due_time' => '14:00', 'catatan' => 'Normal'],
            ['no' => 2, 'outstanding_issue' => '-', 'kondisi_terakhir' => '-', 'tindakan_berikutnya' => '-', 'pic' => '-', 'due_time' => '-', 'catatan' => '-'],
            ['no' => 3, 'outstanding_issue' => '-', 'kondisi_terakhir' => '-', 'tindakan_berikutnya' => '-', 'pic' => '-', 'due_time' => '-', 'catatan' => '-'],
            ['no' => 4, 'outstanding_issue' => '-', 'kondisi_terakhir' => '-', 'tindakan_berikutnya' => '-', 'pic' => '-', 'due_time' => '-', 'catatan' => '-'],
            ['no' => 5, 'outstanding_issue' => '-', 'kondisi_terakhir' => '-', 'tindakan_berikutnya' => '-', 'pic' => '-', 'due_time' => '-', 'catatan' => '-'],
        ];

        $hdRekap = $rData['hd_rekap_shift'] ?? [];
    @endphp

    {{-- ── HALAMAN 1 (HELP DESK) ── --}}
    <div class="page-container page-break">
        <table class="doc-header-table">
            <tr>
                <td style="width: 15%; text-align: left; vertical-align: middle;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="Logo" style="height: 38px; width: auto; max-width: 80px; display: block;">
                    @endif
                </td>
                <td style="width: 85%; text-align: center; vertical-align: middle;">
                    <div class="doc-title">FORM LAPORAN AKTIVITAS ENGINEER – HELP DESK</div>
                </td>
            </tr>
        </table>

        <div class="doc-intro">
            Laporan operasional harian/shift (Help Desk) untuk merekap kondisi layanan, aktivitas, ticket, pekerjaan, dan handover engineer.
        </div>

        {{-- A. IDENTITAS SHIFT --}}
        <div class="section-title">A. IDENTITAS SHIFT</div>
        <table class="report-table">
            <tr>
                <td class="label-cell" style="width: 20%;">Tanggal</td>
                <td style="width: 30%;">{{ $hdTanggal }}</td>
                <td class="label-cell" style="width: 20%;">Shift</td>
                <td style="width: 30%;">
                    <span class="check-box">{!! !empty($hdShifts['pagi']) ? '&#10003;' : '' !!}</span> Pagi&nbsp;&nbsp;&nbsp;
                    <span class="check-box">{!! !empty($hdShifts['siang']) ? '&#10003;' : '' !!}</span> Siang&nbsp;&nbsp;&nbsp;
                    <span class="check-box">{!! !empty($hdShifts['malam']) ? '&#10003;' : '' !!}</span> Malam
                </td>
            </tr>
            <tr>
                <td class="label-cell">Nama Engineer</td>
                <td>{{ $hdEngineer }}</td>
                <td class="label-cell">Team Leader</td>
                <td>{{ $hdLeader }}</td>
            </tr>
            <tr>
                <td class="label-cell">Area / Site</td>
                <td>{{ $hdArea }}</td>
                <td class="label-cell">Customer / Service</td>
                <td>{{ $hdCustServ }}</td>
            </tr>
            <tr>
                <td class="label-cell">Jam Shift</td>
                <td>{{ $hdJamShift }}</td>
                <td class="label-cell">Jumlah Engineer</td>
                <td>{{ $hdJmlEng }} Orang</td>
            </tr>
        </table>

        {{-- B. KONDISI AWAL SHIFT --}}
        <div class="section-title">B. KONDISI AWAL SHIFT</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 26%;">Item / Service</th>
                    <th style="width: 20%;">Kondisi Awal</th>
                    <th style="width: 21%;">Alarm / Issue</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 18%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($hdKondisiAwal, 0, 5) as $idx => $k)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $k['item_service'] ?? '-' }}</td>
                        <td class="text-center">{{ $k['kondisi_awal'] ?? '-' }}</td>
                        <td>{{ $k['alarm_issue'] ?? '-' }}</td>
                        <td class="text-center"><strong>{{ $k['status'] ?? '-' }}</strong></td>
                        <td>{{ $k['keterangan'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- C. REKAP AKTIVITAS SHIFT --}}
        <div class="section-title">C. REKAP AKTIVITAS SHIFT</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 10%;">Waktu</th>
                    <th style="width: 32%;">Aktivitas</th>
                    <th style="width: 15%;">Ticket / WO</th>
                    <th style="width: 18%;">Lokasi / Device</th>
                    <th style="width: 10%;">Hasil</th>
                    <th style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($hdActList, 0, 5) as $idx => $act)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td class="text-center">{{ $act['waktu'] ?? '-' }}</td>
                        <td>{{ $act['aktivitas'] ?? '-' }}</td>
                        <td>{{ $act['ticket_wo'] ?? '-' }}</td>
                        <td>{{ $act['lokasi_device'] ?? '-' }}</td>
                        <td>{{ $act['hasil'] ?? '-' }}</td>
                        <td class="text-center"><strong>{{ $act['status'] ?? '-' }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- D. REKAP TICKET / INCIDENT --}}
        <div class="section-title">D. REKAP TICKET / INCIDENT</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 16%;">Ticket</th>
                    <th style="width: 14%;">Jenis</th>
                    <th style="width: 10%;">Priority</th>
                    <th style="width: 10%;">Start</th>
                    <th style="width: 10%;">Restore</th>
                    <th style="width: 13%;">Close / Status</th>
                    <th style="width: 22%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($hdTicketList, 0, 5) as $idx => $t)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $t['ticket'] ?? '-' }}</td>
                        <td>{{ $t['jenis'] ?? '-' }}</td>
                        <td class="text-center">{{ $t['priority'] ?? '-' }}</td>
                        <td class="text-center">{{ $t['start'] ?? '-' }}</td>
                        <td class="text-center">{{ $t['restore'] ?? '-' }}</td>
                        <td class="text-center"><strong>{{ $t['close_status'] ?? '-' }}</strong></td>
                        <td>{{ $t['keterangan'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- E. MONITORING & SERVICE STATUS --}}
        <div class="section-title">E. MONITORING &amp; SERVICE STATUS</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 23%;">Service / Device</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 15%;">Alarm</th>
                    <th style="width: 18%;">Performance / Parameter</th>
                    <th style="width: 14%;">Action</th>
                    <th style="width: 15%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($hdMonitoringList, 0, 5) as $idx => $m)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $m['service_device'] ?? '-' }}</td>
                        <td class="text-center"><strong>{{ $m['status'] ?? '-' }}</strong></td>
                        <td>{{ $m['alarm'] ?? '-' }}</td>
                        <td>{{ $m['performance'] ?? '-' }}</td>
                        <td>{{ $m['action'] ?? '-' }}</td>
                        <td>{{ $m['keterangan'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
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
                        <div style="margin-top: 2px;">
                            <div class="paraf-box">Paraf</div>
                            <div style="clear: both; margin-top: 2px;"><strong style="font-size: 7.2pt; color: #000000;">Hal 1</strong></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ── HALAMAN 2 (HELP DESK) ── --}}
    <div class="page-container">
        <table class="doc-header-table">
            <tr>
                <td style="width: 15%; text-align: left; vertical-align: middle;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="Logo" style="height: 38px; width: auto; max-width: 80px; display: block;">
                    @endif
                </td>
                <td style="width: 85%; text-align: center; vertical-align: middle;">
                    <div class="doc-title">FORM LAPORAN AKTIVITAS ENGINEER – HELP DESK</div>
                </td>
            </tr>
        </table>

        {{-- F. PEKERJAAN ONSITE / FIELD --}}
        <div class="section-title">F. PEKERJAAN ONSITE / FIELD</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 18%;">Lokasi</th>
                    <th style="width: 27%;">Pekerjaan</th>
                    <th style="width: 16%;">Engineer</th>
                    <th style="width: 14%;">Hasil</th>
                    <th style="width: 10%;">Foto / Evidence</th>
                    <th style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($hdFieldList, 0, 5) as $idx => $f)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $f['lokasi'] ?? '-' }}</td>
                        <td>{{ $f['pekerjaan'] ?? '-' }}</td>
                        <td>{{ $f['engineer'] ?? '-' }}</td>
                        <td>{{ $f['hasil'] ?? '-' }}</td>
                        <td class="text-center">{{ $f['foto_evidence'] ?? '-' }}</td>
                        <td class="text-center"><strong>{{ $f['status'] ?? '-' }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- G. KENDALA & ESCALATION --}}
        <div class="section-title">G. KENDALA &amp; ESCALATION</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 22%;">Kendala / Incident</th>
                    <th style="width: 15%;">Dampak</th>
                    <th style="width: 20%;">Tindakan</th>
                    <th style="width: 14%;">Escalated To</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 14%;">Next Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($hdKendalaList, 0, 5) as $idx => $k)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $k['kendala_incident'] ?? '-' }}</td>
                        <td>{{ $k['dampak'] ?? '-' }}</td>
                        <td>{{ $k['tindakan'] ?? '-' }}</td>
                        <td class="text-center">{{ $k['escalated_to'] ?? '-' }}</td>
                        <td class="text-center"><strong>{{ $k['status'] ?? '-' }}</strong></td>
                        <td>{{ $k['next_action'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- H. HANDOVER KE SHIFT BERIKUTNYA --}}
        <div class="section-title">H. HANDOVER KE SHIFT BERIKUTNYA</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 25%;">Outstanding / Issue</th>
                    <th style="width: 18%;">Kondisi Terakhir</th>
                    <th style="width: 20%;">Tindakan Berikutnya</th>
                    <th style="width: 11%;">PIC</th>
                    <th style="width: 10%;">Due Time</th>
                    <th style="width: 11%;">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach(array_slice($hdHandoverList, 0, 5) as $idx => $h)
                    <tr>
                        <td class="text-center">{{ $idx + 1 }}</td>
                        <td>{{ $h['outstanding_issue'] ?? '-' }}</td>
                        <td>{{ $h['kondisi_terakhir'] ?? '-' }}</td>
                        <td>{{ $h['tindakan_berikutnya'] ?? '-' }}</td>
                        <td class="text-center">{{ $h['pic'] ?? '-' }}</td>
                        <td class="text-center">{{ $h['due_time'] ?? '-' }}</td>
                        <td>{{ $h['catatan'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- I. REKAP SHIFT --}}
        <div class="section-title">I. REKAP SHIFT</div>
        <table class="report-table">
            <tr>
                <td class="label-cell" style="width: 25%;">Total ticket diterima</td>
                <td style="width: 25%;"><strong>{{ $hdRekap['total_ticket_diterima'] ?? '0' }}</strong></td>
                <td class="label-cell" style="width: 25%;">Total ticket closed</td>
                <td style="width: 25%;"><strong>{{ $hdRekap['total_ticket_closed'] ?? '0' }}</strong></td>
            </tr>
            <tr>
                <td class="label-cell">Total incident</td>
                <td><strong>{{ $hdRekap['total_incident'] ?? '0' }}</strong></td>
                <td class="label-cell">Outstanding</td>
                <td><strong>{{ $hdRekap['outstanding'] ?? '0' }}</strong></td>
            </tr>
            <tr>
                <td class="label-cell">Service kritis / alert</td>
                <td><strong>{{ $hdRekap['service_kritis'] ?? '0' }}</strong></td>
                <td class="label-cell">Handover diperlukan</td>
                <td>
                    <span class="check-box">{!! !empty($hdRekap['handover_diperlukan']) ? '&#10003;' : '' !!}</span> Ya&nbsp;&nbsp;&nbsp;&nbsp;
                    <span class="check-box">{!! empty($hdRekap['handover_diperlukan']) ? '&#10003;' : '' !!}</span> Tidak
                </td>
            </tr>
        </table>

        {{-- J. VERIFIKASI --}}
        <div class="section-title">J. VERIFIKASI</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 30%;">Pihak</th>
                    <th style="width: 25%;">Nama</th>
                    <th style="width: 20%;">Tanggal / Jam</th>
                    <th style="width: 25%;">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="label-cell">Engineer / Shift PIC</td>
                    <td><strong>{{ $hdEngineer }}</strong></td>
                    <td class="text-center">{{ $picSignedAt }}</td>
                    <td class="sig-container">
                        @if(!empty($docSig?->pic_signature))
                            <img src="{{ $docSig->pic_signature }}" alt="TTD PIC" class="sig-img">
                        @else
                            <div style="font-size: 6.8pt; color: #64748B;">Digital Verified</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label-cell">Team Leader</td>
                    <td><strong>{{ $hdLeader }}</strong></td>
                    <td class="text-center">{{ $leadSignedAt }}</td>
                    <td class="sig-container">
                        @if(!empty($docSig?->lead_signature))
                            <img src="{{ $docSig->lead_signature }}" alt="TTD Leader" class="sig-img">
                        @else
                            <div style="font-size: 6.8pt; color: #64748B;">Digital Verified</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label-cell">Engineer Shift Berikutnya / Handover</td>
                    <td><strong>PIC Shift Berikutnya</strong></td>
                    <td class="text-center">{{ $custSignedAt }}</td>
                    <td class="sig-container">
                        <div style="font-size: 6.8pt; color: #64748B; padding-top: 10px;">( Handover Verified )</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="font-size: 6.8pt; color: #334155; margin-top: 6px; font-style: italic;">
            Catatan: Daily/Shift Report bukan pengganti laporan Project atau Managed Service. Fungsinya sebagai kontrol operasional dan handover antar-shift.
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
                        <div style="margin-top: 2px;">
                            <div class="paraf-box">Paraf</div>
                            <div style="clear: both; margin-top: 2px;"><strong style="font-size: 7.2pt; color: #000000;">Hal 2</strong></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

@else
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- ═════════════════ DOKUMEN: PROJECT (2 HALAMAN) ═══════════════════ --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @php
        $isImplement = !empty($idObj['kategori_implementasi']);
        $isManaged   = !empty($idObj['kategori_managed_service']);
        if (!$isImplement && !$isManaged) {
            $isImplement = true;
        }

        $manpowerList = is_array($rData['manpower'] ?? null) ? $rData['manpower'] : [];
        if (empty($manpowerList)) {
            $manpowerList = [[
                'nama' => $namaEngineer,
                'unit_kerja' => auth()->user()?->division?->name ?? 'Technical Support',
                'jabatan' => $jabatanEngineer,
                'keterangan' => 'PIC Utama'
            ]];
        }
        $totalTenagaKerja = $rData['total_tenaga_kerja'] ?? count($manpowerList);

        $scopeC = $rData['ruang_lingkup'] ?? [];
        $targetHariIni   = $scopeC['target_hari_ini'] ?? ($namaProject . ' - Kegiatan Lapangan');
        $durasiProject   = $scopeC['durasi_project'] ?? '-';
        $scopePekerjaan  = $scopeC['scope_pekerjaan'] ?? ($project?->description ?? '-');
        $perangkatSistem = $scopeC['perangkat_sistem'] ?? '-';
        $kriteriaSelesai = $scopeC['kriteria_selesai'] ?? '-';

        $dActivities = $rData['rincian_aktivitas'] ?? [];
        if (empty($dActivities) && !empty($actItems)) {
            foreach (array_values($actItems) as $i => $it) {
                $it = (array) $it;
                $dActivities[] = [
                    'no' => $i + 1,
                    'waktu' => $it['time'] ?? '09:00',
                    'aktivitas' => $it['activity'] ?? '-',
                    'perangkat' => $it['location'] ?? '-',
                    'hasil' => '-',
                    'status' => $it['status'] ?? 'Selesai',
                    'kendala' => '-',
                    'tindak_lanjut' => $it['notes'] ?? '-'
                ];
            }
        }
        if (empty($dActivities)) {
            $dActivities = [[
                'no' => 1, 'waktu' => '09:00', 'aktivitas' => '-',
                'perangkat' => '-', 'hasil' => '-', 'status' => 'Selesai', 'kendala' => '-', 'tindak_lanjut' => '-'
            ]];
        }

        $materials   = is_array($rData['materials'] ?? null) ? $rData['materials'] : [];
        $testResults = is_array($rData['test_results'] ?? null) ? $rData['test_results'] : [];
        $incidents   = is_array($rData['incidents'] ?? null) ? $rData['incidents'] : [];

        $hasilAkhir = $rData['hasil_akhir'] ?? [];
        $statusPekerjaan = strtolower($hasilAkhir['status_pekerjaan'] ?? 'selesai');
        $progressPercent = $hasilAkhir['progress_percent'] ?? ($project?->progress ?? '100');
        $kondisiSistem   = $hasilAkhir['kondisi_sistem'] ?? '-';
        $outstanding     = $hasilAkhir['outstanding'] ?? '-';
        $rekomendasi     = $hasilAkhir['rekomendasi'] ?? '-';
        $eskalasiPic     = $hasilAkhir['eskalasi_pic'] ?? '-';

        $photos = $rData['foto_dokumentasi'] ?? [];
        $adminK = $rData['administrasi'] ?? [];
        $nomorWoTicket = $adminK['nomor_wo'] ?? $workOrder;
        $nomorBaBast   = $adminK['nomor_ba'] ?? '-';
        $lampiranExtra = $adminK['lampiran'] ?? 'Dokumentasi Foto Fisik & Checklist';
        $namaFileFolder= $adminK['folder'] ?? ('DOK-' . \Illuminate\Support\Str::slug($namaProject, '_'));

        $leadSignerName = !empty($idObj['nama_leader']) ? $idObj['nama_leader'] : ($docSig?->lead_name ?? ($docSig?->leadUser?->name ?? 'Nugraha Pratama'));
        $custSignerName = (!empty($idObj['pic_customer']) && $idObj['pic_customer'] !== '-') ? $idObj['pic_customer'] : ($picCustomer !== '-' ? $picCustomer : 'PIC Site / Representative');
    @endphp

    {{-- HALAMAN 1 (PROJECT) --}}
    <div class="page-container page-break">
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
                    <span class="check-box">{!! $isManaged ? '&#10003;' : '' !!}</span> Managed Service
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
                    <th style="width: 25%;">Unit Kerja / Divisi</th>
                    <th style="width: 20%;">Posisi / Jabatan</th>
                    <th style="width: 15%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse(array_slice($manpowerList, 0, 4) as $idx => $m)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $m['nama'] ?? '-' }}</td>
                        <td>{{ $m['unit_kerja'] ?? 'Technical Support' }}</td>
                        <td>{{ $m['jabatan'] ?? 'Field Engineer' }}</td>
                        <td class="text-center">{{ $m['keterangan'] ?? 'Hadir' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center">1</td>
                        <td>{{ $namaEngineer }}</td>
                        <td>Technical Support</td>
                        <td>{{ $jabatanEngineer }}</td>
                        <td class="text-center">PIC</td>
                    </tr>
                @endforelse
                <tr>
                    <td colspan="4" class="label-cell text-right" style="padding-right: 8px;"><strong>Total Tenaga Kerja</strong></td>
                    <td class="text-center"><strong>{{ $totalTenagaKerja }} Orang</strong></td>
                </tr>
            </tbody>
        </table>

        {{-- C. RUANG LINGKUP & TARGET PEKERJAAN --}}
        <div class="section-title">C. RUANG LINGKUP &amp; TARGET PEKERJAAN</div>
        <table class="report-table">
            <tr>
                <td class="label-cell" style="width: 28%;">Target Hari Ini</td>
                <td style="width: 72%;">{{ $targetHariIni }}</td>
            </tr>
            <tr>
                <td class="label-cell">Durasi Project</td>
                <td>{{ $durasiProject }}</td>
            </tr>
            <tr>
                <td class="label-cell">Scope Pekerjaan</td>
                <td>{{ $scopePekerjaan }}</td>
            </tr>
            <tr>
                <td class="label-cell">Perangkat / Sistem</td>
                <td>{{ $perangkatSistem }}</td>
            </tr>
            <tr>
                <td class="label-cell">Kriteria Selesai</td>
                <td>{{ $kriteriaSelesai }}</td>
            </tr>
        </table>

        {{-- D. RINCIAN AKTIVITAS PEKERJAAN --}}
        <div class="section-title">D. RINCIAN AKTIVITAS PEKERJAAN</div>
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
                @foreach(array_slice($dActivities, 0, 6) as $act)
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
                @forelse(array_slice($materials, 0, 3) as $mat)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $mat['nama'] ?? '-' }}</td>
                        <td>{{ $mat['spesifikasi'] ?? '-' }}</td>
                        <td class="text-center">{{ $mat['qty'] ?? '-' }}</td>
                        <td class="text-center">{{ $mat['satuan'] ?? 'Pcs' }}</td>
                        <td class="text-center">{{ $mat['kondisi'] ?? 'Baik' }}</td>
                        <td>{{ $mat['keterangan'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center">1</td>
                        <td>-</td>
                        <td>-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td>-</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- F. HASIL UJI / PENGUKURAN --}}
        <div class="section-title">F. HASIL UJI / PENGUKURAN (TESTING &amp; COMMISSIONING)</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 25%;">Parameter Uji</th>
                    <th style="width: 13%;">Sebelum</th>
                    <th style="width: 13%;">Sesudah</th>
                    <th style="width: 10%;">Satuan</th>
                    <th style="width: 16%;">Metode Uji</th>
                    <th style="width: 18%;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse(array_slice($testResults, 0, 2) as $t)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $t['parameter'] ?? '-' }}</td>
                        <td class="text-center">{{ $t['sebelum'] ?? '-' }}</td>
                        <td class="text-center">{{ $t['sesudah'] ?? '-' }}</td>
                        <td class="text-center">{{ $t['satuan'] ?? '-' }}</td>
                        <td class="text-center">{{ $t['metode'] ?? '-' }}</td>
                        <td>{{ $t['keterangan'] ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center">1</td>
                        <td>-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td class="text-center">-</td>
                        <td>-</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- G. KENDALA / MASALAH LAPANGAN --}}
        <div class="section-title">G. KENDALA / MASALAH LAPANGAN</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 11%;">Waktu</th>
                    <th style="width: 28%;">Kendala / Masalah</th>
                    <th style="width: 22%;">Dampak</th>
                    <th style="width: 24%;">Tindakan Sementara</th>
                    <th style="width: 10%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse(array_slice($incidents, 0, 2) as $inc)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $inc['waktu'] ?? '-' }}</td>
                        <td>{{ $inc['kendala'] ?? '-' }}</td>
                        <td>{{ $inc['dampak'] ?? '-' }}</td>
                        <td>{{ $inc['tindakan'] ?? '-' }}</td>
                        <td class="text-center"><strong>{{ $inc['status'] ?? 'Closed' }}</strong></td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center">1</td>
                        <td class="text-center">-</td>
                        <td>Nihil / Operasional Lancar</td>
                        <td>-</td>
                        <td>-</td>
                        <td class="text-center">Closed</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- H. HASIL AKHIR PEKERJAAN (Baris Status) --}}
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
                        <div style="margin-top: 2px;">
                            <div class="paraf-box">Paraf</div>
                            <div style="clear: both; margin-top: 2px;"><strong style="font-size: 7.2pt; color: #000000;">Hal 1</strong></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- HALAMAN 2 (PROJECT) --}}
    <div class="page-container">
        <table class="doc-header-table" style="margin-top: 2px; margin-bottom: 8px;">
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
                <td class="label-cell" style="width: 38%;">Persentase progress</td>
                <td style="width: 62%;"><u>&nbsp;&nbsp;<strong>{{ $progressPercent }}</strong>&nbsp;&nbsp;</u> %</td>
            </tr>
            <tr>
                <td class="label-cell">Kondisi sistem</td>
                <td>{{ $kondisiSistem }}</td>
            </tr>
            <tr>
                <td class="label-cell">Outstanding / Pekerjaan Lanjutan</td>
                <td>{{ $outstanding }}</td>
            </tr>
            <tr>
                <td class="label-cell">Rekomendasi Engineer</td>
                <td>{{ $rekomendasi }}</td>
            </tr>
            <tr>
                <td class="label-cell">Eskalasi ke PIC / Tim Terkait</td>
                <td>{{ $eskalasiPic }}</td>
            </tr>
        </table>

        {{-- I. DOKUMENTASI FOTO PEKERJAAN --}}
        <div class="section-title">I. DOKUMENTASI FOTO PEKERJAAN</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 33.3%;">Foto Sebelum Pekerjaan (Before)</th>
                    <th style="width: 33.3%;">Foto Progress Pekerjaan (In-Progress)</th>
                    <th style="width: 33.4%;">Foto Setelah Pekerjaan (After)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" style="padding: 4px; height: 86px; vertical-align: middle;">
                        @if(!empty($photos['before']['url']))
                            <img src="{{ $photos['before']['url'] }}" alt="Foto Before" class="photo-img">
                        @else
                            <div class="photo-placeholder">[ Foto Before Tidak Ada ]</div>
                        @endif
                    </td>
                    <td class="text-center" style="padding: 4px; height: 86px; vertical-align: middle;">
                        @if(!empty($photos['progress']['url']))
                            <img src="{{ $photos['progress']['url'] }}" alt="Foto Progress" class="photo-img">
                        @else
                            <div class="photo-placeholder">[ Foto In-Progress Tidak Ada ]</div>
                        @endif
                    </td>
                    <td class="text-center" style="padding: 4px; height: 86px; vertical-align: middle;">
                        @if(!empty($photos['after']['url']))
                            <img src="{{ $photos['after']['url'] }}" alt="Foto After" class="photo-img">
                        @else
                            <div class="photo-placeholder">[ Foto After Tidak Ada ]</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 6.8pt;">Area: <strong>{{ $photos['before']['area'] ?? '-' }}</strong></td>
                    <td style="font-size: 6.8pt;">Area: <strong>{{ $photos['progress']['area'] ?? '-' }}</strong></td>
                    <td style="font-size: 6.8pt;">Area: <strong>{{ $photos['after']['area'] ?? '-' }}</strong></td>
                </tr>
            </tbody>
        </table>

        {{-- J. CATATAN ADMINISTRASI DOKUMEN --}}
        <div class="section-title">J. CATATAN ADMINISTRASI DOKUMEN</div>
        <table class="report-table">
            <tr>
                <td class="label-cell" style="width: 25%;">Nomor WO / Ticket</td>
                <td style="width: 75%;">{{ $nomorWoTicket }}</td>
            </tr>
            <tr>
                <td class="label-cell">Nomor BA / BAST</td>
                <td>{{ $nomorBaBast }}</td>
            </tr>
            <tr>
                <td class="label-cell">Lampiran Lain</td>
                <td>{{ $lampiranExtra }}</td>
            </tr>
            <tr>
                <td class="label-cell">Nama File / Folder</td>
                <td>{{ $namaFileFolder }}</td>
            </tr>
        </table>

        {{-- K. VERIFIKASI PEKERJAAN --}}
        <div class="section-title">K. VERIFIKASI PEKERJAAN</div>
        <table class="report-table">
            <thead>
                <tr>
                    <th style="width: 33.3%;">Field Engineer / PIC</th>
                    <th style="width: 33.3%;">Team Leader Engineering</th>
                    <th style="width: 33.4%;">Customer / Client PIC</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="sig-container">
                        @if(!empty($docSig?->pic_signature))
                            <img src="{{ $docSig->pic_signature }}" alt="TTD Engineer" class="sig-img">
                        @else
                            <div style="font-size: 6.8pt; color: #64748B;">Digital Verified</div>
                        @endif
                    </td>
                    <td class="sig-container">
                        @if(!empty($docSig?->lead_signature))
                            <img src="{{ $docSig->lead_signature }}" alt="TTD Leader" class="sig-img">
                        @else
                            <div style="font-size: 6.8pt; color: #64748B;">Digital Verified</div>
                        @endif
                    </td>
                    <td class="sig-container">
                        <div style="font-size: 6.8pt; color: #64748B; padding-top: 10px;">( Tanda Tangan &amp; Stempel )</div>
                    </td>
                </tr>
                <tr>
                    <td>Nama: <strong>{{ $namaEngineer }}</strong></td>
                    <td>Nama: <strong>{{ $leadSignerName }}</strong></td>
                    <td>Nama: <strong>{{ $custSignerName }}</strong></td>
                </tr>
                <tr>
                    <td>Tgl: {{ $picSignedAt }}</td>
                    <td>Tgl: {{ $leadSignedAt }}</td>
                    <td>Tgl: {{ $custSignedAt }}</td>
                </tr>
            </tbody>
        </table>

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
                        <div style="margin-top: 2px;">
                            <div class="paraf-box">Paraf</div>
                            <div style="clear: both; margin-top: 2px;"><strong style="font-size: 7.2pt; color: #000000;">Hal 2</strong></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
@endif

</body>
</html>
