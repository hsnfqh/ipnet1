@php
    if (!function_exists('formatSafeDate')) {
        function formatSafeDate($val, $format = 'd/m/Y', $default = '-') {
            if (empty($val)) return $default;
            if ($val instanceof \Carbon\Carbon || $val instanceof \DateTimeInterface) {
                return $val->format($format);
            }
            try {
                return \Carbon\Carbon::parse($val)->format($format);
            } catch (\Throwable $e) {
                return is_scalar($val) ? (string)$val : $default;
            }
        }
    }
    if (!function_exists('isDatePastSafe')) {
        function isDatePastSafe($val) {
            if (empty($val)) return false;
            if ($val instanceof \Carbon\Carbon) {
                return $val->isPast();
            }
            try {
                return \Carbon\Carbon::parse($val)->isPast();
            } catch (\Throwable $e) {
                return false;
            }
        }
    }

    $customerName = trim((string)($customerName ?? ($project->client ?: '')));
    if (empty($customerName)) {
        $customerName = 'Customer / Klien';
    }
    $customerPic = trim((string)($customerPic ?? ($project->customer_pic ?: ($project->customer_pic_technical ?: ''))));
    if (empty($customerPic)) {
        $customerPic = $customerName . ' Representative';
    }
    $totalRecords = (int) ($totalRecords ?? (isset($assets) ? count($assets) : 0));
    $totalPhysicalUnits = (int) ($totalPhysicalUnits ?? $totalRecords);
    $activeSupportCount = (int) ($activeSupportCount ?? 0);
    $eolEosCount = (int) ($eolEosCount ?? 0);
    $backedUpCount = (int) ($backedUpCount ?? 0);
    $docNumber = $docNumber ?? ('MS-AST/' . date('Y/m/') . str_pad($project->id ?? 1, 4, '0', STR_PAD_LEFT));
    $printedAt = $printedAt ?? (date('d F Y H:i') . ' WIB');
    $printedBy = $printedBy ?? (auth()->user()->name ?? 'Administrator Managed Service');
    $assets = $assets ?? collect([]);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Aset &amp; Spesifikasi Perangkat - {{ $project->name ?? 'Customer' }} - PT IP Network Solusindo</title>
    <style>
        /* ── Konfigurasi Halaman & Margin DomPDF Sesuai Standar Resmi IPNET ── */
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

        /* ── Running Header (Muncul di Setiap Halaman) ── */
        header.page-header {
            position: fixed;
            top: -19mm;
            left: 0;
            right: 0;
            height: 15mm;
            border-bottom: 1.5px solid #000000;
            padding-bottom: 3px;
        }

        /* ── Running Footer (Muncul di Setiap Halaman) ── */
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
            max-width: 95px;
            max-height: 36px;
            display: block;
        }
        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
            line-height: 1.2;
        }
        .doc-sub-corp {
            font-size: 6.8pt;
            font-weight: bold;
            color: #8F0A0D;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            text-align: center;
            margin-top: 1px;
        }

        /* ── Intro & Judul Section Formal ── */
        .section-title {
            font-size: 7.6pt;
            font-weight: bold;
            color: #000000;
            text-transform: uppercase;
            margin-top: 6px;
            margin-bottom: 3px;
            letter-spacing: 0.2px;
        }

        /* ── Tabel Standar Resmi (Header #EBF3FB, Border Hitam Bersih) ── */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
            table-layout: fixed;
            page-break-inside: auto;
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
            font-size: 6.6pt;
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

        /* ── Badge Box Formal B/W Compatible ── */
        .badge-box {
            display: inline-block;
            font-size: 5.9pt;
            font-weight: bold;
            padding: 1px 4px;
            border: 1px solid #000000;
            background-color: #FFFFFF;
            color: #000000;
            text-align: center;
            border-radius: 2px;
        }
        .badge-active { background-color: #E8F5E9; border-color: #2E7D32; color: #1B5E20; }
        .badge-warning { background-color: #FFF8E1; border-color: #F57F17; color: #B78103; }
        .badge-danger { background-color: #FFEBEE; border-color: #C62828; color: #B71C1C; }
        .badge-backup { background-color: #E0F2FE; border-color: #0284C7; color: #0369A1; }

        /* ── Signature Table Persis Standar Formal ── */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 8px;
            page-break-inside: avoid;
        }
        .sig-table th {
            background-color: #EBF3FB;
            color: #000000;
            font-weight: bold;
            font-size: 6.8pt;
            padding: 3.5px 4px;
            border: 1px solid #000000;
            text-align: center;
            width: 33.33%;
        }
        .sig-table td {
            border: 1px solid #000000;
            padding: 3px 5px;
            text-align: center;
            vertical-align: bottom;
            background-color: #FFFFFF;
        }
        .sig-space {
            height: 38px;
        }
        .sig-name {
            font-weight: bold;
            font-size: 7.4pt;
            color: #000000;
            text-decoration: underline;
        }
        .sig-role {
            font-size: 6.3pt;
            color: #000000;
            margin-top: 1px;
            line-height: 1.15;
        }

        .page-number:before {
            content: counter(page);
        }
    </style>
</head>
<body>

    {{-- RUNNING HEADER --}}
    <header class="page-header">
        <table class="doc-header-table">
            <tr>
                <td style="width: 20%; text-align: left;">
                    @if(!empty($logoBase64))
                        <img src="{{ $logoBase64 }}" class="doc-logo" alt="IPNET">
                    @else
                        <span style="font-size: 13pt; font-weight: bold; color: #8F0A0D;">IP-NET</span>
                    @endif
                </td>
                <td style="width: 58%; text-align: center;">
                    <div class="doc-title">DAFTAR ASET &amp; SPESIFIKASI PERANGKAT CUSTOMER</div>
                    <div class="doc-sub-corp">MANAGED SERVICE DELIVERY &bull; PT IP NETWORK SOLUSINDO</div>
                </td>
                <td style="width: 22%; text-align: right; font-size: 6.2pt; color: #000000; line-height: 1.25;">
                    <div><strong>No. Dok:</strong> {{ $docNumber }}</div>
                    <div><strong>Tanggal:</strong> {{ date('d/m/Y') }}</div>
                    <div><strong>Sifat:</strong> Terbatas / Rahasia</div>
                </td>
            </tr>
        </table>
    </header>

    {{-- RUNNING FOOTER --}}
    <footer class="page-footer">
        <table style="width: 100%; border-collapse: collapse; border: none; font-size: 6.2pt; color: #000000;">
            <tr>
                <td style="width: 38%; text-align: left;">
                    PT IP Network Solusindo &bull; Service Delivery &amp; Network Operations
                </td>
                <td style="width: 32%; text-align: center; font-style: italic;">
                    Dokumen Resmi Inventaris &amp; Spesifikasi Perangkat Klien
                </td>
                <td style="width: 30%; text-align: right;">
                    Hal. <span class="page-number"></span> &bull; Dicetak: {{ $printedAt }}
                </td>
            </tr>
        </table>
    </footer>

    {{-- A. INFORMASI PROYEK & CUSTOMER --}}
    <div class="section-title">A. IDENTITAS PROYEK &amp; PROFIL CUSTOMER</div>
    <table class="report-table">
        <tbody>
            <tr>
                <td class="label-cell" style="width: 16%;">Nama Proyek Managed Service</td>
                <td style="width: 34%; font-weight: bold;">{{ $project->name ?? 'Proyek Managed Service' }}</td>
                <td class="label-cell" style="width: 16%;">Nama Customer / Klien</td>
                <td style="width: 34%; font-weight: bold; color: #003366;">{{ $customerName }}</td>
            </tr>
            <tr>
                <td class="label-cell">Lokasi Penempatan / Site</td>
                <td>{{ $project->location ?? ($assets->first()->location_site ?? 'Data Center Customer') }}</td>
                <td class="label-cell">Person in Charge (PIC) Customer</td>
                <td>{{ $customerPic }}</td>
            </tr>
            <tr>
                <td class="label-cell">Level Kontrak / SLA Tier</td>
                <td>
                    <span class="badge-box font-bold">{{ $project->sla_tier ? strtoupper($project->sla_tier) : 'GOLD (STANDARD)' }}</span>
                    &bull; Target Uptime: <strong>{{ number_format($project->sla_target_percentage ?: 99.5, 2) }}%</strong>
                </td>
                <td class="label-cell">Cakupan Layanan Support</td>
                <td>
                    24/7 Service Desk &bull; Preventive &amp; Corrective Maintenance
                </td>
            </tr>
        </tbody>
    </table>

    {{-- B. RINGKASAN REKAPITULASI ASET PERANGKAT --}}
    <div class="section-title">B. REKAPITULASI INVENTARIS &amp; KESIAPAN PERANGKAT</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 20%;">Total Unit Fisik</th>
                <th style="width: 20%;">Perangkat Terdaftar</th>
                <th style="width: 20%;">Support Vendor Active</th>
                <th style="width: 20%;">Siklus EOL / EOS Warning</th>
                <th style="width: 20%;">Arsip Backup Config</th>
            </tr>
        </thead>
        <tbody>
            <tr class="text-center font-bold" style="font-size: 8.5pt;">
                <td style="color: #8F0A0D; padding: 4px;">{{ $totalPhysicalUnits }} Unit</td>
                <td style="color: #003366;">{{ $totalRecords }} Tipe / Model</td>
                <td style="color: #1B5E20;">{{ $activeSupportCount }} Unit (Active)</td>
                <td style="color: {{ $eolEosCount > 0 ? '#B71C1C' : '#000000' }};">
                    {{ $eolEosCount }} {{ $eolEosCount > 0 ? 'Perlu Peremajaan' : 'Semua Aman' }}
                </td>
                <td style="color: #0369A1;">{{ $backedUpCount }} Unit Tersimpan</td>
            </tr>
        </tbody>
    </table>

    {{-- C. DAFTAR LENGKAP SPESIFIKASI & STATUS PERANGKAT CUSTOMER --}}
    <div class="section-title">C. DAFTAR SPESIFIKASI &amp; STATUS PERANGKAT CUSTOMER</div>
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 9%;">Tipe</th>
                <th style="width: 15%;">Brand &amp; Nama Perangkat / Model</th>
                <th style="width: 12%;">Serial Number</th>
                <th style="width: 10%;">OS Version</th>
                <th style="width: 10%;">IP Address</th>
                <th style="width: 5%;">Qty</th>
                <th style="width: 13%;">Support Vendor (SLA/PAR)</th>
                <th style="width: 8%;">EOL / EOS</th>
                <th style="width: 7%;">Backup</th>
                <th style="width: 8%;">Lokasi / Rak</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $index => $asset)
                @php
                    $isEol = isDatePastSafe($asset->eol_date);
                    $isEos = isDatePastSafe($asset->eos_date);
                    $isBackedUp = strtolower((string)($asset->backup_status ?? '')) === 'backed up' || !empty($asset->backup_file_path);
                    $supportType = $asset->service_support_type ?: 'PAR-SD';
                    $expSupport = !empty($asset->support_expiry_date) 
                        ? formatSafeDate($asset->support_expiry_date, 'd M Y', '-') 
                        : formatSafeDate($asset->warranty_expiry, 'd M Y', '-');
                @endphp
                <tr>
                    <td class="text-center font-bold">{{ $index + 1 }}</td>
                    <td class="text-center">
                        <span class="badge-box">{{ $asset->category ?: 'Switch' }}</span>
                    </td>
                    <td>
                        <strong>{{ $asset->model ?: ($asset->device_name ?: 'Perangkat Customer') }}</strong>
                        <div style="font-size: 5.8pt; color: #475569;">{{ $asset->brand ?: 'Standard' }}</div>
                    </td>
                    <td class="text-left font-bold" style="font-family: 'Courier New', monospace; font-size: 6.4pt;">
                        {{ $asset->serial_number ?: '-' }}
                    </td>
                    <td class="text-center" style="font-family: 'Courier New', monospace; font-size: 6.2pt;">
                        {{ $asset->os_version ?: '-' }}
                    </td>
                    <td class="text-center font-bold" style="font-family: 'Courier New', monospace; font-size: 6.2pt; color: #8F0A0D;">
                        {{ $asset->ip_address ?: '-' }}
                    </td>
                    <td class="text-center font-bold">
                        {{ $asset->quantity ?: 1 }}
                    </td>
                    <td>
                        <span class="badge-box badge-active">{{ $asset->effective_support_status }}</span>
                        <span class="badge-box" style="margin-left: 2px;">{{ $supportType }}</span>
                        <div style="font-size: 5.8pt; color: #475569; margin-top: 1px;">Exp: {{ $expSupport }}</div>
                    </td>
                    <td class="text-center" style="font-size: 6pt;">
                        @if($isEol || $isEos)
                            <span class="badge-box badge-danger">EOL/EOS</span>
                        @else
                            <div>EOL: {{ formatSafeDate($asset->eol_date, 'd/m/y', '-') }}</div>
                            <div>EOS: {{ formatSafeDate($asset->eos_date, 'd/m/y', '-') }}</div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($isBackedUp)
                            <span class="badge-box badge-backup">Backed Up</span>
                        @else
                            <span class="badge-box badge-warning">Not Backed</span>
                        @endif
                    </td>
                    <td class="text-left" style="font-size: 6pt;">
                        {{ Str::limit($asset->location_site ?: ($asset->rack_position ?: '-'), 24) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 12px; color: #64748B;">
                        Belum ada data perangkat yang terdaftar untuk proyek / klien ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- D. CATATAN OPERASIONAL & KETENTUAN PEMELIHARAAN --}}
    <div class="section-title">D. KETENTUAN OPERASIONAL &amp; PEMELIHARAAN (SLA &amp; MAINTENANCE)</div>
    <table class="report-table">
        <tbody>
            <tr>
                <td style="padding: 4px 6px; font-size: 6.3pt; line-height: 1.3;">
                    <strong>1. Preventive Maintenance:</strong> Pemeliharaan berkala mencakup pembersihan fisik, pengecekan temperatur suhu rack, utilisasi port/resource, dan validasi jalur kabel dilakukan terjadwal.<br>
                    <strong>2. Corrective Maintenance &amp; RMA Hardware:</strong> Perangkat dengan status garansi Vendor Active dilindungi penggantian unit pengganti (PAR-ND / PAR-SD) sesuai batas waktu SLA tanggap darurat.<br>
                    <strong>3. Siklus Hidup EOL / EOS:</strong> Perangkat yang telah mencapai masa End-of-Life (EOL) atau End-of-Support (EOS) direkomendasikan untuk program peremajaan (refreshment) guna mencegah risiko operasional.<br>
                    <strong>4. Keamanan Backup Konfigurasi:</strong> Seluruh konfigurasi sistem operasi (running config) diarsipkan secara terenkripsi di server pusat PT IP Network Solusindo untuk disaster recovery.
                </td>
            </tr>
        </tbody>
    </table>

    {{-- E. LEMBAR PENGESAHAN DOKUMEN (3 PIHAK FORMAL) --}}
    <div class="section-title">E. LEMBAR PENGESAHAN DOKUMEN (VERIFIKASI &amp; PERSETUJUAN)</div>
    <table class="sig-table">
        <thead>
            <tr>
                <th>Disusun &amp; Diverifikasi Oleh:</th>
                <th>Diketahui Oleh:</th>
                <th>Disetujui Oleh:</th>
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
                    <div class="sig-name">Susanto Djaya</div>
                    <div class="sig-role">Head of Managed Service<br>PT IP Network Solusindo</div>
                </td>
                <td>
                    <div class="sig-space"></div>
                    <div class="sig-name">{{ $customerPic }}</div>
                    <div class="sig-role">Customer Representative &bull; {{ $customerName }}<br>{{ $project->name ?? 'Proyek Customer' }}</div>
                </td>
            </tr>
        </tbody>
    </table>

</body>
</html>