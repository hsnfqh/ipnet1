<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kinerja SLA &amp; Operasional - {{ $project->name }} - PT IP Network Solusindo</title>
    <style>
        @page {
            margin: 16mm 12mm 16mm 12mm;
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
            max-width: 145px;
            max-height: 44px;
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
            padding: 7px 10px;
            margin-bottom: 10px;
        }
        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin: 0;
        }
        .doc-meta {
            font-size: 7pt;
            color: #64748B;
            margin-top: 2px;
        }

        /* ── Section Titles ── */
        .section-heading {
            font-size: 8.5pt;
            font-weight: bold;
            color: #0F172A;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-top: 10px;
            margin-bottom: 4px;
            padding-bottom: 2px;
            border-bottom: 1.5px solid #E2E8F0;
        }
        .section-tag {
            color: #8F0A0D;
            margin-right: 4px;
        }

        /* ── Info Identitas Table ── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 1px solid #CBD5E1;
        }
        .info-table td {
            font-size: 7.2pt;
            padding: 4px 6px;
            border: 1px solid #CBD5E1;
            vertical-align: middle;
        }
        .info-label {
            background-color: #F8FAFC;
            font-weight: bold;
            color: #475569;
            width: 18%;
        }
        .info-value {
            color: #0F172A;
            width: 32%;
        }

        /* ── KPI Summary Cards ── */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .kpi-table td {
            width: 25%;
            padding: 6px 8px;
            background-color: #FFFFFF;
            border: 1px solid #CBD5E1;
            vertical-align: top;
        }
        .kpi-label {
            font-size: 6.8pt;
            font-weight: bold;
            color: #64748B;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .kpi-value {
            font-size: 12pt;
            font-weight: bold;
            color: #0F172A;
            line-height: 1.1;
        }
        .kpi-desc {
            font-size: 6.8pt;
            color: #64748B;
            margin-top: 2px;
        }

        /* ── Data Tables ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            page-break-inside: auto;
        }
        .data-table th {
            background-color: #1E293B;
            color: #FFFFFF;
            font-size: 6.8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 4.5px 5px;
            border: 1px solid #0F172A;
            text-align: left;
            vertical-align: middle;
        }
        .data-table td {
            font-size: 7pt;
            padding: 4px 5px;
            border: 1px solid #E2E8F0;
            vertical-align: middle;
        }
        .data-table tr:nth-child(even) td {
            background-color: #F8FAFC;
        }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            padding: 1px 4px;
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

        /* ── Signatures ── */
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 14px;
            page-break-inside: avoid;
        }
        .signature-table td {
            border: none;
            width: 50%;
            vertical-align: top;
            text-align: center;
            padding: 0 20px;
        }
        .sig-title {
            font-size: 7pt;
            font-weight: bold;
            color: #64748B;
            text-transform: uppercase;
            margin-bottom: 42px;
        }
        .sig-name {
            font-size: 8pt;
            font-weight: bold;
            color: #0F172A;
            border-bottom: 1px solid #CBD5E1;
            padding-bottom: 2px;
            display: inline-block;
            min-width: 170px;
        }
        .sig-role {
            font-size: 6.8pt;
            color: #64748B;
            margin-top: 2px;
        }

        /* ── Page Footer ── */
        .page-footer {
            margin-top: 12px;
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
            <td style="width: 55%;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" class="doc-logo" alt="IPNET Logo">
                @else
                    <div class="company-title">PT IP NETWORK SOLUSINDO</div>
                @endif
                <div class="company-subtitle">Managed Service &amp; Network Operations Division • Project Service Level Agreement</div>
            </td>
            <td style="width: 45%; text-align: right; font-size: 7pt; color: #475569;">
                <div><strong>No. Dokumen:</strong> {{ $docNumber }}</div>
                <div><strong>Tanggal Cetak:</strong> {{ $printedAt }}</div>
                <div><strong>Dicetak Oleh:</strong> {{ $printedBy }}</div>
            </td>
        </tr>
    </table>

    {{-- 2. Title Banner --}}
    <div class="title-banner">
        <h1 class="doc-title">Laporan Kinerja SLA &amp; Operasional Layanan Managed Service</h1>
        <div class="doc-meta">
            Dokumen resmi rekapitulasi performa pemenuhan SLA, siklus kunjungan Preventive Maintenance (PM), dan histori tiket penanganan Corrective Maintenance (CM).
        </div>
    </div>

    {{-- 3. Identitas Proyek & Customer --}}
    <div class="section-heading"><span class="section-tag">[1]</span> Identitas Proyek &amp; Informasi Customer</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Proyek</td>
            <td class="info-value"><strong>{{ $project->name }}</strong></td>
            <td class="info-label">Customer / Klien</td>
            <td class="info-value"><strong>{{ $project->client ?: '-' }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Tier SLA Kontrak</td>
            <td class="info-value">
                <span class="badge badge-gold">{{ $project->sla_tier ?: 'Gold Tier' }}</span>
                <span style="font-size: 6.8pt; color: #64748B; margin-left: 4px;">({{ $project->pm_frequency_label ?: 'Rutin Berkala' }})</span>
            </td>
            <td class="info-label">Periode Kontrak</td>
            <td class="info-value">
                {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : '-' }} s/d 
                {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') : 'Kontrak Berjalan' }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Target Availability</td>
            <td class="info-value"><strong>{{ number_format($targetSla, 2) }}%</strong></td>
            <td class="info-label">Target MTTR &amp; Coverage</td>
            <td class="info-value">{{ $project->mttr_hours ?: 4 }} Jam • {{ $project->sla_coverage_hours ?: '24x7' }}</td>
        </tr>
        <tr>
            <td class="info-label">Sales / Account Mgr</td>
            <td class="info-value">{{ $project->sales_name ?: ($project->creator->name ?? '-') }}</td>
            <td class="info-label">Lokasi / Site</td>
            <td class="info-value">{{ $project->location ?: 'Site Customer' }}</td>
        </tr>
    </table>

    {{-- 4. Ringkasan Kinerja Eksekutif --}}
    <div class="section-heading"><span class="section-tag">[2]</span> Ringkasan Eksekutif Kinerja &amp; SLA</div>
    <table class="kpi-table">
        <tr>
            <td>
                <div class="kpi-label">Pencapaian SLA</div>
                <div class="kpi-value" style="color: {{ $isSlaMet ? '#059669' : '#DC2626' }};">
                    {{ number_format($slaCompliancePercent, 1) }}%
                </div>
                <div class="kpi-desc">Target: <strong>{{ number_format($targetSla, 2) }}%</strong> ({{ $isSlaMet ? 'Terpenuhi' : 'Perhatian' }})</div>
            </td>
            <td>
                <div class="kpi-label">Siklus Kunjungan PM</div>
                <div class="kpi-value" style="color: #059669;">{{ $completedCycles }} / {{ $totalCycles }}</div>
                <div class="kpi-desc">Realisasi: <strong>{{ $pmPercent }}%</strong> Terlaksana</div>
            </td>
            <td>
                <div class="kpi-label">Tiket Insiden (CM)</div>
                <div class="kpi-value" style="color: {{ $activeCount > 0 ? '#D97706' : '#059669' }};">
                    {{ $activeCount }} <span style="font-size: 8pt; font-weight: normal; color: #64748B;">Aktif / {{ $closedCount }} Selesai</span>
                </div>
                <div class="kpi-desc">Total {{ $totalTickets }} Tiket Dilaporkan</div>
            </td>
            <td>
                <div class="kpi-label">Sisa Kuota Tiket CM</div>
                <div class="kpi-value" style="color: {{ $remainingQuota <= 2 ? '#DC2626' : '#1E293B' }};">
                    {{ $cmQuota >= 999 ? 'Unltd' : $remainingQuota }}
                </div>
                <div class="kpi-desc">Dari Kuota {{ $cmQuota >= 999 ? 'Unlimited' : $cmQuota }} Tiket/Tahun</div>
            </td>
        </tr>
    </table>

    {{-- 5. Tabel Siklus Preventive Maintenance (PM) --}}
    <div class="section-heading"><span class="section-tag">[3]</span> Siklus Preventive Maintenance (PM)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 45px; text-align: center;">Siklus</th>
                <th style="width: 70px;">Tgl Rencana</th>
                <th style="width: 70px;">Tgl Realisasi</th>
                <th style="width: 90px; text-align: center;">Status Kunjungan</th>
                <th style="width: 80px; text-align: center;">Status Laporan</th>
                <th style="width: 110px;">Teknisi / PIC</th>
                <th>Catatan Pemeriksaan / Scope Pekerjaan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cycles as $c)
                @php
                    $actStatus = strtolower($c->activity_status ?? 'scheduled');
                    $isDone = in_array($actStatus, ['completed', 'solved']) || !empty($c->actual_date);
                    $engName = $c->assignedEngineer->name ?? ($c->leadEngineer->name ?? ($c->picMs->name ?? '-'));
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
                        {{ $c->activity_notes ?: ($c->cycle_label ?: 'Pemeliharaan rutin perangkat & kelistrikan') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 12px; color: #64748B;">
                        Belum ada jadwal siklus Preventive Maintenance yang dibuat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 6. Tabel Corrective Maintenance (CM) / Tiket Insiden --}}
    <div class="section-heading"><span class="section-tag">[4]</span> Riwayat Corrective Maintenance (CM) / Tiket Gangguan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 75px;">No. Tiket</th>
                <th style="width: 65px;">Waktu Lapor</th>
                <th>Judul Insiden &amp; Deskripsi Masalah</th>
                <th style="width: 50px; text-align: center;">Prioritas</th>
                <th style="width: 55px; text-align: center;">MTTR (Jam)</th>
                <th style="width: 60px; text-align: center;">SLA Met</th>
                <th style="width: 65px; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $t)
                @php
                    $isResolved = in_array(strtolower($t->status ?? ''), ['resolved', 'closed', 'solved', 'completed', 'done', 'approved']) || !empty($t->resolved_at);
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
                            <div style="font-size: 6.5pt; color: #64748B;">Root cause: {{ $t->root_cause }}</div>
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
                    <td colspan="7" style="text-align: center; padding: 12px; color: #059669; font-weight: bold;">
                        Tidak ada tiket gangguan/insiden yang tercatat (Zero Incident - SLA 100%).
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 7. Lembar Pengesahan --}}
    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-title">Disiapkan &amp; Dilaporkan Oleh:</div>
                <div class="sig-name">{{ $printedBy }}</div>
                <div class="sig-role">Managed Service Engineer / Delivery Lead<br>PT IP Network Solusindo</div>
            </td>
            <td>
                <div class="sig-title">Diterima &amp; Diverifikasi Oleh:</div>
                <div class="sig-name">{{ $project->client ?: 'Perwakilan Customer' }}</div>
                <div class="sig-role">Customer Representative / IT PIC<br>{{ $project->name }}</div>
            </td>
        </tr>
    </table>

    {{-- 8. Footer --}}
    <div class="page-footer">
        <div class="footer-left">
            PT IP Network Solusindo • Dokumen Resmi Hasil Cetak Sistem ERP IPNET • Rahasia
        </div>
        <div class="footer-right">
            Dicetak: {{ $printedAt }} • Hal 1/1
        </div>
    </div>

</body>
</html>
