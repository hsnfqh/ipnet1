@php
    $totalProjectsCount = (int) ($totalProjectsCount ?? (isset($portfolioData) ? count($portfolioData) : 0));
    $totalPmCompleted = (int) ($totalPmCompleted ?? 0);
    $totalPmCycles = (int) ($totalPmCycles ?? 0);
    $portfolioPmProgress = (int) ($portfolioPmProgress ?? ($pmProgressPercent ?? ($totalPmCycles > 0 ? round(($totalPmCompleted / $totalPmCycles) * 100) : 0)));
    $pmProgressPercent = $portfolioPmProgress;
    $totalActiveTickets = (int) ($totalActiveTickets ?? 0);
    $totalClosedTickets = (int) ($totalClosedTickets ?? 0);
    $totalTicketsAll = (int) ($totalTicketsAll ?? ($totalActiveTickets + $totalClosedTickets));
    $avgSla = (float) ($avgSla ?? ($avgSlaCompliance ?? 100.0));
    $avgSlaTarget = (float) ($avgSlaTarget ?? 99.50);
    $docNumber = $docNumber ?? ('MS-PORT/' . date('Y/m/dHi'));
    $printedAt = $printedAt ?? (date('d F Y H:i') . ' WIB');
    $printedBy = $printedBy ?? (auth()->user()->name ?? 'Administrator Managed Service');
    $portfolioItems = $portfolioData ?? ($projects ?? []);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Portofolio Managed Service &amp; SLA - PT IP Network Solusindo</title>
    <style>
        /* ── Konfigurasi Halaman & Margin DomPDF Sesuai Standar Activity Log ── */
        @page {
            margin-top: 24mm;
            margin-bottom: 16mm;
            margin-left: 10mm;
            margin-right: 10mm;
            size: a4 landscape;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 7.2pt;
            color: #000000;
            line-height: 1.25;
            margin: 0;
            padding: 0;
        }

        /* ── Fixed Running Header (Muncul di Setiap Halaman) ── */
        header.page-header {
            position: fixed;
            top: -19mm;
            left: 0;
            right: 0;
            height: 15mm;
            border-bottom: 1.5px solid #000000;
            padding-bottom: 3px;
        }

        /* ── Fixed Running Footer (Muncul di Setiap Halaman) ── */
        footer.page-footer {
            position: fixed;
            bottom: -11mm;
            left: 0;
            right: 0;
            height: 8mm;
            border-top: 1px solid #000000;
            padding-top: 2px;
        }

        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin: 0;
        }
        .doc-header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .doc-logo {
            max-width: 100px;
            max-height: 38px;
            display: block;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
            line-height: 1.2;
        }
        .doc-sub-corp {
            font-size: 7pt;
            font-weight: bold;
            color: #8F0A0D;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
            margin-top: 1px;
        }

        /* ── Intro Dokumen ── */
        .doc-intro {
            font-size: 6.7pt;
            color: #1E293B;
            margin-top: 2px;
            margin-bottom: 5px;
            line-height: 1.25;
        }

        /* ── Judul Section Formal ── */
        .section-title {
            font-size: 7.6pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            margin-top: 5px;
            margin-bottom: 2px;
            letter-spacing: 0.2px;
        }

        /* ── Tabel Standar Resmi (Sesuai Activity Log #EBF3FB) ── */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
            table-layout: fixed;
            page-break-inside: auto;
        }
        .report-table th {
            background-color: #EBF3FB;
            color: #000000;
            font-weight: bold;
            font-size: 6.7pt;
            padding: 2.5px 3px;
            border: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
        }
        .report-table td {
            font-size: 6.7pt;
            padding: 2.2px 3.5px;
            border: 1px solid #000000;
            vertical-align: middle;
            color: #000000;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.2;
        }
        .report-table td.label-cell {
            font-weight: bold;
            background-color: #FFFFFF;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .align-top { vertical-align: top !important; }
        .font-bold { font-weight: bold; }

        /* ── Badge Box Formal ── */
        .badge-box {
            display: inline-block;
            font-size: 6pt;
            font-weight: bold;
            padding: 1px 4px;
            border: 1px solid #000000;
            background-color: #FFFFFF;
            color: #000000;
            text-align: center;
        }
        .badge-box-success {
            background-color: #E6F4EA;
            border: 1px solid #137333;
            color: #137333;
        }
        .badge-box-warning {
            background-color: #FEF7E0;
            border: 1px solid #B06000;
            color: #B06000;
        }
        .badge-box-danger {
            background-color: #FCE8E6;
            border: 1px solid #C5221F;
            color: #C5221F;
        }

        /* ── Kotak Pengesahan Formal (3 Pihak) ── */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000000;
            margin-top: 6px;
            page-break-inside: avoid;
        }
        .sig-table th {
            background-color: #EBF3FB;
            font-size: 6.7pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            padding: 2.5px 3px;
            border: 1px solid #000000;
            text-align: center;
            width: 33.33%;
        }
        .sig-table td {
            border: 1px solid #000000;
            padding: 3px;
            text-align: center;
            vertical-align: top;
            width: 33.33%;
        }
        .sig-space {
            height: 42px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 7.2pt;
            color: #000000;
        }
        .sig-role {
            font-size: 6.1pt;
            color: #334155;
            margin-top: 1px;
            line-height: 1.2;
        }

        /* ── Footer Table ── */
        .doc-footer-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }
        .doc-footer-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
            font-size: 5.8pt;
            color: #000000;
            line-height: 1.2;
        }
    </style>
