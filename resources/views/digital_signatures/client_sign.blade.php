<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Tanda Tangan Dokumen: {{ $document->title }} - PT IP Network Solusindo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pdf-lib@1.17.9/dist/pdf-lib.min.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC;
        }
        .btn-ipnet-primary {
            background: linear-gradient(135deg, #8F0A0D 0%, #73080A 100%);
            color: #FFFFFF;
            box-shadow: 0 4px 14px rgba(143, 10, 13, 0.3);
            transition: all 0.2s ease;
        }
        .btn-ipnet-primary:hover {
            background: linear-gradient(135deg, #73080A 0%, #590608 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(143, 10, 13, 0.4);
        }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased flex flex-col justify-between">

    <!-- HEADER BRANDING -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-4xl mx-auto px-4 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/ipnet1.png') }}" alt="IPNET Logo" class="h-8 object-contain" onerror="this.src='{{ asset('images/ipnet.png') }}'">
                <div>
                    <div class="font-extrabold text-sm text-slate-900 leading-tight">PT IP Network Solusindo</div>
                    <div class="text-[10px] font-bold text-[#8F0A0D] tracking-wider uppercase">Official Digital Signature Portal</div>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Portal Resmi Terverifikasi
                </span>
            </div>
        </div>
    </header>

    <!-- MAIN CONTAINER -->
    <main class="max-w-3xl mx-auto px-4 py-6 w-full flex-1">
        <div class="space-y-6">

            <!-- DOKUMEN BANNER -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 font-mono text-[11px] font-bold">
                            {{ $document->document_number }}
                        </span>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 mt-1 leading-snug">
                            {{ $document->title }}
                        </h1>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-red-50 text-[#8F0A0D] font-bold text-[11px] uppercase shrink-0">
                        {{ $document->category }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2 border-t border-slate-100 text-xs text-slate-600">
                    <div>Proyek: <strong class="text-slate-800">{{ $document->project_name ?: '-' }}</strong></div>
                    <div>Perusahaan Klien: <strong class="text-slate-800">{{ $document->client_company ?: '-' }}</strong></div>
                    <div>Tanggal Dokumen: <strong class="text-slate-800">{{ $document->created_at->format('d F Y') }}</strong></div>
                    <div>Dibuat oleh: <strong class="text-slate-800">{{ $document->creator?->name }} (IPNET)</strong></div>
                </div>
            </div>

            @if($document->status === 'completed')
                <!-- SUDAH DITANDATANGANI -->
                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 text-center space-y-3">
                    <div class="w-14 h-14 rounded-full bg-emerald-600 text-white flex items-center justify-center mx-auto text-2xl font-bold shadow-md">
                        ✓
                    </div>
                    <h2 class="text-lg font-black text-emerald-900">Dokumen Telah Selesai Ditandatangani</h2>
                    <p class="text-xs text-emerald-700 max-w-md mx-auto">
                        Dokumen ini telah resmi ditandatangani oleh <strong>{{ $document->client_name }}</strong> pada {{ $document->client_signed_at?->format('d/m/Y H:i') }} WIB dan telah diverifikasi secara elektronik.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('public.digital_signature.verify', $document->verification_hash ?: $document->document_number) }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 shadow-sm transition-all">
                            Lihat Lembar Pengesahan & Bukti Keabsahan &rarr;
                        </a>
                    </div>
                </div>
            @else
                <!-- STATUS TIM INTERNAL -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                        <span class="flex items-center gap-1.5 text-emerald-700">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Telah Ditandatangani oleh Tim PT IP Network Solusindo:
                        </span>
                        <span class="text-slate-400 text-[11px]">{{ count($document->internal_signers ?? []) }} Penandatangan</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                        @foreach($document->internal_signers ?? [] as $s)
                            <div class="bg-white p-2 rounded-lg border border-slate-200 flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-slate-800">{{ $s['name'] }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $s['role_title'] }}</div>
                                </div>
                                <span class="text-emerald-600 font-bold text-xs">✓ Sah</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- DOWNLOAD & LIHAT DOKUMEN ASLI -->
                <div class="bg-white rounded-2xl p-4 border border-slate-200 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-50 text-[#8F0A0D] flex items-center justify-center font-bold">
                            PDF
                        </div>
                        <div>
                            <div class="font-bold text-xs text-slate-900 truncate max-w-xs sm:max-w-md">{{ $document->file_name }}</div>
                            <div class="text-[11px] text-slate-400">{{ number_format($document->file_size / 1024, 1) }} KB &bull; Berkas Resmi Pekerjaan</div>
                        </div>
                    </div>
                    <a href="{{ route('digital_signatures.download_original', $document->id) }}"
                       class="px-3 py-1.5 rounded-xl border border-slate-200 hover:border-slate-400 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all shrink-0">
                        Unduh PDF
                    </a>
                </div>

                <!-- FORM TANDA TANGAN KLIEN -->
                <form id="clientSignForm" class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-5">
                    @csrf
                    <div>
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            Lembar Tanda Tangan PIC Klien
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Mohon lengkapi dan konfirmasi data Anda di bawah ini, lalu goreskan tanda tangan pada kotak yang disediakan.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Anda <span class="text-rose-500">*</span></label>
                            <input type="text" name="client_name" id="client_name" value="{{ $document->client_name }}" required
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan Anda <span class="text-rose-500">*</span></label>
                            <input type="text" name="client_position" id="client_position" value="{{ $document->client_position }}" required placeholder="Contoh: IT Manager / Site Lead"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Perusahaan / Instansi</label>
                            <input type="text" name="client_company" id="client_company" value="{{ $document->client_company }}"
                                   class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                        </div>
                    </div>

                    <!-- KOTAK TANDA TANGAN (CANVAS) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-700">Bubuhkan Tanda Tangan Anda di Sini <span class="text-rose-500">*</span></label>
                            <button type="button" id="clearBtn" class="text-[11px] font-bold text-slate-500 hover:text-rose-600">
                                Bersihkan Kotak
                            </button>
                        </div>
                        <div class="border-2 border-slate-300 rounded-2xl overflow-hidden bg-slate-50/50 shadow-inner relative">
                            <canvas id="clientCanvas" width="700" height="200" class="w-full h-48 cursor-crosshair touch-none bg-white"></canvas>
                        </div>
                        <p class="text-[11px] text-slate-400">Gunakan jari Anda pada layar HP/tablet atau mouse pada komputer untuk membubuhkan tanda tangan.</p>
                    </div>

                    <!-- OPSIONAL / INTERAKTIF: TEMPATKAN DI HALAMAN PDF SEPERTI IPB DISIGN -->
                    <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50">
                        <div class="p-3.5 bg-slate-100/70 border-b border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-teal-600"></span>
                                <span class="text-xs font-bold text-slate-800">Posisi Tanda Tangan pada Berkas PDF (Interaktif)</span>
                            </div>
                            <button type="button" id="btnTogglePdfPlacement" class="text-xs font-bold text-teal-700 hover:text-teal-900 bg-white px-2.5 py-1 rounded-lg border border-slate-200 shadow-2xs">
                                Buka Viewer Dokumen &rarr;
                            </button>
                        </div>
                        
                        <div id="pdfPlacementSection" class="hidden p-4 space-y-3">
                            <p class="text-[11.5px] text-slate-600">
                                Pilih halaman dan geser (drag & drop) kotak tanda tangan ke lokasi yang Anda inginkan (misal di kolom Pihak Kedua / PIC Klien).
                            </p>

                            <!-- Mini Toolbar -->
                            <div class="flex items-center justify-between bg-slate-800 text-white p-2 rounded-xl text-xs gap-2 flex-wrap">
                                <button type="button" id="btnPlaceClientSig" class="px-2.5 py-1 rounded-lg bg-teal-600 hover:bg-teal-500 font-bold text-[11px] flex items-center gap-1">
                                    + Taruh di Halaman Ini
                                </button>
                                <div class="flex items-center gap-1 text-[11px]">
                                    <button type="button" id="cPrevPage" class="px-2 py-0.5 bg-slate-700 hover:bg-slate-600 rounded font-bold">&lsaquo;</button>
                                    <span>Hal <strong id="cPageNum">1</strong> / <span id="cTotalPages">1</span></span>
                                    <button type="button" id="cNextPage" class="px-2 py-0.5 bg-slate-700 hover:bg-slate-600 rounded font-bold">&rsaquo;</button>
                                </div>
                            </div>

                            <!-- PDF Canvas Area -->
                            <div class="bg-slate-700 p-2 sm:p-4 rounded-xl flex items-center justify-center overflow-auto max-h-[500px] relative">
                                <div id="cPageWrapper" class="relative inline-block">
                                    <canvas id="cCanvas" class="shadow-lg bg-white block rounded-xs max-w-full"></canvas>
                                    <div id="cPlacedBox" class="absolute border-2 border-dashed border-teal-500 bg-teal-500/20 rounded shadow hidden cursor-move select-none" style="width: 140px; height: 70px;">
                                        <div class="absolute -top-5 left-0 bg-teal-800 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-t">
                                            TTD Anda
                                        </div>
                                        <img id="cPlacedImg" src="" class="w-full h-full object-contain pointer-events-none p-1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CHECKBOX PERNYATAAN -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                        <label class="flex items-start gap-2.5 cursor-pointer text-xs text-slate-700 select-none">
                            <input type="checkbox" name="agreement" id="agreementCheckbox" required class="mt-0.5 rounded text-[#8F0A0D] focus:ring-[#8F0A0D]">
                            <span class="leading-relaxed">
                                Saya menyatakan dengan sebenarnya bahwa saya berwenang mewakili instansi saya dan menyetujui seluruh isi dokumen <strong>{{ $document->title }}</strong> dengan tanda tangan elektronik yang sah.
                            </span>
                        </label>
                    </div>

                    <!-- TOMBOL SUBMIT -->
                    <div>
                        <button type="button" id="submitBtn" class="btn-ipnet-primary w-full py-3 rounded-xl font-bold text-sm tracking-wide flex items-center justify-center gap-2">
                            <span>Setujui & Tanda Tangani Dokumen</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>
            @endif

        </div>
    </main>

    <!-- FOOTER -->
    <footer class="py-6 border-t border-slate-200 bg-white text-center text-xs text-slate-400 mt-12">
        <div class="max-w-4xl mx-auto px-4">
            <div>&copy; {{ date('Y') }} PT IP Network Solusindo. All rights reserved.</div>
            <div class="text-[11px] mt-0.5">Sistem Pengesahan Tanda Tangan Elektronik Terverifikasi UU ITE</div>
        </div>
    </footer>

    <!-- SCRIPT TANDA TANGAN KLIEN -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const pdfUrl = @json($pdfUrl);
        const canvas = document.getElementById('clientCanvas');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        ctx.strokeStyle = '#0F172A';
        ctx.lineWidth = 2.8;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;
        let hasDrawn = false;

        function getCoords(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            return {
                x: (clientX - rect.left) * scaleX,
                y: (clientY - rect.top) * scaleY
            };
        }

        function start(e) {
            isDrawing = true;
            const coords = getCoords(e);
            lastX = coords.x;
            lastY = coords.y;
        }

        function draw(e) {
            if (!isDrawing) return;
            e.preventDefault();
            const coords = getCoords(e);
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(coords.x, coords.y);
            ctx.stroke();
            lastX = coords.x;
            lastY = coords.y;
            hasDrawn = true;
        }

        function stop() {
            isDrawing = false;
        }

        canvas.addEventListener('mousedown', start);
        canvas.addEventListener('mousemove', draw);
        window.addEventListener('mouseup', stop);

        canvas.addEventListener('touchstart', start, { passive: false });
        canvas.addEventListener('touchmove', draw, { passive: false });
        window.addEventListener('touchend', stop);

        document.getElementById('clearBtn')?.addEventListener('click', function() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            hasDrawn = false;
            if (cPlacedBox) cPlacedBox.classList.add('hidden');
        });

        // INTERACTIVE PDF PLACEMENT FOR CLIENT
        let cPdfDoc = null;
        let cCurPage = 1;
        let cTotal = 1;
        let cPlacedPlacement = null; // { page, xPercent, yPercent, widthPercent, heightPercent }
        const cCanvas = document.getElementById('cCanvas');
        const cCtx = cCanvas ? cCanvas.getContext('2d') : null;
        const cPlacedBox = document.getElementById('cPlacedBox');
        const cPlacedImg = document.getElementById('cPlacedImg');

        const btnTogglePdfPlacement = document.getElementById('btnTogglePdfPlacement');
        const pdfPlacementSection = document.getElementById('pdfPlacementSection');

        btnTogglePdfPlacement?.addEventListener('click', async function() {
            if (pdfPlacementSection.classList.contains('hidden')) {
                pdfPlacementSection.classList.remove('hidden');
                btnTogglePdfPlacement.textContent = 'Tutup Viewer';
                if (!cPdfDoc) {
                    await initClientPdf();
                }
            } else {
                pdfPlacementSection.classList.add('hidden');
                btnTogglePdfPlacement.textContent = 'Buka Viewer Dokumen →';
            }
        });

        async function initClientPdf() {
            try {
                if (window.pdfjsLib) {
                    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                    const loading = pdfjsLib.getDocument({ url: pdfUrl });
                    cPdfDoc = await loading.promise;
                    cTotal = cPdfDoc.numPages;
                    document.getElementById('cTotalPages').textContent = cTotal;
                    await renderClientPage(1);
                }
            } catch (err) {
                console.warn('PDF load warning:', err);
            }
        }

        async function renderClientPage(num) {
            if (!cPdfDoc) return;
            const page = await cPdfDoc.getPage(num);
            const viewport = page.getViewport({ scale: 1.0 });
            
            cCanvas.width = viewport.width;
            cCanvas.height = viewport.height;
            cCanvas.style.width = viewport.width + 'px';
            cCanvas.style.height = viewport.height + 'px';

            await page.render({ canvasContext: cCtx, viewport: viewport }).promise;
            cCurPage = num;
            document.getElementById('cPageNum').textContent = num;
            updateClientBox();
        }

        document.getElementById('cPrevPage')?.addEventListener('click', () => {
            if (cCurPage > 1) renderClientPage(cCurPage - 1);
        });
        document.getElementById('cNextPage')?.addEventListener('click', () => {
            if (cCurPage < cTotal) renderClientPage(cCurPage + 1);
        });

        document.getElementById('btnPlaceClientSig')?.addEventListener('click', () => {
            if (!hasDrawn) {
                alert('Silakan goreskan tanda tangan Anda pada kotak di atas terlebih dahulu!');
                return;
            }
            cPlacedImg.src = canvas.toDataURL('image/png');
            cPlacedBox.classList.remove('hidden');
            
            const w = parseFloat(cCanvas.style.width) || cCanvas.width;
            const h = parseFloat(cCanvas.style.height) || cCanvas.height;

            const boxW = 150;
            const boxH = 75;
            const left = Math.max(0, w - boxW - 50);
            const top = Math.max(0, h - boxH - 60);

            cPlacedBox.style.left = left + 'px';
            cPlacedBox.style.top = top + 'px';
            cPlacedBox.style.width = boxW + 'px';
            cPlacedBox.style.height = boxH + 'px';

            cPlacedPlacement = {
                page: cCurPage,
                xPercent: (left / w) * 100,
                yPercent: (top / h) * 100,
                widthPercent: (boxW / w) * 100,
                heightPercent: (boxH / h) * 100
            };
        });

        function updateClientBox() {
            if (!cPlacedPlacement || cPlacedPlacement.page !== cCurPage) {
                cPlacedBox.classList.add('hidden');
            } else {
                cPlacedBox.classList.remove('hidden');
                const w = parseFloat(cCanvas.style.width) || cCanvas.width;
                const h = parseFloat(cCanvas.style.height) || cCanvas.height;
                cPlacedBox.style.left = ((cPlacedPlacement.xPercent / 100) * w) + 'px';
                cPlacedBox.style.top = ((cPlacedPlacement.yPercent / 100) * h) + 'px';
            }
        }

        // Draggable box for client
        let cDragging = false;
        let cStartDragX = 0, cStartDragY = 0, cInitLeft = 0, cInitTop = 0;

        cPlacedBox?.addEventListener('mousedown', (e) => {
            e.preventDefault();
            cDragging = true;
            cStartDragX = e.clientX;
            cStartDragY = e.clientY;
            cInitLeft = parseFloat(cPlacedBox.style.left) || 0;
            cInitTop = parseFloat(cPlacedBox.style.top) || 0;
        });

        cPlacedBox?.addEventListener('touchstart', (e) => {
            cDragging = true;
            const t = e.touches[0];
            cStartDragX = t.clientX;
            cStartDragY = t.clientY;
            cInitLeft = parseFloat(cPlacedBox.style.left) || 0;
            cInitTop = parseFloat(cPlacedBox.style.top) || 0;
        }, { passive: false });

        window.addEventListener('mousemove', (e) => {
            if (!cDragging) return;
            const deltaX = e.clientX - cStartDragX;
            const deltaY = e.clientY - cStartDragY;
            const w = parseFloat(cCanvas.style.width) || cCanvas.width;
            const h = parseFloat(cCanvas.style.height) || cCanvas.height;
            const newL = Math.max(0, Math.min(w - 140, cInitLeft + deltaX));
            const newT = Math.max(0, Math.min(h - 70, cInitTop + deltaY));
            cPlacedBox.style.left = newL + 'px';
            cPlacedBox.style.top = newT + 'px';
            if (cPlacedPlacement) {
                cPlacedPlacement.xPercent = (newL / w) * 100;
                cPlacedPlacement.yPercent = (newT / h) * 100;
            }
        });

        window.addEventListener('touchmove', (e) => {
            if (!cDragging) return;
            const t = e.touches[0];
            const deltaX = t.clientX - cStartDragX;
            const deltaY = t.clientY - cStartDragY;
            const w = parseFloat(cCanvas.style.width) || cCanvas.width;
            const h = parseFloat(cCanvas.style.height) || cCanvas.height;
            const newL = Math.max(0, Math.min(w - 140, cInitLeft + deltaX));
            const newT = Math.max(0, Math.min(h - 70, cInitTop + deltaY));
            cPlacedBox.style.left = newL + 'px';
            cPlacedBox.style.top = newT + 'px';
            if (cPlacedPlacement) {
                cPlacedPlacement.xPercent = (newL / w) * 100;
                cPlacedPlacement.yPercent = (newT / h) * 100;
            }
        }, { passive: false });

        window.addEventListener('mouseup', () => { cDragging = false; });
        window.addEventListener('touchend', () => { cDragging = false; });

        // Submit Handler
        document.getElementById('submitBtn')?.addEventListener('click', async function() {
            const name = document.getElementById('client_name')?.value?.trim();
            const position = document.getElementById('client_position')?.value?.trim();
            const company = document.getElementById('client_company')?.value?.trim();
            const agree = document.getElementById('agreementCheckbox')?.checked;

            if (!name) {
                alert('Silakan isi Nama Lengkap Anda.');
                return;
            }
            if (!position) {
                alert('Silakan isi Jabatan Anda.');
                return;
            }
            if (!agree) {
                alert('Silakan centang persetujuan dokumen terlebih dahulu.');
                return;
            }
            if (!hasDrawn) {
                alert('Silakan bubuhkan tanda tangan Anda terlebih dahulu pada kotak!');
                return;
            }

            const signatureBase64 = canvas.toDataURL('image/png');
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Memproses Pengesahan & PDF...';

            try {
                const formData = new FormData();
                formData.append('client_name', name);
                formData.append('client_position', position);
                formData.append('client_company', company || '');
                formData.append('signature', signatureBase64);
                formData.append('agreement', '1');

                // Jika user menempatkan posisi tanda tangan secara interaktif, stamp langsung via pdf-lib!
                if (cPlacedPlacement && window.PDFLib) {
                    try {
                        const pdfBytes = await fetch(pdfUrl).then(r => r.arrayBuffer());
                        const pdfDoc = await PDFLib.PDFDocument.load(pdfBytes);
                        const page = pdfDoc.getPage(cPlacedPlacement.page - 1);
                        const { width: pW, height: pH } = page.getSize();
                        const sigImg = await pdfDoc.embedPng(signatureBase64);
                        
                        const x = (cPlacedPlacement.xPercent / 100) * pW;
                        const w = (cPlacedPlacement.widthPercent / 100) * pW;
                        const h = (cPlacedPlacement.heightPercent / 100) * pH;
                        const y = pH - ((cPlacedPlacement.yPercent / 100) * pH) - h;

                        page.drawImage(sigImg, { x, y, width: w, height: h });
                        const modifiedBytes = await pdfDoc.save();
                        formData.append('signed_pdf', new Blob([modifiedBytes], { type: 'application/pdf' }), 'client_signed.pdf');
                        formData.append('placement', JSON.stringify(cPlacedPlacement));
                    } catch (stampErr) {
                        console.warn('Stamping warning:', stampErr);
                    }
                }

                const res = await fetch('{{ route("client.digital_signature.submit", $document->client_signing_token) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await res.json();
                if (data.success) {
                    alert('Terima kasih! Dokumen resmi berhasil ditandatangani dan diverifikasi.');
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Setujui & Tanda Tangani Dokumen';
                }
            } catch (err) {
                console.error(err);
                alert('Gagal mengirim data ke server. Mohon periksa koneksi internet Anda.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Setujui & Tanda Tangani Dokumen';
            }
        });
    });
    </script>
</body>
</html>
