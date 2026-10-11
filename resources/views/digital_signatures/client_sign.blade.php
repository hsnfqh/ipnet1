<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Pengesahan Digital: {{ $document->title }} - PT IP Network Solusindo</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/ipnet1.png') }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Local Vendor Assets with CDN Fallbacks -->
    <script src="{{ asset('vendor/pdfjs/pdf.min.js') }}" onerror="this.src='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js'"></script>
    <script src="{{ asset('vendor/pdf-lib/pdf-lib.min.js') }}" onerror="this.src='https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js'"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}" onerror="this.src='https://cdn.jsdelivr.net/npm/sweetalert2@11'"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0F172A;
        }
        .btn-ipnet-primary {
            background: linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%) !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 14px rgba(143, 10, 13, 0.3) !important;
            transition: all 0.2s ease;
        }
        .btn-ipnet-primary:hover {
            background: linear-gradient(135deg, #C52222 0%, #9C0C0F 60%, #83080A 100%) !important;
            transform: translateY(-1px);
        }
        .pdf-viewport-container {
            background-color: #334155;
            box-shadow: inset 0 0 15px rgba(0,0,0,0.4);
        }
        .pdf-page-canvas {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45);
        }
        .client-signature-box {
            position: absolute;
            border: 2px dashed #0D9488;
            background-color: rgba(20, 184, 166, 0.12);
            cursor: move;
            touch-action: none;
            user-select: none;
            z-index: 25;
            transition: border-color 0.15s ease, background-color 0.15s ease;
        }
        .client-signature-box:hover, .client-signature-box.active {
            border-color: #0F766E;
            background-color: rgba(20, 184, 166, 0.22);
        }
        .client-signature-box .resize-handle {
            position: absolute;
            width: 14px;
            height: 14px;
            background-color: #0F766E;
            border: 2px solid #FFFFFF;
            border-radius: 50%;
            bottom: -7px;
            right: -7px;
            cursor: nwse-resize;
        }
        /* Custom SweetAlert2 Theme */
        .swal2-popup {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            border-radius: 20px !important;
            padding: 1.5rem !important;
        }
        .swal2-title {
            font-size: 1.15rem !important;
            font-weight: 800 !important;
            color: #0F172A !important;
        }
        .swal2-html-container {
            font-size: 0.85rem !important;
            color: #475569 !important;
        }
        .swal2-confirm, .swal2-cancel {
            border-radius: 12px !important;
            font-weight: 700 !important;
            font-size: 0.825rem !important;
            padding: 0.65rem 1.25rem !important;
        }
    </style>