</head>
<body>

    {{-- FIXED RUNNING HEADER (BERULANG DI SETIAP HALAMAN) --}}
    <header class="page-header">
        <table class="doc-header-table">
            <tr>
                <td style="width: 16%; text-align: left; vertical-align: middle;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" class="doc-logo" alt="IPNET Logo">
                    @else
                        <div style="font-size: 13pt; font-weight: bold; color: #8F0A0D;">IPNET</div>
                    @endif
                </td>
                <td style="width: 58%; text-align: center; vertical-align: middle;">
                    <div class="doc-title">REKAPITULASI PORTOFOLIO MANAGED SERVICE &amp; KEPATUHAN SLA</div>
                    <div class="doc-sub-corp">PT IP NETWORK SOLUSINDO &bull; MANAGED SERVICE &amp; NETWORK OPERATIONS DIVISION</div>
                </td>
                <td style="width: 26%; text-align: right; vertical-align: middle; font-size: 6.1pt; line-height: 1.25;">
                    <div><strong>No. Laporan:</strong> {{ $docNumber }}</div>
                    <div><strong>Tanggal:</strong> {{ $printedAt }}</div>
                    <div><strong>Penyusun:</strong> {{ $printedBy }}</div>
                </td>
            </tr>
        </table>
    </header>

    {{-- FIXED RUNNING FOOTER (BERULANG DI SETIAP HALAMAN) --}}
    <footer class="page-footer">
        <table class="doc-footer-table">
            <tr>
                <td style="width: 70%;">
                    PT IP Network Solusindo &bull; Dokumen Resmi Hasil Cetak Sistem FSM IPNET &bull; Rahasia &amp; Terbatas (Confidential)
                </td>
                <td style="width: 30%; text-align: right;">
                    Dicetak: {{ $printedAt }}
                </td>
            </tr>
        </table>
    </footer>

    {{-- SCRIPT DOMPDF CANVAS PENOMORAN HALAMAN PERSISI (HALAMAN X DARI Y) --}}
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman " . $PAGE_NUM . " dari " . $PAGE_COUNT;
            $font = $fontMetrics->get_font("Helvetica", "normal");
            $size = 6.0;
            $color = array(0.1, 0.1, 0.1);
            $w = $fontMetrics->getTextWidth($text, $font, $size);
            $x = 841.89 - 10 * 2.83465 - $w; // A4 landscape width 841.89pt
            $y = 595.28 - 5 * 2.83465;
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>

    <div class="doc-intro">
        Dokumen resmi rekapitulasi portofolio seluruh proyek Managed Service yang aktif berjalan di bawah pengawasan PT IP Network Solusindo. Meliputi kepatuhan Service Level Agreement (SLA Availability), realisasi siklus kunjungan Preventive Maintenance (PM), akumulasi insiden Corrective Maintenance (CM), serta status kesehatan operasional tiap klien customer.
    </div>

    {{-- A. RINGKASAN EKSEKUTIF PORTOFOLIO --}}
    <div class="section-title">A. RINGKASAN EKSEKUTIF PORTOFOLIO MANAGED SERVICE</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 25%;">Total Proyek MS Aktif</th>
                <th style="width: 25%;">Rata-rata Kepatuhan SLA</th>
                <th style="width: 25%;">Realisasi Kunjungan PM</th>
                <th style="width: 25%;">Akumulasi Tiket Gangguan (CM)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="padding: 4px;">
                    <div style="font-size: 13pt; font-weight: bold; color: #000000;">{{ $totalProjectsCount }}</div>
                    <div style="font-size: 6.2pt; color: #334155; margin-top: 1px;">Kontrak Managed Service Berjalan</div>
                </td>
                <td class="text-center" style="padding: 4px;">
                    <div style="font-size: 13pt; font-weight: bold; color: {{ $avgSla >= 99.0 ? '#137333' : '#B06000' }};">
                        {{ number_format($avgSla, 1) }}%
                    </div>
                    <div style="font-size: 6.2pt; color: #334155; margin-top: 1px;">
                        Target Standar: &gt;= {{ number_format($avgSlaTarget, 2) }}% &bull; 
                        <strong style="color: {{ $avgSla >= 99.0 ? '#137333' : '#B06000' }};">{{ $avgSla >= 99.0 ? 'TERPENUHI' : 'PERHATIAN' }}</strong>
                    </div>
                </td>
                <td class="text-center" style="padding: 4px;">
                    <div style="font-size: 13pt; font-weight: bold; color: #137333;">
                        {{ $totalPmCompleted }} / {{ $totalPmCycles }}
                    </div>
                    <div style="font-size: 6.2pt; color: #334155; margin-top: 1px;">
                        Capaian Portofolio: <strong>{{ $pmProgressPercent }}%</strong> Terlaksana
                    </div>
                </td>
                <td class="text-center" style="padding: 4px;">
                    <div style="font-size: 13pt; font-weight: bold; color: {{ $totalActiveTickets > 0 ? '#B06000' : '#137333' }};">
                        {{ $totalActiveTickets }} <span style="font-size: 7.5pt; font-weight: normal;">Aktif</span> / {{ $totalClosedTickets }} <span style="font-size: 7.5pt; font-weight: normal;">Solved</span>
                    </div>
                    <div style="font-size: 6.2pt; color: #334155; margin-top: 1px;">
                        Total {{ $totalTicketsAll }} Tiket Masuk Dilaporkan
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- B. DAFTAR KLIEN & RINCIAN KINERJA SLA TIAP PROYEK --}}
    <div class="section-title">B. STATUS LAYANAN CUSTOMER &amp; KINERJA SLA TIAP PROYEK</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 140px;">Customer / Klien</th>
                <th>Nama Proyek Managed Service</th>
                <th style="width: 55px;">Tier SLA</th>
                <th style="width: 50px;">Target</th>
                <th style="width: 60px;">SLA Aktual</th>
                <th style="width: 65px;">Siklus PM</th>
                <th style="width: 75px;">Tiket CM</th>
                <th style="width: 75px;">Status Layanan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($portfolioItems as $idx => $item)
                @php
                    $p = $item['project'] ?? null;
                    if (!$p) continue;
                    $rowCustomer = trim((string)($p->client ?: '-'));
                    $targetVal = (float) ($item['target_sla'] ?? 99.50);
                    $actualVal = (float) ($item['sla_score'] ?? ($item['sla_compliance'] ?? 100.0));
                    $slaMet = $actualVal >= 90.0;
                    $pmDone = (int) ($item['completed_cycles'] ?? ($item['pm_completed'] ?? 0));
                    $pmTot  = (int) ($item['total_cycles'] ?? ($item['pm_total'] ?? 1));
                    $actT   = (int) ($item['active_tickets'] ?? 0);
                    $cloT   = (int) ($item['closed_tickets'] ?? 0);
                    $health = $item['health'] ?? 'Normal';
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td><strong style="color: #000000;">{{ $rowCustomer }}</strong></td>
                    <td>
                        <strong>{{ $p->name }}</strong>
                        <div style="font-size: 6pt; color: #475569;">
                            PIC: {{ $p->sales_name ?: ($p->creator->name ?? 'IPNET') }} &bull; {{ $p->location ?: 'Site Customer' }}
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge-box"><strong>{{ strtoupper($p->sla_tier ?: 'Gold') }}</strong></span>
                    </td>
                    <td class="text-center">{{ number_format($targetVal, 2) }}%</td>
                    <td class="text-center font-bold" style="color: {{ $slaMet ? '#137333' : '#C5221F' }};">
                        {{ number_format($actualVal, 1) }}%
                    </td>
                    <td class="text-center">
                        <strong>{{ $pmDone }}</strong> / {{ $pmTot }}
                        <span style="font-size: 6pt; color: #475569;">({{ $pmTot > 0 ? round(($pmDone/$pmTot)*100) : 0 }}%)</span>
                    </td>
                    <td class="text-center">
                        <strong style="color: {{ $actT > 0 ? '#B06000' : '#137333' }};">{{ $actT }} Aktif</strong>
                        <div style="font-size: 5.8pt; color: #475569;">{{ $cloT }} Selesai</div>
                    </td>
                    <td class="text-center">
                        @if($health === 'Normal')
                            <span class="badge-box badge-box-success">AMAN (NORMAL)</span>
                        @elseif($health === 'Preventive')
                            <span class="badge-box badge-box-warning">PREVENTIVE</span>
                        @else
                            <span class="badge-box badge-box-danger">CORRECTIVE</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 10px; color: #64748B;">
                        Tidak ada data proyek Managed Service yang ditemukan.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- C. LEMBAR PENGESAHAN DOKUMEN (3 PIHAK FORMAL PERSIS ACTIVITY LOG) --}}
    <div class="section-title">C. LEMBAR PENGESAHAN OPERASIONAL PORTOFOLIO</div>
    <table class="sig-table">
        <thead>
            <tr>
                <th>Disiapkan &amp; Dilaporkan Oleh:</th>
                <th>Diperiksa &amp; Diverifikasi:</th>
                <th>Disetujui &amp; Disahkan:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $printedBy }}</div>
                    <div class="sig-role">Service Delivery Lead / Operations<br>PT IP Network Solusindo</div>
                </td>
                <td>
                    <div class="sig-space"></div>
                    <div class="sig-name">Susanto Djaya</div>
                    <div class="sig-role">Head of Managed Service<br>PT IP Network Solusindo</div>
                </td>
                <td>
                    <div class="sig-space"></div>
                    <div class="sig-name">Direktur Operasional / VP Delivery</div>
                    <div class="sig-role">Manajemen Eksekutif<br>PT IP Network Solusindo</div>
                </td>
            </tr>
        </tbody>
    </table>

</body>
</html>
