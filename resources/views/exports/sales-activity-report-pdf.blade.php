<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aktivitas &amp; Kronologis Sales - PT IP Network Solusindo</title>
    <style>
        @page {
            margin: 10mm 12mm 10mm 12mm;
            size: a4 portrait;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 8pt;
            color: #0F172A;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }
        .page-container {
            width: 100%;
            position: relative;
        }

        /* ── Header Dokumen ── */
        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #8F0A0D;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .doc-header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .doc-title {
            font-size: 13pt;
            font-weight: bold;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .doc-subtitle {
            font-size: 8pt;
            color: #64748B;
            margin-top: 3px;
            margin-bottom: 0;
        }
        .badge-official {
            display: inline-block;
            background-color: #FEF2F2;
            color: #8F0A0D;
            border: 1px solid #FECACA;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: bold;
            padding: 4px 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Section Titles ── */
        .section-title {
            font-size: 8.5pt;
            font-weight: bold;
            color: #0F172A;
            text-transform: uppercase;
            margin-top: 10px;
            margin-bottom: 5px;
            letter-spacing: 0.3px;
        }

        /* ── Tabel Identitas ── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border: 1px solid #1E293B;
        }
        .info-table td {
            font-size: 7.5pt;
            padding: 4px 7px;
            border: 1px solid #1E293B;
            vertical-align: middle;
        }
        .info-label {
            background-color: #F8FAFC;
            font-weight: bold;
            color: #1E293B;
            width: 18%;
        }
        .info-value {
            color: #0F172A;
            width: 32%;
        }

        /* ── Tabel Rincian Kronologis ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border: 1px solid #1E293B;
        }
        .data-table th {
            background-color: #EBF3FB;
            color: #0F172A;
            font-weight: bold;
            font-size: 7.5pt;
            padding: 5px 6px;
            border: 1px solid #1E293B;
            text-align: left;
            text-transform: uppercase;
        }
        .data-table th.center, .data-table td.center {
            text-align: center;
        }
        .data-table td {
            font-size: 7.5pt;
            padding: 5px 6px;
            border: 1px solid #1E293B;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) {
            background-color: #FAFAFA;
        }

        /* ── Callout Box ── */
        .action-box {
            background-color: #FFFBEB;
            border: 1px solid #FCD34D;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 12px;
        }
        .action-box-title {
            font-size: 8pt;
            font-weight: bold;
            color: #92400E;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        /* ── Footer Verifikasi ── */
        .verification-box {
            background-color: #F8FAFC;
            border: 1px solid #CBD5E1;
            border-radius: 4px;
            padding: 8px 10px;
            margin-top: 15px;
            font-size: 7pt;
            color: #475569;
            line-height: 1.4;
        }
        .verification-box strong {
            color: #0F172A;
        }
    </style>
</head>
<body>
<div class="page-container">

    {{-- Header Dokumen --}}
    <table class="doc-header-table">
        <tr>
            <td style="width: 20%; vertical-align: top;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo IPNET" style="max-height: 42px; max-width: 140px;">
                @else
                    <span style="font-size: 14pt; font-weight: bold; color: #8F0A0D;">IP-NET</span>
                @endif
            </td>
            <td style="width: 58%; vertical-align: middle; padding-left: 10px;">
                <h1 class="doc-title">PT IP NETWORK SOLUSINDO</h1>
                <p class="doc-subtitle">FORM LAPORAN AKTIVITAS &amp; KRONOLOGIS SALES (CRM REPORT)</p>
            </td>
            <td style="width: 22%; text-align: right; vertical-align: middle;">
                <div class="badge-official">Dokumen Resmi Sales</div>
                <div style="font-size: 6.8pt; color: #64748B; margin-top: 4px;">
                    No: SLS-ACT/{{ date('Y') }}/{{ str_pad($activity->id, 4, '0', STR_PAD_LEFT) }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Section A: Identitas Prospek & Klien --}}
    <div class="section-title">A. IDENTITAS PROSPEK &amp; PERTEMUAN</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Prospek / Proyek</td>
            <td class="info-value" style="font-weight: bold; color: #8F0A0D;">{{ $activity->project->name ?? '-' }}</td>
            <td class="info-label">Tanggal Pelaksanaan</td>
            <td class="info-value">{{ \Carbon\Carbon::parse($activity->activity_date)->isoFormat('dddd, D MMMM Y') }}</td>
        </tr>
        <tr>
            <td class="info-label">Nama Klien / Customer</td>
            <td class="info-value">{{ $activity->project->client ?? '-' }}</td>
            <td class="info-label">Waktu Pelaksanaan</td>
            <td class="info-value">{{ \Carbon\Carbon::parse($activity->activity_date)->format('H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="info-label">Sales Person (AE)</td>
            <td class="info-value">{{ $activity->sales->name ?? 'Sales Team' }}</td>
            <td class="info-label">Tipe Interaksi / Aktivitas</td>
            <td class="info-value" style="font-weight: bold;">{{ $activity->activity_type }}</td>
        </tr>
        <tr>
            <td class="info-label">Topik Utama / Subjek</td>
            <td class="info-value" colspan="3" style="font-weight: bold;">{{ $activity->subject }}</td>
        </tr>
    </table>

    {{-- Section B: Rincian Kronologis Agenda / Aktivitas --}}
    <div class="section-title">B. RINCIAN KRONOLOGIS AGENDA &amp; AKTIVITAS</div>
    @php
        $items = [];
        if (!empty($activity->notes) && is_string($activity->notes) && str_starts_with(trim($activity->notes), '{')) {
            $decoded = json_decode($activity->notes, true);
            if (is_array($decoded) && isset($decoded['items'])) {
                $items = $decoded['items'];
            }
        }
    @endphp

    @if(!empty($items) && count($items) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th class="center" style="width: 32px;">No</th>
                    <th style="width: 90px;">Waktu &amp; Tanggal</th>
                    <th>Kronologis Agenda / Aktivitas</th>
                    <th style="width: 100px;">PIC Klien</th>
                    <th style="width: 100px;">PIC IPNET</th>
                    <th style="width: 160px;">Catatan Aksi / Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $idx => $item)
                    <tr>
                        <td class="center" style="font-weight: bold; color: #8F0A0D;">{{ $item['no'] ?? ($idx + 1) }}</td>
                        <td>
                            <strong>{{ $item['time'] ?: '-' }}</strong><br>
                            <span style="color: #64748B; font-size: 7pt;">{{ $item['date'] ?: '-' }}</span>
                        </td>
                        <td>
                            <strong>{{ $item['subject'] }}</strong>
                        </td>
                        <td>{{ $item['client_pic'] ?: '-' }}</td>
                        <td>{{ $item['ipnet_pic'] ?: '-' }}</td>
                        <td style="color: #334155;">{{ $item['notes'] ?: '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="center" style="width: 32px;">No</th>
                    <th style="width: 100px;">Waktu</th>
                    <th>Deskripsi Pembahasan / Catatan Aktivitas</th>
                    <th style="width: 120px;">PIC Klien</th>
                    <th style="width: 120px;">PIC IPNET</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center" style="font-weight: bold; color: #8F0A0D;">1</td>
                    <td>{{ \Carbon\Carbon::parse($activity->activity_date)->format('H:i') }} WIB</td>
                    <td style="white-space: pre-line;">{{ $activity->notes ?: 'Tidak ada catatan detail.' }}</td>
                    <td>-</td>
                    <td>{{ $activity->sales->name ?? 'Sales' }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- Section C: Tindak Lanjut / Next Action Plan --}}
    @if(!empty($activity->next_action))
        <div class="action-box">
            <div class="action-box-title">C. RENCANA TINDAK LANJUT BERIKUTNYA (NEXT ACTION PLAN)</div>
            <table style="width: 100%; border-collapse: collapse; font-size: 7.5pt;">
                <tr>
                    <td style="width: 75%; vertical-align: top; color: #1E293B;">
                        <strong>Aksi / Komitmen:</strong> {{ $activity->next_action }}
                    </td>
                    <td style="width: 25%; vertical-align: top; text-align: right; color: #92400E; font-weight: bold;">
                        Target Due Date: {{ $activity->next_action_date ? \Carbon\Carbon::parse($activity->next_action_date)->format('d M Y') : 'Segera' }}
                    </td>
                </tr>
            </table>
        </div>
    @endif

    {{-- Catatan Verifikasi & Legalitas Internal (Tanpa tanda tangan fisik sesuai kebutuhan sales) --}}
    <div class="verification-box">
        <strong>PENGESAHAN DOKUMEN INTERNAL &amp; AUDIT TRAIL:</strong><br>
        Dokumen ini dibuat dan divalidasi secara digital melalui <strong>Sistem CRM &amp; Pipeline PT IP Network Solusindo</strong> pada tanggal {{ now()->format('d/m/Y H:i:s') }} oleh <strong>{{ $activity->sales->name ?? 'Sales Team' }}</strong>.<br>
        Berlaku sebagai bukti sah pencatatan histori aktivitas penjualan, notulensi koordinasi, dan agenda interaksi klien tanpa memerlukan tanda tangan basah.
    </div>

</div>
</body>
</html>
