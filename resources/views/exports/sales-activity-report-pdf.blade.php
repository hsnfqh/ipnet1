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

        /* ── Header Dokumen (Standar Engineer) ── */
        .doc-header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 2px;
            margin-bottom: 4px;
        }
        .doc-header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .doc-title {
            font-size: 13.5pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: center;
        }
        .doc-intro {
            font-size: 7pt;
            color: #1E293B;
            margin-top: 2px;
            margin-bottom: 4px;
            line-height: 1.2;
        }
        .doc-header-line {
            width: 100%;
            height: 0;
            border-bottom: 1px solid #000000;
            margin-top: 2px;
            margin-bottom: 6px;
            clear: both;
        }

        /* ── Section Titles ── */
        .section-title {
            font-size: 8.2pt;
            font-weight: bold;
            color: #0F172A;
            text-transform: uppercase;
            margin-top: 9px;
            margin-bottom: 4px;
            letter-spacing: 0.3px;
        }

        /* ── Tabel Identitas & Data ── */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border: 1px solid #1E293B;
        }
        .info-table td {
            font-size: 7.2pt;
            padding: 3.5px 6px;
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

        /* ── Tabel Data & List ── */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border: 1px solid #1E293B;
        }
        .data-table th {
            background-color: #EBF3FB;
            color: #0F172A;
            font-weight: bold;
            font-size: 7.2pt;
            padding: 4px 5px;
            border: 1px solid #1E293B;
            text-align: left;
            text-transform: uppercase;
        }
        .data-table th.center, .data-table td.center {
            text-align: center;
        }
        .data-table td {
            font-size: 7.2pt;
            padding: 4px 5px;
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
            padding: 6px 8px;
            margin-bottom: 8px;
        }
        .action-box-title {
            font-size: 7.8pt;
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
            padding: 6px 8px;
            margin-top: 8px;
            font-size: 6.8pt;
            color: #475569;
            line-height: 1.35;
        }
        .verification-box strong {
            color: #0F172A;
        }

        .footer-disclaimer {
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px dashed #CBD5E1;
            font-size: 6.8pt;
            color: #475569;
            font-style: italic;
            line-height: 1.35;
        }

        /* ── Footer Dokumen (Standar Engineer) ── */
        .doc-footer {
            margin-top: 10px;
            padding-top: 4px;
            border-top: 1px solid #000000;
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
            font-size: 6pt;
            color: #000000;
            line-height: 1.2;
        }
        .paraf-box {
            border: 1px solid #000000;
            width: 55px;
            height: 16px;
            text-align: center;
            line-height: 16px;
            font-size: 6pt;
            display: inline-block;
            float: right;
        }
    </style>
