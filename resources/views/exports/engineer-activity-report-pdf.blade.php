<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Aktivitas Engineer – IP Network Solusindo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 11px;
            color: #1a202c;
            background: #ffffff;
            line-height: 1.4;
        }

        /* ── HEADER / KOP SURAT ── */
        .kop {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 24px 14px 24px;
            border-bottom: 3px solid #8F0A0D;
            background: #ffffff;
        }

        .kop-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .kop-logo {
            width: 62px;
            height: 62px;
            object-fit: contain;
        }

        .kop-company {
            border-left: 2px solid #8F0A0D;
            padding-left: 14px;
        }

        .kop-company h1 {
            font-size: 15px;
            font-weight: 800;
            color: #8F0A0D;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .kop-company p {
            font-size: 10px;
            color: #4a5568;
            margin-top: 2px;
        }

        .kop-right {
            text-align: right;
        }

        .kop-right .doc-title {
            font-size: 13px;
            font-weight: 800;
            color: #1a202c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-right .doc-no {
            font-size: 10px;
            color: #718096;
            margin-top: 3px;
        }

        /* ── INFO META LAPORAN ── */
        .meta-section {
            padding: 12px 24px;
            background: #fef2f2;
            border-bottom: 1px solid #fed7d7;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .meta-item {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 10px;
        }

        .meta-item .label {
            font-size: 9px;
            color: #718096;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .meta-item .value {
            font-size: 12px;
            font-weight: 800;
            color: #1a202c;
            margin-top: 2px;
        }

        .meta-item .value.red { color: #8F0A0D; }
        .meta-item .value.green { color: #2f855a; }
        .meta-item .value.blue { color: #2b6cb0; }
        .meta-item .value.amber { color: #c05621; }

        /* ── FILTER INFO BAR ── */
        .filter-bar {
            padding: 8px 24px;
            background: #f7fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
            font-size: 10px;
            color: #4a5568;
        }

        .filter-bar span {
            background: #edf2f7;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
            border: 1px solid #e2e8f0;
        }

        .filter-bar strong {
            color: #8F0A0D;
        }

        /* ── TABEL UTAMA ── */
        .table-wrapper {
            padding: 14px 24px;
        }

        .section-title {
            font-size: 11px;
            font-weight: 800;
            color: #8F0A0D;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1.5px solid #fed7d7;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }

        thead tr {
            background: #8F0A0D;
            color: #ffffff;
        }

        thead th {
            padding: 8px 7px;
            text-align: left;
            font-weight: 700;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border: 1px solid #7a080b;
        }

        thead th:first-child {
            text-align: center;
            width: 30px;
        }

        tbody tr:nth-child(even) {
            background: #fef8f8;
        }

        tbody tr:nth-child(odd) {
            background: #ffffff;
        }

        tbody tr:hover {
            background: #fff5f5;
        }

        tbody td {
            padding: 7px 7px;
            border: 1px solid #e2e8f0;
            vertical-align: top;
            color: #2d3748;
            line-height: 1.4;
        }

        tbody td:first-child {
            text-align: center;
            font-weight: 700;
            color: #718096;
            font-size: 9px;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 8px;
            font-size: 9px;
            font-weight: 700;
        }

        .badge-done {
            background: #c6f6d5;
            color: #22543d;
            border: 1px solid #9ae6b4;
        }

        .badge-progress {
            background: #bee3f8;
            color: #2a4365;
            border: 1px solid #90cdf4;
        }

        .badge-delayed {
            background: #feebc8;
            color: #744210;
            border: 1px solid #fbd38d;
        }

        .badge-type {
            background: #fef2f2;
            color: #8F0A0D;
            border: 1px solid #fed7d7;
        }

        .noted-text {
            background: #fffbeb;
            border-left: 2px solid #f6ad55;
            padding: 3px 5px;
            border-radius: 3px;
            font-size: 9px;
            color: #7b341e;
            margin-top: 3px;
        }

        /* ── FOOTER HALAMAN ── */
        .page-footer {
            padding: 12px 24px;
            border-top: 2px solid #8F0A0D;
            margin-top: 14px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .footer-left {
            font-size: 9px;
            color: #718096;
        }

        .footer-left strong {
            color: #8F0A0D;
        }

        .signature-block {
            text-align: center;
            font-size: 10px;
            color: #2d3748;
        }

        .signature-block .sig-name {
            font-weight: 800;
            color: #1a202c;
            margin-top: 40px;
            border-top: 1px solid #4a5568;
            padding-top: 4px;
            min-width: 140px;
        }

        .signature-block .sig-label {
            font-size: 9px;
            color: #718096;
        }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 30px;
            color: #a0aec0;
            font-size: 11px;
        }

        /* ── PRINT ── */
        @media print {
            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
            .kop { break-inside: avoid; }
            thead { display: table-header-group; }
            tr { page-break-inside: avoid; }
        }

        @page {
            size: A4 landscape;
            margin: 10mm 8mm;
        }
    </style>
</head>
<body>

    {{-- ══ KOP SURAT ══ --}}
    <div class="kop">
        <div class="kop-left">
            <img src="{{ public_path('images/ipnet1.png') }}" alt="IP Network Solusindo" class="kop-logo">
            <div class="kop-company">
                <h1>PT IP Network Solusindo</h1>
                <p>Jl. Rawa Buntu No.2, Kec. Serpong, Tangerang Selatan, Banten 15310</p>
                <p>Telp: (021) 2965-5050  |  www.ipnetsolusindo.co.id</p>
            </div>
        </div>
        <div class="kop-right">
            <div class="doc-title">Laporan Aktivitas Engineer</div>
            <div class="doc-no">Dicetak: {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</div>
            <div class="doc-no">Pukul: {{ now()->format('H:i') }} WIB</div>
        </div>
    </div>

    {{-- ══ META SUMMARY ══ --}}
    <div class="meta-section">
        <div class="meta-grid">
            <div class="meta-item">
                <div class="label">Total Aktivitas</div>
                <div class="value red">{{ number_format($totalActivities) }}</div>
            </div>
            <div class="meta-item">
                <div class="label">Selesai</div>
                <div class="value green">{{ number_format($totalCompleted) }}</div>
            </div>
            <div class="meta-item">
                <div class="label">Sedang Berjalan</div>
                <div class="value blue">{{ number_format($totalInProgress) }}</div>
            </div>
            <div class="meta-item">
                <div class="label">Ditunda</div>
                <div class="value amber">{{ number_format($totalDelayed) }}</div>
            </div>
        </div>
    </div>

    {{-- ══ FILTER INFO ══ --}}
    <div class="filter-bar">
        <strong>Filter Aktif:</strong>
        @if($filterEngineer) <span>Engineer: {{ $filterEngineer }}</span> @endif
        @if($filterDate) <span>Tanggal: {{ \Carbon\Carbon::parse($filterDate)->isoFormat('D MMMM Y') }}</span> @endif
        @if($filterStatus) <span>Status: {{ $filterStatus }}</span> @endif
        @if($filterType) <span>Tipe: {{ $filterType }}</span> @endif
        @if(!$filterEngineer && !$filterDate && !$filterStatus && !$filterType)
            <span>Semua Data</span>
        @endif
        <span style="margin-left:auto;">Dicetak oleh: <strong>{{ $printedBy }}</strong></span>
    </div>

    {{-- ══ TABEL AKTIVITAS ══ --}}
    <div class="table-wrapper">
        <div class="section-title">Rincian Catatan Aktivitas</div>

        @if($activities->isEmpty())
            <div class="empty-state">Tidak ada data aktivitas yang ditemukan.</div>
        @else
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th style="width:90px;">Tanggal</th>
                    <th style="width:80px;">Waktu</th>
                    <th style="width:100px;">Engineer</th>
                    <th style="width:80px;">Proyek</th>
                    <th style="width:80px;">Klien</th>
                    <th style="width:100px;">Tipe Aktivitas</th>
                    <th style="min-width:160px;">Aktivitas / Deskripsi</th>
                    <th style="width:65px;">Status</th>
                    <th style="min-width:120px;">Noted</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $i => $act)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        @if($act->activity_date)
                            {{ $act->activity_date->isoFormat('D MMM Y') }}
                        @else —
                        @endif
                    </td>
                    <td>
                        @if($act->start_time)
                            {{ \Carbon\Carbon::parse($act->start_time)->format('H:i') }}
                            @if($act->end_time)
                                – {{ \Carbon\Carbon::parse($act->end_time)->format('H:i') }}
                            @endif
                        @else —
                        @endif
                    </td>
                    <td>
                        <strong>{{ $act->engineer->name ?? '-' }}</strong>
                        @if($act->engineer->position ?? false)
                            <br><span style="color:#718096;font-size:8.5px;">{{ $act->engineer->position }}</span>
                        @endif
                    </td>
                    <td>{{ $act->project->name ?? '-' }}</td>
                    <td>{{ $act->project->client ?? '-' }}</td>
                    <td><span class="badge badge-type">{{ $act->activity_type }}</span></td>
                    <td>{{ $act->description }}</td>
                    <td>
                        @if($act->status === 'Selesai')
                            <span class="badge badge-done">Selesai</span>
                        @elseif($act->status === 'Sedang Berjalan')
                            <span class="badge badge-progress">Berjalan</span>
                        @else
                            <span class="badge badge-delayed">Ditunda</span>
                        @endif
                    </td>
                    <td>
                        @if($act->notes)
                            <div class="noted-text">{{ $act->notes }}</div>
                        @else —
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- ══ FOOTER & TANDA TANGAN ══ --}}
    <div class="page-footer">
        <div class="footer-left">
            <p><strong>PT IP Network Solusindo</strong> — Dokumen Internal</p>
            <p>Laporan ini digenerate secara otomatis oleh sistem IPNET Dashboard.</p>
            <p>Jumlah data: {{ $activities->count() }} entri aktivitas engineer.</p>
        </div>
        <div class="signature-block">
            <div class="sig-label">Mengetahui,</div>
            <div class="sig-name">Lead Engineer / Team Leader</div>
            <div class="sig-label">Nama & Jabatan</div>
        </div>
    </div>

</body>
</html>
