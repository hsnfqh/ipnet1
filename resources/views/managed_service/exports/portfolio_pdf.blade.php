<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Portofolio Managed Service &amp; SLA - PT IP Network Solusindo</title>
    <style>
        /* ── Konfigurasi Halaman & Margin DomPDF A4 Landscape ── */
        @page {
            margin-top: 28mm;
            margin-bottom: 18mm;
            margin-left: 14mm;
            margin-right: 14mm;
            size: a4 landscape;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 7.5pt;
            color: #0F172A;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }

        /* ── Fixed Running Header & Footer di Setiap Halaman ── */
        header.page-header {
            position: fixed;
            top: -22mm;
            left: 0;
            right: 0;
            height: 16mm;
            border-bottom: 1.5px solid #8F0A0D;
            padding-bottom: 3px;
        }
        footer.page-footer {
            position: fixed;
            bottom: -13mm;
            left: 0;
            right: 0;
            height: 10mm;
            border-top: 1px solid #CBD5E1;
            padding-top: 3px;
            font-size: 6.8pt;
            color: #64748B;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .header-company-name {
            font-size: 10pt;
            font-weight: bold;
            color: #8F0A0D;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-company-sub {
            font-size: 6.8pt;
            color: #475569;
        }
        .header-doc-meta {
            text-align: right;
            font-size: 6.8pt;
            color: #334155;
            line-height: 1.3;
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
        }

        /* ── Kop Surat Resmi Halaman Pertama ── */
        .official-letterhead {
            margin-top: -3mm;
            margin-bottom: 10px;
            border-bottom: 2.5px solid #8F0A0D;
            padding-bottom: 8px;
        }
        .letterhead-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        .letterhead-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .letterhead-logo {
            max-width: 155px;
            max-height: 48px;
        }
        .corp-name {
            font-size: 13pt;
            font-weight: 800;
            color: #8F0A0D;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            line-height: 1.1;
        }
        .corp-tagline {
            font-size: 7.5pt;
            font-weight: bold;
            color: #1E293B;
            margin-top: 2px;
            letter-spacing: 0.3px;
        }
        .corp-contact {
            font-size: 6.8pt;
            color: #475569;
            margin-top: 2px;
            line-height: 1.3;
        }
        .corp-sub-bar {
            height: 1px;
            background-color: #1E293B;
            margin-top: 2px;
        }

        /* ── Box Dokumen Resmi & Judul Berkas ── */
        .doc-official-box {
            background-color: #F8FAFC;
            border: 1px solid #CBD5E1;
            border-left: 4.5px solid #8F0A0D;
            padding: 8px 12px;
            margin-bottom: 12px;
            border-radius: 4px;
        }
        .doc-title-main {
            font-size: 11pt;
            font-weight: 800;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin: 0;
        }
        .doc-title-sub {
            font-size: 7.2pt;
            color: #475569;
            margin-top: 3px;
            line-height: 1.4;
        }

        /* ── Section Titles ── */
        .section-header {
            font-size: 8.5pt;
            font-weight: bold;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 10px;
            margin-bottom: 5px;
            padding-bottom: 2px;
            border-bottom: 1.5px solid #E2E8F0;
        }
        .section-num {
            color: #8F0A0D;
            font-weight: 800;
            margin-right: 3px;
        }

        /* ── KPI Metrik Table ── */
        .kpi-grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .kpi-grid-table td {
            width: 25%;
            padding: 7px 10px;
            background-color: #FFFFFF;
            border: 1px solid #CBD5E1;
            vertical-align: top;
        }
        .kpi-card-lbl {
            font-size: 6.8pt;
            font-weight: bold;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 2px;
        }
        .kpi-card-val {
            font-size: 13pt;
            font-weight: 800;
            color: #0F172A;
            line-height: 1.1;
        }
        .kpi-card-sub {
            font-size: 6.8pt;
            color: #64748B;
            margin-top: 3px;
        }

        /* ── Data Tables ── */
        .grid-data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            page-break-inside: auto;
        }
        .grid-data-table th {
            background-color: #8F0A0D;
            color: #FFFFFF;
            font-size: 6.8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 5px 6px;
            border: 1px solid #74080A;
            text-align: left;
            vertical-align: middle;
        }
        .grid-data-table td {
            font-size: 7pt;
            padding: 4.5px 6px;
            border: 1px solid #E2E8F0;
            vertical-align: middle;
        }
        .grid-data-table tr:nth-child(even) td {
            background-color: #F8FAFC;
        }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            padding: 1.5px 5px;
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

        /* ── Lembar Pengesahan Formal (3 Pihak) ── */
        .signature-section {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 14px;
            page-break-inside: avoid;
        }
        .signature-section td {
            border: none;
            width: 33.33%;
            vertical-align: top;
            text-align: center;
            padding: 0 14px;
        }
        .sign-title {
            font-size: 7pt;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 44px;
        }
        .sign-line {
            font-size: 8pt;
            font-weight: bold;
            color: #0F172A;
            border-bottom: 1.2px solid #0F172A;
            padding-bottom: 2px;
            display: inline-block;
            min-width: 160px;
        }
        .sign-dept {
            font-size: 6.8pt;
            color: #64748B;
            margin-top: 2px;
            line-height: 1.25;
        }
    </style>
