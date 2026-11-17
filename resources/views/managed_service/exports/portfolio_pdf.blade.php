<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Portofolio Managed Service &amp; SLA - PT IP Network Solusindo</title>
    <style>
        @page {
            margin: 14mm 12mm 14mm 12mm;
            size: a4 landscape;
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

        /* ── Header Dokumen ── */
        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 8px;
        }
        .doc-header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .doc-logo {
            max-width: 155px;
            max-height: 48px;
        }
        .company-title {
            font-size: 11pt;
            font-weight: bold;
            color: #8F0A0D;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .company-subtitle {
            font-size: 7pt;
            color: #64748B;
            margin-top: 1px;
        }

        /* ── Title Banner ── */
        .title-banner {
            background-color: #F8FAFC;
            border: 1px solid #CBD5E1;
            border-left: 4px solid #8F0A0D;
            padding: 7px 12px;
            margin-bottom: 10px;
        }
        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 0;
        }
        .doc-meta {
            font-size: 7pt;
            color: #64748B;
            margin-top: 2px;
        }

        /* ── KPI Summary Cards (Table Layout) ── */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .kpi-table td {
            width: 25%;
            padding: 7px 10px;
            background-color: #FFFFFF;
            border: 1px solid #CBD5E1;
            vertical-align: top;
        }
        .kpi-label {
            font-size: 6.8pt;
            font-weight: bold;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 3px;
        }
        .kpi-value {
            font-size: 13pt;
            font-weight: bold;
            color: #0F172A;
            line-height: 1.1;
        }
        .kpi-desc {
            font-size: 6.8pt;
            color: #64748B;
            margin-top: 3px;
        }

        /* ── Data Table ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .data-table th {
            background-color: #8F0A0D;
            color: #FFFFFF;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 5.5px 6px;
            border: 1px solid #74080A;
            text-align: left;
            vertical-align: middle;
        }
        .data-table td {
            font-size: 7.2pt;
            padding: 4.5px 6px;
            border: 1px solid #E2E8F0;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) td {
            background-color: #F8FAFC;
        }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            padding: 1.5px 4.5px;
            border-radius: 3px;
            font-size: 6.5pt;
            font-weight: bold;
            text-align: center;
        }
        .badge-platinum { background-color: #F3E8FF; color: #6B21A8; border: 1px solid #D8B4FE; }
        .badge-gold     { background-color: #FEF3C7; color: #92400E; border: 1px solid #FCD34D; }
        .badge-silver   { background-color: #F1F5F9; color: #334155; border: 1px solid #CBD5E1; }
        .badge-bronze   { background-color: #EFF6FF; color: #1E40AF; border: 1px solid #BFDBFE; }
        
        .badge-success  { background-color: #ECFDF5; color: #065F46; border: 1px solid #A7F3D0; }
        .badge-warning  { background-color: #FFFBEB; color: #92400E; border: 1px solid #FDE68A; }
        .badge-danger   { background-color: #FEF2F2; color: #991B1B; border: 1px solid #FECACA; }

        /* ── Sign-off Section ── */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 15px;
            page-break-inside: avoid;
        }
        .signature-table td {
            border: none;
            width: 33.33%;
            vertical-align: top;
            text-align: center;
            padding: 0 10px;
        }
        .sig-title {
            font-size: 7pt;
            font-weight: bold;
            color: #64748B;
            text-transform: uppercase;
            margin-bottom: 45px;
        }
        .sig-name {
            font-size: 8pt;
            font-weight: bold;
            color: #0F172A;
            border-bottom: 1px solid #CBD5E1;
            padding-bottom: 2px;
            display: inline-block;
            min-width: 160px;
        }
        .sig-role {
            font-size: 6.8pt;
            color: #64748B;
            margin-top: 2px;
        }

        /* ── Page Footer ── */
        .page-footer {
            margin-top: 10px;
            border-top: 1px solid #E2E8F0;
            padding-top: 4px;
            font-size: 6.5pt;
            color: #94A3B8;
            display: table;
            width: 100%;
        }
        .footer-left { display: table-cell; text-align: left; }
        .footer-right { display: table-cell; text-align: right; }
    </style>
