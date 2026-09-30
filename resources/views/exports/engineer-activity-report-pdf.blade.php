<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aktivitas Engineer - IP Network Solusindo</title>
    <style>
        @page {
            margin: 20px 25px 25px 25px;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 10px;
            color: #1E293B;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #C81E2C;
            margin-bottom: 16px;
        }
        .header-cell {
            padding-bottom: 14px;
            vertical-align: middle;
        }
        .company-title {
            font-size: 18px;
            font-weight: bold;
            color: #C81E2C;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .company-subtitle {
            font-size: 9.5px;
            font-weight: bold;
            color: #64748B;
            letter-spacing: 0.8px;
            margin-top: 3px;
        }
        .report-badge {
            display: inline-block;
            background: #FDF1F2;
            color: #C81E2C;
            border: 1px solid #FADADF;
            padding: 5px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 10px;
        }
        .meta-container {
            width: 100%;
            margin-bottom: 14px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .meta-table {
            width: 100%;
            font-size: 10px;
        }
        .meta-table td {
            padding: 2.5px 4px;
        }
        .summary-cards {
            width: 100%;
            margin-bottom: 14px;
        }
        .summary-box {
            border: 1px solid #E2E8F0;
            background: #FFFFFF;
            border-radius: 6px;
            padding: 7px 10px;
            text-align: center;
        }
        .summary-title {
            font-size: 9px;
            font-weight: bold;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-value {
            font-size: 15px;
            font-weight: bold;
            color: #0F172A;
            margin-top: 3px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 10px;
            table-layout: fixed;
        }
        .data-table th {
            background-color: #1E293B;
            color: #FFFFFF;
            font-weight: bold;
            font-size: 10px;
            padding: 8px 6px;
            border: 1px solid #475569;
        }
        .data-table td {
            padding: 7px 6px;
            border: 1px solid #CBD5E1;
            vertical-align: middle;
            font-size: 10px;
            font-weight: normal;
            color: #1E293B;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: break-word;
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
        .total-row td {
            background-color: #F1F5F9;
            font-weight: bold;
            font-size: 10px;
            border-top: 2px solid #64748B;
            padding: 7px 6px;
        }
        .signature-table {
            width: 100%;
            margin-top: 22px;
            page-break-inside: avoid;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
    </style>
</head>
<body>
@php
    $items = [];
    if (!empty($parsedActivities) && (is_array($parsedActivities) || $parsedActivities instanceof \Countable || is_iterable($parsedActivities))) {
        $items = $parsedActivities;
    } elseif (!empty($activities) && (is_array($activities) || $activities instanceof \Countable || is_iterable($activities))) {
        $items = collect($activities)->map(function($a, $idx) {
            $rawNotes  = $a->notes ?? '';
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

            if (!$ipnetPic) {
                $ipnetPic = $a->engineer->name ?? '-';
            }

            $description = $a->description ?? '-';
            if (preg_match('/^\[(.+?)\]\s*(.*)$/s', $description, $m)) {
                $description = $m[2] ?: $m[1];
            }

            return [
                'no'         => $idx + 1,
                'activity'   => $description,
                'date'       => $a->activity_date ? (\Carbon\Carbon::parse($a->activity_date)->format('d/m/Y')) : '-',
                'time'       => $a->start_time ? (\Carbon\Carbon::parse($a->start_time)->format('H:i')) : '-',
                'client_pic' => $clientPic ?: '-',
                'ipnet_pic'  => $ipnetPic,
                'notes'      => $notedOnly ?: '-',
            ];
        });
    }

    $totalCount = is_countable($items) ? count($items) : 0;

    if (empty($logoBase64)) {
        $logoPath = public_path('images/ipnet1.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('images/ipnet.png');
        }
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }
    }
@endphp

    <!-- Header dengan Logo di Sebelah Kiri -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="header-cell" style="width: 70%;">
                <table cellpadding="0" cellspacing="0" style="border: none; margin: 0; padding: 0;">
                    <tr>
                        @if(!empty($logoBase64))
                        <td style="width: 44px; vertical-align: middle; padding-right: 12px; border: none; padding-bottom: 0;">
                            <img src="{{ $logoBase64 }}" alt="Logo" style="height: 38px; width: auto; max-width: 44px; display: block;">
                        </td>
                        @endif
                        <td style="vertical-align: middle; border: none; text-align: left; padding-bottom: 0;">
                            <div class="company-title">PT IP NETWORK SOLUSINDO</div>
                            <div class="company-subtitle">FIELD SYSTEM MANAGEMENT - LEMBAR KERJA / CATATAN AKTIVITAS</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="header-cell" style="width: 30%; text-align: right;">
                <span class="report-badge">DOKUMEN RESMI REKAP KERJA</span>
            </td>
        </tr>
    </table>

    <!-- Metadata Filter -->
    <div class="meta-container">
        <table class="meta-table">
            <tr>
                <td style="width: 15%; font-weight: bold; color: #75727C;">Filter Engineer</td>
                <td style="width: 35%;">: {{ $engineerName ?? 'Semua Engineer' }}</td>
                <td style="width: 15%; font-weight: bold; color: #75727C;">Periode</td>
                <td style="width: 35%;">: {{ now()->locale('id')->isoFormat('D MMMM Y') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #75727C;">Filter Project</td>
                <td>: {{ $projectName ?? 'Semua Project' }}</td>
                <td style="font-weight: bold; color: #75727C;">Tanggal Cetak</td>
                <td>: {{ now()->locale('id')->isoFormat('D MMMM Y, H:mm') }} WIB</td>
            </tr>
        </table>
    </div>

    <!-- Summary Box -->
    <table class="summary-cards" style="width: 100%; border-spacing: 6px 0; margin-left: -6px; margin-right: -6px;">
        <tr>
            <td style="width: 25%;">
                <div class="summary-box">
                    <div class="summary-title">TOTAL AKTIVITAS</div>
                    <div class="summary-value" style="color: #C81E2C;">{{ $totalCount }} Agenda</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="summary-box">
                    <div class="summary-title">TOTAL HARI KERJA</div>
                    <div class="summary-value">1 Hari</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="summary-box">
                    <div class="summary-title">TOTAL ENTRI LOG</div>
                    <div class="summary-value">{{ $totalCount }} Entri</div>
                </div>
            </td>
            <td style="width: 25%;">
                <div class="summary-box">
                    <div class="summary-title">JUMLAH ENGINEER</div>
                    <div class="summary-value">1 Orang</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 30px; text-align: center;">No</th>
                <th style="width: 75px; text-align: center;">Tanggal</th>
                <th style="width: 38%; text-align: left; padding-left: 8px;">Uraian Aktivitas</th>
                <th style="width: 95px; text-align: center;">PIC Klien</th>
                <th style="width: 95px; text-align: center;">PIC IPNET</th>
                <th style="width: 32%; text-align: left; padding-left: 8px;">Noted</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                @php
                    $isArr = is_array($item);
                    $no    = $isArr ? ($item['no'] ?? ($index + 1)) : ($item->no ?? ($index + 1));
                    $act   = $isArr ? ($item['activity'] ?? '') : ($item->activity ?? ($item->description ?? ''));
                    $dt    = $isArr ? ($item['date'] ?? '') : ($item->date ?? '');
                    $cpic  = $isArr ? ($item['client_pic'] ?? '') : ($item->client_pic ?? '');
                    $ipic  = $isArr ? ($item['ipnet_pic'] ?? '') : ($item->ipnet_pic ?? '');
                    $nt    = $isArr ? ($item['notes'] ?? '') : ($item->notes ?? '');
                @endphp
                <tr class="{{ $index % 2 === 1 ? 'row-even' : '' }}">
                    <td class="text-center">{{ $no }}</td>
                    <td class="text-center">{{ $dt ?: '-' }}</td>
                    <td style="text-align: left; padding-left: 8px;">{{ $act ?: '-' }}</td>
                    <td class="text-center">{{ (!empty($cpic) && $cpic !== '-') ? $cpic : '-' }}</td>
                    <td class="text-center">{{ (!empty($ipic) && $ipic !== '-') ? $ipic : '-' }}</td>
                    <td style="text-align: left; padding-left: 8px;">{{ (!empty($nt) && $nt !== '-') ? $nt : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 18px; color: #64748B; font-style: italic;">
                        Tidak ada catatan aktivitas untuk periode filter ini.
                    </td>
                </tr>
            @endforelse

            @if(!empty($items) && count($items) > 0)
                <tr class="total-row">
                    <td colspan="2" class="text-right">TOTAL AGENDA AKTIVITAS :</td>
                    <td class="text-center" style="color: #C81E2C;">{{ $totalCount }} Agenda</td>
                    <td colspan="3" class="text-center" style="color: #475569;">Total {{ $totalCount }} aktivitas pengerjaan</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Signature Block -->
    <table class="signature-table" style="width: 100%; margin-top: 25px; border-collapse: collapse;">
        <tr>
            <td style="width: 44%; vertical-align: top;">
                <div style="font-size: 9px; color: #64748B; line-height: 1.45;">
                    * Dokumen rekapitulasi aktivitas ini digenerate secara otomatis melalui sistem Field System Management IP-Net.<br>
                    * Informasi ini digunakan sebagai acuan monitoring produktivitas dan pertanggungjawaban pengerjaan proyek.
                </div>
            </td>
            <td style="width: 28%; text-align: center; vertical-align: top;">
                <div style="font-size: 10px; color: #475569; margin-bottom: 45px;">
                    Jakarta, {{ now()->locale('id')->isoFormat('D MMMM Y') }}<br>
                    <strong>Dibuat Oleh,</strong>
                </div>
                <div style="font-size: 10px; font-weight: bold; border-bottom: 1.5px solid #1E293B; padding-bottom: 2px; color: #0F172A;">
                    {{ $engineerName ?? 'Nugraha Pratama' }}
                </div>
                <div style="font-size: 9px; color: #64748B; margin-top: 3px;">Network Leader</div>
            </td>
            <td style="width: 28%; text-align: center; vertical-align: top;">
                <div style="font-size: 10px; color: #475569; margin-bottom: 45px;">
                    <br>
                    <strong>Mengetahui & Menyetujui,</strong>
                </div>
                <div style="font-size: 10px; font-weight: bold; border-bottom: 1.5px solid #1E293B; padding-bottom: 2px; color: #0F172A;">
                    Susanto Djaya
                </div>
                <div style="font-size: 9px; color: #64748B; margin-top: 3px;">Group Leader</div>
            </td>
        </tr>
    </table>

</body>
</html>
