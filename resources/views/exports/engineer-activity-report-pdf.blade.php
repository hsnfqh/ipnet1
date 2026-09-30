<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aktivitas Engineer – PT IP Network Solusindo</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 10mm 12mm 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #ffffff;
            line-height: 1.35;
        }

        /* ── HEADER / KOP SURAT (Table Based for DomPDF) ── */
        .kop-table {
            width: 100%;
            border-bottom: 2.5px solid #8F0A0D;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .kop-logo {
            width: 52px;
            height: 52px;
            object-fit: contain;
        }

        .company-title {
            font-size: 15px;
            font-weight: bold;
            color: #8F0A0D;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0 0 2px 0;
        }

        .company-sub {
            font-size: 8.5px;
            color: #64748b;
            line-height: 1.3;
        }

        .doc-badge {
            display: inline-block;
            background: #fef2f2;
            color: #8F0A0D;
            border: 1px solid #fecaca;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .doc-title {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 4px;
            text-transform: uppercase;
        }

        .doc-meta {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 2px;
        }

        /* ── META INFO BOX ── */
        .meta-table {
            width: 100%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            margin-bottom: 12px;
            border-collapse: separate;
        }

        .meta-table td {
            padding: 6px 12px;
            font-size: 9.5px;
            vertical-align: middle;
        }

        .meta-label {
            color: #64748b;
            font-size: 8.5px;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.4px;
        }

        .meta-value {
            color: #0f172a;
            font-weight: bold;
            font-size: 10.5px;
        }

        /* ── TABEL UTAMA — persis form: NO, AKTIVITAS, TANGGAL, WAKTU, PIC KLIEN, PIC IPNET, NOTED ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-bottom: 14px;
        }

        .data-table thead tr {
            background-color: #8F0A0D;
            color: #ffffff;
        }

        .data-table th {
            padding: 7px 6px;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: 1px solid #73080A;
            vertical-align: middle;
        }

        .data-table tbody td {
            padding: 7px 6px;
            border: 1px solid #cbd5e1;
            vertical-align: top;
            color: #1e293b;
            line-height: 1.35;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .data-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .badge-pic-klien {
            display: inline-block;
            background: #f1f5f9;
            color: #334155;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: 600;
            font-size: 9px;
            border: 1px solid #e2e8f0;
        }

        .badge-pic-ipnet {
            display: inline-block;
            background: #fef2f2;
            color: #8F0A0D;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 9px;
            border: 1px solid #fecaca;
        }

        /* ── TANDA TANGAN & FOOTER ── */
        .footer-table {
            width: 100%;
            margin-top: 14px;
            border-top: 1.5px solid #8F0A0D;
            padding-top: 10px;
        }

        .footer-note {
            font-size: 8px;
            color: #64748b;
            line-height: 1.4;
        }

        .sig-box {
            text-align: center;
            width: 200px;
        }

        .sig-title {
            font-size: 9px;
            color: #475569;
            margin-bottom: 45px;
        }

        .sig-line {
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
            border-top: 1px solid #334155;
            padding-top: 3px;
        }

        .sig-role {
            font-size: 8px;
            color: #64748b;
        }
    </style>
