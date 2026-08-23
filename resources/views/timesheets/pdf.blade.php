<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Timesheet - IP Network Solusindo</title>
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
            padding-bottom: 12px;
            margin-bottom: 14px;
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
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
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
            padding: 6px 5px;
            text-align: center;
            border: 1px solid #94A3B8;
        }
        .data-table td {
            padding: 5px 5px;
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
        .category-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .cat-onsite { background: #FDF1F2; color: #C81E2C; }
        .cat-remote { background: #EFF6FF; color: #1D4ED8; }
        .cat-overtime { background: #FEF3C7; color: #B45309; }
        .cat-maint { background: #ECFDF5; color: #047857; }
        .total-row td {
            background-color: #E2E8F0;
            font-weight: bold;
            border-top: 2px solid #94A3B8;
            padding: 6px 5px;
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
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="company-title">PT IP NETWORK SOLUSINDO</div>
                <div class="company-subtitle">FIELD SYSTEM MANAGEMENT - LEMBAR KERJA / TIMESHEET</div>
            </td>
            <td style="width: 40%; text-align: right;">
                <span class="report-badge">DOKUMEN RESMI REKAP KERJA</span>
            </td>
        </tr>
    </table>

    <!-- Metadata Filter -->
    <div class="meta-container">
        <table class="meta-table">
            <tr>
                <td style="width: 15%; font-weight: bold; color: #75727C;">Filter Engineer</td>
                <td style="width: 35%;">: {{ $filters['engineer_name'] ?? 'Semua Engineer' }}</td>
                <td style="width: 15%; font-weight: bold; color: #75727C;">Periode</td>
                <td style="width: 35%;">: {{ $filters['period_text'] ?? 'Semua Waktu' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #75727C;">Filter Project</td>
                <td>: {{ $filters['project_name'] ?? 'Semua Project' }}</td>
                <td style="font-weight: bold; color: #75727C;">Tanggal Cetak</td>
                <td>: {{ $generatedAt }} WIB</td>
            </tr>
        </table>
    </div>

    <!-- Summary Box -->
    <table class="summary-cards" style="width: 100%; border-spacing: 6px 0; margin-left: -6px; margin-right: -6px;">
        <tr>
            <td style="width: 25%;">
                <div class="summary-box">
                    <div class="summary-title">Total Jam Kerja</div>
                    <div class="summary-value" style="color: #C81E2C;">{{ $totalHours }} Jam</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="summary-box">
                    <div class="summary-title">Total Hari Kerja</div>
                    <div class="summary-value">{{ $uniqueDays }} Hari</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="summary-box">
                    <div class="summary-title">Total Entri Log</div>
                    <div class="summary-value">{{ $timesheets->count() }} Entri</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="summary-box">
                    <div class="summary-title">Jumlah Engineer</div>
                    <div class="summary-value">{{ $engineerCount }} Orang</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 65px;">Tanggal</th>
                <th style="width: 100px;">Engineer</th>
                <th style="width: 110px;">Project</th>
                <th style="width: 110px;">Task</th>
                <th style="width: 70px;">Jam Kerja</th>
                <th style="width: 55px;">Durasi</th>
                <th style="width: 65px;">Kategori</th>
                <th>Uraian Aktivitas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($timesheets as $index => $ts)
                <tr class="{{ $index % 2 === 1 ? 'row-even' : '' }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $ts->date ? $ts->date->format('d/m/Y') : '-' }}</td>
                    <td style="font-weight: 600;">{{ $ts->user?->name ?? '-' }}</td>
                    <td>{{ $ts->project?->name ?? '-' }}</td>
                    <td>{{ $ts->task?->title ?? '-' }}</td>
                    <td class="text-center">{{ substr($ts->start_time, 0, 5) }} - {{ substr($ts->end_time, 0, 5) }}</td>
                    <td class="text-center" style="font-weight: 600; color: #1E293B;">{{ round($ts->duration_minutes / 60, 2) }} j</td>
                    <td class="text-center">
                        @php
                            $catClass = match($ts->category) {
                                'Remote' => 'cat-remote',
                                'Overtime' => 'cat-overtime',
                                'Maintenance' => 'cat-maint',
                                default => 'cat-onsite'
                            };
                        @endphp
                        <span class="category-badge {{ $catClass }}">{{ $ts->category }}</span>
                    </td>
                    <td>
                        <div style="font-weight: 500;">{{ $ts->activity }}</div>
                        @if($ts->notes)
                            <div style="font-size: 8px; color: #75727C; margin-top: 2px;">Catatan: {{ $ts->notes }}</div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 18px; color: #75727C; font-style: italic;">
                        Tidak ada catatan timesheet untuk periode filter ini.
                    </td>
                </tr>
            @endforelse

            @if($timesheets->isNotEmpty())
                <tr class="total-row">
                    <td colspan="6" class="text-right">TOTAL DURASI JAM KERJA :</td>
                    <td class="text-center" style="color: #C81E2C;">{{ $totalHours }} Jam</td>
                    <td colspan="2" style="font-size: 8.5px; color: #475569;">Total {{ $timesheets->count() }} aktivitas pengerjaan</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Signature Block -->
    <table class="signature-table" style="width: 100%; margin-top: 25px; border-collapse: collapse;">
        <tr>
            <td style="width: {{ $showMaker ? '45%' : '65%' }}; vertical-align: top;">
                <div style="font-size: 8.5px; color: #75727C; line-height: 1.4;">
                    * Dokumen rekapitulasi jam kerja ini digenerate secara otomatis melalui sistem Field System Management IP-Net.<br>
                    * Informasi ini digunakan sebagai acuan monitoring produktivitas dan pertanggungjawaban pengerjaan proyek.
                </div>
            </td>
            @if($showMaker)
            <td style="width: 27%; text-align: center; vertical-align: top;">
                <div style="font-size: 9px; color: #75727C; margin-bottom: 45px;">
                    Jakarta, {{ now()->isoFormat('D MMMM Y') }}<br>
                    <strong>Dibuat Oleh,</strong>
                </div>
                <div style="font-weight: bold; border-bottom: 1px solid #17151C; padding-bottom: 2px; color: #17151C;">
                    {{ $makerName }}
                </div>
                <div style="font-size: 8.5px; color: #75727C; margin-top: 2px;">{{ $makerPosition }}</div>
            </td>
            @endif
            <td style="width: {{ $showMaker ? '28%' : '35%' }}; text-align: center; vertical-align: top;">
                <div style="font-size: 9px; color: #75727C; margin-bottom: 45px;">
                    @if(!$showMaker)
                    Jakarta, {{ now()->isoFormat('D MMMM Y') }}<br>
                    @else
                    <br>
                    @endif
                    <strong>Mengetahui & Menyetujui,</strong>
                </div>
                <div style="font-weight: bold; border-bottom: 1px solid #17151C; padding-bottom: 2px; color: #17151C;">
                    {{ $verifierName }}
                </div>
                <div style="font-size: 8.5px; color: #75727C; margin-top: 2px;">{{ $verifierPosition }}</div>
            </td>
        </tr>
    </table>

</body>
</html>
