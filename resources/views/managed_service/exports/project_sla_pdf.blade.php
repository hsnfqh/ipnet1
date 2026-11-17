@php
    $targetSla = (float) ($targetSla ?? ($project->sla_target_percentage ?: 99.50));
    $slaCompliancePercent = (float) ($slaCompliancePercent ?? ($slaUptimeScore ?? 100.0));
    $isSlaMet = isset($isSlaMet) ? (bool) $isSlaMet : ($slaCompliancePercent >= 90.0);
    $totalCycles = (int) ($totalCycles ?? ($project->pm_total_cycles ?: 12));
    $completedCycles = (int) ($completedCycles ?? 0);
    $pmPercent = (int) ($pmPercent ?? ($totalCycles > 0 ? round(($completedCycles / $totalCycles) * 100) : 0));
    $totalTickets = (int) ($totalTickets ?? 0);
    $activeCount = (int) ($activeCount ?? ($activeTickets ?? 0));
    $closedCount = (int) ($closedCount ?? ($closedTickets ?? 0));
    $cmQuota = (int) ($cmQuota ?? ($project->cm_quota ?: 18));
    $remainingQuota = (int) ($remainingQuota ?? max(0, $cmQuota - $totalTickets));
    $cycles = $cycles ?? ($project->pmCycles ?: collect([]));
    $tickets = $tickets ?? ($project->managedServiceTickets ?: collect([]));
    $coveredAssets = $coveredAssets ?? collect([]);
    $docNumber = $docNumber ?? ('MS-SLA/' . date('Y/m/') . str_pad($project->id ?? 1, 4, '0', STR_PAD_LEFT));
    $printedAt = $printedAt ?? (date('d F Y H:i') . ' WIB');
    $printedBy = $printedBy ?? (auth()->user()->name ?? 'Administrator Managed Service');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja SLA &amp; Operasional - {{ $project->name }} - PT IP Network Solusindo</title>
    <style>
        /* ── Konfigurasi Halaman & Margin DomPDF Sesuai Standar Activity Log ── */
        @page {
            margin: 9mm 12mm 9mm 12mm;
            size: a4 portrait;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 7.2pt;
            color: #000000;
            line-height: 1.28;
            margin: 0;
            padding: 0;
        }

        /* ── Header Dokumen Formal ── */
        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 0;
            margin-bottom: 3px;
        }
        .doc-header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .doc-logo {
            max-width: 95px;
            max-height: 40px;
            display: block;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
            line-height: 1.15;
        }
        .doc-sub-corp {
            font-size: 7pt;
            font-weight: bold;
            color: #8F0A0D;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: center;
            margin-top: 2px;
        }
        .doc-intro {
            font-size: 6.8pt;
            color: #1E293B;
            margin-top: 3px;
            margin-bottom: 4px;
            line-height: 1.25;
            text-align: justify;
        }
        .doc-header-line {
            width: 100%;
            height: 0;
            border-bottom: 1.5px solid #000000;
            margin-top: 3px;
            margin-bottom: 6px;
            clear: both;
        }

        /* ── Judul Section Formal ── */
        .section-title {
            font-size: 7.8pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            margin-top: 6px;
            margin-bottom: 2.5px;
            letter-spacing: 0.2px;
        }

        /* ── Tabel Standar Resmi (Sesuai Activity Log) ── */
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
            font-size: 6.8pt;
            padding: 3px 3.5px;
            border: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
            text-transform: uppercase;
        }
        .report-table td {
            font-size: 6.8pt;
            padding: 2.5px 4px;
            border: 1px solid #000000;
            vertical-align: middle;
            color: #000000;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.22;
        }
        .report-table td.label-cell {
            font-weight: bold;
            background-color: #F8FAFC;
        }
        .text-center { text-align: center; }
        .text-left   { text-align: left; }
        .text-right  { text-align: right; }
        .align-top   { vertical-align: top !important; }
        .report-table tr {
            page-break-inside: avoid;
        }

        /* ── Checkbox / Badge Kotak Formal ── */
        .badge-box {
            display: inline-block;
            font-size: 6.2pt;
            font-weight: bold;
            padding: 1px 4px;
            border: 1px solid #000000;
            background-color: #FFFFFF;
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

        /* ── Kotak Pengesahan / Tanda Tangan Formal ── */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000000;
            margin-top: 6px;
            page-break-inside: avoid;
        }
        .sig-table th {
            background-color: #EBF3FB;
            font-size: 6.8pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            padding: 3px;
            border: 1px solid #000000;
            text-align: center;
            width: 33.33%;
        }
        .sig-table td {
            border: 1px solid #000000;
            padding: 4px;
            text-align: center;
            vertical-align: top;
            width: 33.33%;
        }
        .sig-space {
            height: 48px;
        }
        .sig-name {
            font-weight: bold;
            text-decoration: underline;
            font-size: 7.2pt;
            color: #000000;
        }
        .sig-role {
            font-size: 6.2pt;
            color: #334155;
            margin-top: 1px;
            line-height: 1.2;
        }

        /* ── Footer Dokumen ── */
        .doc-footer-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 8px;
            padding-top: 3px;
            border-top: 1px solid #000000;
        }
        .doc-footer-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
            font-size: 6pt;
            color: #000000;
            line-height: 1.2;
        }
    </style>