</head>
<body>
@php
    $items = [];
    if (!empty($parsedActivities) && (is_array($parsedActivities) || $parsedActivities instanceof \Countable || is_iterable($parsedActivities))) {
        $items = $parsedActivities;
    } elseif (!empty($activities) && (is_array($activities) || $activities instanceof \Countable || is_iterable($activities))) {
        // Fallback jika controller lama yang memanggil dengan $activities
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

    $totalCount = is_countable($items) ? count($items) : (is_array($items) ? count($items) : 0);
@endphp

    {{-- ══ KOP SURAT ══ --}}
    <table class="kop-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 60px; vertical-align: middle;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo" class="kop-logo">
                @else
                    <div style="font-weight:bold; font-size:16px; color:#8F0A0D;">IPNET</div>
                @endif
            </td>
            <td style="padding-left: 10px; vertical-align: middle;">
                <div class="company-title">PT IP Network Solusindo</div>
                <div class="company-sub">
                    Jl. Rawa Buntu No.2, Kec. Serpong, Tangerang Selatan, Banten 15310<br>
                    Telp: (021) 2965-5050 &bull; Website: www.ipnetsolusindo.co.id
                </div>
            </td>
            <td style="text-align: right; vertical-align: middle; width: 260px;">
                <div class="doc-badge">Dokumen Resmi Aktivitas</div>
                <div class="doc-title">Laporan Aktivitas</div>
                <div class="doc-meta">
                    Dicetak: {{ now()->locale('id')->isoFormat('D MMMM Y, H:mm') }} WIB &bull; Oleh: <strong>{{ $printedBy ?? 'Admin' }}</strong>
                </div>
            </td>
        </tr>
    </table>

    {{-- ══ META INFO BAR ══ --}}
    <table class="meta-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 45%;">
                <div class="meta-label">Proyek / Agenda:</div>
                <div class="meta-value">{{ $projectName ?? 'Semua Proyek' }}</div>
            </td>
            <td style="width: 30%;">
                <div class="meta-label">Dicatat Oleh:</div>
                <div class="meta-value">{{ $engineerName ?? '-' }}</div>
            </td>
            <td style="width: 25%; text-align: right;">
                <div class="meta-label">Total Agenda Aktivitas:</div>
                <div class="meta-value" style="color: #8F0A0D;">{{ $totalCount }} Rangkaian Agenda</div>
            </td>
        </tr>
    </table>

    {{-- ══ TABEL DATA AKTIVITAS (Kolom persis form: NO, AKTIVITAS, TANGGAL, WAKTU, PIC KLIEN, PIC IPNET, NOTED) ══ --}}
    <table class="data-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 4%; text-align: center;">No</th>
                <th style="width: 30%; text-align: left;">Aktivitas</th>
                <th style="width: 11%; text-align: center;">Tanggal</th>
                <th style="width: 10%; text-align: center;">Waktu (Jam)</th>
                <th style="width: 13%; text-align: left;">PIC Klien</th>
                <th style="width: 13%; text-align: left;">PIC IPNET</th>
                <th style="width: 19%; text-align: left;">Noted</th>
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
                    {{-- No --}}
                    <td style="text-align: center; font-weight: bold; color: #8F0A0D;">
                        {{ $no ?: '-' }}
                    </td>

                    {{-- Aktivitas --}}
                    <td style="font-weight: 600; color: #0f172a;">
                        {{ $act ?: '-' }}
                    </td>

                    {{-- Tanggal --}}
                    <td style="text-align: center; white-space: nowrap;">
                        {{ $dt ?: '-' }}
                    </td>

                    {{-- Waktu (Jam) --}}
                    <td style="text-align: center; font-weight: 600; white-space: nowrap;">
                        {{ $tm ?: '-' }}
                    </td>

                    {{-- PIC Klien --}}
                    <td>
                        @if(!empty($cpic) && $cpic !== '-')
                            <span class="badge-pic-klien">{{ $cpic }}</span>
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>

                    {{-- PIC IPNET --}}
                    <td>
                        @if(!empty($ipic) && $ipic !== '-')
                            <span class="badge-pic-ipnet">{{ $ipic }}</span>
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>

                    {{-- Noted --}}
                    <td style="color: #334155; word-wrap: break-word;">
                        {{ $nt ?: '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 25px; color: #94a3b8; font-style: italic;">
                        Tidak ada catatan aktivitas yang tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ══ FOOTER & TANDA TANGAN ══ --}}
    <table class="footer-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="vertical-align: top;" class="footer-note">
                <strong>PT IP NETWORK SOLUSINDO</strong> &bull; Laporan Resmi Aktivitas Engineer<br>
                Dokumen ini digenerate secara otomatis oleh sistem IPNET Dashboard pada {{ now()->format('d/m/Y H:i') }} WIB.<br>
                Halaman berlaku sebagai bukti sah dokumentasi kegiatan lapangan dan operasional.
            </td>
            <td style="width: 220px; text-align: center; vertical-align: top;">
                <div class="sig-title">Mengetahui,</div>
                <div class="sig-line">Lead Engineer / Team Leader</div>
                <div class="sig-role">PT IP Network Solusindo</div>
            </td>
        </tr>
    </table>

</body>
</html>
