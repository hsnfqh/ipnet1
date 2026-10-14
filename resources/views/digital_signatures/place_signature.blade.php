<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tempatkan Tanda Tangan - {{ $document->title }} - PT IP Network Solusindo</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/ipnet1.png') }}">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Local Vendor Assets with CDN Fallback -->
    <script src="{{ asset('vendor/pdfjs/pdf.min.js') }}" onerror="this.src='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js'"></script>
    <script src="{{ asset('vendor/pdf-lib/pdf-lib.min.js') }}" onerror="this.src='https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js'"></script>
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}" onerror="this.src='https://cdn.jsdelivr.net/npm/sweetalert2@11'"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
        }
        .pdf-viewport-container {
            background-color: #E2E8F0;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.06);
        }
        .pdf-page-canvas {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
        .signature-box {
            position: absolute;
            border: 2px dashed #8F0A0D;
            background-color: rgba(143, 10, 13, 0.12);
            cursor: move;
            touch-action: none;
            user-select: none;
            z-index: 20;
            transition: border-color 0.15s ease;
        }
        .signature-box:hover, .signature-box.active {
            border-color: #B91C1C;
            background-color: rgba(185, 28, 28, 0.22);
        }
        .signature-box .resize-handle {
            position: absolute;
            width: 14px;
            height: 14px;
            background-color: #8F0A0D;
            border: 2px solid #FFFFFF;
            border-radius: 50%;
            bottom: -7px;
            right: -7px;
            cursor: nwse-resize;
        }
        .btn-ipnet-primary {
            background: linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%) !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 12px rgba(143, 10, 13, 0.25) !important;
        }
        .btn-ipnet-primary:hover {
            background: linear-gradient(135deg, #C52222 0%, #9C0C0F 60%, #83080A 100%) !important;
        }
        /* Custom SweetAlert2 Styling */
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
<body class="h-screen flex flex-col text-slate-800 bg-[#F8FAFC] overflow-hidden select-none">

    <!-- TOP CONTROL BAR (LIGHT THEME ELEGAN) -->
    <header class="bg-white border-b border-slate-200 px-4 py-2.5 flex flex-wrap items-center justify-between gap-3 shrink-0 z-30 shadow-xs">
        
        <!-- Sisi Kiri: Kembali & Info Dokumen -->
        <div class="flex items-center gap-3">
            <a href="{{ route('digital_signatures.show', $document->id) }}" 
               class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition flex items-center gap-1.5 text-xs font-bold"
               title="Kembali ke Detail Dokumen">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span class="hidden sm:inline">Kembali</span>
            </a>
            <div class="h-5 w-px bg-slate-200"></div>
            <div>
                <h1 class="text-sm font-black text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Tempatkan Tanda Tangan</span>
                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-[11px] font-bold hidden md:inline border border-slate-200">{{ $document->document_number }}</span>
                </h1>
                <p class="text-[11px] text-slate-500 truncate max-w-xs sm:max-w-md">{{ $document->title }}</p>
            </div>
        </div>

        <!-- Tengah: Tombol Aksi Tanda Tangan & Zoom & Halaman -->
        <div class="flex items-center gap-2 flex-wrap">
            
            <!-- Tombol Tanda Tangan (Merah Gradien IPNET) -->
            <button type="button" id="btnPlaceSignature" 
                    class="btn-ipnet-primary px-3.5 py-1.5 rounded-lg text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Tanda Tangan</span>
            </button>

            <!-- Tombol Hapus (Rose Border) -->
            <button type="button" id="btnDeleteSignature" 
                    class="px-3 py-1.5 rounded-lg bg-white hover:bg-rose-50 text-rose-700 border border-rose-300 font-bold text-xs flex items-center gap-1.5 shadow-2xs transition active:scale-95 cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                    disabled>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Hapus</span>
            </button>

            <div class="h-5 w-px bg-slate-200 mx-1"></div>

            <!-- Kontrol Zoom -->
            <div class="flex items-center bg-white rounded-lg p-0.5 border border-slate-200 text-xs shadow-2xs">
                <span id="zoomPercentLabel" class="px-2 text-slate-700 font-mono font-bold text-[11px]">100%</span>
                <button type="button" id="btnZoomOut" class="p-1 hover:bg-slate-100 rounded text-slate-600" title="Zoom Out (-)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                </button>
                <button type="button" id="btnZoomIn" class="p-1 hover:bg-slate-100 rounded text-slate-600" title="Zoom In (+)">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </button>
                <button type="button" id="btnZoomReset" class="px-2 py-0.5 hover:bg-slate-100 rounded text-[11px] font-bold text-slate-600" title="Reset Zoom">
                    Reset
                </button>
            </div>

            <div class="h-5 w-px bg-slate-200 mx-1"></div>

            <!-- Navigasi Halaman -->
            <div class="flex items-center bg-white rounded-lg p-0.5 border border-slate-200 text-xs shadow-2xs">
                <button type="button" id="btnFirstPage" class="p-1 hover:bg-slate-100 rounded text-slate-600" title="Halaman Pertama">
                    &laquo;
                </button>
                <button type="button" id="btnPrevPage" class="p-1 hover:bg-slate-100 rounded text-slate-600" title="Halaman Sebelumnya">
                    &lsaquo;
                </button>
                <div class="px-2 flex items-center gap-1 font-mono text-[11px] font-bold">
                    <span id="currentPageNum" class="text-slate-900">1</span>
                    <span class="text-slate-400">/</span>
                    <span id="totalPagesNum" class="text-slate-600">1</span>
                </div>
                <button type="button" id="btnNextPage" class="p-1 hover:bg-slate-100 rounded text-slate-600" title="Halaman Berikutnya">
                    &rsaquo;
                </button>
                <button type="button" id="btnLastPage" class="p-1 hover:bg-slate-100 rounded text-slate-600" title="Halaman Terakhir">
                    &raquo;
                </button>
            </div>
        </div>

        <!-- Sisi Kanan: Submit Button -->
        <div class="flex items-center gap-2">
            <button type="button" id="btnSubmitFinal"
                    class="btn-ipnet-primary px-4 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-2 cursor-pointer shadow-md transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span id="btnSubmitText">Simpan & Tanda Tangani</span>
            </button>
        </div>
    </header>

    <!-- WORKSPACE AREA: VIEWER CANVAS (SESUAI STYLE PDF VIEWER / IPB DiSign) -->
    <main class="flex-1 overflow-auto pdf-viewport-container p-4 sm:p-8 flex items-start justify-center relative" id="pdfScrollContainer">
        
        <!-- Loading Spinner -->
        <div id="pdfLoadingState" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-100/90 z-20">
            <div class="w-10 h-10 border-4 border-slate-300 border-t-[#8F0A0D] rounded-full animate-spin mb-3"></div>
            <div class="text-xs font-bold text-slate-800 tracking-wide">Memuat Berkas PDF Dokumen...</div>
            <div class="text-[11px] text-slate-500 mt-1">Menyiapkan halaman interaktif</div>
        </div>

        <!-- Page Wrapper: Tempat Canvas & Draggable Signature Box -->
        <div id="pageWrapper" class="relative inline-block transition-transform duration-100 ease-out">
            <canvas id="pdfCanvas" class="pdf-page-canvas bg-white block rounded-sm"></canvas>
            
            <!-- Draggable Signature Box Overlay (Muncul saat user klik + Tanda Tangan) -->
            <div id="placedSignatureBox" class="signature-box rounded-md hidden shadow-lg" style="left: 60%; top: 75%; width: 170px; height: 80px;">
                <div class="absolute -top-6 left-0 bg-teal-800 text-white text-[10px] font-bold px-2 py-0.5 rounded-t shadow flex items-center gap-1 pointer-events-none">
                    <svg class="w-3 h-3 text-teal-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    <span>Tanda Tangan: {{ $user->name }}</span>
                </div>
                
                <img id="placedSignatureImg" src="" alt="Tanda Tangan" class="w-full h-full object-contain pointer-events-none select-none p-1">
                
                <!-- Handle resize pojok kanan bawah -->
                <div class="resize-handle" title="Tarik untuk mengubah ukuran"></div>
            </div>
        </div>
    </main>

    <!-- MODAL / POPUP: GORES TANDA TANGAN (JIKA BELUM MEMILIKI GORESAN) -->
    <div id="signatureModal" class="fixed inset-0 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-2xl max-w-lg w-full text-slate-900 overflow-hidden shadow-2xl border border-slate-200 animate-in fade-in zoom-in duration-150">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Bubuhkan Goresan Tanda Tangan</h3>
                    <p class="text-xs text-slate-500">Goreskan tanda tangan Anda pada kanvas di bawah.</p>
                </div>
                <button type="button" id="btnCloseModal" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
            </div>

            <div class="p-5 space-y-4">
                <div class="border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 relative overflow-hidden">
                    <canvas id="modalPadCanvas" width="500" height="200" class="w-full h-48 cursor-crosshair touch-none bg-white"></canvas>
                    <div class="absolute bottom-2 left-2 text-[10px] text-slate-400 pointer-events-none">
                        Tanda tangani di sini &bull; Menggunakan mouse / sentuhan jari
                    </div>
                    <button type="button" id="btnClearPad" 
                            class="absolute bottom-2 right-2 px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold shadow-xs">
                        Bersihkan
                    </button>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-500">
                    <span>Penandatangan: <strong>{{ $user->name }}</strong></span>
                    <span>Status: Internal IPNET</span>
                </div>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" id="btnCancelModal" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100">
                    Batal
                </button>
                <button type="button" id="btnApplyModalSignature" class="btn-ipnet-primary px-5 py-2 rounded-xl text-white font-bold text-xs shadow-md">
                    Gunakan & Tempatkan &rarr;
                </button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT LOGIC (PDF RENDERING, DRAGGING, RESIZING, DAN EMBEDDING PDF) -->
    <script>
        // Setup PDF.js Worker
        if (window.pdfjsLib) {
            pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('vendor/pdfjs/pdf.worker.min.js') }}";
        }

        const pdfUrl = @json($pdfUrl);
        const docId = @json($document->id);
        const submitUrl = @json(route('digital_signatures.sign_internal', $document->id));
        const redirectUrl = @json(route('digital_signatures.show', $document->id));
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Helper notifikasi SweetAlert2 profesional
        function showNotification(type, title, text) {
            if (window.Swal) {
                return Swal.fire({
                    icon: type,
                    title: title,
                    text: text,
                    confirmButtonColor: '#8F0A0D'
                });
            } else {
                alert(title + "\n" + text);
                return Promise.resolve();
            }
        }

        // State PDF
        let pdfDoc = null;
        let currentPageNum = 1;
        let totalPages = 1;
        let currentScale = 1.0;
        let baseViewportWidth = 0;
        let baseViewportHeight = 0;

        // State Tanda Tangan
        let userSignatureDataUrl = null; // Base64 transparent PNG
        let userSignatureAspectRatio = 2.2; // Rasio aspek goresan tanda tangan
        let placedSignature = null; // { page, xPercent, yPercent, widthPercent, heightPercent }
        let isDragging = false;
        let isResizing = false;
        let dragStartX = 0;
        let dragStartY = 0;
        let initialBoxLeft = 0;
        let initialBoxTop = 0;
        let initialBoxWidth = 0;
        let initialBoxHeight = 0;

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

        // DOM Elements
        const pdfCanvas = document.getElementById('pdfCanvas');
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
        const btnDeleteSignature = document.getElementById('btnDeleteSignature');
        const btnPlaceSignature = document.getElementById('btnPlaceSignature');
        const btnSubmitFinal = document.getElementById('btnSubmitFinal');
        const btnSubmitText = document.getElementById('btnSubmitText');

        // Pad Canvas Elements
        const modal = document.getElementById('signatureModal');
        const modalCanvas = document.getElementById('modalPadCanvas');
        const modalCtx = modalCanvas.getContext('2d');
        let isDrawing = false;
        let hasDrawnStroke = false;

        // Inisialisasi PDF
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
                    <a href="${redirectUrl}" class="px-4 py-2 bg-slate-700 rounded-xl text-xs font-bold text-white hover:bg-slate-600">Kembali</a>
                `;
            }
        }

        // Render Halaman ke Canvas
        async function renderPage(num) {
            if (!pdfDoc) return;
            const page = await pdfDoc.getPage(num);
            
            // Dapatkan viewport asli (scale 1.0)
            const unscaledViewport = page.getViewport({ scale: 1.0 });
            baseViewportWidth = unscaledViewport.width;
            baseViewportHeight = unscaledViewport.height;

            // Viewport dengan zoom saat ini
            const viewport = page.getViewport({ scale: currentScale });
            
            // Set ukuran canvas
            const outputScale = window.devicePixelRatio || 1;
            pdfCanvas.width = Math.floor(viewport.width * outputScale);
            pdfCanvas.height = Math.floor(viewport.height * outputScale);
            pdfCanvas.style.width = Math.floor(viewport.width) + "px";
            pdfCanvas.style.height = Math.floor(viewport.height) + "px";
            
            pageWrapper.style.width = Math.floor(viewport.width) + "px";
            pageWrapper.style.height = Math.floor(viewport.height) + "px";

            const transform = outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null;

            const renderContext = {
                canvasContext: ctx,
                transform: transform,
                viewport: viewport
            };

            await page.render(renderContext).promise;

            currentPageNum = num;
            currentPageNumEl.textContent = currentPageNum;
            updateSignatureBoxVisibility();
        }

        // Tampilkan atau sembunyikan kotak tanda tangan tergantung halaman yang sedang aktif
        function updateSignatureBoxVisibility() {
            if (!placedSignature) {
                placedBox.classList.add('hidden');
                btnDeleteSignature.disabled = true;
                return;
            }

            if (placedSignature.page === currentPageNum) {
                placedBox.classList.remove('hidden');
                btnDeleteSignature.disabled = false;
                
                // Posisikan kotak sesuai persentase terhadap ukuran kanvas saat ini
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
                btnDeleteSignature.disabled = false;
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

        // Buka Modal Pembuatan Tanda Tangan
        btnPlaceSignature.addEventListener('click', () => {
            if (userSignatureDataUrl) {
                placeSignatureOnCurrentPage();
            } else {
                openSignatureModal();
            }
        });

        function openSignatureModal() {
            modal.classList.remove('hidden');
            initDrawingPad();
        }

        function closeSignatureModal() {
            modal.classList.add('hidden');
        }

        document.getElementById('btnCloseModal').addEventListener('click', closeSignatureModal);
        document.getElementById('btnCancelModal').addEventListener('click', closeSignatureModal);

        // Drawing Pad Logic
        function initDrawingPad() {
            modalCtx.lineWidth = 2.5;
            modalCtx.lineCap = 'round';
            modalCtx.lineJoin = 'round';
            modalCtx.strokeStyle = '#0F172A';
        }

        function clearModalPad() {
            modalCtx.clearRect(0, 0, modalCanvas.width, modalCanvas.height);
            hasDrawnStroke = false;
        }
        document.getElementById('btnClearPad').addEventListener('click', clearModalPad);

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
            closeSignatureModal();
            placeSignatureOnCurrentPage();
        });

        // Menempatkan tanda tangan pada halaman aktif
        function placeSignatureOnCurrentPage() {
            placedImg.src = userSignatureDataUrl;
            
            const currentWidth = parseFloat(pdfCanvas.style.width);
            const currentHeight = parseFloat(pdfCanvas.style.height);

            // Hitung lebar kotak proporsional dan pertahankan rasio aspek goresan tanda tangan
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

        // Hapus Tanda Tangan
        btnDeleteSignature.addEventListener('click', async () => {
            if (window.Swal) {
                const res = await Swal.fire({
                    title: 'Hapus Penempatan?',
                    text: 'Kotak tanda tangan akan dihapus dari halaman dokumen.',
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
                if (confirm('Hapus penempatan tanda tangan dari dokumen?')) {
                    placedSignature = null;
                    updateSignatureBoxVisibility();
                }
            }
        });

        // DRAG & DROP LOGIC UNTUK KOTAK TANDA TANGAN
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

        // RESIZE LOGIC
        resizeHandle.addEventListener('mousedown', (e) => {
            e.preventDefault();
            e.stopPropagation();
            isResizing = true;
            dragStartX = e.clientX;
            dragStartY = e.clientY;
            initialBoxWidth = parseFloat(placedBox.style.width);
            initialBoxHeight = parseFloat(placedBox.style.height);
        });

        // Touch Drag & Resize
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

                // Boundary constrain
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

                // Kunci aspek rasio agar tanda tangan proporsional dan tidak terdistorsi
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

        // SUBMIT & STAMP TANDA TANGAN KE PDF
        btnSubmitFinal.addEventListener('click', async () => {
            if (!placedSignature || !userSignatureDataUrl) {
                showNotification('warning', 'Tanda Tangan Belum Ditempatkan', 'Silakan klik tombol "Tanda Tangan" dan tempatkan tanda tangan Anda pada dokumen terlebih dahulu.');
                return;
            }

            // Modal Konfirmasi Profesional SweetAlert2
            if (window.Swal) {
                const confirmRes = await Swal.fire({
                    title: 'Konfirmasi Tanda Tangan Resmi',
                    html: `Anda akan membubuhkan tanda tangan resmi pada <strong>Halaman ${placedSignature.page}</strong> dari berkas dokumen ini.<br><small class="text-slate-400 mt-2 block">Tanda tangan akan dicap secara permanen ke dokumen PDF.</small>`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#8F0A0D',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: 'Ya, Bubuhkan & Simpan',
                    cancelButtonText: 'Periksa Kembali'
                });
                if (!confirmRes.isConfirmed) return;
            } else {
                if (!confirm(`Konfirmasi: Anda akan membubuhkan tanda tangan resmi pada Halaman ${placedSignature.page} dari dokumen ini?`)) {
                    return;
                }
            }

            btnSubmitFinal.disabled = true;
            btnSubmitText.textContent = 'Memproses PDF...';

            // Loading state modal SweetAlert2
            if (window.Swal) {
                Swal.fire({
                    title: 'Memproses Dokumen...',
                    html: 'Sedang menempelkan tanda tangan resmi ke berkas PDF.<br><span class="text-xs text-slate-400">Mohon tunggu sebentar...</span>',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            try {
                // Pastikan PDFLib tersedia
                const PDFLibInstance = window.PDFLib || (typeof PDFLib !== 'undefined' ? PDFLib : null);
                if (!PDFLibInstance || !PDFLibInstance.PDFDocument) {
                    throw new Error("Library PDFLib belum siap. Silakan muat ulang halaman (F5) dan coba lagi.");
                }

                // 1. Ambil file PDF asli dalam bentuk ArrayBuffer
                const pdfBytes = await fetch(pdfUrl).then(res => res.arrayBuffer());

                // 2. Load PDF menggunakan pdf-lib
                const pdfLibDoc = await PDFLibInstance.PDFDocument.load(pdfBytes);
                
                // Ambil halaman target (0-indexed)
                const pageIndex = placedSignature.page - 1;
                const targetPage = pdfLibDoc.getPage(pageIndex);
                const { width: pagePdfWidth, height: pagePdfHeight } = targetPage.getSize();

                // Origin offset jika PDF memiliki MediaBox/CropBox origin selain 0
                const originX = (typeof targetPage.getX === 'function') ? targetPage.getX() : 0;
                const originY = (typeof targetPage.getY === 'function') ? targetPage.getY() : 0;

                // 3. Embed gambar tanda tangan PNG hasil crop presisi ke PDF
                const signatureImage = await pdfLibDoc.embedPng(userSignatureDataUrl);

                // 4. Konversi koordinat UI (%) ke PDF points (PDF origin ada di kiri-bawah!)
                const xInPdf = originX + ((placedSignature.xPercent / 100) * pagePdfWidth);
                const widthInPdf = (placedSignature.widthPercent / 100) * pagePdfWidth;
                const heightInPdf = (placedSignature.heightPercent / 100) * pagePdfHeight;
                // Invert Y coordinate untuk PDF
                const yInPdf = originY + (pagePdfHeight - ((placedSignature.yPercent / 100) * pagePdfHeight) - heightInPdf);

                // 5. Gambar tanda tangan langsung ke halaman PDF!
                targetPage.drawImage(signatureImage, {
                    x: xInPdf,
                    y: yInPdf,
                    width: widthInPdf,
                    height: heightInPdf
                });

                // 6. Simpan hasil PDF yang telah ter-stamp
                const modifiedPdfBytes = await pdfLibDoc.save();

                btnSubmitText.textContent = 'Mengunggah Dokumen...';

                // 7. Kirim ke Server via FormData
                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('signature', userSignatureDataUrl);
                
                // Kirim PDF yang telah distamp sebagai Blob file
                const pdfBlob = new Blob([modifiedPdfBytes], { type: 'application/pdf' });
                formData.append('signed_pdf', pdfBlob, 'signed_doc.pdf');
                
                // Kirim data penempatan untuk rekam audit
                formData.append('placement', JSON.stringify({
                    page: placedSignature.page,
                    x: placedSignature.xPercent,
                    y: placedSignature.yPercent,
                    width: placedSignature.widthPercent,
                    height: placedSignature.heightPercent
                }));

                const response = await fetch(submitUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    if (window.Swal) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Dokumen Berhasil Ditandatangani!',
                            text: 'Tanda tangan resmi telah tertempel dan dokumen telah diperbarui.',
                            confirmButtonColor: '#8F0A0D',
                            confirmButtonText: 'Lihat Dokumen'
                        });
                    } else {
                        alert('Berhasil! Tanda tangan telah resmi ditempatkan dan dokumen telah diperbarui.');
                    }
                    window.location.href = result.redirect_url || redirectUrl;
                } else {
                    showNotification('error', 'Gagal Menyimpan', result.message || 'Terjadi kesalahan pada server saat menyimpan dokumen.');
                    btnSubmitFinal.disabled = false;
                    btnSubmitText.textContent = 'Simpan & Tanda Tangani';
                }

            } catch (err) {
                console.error("Error embedding signature:", err);
                showNotification('error', 'Terjadi Kendala', err.message || 'Gagal memproses penempelan tanda tangan.');
                btnSubmitFinal.disabled = false;
                btnSubmitText.textContent = 'Simpan & Tanda Tangani';
            }
        });

        // Mulai memuat PDF saat halaman siap
        window.addEventListener('DOMContentLoaded', loadPdf);
    </script>
</body>
</html>
