<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Evaluasi &amp; Lesson Learned Prospek (Lost Deal Report) - PT IP Network Solusindo</title>
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
        .badge-lost {
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

        /* ── Tabel Standar ── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
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
            width: 20%;
        }
        .info-value {
            color: #0F172A;
            width: 30%;
        }

        /* ── Checklist Grid Faktor ── */
        .factor-box {
            background-color: #F8FAFC;
            border: 1px solid #CBD5E1;
            border-radius: 4px;
            padding: 7px 10px;
            margin-bottom: 10px;
        }
        .factor-item {
            display: inline-block;
            width: 31%;
            margin-bottom: 4px;
            font-size: 7.5pt;
            vertical-align: top;
        }
        .factor-checkbox {
            display: inline-block;
            width: 12px;
            height: 12px;
            border: 1px solid #475569;
            text-align: center;
            line-height: 11px;
            font-size: 8pt;
            font-weight: bold;
            margin-right: 4px;
            vertical-align: middle;
        }
        .factor-checkbox.active {
            background-color: #8F0A0D;
            color: #FFFFFF;
            border-color: #8F0A0D;
        }

        /* ── Content Card ── */
        .content-card {
            background-color: #FFFFFF;
            border: 1px solid #1E293B;
            padding: 6px 9px;
            margin-bottom: 10px;
            font-size: 7.5pt;
            line-height: 1.4;
            color: #1E293B;
        }

        /* ── Gap Table ── */
        .gap-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            border: 1px solid #1E293B;
        }
        .gap-table th {
            background-color: #EBF3FB;
            color: #0F172A;
            font-weight: bold;
            font-size: 7.5pt;
            padding: 5px 6px;
            border: 1px solid #1E293B;
            text-align: left;
        }
        .gap-table td {
            font-size: 7.5pt;
            padding: 5px 6px;
            border: 1px solid #1E293B;
            vertical-align: top;
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
                <p class="doc-subtitle">LAPORAN EVALUASI &amp; LESSON LEARNED PROSPEK (LOST DEAL REPORT)</p>
            </td>
            <td style="width: 22%; text-align: right; vertical-align: middle;">
                <div class="badge-lost">Closed Lost Review</div>
                <div style="font-size: 6.8pt; color: #64748B; margin-top: 4px;">
                    No: LLR-SLS/{{ date('Y') }}/{{ str_pad($project->id, 4, '0', STR_PAD_LEFT) }}
                </div>
            </td>
        </tr>
    </table>

    @php
        $analysis = $project->parsed_lost_analysis;
        $primaryFactor = $analysis['primary_factor'] ?? ($project->lost_reason ?: 'Harga / Komersial');
        $competitorWinner = $analysis['competitor_winner'] ?? ($project->lost_competitor ?: '-');
        $competitorPrice = $analysis['competitor_price'] ?? null;
        $ourPrice = $analysis['our_price'] ?? $project->contract_value;
    @endphp

    {{-- Section A: Identitas Prospek & Pengadaan --}}
    <div class="section-title">A. IDENTITAS PROSPEK / TENDER</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Nama Klien / Perusahaan</td>
            <td class="info-value" style="font-weight: bold;">{{ $project->client ?: '-' }}</td>
            <td class="info-label">Nama Prospek / Proyek</td>
            <td class="info-value" style="font-weight: bold; color: #8F0A0D;">{{ $project->name }}</td>
        </tr>
        <tr>
            <td class="info-label">Sales Person (AE)</td>
            <td class="info-value">{{ $project->sales_name ?: ($project->creator->name ?? 'Sales Team') }}</td>
            <td class="info-label">Presales / Arsitek Solusi</td>
            <td class="info-value">
                @php
                    $hd = is_array($project->handover_data) ? $project->handover_data : [];
                    $psName = $hd['technical_assignments']['presales']['name'] ?? ($hd['technical_assignments']['architect']['name'] ?? 'Presales Team');
                @endphp
                {{ $psName }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Nilai Penawaran IPNET</td>
            <td class="info-value" style="font-weight: bold; color: #0F172A;">
                {{ $ourPrice ? 'Rp ' . number_format((float)$ourPrice, 0, ',', '.') : 'Rp -' }}
            </td>
            <td class="info-label">Tanggal Evaluasi / Review</td>
            <td class="info-value">
                {{ !empty($analysis['evaluated_at']) ? \Carbon\Carbon::parse($analysis['evaluated_at'])->format('d M Y') : now()->format('d M Y') }}
            </td>
        </tr>
    </table>

    {{-- Section B: Klasifikasi Faktor Utama Kekalahan --}}
    <div class="section-title">B. FAKTOR UTAMA KEKALAHAN (PRIMARY LOSS DRIVER)</div>
    @php
        $factors = [
            'Harga / Komersial'                 => 'Harga / Komersial',
            'Spesifikasi Teknis / Kepatuhan TOR' => 'Spek Teknis / TOR',
            'Waktu / Ketersediaan (Lead Time)'  => 'Lead Time / Stok',
            'Relasi / Incumbent'                => 'Relasi / Incumbent',
            'Dukungan Prinsipal / Distributor'  => 'Dukungan Prinsipal',
            'Legalitas / Dokumen Administrasi'  => 'Administrasi Tender',
        ];
    @endphp
    <div class="factor-box">
        @foreach($factors as $fFull => $fShort)
            @php $isActive = str_contains(strtolower($primaryFactor), strtolower($fShort)) || str_contains(strtolower($primaryFactor), strtolower(explode(' ', $fShort)[0])); @endphp
            <div class="factor-item">
                <span class="factor-checkbox {{ $isActive ? 'active' : '' }}">{!! $isActive ? '&#10003;' : '&nbsp;' !!}</span>
                <span style="{{ $isActive ? 'font-weight: bold; color: #8F0A0D;' : 'color: #475569;' }}">{{ $fFull }}</span>
            </div>
        @endforeach
    </div>

    {{-- Section C: Vendor Pemenang & Gap Evaluasi Harga --}}
    <div class="section-title">C. INFORMASI PEMENANG &amp; PERBANDINGAN KOMERSIAL</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Vendor / Kompetitor Pemenang</td>
            <td class="info-value" style="font-weight: bold; color: #8F0A0D;">{{ $competitorWinner }}</td>
            <td class="info-label">Estimasi Harga Pemenang</td>
            <td class="info-value">
                {{ $competitorPrice ? 'Rp ' . number_format((float)$competitorPrice, 0, ',', '.') : 'Tidak dipublikasikan / N/A' }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Selisih Penawaran (Gap)</td>
            <td class="info-value" colspan="3">
                @if($ourPrice && $competitorPrice)
                    @php 
                        $diff = (float)$ourPrice - (float)$competitorPrice;
                        $pct = round(($diff / (float)$ourPrice) * 100, 1);
                    @endphp
                    Penawaran IPNET lebih tinggi sekitar <strong>Rp {{ number_format(abs($diff), 0, ',', '.') }}</strong> ({{ $pct }}% selisih).
                @else
                    {{ $analysis['turning_point'] ?? 'Evaluasi perbandingan harga berdasarkan informasi umpan balik dari tim pengadaan klien.' }}
                @endif
            </td>
        </tr>
    </table>

    {{-- Section D: Kronologi & Titik Balik Kekalahan --}}
    <div class="section-title">D. KRONOLOGI &amp; TITIK BALIK KEKALAHAN (TURNING POINT)</div>
    <div class="content-card">
        {{ $analysis['turning_point'] ?: ($project->lost_reason ?: 'Tidak ada rincian titik balik khusus yang dicatat.') }}
    </div>

    {{-- Section E: Analisis Gap Penawaran --}}
    <div class="section-title">E. ANALISIS GAP: KELEMAHAN INTERNAL VS KEUNGGULAN KOMPETITOR</div>
    <table class="gap-table">
        <thead>
            <tr>
                <th style="width: 50%; color: #8F0A0D;">Faktor Kelemahan Penawaran IPNET</th>
                <th style="width: 50%; color: #1E3A8A;">Keunggulan Penawaran Kompetitor Pemenang</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $analysis['gap_our_weakness'] ?: 'Margin penawaran kurang fleksibel / lead time barang lebih panjang dari ekspektasi klien.' }}</td>
                <td>{{ $analysis['gap_competitor_strength'] ?: ($competitorWinner . ' menawarkan kesiapan unit lebih cepat dan pendekatan intensif ke pengambil keputusan.') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Section F: Pelajaran yang Didapat (Lesson Learned) --}}
    <div class="section-title">F. LESSON LEARNED &amp; KEY TAKEAWAYS</div>
    <div class="content-card" style="border-left: 3px solid #8F0A0D;">
        {!! nl2br(e($analysis['lessons_learned'] ?: ($project->lost_reason ?: 'Pentingnya validasi pagu anggaran klien di tahap awal dan penguncian registrasi deal ke pihak prinsipal distributor lebih dini.'))) !!}
    </div>

    {{-- Section G: Rencana Perbaikan & Tindak Lanjut Masa Depan --}}
    <div class="section-title">G. RENCANA PERBAIKAN &amp; TINDAK LANJUT MASA DEPAN (RE-ENGAGEMENT PLAN)</div>
    <div class="content-card" style="border-left: 3px solid #059669;">
        {!! nl2br(e($analysis['future_strategy'] ?: 'Tetap menjalin hubungan baik dengan PIC klien (courtesy call berkala) dan memetakan jadwal renewal / ekspansi kebutuhan fase berikutnya.')) !!}
    </div>

    {{-- Section H: Lembar Pengesahan & Verifikasi (Tanda Tangan) --}}
    <div class="section-title">H. LEMBAR PENGESAHAN &amp; VERIFIKASI EVALUASI (TANDA TANGAN)</div>
    <table class="gap-table">
        <thead>
            <tr>
                <th style="width: 28%;">Pihak Evaluator</th>
                <th style="width: 26%;">Nama Personel</th>
                <th style="width: 18%; text-align: center;">Tanggal</th>
                <th style="width: 28%; text-align: center;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold; background-color: #F8FAFC;">Account Executive (Sales)</td>
                <td style="font-weight: bold;">{{ $project->sales_name ?: ($analysis['evaluated_by'] ?? 'Sales Person') }}</td>
                <td style="text-align: center;">{{ !empty($analysis['evaluated_at']) ? \Carbon\Carbon::parse($analysis['evaluated_at'])->format('d/m/Y') : now()->format('d/m/Y') }}</td>
                <td style="text-align: center; padding: 6px 4px;">
                    <div style="border: 1px dashed #059669; background-color: #ECFDF5; border-radius: 4px; padding: 4px; display: inline-block; min-width: 120px;">
                        <span style="font-size: 6.8pt; font-weight: bold; color: #065F46; display: block;">[ TANDA TANGAN DIGITAL ]</span>
                        <span style="font-size: 6.2pt; color: #047857; display: block;">Evaluated &amp; Logged</span>
                        <span style="font-size: 5.8pt; color: #64748B; font-family: monospace;">{{ $project->sales_name ?: ($analysis['evaluated_by'] ?? 'Sales Person') }}</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold; background-color: #F8FAFC;">Division Head / Atasan Sales</td>
                <td style="font-weight: bold; color: #0F172A;">Susanto Djaya</td>
                <td style="text-align: center;">{{ !empty($analysis['evaluated_at']) ? \Carbon\Carbon::parse($analysis['evaluated_at'])->format('d/m/Y') : now()->format('d/m/Y') }}</td>
                <td style="text-align: center; padding: 6px 4px;">
                    <div style="border: 1px dashed #8F0A0D; background-color: #FEF2F2; border-radius: 4px; padding: 4px; display: inline-block; min-width: 120px;">
                        <span style="font-size: 6.8pt; font-weight: bold; color: #8F0A0D; display: block;">[ TANDA TANGAN ATASAN ]</span>
                        <span style="font-size: 6.2pt; color: #991B1B; display: block;">Approved Division Head</span>
                        <span style="font-size: 5.8pt; color: #64748B; font-family: monospace;">Susanto Djaya</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Footer Verifikasi Retrospektif Internal --}}
    <div class="verification-box">
        <strong>CATATAN REVIEW RETROSPEKTIF INTERNAL:</strong><br>
        Dokumen ini diterbitkan oleh <strong>Sistem Manajemen Penjualan &amp; CRM PT IP Network Solusindo</strong> pada tanggal {{ now()->format('d/m/Y H:i:s') }}.<br>
        Disusun oleh: <strong>{{ $project->sales_name ?: ($analysis['evaluated_by'] ?? 'Sales Team') }}</strong> dan disetujui oleh <strong>Susanto Djaya (Division Head)</strong> untuk keperluan evaluasi internal, continuous improvement strategi komersial, dan transfer pembelajaran.
    </div>

    <div style="margin-top: 8px; padding-top: 6px; border-top: 1px dashed #CBD5E1; font-size: 6.8pt; color: #475569; font-style: italic; line-height: 1.35;">
        <strong>Catatan Dokumen Resmi:</strong> Dokumen evaluasi ini diterbitkan secara sah melalui Sistem Terintegrasi CRM &amp; Pipeline PT IP Network Solusindo. Berfungsi sebagai materi evaluasi retrospektif internal dan dasar continuous improvement strategi penjualan tanpa memerlukan tanda tangan basah.
    </div>

</div>
</body>
</html>