</head>
<body>

    {{-- 1. Header Dokumen --}}
    <table class="doc-header-table">
        <tr>
            <td style="width: 50%;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" class="doc-logo" alt="IPNET Logo">
                @else
                    <div class="company-title">PT IP NETWORK SOLUSINDO</div>
                @endif
                <div class="company-subtitle">Managed Service &amp; Network Operations Division • SLA Assurance Report</div>
            </td>
            <td style="width: 50%; text-align: right; font-size: 7pt; color: #475569;">
                <div><strong>Tanggal Cetak:</strong> {{ $printedAt }}</div>
                <div><strong>Dicetak Oleh:</strong> {{ $printedBy }}</div>
                <div><strong>Kategori:</strong> Rekapitulasi Portofolio SLA Seluruh Proyek</div>
            </td>
        </tr>
    </table>

    {{-- 2. Title Banner --}}
    <div class="title-banner">
        <h1 class="doc-title">Laporan Rekapitulasi Portofolio Managed Service &amp; Kinerja SLA</h1>
        <div class="doc-meta">
            Monitoring performa siklus Preventive Maintenance (PM), kepatuhan SLA kontrak operasional, serta tiket insiden Corrective Maintenance (CM).
            @if(!empty($filterTier) && $filterTier !== 'all')
                • <strong>Filter Tier:</strong> {{ $filterTier }}
            @endif
            @if(!empty($filterSearch))
                • <strong>Kata Kunci:</strong> "{{ $filterSearch }}"
            @endif
        </div>
    </div>

    {{-- 3. Ringkasan Eksekutif KPI --}}
    <table class="kpi-table">
        <tr>
            <td>
                <div class="kpi-label">Total Proyek Terkelola</div>
                <div class="kpi-value" style="color: #8F0A0D;">{{ $totalProjectsCount }}</div>
                <div class="kpi-desc">Kontrak Proyek Aktif</div>
            </td>
            <td>
                <div class="kpi-label">Progress Siklus PM</div>
                <div class="kpi-value" style="color: #059669;">{{ $portfolioPmProgress }}%</div>
                <div class="kpi-desc">{{ $totalPmCompleted }} dari {{ $totalPmCycles }} Siklus Selesai</div>
            </td>
            <td>
                <div class="kpi-label">Rata-rata Target SLA</div>
                <div class="kpi-value" style="color: #1E293B;">{{ number_format($avgSlaTarget, 2) }}%</div>
                <div class="kpi-desc">Kepatuhan Aktual: <strong>{{ number_format($avgSlaCompliance, 1) }}%</strong></div>
            </td>
            <td>
                <div class="kpi-label">Tiket Insiden (CM)</div>
                <div class="kpi-value" style="color: {{ $totalActiveTickets > 0 ? '#D97706' : '#059669' }};">
                    {{ $totalActiveTickets }} <span style="font-size: 8.5pt; font-weight: normal; color: #64748B;">Aktif / {{ $totalClosedTickets }} Closed</span>
                </div>
                <div class="kpi-desc">Total {{ $totalActiveTickets + $totalClosedTickets }} Tiket Masuk</div>
            </td>
        </tr>
    </table>

    {{-- 4. Tabel Rekapitulasi Proyek & SLA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 175px;">Nama Project &amp; Customer</th>
                <th style="width: 55px; text-align: center;">Tier SLA</th>
                <th style="width: 85px; text-align: center;">Siklus PM</th>
                <th style="width: 65px; text-align: center;">Target SLA</th>
                <th style="width: 80px; text-align: center;">Kepatuhan SLA</th>
                <th style="width: 60px; text-align: center;">Tiket Aktif</th>
                <th style="width: 60px; text-align: center;">Tiket Closed</th>
                <th style="width: 75px; text-align: center;">Kuota &amp; Sisa CM</th>
                <th style="width: 75px; text-align: center;">Status Layanan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $idx => $row)
                @php
                    $p = $row['project'];
                    $tier = strtolower($p->sla_tier ?? 'gold');
                    $badgeTier = match($tier) {
                        'platinum' => 'badge-platinum',
                        'gold'     => 'badge-gold',
                        'silver'   => 'badge-silver',
                        default    => 'badge-bronze',
                    };
                    $isSlaMet = ($row['sla_compliance'] >= 90.0);
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold; color: #64748B;">{{ $idx + 1 }}</td>
                    <td>
                        <strong style="color: #0F172A; font-size: 7.5pt;">{{ $p->name }}</strong>
                        <div style="font-size: 6.8pt; color: #64748B;">
                            Customer: {{ $p->client ?: '-' }}
                            @if($p->sales_name)
                                • Sales: {{ $p->sales_name }}
                            @endif
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge {{ $badgeTier }}">{{ $p->sla_tier ?: 'Gold' }}</span>
                    </td>
                    <td style="text-align: center;">
                        <strong>{{ $row['completed_cycles'] }} / {{ $row['total_cycles'] }}</strong>
                        <div style="font-size: 6.5pt; color: {{ $row['pm_percent'] >= 80 ? '#059669' : '#8F0A0D' }}; font-weight: bold;">
                            ({{ $row['pm_percent'] }}%)
                        </div>
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ number_format($row['target_sla'], 2) }}%
                    </td>
                    <td style="text-align: center;">
                        <span class="badge {{ $isSlaMet ? 'badge-success' : 'badge-danger' }}">
                            {{ number_format($row['sla_compliance'], 1) }}%
                        </span>
                    </td>
                    <td style="text-align: center;">
                        @if($row['active_tickets'] > 0)
                            <span class="badge badge-warning">{{ $row['active_tickets'] }} Aktif</span>
                        @else
                            <span style="color: #059669; font-weight: bold;">0</span>
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: bold; color: #059669;">
                        {{ $row['closed_tickets'] }}
                    </td>
                    <td style="text-align: center;">
                        <span style="font-weight: bold;">{{ $row['total_tickets'] }} / {{ $row['cm_quota'] >= 999 ? '∞' : $row['cm_quota'] }}</span>
                        <div style="font-size: 6.5pt; color: {{ $row['remaining_quota'] <= 2 ? '#DC2626' : '#64748B' }}; font-weight: bold;">
                            Sisa: {{ $row['cm_quota'] >= 999 ? '∞' : $row['remaining_quota'] }}
                        </div>
                    </td>
                    <td style="text-align: center;">
                        @if($row['active_tickets'] === 0 && $isSlaMet)
                            <span class="badge badge-success">Optimal</span>
                        @elseif($row['active_tickets'] > 0 && $isSlaMet)
                            <span class="badge badge-warning">Ada Tiket</span>
                        @else
                            <span class="badge badge-danger">Perhatian</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; padding: 18px; color: #64748B;">
                        Tidak ada data proyek yang sesuai kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 5. Lembar Pengesahan / Tanda Tangan --}}
    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-title">Disiapkan Oleh</div>
                <div class="sig-name">{{ $printedBy }}</div>
                <div class="sig-role">Managed Service Coordinator / Delivery Lead</div>
            </td>
            <td>
                <div class="sig-title">Ditinjau &amp; Diverifikasi Oleh</div>
                <div class="sig-name">Lead Network Engineer</div>
                <div class="sig-role">NOC &amp; Field Infrastructure Lead</div>
            </td>
            <td>
                <div class="sig-title">Disetujui Oleh</div>
                <div class="sig-name">Head of Managed Service</div>
                <div class="sig-role">Group Leader Delivery &amp; Operation</div>
            </td>
        </tr>
    </table>

    {{-- 6. Footer --}}
    <div class="page-footer">
        <div class="footer-left">
            PT IP Network Solusindo • Dokumen Resmi Hasil Cetak Sistem ERP IPNET • Sifat: Terbatas &amp; Rahasia
        </div>
        <div class="footer-right">
            Dicetak: {{ $printedAt }}
        </div>
    </div>

</body>
</html>
