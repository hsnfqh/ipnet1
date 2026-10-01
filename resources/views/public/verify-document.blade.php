<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen Resmi - PT IP Network Solusindo</title>
    <link rel="icon" type="image/png" href="{{ asset('images/ipnet.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #C81E2C;
            --primary-dark: #8F0A0D;
            --primary-light: #FEE2E2;
            --dark: #0F172A;
            --gray-900: #0F172A;
            --gray-800: #1E293B;
            --gray-700: #334155;
            --gray-600: #475569;
            --gray-500: #64748B;
            --gray-400: #94A3B8;
            --gray-200: #E2E8F0;
            --gray-100: #F1F5F9;
            --gray-50: #F8FAFC;
            --success: #16A34A;
            --success-light: #DCFCE7;
            --warning: #D97706;
            --warning-light: #FEF3C7;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0F172A 0%, #1E1B4B 50%, #0F172A 100%);
            min-height: 100vh;
            color: var(--gray-800);
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .container {
            width: 100%;
            max-width: 760px;
            margin: 0 auto;
        }

        /* Top Brand Header */
        .brand-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .brand-logo-container {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #FFFFFF;
            padding: 10px 18px;
            border-radius: 9999px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            margin-bottom: 12px;
        }

        .brand-logo {
            height: 32px;
            width: auto;
            margin-right: 10px;
        }

        .brand-name {
            font-size: 15px;
            font-weight: 800;
            color: var(--primary-dark);
            letter-spacing: 0.5px;
        }

        .system-tag {
            color: rgba(255, 255, 255, 0.75);
            font-size: 12px;
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Main Card */
        .card {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 24px;
        }

        /* Status Banner */
        .status-banner {
            padding: 24px 20px;
            text-align: center;
            position: relative;
        }

        .status-banner.valid {
            background: linear-gradient(135deg, #15803D 0%, #16A34A 100%);
            color: #FFFFFF;
        }

        .status-banner.pending {
            background: linear-gradient(135deg, #B45309 0%, #D97706 100%);
            color: #FFFFFF;
        }

        .status-banner.invalid {
            background: linear-gradient(135deg, #991B1B 0%, #DC2626 100%);
            color: #FFFFFF;
        }

        .status-icon {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .status-title {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .status-subtitle {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.9);
            max-width: 480px;
            margin: 0 auto;
            line-height: 1.5;
        }

        /* Card Content */
        .card-body {
            padding: 24px 20px;
        }

        /* Meta Grid */
        .meta-list {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px 0;
            border-bottom: 1px solid #E2E8F0;
            font-size: 13px;
        }

        .meta-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .meta-row:first-child {
            padding-top: 0;
        }

        .meta-label {
            color: var(--gray-500);
            font-weight: 500;
            width: 40%;
        }

        .meta-value {
            color: var(--gray-900);
            font-weight: 700;
            width: 60%;
            text-align: right;
            word-break: break-word;
        }

        .meta-value.highlight {
            color: var(--primary);
            font-family: 'JetBrains Mono', monospace;
            font-size: 14px;
        }

        /* Section Heading */
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }

        .section-header-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--gray-900);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-header-badge {
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 9999px;
            margin-left: 10px;
        }

        /* Signers Timeline Grid */
        .signers-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
            margin-bottom: 24px;
        }

        @media (min-width: 640px) {
            .signers-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .signer-card {
            background: #FFFFFF;
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: 14px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        .signer-card.signed {
            border-color: #86EFAC;
            background: #F0FDF4;
        }

        .signer-tier-badge {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray-500);
            margin-bottom: 6px;
        }

        .signer-signature-box {
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 8px 0;
            background: #FFFFFF;
            border-radius: 8px;
            border: 1px dashed var(--gray-200);
            padding: 4px;
        }

        .signer-signature-img {
            max-height: 52px;
            max-width: 100%;
            object-fit: contain;
        }

        .signer-signature-placeholder {
            font-size: 11px;
            color: var(--gray-400);
            font-style: italic;
        }

        .signer-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 2px;
        }

        .signer-title {
            font-size: 11px;
            color: var(--gray-500);
            margin-bottom: 6px;
        }

        .signer-status-badge {
            display: inline-block;
            font-size: 10.5px;
            font-weight: 700;
            color: var(--primary);
            background: var(--primary-light);
            padding: 3px 8px;
            border-radius: 6px;
            margin-top: auto;
        }

        .signer-status-badge.verified {
            color: #15803D;
            background: #DCFCE7;
        }

        /* Hash Block */
        .hash-box {
            background: #0F172A;
            color: #E2E8F0;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 24px;
        }

        .hash-title {
            font-size: 11px;
            font-weight: 700;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .hash-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            word-break: break-all;
            color: #38BDF8;
            line-height: 1.6;
        }

        /* Security Info Banner */
        .security-info {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            background: #FEF2F2;
            border-left: 4px solid var(--primary);
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 12px;
            color: #991B1B;
            line-height: 1.5;
            margin-bottom: 24px;
        }

        .security-info-icon {
            font-size: 16px;
            line-height: 1;
        }

        /* Action Buttons */
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        @media (min-width: 640px) {
            .btn-group {
                flex-direction: row;
            }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            flex: 1;
        }

        .btn-primary {
            background: var(--primary);
            color: #FFFFFF;
            border: 1px solid var(--primary-dark);
            box-shadow: 0 4px 12px rgba(200, 30, 44, 0.25);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--gray-100);
            color: var(--gray-700);
            border: 1px solid var(--gray-200);
        }

        .btn-secondary:hover {
            background: var(--gray-200);
            color: var(--gray-900);
        }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: auto;
            padding: 16px 0;
            line-height: 1.5;
        }

        .footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Brand Header -->
        <header class="brand-header">
            <div class="brand-logo-container">
                @if(file_exists(public_path('images/ipnet.png')))
                    <img src="{{ asset('images/ipnet.png') }}" alt="Logo IP-Net" class="brand-logo">
                @endif
                <span class="brand-name">PT IP NETWORK SOLUSINDO</span>
            </div>
            <div class="system-tag">Sistem Verifikasi Integritas Dokumen &amp; Digital Signature</div>
        </header>

        <!-- Main Card -->
        <main class="card">
            @if($document)
                <!-- Status Banner -->
                @if($document->status === 'fully_approved')
                    <div class="status-banner valid">
                        <div class="status-icon">✓</div>
                        <h1 class="status-title">Dokumen Sah &amp; Terverifikasi</h1>
                        <p class="status-subtitle">
                            Integritas rekapitulasi kerja ini telah divalidasi secara kriptografis dan disetujui lengkap oleh seluruh penanggung jawab berwenang.
                        </p>
                    </div>
                @else
                    <div class="status-banner pending">
                        <div class="status-icon">⏳</div>
                        <h1 class="status-title">Proses Penandatanganan Berjenjang</h1>
                        <p class="status-subtitle">
                            Dokumen terdaftar namun masih dalam tahapan verifikasi otorisasi berjenjang (Lead Engineer / Head Division).
                        </p>
                    </div>
                @endif

                <div class="card-body">
                    <!-- Metadata List -->
                    <div class="meta-list">
                        <div class="meta-row">
                            <span class="meta-label">Nomor Dokumen</span>
                            <span class="meta-value highlight">{{ $document->document_number }}</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Nama Proyek / Scope</span>
                            <span class="meta-value">{{ $document->project_name ?? ($document->project?->name ?? 'Kegiatan Lapangan Terjadwal') }}</span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Status Keabsahan</span>
                            <span class="meta-value" style="color: {{ $document->status === 'fully_approved' ? '#16A34A' : '#D97706' }};">
                                {{ $document->status === 'fully_approved' ? 'Sah & Lengkap (3 Tingkat Otorisasi)' : 'Dalam Proses Signing' }}
                            </span>
                        </div>
                        <div class="meta-row">
                            <span class="meta-label">Waktu Validasi Terakhir</span>
                            <span class="meta-value">{{ $document->updated_at ? $document->updated_at->format('d F Y, H:i') . ' WIB' : '-' }}</span>
                        </div>
                    </div>

                    <!-- 3-Tier Multi-Signature Cards -->
                    <div class="section-header">
                        <h2 class="section-header-title">Otorisasi &amp; Tanda Tangan Digital</h2>
                        <span class="section-header-badge">3-Tier Verification</span>
                    </div>

                    <div class="signers-grid">
                        <!-- Step 1: PIC -->
                        <div class="signer-card {{ !empty($document->pic_signature) ? 'signed' : '' }}">
                            <div class="signer-tier-badge">Tingkat 1 • Pembuat</div>
                            <div class="signer-signature-box">
                                @if(!empty($document->pic_signature))
                                    <img src="{{ $document->pic_signature }}" alt="TTD PIC" class="signer-signature-img">
                                @else
                                    <span class="signer-signature-placeholder">Belum TTD</span>
                                @endif
                            </div>
                            <div class="signer-name">{{ $document->pic_name ?? 'PIC Field Engineer' }}</div>
                            <div class="signer-title">{{ $document->pic_title ?? 'Field Engineer' }}</div>
                            @if(!empty($document->pic_signed_at))
                                <div class="signer-status-badge verified">
                                    Signed: {{ $document->pic_signed_at->format('d/m/Y H:i') }}
                                </div>
                            @else
                                <div class="signer-status-badge">Menunggu TTD</div>
                            @endif
                        </div>

                        <!-- Step 2: Lead -->
                        <div class="signer-card {{ !empty($document->lead_signature) ? 'signed' : '' }}">
                            <div class="signer-tier-badge">Tingkat 2 • Pemeriksa</div>
                            <div class="signer-signature-box">
                                @if(!empty($document->lead_signature))
                                    <img src="{{ $document->lead_signature }}" alt="TTD Lead" class="signer-signature-img">
                                @else
                                    <span class="signer-signature-placeholder">Belum TTD</span>
                                @endif
                            </div>
                            <div class="signer-name">{{ $document->lead_name ?? 'Lead Engineer' }}</div>
                            <div class="signer-title">{{ $document->lead_title ?? 'Lead Network Engineer' }}</div>
                            @if(!empty($document->lead_signed_at))
                                <div class="signer-status-badge verified">
                                    Verified: {{ $document->lead_signed_at->format('d/m/Y H:i') }}
                                </div>
                            @else
                                <div class="signer-status-badge">Menunggu TTD</div>
                            @endif
                        </div>

                        <!-- Step 3: Head Div -->
                        <div class="signer-card {{ !empty($document->head_signature) ? 'signed' : '' }}">
                            <div class="signer-tier-badge">Tingkat 3 • Otorisasi</div>
                            <div class="signer-signature-box">
                                @if(!empty($document->head_signature))
                                    <img src="{{ $document->head_signature }}" alt="TTD Head" class="signer-signature-img">
                                @else
                                    <span class="signer-signature-placeholder">Belum TTD</span>
                                @endif
                            </div>
                            <div class="signer-name">{{ $document->head_name ?? 'Head of Division' }}</div>
                            <div class="signer-title">{{ $document->head_title ?? 'Division Head' }}</div>
                            @if(!empty($document->head_signed_at))
                                <div class="signer-status-badge verified">
                                    Approved: {{ $document->head_signed_at->format('d/m/Y H:i') }}
                                </div>
                            @else
                                <div class="signer-status-badge">Menunggu TTD</div>
                            @endif
                        </div>
                    </div>

                    <!-- Hash Box -->
                    @if(!empty($document->verification_hash))
                        <div class="hash-box">
                            <div class="hash-title">
                                <span>Cryptographic Fingerprint (SHA-256)</span>
                                <span style="font-size: 10px; color: #34D399;">INTEGRITY LOCKED</span>
                            </div>
                            <div class="hash-code">{{ $document->verification_hash }}</div>
                        </div>
                    @endif

                    <!-- Security Info Note -->
                    <div class="security-info">
                        <span class="security-info-icon">🛡️</span>
                        <div>
                            <strong>Jaminan Keaslian Dokumen:</strong> Dokumen ini dilindungi secara digital oleh sistem verifikasi integritas PT IP Network Solusindo. Segala perubahan manual terhadap isi rekapitulasi kerja di luar sistem akan membatalkan validitas tanda tangan kriptografis ini.
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="btn-group">
                        @if($document->project_name)
                            <a href="{{ url('/engineer/activity-logs/export/pdf?scope_key=' . urlencode($document->scope_key) . '&project_name=' . urlencode($document->project_name)) }}" class="btn btn-primary" target="_blank">
                                📄 Unduh Laporan PDF Resmi
                            </a>
                        @endif
                        <a href="{{ url('/') }}" class="btn btn-secondary">
                            Kembali ke Portal Sistem
                        </a>
                    </div>
                </div>

            @else
                <!-- Document Not Found -->
                <div class="status-banner invalid">
                    <div class="status-icon">✕</div>
                    <h1 class="status-title">Dokumen Tidak Ditemukan</h1>
                    <p class="status-subtitle">
                        Nomor dokumen <strong>{{ $documentNumber }}</strong> belum terdaftar atau belum pernah diterbitkan pada sistem resmi PT IP Network Solusindo.
                    </p>
                </div>

                <div class="card-body" style="text-align: center;">
                    <p style="font-size: 13.5px; color: var(--gray-600); line-height: 1.6; margin-bottom: 20px;">
                        Pastikan Anda memindai QR Code dari dokumen rekapitulasi kerja resmi yang telah dicetak dari sistem internal PT IP Network Solusindo. Jika Anda merasa ini adalah kekeliruan, silakan hubungi tim administrasi operasional.
                    </p>
                    <a href="{{ url('/') }}" class="btn btn-secondary" style="max-width: 240px; margin: 0 auto;">
                        Kembali ke Beranda
                    </a>
                </div>
            @endif
        </main>

        <!-- Footer -->
        <footer class="footer">
            &copy; {{ date('Y') }} PT IP Network Solusindo • Field System Management<br>
            Jl. Raya Pasar Minggu No. 17A, Jakarta Selatan
        </footer>
    </div>

</body>
</html>