</head>
<body>
<div class="page-container">

    {{-- Header Dokumen (Standar Engineer) --}}
    <table class="doc-header-table">
        <tr>
            <td style="width: 15%; text-align: left; vertical-align: middle;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" alt="Logo IPNET" style="height: 38px; width: auto; max-width: 80px; display: block;">
                @else
                    <span style="font-size: 14pt; font-weight: bold; color: #000000;">IP-NET</span>
                @endif
            </td>
            <td style="width: 85%; text-align: center; vertical-align: middle;">
                <div class="doc-title">FORM LAPORAN AKTIVITAS SALES</div>
            </td>
        </tr>
    </table>

    <div class="doc-intro">
        Dokumen ini digunakan sebagai laporan aktivitas sales, notulensi koordinasi dengan klien, dan bukti pelaksanaan agenda bisnis.
    </div>
    <div class="doc-header-line"></div>

    @php
        $payload = [];
        $items = [];
        $attendees = [];
        if (!empty($activity->notes) && is_string($activity->notes) && str_starts_with(trim($activity->notes), '{')) {
            $decoded = json_decode($activity->notes, true);
            if (is_array($decoded)) {
                $payload = $decoded;
                $items = $decoded['items'] ?? [];
                $attendees = $decoded['attendees'] ?? [];
            }
        }
    @endphp

    {{-- Section A: Identitas Prospek & Pertemuan --}}
    <div class="section-title">A. IDENTITAS PROSPEK &amp; PERTEMUAN</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Prospek / Proyek Tujuan</td>
            <td class="info-value" style="font-weight: bold; color: #8F0A0D;">{{ $activity->project->name ?? '-' }}</td>
            <td class="info-label">No. Dokumen / Laporan</td>
            <td class="info-value" style="font-weight: bold; font-family: monospace; color: #8F0A0D;">
                SLS-ACT/{{ date('Y') }}/{{ str_pad($activity->id, 4, '0', STR_PAD_LEFT) }}
            </td>
        </tr>
        <tr>
            <td class="info-label">Judul / Topik Pertemuan</td>
            <td class="info-value" style="font-weight: bold;">{{ $payload['title'] ?? $activity->subject }}</td>
            <td class="info-label">Tipe Aktivitas / Agenda</td>
            <td class="info-value" style="font-weight: bold;">{{ $activity->activity_type }}</td>
        </tr>
        <tr>
            <td class="info-label">Sales Person (AE)</td>
            <td class="info-value">{{ $activity->sales->name ?? 'Sales Team' }}</td>
            <td class="info-label">Media / Lokasi Pertemuan</td>
            <td class="info-value">{{ $payload['meeting_media'] ?? 'Onsite di Lokasi Klien' }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal Pelaksanaan</td>
            <td class="info-value">{{ \Carbon\Carbon::parse($activity->activity_date)->isoFormat('dddd, D MMMM Y') }}</td>
            <td class="info-label">Durasi Pelaksanaan</td>
            <td class="info-value" style="font-weight: bold; color: #8F0A0D;">
                {{ $payload['duration_days'] ?? (!empty($payload['meeting_time_start']) ? ($payload['meeting_time_start'] . ' WIB') : '1 Hari') }}{{ !empty($payload['duration_days']) ? ' Hari' : '' }}
            </td>
        </tr>
    </table>

    {{-- Section B: Komposisi Peserta Pertemuan (Attendees) --}}
    <div class="section-title">B. KOMPOSISI PESERTA PERTEMUAN (ATTENDEES)</div>
    @if(!empty($attendees) && count($attendees) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th class="center" style="width: 28px;">No</th>
                    <th style="width: 100px;">Pihak</th>
                    <th style="width: 140px;">Nama Peserta</th>
                    <th style="width: 120px;">Jabatan / Peran</th>
                    <th style="width: 100px;">Kontak / No. HP</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendees as $attIdx => $att)
                    @if(!empty($att['nama']) || !empty($att['jabatan']))
                        <tr>
                            <td class="center" style="font-weight: bold; color: #8F0A0D;">{{ $attIdx + 1 }}</td>
                            <td><strong>{{ $att['pihak'] ?? 'Klien' }}</strong></td>
                            <td style="font-weight: bold;">{{ $att['nama'] ?: '-' }}</td>
                            <td>{{ $att['jabatan'] ?: '-' }}</td>
                            <td>{{ $att['kontak'] ?: '-' }}</td>
                            <td>{{ $att['keterangan'] ?: '-' }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
        <div style="font-size: 6.8pt; color: #475569; margin-top: -4px; margin-bottom: 6px;">
            Total peserta terdata: <strong>{{ count($attendees) }} orang</strong>
        </div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="center" style="width: 28px;">No</th>
                    <th style="width: 100px;">Pihak</th>
                    <th style="width: 160px;">Nama Peserta</th>
                    <th style="width: 130px;">Jabatan / Peran</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center" style="font-weight: bold; color: #8F0A0D;">1</td>
                    <td>Internal IPNET</td>
                    <td style="font-weight: bold;">{{ $activity->sales->name ?? 'Sales In Charge' }}</td>
                    <td>Account Executive</td>
                    <td>Sales Person In Charge</td>
                </tr>
                <tr>
                    <td class="center" style="font-weight: bold; color: #8F0A0D;">2</td>
                    <td>Klien / Customer</td>
                    <td style="font-weight: bold;">{{ $activity->project->client ?? 'PIC Klien' }}</td>
                    <td>Perwakilan Manajemen / PIC</td>
                    <td>Customer Attendee</td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- Section C: Ruang Lingkup Kebutuhan & Estimasi Nilai --}}
    @if(!empty($payload['scope_category']) || !empty($payload['scope_description']) || !empty($payload['estimated_budget']))
        <div class="section-title">C. RUANG LINGKUP KEBUTUHAN &amp; ESTIMASI NILAI (OPPORTUNITY SCOPE)</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Kategori Kebutuhan</td>
                <td class="info-value" colspan="3" style="font-weight: bold;">
                    {{ $payload['scope_category'] ?? '-' }}
                </td>
            </tr>
            @if(!empty($payload['scope_description']))
                <tr>
                    <td class="info-label" style="vertical-align: top;">Ringkasan Kebutuhan &amp; Spesifikasi</td>
                    <td class="info-value" colspan="3" style="white-space: pre-line;">
                        {{ $payload['scope_description'] }}
                    </td>
                </tr>
            @endif
            <tr>
                <td class="info-label">Estimasi Budget Customer</td>
                <td class="info-value" style="font-weight: bold; color: #8F0A0D;">
                    {{ !empty($payload['estimated_budget']) ? 'Rp ' . number_format((float)$payload['estimated_budget'], 0, ',', '.') : 'Dalam Proses Estimasi' }}
                </td>
                <td class="info-label">Target Tender / PO</td>
                <td class="info-value" style="font-weight: bold;">
                    {{ $payload['tender_timeline'] ?? '-' }}
                </td>
            </tr>
        </table>
    @endif

    {{-- Section D: Rincian Kronologis Agenda & Notulensi Pembahasan --}}
    <div class="section-title">D. RINCIAN KRONOLOGIS AGENDA &amp; NOTULENSI PEMBAHASAN</div>
    @if(!empty($items) && count($items) > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th class="center" style="width: 28px;">No</th>
                    <th style="width: 80px;">Waktu &amp; Tanggal</th>
                    <th>Kronologis Agenda / Pembahasan</th>
                    <th style="width: 90px;">PIC Klien</th>
                    <th style="width: 90px;">PIC IPNET</th>
                    <th style="width: 140px;">Catatan Notulensi / Hasil</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $idx => $item)
                    <tr>
                        <td class="center" style="font-weight: bold; color: #8F0A0D;">{{ $item['no'] ?? ($idx + 1) }}</td>
                        <td>
                            <strong>{{ $item['time'] ?: '-' }}</strong><br>
                            <span style="color: #64748B; font-size: 6.8pt;">{{ $item['date'] ?: '-' }}</span>
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
                    <th class="center" style="width: 28px;">No</th>
                    <th style="width: 90px;">Waktu</th>
                    <th>Deskripsi Pembahasan / Catatan Aktivitas</th>
                    <th style="width: 110px;">PIC Klien</th>
                    <th style="width: 110px;">PIC IPNET</th>
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

    {{-- Section E: Identifikasi Kendala, Kompetitor & Potensi Risiko --}}
    @if(!empty($payload['competitors']) || !empty($payload['challenges']) || !empty($payload['win_probability']))
        <div class="section-title">E. IDENTIFIKASI KENDALA, KOMPETITOR &amp; POTENSI RISIKO</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Kompetitor / Brand Pesaing</td>
                <td class="info-value" colspan="3" style="font-weight: bold; color: #8F0A0D;">
                    {{ $payload['competitors'] ?? '-' }}
                </td>
            </tr>
            @if(!empty($payload['challenges']))
                <tr>
                    <td class="info-label" style="vertical-align: top;">Kendala / Isu Kritis Customer</td>
                    <td class="info-value" colspan="3" style="white-space: pre-line;">
                        {{ $payload['challenges'] }}
                    </td>
                </tr>
            @endif
            <tr>
                <td class="info-label">Estimasi Win Probability</td>
                <td class="info-value" colspan="3" style="font-weight: bold; color: #0F172A;">
                    {{ $payload['win_probability'] ?? ($activity->project->win_probability ?? 50) }}%
                </td>
            </tr>
        </table>
    @endif

    {{-- Section F: Rencana Tindak Lanjut Berikutnya --}}
    @if(!empty($activity->next_action) || !empty($payload['next_action_pic']))
        <div class="section-title">F. RENCANA TINDAK LANJUT BERIKUTNYA (NEXT ACTION PLAN &amp; COMMITMENT)</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Komitmen / Aksi Berikutnya</td>
                <td class="info-value" style="font-weight: bold; color: #0F172A;">
                    {{ $activity->next_action ?: '-' }}
                </td>
                <td class="info-label">PIC Pelaksana</td>
                <td class="info-value" style="font-weight: bold;">
                    {{ $payload['next_action_pic'] ?? ($activity->sales->name ?? 'Sales Team') }}
                </td>
            </tr>
            <tr>
                <td class="info-label">Target Due Date</td>
                <td class="info-value" colspan="3" style="font-weight: bold; color: #92400E;">
                    {{ $activity->next_action_date ? \Carbon\Carbon::parse($activity->next_action_date)->isoFormat('dddd, D MMMM Y') : 'Segera' }}
                </td>
            </tr>
        </table>
    @endif

    {{-- Section G: Lembar Pengesahan & Verifikasi (Tanda Tangan) --}}
    <div class="section-title">G. LEMBAR PENGESAHAN &amp; VERIFIKASI (TANDA TANGAN)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 28%;">Pihak</th>
                <th style="width: 26%;">Nama</th>
                <th class="center" style="width: 18%;">Tanggal</th>
                <th class="center" style="width: 28%;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold; background-color: #F8FAFC;">Account Executive (Sales)</td>
                <td style="font-weight: bold;">{{ $activity->sales->name ?? 'Sales Team' }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($activity->activity_date ?? now())->format('d/m/Y') }}</td>
                <td class="center" style="height: 48px; background-color: #FFFFFF;"></td>
            </tr>
            <tr>
                <td style="font-weight: bold; background-color: #F8FAFC;">Division Head / Atasan Sales</td>
                <td style="font-weight: bold; color: #0F172A;">Susanto Djaya</td>
                <td class="center">{{ \Carbon\Carbon::parse($activity->activity_date ?? now())->format('d/m/Y') }}</td>
                <td class="center" style="height: 48px; background-color: #FFFFFF;"></td>
            </tr>
            <tr>
                <td style="font-weight: bold; background-color: #F8FAFC;">Customer / PIC Klien</td>
                <td style="font-weight: bold; color: #334155;">{{ $clientPic ?? ($activity->project->client ?? 'PIC Customer Representative') }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($activity->activity_date ?? now())->format('d/m/Y') }}</td>
                <td class="center" style="height: 48px; background-color: #FFFFFF;"></td>
            </tr>
        </tbody>
    </table>

    {{-- Catatan Footer Dokumen Resmi --}}
    <div class="footer-disclaimer">
        <strong>Catatan Dokumen Resmi:</strong> Dokumen ini diterbitkan secara sah melalui Sistem Manajemen Penjualan PT IP Network Solusindo. Berfungsi sebagai bukti otentik notulensi koordinasi bisnis, berita acara pertemuan komersial, dan rujukan audit operasional sales.
    </div>

    {{-- Footer Dokumen Resmi PT IP Network Solusindo (Standar Engineer) --}}
    <div class="doc-footer">
        <table class="footer-table">
            <tr>
                <td style="width: 65%;">
                    <div style="font-weight: bold; color: #000000; font-size: 6.2pt;">Dokumen ini merupakan milik PT.IP Network Solusindo</div>
                    <div style="color: #334155; margin-top: 1px;">
                        Seluruh isi dokumen hanya dipergunakan untuk kepentingan penyelenggaraan tata kelola perusahaan dan tidak diperkenankan diperbanyak, disalin, dipublikasikan ataupun didistribusikan kepada pihak lain, baik sebagian maupun seluruhnya, tanpa persetujuan tertulis dari Direksi PT.IP Network Solusindo
                    </div>
                </td>
                <td style="width: 35%; text-align: right; vertical-align: top;">
                    <div style="font-weight: bold; color: #000000; font-size: 6.2pt;">&copy;PT.IP Network Solusindo, Seluruh Hak Dilindungi.</div>
                    <div style="margin-top: 2px;">
                        <div class="paraf-box">Paraf</div>
                        <div style="clear: both; margin-top: 2px;"><strong style="font-size: 7.2pt; color: #000000;">Hal 1</strong></div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</div>
</body>
</html>
