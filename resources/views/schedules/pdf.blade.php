<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Jadwal Kerja - PT IP Network Solusindo</title>
    <style>
        @page {
            margin: 20px 25px 25px 25px;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #17151C;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #C81E2C;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #C81E2C;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .company-subtitle {
            font-size: 9px;
            font-weight: bold;
            color: #75727C;
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .report-badge {
            display: inline-block;
            background: #FDF1F2;
            color: #C81E2C;
            border: 1px solid #FADADF;
            padding: 4px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10.5px;
            letter-spacing: 0.3px;
        }
        .meta-container {
            width: 100%;
            margin-bottom: 12px;
            background: #F8F7F6;
            border: 1px solid #E7E5E3;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .meta-table {
            width: 100%;
            font-size: 9.5px;
        }
        .meta-table td {
            padding: 2px 4px;
        }
        .summary-cards {
            width: 100%;
            margin-bottom: 12px;
        }
        .summary-box {
            border: 1px solid #E7E5E3;
            background: #FFFFFF;
            border-radius: 6px;
            padding: 6px 10px;
            text-align: center;
        }
        .summary-title {
            font-size: 8.5px;
            font-weight: bold;
            color: #75727C;
            text-transform: uppercase;
        }
        .summary-value {
            font-size: 15px;
            font-weight: bold;
            color: #17151C;
            margin-top: 2px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 9px;
        }
        .data-table th {
            background-color: #1E293B;
            color: #FFFFFF;
            font-weight: bold;
            padding: 7px 6px;
            text-align: center;
            border: 1px solid #94A3B8;
            font-size: 9px;
            letter-spacing: 0.3px;
        }
        .data-table td {
            padding: 6px 6px;
            border: 1px solid #E2E8F0;
            vertical-align: top;
        }
        .row-even {
            background-color: #F8FAFC;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .time-badge {
            display: inline-block;
            background: #EFF6FF;
            color: #1D4ED8;
            border: 1px solid #BFDBFE;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
            font-size: 8.5px;
            font-weight: bold;
            white-space: nowrap;
        }
        .project-tag {
            font-weight: bold;
            color: #1E293B;
        }
        .engineer-badge {
            display: inline-block;
            background: #F1F5F9;
            color: #334155;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: 600;
        }
        .signature-table {
            width: 100%;
            margin-top: 18px;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .empty-state {
            text-align: center;
            padding: 24px;
            color: #64748B;
            font-style: italic;
        }
        .footer-note {
            margin-top: 12px;
            font-size: 8px;
            color: #94A3B8;
            border-top: 1px dashed #CBD5E1;
            padding-top: 6px;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="company-title">PT IP NETWORK SOLUSINDO</div>
                <div class="company-subtitle">FIELD SYSTEM MANAGEMENT &mdash; LAPORAN JADWAL KERJA LAPANGAN</div>
            </td>
            <td style="width: 40%; text-align: right;">
                <span class="report-badge">DOKUMEN RESMI JADWAL KERJA</span>
            </td>
        </tr>
    </table>

    <!-- Metadata Filter -->
    <div class="meta-container">
        <table class="meta-table">
            <tr>
                <td style="width: 15%; font-weight: bold; color: #75727C;">Filter Engineer:</td>
                <td style="width: 35%; font-weight: bold; color: #17151C;">{{ $engineerFilterName }}</td>
                <td style="width: 15%; font-weight: bold; color: #75727C;">Tanggal Cetak:</td>
                <td style="width: 35%; color: #17151C;">{{ $generatedAt }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #75727C;">Total Jadwal:</td>
                <td style="color: #17151C;">{{ $totalSchedules }} Agenda Kegiatan</td>
                <td style="font-weight: bold; color: #75727C;">Dicetak Oleh:</td>
                <td style="color: #17151C;">{{ $printedBy }}</td>
            </tr>
        </table>
    </div>

    <!-- KPI Summary Cards -->
    <table class="summary-cards" style="border-collapse: separate; border-spacing: 6px 0;">
        <tr>
            <td class="summary-box" style="width: 25%;">
                <div class="summary-title">Total Jadwal Kerja</div>
                <div class="summary-value" style="color: #C81E2C;">{{ $totalSchedules }}</div>
            </td>
            <td class="summary-box" style="width: 25%;">
                <div class="summary-title">Project Terlibat</div>
                <div class="summary-value" style="color: #2563EB;">{{ $uniqueProjects }} <span style="font-size: 10px; font-weight: normal; color: #75727C;">Project</span></div>
            </td>
            <td class="summary-box" style="width: 25%;">
                <div class="summary-title">Engineer Ditugaskan</div>
                <div class="summary-value" style="color: #059669;">{{ $uniqueEngineers }} <span style="font-size: 10px; font-weight: normal; color: #75727C;">Personel</span></div>
            </td>
            <td class="summary-box" style="width: 25%;">
                <div class="summary-title">Rentang Hari</div>
                <div class="summary-value" style="color: #D97706;">{{ $uniqueDays }} <span style="font-size: 10px; font-weight: normal; color: #75727C;">Hari</span></div>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 8%;">Hari</th>
                <th style="width: 12%;">Waktu</th>
                <th style="width: 20%;">Agenda / Judul Jadwal</th>
                <th style="width: 16%;">Project</th>
                <th style="width: 14%;">Engineer</th>
                <th style="width: 16%;">Lokasi / Site</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schedules as $index => $sched)
                @php
                    $parsedDate = $sched->date ? \Carbon\Carbon::parse($sched->date) : null;
                    $dayName    = $parsedDate ? ($daysIndo[$parsedDate->format('l')] ?? $parsedDate->format('l')) : '-';
                    $dateStr    = $parsedDate ? $parsedDate->format('d/m/Y') : '-';

                    $timeStr = '-';
                    if ($sched->start_time && $sched->end_time) {
                        $timeStr = substr($sched->start_time, 0, 5) . ' - ' . substr($sched->end_time, 0, 5) . ' WIB';
                    } elseif ($sched->start_time) {
                        $timeStr = substr($sched->start_time, 0, 5) . ' WIB';
                    }
                @endphp
                <tr class="{{ $index % 2 === 1 ? 'row-even' : '' }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center" style="font-weight: 600;">{{ $dateStr }}</td>
                    <td class="text-center">{{ $dayName }}</td>
                    <td class="text-center">
                        <span class="time-badge">{{ $timeStr }}</span>
                    </td>
                    <td>
                        <strong style="color: #0F172A;">{{ $sched->title }}</strong>
                        @if(!empty($sched->description))
                            <div style="font-size: 8px; color: #64748B; margin-top: 2px;">{{ $sched->description }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="project-tag">{{ $sched->project?->name ?? 'Non-Project / Umum' }}</span>
                        @if($sched->project?->client)
                            <div style="font-size: 8px; color: #64748B;">Client: {{ $sched->project->client }}</div>
                        @endif
                    </td>
                    <td>
                        <span class="engineer-badge">{{ $sched->engineer?->name ?? 'Belum Ditentukan' }}</span>
                    </td>
                    <td>{{ $sched->location ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-state">
                        Tidak ada data jadwal kerja yang terdaftar sesuai kriteria filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature & Approval Block -->
    <table class="signature-table">
        <tr>
            <td style="width: 70%;"></td>
            <td class="signature-box" style="width: 30%;">
                <div style="font-size: 9.5px; color: #75727C; margin-bottom: 50px;">Mengetahui & Menyetujui,</div>
                <div style="font-weight: bold; text-decoration: underline; color: #17151C;">Hariyadi</div>
                <div style="font-size: 8.5px; color: #75727C;">Direktur Utama</div>
            </td>
        </tr>
    </table>

    <!-- Official Footer Note -->
    <div class="footer-note">
        * Dokumen jadwal kerja lapangan ini digenerate secara otomatis melalui sistem Field System Management PT IP Network Solusindo.<br>
        * Mohon seluruh Engineer menjalankan tugas lapangan sesuai alokasi waktu dan lokasi yang telah ditentukan. Segala perubahan jadwal wajib dikoordinasikan dengan Lead Engineer.
    </div>

</body>
</html>
