<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }} - PT IP Network Solusindo</title>
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
            padding: 5px 6px;
            border: 1px solid #E2E8F0;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) {
            background-color: #F8FAFC;
        }
        .badge-status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8.5px;
            font-weight: bold;
            text-align: center;
        }
        .footer-signature {
            width: 100%;
            margin-top: 18px;
            page-break-inside: avoid;
        }
        .signature-box {
            width: 250px;
            text-align: center;
            font-size: 9.5px;
            float: right;
        }
        .signature-line {
            margin-top: 45px;
            border-top: 1px solid #17151C;
            font-weight: bold;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT PERUSAHAAN --}}
    <table class="header-table">
        <tr>
            <td style="vertical-align: middle;">
                <div class="company-title">PT IP NETWORK SOLUSINDO</div>
                <div class="company-subtitle">FIELD SYSTEM MANAGEMENT & IT INFRASTRUCTURE SERVICES</div>
            </td>
            <td style="text-align: right; vertical-align: middle;">
                <div class="report-badge">{{ $title }}</div>
                <div style="font-size: 8.5px; color: #75727C; margin-top: 4px;">Dicetak: {{ $generatedAt }}</div>
            </td>
        </tr>
    </table>

    {{-- METADATA DOKUMEN --}}
    <div class="meta-container">
        <table class="meta-table">
            <tr>
                <td style="width: 15%; font-weight: bold; color: #75727C;">Periode / Tanggal:</td>
                <td style="width: 45%; font-weight: bold; color: #17151C;">{{ $dateFormatted }}</td>
                <td style="width: 15%; font-weight: bold; color: #75727C;">Dicetak Oleh:</td>
                <td style="width: 25%; font-weight: bold; color: #17151C;">{{ $printedBy }}</td>
            </tr>
        </table>
    </div>

    {{-- SUMMARY KPI CARDS --}}
    @if($type === 'daily')
        <table class="summary-cards" style="border-spacing: 6px; border-collapse: separate;">
            <tr>
                <td style="width: 20%;" class="summary-box">
                    <div class="summary-title">Total Personel</div>
                    <div class="summary-value">{{ $summary['total'] }} Orang</div>
                </td>
                <td style="width: 20%;" class="summary-box">
                    <div class="summary-title" style="color: #059669;">Hadir (Sesuai Radius)</div>
                    <div class="summary-value" style="color: #059669;">{{ $summary['hadir'] }} Orang</div>
                </td>
                <td style="width: 20%;" class="summary-box">
                    <div class="summary-title" style="color: #D97706;">Luar Jangkauan</div>
                    <div class="summary-value" style="color: #D97706;">{{ $summary['luar_radius'] }} Orang</div>
                </td>
                <td style="width: 20%;" class="summary-box">
                    <div class="summary-title" style="color: #DC2626;">Belum Hadir</div>
                    <div class="summary-value" style="color: #DC2626;">{{ $summary['belum_hadir'] }} Orang</div>
                </td>
                <td style="width: 20%;" class="summary-box">
                    <div class="summary-title">Tingkat Kehadiran</div>
                    <div class="summary-value" style="color: #2563EB;">{{ $summary['persentase'] }}</div>
                </td>
            </tr>
        </table>
    @else
        <table class="summary-cards" style="border-spacing: 6px; border-collapse: separate;">
            <tr>
                <td style="width: 25%;" class="summary-box">
                    <div class="summary-title">Total Personel</div>
                    <div class="summary-value">{{ $summary['total_engineer'] }} Orang</div>
                </td>
                <td style="width: 25%;" class="summary-box">
                    <div class="summary-title">Hari Kerja Efektif</div>
                    <div class="summary-value">{{ $summary['workdays_count'] }}</div>
                </td>
                <td style="width: 25%;" class="summary-box">
                    <div class="summary-title">Total Kehadiran Tim</div>
                    <div class="summary-value" style="color: #059669;">{{ $summary['total_hadir_acc'] }}</div>
                </td>
                <td style="width: 25%;" class="summary-box">
                    <div class="summary-title">Rata-Rata Kehadiran</div>
                    <div class="summary-value" style="color: #2563EB;">{{ $summary['avg_rate'] }}</div>
                </td>
            </tr>
        </table>
    @endif

    {{-- TABEL DATA PRESENSI --}}
    <table class="data-table">
        <thead>
            @if($type === 'daily')
                <tr>
                    <th style="width: 28px;">No</th>
                    <th style="width: 140px; text-align: left;">Nama Engineer</th>
                    <th style="width: 100px;">Jabatan / Role</th>
                    <th style="width: 80px;">Divisi</th>
                    <th style="width: 100px;">Status Kehadiran</th>
                    <th style="width: 75px;">Clock-In</th>
                    <th style="width: 75px;">Clock-Out</th>
                    <th style="width: 65px;">Durasi</th>
                    <th style="width: 65px;">Jarak Kantor</th>
                    <th style="text-align: left;">Catatan / Keterangan</th>
                </tr>
            @else
                <tr>
                    <th style="width: 32px;">No</th>
                    <th style="width: 170px; text-align: left;">Nama Engineer</th>
                    <th style="width: 130px;">Jabatan / Role</th>
                    <th style="width: 100px;">Divisi</th>
                    <th style="width: 85px;">Total Hari Hadir</th>
                    <th style="width: 85px;">Sesuai Radius</th>
                    <th style="width: 85px;">Luar Radius</th>
                    <th style="width: 90px;">Total Jam Kerja</th>
                    <th style="width: 85px;">Persentase (%)</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @if($type === 'daily')
                @forelse($rows as $index => $row)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="font-weight: bold; color: #17151C;">{{ $row['engineer_name'] }}</td>
                        <td style="text-align: center; color: #475569;">{{ $row['role'] }}</td>
                        <td style="text-align: center; color: #475569;">{{ $row['division'] }}</td>
                        <td style="text-align: center;">
                            <span class="badge-status" style="color: {{ $row['status_color'] }}; background: {{ $row['status'] === 'Belum Hadir' ? '#F1F5F9' : ($row['status'] === 'Hadir (Sesuai Radius)' ? '#ECFDF5' : '#FEF3C7') }}; border: 1px solid {{ $row['status_color'] }};">
                                {{ $row['status'] }}
                            </span>
                        </td>
                        <td style="text-align: center; font-weight: bold;">{{ $row['clock_in'] }}</td>
                        <td style="text-align: center; font-weight: bold;">{{ $row['clock_out'] }}</td>
                        <td style="text-align: center; font-weight: bold; color: #2563EB;">{{ $row['duration'] }}</td>
                        <td style="text-align: center;">
                            <div>{{ $row['distance'] }}</div>
                            @if(!empty($row['address']) && $row['address'] !== '-')
                                <div style="font-size: 7.5px; color: #64748B; margin-top: 2px; line-height: 1.1;">{{ \Illuminate\Support\Str::limit($row['address'], 40) }}</div>
                            @endif
                        </td>
                        <td style="color: #475569; font-size: 8.5px;">{{ $row['note'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 15px; color: #75727C; font-style: italic;">
                            Tidak ada data presensi pada tanggal ini.
                        </td>
                    </tr>
                @endforelse
            @else
                @forelse($rows as $index => $row)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td style="font-weight: bold; color: #17151C;">{{ $row['engineer_name'] }}</td>
                        <td style="text-align: center; color: #475569;">{{ $row['role'] }}</td>
                        <td style="text-align: center; color: #475569;">{{ $row['division'] }}</td>
                        <td style="text-align: center; font-weight: bold; color: #059669;">{{ $row['hadir_days'] }} Hari</td>
                        <td style="text-align: center;">{{ $row['in_range_days'] }}</td>
                        <td style="text-align: center;">{{ $row['out_range_days'] }}</td>
                        <td style="text-align: center; font-weight: bold; color: #2563EB;">{{ $row['total_hours'] }}</td>
                        <td style="text-align: center; font-weight: bold; color: #17151C;">{{ $row['rate'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 15px; color: #75727C; font-style: italic;">
                            Tidak ada data presensi pada bulan ini.
                        </td>
                    </tr>
                @endforelse
            @endif
        </tbody>
    </table>

    {{-- BLOK TANDA TANGAN RESMI --}}
    <table class="footer-signature" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
        <tr>
            <td style="width: {{ $showMaker ? '45%' : '60%' }};"></td>
            @if($showMaker)
            <td style="width: 27%; text-align: center; vertical-align: top;">
                <div class="signature-box" style="margin: 0 auto;">
                    <div>Jakarta, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</div>
                    <div style="font-weight: bold; margin-top: 4px;">Dibuat Oleh,</div>
                    <div style="color: #75727C; font-size: 8.5px;">{{ $makerPosition }}</div>
                    <div class="signature-line" style="margin-top: 45px; font-weight: bold; border-bottom: 1px solid #17151C; padding-bottom: 2px;">
                        {{ $makerName }}
                    </div>
                </div>
            </td>
            @endif
            <td style="width: {{ $showMaker ? '28%' : '40%' }}; text-align: center; vertical-align: top;">
                <div class="signature-box" style="margin: 0 auto;">
                    <div>{{ $showMaker ? '' : 'Jakarta, ' . \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</div>
                    <div style="font-weight: bold; margin-top: 4px;">Mengetahui & Menyetujui,</div>
                    <div style="color: #75727C; font-size: 8.5px;">{{ $verifierPosition }}</div>
                    <div class="signature-line" style="margin-top: 45px; font-weight: bold; border-bottom: 1px solid #17151C; padding-bottom: 2px;">
                        {{ $verifierName }}
                    </div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
