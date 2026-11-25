<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengendalian Proyek & Keuangan - {{ $project->name }}</title>
    <style>
        @page {
            margin: 10mm 12mm 18mm 12mm;
            size: a4 portrait;
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
        .page-break {
            page-break-after: always;
        }

        /* ── Header Dokumen ── */
        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 0;
            margin-bottom: 6px;
        }
        .doc-header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .doc-title {
            font-size: 12pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: center;
        }
        .doc-subtitle {
            font-size: 8pt;
            font-weight: bold;
            color: #8F0A0D;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
            margin-top: 2px;
        }
        .doc-intro {
            font-size: 6.8pt;
            color: #334155;
            margin-top: 6px;
            margin-bottom: 8px;
            line-height: 1.35;
            text-align: justify;
        }
        .doc-header-line {
            width: 100%;
            height: 0;
            border-bottom: 1.5px solid #000000;
            margin-top: 6px;
            margin-bottom: 14px;
            clear: both;
        }

        /* ── Spasi Antar Point Section (A, B, C, D) ── */
        .section-spacer {
            height: 13px;
            line-height: 13px;
            font-size: 1px;
            clear: both;
        }

        /* ── Judul Section ── */
        .section-title {
            font-size: 7.8pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            margin-top: 0;
            margin-bottom: 6px;
            letter-spacing: 0.2px;
            background-color: #F1F5F9;
            padding: 3px 6px;
            border-left: 3px solid #8F0A0D;
            clear: both;
        }

        /* ── Tabel Standar ── */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
            margin-bottom: 6px;
            table-layout: fixed;
        }
        .report-table th {
            background-color: #EBF3FB;
            color: #000000;
            font-weight: bold;
            font-size: 6.7pt;
            padding: 3px 3px;
            border: 1px solid #000000;
            text-align: center;
            vertical-align: middle;
        }
        .report-table td {
            font-size: 6.7pt;
            padding: 2.5px 3.5px;
            border: 1px solid #000000;
            vertical-align: middle;
            color: #000000;
            word-wrap: break-word;
            overflow-wrap: break-word;
            line-height: 1.2;
        }
        .report-table td.label-cell {
            font-weight: bold;
            background-color: #F8FAFC;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .report-table tr {
            page-break-inside: avoid;
        }
        .keep-together {
            page-break-inside: avoid;
        }

        /* ── Status Badges ── */
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 6pt;
            font-weight: bold;
            text-align: center;
        }
        .badge-success { background-color: #DCFCE7; color: #166534; border: 0.5px solid #166534; }
        .badge-warning { background-color: #FEF3C7; color: #92400E; border: 0.5px solid #92400E; }
        .badge-danger  { background-color: #FEE2E2; color: #991B1B; border: 0.5px solid #991B1B; }
        .badge-info    { background-color: #E0E7FF; color: #3730A3; border: 0.5px solid #3730A3; }

        /* ── Footer Dokumen Resmi (Fixed di Paling Bawah Setiap Halaman) ── */
        .doc-footer {
            position: fixed;
            bottom: -14mm;
            left: 0;
            right: 0;
            height: 11mm;
            border-top: 1px solid #000000;
            padding-top: 3px;
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
            font-size: 6.2pt;
            color: #334155;
            line-height: 1.25;
        }

        /* ── Kotak Tanda Tangan ── */
        .sig-container {
            height: 45px;
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>
<body>

    {{-- FOOTER DOKUMEN RESMI (Fixed di Paling Bawah Setiap Halaman) --}}
    <div class="doc-footer">
        <table class="footer-table">
            <tr>
                <td style="width: 48%;">
                    <strong style="color: #000000;">PT IP Network Solusindo</strong> — Field System Management (FSM)<br>
                    No. Dokumen: <strong style="color: #000000;">{{ $docNumber }}</strong>
                </td>
                <td style="width: 52%; text-align: right;">
                    Dicetak pada: {{ $printedAt }} | Oleh: {{ $printedBy }}<br>
                    <em>Laporan Resmi PMO: {{ $docSubtitle ?? 'S-Curve Progress & Financial Expenses' }}</em>
                </td>
            </tr>
        </table>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- HALAMAN 1: IDENTITAS, S-CURVE & WBS DELIVERABLES SCHEDULE --}}
    {{-- ══════════════════════════════════════════════════════════ --}}

    {{-- KOP SURAT / DOKUMEN HEADER --}}
    <table class="doc-header-table">
        <tr>
            <td style="width: 18%; text-align: left; vertical-align: middle;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo IPNET" style="height: 38px; width: auto; max-width: 90px; display: block;">
                @else
                    <span style="font-weight: bold; font-size: 11pt; color: #8F0A0D;">PT IP NETWORK SOLUSINDO</span>
                @endif
            </td>
            <td style="width: 82%; text-align: center; vertical-align: middle;">
                <div class="doc-title">{{ $docTitle ?? (!empty($hideExpenses) ? 'LAPORAN KEMAJUAN PROYEK' : 'LAPORAN PENGENDALIAN PROYEK & KEUANGAN') }}</div>
                <div class="doc-subtitle">{{ $docSubtitle ?? (!empty($hideExpenses) ? 'S-CURVE PROGRESS & WBS DELIVERABLES SCHEDULE' : 'S-CURVE PROGRESS, WBS DELIVERABLES & EXPENSES TRACKING') }}</div>
            </td>
        </tr>
    </table>

    <div class="doc-intro">
        {{ $docIntro ?? (!empty($hideExpenses) ? 'Dokumen resmi laporan kemajuan teknis pelaksanaan proyek PT IP Network Solusindo. Mencakup capaian progres fisik mingguan (S-Curve & Actual Cumulative) serta rincian pencapaian jadwal WBS deliverables.' : 'Dokumen resmi laporan pengendalian teknis dan keuangan proyek PT IP Network Solusindo. Mencakup capaian progres fisik mingguan (S-Curve & Actual Cumulative), rincian WBS deliverables, serta realisasi pengeluaran operasional proyek.') }}
    </div>
    <div class="doc-header-line"></div>

    {{-- SECTION A: IDENTITAS & INFORMASI PROYEK --}}
    <div class="section-title">A. INFORMASI &amp; IDENTITAS PROYEK</div>
    <table class="report-table">
        <tr>
            <td class="label-cell" style="width: 18%;">Nama Proyek</td>
            <td colspan="3" class="font-bold" style="color: #0F172A;">{{ $project->name }}</td>
        </tr>
        <tr>
            <td class="label-cell" style="width: 18%;">Klien / Customer</td>
            <td style="width: 32%;">
                <span class="font-bold" style="color: #0F172A;">{{ $project->client ?: '-' }}</span>
                @if(!empty($customerPic) && $customerPic !== '-')
                    <div style="font-size: 6.5pt; color: #64748B; margin-top: 2px;">PIC: {{ $customerPic }}</div>
                @endif
            </td>
            <td class="label-cell" style="width: 18%;">No. SPK / Kontrak / PO</td>
            <td style="width: 32%; font-weight: bold; color: #0F172A;">{{ $contractSpkNumber ?? ($project->po_spk_number ?: ($project->contract_number ?: ($project->po_number ?: ($project->handover_data['contract_spk_number'] ?? ($project->handover_data['spk_number'] ?? '-'))))) }}</td>
        </tr>
        <tr>
            <td class="label-cell">Periode Pelaksanaan</td>
            <td>{{ $projectTimelineText }}</td>
            <td class="label-cell">Status &amp; Kesehatan</td>
            <td>
                <span class="badge {{ $healthStatus === 'healthy' ? 'badge-success' : ($healthStatus === 'warning' ? 'badge-warning' : 'badge-danger') }}">
                    {{ $healthStatusText }}
                </span>
                ({{ $project->status ?: 'In Progress' }})
            </td>
        </tr>
        <tr>
            <td class="label-cell">Project Manager (PM)</td>
            <td>{{ $project->pm ? $project->pm->name : 'PM IPNET' }}</td>
            <td class="label-cell">Tim Teknis Pelaksana</td>
            <td>
                @if(!empty($project->handover_data['assigned_teams']) && is_array($project->handover_data['assigned_teams']))
                    {{ implode(', ', $project->handover_data['assigned_teams']) }}
                @elseif($project->division)
                    {{ $project->division->name }}
                @else
                    Tim Engineering IPNET
                @endif
            </td>
        </tr>
    </table>

    @php
        $secAscii = 66; // 66 = 'B'
    @endphp

    @if(!empty($showScurve))
    <div class="section-spacer"></div>
    {{-- SECTION B: RINGKASAN PROGRESS & KURVA S --}}
    <div class="section-title">{{ chr($secAscii++) }}. KURVA S &amp; PROGRES KUMULATIF MINGGUAN (ACTUAL CUMULATIVE)</div>
    
    {{-- Ringkasan Metrik (3 Kolom Bersih, Tanpa Deviasi) --}}
    <table class="report-table" style="margin-bottom: 4px;">
        <tr style="background-color: #F8FAFC;">
            <td class="text-center" style="width: 33.3%;">
                <span style="font-size: 6pt; color: #64748B; text-transform: uppercase; font-weight: bold; display: block;">Target Plan Kumulatif</span>
                <span style="font-size: 10pt; font-weight: bold; color: #1E3A8A;">
                    {{ number_format($totalWeeklyWeightCum[$currentWeek] ?? end($totalWeeklyWeightCum) ?? 0, 2) }}%
                </span>
            </td>
            <td class="text-center" style="width: 33.3%;">
                <span style="font-size: 6pt; color: #64748B; text-transform: uppercase; font-weight: bold; display: block;">Realisasi Aktual Kumulatif</span>
                <span style="font-size: 10pt; font-weight: bold; color: #047857;">
                    {{ number_format($actualEV, 2) }}%
                </span>
            </td>
            <td class="text-center" style="width: 33.4%;">
                <span style="font-size: 6pt; color: #64748B; text-transform: uppercase; font-weight: bold; display: block;">Posisi Minggu Proyek</span>
                <span style="font-size: 10pt; font-weight: bold; color: #334155;">
                    Minggu {{ $currentWeek }} dari {{ $totalWeeks }} Minggu
                </span>
            </td>
        </tr>
    </table>

    {{-- GRAFIK VISUAL KURVA S (GENERATED HIGH-RES PNG UNTUK 100% RELIABILITAS TAMPIL DI PDF) --}}
    @if(!empty($sCurveChartBase64))
        <div style="border: 1px solid #CBD5E1; background-color: #FAFCFF; border-radius: 3px; padding: 3px; margin-bottom: 5px; text-align: center;">
            <img src="{{ $sCurveChartBase64 }}" style="width: 100%; height: auto; max-height: 145px; display: block;" alt="Kurva S Proyek">
        </div>
    @endif

    {{-- MATRIKS MINGGUAN KURVA S (Tanpa Baris Deviasi) --}}
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 18%; text-align: left;">Parameter Kurva S</th>
                @for($w = 1; $w <= min($totalWeeks, 14); $w++)
                    <th style="width: {{ 72 / min($totalWeeks, 14) }}%;">W{{ $w }}</th>
                @endfor
                <th style="width: 10%;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="label-cell">Target Plan Mingguan (%)</td>
                @for($w = 1; $w <= min($totalWeeks, 14); $w++)
                    <td class="text-center">{{ number_format($totalWeeklyWeightPlan[$w] ?? 0, 2) }}%</td>
                @endfor
                <td class="text-center font-bold">{{ number_format(array_sum($totalWeeklyWeightPlan), 2) }}%</td>
            </tr>
            <tr style="background-color: #F8FAFC;">
                <td class="label-cell font-bold" style="color: #1E3A8A;">Plan Kumulatif (%)</td>
                @for($w = 1; $w <= min($totalWeeks, 14); $w++)
                    <td class="text-center font-bold" style="color: #1E3A8A;">{{ number_format($totalWeeklyWeightCum[$w] ?? 0, 2) }}%</td>
                @endfor
                <td class="text-center font-bold" style="color: #1E3A8A;">100.00%</td>
            </tr>
            <tr>
                <td class="label-cell font-bold" style="color: #047857;">Actual Kumulatif (%)</td>
                @php $lastAct = 0; @endphp
                @for($w = 1; $w <= min($totalWeeks, 14); $w++)
                    @php 
                        $valAct = $actualCumulativePerWeek[$w] ?? null;
                        if ($valAct !== null && $valAct > 0) { $lastAct = $valAct; }
                    @endphp
                    <td class="text-center font-bold" style="color: #047857;">
                        {{ $valAct !== null ? number_format($valAct, 2) . '%' : ($w <= $currentWeek ? number_format($lastAct, 2) . '%' : '-') }}
                    </td>
                @endfor
                <td class="text-center font-bold" style="color: #047857;">{{ number_format($actualEV, 2) }}%</td>
            </tr>
        </tbody>
    </table>
    @endif

    @if(!empty($showWeekly))
    <div class="section-spacer"></div>
    {{-- SECTION C: TABEL WBS DELIVERABLES SCHEDULE --}}
    <div class="section-title">{{ chr($secAscii++) }}. RINCIAN JADWAL KERJA &amp; BOBOT DELIVERABLE (WBS SCHEDULE)</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 28%; text-align: left;">Nama Task / Deliverable WBS</th>
                <th style="width: 7%;">Durasi</th>
                <th style="width: 7%;">Start</th>
                <th style="width: 7%;">Finish</th>
                <th style="width: 6%;">Week</th>
                <th style="width: 10%;">Bobot (%)</th>
                <th style="width: 9%;">Daily (%)</th>
                <th style="width: 10%;">Progres (%)</th>
                <th style="width: 12%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totDur = 0;
                $totBobot = 0.0;
                $sampleDailyWeight = null;
            @endphp
            @forelse($weeklyWeightMatrix as $idx => $row)
                @php
                    $totDur += $row['duration'];
                    $totBobot += $row['weight'];
                    $t = $project->tasks->firstWhere('id', $row['id']) ?? $project->tasks->firstWhere('title', $row['title']);
                    $prog = $t ? (float)($t->progress ?: ($t->status === 'Completed' || $t->status === 'Done' ? 100 : 0)) : 0;
                    $dWeight = isset($row['daily_weight']) && $row['daily_weight'] !== null
                        ? (float) $row['daily_weight']
                        : 1.03;
                    if ($sampleDailyWeight === null && $dWeight > 0) {
                        $sampleDailyWeight = $dWeight;
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="font-bold text-left" style="color: #0F172A;">{{ $row['title'] }}</td>
                    <td class="text-center">{{ $row['duration'] }} Hari</td>
                    <td class="text-center">Day {{ $row['starting_day'] }}</td>
                    <td class="text-center">Day {{ $row['finish_day'] }}</td>
                    <td class="text-center font-bold">W{{ $row['week'] ?? ceil($row['finish_day'] / 7) }}</td>
                    <td class="text-right font-bold" style="color: #1E3A8A;">{{ number_format($row['weight'], 2) }}%</td>
                    <td class="text-right">{{ number_format($dWeight, 2) }}%</td>
                    <td class="text-right font-bold" style="color: {{ $prog >= 100 ? '#047857' : ($prog > 0 ? '#1E3A8A' : '#64748B') }};">
                        {{ number_format($prog, 1) }}%
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $prog >= 100 ? 'badge-success' : ($prog > 0 ? 'badge-info' : 'badge-warning') }}">
                            {{ $prog >= 100 ? 'Completed' : ($prog > 0 ? 'In Progress' : 'Not Started') }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 6px; color: #64748B;">Belum ada task WBS yang didaftarkan.</td>
                </tr>
            @endforelse
            <tr style="background-color: #F1F5F9; font-weight: bold;">
                <td colspan="2" class="text-center font-bold">TOTAL REKAPITULASI SCHEDULE ({{ count($weeklyWeightMatrix) }} TASKS)</td>
                <td class="text-center font-bold">{{ $totDur }} Hari</td>
                <td class="text-center" colspan="3">Day 0 s/d Day {{ $totalProjectDays ?? $totDur }} ({{ $totalWeeks }} Minggu)</td>
                <td class="text-right font-bold" style="color: #1E3A8A;">{{ number_format($totBobot, 2) }}%</td>
                <td class="text-right font-bold">{{ number_format($sampleDailyWeight ?? 1.03, 2) }}%</td>
                <td class="text-right font-bold" style="color: #047857;">{{ number_format($actualEV, 2) }}% EV</td>
                <td class="text-center font-bold">
                    <span class="badge {{ $actualEV >= 100 ? 'badge-success' : 'badge-info' }}">
                        {{ $actualEV >= 100 ? '100% Selesai' : number_format($actualEV, 1) . '% On Going' }}
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    @endif

    @if(!empty($showExpenses))
    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- HALAMAN EXPENSES FINANCIAL TRACKING                      --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    @if(!empty($showScurve) || !empty($showWeekly))
    <div class="page-break"></div>

    {{-- KOP HALAMAN EXPENSES --}}
    <table class="doc-header-table">
        <tr>
            <td style="width: 18%; text-align: left; vertical-align: middle;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo IPNET" style="height: 38px; width: auto; max-width: 90px; display: block;">
                @else
                    <span style="font-weight: bold; font-size: 11pt; color: #8F0A0D;">PT IP NETWORK SOLUSINDO</span>
                @endif
            </td>
            <td style="width: 82%; text-align: center; vertical-align: middle;">
                <div class="doc-title">LAPORAN KEUANGAN &amp; PENGELUARAN PROYEK</div>
                <div class="doc-subtitle">RINCIAN BIAYA OPERASIONAL &amp; REALISASI BELANJA PROYEK</div>
            </td>
        </tr>
    </table>
    <div class="doc-header-line"></div>
    @endif

    @php
        $expLetter = chr($secAscii++);
    @endphp

    {{-- SECTION D: RINGKASAN ANGGARAN & PENGELUARAN --}}
    <div class="section-title">{{ $expLetter }}. REKAPITULASI ANGGARAN &amp; REALISASI PENGELUARAN PROYEK</div>
    <table class="report-table" style="margin-bottom: 5px;">
        <tr style="background-color: #F8FAFC;">
            <td class="text-center" style="width: 25%;">
                <span style="font-size: 6pt; color: #64748B; text-transform: uppercase; font-weight: bold; display: block;">Nilai Kontrak (Budget)</span>
                <span style="font-size: 9.5pt; font-weight: bold; color: #0F172A;">
                    Rp {{ number_format($contractValue, 0, ',', '.') }}
                </span>
            </td>
            <td class="text-center" style="width: 25%;">
                <span style="font-size: 6pt; color: #64748B; text-transform: uppercase; font-weight: bold; display: block;">Total Realisasi Pengeluaran</span>
                <span style="font-size: 9.5pt; font-weight: bold; color: #B91C1C;">
                    Rp {{ number_format($totalSpend, 0, ',', '.') }}
                </span>
            </td>
            <td class="text-center" style="width: 25%;">
                <span style="font-size: 6pt; color: #64748B; text-transform: uppercase; font-weight: bold; display: block;">Sisa Saldo (Remaining)</span>
                <span style="font-size: 9.5pt; font-weight: bold; color: #047857;">
                    Rp {{ number_format($remainingBudget, 0, ',', '.') }}
                </span>
            </td>
            <td class="text-center" style="width: 25%;">
                <span style="font-size: 6pt; color: #64748B; text-transform: uppercase; font-weight: bold; display: block;">Persentase Pengeluaran</span>
                <span style="font-size: 9.5pt; font-weight: bold; color: {{ $spendPercentage > 85 ? '#B91C1C' : '#334155' }};">
                    {{ $spendPercentage }}%
                </span>
            </td>
        </tr>
    </table>

    <div class="section-spacer"></div>

    {{-- SECTION D2: TABEL RINCIAN PENGELUARAN --}}
    <div class="section-title">{{ $expLetter }}.1 RINCIAN PENGELUARAN BIAYA PROYEK (EXPENSES LIST)</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 14%;">Tanggal</th>
                <th style="width: 18%;">Kategori</th>
                <th style="width: 35%; text-align: left;">Keterangan &amp; Keperluan Pengeluaran</th>
                <th style="width: 13%;">Dicatat Oleh</th>
                <th style="width: 15%; text-align: right;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $idx => $exp)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $exp['expense_date'] ?? '-' }}</td>
                    <td class="text-center font-bold">
                        <span class="badge badge-info">{{ $exp['category'] ?? 'Operasional' }}</span>
                    </td>
                    <td class="text-left">{{ $exp['description'] ?? '-' }}</td>
                    <td class="text-center">{{ $exp['creator_name'] ?? 'PM' }}</td>
                    <td class="text-right font-bold" style="color: #0F172A;">
                        {{ $exp['formatted'] ?? ('Rp ' . number_format($exp['amount'] ?? 0, 0, ',', '.')) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 8px; color: #64748B;">
                        Belum ada catatan pengeluaran biaya operasional untuk proyek ini.
                    </td>
                </tr>
            @endforelse
            <tr style="background-color: #F1F5F9; font-weight: bold;">
                <td colspan="5" class="text-right font-bold" style="padding-right: 8px;">TOTAL REALISASI PENGELUARAN PROYEK</td>
                <td class="text-right font-bold" style="color: #B91C1C; font-size: 7.8pt;">
                    Rp {{ number_format($totalSpend, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>
    @endif

</body>
</html>
