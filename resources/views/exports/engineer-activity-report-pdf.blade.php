<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aktivitas Engineer - PT. IP Network Solusindo</title>
    <style>
        @page {
            margin: 20px 25px 25px 25px;
            size: A4 landscape;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9.5px;
            color: #17151C;
            line-height: 1.35;
            background: #ffffff;
        }

        /* ── HEADER / KOP SURAT RESMI ── */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #C81E2C;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header-logo {
            width: 46px;
            height: 46px;
        }

        .company-title {
            font-size: 15px;
            font-weight: bold;
            color: #C81E2C;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .company-subtitle {
            font-size: 8.5px;
            font-weight: bold;
            color: #4A5568;
            letter-spacing: 0.5px;
            margin-top: 2px;
        }

        .company-contact {
            font-size: 8px;
            color: #718096;
            margin-top: 1px;
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
            letter-spacing: 0.3px;
        }

        /* ── INFORMASI AGENDA / METADATA ── */
        .meta-container {
            width: 100%;
            margin-bottom: 10px;
            background: #F8F7F6;
            border: 1px solid #E7E5E3;
            border-radius: 5px;
            padding: 7px 10px;
        }

        .meta-table {
            width: 100%;
            font-size: 9px;
            border-collapse: collapse;
        }

        .meta-table td {
            padding: 2px 4px;
            vertical-align: middle;
        }

        /* ── KPI / SUMMARY CARDS ── */
        .summary-cards {
            width: 100%;
            margin-bottom: 10px;
        }

        .summary-box {
            border: 1px solid #E7E5E3;
            background: #FFFFFF;
            border-radius: 5px;
            padding: 5px 8px;
            text-align: center;
        }

        .summary-title {
            font-size: 8px;
            font-weight: bold;
            color: #75727C;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .summary-value {
            font-size: 13px;
            font-weight: bold;
            color: #17151C;
            margin-top: 1px;
        }

        /* ── TABEL UTAMA AKTIVITAS ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8.5px;
        }

        .data-table th {
            background-color: #1E293B;
            color: #FFFFFF;
            font-weight: bold;
            padding: 6px 5px;
            text-align: center;
            border: 1px solid #64748B;
            font-size: 8.5px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .data-table td {
            padding: 5px 6px;
            border: 1px solid #CBD5E1;
            vertical-align: top;
            color: #1E293B;
            line-height: 1.35;
        }

        .row-even {
            background-color: #F8FAFC;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .time-badge {
            display: inline-block;
            background: #EFF6FF;
            color: #1D4ED8;
            border: 1px solid #BFDBFE;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 8.5px;
            font-weight: bold;
            white-space: nowrap;
        }

        /* ── PENGESAHAN & TANDA TANGAN ── */
        .signature-table {
            width: 100%;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .signature-box {
            text-align: center;
            width: 220px;
        }

        .footer-note {
            font-size: 7.5px;
            color: #94A3B8;
            border-top: 1px dashed #CBD5E1;
            padding-top: 5px;
            line-height: 1.4;
            margin-top: 10px;
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

    {{-- ══ KOP SURAT RESMI PERUSAHAAN ══ --}}
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 52px; vertical-align: middle;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo" class="header-logo">
                @else
                    <div style="font-weight: bold; font-size: 16px; color: #C81E2C;">IPNET</div>
                @endif
            </td>
            <td style="vertical-align: middle; padding-left: 8px;">
                <div class="company-title">PT. IP NETWORK SOLUSINDO</div>
                <div class="company-subtitle">GOLDEN CENTRUM COMPLEX &bull; JL. MAJAPAHIT 26P JAKARTA 10160</div>
                <div class="company-contact">Telp: (021) 2965-5050 &bull; Email: info@ipnetsolusindo.co.id &bull; Website: www.ipnetsolusindo.co.id</div>
            </td>
            <td style="width: 250px; text-align: right; vertical-align: middle;">
                <span class="report-badge">LAPORAN AKTIVITAS ENGINEER</span>
                <div style="font-size: 8px; color: #75727C; margin-top: 3px;">
                    Dicetak: {{ now()->locale('id')->isoFormat('D MMMM Y, H:mm') }} WIB
                </div>
            </td>
        </tr>
    </table>

    {{-- ══ INFORMASI DOKUMEN / FILTER ══ --}}
    <div class="meta-container">
        <table class="meta-table">
            <tr>
                <td style="width: 14%; font-weight: bold; color: #75727C;">Proyek / Agenda</td>
                <td style="width: 36%; font-weight: bold; color: #17151C;">: {{ $projectName ?? 'Semua Proyek' }}</td>
                <td style="width: 14%; font-weight: bold; color: #75727C;">Tanggal Cetak</td>
                <td style="width: 36%; color: #17151C;">: {{ now()->locale('id')->isoFormat('dddd, D MMMM Y, H:mm') }} WIB</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #75727C;">Dicatat Oleh</td>
                <td style="color: #17151C;">: {{ $engineerName ?? '-' }}</td>
                <td style="font-weight: bold; color: #75727C;">Total Agenda</td>
                <td style="font-weight: bold; color: #C81E2C;">: {{ $totalCount }} Rangkaian Agenda Kegiatan</td>
            </tr>
        </table>
    </div>

    {{-- ══ RINGKASAN METRIK / SUMMARY CARDS ══ --}}
    <table class="summary-cards" style="width: 100%; border-collapse: separate; border-spacing: 6px 0;">
        <tr>
            <td class="summary-box" style="width: 25%;">
                <div class="summary-title">Total Aktivitas</div>
                <div class="summary-value" style="color: #C81E2C;">{{ $totalCount }} <span style="font-size: 9px; font-weight: normal; color: #75727C;">Agenda</span></div>
            </td>
            <td class="summary-box" style="width: 25%;">
                <div class="summary-title">Status Proyek</div>
                <div class="summary-value" style="color: #059669; font-size: 11px;">Aktif / On Going</div>
            </td>
            <td class="summary-box" style="width: 25%;">
                <div class="summary-title">Pelaksana Teknis</div>
                <div class="summary-value" style="color: #1E293B; font-size: 11px;">{{ $engineerName ?? '-' }}</div>
            </td>
            <td class="summary-box" style="width: 25%;">
                <div class="summary-title">Klasifikasi Dokumen</div>
                <div class="summary-value" style="color: #2563EB; font-size: 11px;">Dokumen Resmi Operasional</div>
            </td>
        </tr>
    </table>

    {{-- ══ TABEL DATA AKTIVITAS (NO, AKTIVITAS, TANGGAL, WAKTU, PIC KLIEN, PIC IPNET, NOTED) ══ --}}
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 31%; text-align: left;">Aktivitas</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 9%;">Waktu</th>
                <th style="width: 13%; text-align: left;">PIC Klien</th>
                <th style="width: 14%; text-align: left;">PIC IPNET</th>
                <th style="width: 19%; text-align: left;">Noted</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                @php
                    $isArr = is_array($item);
                    $no    = $isArr ? ($item['no'] ?? ($index + 1)) : ($item->no ?? ($index + 1));
                    $act   = $isArr ? ($item['activity'] ?? '') : ($item->activity ?? ($item->description ?? ''));
                    $dt    = $isArr ? ($item['date'] ?? '') : ($item->date ?? '');
                    $tm    = $isArr ? ($item['time'] ?? '') : ($item->time ?? '');
                    $cpic  = $isArr ? ($item['client_pic'] ?? '') : ($item->client_pic ?? '');
                    $ipic  = $isArr ? ($item['ipnet_pic'] ?? '') : ($item->ipnet_pic ?? '');
                    $nt    = $isArr ? ($item['notes'] ?? '') : ($item->notes ?? '');
                @endphp
                <tr class="{{ $index % 2 === 1 ? 'row-even' : '' }}">
                    <td class="text-center" style="font-weight: bold; color: #475569;">{{ $no }}</td>
                    <td class="text-left" style="font-weight: 600; color: #0F172A;">{{ $act ?: '-' }}</td>
                    <td class="text-center" style="white-space: nowrap;">{{ $dt ?: '-' }}</td>
                    <td class="text-center">
                        @if($tm && $tm !== '-')
                            <span class="time-badge">{{ $tm }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-left" style="color: #334155;">{{ (!empty($cpic) && $cpic !== '-') ? $cpic : '-' }}</td>
                    <td class="text-left" style="color: #334155; font-weight: 500;">{{ (!empty($ipic) && $ipic !== '-') ? $ipic : '-' }}</td>
                    <td class="text-left" style="color: #475569;">{{ (!empty($nt) && $nt !== '-') ? $nt : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 18px; color: #75727C; font-style: italic;">
                        Tidak ada data catatan aktivitas yang tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ══ PENGESAHAN & TANDA TANGAN ══ --}}
    <table class="signature-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 60%; vertical-align: top; padding-right: 25px;">
                <div style="font-size: 8px; font-weight: bold; color: #475569; text-transform: uppercase; margin-bottom: 2px;">
                    Catatan Dokumen & Bukti Pelaksanaan:
                </div>
                <div style="font-size: 8px; color: #64748B; line-height: 1.45;">
                    1. Dokumen ini merupakan bukti catatan kronologis resmi kegiatan teknis operasional lapangan PT. IP Network Solusindo.<br>
                    2. Digenerate secara otomatis oleh Sistem Manajemen Operasional IPNET pada {{ now()->format('d/m/Y H:i') }} WIB.<br>
                    3. Seluruh rincian aktivitas dan koordinasi lapangan telah terdaftar dalam sistem.
                </div>
            </td>
            <td class="signature-box" style="vertical-align: top;">
                <div style="font-size: 8.5px; color: #374151; margin-bottom: 2px;">
                    Jakarta, {{ now()->locale('id')->isoFormat('D MMMM Y') }}
                </div>
                <div style="font-size: 8.5px; font-weight: bold; color: #1E293B; margin-bottom: 45px;">
                    Lead Engineer / Penanggung Jawab
                </div>
                <div style="font-size: 9px; font-weight: bold; color: #1E293B; text-decoration: underline;">
                    ( {{ $engineerName ?? '........................................' }} )
                </div>
                <div style="font-size: 8px; color: #64748B; margin-top: 2px;">
                    PT. IP Network Solusindo
                </div>
            </td>
        </tr>
    </table>

    {{-- ══ FOOTER NOTE ══ --}}
    <div class="footer-note">
        PT. IP Network Solusindo &bull; Golden Centrum Complex, Jl. Majapahit 26P Jakarta 10160 &bull; Halaman Resmi Laporan Aktivitas Lapangan &bull; Dicetak Otomatis
    </div>

</body>
</html>
