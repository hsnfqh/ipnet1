<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aktivitas Engineer - PT. IP Network Solusindo</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 18mm 18mm 16mm 18mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #1a1a1a;
            background: #ffffff;
            line-height: 1.4;
        }

        /* ── KOP SURAT RESMI PERUSAHAAN ── */
        .kop-table {
            width: 100%;
            border-bottom: 2px solid #8F0A0D;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .kop-logo {
            width: 60px;
            height: auto;
            max-height: 55px;
        }

        .company-name {
            font-size: 14pt;
            font-weight: bold;
            color: #8F0A0D;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .company-address {
            font-size: 8.5pt;
            color: #4b5563;
            line-height: 1.35;
        }

        .doc-title-block {
            text-align: right;
            vertical-align: middle;
        }

        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            color: #111827;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-sub {
            font-size: 8pt;
            color: #6b7280;
            margin-top: 3px;
        }

        /* ── INFORMASI AGENDA / METADATA DOKUMEN ── */
        .meta-box {
            width: 100%;
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            margin-bottom: 14px;
            border-collapse: collapse;
        }

        .meta-box td {
            padding: 6px 10px;
            font-size: 9pt;
            vertical-align: middle;
        }

        .meta-label {
            font-weight: bold;
            color: #4b5563;
            width: 15%;
        }

        .meta-separator {
            width: 2%;
            text-align: center;
            color: #6b7280;
        }

        .meta-value {
            color: #111827;
            width: 33%;
        }

        .meta-value-bold {
            font-weight: bold;
            color: #111827;
        }

        /* ── TABEL UTAMA AKTIVITAS ── */
        .activity-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 18px;
        }

        .activity-table thead th {
            background-color: #8F0A0D;
            color: #ffffff;
            font-weight: bold;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 8px 6px;
            border: 1px solid #73080A;
            vertical-align: middle;
        }

        .activity-table tbody td {
            padding: 7px 8px;
            border: 1px solid #d1d5db;
            vertical-align: top;
            color: #1f2937;
            line-height: 1.35;
        }

        .activity-table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .col-no {
            width: 4%;
            text-align: center;
            font-weight: bold;
            color: #374151;
        }

        .col-activity {
            width: 30%;
        }

        .col-date {
            width: 11%;
            text-align: center;
            white-space: nowrap;
        }

        .col-time {
            width: 10%;
            text-align: center;
            white-space: nowrap;
        }

        .col-pic-client {
            width: 13%;
        }

        .col-pic-ipnet {
            width: 13%;
        }

        .col-notes {
            width: 19%;
        }

        /* ── PENGESAHAN / TANDA TANGAN ── */
        .sign-table {
            width: 100%;
            page-break-inside: avoid;
            margin-top: 10px;
        }

        .sign-notes {
            vertical-align: top;
            font-size: 8pt;
            color: #6b7280;
            line-height: 1.45;
            padding-right: 30px;
        }

        .sign-notes-title {
            font-weight: bold;
            color: #374151;
            margin-bottom: 2px;
            text-transform: uppercase;
            font-size: 7.5pt;
            letter-spacing: 0.3px;
        }

        .sign-box {
            width: 240px;
            text-align: center;
            vertical-align: top;
        }

        .sign-place-date {
            font-size: 8.5pt;
            color: #374151;
            margin-bottom: 4px;
        }

        .sign-role-title {
            font-size: 8.5pt;
            font-weight: bold;
            color: #111827;
            margin-bottom: 50px;
        }

        .sign-name-line {
            font-size: 9pt;
            font-weight: bold;
            color: #111827;
            border-top: 1px solid #111827;
            padding-top: 4px;
            display: inline-block;
            min-width: 180px;
        }

        .sign-company {
            font-size: 8pt;
            color: #6b7280;
            margin-top: 2px;
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
@endphp

    {{-- ══ KOP SURAT RESMI ══ --}}
    <table class="kop-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 70px; vertical-align: middle;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo" class="kop-logo">
                @else
                    <div style="font-weight: bold; font-size: 16pt; color: #8F0A0D;">IPNET</div>
                @endif
            </td>
            <td style="padding-left: 8px; vertical-align: middle;">
                <div class="company-name">PT. IP Network Solusindo</div>
                <div class="company-address">
                    Golden Centrum Complex<br>
                    Jl. Majapahit 26P Jakarta 10160
                </div>
            </td>
            <td class="doc-title-block">
                <div class="doc-title">Laporan Aktivitas Engineer</div>
                <div class="doc-sub">Dokumentasi Catatan Kronologis Kegiatan Operasional</div>
            </td>
        </tr>
    </table>

    {{-- ══ INFORMASI DOKUMEN / AGENDA ══ --}}
    <table class="meta-box" cellpadding="0" cellspacing="0">
        <tr>
            <td class="meta-label">Proyek / Agenda</td>
            <td class="meta-separator">:</td>
            <td class="meta-value meta-value-bold">{{ $projectName ?? 'Semua Proyek' }}</td>

            <td class="meta-label">Tanggal Cetak</td>
            <td class="meta-separator">:</td>
            <td class="meta-value">{{ now()->locale('id')->isoFormat('D MMMM Y, H:mm') }} WIB</td>
        </tr>
        <tr>
            <td class="meta-label">Dicatat Oleh</td>
            <td class="meta-separator">:</td>
            <td class="meta-value">{{ $engineerName ?? '-' }}</td>

            <td class="meta-label">Total Agenda</td>
            <td class="meta-separator">:</td>
            <td class="meta-value meta-value-bold">{{ $totalCount }} Rangkaian Agenda</td>
        </tr>
    </table>

    {{-- ══ TABEL DATA AKTIVITAS ══ --}}
    <table class="activity-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-activity text-left">Aktivitas</th>
                <th class="col-date">Tanggal</th>
                <th class="col-time">Waktu (Jam)</th>
                <th class="col-pic-client text-left">PIC Klien</th>
                <th class="col-pic-ipnet text-left">PIC IPNET</th>
                <th class="col-notes text-left">Noted</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                @php
                    $isArr = is_array($item);
                    $no    = $isArr ? ($item['no'] ?? '') : ($item->no ?? '');
                    $act   = $isArr ? ($item['activity'] ?? '') : ($item->activity ?? ($item->description ?? ''));
                    $dt    = $isArr ? ($item['date'] ?? '') : ($item->date ?? '');
                    $tm    = $isArr ? ($item['time'] ?? '') : ($item->time ?? '');
                    $cpic  = $isArr ? ($item['client_pic'] ?? '') : ($item->client_pic ?? '');
                    $ipic  = $isArr ? ($item['ipnet_pic'] ?? '') : ($item->ipnet_pic ?? '');
                    $nt    = $isArr ? ($item['notes'] ?? '') : ($item->notes ?? '');
                @endphp
                <tr>
                    <td class="col-no">{{ $no ?: '-' }}</td>
                    <td class="col-activity" style="font-weight: 500;">{{ $act ?: '-' }}</td>
                    <td class="col-date">{{ $dt ?: '-' }}</td>
                    <td class="col-time">{{ $tm ?: '-' }}</td>
                    <td class="col-pic-client">{{ (!empty($cpic) && $cpic !== '-') ? $cpic : '-' }}</td>
                    <td class="col-pic-ipnet">{{ (!empty($ipic) && $ipic !== '-') ? $ipic : '-' }}</td>
                    <td class="col-notes">{{ (!empty($nt) && $nt !== '-') ? $nt : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px; color: #6b7280; font-style: italic;">
                        Tidak ada data catatan aktivitas yang tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ══ LEMBAR PENGESAHAN & CATATAN ══ --}}
    <table class="sign-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="sign-notes">
                <div class="sign-notes-title">Catatan Dokumen:</div>
                1. Laporan ini merupakan catatan resmi kronologis aktivitas teknis lapangan PT. IP Network Solusindo.<br>
                2. Diterbitkan secara otomatis melalui Sistem Operasional IPNET pada {{ now()->format('d/m/Y H:i') }} WIB.<br>
                3. Dokumen ini sah dan mengikat sebagai bukti pelaksanaan pekerjaan dan koordinasi teknis di site/lapangan.
            </td>
            <td class="sign-box">
                <div class="sign-place-date">Jakarta, {{ now()->locale('id')->isoFormat('D MMMM Y') }}</div>
                <div class="sign-role-title">Lead Engineer / Penanggung Jawab</div>
                <div class="sign-name-line">( {{ $engineerName ?? '........................................' }} )</div>
                <div class="sign-company">PT. IP Network Solusindo</div>
            </td>
        </tr>
    </table>

</body>
</html>
