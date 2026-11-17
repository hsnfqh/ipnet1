<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja SLA &amp; Operasional - {{ $project->name }} - PT IP Network Solusindo</title>
    <style>
        /* ── Konfigurasi Halaman & Margin DomPDF ── */
        @page {
            margin-top: 30mm;
            margin-bottom: 20mm;
            margin-left: 14mm;
            margin-right: 14mm;
            size: a4 portrait;
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
            top: -24mm;
            left: 0;
            right: 0;
            height: 18mm;
            border-bottom: 1.5px solid #8F0A0D;
            padding-bottom: 3px;
        }
        footer.page-footer {
            position: fixed;
            bottom: -14mm;
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
        .header-logo {
            max-width: 130px;
            max-height: 38px;
        }
        .header-company-name {
            font-size: 9.5pt;
            font-weight: bold;
            color: #8F0A0D;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header-company-sub {
            font-size: 6.5pt;
            color: #475569;
            line-height: 1.25;
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
            position: relative;
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
            margin-top: 12px;
            margin-bottom: 5px;
            padding-bottom: 2px;
            border-bottom: 1.5px solid #E2E8F0;
        }
        .section-num {
            color: #8F0A0D;
            font-weight: 800;
            margin-right: 3px;
        }

        /* ── Tabel Data & Formulir ── */
        .table-form {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 1px solid #CBD5E1;
        }
        .table-form td {
            font-size: 7.2pt;
            padding: 4.5px 7px;
            border: 1px solid #CBD5E1;
            vertical-align: middle;
        }
        .lbl-col {
            background-color: #F8FAFC;
            font-weight: bold;
            color: #475569;
            width: 18%;
        }
        .val-col {
            color: #0F172A;
            width: 32%;
        }

        /* ── KPI Metrik Table ── */
        .kpi-grid-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        .kpi-grid-table td {
            width: 25%;
            padding: 7px 9px;
            background-color: #FFFFFF;
            border: 1px solid #CBD5E1;
            vertical-align: top;
        }
        .kpi-card-lbl {
            font-size: 6.8pt;
            font-weight: bold;
            color: #64748B;
            text-transform: uppercase;
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

        /* ── Data Tables (PM, CM, Assets) ── */
        .grid-data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            page-break-inside: auto;
        }
        .grid-data-table th {
            background-color: #1E293B;
            color: #FFFFFF;
            font-size: 6.8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 5px 6px;
            border: 1px solid #0F172A;
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

        /* ── Rekomendasi & Catatan Box ── */
        .notes-box {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-left: 3px solid #0284C7;
            padding: 7px 10px;
            margin-bottom: 12px;
            font-size: 7pt;
            line-height: 1.45;
            color: #334155;
        }

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
            padding: 0 10px;
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
            min-width: 145px;
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
                    <div class="header-company-sub">Managed Service &amp; Network Operations Division • SLA Operational Document</div>
                </td>
                <td style="width: 40%;" class="header-doc-meta">
                    <div><strong>No. Dok:</strong> {{ $docNumber }}</div>
                    <div><strong>Project ID:</strong> #{{ $project->id }} • {{ $printedAt }}</div>
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
                    {{-- DomPDF Script Penomoran Halaman Otomatis --}}
                    Dicetak: {{ $printedAt }}
                </td>
            </tr>
        </table>
    </footer>

    {{-- SCRIPT DOMPDF CANVAS UNTUK PENOMORAN HALAMAN PERSISI (HALAMAN X DARI Y) --}}
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman " . $PAGE_NUM . " dari " . $PAGE_COUNT;
            $font = $fontMetrics->get_font("Helvetica", "bold");
            $size = 6.8;
            $color = array(0.4, 0.45, 0.5);
            $w = $fontMetrics->getTextWidth($text, $font, $size);
            $x = 595.28 - 14 * 2.83465 - $w;
            $y = 841.89 - 11 * 2.83465;
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>

    {{-- KOP SURAT RESMI PERUSAHAAN (HALAMAN PERTAMA) --}}
    <div class="official-letterhead">
        <table class="letterhead-table">
            <tr>
                <td style="width: 25%; text-align: left;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" class="letterhead-logo" alt="IPNET Logo">
                    @else
                        <div class="corp-name">IPNET</div>
                    @endif
                </td>
                <td style="width: 75%; text-align: left; padding-left: 12px;">
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

    {{-- BOX JUDUL BERKAS / SURAT OPERASIONAL --}}
    <div class="doc-official-box">
        <table style="width: 100%; border-collapse: collapse; border: none;">
            <tr>
                <td style="border: none; padding: 0;">
                    <h1 class="doc-title-main">Laporan Kinerja SLA &amp; Operasional Layanan Managed Service</h1>
                    <div class="doc-title-sub">
                        Dokumen resmi rekapitulasi performa pemenuhan Service Level Agreement (SLA), siklus kunjungan Preventive Maintenance (PM), histori tiket penanganan Corrective Maintenance (CM), dan daftar inventaris aset jaringan klien.
                    </div>
                </td>
                <td style="border: none; padding: 0; width: 140px; text-align: right; vertical-align: top;">
                    <div style="font-size: 7pt; font-weight: bold; color: #8F0A0D; text-transform: uppercase;">Berkas Resmi Operasional</div>
                    <div style="font-size: 6.8pt; color: #475569; margin-top: 2px;">Klasifikasi: <strong>CONFIDENTIAL</strong></div>
                </td>
            </tr>
        </table>
    </div>

    {{-- SECTION 1: IDENTITAS PROYEK & CUSTOMER --}}
    <div class="section-header"><span class="section-num">[1]</span> Identitas Kontrak &amp; Informasi Customer</div>
    <table class="table-form">
        <tr>
            <td class="lbl-col">Nama Proyek</td>
            <td class="val-col"><strong>{{ $project->name }}</strong></td>
            <td class="lbl-col">Customer / Klien</td>
            <td class="val-col"><strong>{{ $project->client ?: '-' }}</strong></td>
        </tr>
        <tr>
            <td class="lbl-col">Tier SLA Kontrak</td>
            <td class="val-col">
                @php
                    $tierLower = strtolower($project->sla_tier ?? 'gold');
                    $tierBadge = match($tierLower) {
                        'platinum' => 'badge-platinum',
                        'silver'   => 'badge-silver',
                        'bronze'   => 'badge-bronze',
                        default    => 'badge-gold',
                    };
                @endphp
                <span class="badge {{ $tierBadge }}">{{ strtoupper($project->sla_tier ?: 'Gold') }} TIER</span>
                <span style="font-size: 6.8pt; color: #64748B; margin-left: 4px;">({{ $project->pm_frequency_label ?: 'Rutin Berkala' }})</span>
            </td>
            <td class="lbl-col">Periode Layanan</td>
            <td class="val-col">
                {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : '-' }} s/d 
                {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') : 'Kontrak Berjalan' }}
            </td>
        </tr>
        <tr>
            <td class="lbl-col">Target Availability</td>
            <td class="val-col"><strong>{{ number_format($targetSla, 2) }}%</strong></td>
            <td class="lbl-col">Target MTTR &amp; Coverage</td>
            <td class="val-col">{{ $project->mttr_hours ?: 4 }} Jam Respon • Coverage {{ $project->sla_coverage_hours ?: '24x7 Onsite' }}</td>
        </tr>
        <tr>
            <td class="lbl-col">Account Manager / Sales</td>
            <td class="val-col">{{ $project->sales_name ?: ($project->creator->name ?? 'Tim Komersial IPNET') }}</td>
            <td class="lbl-col">Lokasi Site / DC Utama</td>
            <td class="val-col">{{ $project->location ?: 'Site Customer / Kantor Pusat' }}</td>
        </tr>
    </table>

    {{-- SECTION 2: RINGKASAN EKSEKUTIF KINERJA & SLA --}}
    <div class="section-header"><span class="section-num">[2]</span> Ringkasan Eksekutif Pencapaian Layanan (KPI Summary)</div>
    <table class="kpi-grid-table">
        <tr>
            <td>
                <div class="kpi-card-lbl">Pencapaian SLA Availability</div>
                <div class="kpi-card-val" style="color: {{ $isSlaMet ? '#059669' : '#DC2626' }};">
                    {{ number_format($slaCompliancePercent, 1) }}%
                </div>
                <div class="kpi-card-sub">
                    Target: <strong>{{ number_format($targetSla, 2) }}%</strong> • Status: 
                    <strong style="color: {{ $isSlaMet ? '#059669' : '#DC2626' }};">{{ $isSlaMet ? 'TERPENUHI (NORMAL)' : 'PERHATIAN (BREACH)' }}</strong>
                </div>
            </td>
            <td>
                <div class="kpi-card-lbl">Siklus Kunjungan PM</div>
                <div class="kpi-card-val" style="color: #059669;">{{ $completedCycles }} / {{ $totalCycles }}</div>
                <div class="kpi-card-sub">Realisasi: <strong>{{ $pmPercent }}%</strong> Terlaksana Sesuai Jadwal</div>
            </td>
            <td>
                <div class="kpi-card-lbl">Tiket Insiden / Gangguan (CM)</div>
                <div class="kpi-card-val" style="color: {{ $activeCount > 0 ? '#D97706' : '#059669' }};">
                    {{ $activeCount }} <span style="font-size: 8pt; font-weight: normal; color: #64748B;">Aktif / {{ $closedCount }} Solved</span>
                </div>
                <div class="kpi-card-sub">Total {{ $totalTickets }} Tiket Gangguan Dilaporkan</div>
            </td>
            <td>
                <div class="kpi-card-lbl">Sisa Kuota Tiket CM</div>
                <div class="kpi-card-val" style="color: {{ $remainingQuota <= 2 ? '#DC2626' : '#1E293B' }};">
                    {{ $cmQuota >= 999 ? 'Unlimited' : $remainingQuota }}
                </div>
                <div class="kpi-card-sub">Alokasi Kontrak: {{ $cmQuota >= 999 ? 'Unlimited' : $cmQuota }} Tiket/Tahun</div>
            </td>
        </tr>
    </table>

    {{-- SECTION 3: TABEL SIKLUS PREVENTIVE MAINTENANCE (PM) --}}
    <div class="section-header"><span class="section-num">[3]</span> Siklus Pemeliharaan Berkala / Preventive Maintenance (PM)</div>
    <table class="grid-data-table">
        <thead>
            <tr>
                <th style="width: 38px; text-align: center;">Siklus</th>
                <th style="width: 68px;">Tgl Rencana</th>
                <th style="width: 68px;">Tgl Realisasi</th>
                <th style="width: 82px; text-align: center;">Status Kunjungan</th>
                <th style="width: 78px; text-align: center;">Status BAST / Lap.</th>
                <th style="width: 105px;">Teknisi / PIC Lapangan</th>
                <th>Scope Pekerjaan / Catatan Pemeriksaan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cycles as $c)
                @php
                    $actStatus = strtolower($c->activity_status ?? 'scheduled');
                    $isDone = in_array($actStatus, ['completed', 'solved']) || !empty($c->actual_date);
                    $engName = $c->assignedEngineer->name ?? ($c->leadEngineer->name ?? ($c->picMs->name ?? 'Tim Engineer IPNET'));
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold;">
                        #{{ $c->cycle_number }}
                    </td>
                    <td>
                        {{ $c->scheduled_date ? \Carbon\Carbon::parse($c->scheduled_date)->format('d/m/Y') : '-' }}
                    </td>
                    <td>
                        {{ $c->actual_date ? \Carbon\Carbon::parse($c->actual_date)->format('d/m/Y') : ($isDone ? 'Selesai' : 'Belum') }}
                    </td>
                    <td style="text-align: center;">
                        @if($isDone)
                            <span class="badge badge-success">Completed</span>
                        @elseif($actStatus === 'in_progress')
                            <span class="badge badge-warning">In Progress</span>
                        @else
                            <span class="badge badge-silver">Scheduled</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="badge badge-silver">{{ ucfirst($c->report_status ?: 'Draft') }}</span>
                    </td>
                    <td>
                        <strong style="font-size: 6.8pt; color: #1E293B;">{{ $engName }}</strong>
                    </td>
                    <td style="font-size: 6.8pt; color: #475569;">
                        {{ $c->activity_notes ?: ($c->cycle_label ?: 'Pemeriksaan fisik, kelistrikan UPS, suhu perangkat, backup configuration, dan log inspection') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 10px; color: #64748B;">
                        Belum ada jadwal siklus Preventive Maintenance terdaftar untuk proyek ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SECTION 4: TABEL CORRECTIVE MAINTENANCE (CM) --}}
    <div class="section-header"><span class="section-num">[4]</span> Histori Penanganan Gangguan / Corrective Maintenance (CM)</div>
    <table class="grid-data-table">
        <thead>
            <tr>
                <th style="width: 72px;">No. Tiket</th>
                <th style="width: 65px;">Waktu Lapor</th>
                <th>Judul Insiden &amp; Deskripsi Gangguan</th>
                <th style="width: 50px; text-align: center;">Prioritas</th>
                <th style="width: 52px; text-align: center;">MTTR (Jam)</th>
                <th style="width: 58px; text-align: center;">SLA Met</th>
                <th style="width: 62px; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $t)
                @php
                    $isResolved = in_array(strtolower($t->status ?? ''), ['resolved', 'closed', 'solved', 'completed', 'submitted', 'selesai', 'done', 'approved']) || !empty($t->resolved_at);
                    $prio = strtolower($t->priority ?? 'medium');
                    $badgePrio = match($prio) {
                        'critical', 'high' => 'badge-danger',
                        'medium'           => 'badge-warning',
                        default            => 'badge-silver',
                    };
                    $slaMet = ($t->sla_met === true || $t->sla_met === 1 || $t->sla_met === null);
                @endphp
                <tr>
                    <td><strong>{{ $t->ticket_number }}</strong></td>
                    <td>{{ $t->incident_date ? \Carbon\Carbon::parse($t->incident_date)->format('d/m/y H:i') : '-' }}</td>
                    <td>
                        <strong style="color: #0F172A; font-size: 7.2pt;">{{ $t->title }}</strong>
                        @if($t->root_cause)
                            <div style="font-size: 6.5pt; color: #64748B;"><strong>Penyebab:</strong> {{ $t->root_cause }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="badge {{ $badgePrio }}">{{ ucfirst($t->priority ?: 'Normal') }}</span>
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        {{ $t->duration_hours ? number_format($t->duration_hours, 1) : '-' }}
                    </td>
                    <td style="text-align: center;">
                        <span class="badge {{ $slaMet ? 'badge-success' : 'badge-danger' }}">
                            {{ $slaMet ? 'Terpenuhi' : 'Breach' }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge {{ $isResolved ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($t->status ?: 'Open') }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 10px; color: #059669; font-weight: bold;">
                        Zero Incident • Tidak ada laporan tiket gangguan selama periode pemantauan berlangsung (SLA Availability 100%).
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SECTION 5: INVENTARIS PERANGKAT CUSTOMER (ASSET INVENTORY YANG DI-COVER) --}}
    @if(isset($coveredAssets) && $coveredAssets->isNotEmpty())
    <div class="section-header"><span class="section-num">[5]</span> Daftar Inventaris Perangkat yang Dikelola (Covered CI Assets)</div>
    <table class="grid-data-table">
        <thead>
            <tr>
                <th style="width: 25px; text-align: center;">No</th>
                <th style="width: 60px;">Tipe</th>
                <th style="width: 65px;">Brand</th>
                <th>Nama Perangkat / Model</th>
                <th style="width: 85px;">Serial Number</th>
                <th style="width: 75px;">IP Address</th>
                <th style="width: 75px;">Support Service</th>
                <th style="width: 55px; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($coveredAssets as $idx => $asset)
            <tr>
                <td style="text-align: center;">{{ $idx + 1 }}</td>
                <td>{{ $asset->category ?: 'Switch' }}</td>
                <td>{{ $asset->brand ?: '-' }}</td>
                <td><strong>{{ $asset->device_name }}</strong></td>
                <td style="font-family: monospace; font-size: 6.5pt;">{{ $asset->serial_number ?: '-' }}</td>
                <td style="font-family: monospace; font-size: 6.5pt;">{{ $asset->ip_address ?: '-' }}</td>
                <td>
                    <span class="badge badge-silver">{{ $asset->service_support_type ?: 'PAR-ND' }}</span>
                </td>
                <td style="text-align: center;">
                    <span class="badge {{ strtolower($asset->status ?? 'online') === 'online' ? 'badge-success' : 'badge-warning' }}">
                        {{ $asset->status ?: 'Online' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- SECTION 6: CATATAN & REKOMENDASI TEKNIS OPERASIONAL --}}
    <div class="section-header"><span class="section-num">[{{ isset($coveredAssets) && $coveredAssets->isNotEmpty() ? '6' : '5' }}]</span> Catatan &amp; Rekomendasi Operasional Tim Managed Service</div>
    <div class="notes-box">
        <strong>Ringkasan Kepatuhan &amp; Rekomendasi Teknis:</strong><br>
        1. Pemeliharaan preventif (PM) berjalan sesuai siklus jadwal kontrak. Seluruh backup konfigurasi perangkat telah diamankan secara berkala pada storage aman.<br>
        2. Kepatuhan SLA Availability mencapai <strong>{{ number_format($slaCompliancePercent, 1) }}%</strong> dari target {{ number_format($targetSla, 2) }}%. Penanganan insiden dilakukan sesuai target MTTR {{ $project->mttr_hours ?: 4 }} jam.<br>
        3. Direkomendasikan melakukan review masa berlaku lisensi perangkat lunak dan kontrak service support vendor secara proaktif sebelum tanggal expired.
    </div>

    {{-- SECTION 7: LEMBAR PENGESAHAN DOKUMEN (3 PIHAK RESMI) --}}
    <div class="section-header"><span class="section-num">[{{ isset($coveredAssets) && $coveredAssets->isNotEmpty() ? '7' : '6' }}]</span> Lembar Pengesahan &amp; Verifikasi Dokumen</div>
    <table class="signature-section">
        <tr>
            <td>
                <div class="sign-title">Disiapkan &amp; Dilaporkan Oleh:</div>
                <div class="sign-line">{{ $printedBy }}</div>
                <div class="sign-dept">Lead Engineer / Delivery PIC<br>PT IP Network Solusindo</div>
            </td>
            <td>
                <div class="sign-title">Diperiksa &amp; Disetujui Oleh:</div>
                <div class="sign-line">Head of Operations &amp; MS</div>
                <div class="sign-dept">Service Delivery Manager<br>PT IP Network Solusindo</div>
            </td>
            <td>
                <div class="sign-title">Diterima &amp; Dikonfirmasi Oleh:</div>
                <div class="sign-line">{{ $project->client ?: 'Perwakilan Resmi Klien' }}</div>
                <div class="sign-dept">Authorized IT PIC / Representative<br>{{ $project->name }}</div>
            </td>
        </tr>
    </table>

</body>
</html>