</head>
<body class="min-h-screen text-slate-100 flex flex-col justify-between overflow-x-hidden">

    <!-- TOP HEADER BRANDING -->
    <header class="bg-[#1E293B] border-b border-slate-700 sticky top-0 z-40 shadow-md">
        <div class="max-w-7xl mx-auto px-4 py-2.5 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/ipnet1.png') }}" alt="IPNET Logo" class="h-8 object-contain" onerror="this.src='{{ asset('images/ipnet.png') }}'">
                <div>
                    <div class="font-extrabold text-sm text-white leading-tight">PT IP Network Solusindo</div>
                    <div class="text-[10px] font-bold text-red-400 tracking-wider uppercase">Portal Penandatanganan Dokumen Resmi</div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-950 text-emerald-300 text-xs font-bold border border-emerald-700/60">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="hidden sm:inline">Dokumen Sah &bull;</span> Terverifikasi UU ITE
                </span>
                <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 font-mono text-[11px] font-bold border border-slate-700">
                    {{ $document->document_number }}
                </span>
            </div>
        </div>
    </header>

    @if($document->status === 'completed')
        <!-- TAMPILAN JIKA DOKUMEN SUDAH SELESAI DITANDATANGANI -->
        <main class="max-w-3xl mx-auto px-4 py-12 w-full flex-1">
            <div class="bg-white text-slate-800 border border-slate-200 rounded-3xl p-8 text-center space-y-4 shadow-xl">
                <div class="w-16 h-16 rounded-full bg-emerald-500 text-white flex items-center justify-center mx-auto text-3xl font-black shadow-lg">
                    ✓
                </div>
                <h1 class="text-2xl font-black text-slate-900">Dokumen Telah Resmi Ditandatangani</h1>
                <p class="text-xs sm:text-sm text-slate-600 max-w-lg mx-auto leading-relaxed">
                    Dokumen <strong>{{ $document->title }}</strong> telah resmi disahkan oleh <strong>{{ $document->client_name }}</strong> ({{ $document->client_position }}) pada {{ $document->client_signed_at?->format('d F Y, H:i') }} WIB dan telah ter-stempel secara digital pada berkas PDF.
                </p>
                <div class="pt-4 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('public.digital_signature.verify', $document->verification_hash ?: $document->document_number) }}"
                       class="btn-ipnet-primary px-6 py-2.5 rounded-xl font-bold text-xs inline-flex items-center gap-2">
                        <span>Lihat Bukti Keabsahan & Pengesahan &rarr;</span>
                    </a>
                    <a href="{{ route('digital_signatures.download_pdf', $document->id) }}"
                       class="px-5 py-2.5 rounded-xl border border-slate-300 hover:border-slate-400 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                        Unduh Berkas PDF Ditandatangani
                    </a>
                </div>
            </div>
        </main>
    @else
        <!-- WORKSPACE UTAMA: PANEL KONTROL KIRI + VIEWER PDF KANAN -->
        <main class="flex-1 max-w-[1600px] w-full mx-auto p-3 sm:p-4 lg:p-6 grid grid-cols-1 lg:grid-cols-12 gap-5">

            <!-- SIDEBAR KIRI: FORM IDENTITAS PIC KLIEN & KONTROL (4 KOLOM) -->
            <aside class="lg:col-span-4 space-y-4 order-2 lg:order-1">
                
                <!-- CARD INFO DOKUMEN -->
                <div class="bg-[#1E293B] border border-slate-700 rounded-2xl p-4 shadow-sm space-y-2.5">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="text-[10px] font-bold text-red-400 uppercase tracking-wider block">{{ $document->category }}</span>
                            <h2 class="text-sm font-extrabold text-white leading-snug line-clamp-2">{{ $document->title }}</h2>
                        </div>
                        <a href="{{ route('digital_signatures.download_original', $document->id) }}" 
                           class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[11px] font-semibold border border-slate-700 shrink-0" 
                           title="Unduh Berkas Asli">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                    </div>
                    <div class="text-[11px] text-slate-400 space-y-1 border-t border-slate-700/60 pt-2">
                        @if($document->project_name)
                            <div>Proyek: <strong class="text-slate-200">{{ $document->project_name }}</strong></div>
                        @endif
                        <div>Dibuat oleh: <strong class="text-slate-200">{{ $document->creator?->name }} (PT IP Network Solusindo)</strong></div>
                    </div>
                </div>

                <!-- CARD FORMULIR PIC KLIEN -->
                <div class="bg-[#1E293B] border border-slate-700 rounded-2xl p-4 sm:p-5 shadow-sm space-y-4">
                    <div>
                        <h3 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            Konfirmasi Identitas PIC Klien
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Lengkapi identitas penandatangan yang mewakili instansi Anda.</p>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11.5px] font-bold text-slate-300 mb-1">
                                Nama Lengkap Anda <span class="text-red-400">*</span>
                            </label>
                            <input type="text" id="clientNameInput" value="{{ $document->client_name }}" required
                                   class="w-full px-3 py-2 text-xs rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                                   placeholder="Contoh: Budi Santoso">
                        </div>

                        <div>
                            <label class="block text-[11.5px] font-bold text-slate-300 mb-1">
                                Jabatan / Posisi <span class="text-red-400">*</span>
                            </label>
                            <input type="text" id="clientPositionInput" value="{{ $document->client_position }}" required
                                   class="w-full px-3 py-2 text-xs rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                                   placeholder="Contoh: IT Manager / Site Lead / Project Manager">
                        </div>

                        <div>
                            <label class="block text-[11.5px] font-bold text-slate-300 mb-1">
                                Perusahaan / Instansi Klien
                            </label>
                            <input type="text" id="clientCompanyInput" value="{{ $document->client_company }}"
                                   class="w-full px-3 py-2 text-xs rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                                   placeholder="Contoh: PT Mitra Telekomunikasi">
                        </div>
                    </div>

                    <!-- STATUS & KONTROL PENEMPATAN TANDA TANGAN -->
                    <div class="border-t border-slate-700/60 pt-4 space-y-3">
                        <label class="block text-[11.5px] font-bold text-slate-300">
                            Tanda Tangan Pada Dokumen <span class="text-red-400">*</span>
                        </label>

                        <!-- Box jika belum ada tanda tangan -->
                        <div id="unplacedNoticeBox" class="p-3.5 rounded-xl bg-slate-900 border border-dashed border-slate-600 text-center space-y-2">
                            <p class="text-xs text-slate-400">
                                Anda belum membubuhkan tanda tangan pada lembar dokumen.
                            </p>
                            <button type="button" id="btnSidebarOpenSignature" 
                                    class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Goreskan & Tempatkan Tanda Tangan</span>
                            </button>
                        </div>

                        <!-- Box jika sudah menempatkan tanda tangan -->
                        <div id="placedNoticeBox" class="hidden p-3.5 rounded-xl bg-emerald-950/40 border border-emerald-700/60 space-y-2.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-emerald-400 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    Tanda Tangan Siap Ditempatkan
                                </span>
                                <span id="placedPageBadge" class="px-2 py-0.5 rounded bg-emerald-900 text-emerald-300 font-mono text-[10px] font-bold">Halaman 1</span>
                            </div>
                            <div class="flex items-center gap-3 bg-slate-900/80 p-2 rounded-lg border border-slate-800">
                                <div class="w-16 h-10 bg-white rounded flex items-center justify-center overflow-hidden p-0.5 shrink-0">
                                    <img id="sidebarSignatureThumbnail" class="max-w-full max-h-full object-contain" alt="Tanda Tangan">
                                </div>
                                <div class="text-[11px] text-slate-400 leading-snug">
                                    Geser (drag) kotak tanda tangan pada lembar dokumen di sebelah kanan ke posisi yang diinginkan.
                                </div>
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-1">
                                <button type="button" id="btnSidebarRecreateSig" class="text-[11px] font-bold text-slate-400 hover:text-white underline">
                                    Ganti Goresan
                                </button>
                                <span class="text-slate-600">&bull;</span>
                                <button type="button" id="btnSidebarDeleteSig" class="text-[11px] font-bold text-rose-400 hover:text-rose-300">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- CHECKBOX PERNYATAAN HUKUM ITE -->
                    <div class="border-t border-slate-700/60 pt-3">
                        <label class="flex items-start gap-2.5 cursor-pointer text-[11px] text-slate-300 leading-relaxed select-none">
                            <input type="checkbox" id="agreementCheckbox" required 
                                   class="mt-0.5 rounded bg-slate-900 border-slate-700 text-red-600 focus:ring-red-600">
                            <span>
                                Saya menyatakan dengan sebenarnya bahwa saya sah mewakili pihak klien dan menyetujui seluruh isi dokumen serta tanda tangan digital ini memiliki kekuatan hukum yang sah.
                            </span>
                        </label>
                    </div>

                    <!-- TOMBOL SUBMIT FINAL -->
                    <button type="button" id="btnSubmitClientSign" 
                            class="btn-ipnet-primary w-full py-3 rounded-xl font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg transition active:scale-95">
                        <span id="btnSubmitClientText">Setujui & Sahkan Dokumen Resmi</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </aside>

            <!-- AREA DOKUMEN PDF (8 KOLOM) -->
            <section class="lg:col-span-8 flex flex-col order-1 lg:order-2 bg-[#1E293B] border border-slate-700 rounded-2xl overflow-hidden shadow-xl min-h-[600px] lg:min-h-[820px]">
                
                <!-- TOOLBAR DOKUMEN -->
                <div class="bg-slate-900 px-4 py-2.5 border-b border-slate-700 flex flex-wrap items-center justify-between gap-2.5 shrink-0">
                    
                    <!-- Sisi Kiri: Tombol + Tanda Tangan & Hapus -->
                    <div class="flex items-center gap-2">
                        <button type="button" id="btnToolbarOpenSignature" 
                                class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition active:scale-95 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Tanda Tangan</span>
                        </button>
                        <button type="button" id="btnToolbarDeleteSignature" 
                                class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition active:scale-95 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                                disabled>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus</span>
                        </button>
                    </div>

                    <!-- Sisi Kanan: Zoom & Navigasi Halaman -->
                    <div class="flex items-center gap-2 flex-wrap">
                        
                        <!-- Zoom -->
                        <div class="flex items-center bg-slate-800 rounded-lg p-0.5 border border-slate-700 text-xs">
                            <span id="zoomPercentLabel" class="px-2 text-slate-300 font-mono font-bold text-[11px]">100%</span>
                            <button type="button" id="btnZoomOut" class="p-1 hover:bg-slate-700 rounded text-slate-300" title="Zoom Out (-)">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </button>
                            <button type="button" id="btnZoomIn" class="p-1 hover:bg-slate-700 rounded text-slate-300" title="Zoom In (+)">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                            <button type="button" id="btnZoomReset" class="px-2 py-0.5 hover:bg-slate-700 rounded text-[11px] font-bold text-slate-300" title="Reset Zoom">
                                Reset
                            </button>
                        </div>

                        <!-- Halaman -->
                        <div class="flex items-center bg-slate-800 rounded-lg p-0.5 border border-slate-700 text-xs">
                            <button type="button" id="btnFirstPage" class="p-1 hover:bg-slate-700 rounded text-slate-300" title="Halaman Pertama">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                            </button>
                            <button type="button" id="btnPrevPage" class="p-1 hover:bg-slate-700 rounded text-slate-300" title="Halaman Sebelumnya">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <div class="px-2 text-slate-200 font-bold font-mono text-[11px] flex items-center gap-1">
                                <span id="currentPageNum">1</span> / <span id="totalPagesNum">1</span>
                            </div>
                            <button type="button" id="btnNextPage" class="p-1 hover:bg-slate-700 rounded text-slate-300" title="Halaman Selanjutnya">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <button type="button" id="btnLastPage" class="p-1 hover:bg-slate-700 rounded text-slate-300" title="Halaman Terakhir">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- PETUNJUK INTERAKTIF -->
                <div class="bg-slate-800/80 px-4 py-2 border-b border-slate-700 text-[11px] text-slate-300 flex items-center justify-between">
                    <span>💡 <strong>Petunjuk:</strong> Klik tombol <strong>"Tanda Tangan"</strong> lalu geser (drag & drop) tanda tangan langsung ke kolom tanda tangan Anda.</span>
                    <span class="text-slate-400 hidden md:inline">Resolusi Asli Berkas PDF</span>
                </div>

                <!-- CANVAS VIEWPORT CONTAINER -->
                <div class="pdf-viewport-container flex-1 overflow-auto flex items-center justify-center p-4 sm:p-8 relative min-h-[500px]">
                    
                    <!-- Loading Indicator -->
                    <div id="pdfLoadingState" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/80 z-30">
                        <div class="w-10 h-10 border-3 border-red-500 border-t-transparent rounded-full animate-spin mb-3"></div>
                        <div class="text-sm font-bold text-white">Memuat Dokumen PDF...</div>
                        <div class="text-xs text-slate-400 mt-1">Mengambil berkas resolusi tinggi dari server</div>
                    </div>

                    <!-- Page Wrapper (Menjaga relasi koordinat 1:1 antara Canvas dan Kotak Tanda Tangan) -->
                    <div id="pageWrapper" class="relative inline-block transition-all duration-75">
                        
                        <!-- PDF Canvas -->
                        <canvas id="pdfCanvas" class="pdf-page-canvas block bg-white"></canvas>

                        <!-- Draggable & Resizable Signature Box -->
                        <div id="placedSignatureBox" class="client-signature-box hidden">
                            <div class="absolute -top-5 left-0 bg-[#0F766E] text-white text-[9px] font-bold px-1.5 py-0.5 rounded-t shadow-xs flex items-center gap-1">
                                <span>Tanda Tangan Anda</span>
                            </div>
                            <img id="placedSignatureImg" class="w-full h-full object-contain pointer-events-none select-none" />
                            <div class="resize-handle" title="Tarik untuk mengubah ukuran"></div>
                        </div>

                    </div>
                </div>
            </section>

        </main>
    @endif

    <!-- MODAL GORESKAN TANDA TANGAN -->
    <div id="signatureModal" class="fixed inset-0 bg-black/75 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full text-slate-900 overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in duration-150">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Bubuhkan Goresan Tanda Tangan Anda</h3>
                    <p class="text-xs text-slate-500">Goreskan tanda tangan menggunakan mouse, touchpad, atau jari pada layar sentuh.</p>
                </div>
                <button type="button" id="btnCloseModal" class="text-slate-400 hover:text-slate-700 text-2xl font-bold leading-none">&times;</button>
            </div>

            <div class="p-5 space-y-4">
                <div class="border-2 border-dashed border-slate-300 rounded-2xl bg-slate-50 relative overflow-hidden shadow-inner">
                    <canvas id="modalPadCanvas" width="550" height="220" class="w-full h-52 cursor-crosshair touch-none bg-white block"></canvas>
                    <div class="absolute bottom-2 left-3 text-[10px] text-slate-400 pointer-events-none">
                        Tanda tangan di area ini &bull; Mouse / Sentuhan Layar
                    </div>
                    <button type="button" id="btnClearPad" 
                            class="absolute bottom-2 right-2 px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold shadow-xs">
                        Bersihkan
                    </button>
                </div>

                <div class="text-xs text-slate-500 bg-slate-50 p-3 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span>Penandatangan: <strong id="modalSignerNameDisplay" class="text-slate-800">{{ $document->client_name ?: 'PIC Klien' }}</strong></span>
                    <span class="text-emerald-600 font-bold">✓ Pihak Klien</span>
                </div>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" id="btnCancelModal" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100">
                    Batal
                </button>
                <button type="button" id="btnApplyModalSignature" class="btn-ipnet-primary px-5 py-2 rounded-xl text-white font-bold text-xs shadow-md">
                    Gunakan & Tempatkan di Dokumen &rarr;
                </button>
            </div>
        </div>
    </div>

    <!-- FOOTER RESMI -->
    <footer class="py-4 border-t border-slate-800 bg-[#0B1120] text-center text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>&copy; {{ date('Y') }} PT IP Network Solusindo. All rights reserved.</div>
            <div class="text-[11px] text-slate-500">Sistem Pengesahan Tanda Tangan Elektronik Terverifikasi UU ITE</div>
        </div>
    </footer>

    <!-- SCRIPT CLIENT WORKSPACE LOGIC -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Setup Worker PDF.js
        if (window.pdfjsLib) {
            pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('vendor/pdfjs/pdf.worker.min.js') }}";
        }

        const pdfUrl = @json($pdfUrl);
        const submitUrl = @json(route('client.digital_signature.submit', $document->client_signing_token));
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // State PDF
        let pdfDoc = null;
        let currentPageNum = 1;
        let totalPages = 1;
        let currentScale = 1.0;

        // State Tanda Tangan
        let userSignatureDataUrl = null;
        let userSignatureAspectRatio = 2.2;
        let placedSignature = null; // { page, xPercent, yPercent, widthPercent, heightPercent }
        let isDragging = false;
        let isResizing = false;
        let dragStartX = 0;
        let dragStartY = 0;
        let initialBoxLeft = 0;
        let initialBoxTop = 0;
        let initialBoxWidth = 0;
        let initialBoxHeight = 0;

        // DOM Elements
        const pdfCanvas = document.getElementById('pdfCanvas');
        if (!pdfCanvas) return; // Jika status completed, hentikan script
        const ctx = pdfCanvas.getContext('2d');
        const pageWrapper = document.getElementById('pageWrapper');
        const placedBox = document.getElementById('placedSignatureBox');
        const placedImg = document.getElementById('placedSignatureImg');
        const resizeHandle = placedBox.querySelector('.resize-handle');
        const loadingState = document.getElementById('pdfLoadingState');

        // Toolbar Elements
        const currentPageNumEl = document.getElementById('currentPageNum');
        const totalPagesNumEl = document.getElementById('totalPagesNum');
        const zoomPercentLabel = document.getElementById('zoomPercentLabel');
        const btnToolbarOpenSignature = document.getElementById('btnToolbarOpenSignature');
        const btnToolbarDeleteSignature = document.getElementById('btnToolbarDeleteSignature');
        const btnSidebarOpenSignature = document.getElementById('btnSidebarOpenSignature');
        const btnSidebarRecreateSig = document.getElementById('btnSidebarRecreateSig');
        const btnSidebarDeleteSig = document.getElementById('btnSidebarDeleteSig');
        const unplacedNoticeBox = document.getElementById('unplacedNoticeBox');
        const placedNoticeBox = document.getElementById('placedNoticeBox');
        const placedPageBadge = document.getElementById('placedPageBadge');
        const sidebarSignatureThumbnail = document.getElementById('sidebarSignatureThumbnail');
        const btnSubmitClientSign = document.getElementById('btnSubmitClientSign');
        const btnSubmitClientText = document.getElementById('btnSubmitClientText');

        // Inputs
        const clientNameInput = document.getElementById('clientNameInput');
        const clientPositionInput = document.getElementById('clientPositionInput');
        const clientCompanyInput = document.getElementById('clientCompanyInput');
        const agreementCheckbox = document.getElementById('agreementCheckbox');
        const modalSignerNameDisplay = document.getElementById('modalSignerNameDisplay');

        // Pad Canvas Elements
        const modal = document.getElementById('signatureModal');
        const modalCanvas = document.getElementById('modalPadCanvas');
        const modalCtx = modalCanvas.getContext('2d');
        let isDrawing = false;
        let hasDrawnStroke = false;

        // SweetAlert Helper
        function showNotification(type, title, text) {
            if (window.Swal) {
                return Swal.fire({
                    icon: type,
                    title: title,
                    text: text,
                    confirmButtonColor: '#8F0A0D'
                });
            } else {
                alert(title + ': ' + text);
            }
        }

        // Auto-Crop White Space transparan agar batas kotak 100% presisi dengan tinta tanda tangan
        function cropSignatureCanvas(sourceCanvas) {
            const sCtx = sourceCanvas.getContext('2d');
            const w = sourceCanvas.width;
            const h = sourceCanvas.height;
            const imgData = sCtx.getImageData(0, 0, w, h);
            const data = imgData.data;

            let minX = w, minY = h, maxX = -1, maxY = -1;

            for (let y = 0; y < h; y++) {
                for (let x = 0; x < w; x++) {
                    const alpha = data[(y * w + x) * 4 + 3];
                    if (alpha > 15) {
                        if (x < minX) minX = x;
                        if (x > maxX) maxX = x;
                        if (y < minY) minY = y;
                        if (y > maxY) maxY = y;
                    }
                }
            }

            if (maxX === -1) {
                return { dataUrl: null, width: 0, height: 0, ratio: 2.2 };
            }

            // Margin pengaman 4px di sekeliling goresan tinta
            const pad = 4;
            minX = Math.max(0, minX - pad);
            minY = Math.max(0, minY - pad);
            maxX = Math.min(w - 1, maxX + pad);
            maxY = Math.min(h - 1, maxY + pad);

            const cropW = Math.max(10, maxX - minX + 1);
            const cropH = Math.max(10, maxY - minY + 1);

            const cropCanvas = document.createElement('canvas');
            cropCanvas.width = cropW;
            cropCanvas.height = cropH;
            const cropCtx = cropCanvas.getContext('2d');
            cropCtx.drawImage(sourceCanvas, minX, minY, cropW, cropH, 0, 0, cropW, cropH);

            return {
                dataUrl: cropCanvas.toDataURL('image/png'),
                width: cropW,
                height: cropH,
                ratio: cropW / cropH
            };
        }

        // Muat Berkas PDF
        async function loadPdf() {
            try {
                loadingState.classList.remove('hidden');
                if (!window.pdfjsLib) {
                    throw new Error("Library PDF.js belum selesai dimuat.");
                }
                const loadingTask = pdfjsLib.getDocument({ url: pdfUrl });
                pdfDoc = await loadingTask.promise;
                totalPages = pdfDoc.numPages;
                totalPagesNumEl.textContent = totalPages;
                
                await renderPage(currentPageNum);
                loadingState.classList.add('hidden');
            } catch (err) {
                console.error("Gagal memuat dokumen PDF:", err);
                loadingState.innerHTML = `
                    <div class="text-rose-400 font-bold text-sm mb-2">Gagal Membuka Berkas PDF</div>
                    <div class="text-xs text-slate-300 max-w-sm text-center mb-4">${err.message || 'Berkas tidak dapat diakses atau format tidak valid.'}</div>
                `;
            }
        }

        // Render Halaman ke Canvas
        async function renderPage(num) {
            if (!pdfDoc) return;
            const page = await pdfDoc.getPage(num);
            
            const viewport = page.getViewport({ scale: currentScale });
            const outputScale = window.devicePixelRatio || 1;
            
            pdfCanvas.width = Math.floor(viewport.width * outputScale);
            pdfCanvas.height = Math.floor(viewport.height * outputScale);
            pdfCanvas.style.width = Math.floor(viewport.width) + "px";
            pdfCanvas.style.height = Math.floor(viewport.height) + "px";
            
            pageWrapper.style.width = Math.floor(viewport.width) + "px";
            pageWrapper.style.height = Math.floor(viewport.height) + "px";

            const transform = outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null;

            await page.render({
                canvasContext: ctx,
                transform: transform,
                viewport: viewport
            }).promise;

            currentPageNum = num;
            currentPageNumEl.textContent = currentPageNum;
            updateSignatureBoxVisibility();
        }

        // Update Kotak Penempatan Tanda Tangan
        function updateSignatureBoxVisibility() {
            if (!placedSignature) {
                placedBox.classList.add('hidden');
                btnToolbarDeleteSignature.disabled = true;
                unplacedNoticeBox.classList.remove('hidden');
                placedNoticeBox.classList.add('hidden');
                return;
            }

            unplacedNoticeBox.classList.add('hidden');
            placedNoticeBox.classList.remove('hidden');
            placedPageBadge.textContent = 'Halaman ' + placedSignature.page;
            btnToolbarDeleteSignature.disabled = false;

            if (placedSignature.page === currentPageNum) {
                placedBox.classList.remove('hidden');
                
                const currentWidth = parseFloat(pdfCanvas.style.width);
                const currentHeight = parseFloat(pdfCanvas.style.height);

                const leftPx = (placedSignature.xPercent / 100) * currentWidth;
                const topPx = (placedSignature.yPercent / 100) * currentHeight;
                const widthPx = (placedSignature.widthPercent / 100) * currentWidth;
                const heightPx = (placedSignature.heightPercent / 100) * currentHeight;

                placedBox.style.left = leftPx + 'px';
                placedBox.style.top = topPx + 'px';
                placedBox.style.width = widthPx + 'px';
                placedBox.style.height = heightPx + 'px';
            } else {
                placedBox.classList.add('hidden');
            }
        }

        // Navigasi Halaman
        document.getElementById('btnFirstPage').addEventListener('click', () => {
            if (currentPageNum > 1) renderPage(1);
        });
        document.getElementById('btnPrevPage').addEventListener('click', () => {
            if (currentPageNum > 1) renderPage(currentPageNum - 1);
        });
        document.getElementById('btnNextPage').addEventListener('click', () => {
            if (currentPageNum < totalPages) renderPage(currentPageNum + 1);
        });
        document.getElementById('btnLastPage').addEventListener('click', () => {
            if (currentPageNum < totalPages) renderPage(totalPages);
        });

        // Kontrol Zoom
        function setZoom(newScale) {
            if (newScale < 0.4) newScale = 0.4;
            if (newScale > 2.5) newScale = 2.5;
            currentScale = newScale;
            zoomPercentLabel.textContent = Math.round(currentScale * 100) + '%';
            renderPage(currentPageNum);
        }

        document.getElementById('btnZoomIn').addEventListener('click', () => setZoom(currentScale + 0.15));
        document.getElementById('btnZoomOut').addEventListener('click', () => setZoom(currentScale - 0.15));
        document.getElementById('btnZoomReset').addEventListener('click', () => setZoom(1.0));

        // Drawing Pad Handlers
        function initDrawingPad() {
            modalCtx.lineWidth = 2.6;
            modalCtx.lineCap = 'round';
            modalCtx.lineJoin = 'round';
            modalCtx.strokeStyle = '#0F172A';
        }

        function clearModalPad() {
            modalCtx.clearRect(0, 0, modalCanvas.width, modalCanvas.height);
            hasDrawnStroke = false;
        }
        document.getElementById('btnClearPad').addEventListener('click', clearModalPad);

        function openSignatureModal() {
            modalSignerNameDisplay.textContent = clientNameInput.value.trim() || 'PIC Klien';
            modal.classList.remove('hidden');
            initDrawingPad();
        }

        function closeSignatureModal() {
            modal.classList.add('hidden');
        }

        document.getElementById('btnCloseModal').addEventListener('click', closeSignatureModal);
        document.getElementById('btnCancelModal').addEventListener('click', closeSignatureModal);

        function getMousePos(canvas, evt) {
            const rect = canvas.getBoundingClientRect();
            const clientX = evt.touches ? evt.touches[0].clientX : evt.clientX;
            const clientY = evt.touches ? evt.touches[0].clientY : evt.clientY;
            return {
                x: (clientX - rect.left) * (canvas.width / rect.width),
                y: (clientY - rect.top) * (canvas.height / rect.height)
            };
        }

        function startDrawing(e) {
            e.preventDefault();
            isDrawing = true;
            const pos = getMousePos(modalCanvas, e);
            modalCtx.beginPath();
            modalCtx.moveTo(pos.x, pos.y);
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const pos = getMousePos(modalCanvas, e);
            modalCtx.lineTo(pos.x, pos.y);
            modalCtx.stroke();
            hasDrawnStroke = true;
        }

        function stopDrawing() {
            isDrawing = false;
        }

        modalCanvas.addEventListener('mousedown', startDrawing);
        modalCanvas.addEventListener('mousemove', draw);
        window.addEventListener('mouseup', stopDrawing);

        modalCanvas.addEventListener('touchstart', startDrawing, { passive: false });
        modalCanvas.addEventListener('touchmove', draw, { passive: false });
        window.addEventListener('touchend', stopDrawing);

        // Buka modal tanda tangan dari tombol toolbar & sidebar
        btnToolbarOpenSignature.addEventListener('click', () => {
            if (userSignatureDataUrl) {
                placeSignatureOnCurrentPage();
            } else {
                openSignatureModal();
            }
        });
        btnSidebarOpenSignature.addEventListener('click', openSignatureModal);
        btnSidebarRecreateSig.addEventListener('click', openSignatureModal);

        // Terapkan tanda tangan dari modal
        document.getElementById('btnApplyModalSignature').addEventListener('click', () => {
            if (!hasDrawnStroke) {
                showNotification('warning', 'Tanda Tangan Belum Ada', 'Silakan goreskan tanda tangan Anda pada kanvas terlebih dahulu.');
                return;
            }

            const cropped = cropSignatureCanvas(modalCanvas);
            if (!cropped.dataUrl) {
                showNotification('warning', 'Tanda Tangan Belum Ada', 'Silakan goreskan tanda tangan Anda pada kanvas terlebih dahulu.');
                return;
            }

            userSignatureDataUrl = cropped.dataUrl;
            userSignatureAspectRatio = cropped.ratio;
            sidebarSignatureThumbnail.src = userSignatureDataUrl;
            closeSignatureModal();
            placeSignatureOnCurrentPage();
        });

        // Menempatkan tanda tangan pada halaman aktif
        function placeSignatureOnCurrentPage() {
            placedImg.src = userSignatureDataUrl;
            
            const currentWidth = parseFloat(pdfCanvas.style.width);
            const currentHeight = parseFloat(pdfCanvas.style.height);

            // Ukuran kotak proporsional dengan aspek rasio hasil crop
            const targetPixelWidth = Math.min(180, currentWidth * 0.28);
            const targetPixelHeight = targetPixelWidth / (userSignatureAspectRatio || 2.2);

            const widthPercent = (targetPixelWidth / currentWidth) * 100;
            const heightPercent = (targetPixelHeight / currentHeight) * 100;

            // Default posisi: 55% dari kiri, 68% dari atas
            let defaultXPercent = 55;
            let defaultYPercent = 68;
            if (defaultXPercent + widthPercent > 98) defaultXPercent = 98 - widthPercent;
            if (defaultYPercent + heightPercent > 96) defaultYPercent = 96 - heightPercent;

            placedSignature = {
                page: currentPageNum,
                xPercent: defaultXPercent,
                yPercent: defaultYPercent,
                widthPercent: widthPercent,
                heightPercent: heightPercent
            };

            updateSignatureBoxVisibility();
        }

        // Hapus Penempatan Tanda Tangan
        async function deletePlacement() {
            if (window.Swal) {
                const res = await Swal.fire({
                    title: 'Hapus Penempatan?',
                    text: 'Kotak tanda tangan akan dihapus dari lembar dokumen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#E11D48',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                });
                if (res.isConfirmed) {
                    placedSignature = null;
                    updateSignatureBoxVisibility();
                }
            } else {
                if (confirm('Hapus penempatan tanda tangan dari lembar dokumen?')) {
                    placedSignature = null;
                    updateSignatureBoxVisibility();
                }
            }
        }
        btnToolbarDeleteSignature.addEventListener('click', deletePlacement);
        btnSidebarDeleteSig.addEventListener('click', deletePlacement);

        // DRAG & RESIZE KOTAK TANDA TANGAN
        placedBox.addEventListener('mousedown', (e) => {
            if (e.target.classList.contains('resize-handle')) return;
            e.preventDefault();
            isDragging = true;
            dragStartX = e.clientX;
            dragStartY = e.clientY;
            initialBoxLeft = parseFloat(placedBox.style.left);
            initialBoxTop = parseFloat(placedBox.style.top);
            placedBox.classList.add('active');
        });

        resizeHandle.addEventListener('mousedown', (e) => {
            e.preventDefault();
            e.stopPropagation();
            isResizing = true;
            dragStartX = e.clientX;
            dragStartY = e.clientY;
            initialBoxWidth = parseFloat(placedBox.style.width);
            initialBoxHeight = parseFloat(placedBox.style.height);
        });

        placedBox.addEventListener('touchstart', (e) => {
            if (e.target.classList.contains('resize-handle')) return;
            const touch = e.touches[0];
            isDragging = true;
            dragStartX = touch.clientX;
            dragStartY = touch.clientY;
            initialBoxLeft = parseFloat(placedBox.style.left);
            initialBoxTop = parseFloat(placedBox.style.top);
        }, { passive: false });

        resizeHandle.addEventListener('touchstart', (e) => {
            e.stopPropagation();
            const touch = e.touches[0];
            isResizing = true;
            dragStartX = touch.clientX;
            dragStartY = touch.clientY;
            initialBoxWidth = parseFloat(placedBox.style.width);
            initialBoxHeight = parseFloat(placedBox.style.height);
        }, { passive: false });

        window.addEventListener('mousemove', (e) => {
            handlePointerMove(e.clientX, e.clientY);
        });

        window.addEventListener('touchmove', (e) => {
            if (isDragging || isResizing) {
                const touch = e.touches[0];
                handlePointerMove(touch.clientX, touch.clientY);
            }
        }, { passive: false });

        function handlePointerMove(clientX, clientY) {
            const currentWidth = parseFloat(pdfCanvas.style.width);
            const currentHeight = parseFloat(pdfCanvas.style.height);

            if (isDragging) {
                const deltaX = clientX - dragStartX;
                const deltaY = clientY - dragStartY;

                let newLeft = initialBoxLeft + deltaX;
                let newTop = initialBoxTop + deltaY;

                const boxW = parseFloat(placedBox.style.width);
                const boxH = parseFloat(placedBox.style.height);

                if (newLeft < 0) newLeft = 0;
                if (newTop < 0) newTop = 0;
                if (newLeft + boxW > currentWidth) newLeft = currentWidth - boxW;
                if (newTop + boxH > currentHeight) newTop = currentHeight - boxH;

                placedBox.style.left = newLeft + 'px';
                placedBox.style.top = newTop + 'px';

                if (placedSignature) {
                    placedSignature.xPercent = (newLeft / currentWidth) * 100;
                    placedSignature.yPercent = (newTop / currentHeight) * 100;
                }
            } else if (isResizing) {
                const deltaX = clientX - dragStartX;

                let newW = initialBoxWidth + deltaX;
                if (newW < 45) newW = 45;

                const left = parseFloat(placedBox.style.left);
                const top = parseFloat(placedBox.style.top);

                if (left + newW > currentWidth) newW = currentWidth - left;

                // Kunci rasio aspek tanda tangan
                const ratio = userSignatureAspectRatio || (initialBoxWidth / initialBoxHeight) || 2.2;
                let newH = newW / ratio;
                if (top + newH > currentHeight) {
                    newH = currentHeight - top;
                    newW = newH * ratio;
                }

                placedBox.style.width = newW + 'px';
                placedBox.style.height = newH + 'px';

                if (placedSignature) {
                    placedSignature.widthPercent = (newW / currentWidth) * 100;
                    placedSignature.heightPercent = (newH / currentHeight) * 100;
                }
            }
        }

        window.addEventListener('mouseup', () => {
            isDragging = false;
            isResizing = false;
            placedBox.classList.remove('active');
        });
        window.addEventListener('touchend', () => {
            isDragging = false;
            isResizing = false;
        });

        // SUBMIT PENGESAHAN DOKUMEN OLEH PIC KLIEN
        btnSubmitClientSign.addEventListener('click', async () => {
            const clientName = clientNameInput.value.trim();
            const clientPosition = clientPositionInput.value.trim();
            const clientCompany = clientCompanyInput.value.trim();
            const agreed = agreementCheckbox.checked;

            if (!clientName) {
                showNotification('warning', 'Nama Belum Lengkap', 'Silakan lengkapi Nama Lengkap Anda.');
                clientNameInput.focus();
                return;
            }
            if (!clientPosition) {
                showNotification('warning', 'Jabatan Belum Lengkap', 'Silakan lengkapi Jabatan/Posisi Anda.');
                clientPositionInput.focus();
                return;
            }
            if (!agreed) {
                showNotification('warning', 'Persetujuan Diperlukan', 'Silakan centang persetujuan keabsahan dokumen terlebih dahulu.');
                agreementCheckbox.focus();
                return;
            }
            if (!placedSignature || !userSignatureDataUrl) {
                showNotification('warning', 'Tanda Tangan Belum Ada', 'Silakan klik tombol "Tanda Tangan" dan tempatkan tanda tangan Anda pada dokumen terlebih dahulu.');
                return;
            }

            // Modal Konfirmasi SweetAlert2
            if (window.Swal) {
                const confirmRes = await Swal.fire({
                    title: 'Sahkan Dokumen Resmi?',
                    html: `Anda akan membubuhkan tanda tangan resmi atas nama <strong>${clientName}</strong> (${clientPosition}) pada <strong>Halaman ${placedSignature.page}</strong> berkas dokumen ini.<br><small class="text-slate-400 mt-2 block">Tanda tangan akan dicap secara permanen dan sah demi hukum.</small>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#8F0A0D',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: 'Ya, Sahkan Dokumen',
                    cancelButtonText: 'Periksa Kembali'
                });
                if (!confirmRes.isConfirmed) return;
            } else {
                if (!confirm(`Konfirmasi: Sahkan dokumen resmi atas nama ${clientName}?`)) return;
            }

            btnSubmitClientSign.disabled = true;
            btnSubmitClientText.textContent = 'Memproses PDF...';

            if (window.Swal) {
                Swal.fire({
                    title: 'Memproses Dokumen Resmi...',
                    html: 'Sedang menempelkan tanda tangan resmi ke berkas PDF.<br><span class="text-xs text-slate-400">Mohon tunggu sebentar...</span>',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => { Swal.showLoading(); }
                });
            }

            try {
                const PDFLibInstance = window.PDFLib || (typeof PDFLib !== 'undefined' ? PDFLib : null);
                if (!PDFLibInstance || !PDFLibInstance.PDFDocument) {
                    throw new Error("Library PDFLib belum siap. Silakan muat ulang halaman (F5) dan coba lagi.");
                }

                // 1. Ambil PDF asli
                const pdfBytes = await fetch(pdfUrl).then(res => res.arrayBuffer());

                // 2. Load PDF via pdf-lib
                const pdfLibDoc = await PDFLibInstance.PDFDocument.load(pdfBytes);
                
                // Ambil halaman target
                const pageIndex = placedSignature.page - 1;
                const targetPage = pdfLibDoc.getPage(pageIndex);
                const { width: pagePdfWidth, height: pagePdfHeight } = targetPage.getSize();

                // Origin offset MediaBox/CropBox
                const originX = (typeof targetPage.getX === 'function') ? targetPage.getX() : 0;
                const originY = (typeof targetPage.getY === 'function') ? targetPage.getY() : 0;

                // 3. Embed tanda tangan crop presisi
                const signatureImage = await pdfLibDoc.embedPng(userSignatureDataUrl);

                // 4. Konversi koordinat UI (%) ke PDF points presisi 1:1
                const xInPdf = originX + ((placedSignature.xPercent / 100) * pagePdfWidth);
                const widthInPdf = (placedSignature.widthPercent / 100) * pagePdfWidth;
                const heightInPdf = (placedSignature.heightPercent / 100) * pagePdfHeight;
                const yInPdf = originY + (pagePdfHeight - ((placedSignature.yPercent / 100) * pagePdfHeight) - heightInPdf);

                // 5. Gambar tanda tangan langsung ke PDF
                targetPage.drawImage(signatureImage, {
                    x: xInPdf,
                    y: yInPdf,
                    width: widthInPdf,
                    height: heightInPdf
                });

                // 6. Simpan hasil PDF
                const modifiedPdfBytes = await pdfLibDoc.save();

                btnSubmitClientText.textContent = 'Mengunggah Data...';

                // 7. Siapkan FormData
                const formData = new FormData();
                formData.append('client_name', clientName);
                formData.append('client_position', clientPosition);
                formData.append('client_company', clientCompany);
                formData.append('signature', userSignatureDataUrl);
                formData.append('agreement', '1');
                formData.append('signed_pdf', new Blob([modifiedPdfBytes], { type: 'application/pdf' }), 'client_signed.pdf');
                formData.append('placement', JSON.stringify(placedSignature));

                const res = await fetch(submitUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await res.json();
                if (data.success) {
                    if (window.Swal) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Pengesahan Berhasil!',
                            text: 'Terima kasih, dokumen resmi telah berhasil disahkan secara digital dan tercatat dalam sistem verifikasi.',
                            confirmButtonColor: '#8F0A0D'
                        });
                    } else {
                        alert('Pengesahan Berhasil! Terima kasih.');
                    }
                    window.location.reload();
                } else {
                    if (window.Swal) {
                        Swal.fire({ icon: 'error', title: 'Gagal Menyimpan', text: data.message || 'Terjadi kesalahan sistem.', confirmButtonColor: '#8F0A0D' });
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                    }
                    btnSubmitClientSign.disabled = false;
                    btnSubmitClientText.textContent = 'Setujui & Sahkan Dokumen Resmi';
                }
            } catch (err) {
                console.error(err);
                if (window.Swal) {
                    Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: err.message || 'Gagal memproses dokumen PDF.', confirmButtonColor: '#8F0A0D' });
                } else {
                    alert('Terjadi kesalahan: ' + err.message);
                }
                btnSubmitClientSign.disabled = false;
                btnSubmitClientText.textContent = 'Setujui & Sahkan Dokumen Resmi';
            }
        });

        // Inisialisasi awal
        loadPdf();
    });
    </script>
</body>
</html>
