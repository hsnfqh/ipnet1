@extends('layouts.app')

@section('title', $document->title . ' - Digital Signature')

@push('styles')
<style>
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
    }
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.02);
    }
    .btn-ipnet-primary {
        background: linear-gradient(135deg, #8F0A0D 0%, #73080A 100%);
        color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(143, 10, 13, 0.25);
        transition: all 0.2s ease;
    }
    .btn-ipnet-primary:hover {
        background: linear-gradient(135deg, #73080A 0%, #590608 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.35);
    }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="documentDetailManager()">

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">&times;</button>
        </div>
    @endif

    {{-- BREADCRUMB & HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('digital_signatures.index') }}" class="hover:text-[#8F0A0D] font-medium">Digital Signature</a>
                <span>/</span>
                <span class="font-mono text-gray-700 font-bold">{{ $document->document_number }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ $document->title }}</h1>
                @if($document->status === 'completed')
                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        Selesai & Sah (Verified)
                    </span>
                @elseif($document->status === 'ready_for_client')
                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-bold inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Internal Selesai &bull; Siap Kirim ke Klien
                    </span>
                @elseif($document->status === 'internal_in_progress')
                    <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                        Proses Tanda Tangan Internal
                    </span>
                @else
                    <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-bold">Draft</span>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 mt-1">
                <span>Kategori: <strong class="text-gray-700">{{ $document->category }}</strong></span>
                <span>&bull;</span>
                <span>Proyek: <strong class="text-gray-700">{{ $document->project_name ?: '-' }}</strong></span>
                <span>&bull;</span>
                <span>Dibuat oleh: <strong class="text-gray-700">{{ $document->creator?->name }}</strong> ({{ $document->created_at->format('d/m/Y H:i') }} WIB)</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('digital_signatures.download_pdf', $document->id) }}" class="px-3 py-2 rounded-xl bg-gray-900 text-white font-bold text-xs hover:bg-gray-800 flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Lembar Pengesahan PDF</span>
            </a>
            <a href="{{ route('digital_signatures.download_original', $document->id) }}" class="px-3 py-2 rounded-xl border border-gray-200 text-gray-700 font-bold text-xs hover:bg-gray-50 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Berkas Asli</span>
            </a>
        </div>
    </div>

    {{-- PROMINENT BANNER: BAGIKAN LINK KE KLIEN --}}
    @if($isAllInternalSigned)
        <div class="ipnet-card p-5 border-blue-200 bg-gradient-to-r from-blue-50/80 via-white to-blue-50/40">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <h3 class="text-sm font-black text-blue-950 uppercase tracking-wider">
                            {{ $document->status === 'completed' ? 'Tautan Verifikasi & TTD Klien' : 'Tautan TTD Siap Dikirim ke Klien' }}
                        </h3>
                    </div>
                    <p class="text-xs text-blue-800">
                        @if($document->status === 'completed')
                            PIC Klien (<strong>{{ $document->client_name }}</strong>) telah menandatangani dokumen ini pada {{ $document->client_signed_at?->format('d/m/Y H:i') }} WIB.
                        @else
                            Seluruh pihak internal telah menandatangani! Silakan salin link di bawah atau kirim langsung ke WhatsApp PIC Klien (<strong>{{ $document->client_name }}</strong>).
                        @endif
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <div class="relative flex-1 sm:w-80">
                        <input type="text" readonly value="{{ $clientSigningUrl }}" id="clientUrlInput"
                               class="w-full pl-3 pr-20 py-2 text-xs rounded-xl border border-blue-200 bg-white font-mono text-gray-700 select-all">
                        <button type="button" @click="copyLink()"
                                class="absolute right-1 top-1 bottom-1 px-3 rounded-lg bg-blue-600 text-white font-bold text-[11px] hover:bg-blue-700 transition-all">
                            <span x-text="copied ? 'Tersalin!' : 'Salin'">Salin</span>
                        </button>
                    </div>

                    <a href="{{ $waLink }}" target="_blank"
                       class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-flex items-center justify-center gap-1.5 shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        <span>Kirim WA</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- KARTU TANDA TANGAN INTERNAL SAYA (JIKA GILIRAN USER INI) --}}
    @if($canSign)
        <div class="ipnet-card p-6 border-[#8F0A0D]/30 bg-gradient-to-br from-red-50/40 via-white to-red-50/20 space-y-4">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-[#8F0A0D] text-white flex items-center justify-center font-bold text-sm">✍️</div>
                    <div>
                        <h2 class="text-sm font-black text-gray-900">Giliran Anda Menandatangani Dokumen</h2>
                        <p class="text-xs text-gray-600">Sebagai <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->position ?: 'Engineer Pelaksana' }}).</p>
                    </div>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-700">Goreskan Tanda Tangan Anda pada Kotak di Bawah:</label>
                <div class="border-2 border-gray-300 rounded-2xl overflow-hidden bg-white shadow-inner relative">
                    <canvas id="internalSignaturePad" width="700" height="180" class="w-full h-44 cursor-crosshair touch-none"></canvas>
                    <div class="absolute bottom-2 right-2 flex items-center gap-2">
                        <button type="button" @click="clearPad()" class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 text-xs font-semibold">
                            Bersihkan
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-1">
                    <span class="text-[11px] text-gray-400">Gunakan mouse pada laptop atau goreskan jari pada layar sentuh/HP.</span>
                    <button type="button" @click="submitInternalSign()" :disabled="submittingSign"
                            class="btn-ipnet-primary px-5 py-2 rounded-xl text-xs font-bold inline-flex items-center gap-2">
                        <span x-show="!submittingSign">Simpan Tanda Tangan &rarr;</span>
                        <span x-show="submittingSign">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- GRID PROGRESS PENANDATANGAN --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- KOLOM 1 & 2: TIMELINE PENANDATANGAN --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- 1. Penandatangan Internal IPNET --}}
            <div class="ipnet-card p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                        Penandatangan Internal IPNET
                    </h3>
                    <span class="text-[11px] font-semibold text-gray-500">
                        Mode: {{ $document->workflow_type === 'sequential' ? 'Berurutan' : 'Bebas (Paralel)' }}
                    </span>
                </div>

                <div class="space-y-3">
                    @foreach($document->internal_signers ?? [] as $idx => $s)
                        @php
                            $isSigned = ($s['status'] ?? '') === 'signed';
                        @endphp
                        <div class="p-4 rounded-xl border {{ $isSigned ? 'border-emerald-200 bg-emerald-50/20' : 'border-gray-200 bg-white' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $isSigned ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $isSigned ? '✓' : ($idx + 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-xs text-gray-900">{{ $s['name'] }}</div>
                                    <div class="text-[11px] text-gray-500">{{ $s['role_title'] }} &bull; {{ $s['email'] }}</div>
                                    @if($isSigned)
                                        <div class="text-[10px] text-emerald-700 font-semibold mt-0.5">
                                            Ditandatangani pada {{ \Carbon\Carbon::parse($s['signed_at'])->format('d/m/Y H:i') }} WIB
                                        </div>
                                    @else
                                        <div class="text-[10px] text-amber-600 font-semibold mt-0.5">
                                            Menunggu tanda tangan
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right">
                                @if($isSigned && !empty($s['signature']))
                                    <div class="bg-white border border-gray-200 rounded-lg p-1 inline-block">
                                        <img src="{{ $s['signature'] }}" alt="Signature" class="h-9 max-w-[120px] object-contain">
                                    </div>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-[10px] font-bold">Pending</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 2. Penandatangan PIC Klien --}}
            <div class="ipnet-card p-5 space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        Penandatangan PIC Klien (Eksternal)
                    </h3>
                    @if($document->client_signed_at)
                        <span class="text-[11px] font-bold text-emerald-600">✓ Selesai Ditandatangani</span>
                    @else
                        <span class="text-[11px] font-bold text-amber-600">⌛ Menunggu Tanda Tangan Klien</span>
                    @endif
                </div>

                <div class="p-4 rounded-xl border {{ $document->client_signed_at ? 'border-emerald-200 bg-emerald-50/20' : 'border-gray-200 bg-white' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $document->client_signed_at ? 'bg-emerald-600 text-white' : 'bg-blue-100 text-blue-700' }}">
                            {{ $document->client_signed_at ? '✓' : 'K' }}
                        </div>
                        <div>
                            <div class="font-bold text-xs text-gray-900">{{ $document->client_name ?: 'PIC Klien' }}</div>
                            <div class="text-[11px] text-gray-500">
                                {{ $document->client_position ?: 'Penanggung Jawab' }} &bull; {{ $document->client_company ?: '-' }}
                            </div>
                            @if($document->client_phone)
                                <div class="text-[10.5px] text-gray-400">WhatsApp/HP: {{ $document->client_phone }}</div>
                            @endif
                            @if($document->client_signed_at)
                                <div class="text-[10px] text-emerald-700 font-semibold mt-0.5">
                                    Ditandatangani pada {{ $document->client_signed_at->format('d/m/Y H:i') }} WIB (IP: {{ $document->client_ip }})
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="text-right">
                        @if($document->client_signature)
                            <div class="bg-white border border-gray-200 rounded-lg p-1 inline-block">
                                <img src="{{ $document->client_signature }}" alt="Client Signature" class="h-10 max-w-[130px] object-contain">
                            </div>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 text-[10px] font-bold">Belum TTD</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        {{-- KOLOM 3: INFORMASI DOKUMEN & PREVIEW --}}
        <div class="space-y-6">

            <div class="ipnet-card p-5 space-y-3.5">
                <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider border-b border-gray-100 pb-2.5">
                    Informasi Dokumen
                </h3>
                <dl class="space-y-2 text-xs">
                    <div>
                        <dt class="text-gray-400 text-[10.5px]">Nomor Berkas:</dt>
                        <dd class="font-mono font-bold text-gray-800">{{ $document->document_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-[10.5px]">Nama File Asli:</dt>
                        <dd class="font-medium text-gray-800 truncate" title="{{ $document->file_name }}">{{ $document->file_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-400 text-[10.5px]">Ukuran File:</dt>
                        <dd class="text-gray-800">{{ number_format($document->file_size / 1024, 1) }} KB</dd>
                    </div>
                    @if($document->verification_hash)
                        <div>
                            <dt class="text-gray-400 text-[10.5px]">Verification Hash:</dt>
                            <dd class="font-mono text-[10px] text-emerald-700 break-all">{{ substr($document->verification_hash, 0, 24) }}...</dd>
                        </div>
                    @endif
                </dl>

                @if($document->verification_hash)
                    <div class="pt-2 border-t border-gray-100">
                        <a href="{{ route('public.digital_signature.verify', $document->verification_hash) }}" target="_blank"
                           class="w-full py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-bold flex items-center justify-center gap-1.5 transition-all">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Buka Halaman Verifikasi Sah</span>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Hapus Dokumen --}}
            @if($document->created_by === auth()->id() || auth()->user()->hasAnyRole(['Super Admin', 'Superadmin', 'Admin']))
                <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50">
                    <form action="{{ route('digital_signatures.destroy', $document->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-bold text-gray-400 hover:text-rose-600 transition-colors">
                            &times; Hapus Dokumen Ini
                        </button>
                    </form>
                </div>
            @endif

        </div>

    </div>

</div>

@push('scripts')
<script>
function documentDetailManager() {
    return {
        copied: false,
        submittingSign: false,
        copyLink() {
            const input = document.getElementById('clientUrlInput');
            if (input) {
                input.select();
                navigator.clipboard.writeText(input.value).then(() => {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2500);
                });
            }
        },
        clearPad() {
            const canvas = document.getElementById('internalSignaturePad');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            }
        },
        async submitInternalSign() {
            const canvas = document.getElementById('internalSignaturePad');
            if (!canvas) return;

            // Pastikan canvas tidak kosong
            const blank = document.createElement('canvas');
            blank.width = canvas.width;
            blank.height = canvas.height;
            if (canvas.toDataURL() === blank.toDataURL()) {
                alert('Silakan goreskan tanda tangan Anda terlebih dahulu pada kotak!');
                return;
            }

            const dataUrl = canvas.toDataURL('image/png');
            this.submittingSign = true;

            try {
                const res = await fetch('{{ route("digital_signatures.sign_internal", $document->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ signature: dataUrl })
                });

                const data = await res.json();
                if (data.success) {
                    alert('Tanda tangan internal Anda berhasil disimpan!');
                    window.location.reload();
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (e) {
                alert('Gagal menghubungi server.');
            } finally {
                this.submittingSign = false;
            }
        }
    };
}

// Inisialisasi Canvas Signature Drawing
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('internalSignaturePad');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    ctx.strokeStyle = '#1E293B';
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;

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
});
</script>
@endpush
@endsection
