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
        box-shadow: 0 1px 3px rgba(0,0,0,0.03), 0 4px 12px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
    }
    .ipnet-card:hover {
        border-color: #CBD5E1;
    }
    .btn-ipnet-primary {
        background: linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%) !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 12px rgba(143, 10, 13, 0.22) !important;
        transition: all 0.2s ease !important;
    }
    .btn-ipnet-primary:hover {
        background: linear-gradient(135deg, #C52222 0%, #9C0C0F 60%, #83080A 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.3) !important;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="documentDetailManager()">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Detail Dokumen Digital Signature'])

        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">

            {{-- ALERT MESSAGES --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs font-semibold shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold px-2">&times;</button>
                </div>
            @endif

            {{-- BREADCRUMB & HEADER --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                        <a href="{{ route('digital_signatures.index') }}" class="hover:text-[#8F0A0D] font-semibold transition">Digital Signature</a>
                        <span>/</span>
                        <span class="font-mono text-slate-700 font-bold">{{ $document->document_number }}</span>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight">{{ $document->title }}</h2>
                        @if($document->status === 'completed')
                            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold inline-flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                Selesai & Sah
                            </span>
                        @elseif($document->status === 'ready_for_client')
                            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-bold inline-flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                Menunggu TTD Klien
                            </span>
                        @elseif($document->status === 'internal_in_progress')
                            <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold inline-flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                                Proses TTD Internal
                            </span>
                        @else
                            <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">Draft</span>
                        @endif
                    </div>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1">
                        <span>Kategori: <strong class="text-slate-700">{{ $document->category }}</strong></span>
                        <span>&bull;</span>
                        <span>Proyek: <strong class="text-slate-700">{{ $document->project_name ?: '-' }}</strong></span>
                        <span>&bull;</span>
                        <span>Dibuat oleh: <strong class="text-slate-700">{{ $document->creator?->name }}</strong> ({{ $document->created_at->format('d/m/Y H:i') }} WIB)</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('digital_signatures.download_pdf', $document->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 flex items-center gap-1.5 shadow-xs transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh Dokumen PDF</span>
                    </a>
                    <a href="{{ route('digital_signatures.download_certificate', $document->id) }}" class="px-3 py-2 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 flex items-center gap-1.5 transition-all" title="Unduh Lembar Pengesahan Resmi">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Lembar Pengesahan</span>
                    </a>
                    <a href="{{ route('digital_signatures.download_original', $document->id) }}" class="px-3 py-2 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 flex items-center gap-1.5 transition-all">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Berkas Asli</span>
                    </a>
                </div>
            </div>

            {{-- PROMINENT BANNER: BAGIKAN LINK KE KLIEN (SINGKAT & PROFESIONAL) --}}
            @if($isAllInternalSigned)
                <div class="ipnet-card p-5 border-blue-200 bg-gradient-to-r from-blue-50/80 via-white to-blue-50/40">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                                <h3 class="text-sm font-bold text-blue-950 uppercase tracking-wider">
                                    {{ $document->status === 'completed' ? 'Tautan Verifikasi Dokumen Klien' : 'Tautan Tanda Tangan Klien' }}
                                </h3>
                            </div>
                            <p class="text-xs text-blue-900">
                                @if($document->status === 'completed')
                                    Dokumen telah resmi disahkan oleh <strong>{{ $document->client_name }}</strong> pada {{ $document->client_signed_at?->format('d/m/Y H:i') }} WIB.
                                @else
                                    Tanda tangan internal lengkap. Bagikan tautan ini ke PIC Klien (<strong>{{ $document->client_name }}</strong>) untuk pengesahan dokumen.
                                @endif
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                            <div class="relative flex-1 sm:w-80">
                                <input type="text" readonly value="{{ $clientSigningUrl }}" id="clientUrlInput"
                                       class="w-full pl-3 pr-20 py-2 text-xs rounded-xl border border-blue-200 bg-white font-mono text-slate-700 select-all">
                                <button type="button" @click="copyLink()"
                                        class="absolute right-1 top-1 bottom-1 px-3 rounded-lg bg-blue-600 text-white font-bold text-[11px] hover:bg-blue-700 transition-all cursor-pointer">
                                    <span x-text="copied ? 'Tersalin!' : 'Salin'">Salin</span>
                                </button>
                            </div>

                            <a href="{{ $waLink }}" target="_blank"
                               class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-flex items-center justify-center gap-1.5 shadow-xs transition-all">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                <span>Kirim WA</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- KARTU TANDA TANGAN INTERNAL SAYA (JIKA GILIRAN USER INI) --}}
            {{-- KARTU PERMINTAAN PENANDATANGANAN DOKUMEN (FORMAL & KONSISTEN IPNET) --}}
            @if($canSign)
                <div class="ipnet-card p-5 border-red-200 bg-gradient-to-r from-red-50/70 via-white to-red-50/30">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#B91C1C] to-[#8F0A0D] text-white flex items-center justify-center shadow-xs shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900">Permintaan Penandatanganan Dokumen</h3>
                                <p class="text-xs text-slate-500 mt-0.5">Dokumen ini memerlukan tanda tangan dan persetujuan resmi Anda sebagai <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->position ?: 'Pihak Berwenang IPNET' }}).</p>
                            </div>
                        </div>

                        <!-- DUA TOMBOL AKSI: TANDA TANGANI (MERAH GRADIENT IPNET) & TOLAK (ROSE BORDER) -->
                        <div class="flex items-center gap-2.5 shrink-0">
                            <a href="{{ route('digital_signatures.place_signature', $document->id) }}"
                               class="btn-ipnet-primary px-5 py-2.5 rounded-xl text-white font-bold text-xs flex items-center gap-2 shadow-sm transition-all active:scale-95">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <span>Tanda Tangani</span>
                            </a>
                            <button type="button" onclick="rejectDocumentInternal({{ $document->id }})"
                                    class="px-4 py-2.5 rounded-xl bg-white hover:bg-rose-50 text-rose-700 border border-rose-300 font-bold text-xs flex items-center gap-2 shadow-2xs transition-all active:scale-95 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Tolak</span>
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
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                                Penandatangan Internal IPNET
                            </h3>
                            <span class="text-[11px] font-semibold text-slate-500">
                                Mode: {{ $document->workflow_type === 'sequential' ? 'Berurutan' : 'Bebas (Paralel)' }}
                            </span>
                        </div>

                        <div class="space-y-3">
                            @foreach($document->internal_signers ?? [] as $idx => $s)
                                @php
                                    $isSigned = ($s['status'] ?? '') === 'signed';
                                @endphp
                                <div class="p-4 rounded-xl border {{ $isSigned ? 'border-red-200 bg-red-50/15' : 'border-slate-200 bg-white' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $isSigned ? 'bg-gradient-to-br from-[#B91C1C] to-[#8F0A0D] text-white shadow-xs' : 'bg-slate-100 text-slate-500' }}">
                                            @if($isSigned)
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            @else
                                                {{ $idx + 1 }}
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-xs text-slate-900">{{ $s['name'] }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $s['role_title'] }} &bull; {{ $s['email'] }}</div>
                                            @if($isSigned)
                                                <div class="text-[10.5px] text-[#8F0A0D] font-bold mt-0.5 flex items-center gap-1">
                                                    <span>Ditandatangani pada {{ \Carbon\Carbon::parse($s['signed_at'])->format('d/m/Y H:i') }} WIB</span>
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
                                            <div class="bg-white border border-slate-200 rounded-lg p-1 inline-block">
                                                <img src="{{ $s['signature'] }}" alt="Signature" class="h-9 max-w-[120px] object-contain">
                                            </div>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold">Pending</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- 2. Penandatangan PIC Klien --}}
                    <div class="ipnet-card p-5 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                                Penandatangan PIC Klien (Eksternal)
                            </h3>
                            @if($document->client_signed_at)
                                <span class="text-[11px] font-bold text-[#8F0A0D]">✓ Selesai Ditandatangani</span>
                            @else
                                <span class="text-[11px] font-bold text-amber-600">⌛ Menunggu Tanda Tangan Klien</span>
                            @endif
                        </div>

                        <div class="p-4 rounded-xl border {{ $document->client_signed_at ? 'border-red-200 bg-red-50/15' : 'border-slate-200 bg-white' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $document->client_signed_at ? 'bg-gradient-to-br from-[#B91C1C] to-[#8F0A0D] text-white shadow-xs' : 'bg-slate-100 text-slate-700 font-bold' }}">
                                    @if($document->client_signed_at)
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    @else
                                        K
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-xs text-slate-900">{{ $document->client_name ?: 'PIC Klien' }}</div>
                                    <div class="text-[11px] text-slate-500">
                                        {{ $document->client_position ?: 'Penanggung Jawab' }} &bull; {{ $document->client_company ?: '-' }}
                                    </div>
                                    @if($document->client_phone)
                                        <div class="text-[10.5px] text-slate-400">WhatsApp/HP: {{ $document->client_phone }}</div>
                                    @endif
                                    @if($document->client_signed_at)
                                        <div class="text-[10.5px] text-[#8F0A0D] font-bold mt-0.5">
                                            Ditandatangani pada {{ $document->client_signed_at->format('d/m/Y H:i') }} WIB (IP: {{ $document->client_ip }})
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right">
                                @if($document->client_signature)
                                    <div class="bg-white border border-slate-200 rounded-lg p-1 inline-block">
                                        <img src="{{ $document->client_signature }}" alt="Client Signature" class="h-10 max-w-[130px] object-contain">
                                    </div>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold">Belum TTD</span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                {{-- KOLOM 3: INFORMASI DOKUMEN & PREVIEW --}}
                <div class="space-y-6">

                    <div class="ipnet-card p-5 space-y-3.5">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2.5">
                            Informasi Dokumen
                        </h3>
                        <dl class="space-y-2 text-xs">
                            <div>
                                <dt class="text-slate-400 text-[10.5px]">Nomor Berkas:</dt>
                                <dd class="font-mono font-bold text-slate-800">{{ $document->document_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 text-[10.5px]">Nama File Asli:</dt>
                                <dd class="font-medium text-slate-800 truncate" title="{{ $document->file_name }}">{{ $document->file_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-slate-400 text-[10.5px]">Ukuran File:</dt>
                                <dd class="text-slate-800">{{ number_format($document->file_size / 1024, 1) }} KB</dd>
                            </div>
                            @if($document->verification_hash)
                                <div>
                                    <dt class="text-slate-400 text-[10.5px]">Verification Hash:</dt>
                                    <dd class="font-mono text-[10px] text-emerald-700 break-all">{{ substr($document->verification_hash, 0, 24) }}...</dd>
                                </div>
                            @endif
                        </dl>

                        @if($document->verification_hash)
                            <div class="pt-2 border-t border-slate-100">
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
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                            <form action="{{ route('digital_signatures.destroy', $document->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-bold text-slate-400 hover:text-rose-600 transition-colors">
                                    &times; Hapus Dokumen Ini
                                </button>
                            </form>
                        </div>
                    @endif

                </div>

            </div>

            {{-- PRATINJAU DOKUMEN LANGSUNG (INTEGRATED PDF VIEWER) --}}
            <div class="ipnet-card p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-red-50 text-[#8F0A0D] flex items-center justify-center font-bold text-xs border border-red-100 shadow-2xs">
                            PDF
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-slate-900">Pratinjau Dokumen Langsung</h3>
                            <p class="text-xs text-slate-500">Tinjau seluruh isi berkas dokumen tanpa perlu mengunduh terlebih dahulu.</p>
                        </div>
                    </div>

                    <!-- Toolbar PDF Preview -->
                    <div class="flex items-center gap-2 flex-wrap text-xs">
                        <!-- Navigasi Halaman -->
                        <div class="flex items-center bg-slate-50 rounded-xl p-0.5 border border-slate-200">
                            <button type="button" id="prevDetailPdfPage" class="p-1 hover:bg-white rounded-lg text-slate-700 font-bold transition" title="Halaman Sebelumnya">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <span class="px-2 text-slate-800 font-bold font-mono text-[11px]">
                                Hal <span id="currentDetailPdfPage">1</span> / <span id="totalDetailPdfPages">1</span>
                            </span>
                            <button type="button" id="nextDetailPdfPage" class="p-1 hover:bg-white rounded-lg text-slate-700 font-bold transition" title="Halaman Berikutnya">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>

                        <!-- Zoom -->
                        <div class="flex items-center bg-slate-50 rounded-xl p-0.5 border border-slate-200">
                            <button type="button" id="zoomOutDetailPdf" class="px-2 py-0.5 hover:bg-white rounded-lg text-slate-700 font-bold transition" title="Zoom Out">-</button>
                            <span id="zoomDetailPdfLabel" class="px-1.5 text-slate-700 font-mono font-bold text-[11px]">100%</span>
                            <button type="button" id="zoomInDetailPdf" class="px-2 py-0.5 hover:bg-white rounded-lg text-slate-700 font-bold transition" title="Zoom In">+</button>
                        </div>

                        <!-- Buka Tab Baru & Download -->
                        <a href="{{ route('digital_signatures.stream_pdf', $document->id) }}" target="_blank"
                           class="px-3 py-1.5 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs flex items-center gap-1.5 border border-slate-200 transition"
                           title="Buka Berkas di Tab Baru">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            <span>Tab Baru</span>
                        </a>
                        <a href="{{ route('digital_signatures.download_pdf', $document->id) }}"
                           class="btn-ipnet-primary px-3.5 py-1.5 rounded-xl text-white font-bold text-xs flex items-center gap-1.5 shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Unduh PDF</span>
                        </a>
                    </div>
                </div>

                <!-- Canvas Preview Container -->
                <div class="bg-slate-100/80 rounded-2xl p-4 sm:p-6 flex items-center justify-center overflow-auto min-h-[450px] max-h-[850px] relative border border-slate-200">
                    <div id="detailPdfLoading" class="absolute inset-0 flex flex-col items-center justify-center bg-white/80 backdrop-blur-2xs z-10">
                        <div class="w-9 h-9 border-3 border-[#8F0A0D] border-t-transparent rounded-full animate-spin mb-2.5"></div>
                        <span class="text-xs font-bold text-slate-800">Memuat berkas PDF...</span>
                    </div>
                    <canvas id="detailPdfCanvas" class="bg-white rounded-xs shadow-md block max-w-full"></canvas>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/pdfjs/pdf.min.js') }}" onerror="this.src='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js'"></script>
<script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}" onerror="this.src='https://cdn.jsdelivr.net/npm/sweetalert2@11'"></script>
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
        }
    };
}

// Handler Tolak Dokumen Internal dengan SweetAlert2
async function rejectDocumentInternal(docId) {
    if (!window.Swal) {
        const reason = prompt('Masukkan alasan penolakan dokumen:');
        if (!reason) return;
        submitReject(docId, reason);
        return;
    }

    const { value: reason } = await Swal.fire({
        title: 'Tolak Menandatangani Dokumen?',
        text: 'Mohon tuliskan catatan atau alasan mengapa dokumen ini ditolak.',
        input: 'textarea',
        inputPlaceholder: 'Tuliskan alasan penolakan di sini...',
        inputAttributes: {
            'aria-label': 'Alasan penolakan'
        },
        showCancelButton: true,
        confirmButtonColor: '#E11D48',
        cancelButtonColor: '#64748B',
        confirmButtonText: 'Ya, Tolak Dokumen',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value || !value.trim()) {
                return 'Alasan penolakan harus diisi!';
            }
        }
    });

    if (reason) {
        submitReject(docId, reason.trim());
    }
}

async function submitReject(docId, reason) {
    if (window.Swal) {
        Swal.fire({
            title: 'Memproses Penolakan...',
            text: 'Mohon tunggu sebentar...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });
    }

    try {
        const res = await fetch(`{{ url('/digital-signatures') }}/${docId}/reject-internal`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ reason: reason })
        });

        const data = await res.json();
        if (data.success) {
            if (window.Swal) {
                await Swal.fire({
                    icon: 'success',
                    title: 'Dokumen Ditolak',
                    text: 'Penolakan dokumen telah dicatat dalam riwayat dokumen.',
                    confirmButtonColor: '#8F0A0D'
                });
            } else {
                alert('Dokumen berhasil ditolak.');
            }
            window.location.reload();
        } else {
            if (window.Swal) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Terjadi kesalahan.', confirmButtonColor: '#8F0A0D' });
            } else {
                alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            }
        }
    } catch (e) {
        if (window.Swal) {
            Swal.fire({ icon: 'error', title: 'Koneksi Gagal', text: 'Gagal menghubungi server.', confirmButtonColor: '#8F0A0D' });
        } else {
            alert('Gagal menghubungi server.');
        }
    }
}