</head>
<body>

    {{-- FIXED RUNNING HEADER (MUNCUL DI SETIAP HALAMAN) --}}
    <header class="page-header">
        <table class="header-table">
            <tr>
                <td style="width: 60%;">
                    <div class="header-company-name">PT IP NETWORK SOLUSINDO</div>
                    <div class="header-company-sub">Managed Service &amp; Network Operations Division • Executive Portfolio SLA Summary</div>
                </td>
                <td style="width: 40%;" class="header-doc-meta">
                    <div><strong>No. Dokumen:</strong> {{ $docNumber }}</div>
                    <div><strong>Tanggal Terbit:</strong> {{ $printedAt }}</div>
                </td>
            </tr>
        </table>
    </header>

    {{-- FIXED RUNNING FOOTER (MUNCUL DI SETIAP HALAMAN) --}}
    <footer class="page-footer">
        <table class="footer-table">
            <tr>
                <td style="width: 70%;">
                    PT IP Network Solusindo • Dokumen Resmi Hasil Cetak Sistem ERP IPNET • Rahasia &amp; Terbatas (Confidential)
                </td>
                <td style="width: 30%; text-align: right;">
                    Dicetak: {{ $printedAt }}
                </td>
            </tr>
        </table>
    </footer>

    {{-- SCRIPT DOMPDF CANVAS UNTUK PENOMORAN HALAMAN OTOMATIS --}}
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman " . $PAGE_NUM . " dari " . $PAGE_COUNT;
            $font = $fontMetrics->get_font("Helvetica", "bold");
            $size = 6.8;
            $color = array(0.4, 0.45, 0.5);
            $w = $fontMetrics->getTextWidth($text, $font, $size);
            $x = 841.89 - 14 * 2.83465 - $w; // A4 landscape width 841.89pt
            $y = 595.28 - 10 * 2.83465;
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>

    {{-- KOP SURAT RESMI PERUSAHAAN (HALAMAN PERTAMA) --}}
    <div class="official-letterhead">
        <table class="letterhead-table">
            <tr>
                <td style="width: 20%; text-align: left;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" class="letterhead-logo" alt="IPNET Logo">
                    @else
                        <div class="corp-name">IPNET</div>
                    @endif
                </td>
                <td style="width: 80%; text-align: left; padding-left: 14px;">
                    <div class="corp-name">PT IP NETWORK SOLUSINDO</div>
                    <div class="corp-tagline">Network Solutions, IT Managed Services &amp; Telecommunication Infrastructure</div>
                    <div class="corp-contact">
                        Grand Slipi Tower Lt. 9, Jl. Letjen S. Parman Kav. 22-24, Slipi, Palmerah, Jakarta Barat 11480<br>
                        Telepon: (021) 2986-6190 • E-mail: support@ipnetsolusindo.com • Website: www.ipnetsolusindo.com
                    </div>
                </td>
            </tr>
        </table>
        <div class="corp-sub-bar"></div>
    </div>

    {{-- BOX JUDUL BERKAS OPERASIONAL --}}
    <div class="doc-official-box">
        <table style="width: 100%; border-collapse: collapse; border: none;">
            <tr>
                <td style="border: none; padding: 0;">
                    <h1 class="doc-title-main">Rekapitulasi Portofolio Klien Managed Service &amp; Kepatuhan SLA</h1>
                    <div class="doc-title-sub">
                        Dokumen eksekutif rekapitulasi seluruh kontrak pemeliharaan aktif, status kesehatan layanan, kepatuhan SLA ketersediaan (Availability), serta realisasi penugasan berkala di seluruh customer PT IP Network Solusindo.
                    </div>
                </td>
                <td style="border: none; padding: 0; width: 160px; text-align: right; vertical-align: top;">
                    <div style="font-size: 7pt; font-weight: bold; color: #8F0A0D; text-transform: uppercase;">Berkas Resmi Operasional</div>
                    <div style="font-size: 6.8pt; color: #475569; margin-top: 2px;">Klasifikasi: <strong>CONFIDENTIAL</strong></div>
                </td>
            </tr>
        </table>
    </div>

    {{-- SECTION 1: RINGKASAN EKSEKUTIF --}}
    <div class="section-header"><span class="section-num">[1]</span> Ringkasan Eksekutif Portofolio (Executive Summary)</div>
    <table class="kpi-grid-table">
        <tr>
            <td>
                <div class="kpi-card-lbl">Total Proyek MS Aktif</div>
                <div class="kpi-card-val" style="color: #8F0A0D;">{{ $totalProjectsCount }}</div>
                <div class="kpi-card-sub">Kontrak Layanan Berjalan</div>
            </td>
            <td>
                <div class="kpi-card-lbl">Rata-rata Kepatuhan SLA</div>
                <div class="kpi-card-val" style="color: {{ $avgSla >= 99.0 ? '#059669' : '#D97706' }};">
                    {{ number_format($avgSla, 1) }}%
                </div>
                <div class="kpi-card-sub">Target Standar: &gt;= 99.50%</div>
            </td>
            <td>
                <div class="kpi-card-lbl">Realisasi Siklus PM</div>
                <div class="kpi-card-val" style="color: #059669;">{{ $totalPmCompleted }} / {{ $totalPmCycles }}</div>
                <div class="kpi-card-sub">Persentase Capaian: <strong>{{ $pmProgressPercent }}%</strong></div>
            </td>
            <td>
                <div class="kpi-card-lbl">Akumulasi Tiket Insiden (CM)</div>
                <div class="kpi-card-val" style="color: {{ $totalActiveTickets > 0 ? '#D97706' : '#059669' }};">
                    {{ $totalActiveTickets }} <span style="font-size: 8pt; font-weight: normal; color: #64748B;">Aktif / {{ $totalClosedTickets }} Selesai</span>
                </div>
                <div class="kpi-card-sub">Total {{ $totalTicketsAll }} Tiket Dilaporkan</div>
            </td>
        </tr>
    </table>

    {{-- SECTION 2: TABEL PORTOFOLIO KLIEN & KINERJA SLA --}}
    <div class="section-header"><span class="section-num">[2]</span> Status Layanan Customer &amp; Distribusi Proyek SLA</div>
    <table class="grid-data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 140px;">Customer / Klien</th>
                <th>Nama Proyek Managed Service</th>
                <th style="width: 65px; text-align: center;">Tier SLA</th>
                <th style="width: 60px; text-align: center;">Target</th>
                <th style="width: 65px; text-align: center;">SLA Aktual</th>
                <th style="width: 65px; text-align: center;">Siklus PM</th>
                <th style="width: 65px; text-align: center;">Tiket Aktif</th>
                <th style="width: 75px; text-align: center;">Status Layanan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($portfolioData as $idx => $item)
                @php
                    $p = $item['project'];
                    $tierLower = strtolower($p->sla_tier ?? 'gold');
                    $tierBadge = match($tierLower) {
                        'platinum' => 'badge-platinum',
                        'silver'   => 'badge-silver',
                        'bronze'   => 'badge-bronze',
                        default    => 'badge-gold',
                    };
                    $slaMet = $item['sla_score'] >= $item['target_sla'];
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $idx + 1 }}</td>
                    <td><strong>{{ $p->client ?: '-' }}</strong></td>
                    <td>
                        <strong style="color: #0F172A;">{{ $p->name }}</strong>
                        <div style="font-size: 6.2pt; color: #64748B;">PIC: {{ $p->sales_name ?: ($p->creator->name ?? 'IPNET') }} • {{ $p->location ?: 'Site Customer' }}</div>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge {{ $tierBadge }}">{{ strtoupper($p->sla_tier ?: 'Gold') }}</span>
                    </td>
                    <td style="text-align: center;">{{ number_format($item['target_sla'], 2) }}%</td>
                    <td style="text-align: center; font-weight: bold; color: {{ $slaMet ? '#059669' : '#DC2626' }};">
                        {{ number_format($item['sla_score'], 1) }}%
                    </td>
                    <td style="text-align: center;">
                        <strong>{{ $item['pm_completed'] }}</strong> / {{ $item['pm_total'] }}
                    </td>
                    <td style="text-align: center;">
                        <strong style="color: {{ $item['active_tickets'] > 0 ? '#D97706' : '#059669' }};">
                            {{ $item['active_tickets'] }}
                        </strong>
                        <span style="font-size: 6.2pt; color: #64748B;">({{ $item['closed_tickets'] }} selesai)</span>
                    </td>
                    <td style="text-align: center;">
                        @if($item['health'] === 'Normal')
                            <span class="badge badge-success">Aman (Normal)</span>
                        @elseif($item['health'] === 'Preventive')
                            <span class="badge badge-silver">Preventive</span>
                        @else
                            <span class="badge badge-danger">Corrective</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 12px; color: #64748B;">
                        Tidak ada data proyek Managed Service yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SECTION 3: LEMBAR PENGESAHAN DOKUMEN --}}
    <div class="section-header"><span class="section-num">[3]</span> Lembar Pengesahan Operasional</div>
    <table class="signature-section">
        <tr>
            <td>
                <div class="sign-title">Disiapkan &amp; Dilaporkan Oleh:</div>
                <div class="sign-line">{{ $printedBy }}</div>
                <div class="sign-dept">Service Delivery Lead / Operations<br>PT IP Network Solusindo</div>
            </td>
            <td>
                <div class="sign-title">Diperiksa &amp; Diverifikasi Oleh:</div>
                <div class="sign-line">Head of Managed Service</div>
                <div class="sign-dept">Divisi Managed Service &amp; Operasional<br>PT IP Network Solusindo</div>
            </td>
            <td>
                <div class="sign-title">Disetujui Oleh:</div>
                <div class="sign-line">Direktur Operasional / VP Delivery</div>
                <div class="sign-dept">Manajemen Eksekutif<br>PT IP Network Solusindo</div>
            </td>
        </tr>
    </table>

</body>
</html>