</head>
<body>

    {{-- SCRIPT DOMPDF CANVAS PENOMORAN HALAMAN PERSISI (HALAMAN X DARI Y) --}}
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman " . $PAGE_NUM . " dari " . $PAGE_COUNT;
            $font = $fontMetrics->get_font("Helvetica", "normal");
            $size = 6.2;
            $color = array(0.1, 0.1, 0.1);
            $w = $fontMetrics->getTextWidth($text, $font, $size);
            $x = 595.28 - 12 * 2.83465 - $w;
            $y = 841.89 - 6 * 2.83465;
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>

    {{-- HEADER DOKUMEN RESMI (LOGO + JUDUL FORMAL) --}}
    <table class="doc-header-table">
        <tr>
            <td style="width: 16%; text-align: left; vertical-align: middle;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" class="doc-logo" alt="IPNET Logo">
                @else
                    <div style="font-size: 14pt; font-weight: bold; color: #8F0A0D;">IPNET</div>
                @endif
            </td>
            <td style="width: 60%; text-align: center; vertical-align: middle;">
                <div class="doc-title">LAPORAN KINERJA SLA &amp; OPERASIONAL MANAGED SERVICE</div>
                <div class="doc-sub-corp">PT IP NETWORK SOLUSINDO – MANAGED SERVICE &amp; NETWORK OPERATIONS DIVISION</div>
            </td>
            <td style="width: 24%; text-align: right; vertical-align: middle; font-size: 6.5pt; line-height: 1.3;">
                <div><strong>No. Laporan:</strong> {{ $docNumber }}</div>
                <div><strong>Tanggal:</strong> {{ $printedAt }}</div>
                <div><strong>PIC:</strong> {{ $printedBy }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-intro">
        Dokumen resmi pemantauan performa pemenuhan Service Level Agreement (SLA), siklus kunjungan berkala Preventive Maintenance (PM), histori tiket penanganan gangguan Corrective Maintenance (CM), serta status operasional aset perangkat jaringan klien.
    </div>
    <div class="doc-header-line"></div>

    {{-- A. IDENTITAS KONTRAK & CUSTOMER --}}
    <div class="section-title">A. IDENTITAS KONTRAK &amp; INFORMASI CUSTOMER</div>
    <table class="report-table">
        <tr>
            <td class="label-cell" style="width: 18%;">Nama Proyek</td>
            <td style="width: 32%;"><strong>{{ $project->name }}</strong></td>
            <td class="label-cell" style="width: 18%;">Customer / Klien</td>
            <td style="width: 32%;"><strong>{{ $project->client ?: '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label-cell">Tier SLA Kontrak</td>
            <td>
                <strong>{{ strtoupper($project->sla_tier ?: 'Gold') }} TIER</strong> 
                <span style="font-size: 6pt; color: #475569;">({{ $project->pm_frequency_label ?: 'Rutin Berkala' }})</span>
            </td>
            <td class="label-cell">Periode Layanan</td>
            <td>
                {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : '-' }} s/d 
                {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') : 'Kontrak Berjalan' }}
            </td>
        </tr>
        <tr>
            <td class="label-cell">Target Availability</td>
            <td><strong>{{ number_format($targetSla, 2) }}%</strong></td>
            <td class="label-cell">Target MTTR &amp; Coverage</td>
            <td>{{ $project->mttr_hours ?: 4 }} Jam Respon • Coverage {{ $project->sla_coverage_hours ?: '24x7 Onsite' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Account Manager / Sales</td>
            <td>{{ $project->sales_name ?: ($project->creator->name ?? 'Tim Komersial IPNET') }}</td>
            <td class="label-cell">Lokasi Site / DC Utama</td>
            <td>{{ $project->location ?: 'Site Customer / Kantor Pusat' }}</td>
        </tr>
    </table>

    {{-- B. RINGKASAN EKSEKUTIF PENCAPAIAN LAYANAN & SLA --}}
    <div class="section-title">B. RINGKASAN EKSEKUTIF PENCAPAIAN LAYANAN &amp; KEPATUHAN SLA</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 25%;">Pencapaian SLA Availability</th>
                <th style="width: 25%;">Siklus Preventive Maintenance</th>
                <th style="width: 25%;">Tiket Insiden / Gangguan (CM)</th>
                <th style="width: 25%;">Alokasi Kuota Tiket CM</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="padding: 5px;">
                    <div style="font-size: 13pt; font-weight: bold; color: {{ $isSlaMet ? '#137333' : '#C5221F' }};">
                        {{ number_format($slaCompliancePercent, 1) }}%
                    </div>
                    <div style="font-size: 6.2pt; color: #334155; margin-top: 1px;">
                        Target: {{ number_format($targetSla, 2) }}% &bull; 
                        <strong style="color: {{ $isSlaMet ? '#137333' : '#C5221F' }};">{{ $isSlaMet ? 'TERPENUHI' : 'PERHATIAN' }}</strong>
                    </div>
                </td>
                <td class="text-center" style="padding: 5px;">
                    <div style="font-size: 13pt; font-weight: bold; color: #137333;">
                        {{ $completedCycles }} / {{ $totalCycles }}
                    </div>
                    <div style="font-size: 6.2pt; color: #334155; margin-top: 1px;">
                        Realisasi Kunjungan: <strong>{{ $pmPercent }}%</strong> Terlaksana
                    </div>
                </td>
                <td class="text-center" style="padding: 5px;">
                    <div style="font-size: 13pt; font-weight: bold; color: {{ $activeCount > 0 ? '#B06000' : '#137333' }};">
                        {{ $activeCount }} <span style="font-size: 7.5pt; font-weight: normal;">Aktif</span> / {{ $closedCount }} <span style="font-size: 7.5pt; font-weight: normal;">Solved</span>
                    </div>
                    <div style="font-size: 6.2pt; color: #334155; margin-top: 1px;">
                        Total {{ $totalTickets }} Tiket Gangguan Masuk
                    </div>
                </td>
                <td class="text-center" style="padding: 5px;">
                    <div style="font-size: 13pt; font-weight: bold; color: #000000;">
                        {{ $cmQuota >= 999 ? 'Unlimited' : $remainingQuota }}
                    </div>
                    <div style="font-size: 6.2pt; color: #334155; margin-top: 1px;">
                        Alokasi: {{ $cmQuota >= 999 ? 'Unlimited' : $cmQuota }} Tiket/Tahun
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- C. SIKLUS PREVENTIVE MAINTENANCE (PM) --}}
    <div class="section-title">C. SIKLUS PEMELIHARAAN PREVENTIF / PREVENTIVE MAINTENANCE (PM)</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 32px;">Siklus</th>
                <th style="width: 62px;">Tgl Rencana</th>
                <th style="width: 62px;">Tgl Realisasi</th>
                <th style="width: 78px;">Status Kunjungan</th>
                <th style="width: 72px;">Status Laporan</th>
                <th style="width: 95px;">Teknisi / PIC IPNET</th>
                <th>Scope Pekerjaan &amp; Catatan Pemeriksaan</th>
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
                    <td class="text-center"><strong>#{{ $c->cycle_number }}</strong></td>
                    <td class="text-center">{{ $c->scheduled_date ? \Carbon\Carbon::parse($c->scheduled_date)->format('d/m/Y') : '-' }}</td>
                    <td class="text-center">{{ $c->actual_date ? \Carbon\Carbon::parse($c->actual_date)->format('d/m/Y') : ($isDone ? 'Selesai' : 'Belum') }}</td>
                    <td class="text-center">
                        @if($isDone)
                            <span class="badge-box badge-box-success">COMPLETED</span>
                        @elseif($actStatus === 'in_progress')
                            <span class="badge-box badge-box-warning">IN PROGRESS</span>
                        @else
                            <span class="badge-box">SCHEDULED</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge-box">{{ strtoupper($c->report_status ?: 'Draft') }}</span>
                    </td>
                    <td><strong>{{ $engName }}</strong></td>
                    <td>{{ $c->activity_notes ?: ($c->cycle_label ?: 'Pemeriksaan fisik, pembersihan, cek kelistrikan & suhu, backup konfigurasi, log analysis') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 8px; color: #64748B;">
                        Belum ada jadwal siklus Preventive Maintenance terdaftar untuk proyek ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- D. HISTORI TIKET CORRECTIVE MAINTENANCE (CM) --}}
    <div class="section-title">D. HISTORI PENANGANAN GANGGUAN / CORRECTIVE MAINTENANCE (CM)</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 65px;">No. Tiket</th>
                <th style="width: 58px;">Waktu Lapor</th>
                <th>Judul Insiden &amp; Deskripsi Masalah</th>
                <th style="width: 45px;">Prioritas</th>
                <th style="width: 48px;">MTTR (Jam)</th>
                <th style="width: 55px;">SLA Met</th>
                <th style="width: 58px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $t)
                @php
                    $isResolved = in_array(strtolower($t->status ?? ''), ['resolved', 'closed', 'solved', 'completed', 'submitted', 'selesai', 'done', 'approved']) || !empty($t->resolved_at);
                    $prio = strtolower($t->priority ?? 'medium');
                    $slaMet = ($t->sla_met === true || $t->sla_met === 1 || $t->sla_met === null);
                @endphp
                <tr>
                    <td><strong>{{ $t->ticket_number }}</strong></td>
                    <td class="text-center">{{ $t->incident_date ? \Carbon\Carbon::parse($t->incident_date)->format('d/m/y H:i') : '-' }}</td>
                    <td>
                        <strong>{{ $t->title }}</strong>
                        @if($t->root_cause)
                            <div style="font-size: 6pt; color: #475569;">Penyebab: {{ $t->root_cause }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge-box {{ $prio === 'critical' || $prio === 'high' ? 'badge-box-danger' : ($prio === 'medium' ? 'badge-box-warning' : '') }}">
                            {{ strtoupper($t->priority ?: 'Normal') }}
                        </span>
                    </td>
                    <td class="text-center font-bold">
                        {{ $t->duration_hours ? number_format($t->duration_hours, 1) : '-' }}
                    </td>
                    <td class="text-center">
                        <span class="badge-box {{ $slaMet ? 'badge-box-success' : 'badge-box-danger' }}">
                            {{ $slaMet ? 'TERPENUHI' : 'BREACH' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge-box {{ $isResolved ? 'badge-box-success' : 'badge-box-warning' }}">
                            {{ strtoupper($t->status ?: 'Open') }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 8px; color: #137333; font-weight: bold;">
                        Zero Incident &bull; Tidak ada tiket insiden / gangguan selama periode pemantauan (SLA Availability 100%).
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- E. DAFTAR INVENTARIS PERANGKAT YANG DI-COVER --}}
    @if(isset($coveredAssets) && $coveredAssets->isNotEmpty())
    <div class="section-title">E. INVENTARIS PERANGKAT YANG DIKELOLA (COVERED CI ASSETS)</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 55px;">Tipe</th>
                <th style="width: 60px;">Brand</th>
                <th>Nama Perangkat / Model</th>
                <th style="width: 80px;">Serial Number</th>
                <th style="width: 70px;">IP Address</th>
                <th style="width: 70px;">Service Support</th>
                <th style="width: 50px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($coveredAssets as $idx => $asset)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ $asset->category ?: 'Switch' }}</td>
                <td>{{ $asset->brand ?: '-' }}</td>
                <td><strong>{{ $asset->device_name }}</strong></td>
                <td style="font-family: monospace; font-size: 6.2pt;">{{ $asset->serial_number ?: '-' }}</td>
                <td style="font-family: monospace; font-size: 6.2pt;">{{ $asset->ip_address ?: '-' }}</td>
                <td class="text-center"><strong>{{ $asset->service_support_type ?: 'PAR-ND' }}</strong></td>
                <td class="text-center">
                    <span class="badge-box {{ strtolower($asset->status ?? 'online') === 'online' ? 'badge-box-success' : 'badge-box-warning' }}">
                        {{ strtoupper($asset->status ?: 'Online') }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- F. CATATAN & REKOMENDASI TEKNIS OPERASIONAL --}}
    <div class="section-title">{{ isset($coveredAssets) && $coveredAssets->isNotEmpty() ? 'F' : 'E' }}. CATATAN &amp; REKOMENDASI TEKNIS OPERASIONAL</div>
    <table class="report-table">
        <tr>
            <td style="background-color: #FFFFFF; padding: 5px; line-height: 1.35;">
                1. Kinerja ketersediaan jaringan (Availability SLA) mencapai <strong>{{ number_format($slaCompliancePercent, 1) }}%</strong> (Target: {{ number_format($targetSla, 2) }}%), respon penanganan insiden memenuhi standar MTTR {{ $project->mttr_hours ?: 4 }} jam.<br>
                2. Pemeliharaan berkala (Preventive Maintenance) terlaksana sesuai jadwal. File backup konfigurasi dan checklist operasional tersimpan pada repositori FSM.<br>
                3. Disarankan melakukan review status siklus hidup perangkat (EOL/EOS) serta perpanjangan kontrak dukungan vendor sebelum tanggal expired support.
            </td>
        </tr>
    </table>

    {{-- G. LEMBAR PENGESAHAN DOKUMEN (3 PIHAK FORMAL) --}}
    <div class="section-title">{{ isset($coveredAssets) && $coveredAssets->isNotEmpty() ? 'G' : 'F' }}. LEMBAR PENGESAHAN DOKUMEN</div>
    <table class="sig-table">
        <thead>
            <tr>
                <th>Disiapkan Oleh:</th>
                <th>Diperiksa &amp; Diverifikasi:</th>
                <th>Diterima &amp; Dikonfirmasi:</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $printedBy }}</div>
                    <div class="sig-role">Lead Engineer / Delivery PIC<br>PT IP Network Solusindo</div>
                </td>
                <td>
                    <div class="sig-space"></div>
                    <div class="sig-name">Head of Managed Service</div>
                    <div class="sig-role">Service Delivery Manager<br>PT IP Network Solusindo</div>
                </td>
                <td>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $project->client ?: 'PIC Customer / Representative' }}</div>
                    <div class="sig-role">Customer IT PIC / Representative<br>{{ $project->name }}</div>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- FOOTER BAWAH HALAMAN --}}
    <table class="doc-footer-table">
        <tr>
            <td style="width: 70%;">
                PT IP Network Solusindo &bull; Dokumen Resmi Sistem Field System Management (FSM) &bull; Rahasia &amp; Terbatas
            </td>
            <td style="width: 30%; text-align: right;">
                Dicetak: {{ $printedAt }}
            </td>
        </tr>
    </table>

</body>
</html>
