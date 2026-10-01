<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Verifikasi &amp; Audit Integritas Dokumen - PT IP Network Solusindo</title>
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
            --danger: #DC2626;
            --danger-light: #FEE2E2;
            --warning: #D97706;
            --warning-light: #FEF3C7;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0F172A 0%, #1E1B4B 45%, #0F172A 100%);
            min-height: 100vh;
            color: var(--gray-800);
            padding: 32px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .container {
            width: 100%;
            max-width: 860px;
            margin: 0 auto;
        }

        /* Top Brand Header */
        .brand-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #FFFFFF;
            padding: 10px 22px;
            border-radius: 9999px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.35);
            margin-bottom: 12px;
        }

        .brand-logo {
            height: 32px;
            width: auto;
        }

        .brand-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--primary-dark);
            letter-spacing: 0.5px;
        }

        .portal-title {
            font-size: 24px;
            font-weight: 800;
            color: #FFFFFF;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }

        .portal-subtitle {
            color: rgba(255, 255, 255, 0.75);
            font-size: 13px;
            max-width: 580px;
            margin: 0 auto;
            line-height: 1.5;
        }

        /* Main Card */
        .main-card {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 24px;
        }

        /* Tabs Navigation */
        .tab-nav {
            display: flex;
            background: var(--gray-100);
            border-bottom: 1px solid var(--gray-200);
            padding: 6px 8px 0;
            gap: 6px;
        }

        .tab-btn {
            flex: 1;
            padding: 12px 16px;
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            color: var(--gray-500);
            background: transparent;
            border: none;
            border-radius: 12px 12px 0 0;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .tab-btn.active {
            background: #FFFFFF;
            color: var(--primary-dark);
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.04);
            border-top: 3px solid var(--primary);
        }

        .card-body {
            padding: 28px 24px;
        }

        /* Dropzone Upload */
        .dropzone {
            border: 2px dashed #CBD5E1;
            border-radius: 16px;
            padding: 36px 20px;
            text-align: center;
            background: #FAFBFD;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .dropzone:hover, .dropzone.dragover {
            border-color: var(--primary);
            background: #FFF5F5;
        }

        .dropzone-icon {
            width: 52px;
            height: 52px;
            margin: 0 auto 12px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .dropzone-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 4px;
        }

        .dropzone-desc {
            font-size: 12.5px;
            color: var(--gray-500);
            margin-bottom: 14px;
        }

        .file-chosen-name {
            display: none;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--primary-dark);
            background: #FEE2E2;
            padding: 6px 14px;
            border-radius: 8px;
            margin-top: 10px;
            display: inline-block;
            font-weight: 600;
        }

        /* Form Group */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--gray-700);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--gray-200);
            border-radius: 10px;
            font-size: 14px;
            font-family: 'JetBrains Mono', monospace;
            color: var(--gray-900);
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(200, 30, 44, 0.1);
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(200, 30, 44, 0.3);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 16px;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(200, 30, 44, 0.4);
        }

        /* Results Card */
        .result-box {
            margin-top: 28px;
            border-radius: 16px;
            overflow: hidden;
            border: 1.5px solid var(--gray-200);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .result-header {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            color: #FFFFFF;
        }

        .result-header.authentic {
            background: linear-gradient(135deg, #15803D 0%, #16A34A 100%);
        }

        .result-header.discrepancy {
            background: linear-gradient(135deg, #991B1B 0%, #DC2626 100%);
        }

        .result-header.not_found {
            background: linear-gradient(135deg, #B45309 0%, #D97706 100%);
        }

        .result-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .result-title {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: 0.3px;
        }

        .result-desc {
            font-size: 12.5px;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 2px;
            line-height: 1.4;
        }

        .result-body {
            padding: 20px;
            background: #FFFFFF;
        }

        /* Discrepancy Table */
        .diff-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            margin-top: 14px;
        }

        .diff-table th {
            background: var(--gray-100);
            color: var(--gray-700);
            text-align: left;
            padding: 10px 12px;
            font-weight: 700;
            border: 1px solid var(--gray-200);
        }

        .diff-table td {
            padding: 10px 12px;
            border: 1px solid var(--gray-200);
            vertical-align: top;
        }

        .diff-row-bad {
            background: #FEF2F2;
        }

        .diff-tag-expected {
            color: #15803D;
            font-weight: 700;
        }

        .diff-tag-found {
            color: #DC2626;
            font-weight: 700;
        }

        /* 3-Tier Signers Preview */
        .signers-preview {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-top: 16px;
        }

        @media (min-width: 640px) {
            .signers-preview {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .signer-badge-card {
            border: 1px solid #86EFAC;
            background: #F0FDF4;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
        }

        .signer-badge-name {
            font-weight: 700;
            font-size: 12.5px;
            color: var(--gray-900);
        }

        .signer-badge-role {
            font-size: 11px;
            color: var(--gray-500);
        }

        .signer-badge-status {
            font-size: 10.5px;
            color: var(--primary);
            font-weight: 700;
            margin-top: 4px;
        }

        /* Action Buttons */
        .actions-row {
            display: flex;
            gap: 10px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .btn-view-pdf {
            background: var(--primary);
            color: #FFFFFF;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-view-pdf:hover {
            background: var(--primary-dark);
        }

        .footer {
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            line-height: 1.5;
            margin-top: auto;
            padding: 16px 0;
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Brand Header -->
        <header class="brand-header">
            <div class="brand-badge">
                @if(file_exists(public_path('images/ipnet.png')))
                    <img src="{{ asset('images/ipnet.png') }}" alt="Logo IP-Net" class="brand-logo">
                @endif
                <span class="brand-title">PT IP NETWORK SOLUSINDO</span>
            </div>
            <h1 class="portal-title">Portal Verifikasi &amp; Audit Integritas Dokumen</h1>
            <p class="portal-subtitle">
                Layanan publik resmi untuk memeriksa keaslian laporan rekapitulasi kerja lapangan dan mendeteksi adanya manipulasi data (Automated Discrepancy Detection).
            </p>
        </header>

        <!-- Main Card -->
        <main class="main-card">
            <!-- Tabs Navigation -->
            <div class="tab-nav">
                <button type="button" class="tab-btn active" id="tabUploadBtn" onclick="switchTab('upload')">
                    📄 Uji File PDF (Deteksi Selisih Otomatis)
                </button>
                <button type="button" class="tab-btn" id="tabDocBtn" onclick="switchTab('number')">
                    🔢 Cek Nomor Dokumen
                </button>
            </div>

            <div class="card-body">
                <!-- Form Uji Dokumen -->
                <form action="{{ route('public.verify.inspect') }}" method="POST" enctype="multipart/form-data" id="verifyForm">
                    @csrf

                    <!-- Mode 1: Upload File PDF -->
                    <div id="uploadSection">
                        <div class="dropzone" id="dropzoneBox" onclick="document.getElementById('pdfInput').click()">
                            <input type="file" name="document_file" id="pdfInput" accept="application/pdf" style="display: none;" onchange="handleFileSelected(this)">
                            <div class="dropzone-icon">📥</div>
                            <div class="dropzone-title">Pilih atau Tarik (Drag &amp; Drop) Berkas PDF Laporan</div>
                            <div class="dropzone-desc">Unggah file PDF yang diserahkan oleh teknisi untuk menguji keabsahan dan mendeteksi perubahan.</div>
                            <div id="fileSelectedBadge" class="file-chosen-name" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- Mode 2: Input Manual Nomor Dokumen -->
                    <div id="numberSection" style="display: none;">
                        <div class="form-group">
                            <label class="form-label">Nomor Dokumen Rekapitulasi Kerja</label>
                            <input type="text" name="document_number" id="docNumberInput" value="{{ $prefillDocNumber ?? old('document_number') }}" placeholder="Contoh: IPNET-ACT-202610-0001" class="form-input">
                            <p style="font-size: 11.5px; color: var(--gray-500); margin-top: 4px;">
                                Masukkan kode dokumen yang tertera di pojok kiri bawah lembar kerja atau di bawah QR Code.
                            </p>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        ⚡ Uji Keabsahan &amp; Deteksi Selisih
                    </button>
                </form>

                <!-- HASIL PEMERIKSAAN OTOMATIS -->
                @if(isset($result) && $result)
                    <div class="result-box">
                        @if($result['status'] === 'authentic')
                            <!-- Status: ASLI & SAH -->
                            <div class="result-header authentic">
                                <div class="result-icon">✓</div>
                                <div>
                                    <div class="result-title">DOKUMEN ASLI &amp; TERVERIFIKASI RESMI</div>
                                    <div class="result-desc">
                                        Data pada berkas identik 100% dengan arsip bertanda tangan digital resmi di server PT IP Network Solusindo. Tidak terdeteksi manipulasi data.
                                    </div>
                                </div>
                            </div>
                            <div class="result-body">
                                <p style="font-size: 13px; color: var(--gray-700); margin-bottom: 10px;">
                                    <strong>Nomor Dokumen:</strong> <span style="font-family: 'JetBrains Mono'; color: var(--primary); font-weight: bold;">{{ $result['document_number'] }}</span>
                                    <br>
                                    <strong>Proyek:</strong> {{ $result['document']?->project_name ?? '-' }}
                                </p>

                                <!-- 3-Tier Signers Preview -->
                                <div style="font-size: 12px; font-weight: 700; color: var(--gray-700); text-transform: uppercase; margin-top: 14px;">
                                    Tanda Tangan Digital Terverifikasi:
                                </div>
                                <div class="signers-preview">
                                    <div class="signer-badge-card">
                                        <div class="signer-badge-name">{{ $result['document']?->pic_name ?? 'PIC Field' }}</div>
                                        <div class="signer-badge-role">PIC Lapangan</div>
                                        <div class="signer-badge-status">Signed ✓</div>
                                    </div>
                                    <div class="signer-badge-card">
                                        <div class="signer-badge-name">{{ $result['document']?->lead_name ?? 'Lead Engineer' }}</div>
                                        <div class="signer-badge-role">Lead Engineer</div>
                                        <div class="signer-badge-status">Verified ✓</div>
                                    </div>
                                    <div class="signer-badge-card">
                                        <div class="signer-badge-name">{{ $result['document']?->head_name ?? 'Susanto Djaya' }}</div>
                                        <div class="signer-badge-role">Head of Division</div>
                                        <div class="signer-badge-status">Approved ✓</div>
                                    </div>
                                </div>

                                <div class="actions-row">
                                    <a href="{{ url('/verify-document/' . $result['document_number']) }}" target="_blank" class="btn-view-pdf">
                                        📄 Buka Dokumen PDF Resmi dari Server
                                    </a>
                                </div>
                            </div>

                        @elseif($result['status'] === 'discrepancy_detected')
                            <!-- Status: TERDETEKSI SELISIH / MODIFIKASI -->
                            <div class="result-header discrepancy">
                                <div class="result-icon">⚠️</div>
                                <div>
                                    <div class="result-title">PERINGATAN: TERDETEKSI SELISIH DATA (DISCREPANCY DETECTED)!</div>
                                    <div class="result-desc">
                                        Berkas PDF yang diunggah <strong>TIDAK COCOK</strong> dengan arsip resmi bertanda tangan digital di server PT IP Network Solusindo. Terdapat indikasi modifikasi di luar sistem.
                                    </div>
                                </div>
                            </div>
                            <div class="result-body">
                                <p style="font-size: 13px; color: var(--gray-900); margin-bottom: 6px;">
                                    <strong>Nomor Dokumen:</strong> <span style="font-family: 'JetBrains Mono'; font-weight: bold;">{{ $result['document_number'] }}</span>
                                    <br>
                                    <strong>Jumlah Perbedaan Ditemukan:</strong> <span style="color: #DC2626; font-weight: bold;">{{ $result['total_discrepancies'] }} Poin Ketidaksesuaian</span>
                                </p>

                                <!-- Tabel Rincian Selisih Data -->
                                <table class="diff-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%;">Bagian / Kolom</th>
                                            <th style="width: 35%;">Data Sah Resmi di Server IP-Net</th>
                                            <th style="width: 40%;">Data di Berkas yang Anda Unggah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($result['discrepancies'] as $diff)
                                            <tr class="diff-row-bad">
                                                <td>
                                                    <strong>{{ $diff['field'] }}</strong>
                                                    <div style="font-size: 11px; color: var(--gray-500);">{{ $diff['desc'] }}</div>
                                                </td>
                                                <td>
                                                    <span class="diff-tag-expected">✓ {{ $diff['expected'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="diff-tag-found">✗ {{ $diff['found_in_file'] }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <div class="actions-row">
                                    <a href="{{ url('/verify-document/' . $result['document_number']) }}" target="_blank" class="btn-view-pdf">
                                        🛡️ Lihat Dokumen Asli Resmi (Server Source of Truth)
                                    </a>
                                </div>
                            </div>

                        @else
                            <!-- Status: TIDAK DITEMUKAN / INVALID -->
                            <div class="result-header not_found">
                                <div class="result-icon">✕</div>
                                <div>
                                    <div class="result-title">DOKUMEN TIDAK TERDAFTAR</div>
                                    <div class="result-desc">
                                        {{ $result['message'] }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </main>

        <!-- Footer -->
        <footer class="footer">
            &copy; {{ date('Y') }} PT IP Network Solusindo • Enterprise Document Integrity System<br>
            Sistem Verifikasi Berbasis Digital Signature &amp; Automated Discrepancy Detection
        </footer>
    </div>

    <script>
        function switchTab(mode) {
            const uploadBtn = document.getElementById('tabUploadBtn');
            const docBtn = document.getElementById('tabDocBtn');
            const uploadSec = document.getElementById('uploadSection');
            const numberSec = document.getElementById('numberSection');

            if (mode === 'upload') {
                uploadBtn.classList.add('active');
                docBtn.classList.remove('active');
                uploadSec.style.display = 'block';
                numberSec.style.display = 'none';
            } else {
                docBtn.classList.add('active');
                uploadBtn.classList.remove('active');
                numberSec.style.display = 'block';
                uploadSec.style.display = 'none';
            }
        }

        function handleFileSelected(input) {
            const badge = document.getElementById('fileSelectedBadge');
            if (input.files && input.files[0]) {
                badge.style.display = 'inline-block';
                badge.textContent = '📄 ' + input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(1) + ' KB)';
            } else {
                badge.style.display = 'none';
            }
        }

        // Drag & drop support
        const dropzone = document.getElementById('dropzoneBox');
        if (dropzone) {
            ['dragenter', 'dragover'].forEach(name => {
                dropzone.addEventListener(name, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('dragover');
                }, false);
            });
            ['dragleave', 'drop'].forEach(name => {
                dropzone.addEventListener(name, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('dragover');
                }, false);
            });
            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files && files[0] && files[0].type === 'application/pdf') {
                    document.getElementById('pdfInput').files = files;
                    handleFileSelected(document.getElementById('pdfInput'));
                }
            });
        }
    </script>
</body>
</html>
