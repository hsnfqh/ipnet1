<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Pengesahan: {{ $document->document_number }}</title>
    <style>
        @page {
            margin: 20mm 18mm 18mm 18mm;
            size: a4 portrait;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #1e293b;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #8F0A0D;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #8F0A0D;
            letter-spacing: 0.5px;
        }
        .company-sub {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
        .doc-title {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
            margin-bottom: 4px;
            letter-spacing: 0.8px;
        }
        .doc-sub {
            text-align: center;
            font-size: 10px;
            color: #64748b;
            margin-bottom: 18px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
            font-size: 11px;
        }
        .info-table td {
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
        }
        .info-label {
            background-color: #f8fafc;
            font-weight: bold;
            width: 25%;
            color: #334155;
        }
        .info-val {
            width: 75%;
            color: #0f172a;
        }
        .section-header {
            font-size: 11.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #8F0A0D;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .signatures-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px;
        }
        .sig-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            background-color: #ffffff;
            min-height: 160px;
        }
        .sig-box-title {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            color: #475569;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .sig-img {
            max-height: 65px;
            max-width: 150px;
            margin: 6px auto;
            display: block;
        }
        .sig-name {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 6px;
            text-decoration: underline;
        }
        .sig-pos {
            font-size: 9.5px;
            color: #64748b;
        }
        .sig-time {
            font-size: 8.5px;
            color: #16a34a;
            font-weight: bold;
            margin-top: 3px;
        }
        .verification-footer {
            border: 1px dashed #94a3b8;
            background-color: #f8fafc;
            border-radius: 6px;
            padding: 10px 14px;
            margin-top: 15px;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-table td {
            vertical-align: middle;
        }
        .qr-img {
            width: 75px;
            height: 75px;
        }
        .legal-notice {
            font-size: 8.5px;
            color: #64748b;
            line-height: 1.35;
            margin-left: 12px;
        }
    </style>
</head>
<body>

    <!-- KOP RESMI IPNET -->
    <table class="header-table">
        <tr>
            <td style="width: 70px;">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" style="height: 48px;" alt="IPNET">
                @else
                    <div style="font-weight: bold; font-size: 20px; color: #8F0A0D;">IPNET</div>
                @endif
            </td>
            <td>
                <div class="company-name">PT IP NETWORK SOLUSINDO</div>
                <div class="company-sub">Telecommunication, IT Infrastructure & Networking Solutions Provider</div>
                <div class="company-sub">Wisma 46 Kota BNI, Jl. Jend. Sudirman Kav. 1, Jakarta Pusat | www.ipnetsolusindo.com</div>
            </td>
            <td style="text-align: right; width: 140px;">
                <div style="font-size: 9px; font-weight: bold; color: #64748b;">KODE VERIFIKASI</div>
                <div style="font-family: monospace; font-size: 9.5px; font-weight: bold; color: #8F0A0D;">{{ $document->document_number }}</div>
            </td>
        </tr>
    </table>

    <!-- JUDUL SERTIFIKAT PENGESAHAN -->
    <div class="doc-title">LEMBAR PENGESAHAN DOKUMEN ELEKTRONIK</div>
    <div class="doc-sub">Certificate of Official Digital Signatures & Electronic Verification</div>

    <!-- TABEL INFORMASI DOKUMEN -->
    <table class="info-table">
        <tr>
            <td class="info-label">Nomor Dokumen</td>
            <td class="info-val"><strong>{{ $document->document_number }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Judul Dokumen</td>
            <td class="info-val">{{ $document->title }}</td>
        </tr>
        <tr>
            <td class="info-label">Kategori / Jenis</td>
            <td class="info-val">{{ $document->category }}</td>
        </tr>
        <tr>
            <td class="info-label">Proyek Terkait</td>
            <td class="info-val">{{ $document->project_name ?: 'Non-Proyek / Penugasan Khusus' }}</td>
        </tr>
        <tr>
            <td class="info-label">Nama Berkas Asli</td>
            <td class="info-val">{{ $document->file_name }} ({{ number_format($document->file_size / 1024, 1) }} KB)</td>
        </tr>
        <tr>
            <td class="info-label">Status Keabsahan</td>
            <td class="info-val">
                @if($document->status === 'completed')
                    <span style="color: #16a34a; font-weight: bold;">TERVERIFIKASI LENGKAP & SAH HUKUM</span>
                @else
                    <span style="color: #d97706; font-weight: bold;">DALAM PROSES PENGESAHAN</span>
                @endif
            </td>
        </tr>
    </table>

    <!-- PENGESAHAN TANDA TANGAN -->
    <div class="section-header">DAFTAR PENGESAHAN TANDA TANGAN ELEKTRONIK</div>

    <table class="signatures-table">
        <tr>
            <!-- PIHAK INTERNAL (KIRI) -->
            <td>
                <div class="sig-box">
                    <div class="sig-box-title">PIHAK PERTAMA (PT IP NETWORK SOLUSINDO)</div>
                    @php
                        $firstInternal = ($document->internal_signers ?? [])[0] ?? null;
                    @endphp
                    @if($firstInternal && !empty($firstInternal['signature']))
                        <img src="{{ $firstInternal['signature'] }}" class="sig-img" alt="Internal Signature">
                    @else
                        <div style="height: 65px; line-height: 65px; color: #94a3b8; font-style: italic; font-size: 10px;">
                            (Tanda Tangan Belum Dibubuhkan)
                        </div>
                    @endif
                    <div class="sig-name">{{ $firstInternal['name'] ?? 'Tim IPNET' }}</div>
                    <div class="sig-pos">{{ $firstInternal['role_title'] ?? 'Engineer Pelaksana' }}</div>
                    @if(!empty($firstInternal['signed_at']))
                        <div class="sig-time">Ditandatangani: {{ \Carbon\Carbon::parse($firstInternal['signed_at'])->format('d/m/Y H:i') }} WIB</div>
                    @endif
                </div>
            </td>

            <!-- PIHAK KLIEN (KANAN) -->
            <td>
                <div class="sig-box">
                    <div class="sig-box-title">PIHAK KEDUA (CUSTOMER / KLIEN)</div>
                    @if($document->client_signature)
                        <img src="{{ $document->client_signature }}" class="sig-img" alt="Client Signature">
                    @else
                        <div style="height: 65px; line-height: 65px; color: #94a3b8; font-style: italic; font-size: 10px;">
                            (Menunggu Tanda Tangan Klien)
                        </div>
                    @endif
                    <div class="sig-name">{{ $document->client_name ?: 'PIC Klien' }}</div>
                    <div class="sig-pos">{{ $document->client_position ?: 'Penanggung Jawab' }} - {{ $document->client_company ?: 'Klien' }}</div>
                    @if($document->client_signed_at)
                        <div class="sig-time">Ditandatangani: {{ $document->client_signed_at->format('d/m/Y H:i') }} WIB</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- JIKA ADA PENANDATANGAN INTERNAL LEBIH DARI 1 (CONTOH: TEAM LEADER / PM) --}}
    @if(count($document->internal_signers ?? []) > 1)
        <table class="signatures-table" style="margin-top: -10px;">
            <tr>
                @foreach(array_slice($document->internal_signers, 1) as $extraSigner)
                    <td>
                        <div class="sig-box">
                            <div class="sig-box-title">MENGETAHUI / TEAM LEADER (IPNET)</div>
                            @if(!empty($extraSigner['signature']))
                                <img src="{{ $extraSigner['signature'] }}" class="sig-img" alt="Leader Signature">
                            @else
                                <div style="height: 65px; line-height: 65px; color: #94a3b8; font-style: italic; font-size: 10px;">
                                    (Pending)
                                </div>
                            @endif
                            <div class="sig-name">{{ $extraSigner['name'] }}</div>
                            <div class="sig-pos">{{ $extraSigner['role_title'] }}</div>
                            @if(!empty($extraSigner['signed_at']))
                                <div class="sig-time">Ditandatangani: {{ \Carbon\Carbon::parse($extraSigner['signed_at'])->format('d/m/Y H:i') }} WIB</div>
                            @endif
                        </div>
                    </td>
                @endforeach
            </tr>
        </table>
    @endif

    <!-- QR CODE & LEGAL FOOTER -->
    <div class="verification-footer">
        <table class="footer-table">
            <tr>
                <td style="width: 80px; text-align: center;">
                    @if($qrPngBase64)
                        <img src="{{ $qrPngBase64 }}" class="qr-img" alt="QR Verification">
                    @endif
                </td>
                <td>
                    <div class="legal-notice">
                        <strong style="color: #0f172a;">KEABSAHAN HUKUM TANDA TANGAN ELEKTRONIK:</strong><br>
                        Dokumen ini diterbitkan dan ditandatangani secara elektronik menggunakan sistem digital resmi PT IP Network Solusindo. Sesuai dengan <strong>Undang-Undang No. 11 Tahun 2008 Pasal 5 Ayat 1</strong> dan <strong>PP No. 71 Tahun 2019</strong> tentang Penyelenggaraan Sistem dan Transaksi Elektronik, informasi elektronik dan/atau dokumen elektronik dan/atau hasil cetaknya merupakan alat bukti hukum yang sah.<br>
                        <span style="font-family: monospace; font-size: 8px; color: #475569;">
                            Verification URL: {{ $verifyUrl }}<br>
                            Fingerprint Hash: {{ $document->verification_hash ?: 'PENDING' }}
                        </span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