// Inisialisasi PDF.js Viewer Langsung pada Halaman Detail
document.addEventListener('DOMContentLoaded', function() {
    const pdfUrl = @json(route('digital_signatures.stream_pdf', $document->id));
    const canvas = document.getElementById('detailPdfCanvas');
    if (!canvas) return;

    if (window.pdfjsLib) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = "{{ asset('vendor/pdfjs/pdf.worker.min.js') }}";
    }

    const ctx = canvas.getContext('2d');
    const loadingEl = document.getElementById('detailPdfLoading');
    const curPageEl = document.getElementById('currentDetailPdfPage');
    const totPagesEl = document.getElementById('totalDetailPdfPages');
    const zoomLabel = document.getElementById('zoomDetailPdfLabel');

    let pdfDoc = null;
    let pageNum = 1;
    let totalPages = 1;
    let scale = 1.0;

    async function loadPdf() {
        try {
            if (!window.pdfjsLib) return;
            const loadingTask = pdfjsLib.getDocument({ url: pdfUrl });
            pdfDoc = await loadingTask.promise;
            totalPages = pdfDoc.numPages;
            totPagesEl.textContent = totalPages;
            await renderPage(1);
            if (loadingEl) loadingEl.classList.add('hidden');
        } catch (e) {
            console.error('PDF detail render error:', e);
            if (loadingEl) {
                loadingEl.innerHTML = '<span class="text-xs text-rose-600 font-bold">Gagal memuat pratinjau PDF.</span>';
            }
        }
    }

    async function renderPage(num) {
        if (!pdfDoc) return;
        const page = await pdfDoc.getPage(num);
        const viewport = page.getViewport({ scale: scale });
        const outputScale = window.devicePixelRatio || 1;

        canvas.width = Math.floor(viewport.width * outputScale);
        canvas.height = Math.floor(viewport.height * outputScale);
        canvas.style.width = Math.floor(viewport.width) + "px";
        canvas.style.height = Math.floor(viewport.height) + "px";

        const transform = outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null;
        await page.render({ canvasContext: ctx, transform: transform, viewport: viewport }).promise;

        pageNum = num;
        curPageEl.textContent = pageNum;
    }

    document.getElementById('prevDetailPdfPage')?.addEventListener('click', () => {
        if (pageNum > 1) renderPage(pageNum - 1);
    });
    document.getElementById('nextDetailPdfPage')?.addEventListener('click', () => {
        if (pageNum < totalPages) renderPage(pageNum + 1);
    });

    document.getElementById('zoomInDetailPdf')?.addEventListener('click', () => {
        scale = Math.min(2.2, scale + 0.15);
        zoomLabel.textContent = Math.round(scale * 100) + '%';
        renderPage(pageNum);
    });
    document.getElementById('zoomOutDetailPdf')?.addEventListener('click', () => {
        scale = Math.max(0.5, scale - 0.15);
        zoomLabel.textContent = Math.round(scale * 100) + '%';
        renderPage(pageNum);
    });

    loadPdf();
});
</script>
@endpush
@endsection
