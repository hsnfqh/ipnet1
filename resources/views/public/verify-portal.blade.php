@extends('layouts.app')

@section('title', 'Portal Verifikasi & Audit Integritas Dokumen - PT IP Network Solusindo')

@push('styles')
<style>
    /* ========================================================
       Smooth Fluid Entrance & Micro-Animations (Matching Login & Dashboard)
       ======================================================== */
    @keyframes heroReveal {
        0% { opacity: 0; transform: translateY(24px) scale(0.98); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(18px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes logoFloat {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-7px) rotate(0.5deg); }
    }

    @keyframes pulseGlow {
        0%, 100% { opacity: 0.35; transform: scale(1); }
        50% { opacity: 0.65; transform: scale(1.1); }
    }

    @keyframes cardEntrance {
        0% { opacity: 0; transform: translateY(20px) scale(0.98); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Flowing Red Rim Beam Animation at Top of Card */
    @keyframes rimShimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .card-top-rim {
        background: linear-gradient(90deg, transparent 0%, #8F0A0D 20%, #C61828 45%, #FFA8B2 50%, #C61828 55%, #8F0A0D 80%, transparent 100%);
        background-size: 200% 100%;
        animation: rimShimmer 3.5s linear infinite;
    }

    .anim-hero-reveal {
        animation: heroReveal 0.7s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-fade-up {
        animation: fadeUpStagger 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-card-enter {
        animation: cardEntrance 0.65s cubic-bezier(0.16, 1, 0.3, 1) 0.1s both;
    }

    .animate-logo-float {
        animation: logoFloat 4.5s ease-in-out infinite;
    }

    .animate-pulse-glow {
        animation: pulseGlow 6s ease-in-out infinite;
    }

    /* Primary Action Button (Matching Login Button) */
    .ipnet-login-btn {
        background: linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%);
        background-size: 200% auto;
        box-shadow: 0 4px 16px rgba(143, 10, 13, 0.28);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .ipnet-login-btn:hover {
        background-position: right center;
        box-shadow: 0 8px 26px rgba(143, 10, 13, 0.38);
        transform: translateY(-2px);
    }
    .ipnet-login-btn:active {
        transform: translateY(0) scale(0.99);
    }

    /* Form Input Micro-Interactions */
    .ipnet-input {
        background-color: #FFFFFF;
        border: 1.5px solid #CBD5E1;
        border-radius: 14px;
        color: #1E293B;
        font-size: 13.5px;
        font-weight: 500;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        outline: none;
    }
    .ipnet-input:hover {
        border-color: #94A3B8;
    }
    .ipnet-input:focus {
        border-color: #8F0A0D;
        box-shadow: 0 0 0 3.5px rgba(143, 10, 13, 0.12);
        background-color: #FFFFFF;
        transform: translateY(-1px);
    }

    .dropzone-active {
        border-color: #8F0A0D !important;
        background-color: rgba(254, 242, 242, 0.6) !important;
        transform: scale(1.01);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen w-full relative flex flex-col justify-center items-center p-3 sm:p-6 text-white overflow-x-hidden select-none bg-[#750608]">

    {{-- ======================================================== --}}
    {{-- Layered Geometric Faceted Red Background (Matching Login & Dashboard) --}}
    {{-- ======================================================== --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden select-none z-0">
        <svg class="w-full h-full object-cover" viewBox="0 0 1000 1000" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="portalFacetGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#C61828" />
                    <stop offset="50%" stop-color="#9E0E1D" />
                    <stop offset="100%" stop-color="#7A0813" />
                </linearGradient>
                <linearGradient id="portalFacetGrad2" x1="100%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#B01423" />
                    <stop offset="100%" stop-color="#5A040C" />
                </linearGradient>
                <linearGradient id="portalFacetGrad3" x1="0%" y1="100%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#830B17" />
                    <stop offset="100%" stop-color="#420207" />
                </linearGradient>
                <linearGradient id="portalFacetHighlight" x1="0%" y1="0%" x2="100%" y2="50%">
                    <stop offset="0%" stop-color="#FFA8B2" stop-opacity="0.25" />
                    <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0" />
                </linearGradient>
                <filter id="portalFacetShadow" x="-10%" y="-10%" width="130%" height="130%">
                    <feDropShadow dx="-8" dy="12" stdDeviation="16" flood-color="#2A0205" flood-opacity="0.5" />
                </filter>
                <pattern id="portal-grid" width="32" height="32" patternUnits="userSpaceOnUse">
                    <path d="M 32 0 L 0 0 0 32" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="1"/>
                </pattern>
            </defs>

            <!-- Base Geometric Background -->
            <rect width="1000" height="1000" fill="url(#portalFacetGrad1)" />

            <!-- Grid Texture Overlay -->
            <rect width="1000" height="1000" fill="url(#portal-grid)" />

            <!-- Large Diagonal Angled Planes -->
            <polygon points="0,0 600,0 200,1000 0,1000" fill="url(#portalFacetGrad2)" opacity="0.95" />
            <polygon points="180,0 820,0 1000,1000 450,1000" fill="url(#portalFacetGrad1)" filter="url(#portalFacetShadow)" />
            <polygon points="520,0 1000,0 1000,1000 780,1000" fill="url(#portalFacetGrad3)" filter="url(#portalFacetShadow)" />

            <!-- Ambient Angular Highlights -->
            <polygon points="0,0 520,0 900,1000 280,1000" fill="url(#portalFacetHighlight)" />
        </svg>
    </div>

    {{-- Ambient Light Orbs --}}
    <div class="fixed -top-24 -left-24 w-96 h-96 rounded-full bg-rose-400/20 blur-3xl pointer-events-none animate-pulse-glow z-0"></div>
    <div class="fixed bottom-10 right-0 w-80 h-80 rounded-full bg-black/40 blur-3xl pointer-events-none z-0"></div>

    {{-- ======================================================== --}}
    {{-- Center Content: Floating Logo + Center Executive Card    --}}
    {{-- ======================================================== --}}
    <main class="relative z-10 w-full max-w-2xl mx-auto my-auto flex flex-col items-center">
        
        {{-- Floating Logo Ring --}}
        <div class="relative mb-2.5 anim-hero-reveal">
            <div class="absolute inset-0 bg-white/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="relative w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/10 backdrop-blur-md border border-white/25 flex items-center justify-center shadow-[0_12px_28px_rgba(0,0,0,0.3)] animate-logo-float transition-transform hover:scale-105 cursor-pointer">
                <img src="{{ asset('images/ipnet1.png') }}" alt="IP Network Solusindo" class="h-11 sm:h-13 w-auto object-contain drop-shadow-md">
            </div>
        </div>

        {{-- Center Titles --}}
        <div class="text-center max-w-lg mb-4">
            <h1 class="font-display font-black text-[22px] sm:text-[26px] text-white tracking-tight leading-tight drop-shadow-sm anim-fade-up">
                PT IP Network Solusindo
            </h1>
            <div class="inline-flex items-center gap-2 mt-1 anim-fade-up">
                <span class="h-[1px] w-5 bg-white/40"></span>
                <p class="text-rose-100 text-[11px] sm:text-[11.5px] font-bold tracking-[2px] uppercase">
                    Portal Verifikasi &amp; Audit Integritas Dokumen
                </p>
                <span class="h-[1px] w-5 bg-white/40"></span>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- Modern White Executive Center Card                       --}}
        {{-- ======================================================== --}}
        <div class="w-full bg-white border border-[#E2E8F0] rounded-[22px] p-5 sm:p-7 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.4)] relative z-10 anim-card-enter overflow-hidden text-[#1E293B]">
            
            {{-- Flowing Red Shimmer Rim Beam at Top of Card --}}
            <div class="absolute top-0 left-0 right-0 h-[4px] card-top-rim"></div>

            {{-- Segmented Navigation Tabs --}}
            <div class="bg-[#F1F5F9] p-1.5 rounded-2xl flex gap-1 mb-5 border border-[#E2E8F0]">
                <button type="button" id="tabUploadBtn" onclick="switchVerificationTab('upload')"
                        class="flex-1 py-2 px-3 sm:px-4 rounded-xl text-[12.5px] sm:text-[13px] font-bold transition-all flex items-center justify-center gap-2 cursor-pointer bg-white text-[#8F0A0D] shadow-sm border border-[#E2E8F0]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <span>Unggah Berkas PDF (Deteksi Selisih)</span>
                </button>
                <button type="button" id="tabDocBtn" onclick="switchVerificationTab('number')"
                        class="flex-1 py-2 px-3 sm:px-4 rounded-xl text-[12.5px] sm:text-[13px] font-bold transition-all flex items-center justify-center gap-2 cursor-pointer text-[#64748B] hover:text-[#1E293B]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Cek Nomor Dokumen</span>
                </button>
            </div>

            {{-- Form Audit Dokumen --}}
            <form action="{{ route('public.verify.inspect') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Mode 1: Upload File PDF Dropzone --}}
                <div id="uploadSection" class="transition-all">
                    <div id="dropzoneBox" onclick="document.getElementById('pdfInput').click()"
                         class="border-2 border-dashed border-[#CBD5E1] hover:border-[#8F0A0D] bg-[#F8FAFC] hover:bg-rose-50/30 rounded-2xl p-5 sm:p-7 text-center cursor-pointer transition-all duration-200 group flex flex-col items-center justify-center">
                        <div class="w-12 h-12 rounded-xl bg-[#8F0A0D]/10 text-[#8F0A0D] group-hover:bg-[#8F0A0D] group-hover:text-white flex items-center justify-center mb-2.5 transition-colors duration-200 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <p class="text-[13.5px] sm:text-[14px] font-bold text-[#1E293B] group-hover:text-[#8F0A0D] transition-colors">
                            Pilih atau Tarik (Drag &amp; Drop) Berkas PDF Laporan
                        </p>
                        <p class="text-[11.5px] text-[#64748B] mt-1 max-w-sm">
                            Unggah berkas PDF yang diserahkan oleh teknisi lapangan untuk menguji keabsahan dan mendeteksi perubahan data.
                        </p>
                        
                        <div id="fileSelectedBadge" class="hidden mt-3 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11.5px] font-bold">
                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span id="fileNameText">Berkas terpilih</span>
                        </div>
                    </div>
                    <input type="file" id="pdfInput" name="pdf_file" accept="application/pdf" class="hidden" onchange="handleFileSelected(this)">
                </div>

                {{-- Mode 2: Input Manual Nomor Dokumen --}}
                <div id="numberSection" class="hidden transition-all">
                    <label class="block text-[13px] font-bold text-[#334155] mb-2">
                        Nomor Dokumen Rekapitulasi Kerja
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#94A3B8]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="text" name="document_number" id="documentNumberInput"
                               value="{{ $prefillDocNumber ?? '' }}"
                               placeholder="Contoh: DOC-ACT-202610-0001"
                               class="w-full pl-11 pr-4 py-3 ipnet-input font-mono text-[13.5px] uppercase">
                    </div>
                    <p class="text-[11.5px] text-[#64748B] mt-1.5">
                        Nomor dokumen tertera di bagian pojok kanan atas kop laporan atau di bawah QR Code verifikasi.
                    </p>
                </div>

                {{-- Action Button --}}
                <button type="submit" class="w-full mt-4 py-3 px-5 rounded-xl font-bold text-[13.5px] text-white flex items-center justify-center gap-2 ipnet-login-btn cursor-pointer">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Uji Keabsahan &amp; Deteksi Selisih</span>
                </button>
            </form>

            {{-- ======================================================== --}}
            {{-- HASIL AUDIT / VERIFIKASI (JIKA ADA RESPONSE)             --}}
            {{-- ======================================================== --}}
            @if(isset($result))
                <div class="mt-7 pt-6 border-t border-[#E2E8F0]">
                    
                    @if($result['status'] === 'authentic')
                        {{-- STATUS 1: DOKUMEN SAH & ASLI 100% --}}
                        <div class="border border-emerald-200 bg-emerald-50/90 rounded-2xl p-5 sm:p-6 mb-4">
                            <div class="flex items-start gap-3.5 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-[16px] font-bold text-emerald-950 leading-tight">
                                        DOKUMEN ASLI &amp; TERVERIFIKASI RESMI
                                    </h3>
                                    <p class="text-[12.5px] text-emerald-800 mt-1 leading-relaxed">
                                        Data pada berkas identik 100% dengan arsip bertanda tangan digital resmi di server PT IP Network Solusindo. Tidak terdeteksi manipulasi data.
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-2 text-[12.5px] text-emerald-900 border-t border-emerald-200/70 pt-3">
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1">
                                    <span class="text-emerald-700">Nomor Dokumen:</span>
                                    <span class="font-mono font-bold text-[13px] text-emerald-950 bg-white/80 px-2.5 py-0.5 rounded-lg border border-emerald-200 inline-block">
                                        {{ $result['document_number'] }}
                                    </span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1">
                                    <span class="text-emerald-700">Nama Proyek:</span>
                                    <span class="font-semibold text-emerald-950">
                                        {{ $result['document']->project?->name ?? 'Internal Project' }}
                                    </span>
                                </div>
                                @if(!empty($result['integrity_hash']))
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1 pt-1">
                                        <span class="text-emerald-700 shrink-0">SHA-256 Hash Segel:</span>
                                        <span class="font-mono text-[10.5px] text-emerald-800 bg-white/80 px-2 py-0.5 rounded border border-emerald-200 break-all">
                                            {{ $result['integrity_hash'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- 3-Tier Signatures Status --}}
                            <div class="mt-4 pt-3 border-t border-emerald-200/70">
                                <p class="text-[11px] font-bold tracking-wider uppercase text-emerald-800 mb-2">
                                    Tanda Tangan Digital Terverifikasi:
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                    <div class="bg-white rounded-xl p-2.5 text-center border border-emerald-200 shadow-xs">
                                        <div class="text-[12px] font-bold text-[#1E293B] truncate">
                                            {{ $result['document']->picUser?->name ?? 'Syaiful Amin' }}
                                        </div>
                                        <div class="text-[10px] text-[#64748B]">PIC Lapangan</div>
                                        <span class="inline-block mt-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                            Signed ✓
                                        </span>
                                    </div>

                                    <div class="bg-white rounded-xl p-2.5 text-center border border-emerald-200 shadow-xs">
                                        <div class="text-[12px] font-bold text-[#1E293B] truncate">
                                            {{ $result['document']->leadUser?->name ?? 'Nugraha Pratama' }}
                                        </div>
                                        <div class="text-[10px] text-[#64748B]">Lead Engineer</div>
                                        <span class="inline-block mt-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                            Verified ✓
                                        </span>
                                    </div>

                                    <div class="bg-white rounded-xl p-2.5 text-center border border-emerald-200 shadow-xs">
                                        <div class="text-[12px] font-bold text-[#1E293B] truncate">
                                            {{ $result['document']->head_name ?? 'Susanto Djaya' }}
                                        </div>
                                        <div class="text-[10px] text-[#64748B]">Head of Division</div>
                                        <span class="inline-block mt-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                            Approved ✓
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Direct PDF View Button --}}
                            <div class="mt-4 pt-3 border-t border-emerald-200/70">
                                <a href="{{ url('/verify-document/' . $result['document_number']) }}" target="_blank"
                                   class="w-full py-2.5 px-4 rounded-xl text-[13px] font-bold text-white flex items-center justify-center gap-2 ipnet-login-btn">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Buka Dokumen PDF Resmi dari Server
                                </a>
                            </div>
                        </div>

                    @elseif($result['status'] === 'discrepancy_detected')
                        {{-- STATUS 2: TERDETEKSI SELISIH / MODIFIKASI DATA --}}
                        <div class="border border-rose-200 bg-rose-50/90 rounded-2xl p-5 sm:p-6 mb-4">
                            <div class="flex items-start gap-3.5 mb-4">
                                <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-sm mt-0.5">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-[16px] font-bold text-rose-950 leading-tight">
                                        PERINGATAN: TERDETEKSI SELISIH DATA (DISCREPANCY DETECTED)!
                                    </h3>
                                    <p class="text-[12.5px] text-rose-800 mt-1 leading-relaxed">
                                        Berkas PDF yang Anda unggah <strong>TIDAK COCOK</strong> dengan arsip resmi bertanda tangan digital di server PT IP Network Solusindo. Terdapat indikasi modifikasi di luar sistem.
                                    </p>
                                </div>
                            </div>

                            <div class="text-[12.5px] text-rose-900 mb-3 border-t border-rose-200/70 pt-3">
                                <strong>Nomor Dokumen:</strong> <span class="font-mono font-bold">{{ $result['document_number'] }}</span> &bull;
                                <strong>Jumlah Perbedaan:</strong> <span class="text-rose-600 font-bold">{{ $result['total_discrepancies'] }} Poin Ketidaksesuaian</span>
                            </div>

                            {{-- Tabel Perbandingan Selisih Data --}}
                            <div class="overflow-x-auto rounded-xl border border-rose-200 bg-white">
                                <table class="w-full text-left text-[12px]">
                                    <thead class="bg-rose-100/60 text-rose-950 font-bold border-b border-rose-200">
                                        <tr>
                                            <th class="p-3">Bagian / Kolom</th>
                                            <th class="p-3">Data Sah Resmi di Server IP-Net</th>
                                            <th class="p-3">Data di Berkas yang Anda Unggah</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-rose-100">
                                        @foreach($result['discrepancies'] as $diff)
                                            <tr class="hover:bg-rose-50/50 transition-colors">
                                                <td class="p-3 align-top">
                                                    <span class="font-bold text-[#1E293B]">{{ $diff['field'] }}</span>
                                                    <div class="text-[10.5px] text-[#64748B]">{{ $diff['desc'] }}</div>
                                                </td>
                                                <td class="p-3 align-top font-semibold text-emerald-800">
                                                    <span class="inline-block px-2 py-0.5 rounded bg-emerald-50 border border-emerald-200">
                                                        ✓ {{ $diff['expected'] }}
                                                    </span>
                                                </td>
                                                <td class="p-3 align-top font-semibold text-rose-800">
                                                    <span class="inline-block px-2 py-0.5 rounded bg-rose-50 border border-rose-200">
                                                        ✗ {{ $diff['found_in_file'] }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Source of Truth Action --}}
                            <div class="mt-4 pt-3 border-t border-rose-200/70">
                                <a href="{{ url('/verify-document/' . $result['document_number']) }}" target="_blank"
                                   class="w-full py-2.5 px-4 rounded-xl text-[13px] font-bold text-white flex items-center justify-center gap-2 ipnet-login-btn">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Lihat Dokumen Asli Resmi (Server Source of Truth)
                                </a>
                            </div>
                        </div>

                    @else
                        {{-- STATUS 3: DOKUMEN TIDAK TERDAFTAR --}}
                        <div class="border border-amber-200 bg-amber-50/90 rounded-2xl p-5 mb-4 text-amber-950">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-[14px]">DOKUMEN TIDAK DITEMUKAN</h4>
                                    <p class="text-[12px] text-amber-800 mt-0.5">
                                        {{ $result['message'] ?? 'Nomor dokumen tidak terdaftar pada basis data resmi PT IP Network Solusindo.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            @endif

        </div>

    </main>

    {{-- Bottom Copyright Note --}}
    <footer class="relative z-10 text-center text-white/70 text-[11px] font-medium tracking-wide mt-3.5">
        &copy; {{ date('Y') }} PT IP Network Solusindo &bull; Enterprise Document Integrity System
    </footer>

</div>

<script>
    function switchVerificationTab(mode) {
        const uploadBtn = document.getElementById('tabUploadBtn');
        const docBtn = document.getElementById('tabDocBtn');
        const uploadSec = document.getElementById('uploadSection');
        const numberSec = document.getElementById('numberSection');

        if (mode === 'upload') {
            uploadBtn.className = "flex-1 py-2 px-3 sm:px-4 rounded-xl text-[12.5px] sm:text-[13px] font-bold transition-all flex items-center justify-center gap-2 cursor-pointer bg-white text-[#8F0A0D] shadow-sm border border-[#E2E8F0]";
            docBtn.className = "flex-1 py-2 px-3 sm:px-4 rounded-xl text-[12.5px] sm:text-[13px] font-bold transition-all flex items-center justify-center gap-2 cursor-pointer text-[#64748B] hover:text-[#1E293B]";
            uploadSec.classList.remove('hidden');
            numberSec.classList.add('hidden');
        } else {
            docBtn.className = "flex-1 py-2 px-3 sm:px-4 rounded-xl text-[12.5px] sm:text-[13px] font-bold transition-all flex items-center justify-center gap-2 cursor-pointer bg-white text-[#8F0A0D] shadow-sm border border-[#E2E8F0]";
            uploadBtn.className = "flex-1 py-2 px-3 sm:px-4 rounded-xl text-[12.5px] sm:text-[13px] font-bold transition-all flex items-center justify-center gap-2 cursor-pointer text-[#64748B] hover:text-[#1E293B]";
            numberSec.classList.remove('hidden');
            uploadSec.classList.add('hidden');
        }
    }

    function handleFileSelected(input) {
        const badge = document.getElementById('fileSelectedBadge');
        const text = document.getElementById('fileNameText');
        if (input.files && input.files[0]) {
            badge.classList.remove('hidden');
            badge.classList.add('inline-flex');
            text.textContent = input.files[0].name + ' (' + (input.files[0].size / 1024).toFixed(1) + ' KB)';
        } else {
            badge.classList.add('hidden');
            badge.classList.remove('inline-flex');
        }
    }

    // Drag & Drop
    const dropzone = document.getElementById('dropzoneBox');
    if (dropzone) {
        ['dragenter', 'dragover'].forEach(name => {
            dropzone.addEventListener(name, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('dropzone-active');
            }, false);
        });
        ['dragleave', 'drop'].forEach(name => {
            dropzone.addEventListener(name, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('dropzone-active');
            }, false);
        });
        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            if (files && files[0] && files[0].type === 'application/pdf') {
                const input = document.getElementById('pdfInput');
                input.files = files;
                handleFileSelected(input);
            }
        });
    }
</script>
@endsection
