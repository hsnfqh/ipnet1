@extends('layouts.app')

@section('title', 'Dashboard - PT IP Network Solusindo')

@push('styles')
<style>
    /* ========================================================
       IPNET Official Brand Design System (ipnetsolusindo.com)
       ======================================================== */
    :root {
        --ipnet-primary: #8F0A0D;
        --ipnet-primary-hover: #73080A;
        --ipnet-card-bg: #FFFFFF;
        --ipnet-card-border: #E2E8F0;
        --ipnet-text-main: #1E293B;
    }

    .ipnet-hero-banner {
        background: linear-gradient(125deg, #B81525 0%, #9E0E1D 40%, #830B17 75%, #63050F 100%);
        position: relative;
        overflow: hidden;
    }

    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .ipnet-card-blush {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .ipnet-card-blush:hover, .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }

    .ipnet-metric-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 16px 18px;
        position: relative;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }

    .ipnet-metric-card:hover {
        transform: translateY(-2px);
        border-color: #CBD5E1;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .ipnet-badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #8F0A0D;
        display: inline-block;
        margin-right: 8px;
    }

    .btn-ipnet-primary {
        background-color: #8F0A0D;
        color: #FFFFFF;
        transition: all 0.2s ease;
    }

    .btn-ipnet-primary:hover {
        background-color: #73080A;
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.3);
        transform: translateY(-1px);
    }

    /* ========================================================
       Smooth Fluid Entrance Animations
       ======================================================== */
    @keyframes heroReveal {
        0% {
            opacity: 0;
            transform: translateY(22px) scale(0.985);
        }
        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes fadeUpStagger {
        0% {
            opacity: 0;
            transform: translateY(18px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .anim-hero-reveal {
        animation: heroReveal 0.65s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-fade-up {
        animation: fadeUpStagger 0.55s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-delay-1 { animation-delay: 0.08s !important; }
    .anim-delay-2 { animation-delay: 0.14s !important; }
    .anim-delay-3 { animation-delay: 0.20s !important; }
    .anim-delay-4 { animation-delay: 0.26s !important; }
    .anim-delay-5 { animation-delay: 0.32s !important; }
    .anim-delay-6 { animation-delay: 0.38s !important; }
    .anim-delay-7 { animation-delay: 0.44s !important; }
    .anim-delay-8 { animation-delay: 0.52s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">
            
            {{-- ======================================================== --}}
            {{-- 1. EXECUTIVE HERO BANNER (COMPACT IPNET BRAND PATTERN)   --}}
            {{-- ======================================================== --}}
            <div class="ipnet-hero-banner rounded-2xl px-5 py-4 sm:px-6 sm:py-4.5 text-white shadow-md shadow-red-950/15 relative anim-hero-reveal">
                {{-- Layered Geometric Faceted Red Planes --}}
                <div class="absolute inset-0 pointer-events-none overflow-hidden select-none">
                    <svg class="w-full h-full object-cover" viewBox="0 0 1440 200" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="redGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#C61828" />
                                <stop offset="100%" stop-color="#9E0E1D" />
                            </linearGradient>
                            <linearGradient id="redGrad2" x1="100%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#B01423" />
                                <stop offset="100%" stop-color="#7A0813" />
                            </linearGradient>
                            <linearGradient id="redGrad3" x1="0%" y1="100%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#940E1B" />
                                <stop offset="100%" stop-color="#5A040C" />
                            </linearGradient>
                            <linearGradient id="redGradHighlight" x1="0%" y1="0%" x2="100%" y2="50%">
                                <stop offset="0%" stop-color="#FFA8B2" stop-opacity="0.20" />
                                <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0" />
                            </linearGradient>
                            <filter id="facetDropShadow" x="-10%" y="-10%" width="130%" height="130%">
                                <feDropShadow dx="-8" dy="10" stdDeviation="14" flood-color="#3A0207" flood-opacity="0.4" />
                            </filter>
                        </defs>

                        <!-- Base Background -->
                        <rect width="1440" height="200" fill="url(#redGrad1)" />

                        <!-- Top-Left Large Diagonal Angled Plane -->
                        <polygon points="0,0 650,0 280,200 0,200" fill="url(#redGrad1)" />

                        <!-- Intersecting Broad Diagonal Facet Strip -->
                        <polygon points="220,0 850,0 1300,200 600,200" fill="url(#redGrad2)" filter="url(#facetDropShadow)" />

                        <!-- Crossing Foreground Diagonal Bright Red Plane -->
                        <polygon points="0,0 520,0 1080,200 480,200" fill="url(#redGrad1)" opacity="0.9" filter="url(#facetDropShadow)" />

                        <!-- Right Edge Deeper Contrast Facet -->
                        <polygon points="780,0 1440,0 1440,200 1100,200" fill="url(#redGrad3)" filter="url(#facetDropShadow)" />

                        <!-- Soft Angular Ambient Highlight Overlays -->
                        <polygon points="0,0 680,0 1120,200 380,200" fill="url(#redGradHighlight)" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center mb-1.5 px-2.5 py-0.5 text-[10.5px] font-bold text-white bg-white/15 backdrop-blur-md rounded-full border border-white/20 tracking-wider uppercase">
                            <span class="w-1.5 h-1.5 mr-1.5 bg-white rounded-full inline-block"></span>
                            PT IP NETWORK SOLUSINDO &bull; {{ ($isExecutive ?? false) ? 'EXECUTIVE DASHBOARD' : 'DASHBOARD' }}
                        </div>

                        <h1 class="text-[18px] sm:text-[21px] font-extrabold text-white tracking-tight leading-tight">
                            Selamat Datang, {{ auth()->user()->name }}
                        </h1>
                        <p class="mt-0.5 text-[12.5px] text-white/80 leading-relaxed line-clamp-1">
                            @if($isExecutive ?? false)
                                Pantau ringkasan pipeline sales komersial, perolehan nilai kontrak proyek, dan persetujuan draft.
                            @else
                                Pantau kapasitas tim teknisi, progres instalasi proyek jaringan, dan jadwal operasional lapangan.
                            @endif
                        </p>
                    </div>

                    {{-- Quick Action Buttons (Untuk Eksekutif/Direktur/Head: Tanpa tombol tambah baru, fokus review & view) --}}
                    <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                        @if($isExecutive ?? false)
                            @if(isset($pendingDraftApprovals) && $pendingDraftApprovals->count() > 0)
                                <a href="{{ route('projects.index', ['status' => 'Draft']) }}" class="px-3.5 py-2 rounded-xl bg-white text-[#8F0A0D] font-bold text-[12.5px] hover:bg-[#FFF7F6] hover:scale-[1.02] transition-all shadow-sm flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-red-600 animate-pulse"></span>
                                    <span>Persetujuan Draft</span>
                                    <span class="px-1.5 py-0.2 bg-[#8F0A0D] text-white text-[10px] rounded-full font-black ml-1">{{ $pendingDraftApprovals->count() }}</span>
                                </a>
                            @endif
                            <a href="{{ route('acquire.index') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 hover:scale-[1.02] border border-white/25 text-white font-bold text-[12.5px] transition-all flex items-center gap-1.5 backdrop-blur-sm">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.651V9.35"/>
                                </svg>
                                <span>Peluang & Pipeline</span>
                            </a>
                            <a href="{{ route('schedules.index') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 hover:scale-[1.02] border border-white/25 text-white font-bold text-[12.5px] transition-all flex items-center gap-1.5 backdrop-blur-sm">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Jadwal Kerja</span>
                            </a>
                        @else
                            <a href="{{ route('tasks.index') }}" class="px-3.5 py-2 rounded-xl bg-white text-[#8F0A0D] font-bold text-[12.5px] hover:bg-[#FFF7F6] hover:scale-[1.02] transition-all shadow-sm flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Delegasi Tugas Baru</span>
                            </a>
                            <a href="{{ route('schedules.index') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 hover:scale-[1.02] border border-white/25 text-white font-bold text-[12.5px] transition-all flex items-center gap-1.5 backdrop-blur-sm">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Jadwal Kerja</span>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 1.5 PERMOHONAN PERSETUJUAN DRAFT DARI SALES (DUAL SIGN-OFF) --}}
            {{-- ======================================================== --}}
            @if(isset($pendingDraftApprovals) && $pendingDraftApprovals->count() > 0)
                <div class="rounded-2xl p-5 border-2 border-red-200 bg-gradient-to-br from-red-50/90 via-white to-amber-50/40 shadow-md shadow-red-950/5 space-y-3.5 anim-fade-up">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2.5">
                            <span class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-[#8F0A0D]"></span>
                            </span>
                            <h3 class="text-[14px] font-extrabold text-[#1E293B] flex items-center gap-2">
                                <span>Permohonan Persetujuan Draft Proyek dari Sales</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-[#8F0A0D] text-white shadow-xs">
                                    {{ $pendingDraftApprovals->count() }} Menunggu Otorisasi Anda
                                </span>
                            </h3>
                        </div>
                        <span class="text-[11.5px] font-semibold text-[#64748B]">Otorisasi dan persetujuan komersial sebelum diserahkan ke PMO / Tim Teknis</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 pt-1">
                        @foreach($pendingDraftApprovals as $pProj)
                            @php
                                $hd = is_array($pProj->handover_data) ? $pProj->handover_data : [];
                                $dApp = $hd['draft_approvals'] ?? [];
                                $isHeadWaiting = !empty($dApp['head']['assigned']) && empty($dApp['head']['approved']);
                                $isDirWaiting = !empty($dApp['director']['assigned']) && empty($dApp['director']['approved']);
                                $salesNotes = $dApp['head']['sales_notes'] ?? ($dApp['director']['sales_notes'] ?? null);
                            @endphp
                            <div class="p-4 bg-white rounded-xl border border-gray-200 shadow-2xs hover:border-red-400 transition flex flex-col justify-between space-y-3">
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-md {{ $isHeadWaiting ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-red-50 text-[#8F0A0D] border border-red-200' }}">
                                            {{ $isHeadWaiting ? 'Review Teknis (Head Divisi)' : 'Otorisasi Direktur' }}
                                        </span>
                                        <span class="text-[10.5px] font-semibold text-gray-500 truncate max-w-[140px]">
                                            Sales: {{ $pProj->sales_name ?: ($pProj->creator ? $pProj->creator->name : 'Sales') }}
                                        </span>
                                    </div>
                                    <h4 class="text-[13.5px] font-extrabold text-gray-900 leading-snug line-clamp-1">{{ $pProj->name }}</h4>
                                    <div class="text-[11.5px] text-gray-500 flex items-center gap-1.5">
                                        <span>Klien:</span>
                                        <span class="font-bold text-gray-800 truncate">{{ $pProj->client ?: '-' }}</span>
                                    </div>
                                    @if($pProj->contract_value)
                                        <div class="text-[11.5px] text-[#8F0A0D] font-black">
                                            {{ \App\Helpers\CurrencyHelper::formatRupiah($pProj->contract_value) }}
                                        </div>
                                    @endif
                                    @if($salesNotes)
                                        <div class="text-[11px] text-gray-600 bg-gray-50 p-2 rounded-lg border border-gray-100 italic line-clamp-2">
                                            "{{ $salesNotes }}"
                                        </div>
                                    @endif
                                </div>
                                <a href="{{ route('projects.show', $pProj->id) }}" class="inline-flex items-center justify-center gap-1.5 w-full py-2 px-3.5 rounded-xl text-xs font-bold text-white btn-ipnet-primary transition shadow-2xs hover:scale-[1.01]">
                                    <span>Review & Setujui Sekarang</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($isExecutive ?? false)
                {{-- ======================================================== --}}
                {{-- KONTEN EKSEKUTIF (HARIYADI & SUSANTO): HANYA KONEK KE SALES --}}
                {{-- ======================================================== --}}

                {{-- 5 SUMMARY EXECUTIVE SALES METRIC CARDS --}}
                <div class="anim-fade-up anim-delay-1">
                    <div class="flex items-center justify-between mb-3.5">
                        <div>
                            <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                <span class="ipnet-badge-dot"></span> RINGKASAN SALES & PIPELINE
                            </p>
                            <h2 class="text-[18px] font-bold text-[#292929] tracking-tight">Performa Nilai Proyek & Pipeline Komersial</h2>
                        </div>
                        <span class="text-xs font-semibold text-slate-500 bg-white px-3 py-1.5 rounded-xl border border-slate-200">
                            Periode Tahun {{ $selectedYear ?? date('Y') }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                        
                        {{-- 1. Total Nilai Project --}}
                        <div class="ipnet-metric-card border-l-4 border-l-[#8F0A0D] flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Total Nilai Project</span>
                                    <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-[20px] xl:text-[22px] font-black text-[#1E293B] tracking-tight truncate" title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalProjectValue ?? 0) }}">
                                    {{ \App\Helpers\CurrencyHelper::formatCompact($totalProjectValue ?? 0) }}
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-[#64748B] mt-2.5 pt-2 border-t border-slate-100 font-medium">
                                <span>Seluruh Portofolio</span>
                                <span class="font-bold text-slate-800">{{ $totalProjectCount ?? 0 }} <span class="text-[10px] font-normal text-slate-400">items</span></span>
                            </div>
                        </div>

                        {{-- 2. Total Opportunity Pipeline --}}
                        <div class="ipnet-metric-card border-l-4 border-l-blue-500 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Total Peluang</span>
                                    <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.516 0c.85.493 1.508 1.333 1.508 2.316V18"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-[20px] xl:text-[22px] font-black text-[#1E293B] tracking-tight truncate" title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalOppValue ?? 0) }}">
                                    {{ \App\Helpers\CurrencyHelper::formatCompact($totalOppValue ?? 0) }}
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-[#64748B] mt-2.5 pt-2 border-t border-slate-100 font-medium">
                                <span>Pipeline Aktif</span>
                                <span class="font-bold text-blue-600">{{ $totalOppCount ?? 0 }} <span class="text-[10px] font-normal text-slate-400">prospek</span></span>
                            </div>
                        </div>

                        {{-- 3. Total In Progress --}}
                        <div class="ipnet-metric-card border-l-4 border-l-amber-500 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">In Progress</span>
                                    <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-[20px] xl:text-[22px] font-black text-[#1E293B] tracking-tight truncate" title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalInProgressValue ?? 0) }}">
                                    {{ \App\Helpers\CurrencyHelper::formatCompact($totalInProgressValue ?? 0) }}
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-[#64748B] mt-2.5 pt-2 border-t border-slate-100 font-medium">
                                <span>Sedang Berjalan</span>
                                <span class="font-bold text-amber-600">{{ $totalInProgressCount ?? 0 }} <span class="text-[10px] font-normal text-slate-400">deals</span></span>
                            </div>
                        </div>

                        {{-- 4. Total Closed Won --}}
                        <div class="ipnet-metric-card border-l-4 border-l-emerald-500 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Closed Won</span>
                                    <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-[20px] xl:text-[22px] font-black text-emerald-600 tracking-tight truncate" title="{{ \App\Helpers\CurrencyHelper::formatRupiah($totalCompleteValue ?? 0) }}">
                                    {{ \App\Helpers\CurrencyHelper::formatCompact($totalCompleteValue ?? 0) }}
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-[#64748B] mt-2.5 pt-2 border-t border-slate-100 font-medium">
                                <span>Kontrak Terkunci</span>
                                <span class="font-bold text-emerald-600">{{ $totalCompleteCount ?? 0 }} <span class="text-[10px] font-normal text-slate-400">deals</span></span>
                            </div>
                        </div>

                        {{-- 5. Win Rate / Rasio Closing --}}
                        <div class="ipnet-metric-card border-l-4 border-l-purple-500 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Win Rate</span>
                                    <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="text-[20px] xl:text-[22px] font-black text-purple-600 tracking-tight">
                                    {{ $winRate ?? 0 }}%
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-[#64748B] mt-2.5 pt-2 border-t border-slate-100 font-medium">
                                <span>Efektivitas Sales</span>
                                <span class="font-bold text-purple-600">Konversi Deals</span>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- PIPELINE STAGE FUNNEL BREAKDOWN --}}
                <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-2">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5 pb-4 border-b border-[#E2E8F0]">
                        <div>
                            <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                <span class="ipnet-badge-dot"></span> DISTRIBUSI TAHAPAN PROYEK
                            </p>
                            <h3 class="text-[18px] font-bold text-[#292929] tracking-tight">
                                Sales Pipeline & Stage Conversion
                            </h3>
                            <p class="text-[12.5px] text-[#75727C] mt-0.5">
                                Pantau sebaran nilai dan volume peluang di setiap tahapan siklus penjualan
                            </p>
                        </div>
                        <a href="{{ route('acquire.index') }}" class="text-xs font-bold text-[#8F0A0D] hover:underline inline-flex items-center gap-1">
                            <span>Buka Kanban Pipeline</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2.5">
                        @foreach(($stageFunnel ?? []) as $sKey => $sData)
                            <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/70 hover:bg-white hover:border-slate-300 transition flex flex-col justify-between space-y-2">
                                <div>
                                    <div class="w-2.5 h-2.5 rounded-full mb-1.5" style="background-color: {{ $sData['color'] ?? '#8F0A0D' }};"></div>
                                    <div class="text-[11px] font-bold text-slate-700 leading-snug line-clamp-2">{{ $sData['label'] }}</div>
                                </div>
                                <div>
                                    <div class="text-[14px] font-black text-slate-900">{{ $sData['count'] }} <span class="text-[10px] font-normal text-slate-400">deals</span></div>
                                    <div class="text-[10.5px] font-semibold text-slate-500 truncate" title="{{ \App\Helpers\CurrencyHelper::formatRupiah($sData['value'] ?? 0) }}">
                                        {{ \App\Helpers\CurrencyHelper::formatCompact($sData['value'] ?? 0) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- PRIORITY DEALS & RECENT SALES CRM ACTIVITIES --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 anim-fade-up anim-delay-3">
                    
                    {{-- Proyek Komersial Terkini --}}
                    <div class="lg:col-span-2 ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#E2E8F0]">
                                <div>
                                    <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                        <span class="ipnet-badge-dot"></span> PORTOFOLIO SALES
                                    </p>
                                    <h3 class="text-[16px] font-bold text-[#292929]">Proyek & Peluang Komersial Terkini</h3>
                                </div>
                                <a href="{{ route('projects.index') }}" class="text-xs font-bold text-[#8F0A0D] hover:underline">Lihat Semua &rarr;</a>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                            <th class="pb-2.5">Proyek & Klien</th>
                                            <th class="pb-2.5">PIC Sales</th>
                                            <th class="pb-2.5">Nilai Kontrak</th>
                                            <th class="pb-2.5">Status</th>
                                            <th class="pb-2.5 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @forelse(($priorityDeals ?? []) as $deal)
                                            <tr class="hover:bg-slate-50/80 transition">
                                                <td class="py-3 pr-2">
                                                    <div class="font-extrabold text-slate-900 text-[12.5px] line-clamp-1">{{ $deal->name }}</div>
                                                    <div class="text-[11px] text-slate-500 font-medium">{{ $deal->client ?: '-' }}</div>
                                                </td>
                                                <td class="py-3 px-2 font-semibold text-slate-700 whitespace-nowrap">
                                                    {{ $deal->sales_name ?: ($deal->creator ? $deal->creator->name : 'Sales Team') }}
                                                </td>
                                                <td class="py-3 px-2 font-black text-[#8F0A0D] whitespace-nowrap">
                                                    {{ $deal->contract_value ? \App\Helpers\CurrencyHelper::formatRupiah($deal->contract_value) : '-' }}
                                                </td>
                                                <td class="py-3 px-2 whitespace-nowrap">
                                                    @php
                                                        $st = $deal->status ?: 'Draft';
                                                        $isWon = in_array(strtolower($st), ['completed', 'finished', 'closed']);
                                                        $isProg = in_array(strtolower($st), ['on progress', 'in progress']);
                                                    @endphp
                                                    <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold {{ $isWon ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($isProg ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700 border border-slate-200') }}">
                                                        {{ $deal->sales_stage ?: $deal->status }}
                                                    </span>
                                                </td>
                                                <td class="py-3 pl-2 text-right whitespace-nowrap">
                                                    <a href="{{ route('projects.show', $deal->id) }}" class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition inline-block">
                                                        Detail &rarr;
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-6 text-center text-slate-400">Belum ada proyek komersial terdaftar.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Log Aktivitas Sales CRM Terbaru --}}
                    <div class="ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#E2E8F0]">
                                <div>
                                    <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                        <span class="ipnet-badge-dot"></span> SALES ACTIVITY
                                    </p>
                                    <h3 class="text-[16px] font-bold text-[#292929]">Aktivitas CRM Terbaru</h3>
                                </div>
                                <span class="text-[11px] text-slate-400 font-semibold">{{ count($recentActivities ?? []) }} log</span>
                            </div>

                            <div class="space-y-3">
                                @forelse(($recentActivities ?? []) as $act)
                                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                                        <div class="flex items-center justify-between gap-1 text-[11px]">
                                            <span class="font-bold text-[#8F0A0D]">{{ $act->activity_type ?? 'Meeting' }}</span>
                                            <span class="text-slate-400">{{ $act->activity_date ? \Carbon\Carbon::parse($act->activity_date)->format('d M Y') : '-' }}</span>
                                        </div>
                                        <div class="font-bold text-slate-800 text-[12px] line-clamp-1">{{ $act->subject ?: ($act->project ? $act->project->name : 'Client Follow-up') }}</div>
                                        <div class="text-[11px] text-slate-500 line-clamp-2">{{ $act->notes ?: '-' }}</div>
                                        <div class="text-[10px] font-semibold text-slate-400 pt-1">Oleh: {{ $act->sales ? $act->sales->name : 'Sales' }}</div>
                                    </div>
                                @empty
                                    <div class="py-8 text-center text-slate-400 text-xs">
                                        Belum ada catatan aktivitas sales CRM bulan ini.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>

            @else
                {{-- ======================================================== --}}
                {{-- KONTEN TEAM LEADER TEKNIKAL (NUGRAHA, IGNATIUS, DORIS, DLL) --}}
                {{-- ======================================================== --}}

                {{-- 2. METRIC SUMMARY CARDS --}}
                <div class="anim-fade-up anim-delay-1">
                    <div class="flex items-center justify-between mb-3.5">
                        <div>
                            <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                <span class="ipnet-badge-dot"></span> RINGKASAN OPERASIONAL
                            </p>
                            <h2 class="text-[18px] font-bold text-[#292929] tracking-tight">Status & Distribusi Penugasan</h2>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
                        {{-- Metric 1: Total Proyek --}}
                        <a href="{{ route('projects.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-1">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Total Proyek</span>
                                <div class="w-7 h-7 rounded-lg bg-[#8F0A0D]/10 text-[#8F0A0D] flex items-center justify-center group-hover:bg-[#8F0A0D] group-hover:text-white transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-[22px] font-extrabold text-[#292929] tracking-tight">{{ $projectsCount ?? 0 }}</div>
                            <p class="text-[11px] text-[#75727C] mt-0.5">Portofolio aktif</p>
                        </a>

                        {{-- Metric 2: Total Tugas --}}
                        <a href="{{ route('tasks.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-2">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Total Tugas</span>
                                <div class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-[22px] font-extrabold text-[#292929] tracking-tight">{{ $tasksCount ?? 0 }}</div>
                            <p class="text-[11px] text-[#75727C] mt-0.5">Seluruh kegiatan</p>
                        </a>

                        {{-- Metric 3: Ditugaskan --}}
                        <a href="{{ route('tasks.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Ditugaskan</span>
                                <div class="w-7 h-7 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <circle cx="12" cy="12" r="9"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-[22px] font-extrabold text-[#292929] tracking-tight">{{ $tasksAssigned ?? 0 }}</div>
                            <p class="text-[11px] text-[#75727C] mt-0.5">Menunggu respon</p>
                        </a>

                        {{-- Metric 4: Dalam Proses --}}
                        <a href="{{ route('tasks.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Dalam Proses</span>
                                <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-[22px] font-extrabold text-[#292929] tracking-tight">{{ $tasksInProgress ?? 0 }}</div>
                            <p class="text-[11px] text-amber-600 font-semibold mt-0.5">Sedang berjalan</p>
                        </a>

                        {{-- Metric 5: Selesai --}}
                        <a href="{{ route('tasks.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-5">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Selesai</span>
                                <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-[22px] font-extrabold text-emerald-600 tracking-tight">{{ $tasksCompleted ?? 0 }}</div>
                            <p class="text-[11px] text-[#75727C] mt-0.5">Tuntas diverifikasi</p>
                        </a>

                        {{-- Metric 6: Tenggat Waktu --}}
                        <a href="{{ route('tasks.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Tenggat Waktu</span>
                                <div class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-600 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-[20px] font-extrabold text-[#8F0A0D] tracking-tight truncate">
                                {{ ($upcomingDeadline ?? false) ? $upcomingDeadline->deadline->format('d M') : '-' }}
                            </div>
                            <p class="text-[11px] text-[#75727C] mt-0.5 truncate">
                                {{ ($overdueTasksCount ?? 0) > 0 ? $overdueTasksCount . ' tugas terlewat' : 'Jadwal terdekat' }}
                            </p>
                        </a>
                    </div>
                </div>

                {{-- 3. WORKLOAD LOAD CHART PERSONIL LAPANGAN --}}
                <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-3">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-5 pb-4 border-b border-[#E2E8F0]">
                        <div>
                            <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                <span class="ipnet-badge-dot"></span> MANAJEMEN PERSONIL
                            </p>
                            <h3 class="text-[18px] font-bold text-[#292929] tracking-tight">
                                Beban & Kapasitas Penugasan Personil
                            </h3>
                            <p class="text-[12.5px] text-[#75727C] mt-0.5">
                                Pantau perbandingan tugas aktif dan tugas selesai untuk keseimbangan distribusi tim
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2.5">
                            @if($canFilterTeams ?? false)
                            <div class="relative">
                                <select id="engTeamFilter" onchange="filterEngineerTeam(this.value)" 
                                        class="text-[12px] font-semibold px-3 py-2 rounded-xl border border-[#CBD5E1] bg-white text-[#1E293B] outline-none hover:border-[#8F0A0D] transition cursor-pointer shadow-xs">
                                    <option value="Maintenance" {{ ($defaultTeamFilter ?? '') === 'Maintenance' ? 'selected' : '' }}>Tim Maintenance & Helpdesk</option>
                                    <option value="All" {{ ($defaultTeamFilter ?? '') === 'All' ? 'selected' : '' }}>Semua Tim (Lintas Divisi)</option>
                                    <option value="Network" {{ ($defaultTeamFilter ?? '') === 'Network' ? 'selected' : '' }}>Divisi Network</option>
                                    <option value="Security" {{ ($defaultTeamFilter ?? '') === 'Security' ? 'selected' : '' }}>Divisi Security</option>
                                </select>
                            </div>
                            @endif

                            {{-- Period Switcher --}}
                            <div class="flex gap-1 bg-[#F1F5F9] p-1 rounded-xl border border-[#E2E8F0]">
                                <button onclick="setEngineerPeriod('week')" 
                                         class="text-[11.5px] font-bold px-3 py-1.5 rounded-lg transition cursor-pointer" 
                                         id="engPeriodWeek"
                                         style="background:transparent; color:#64748B;">
                                    Minggu Ini
                                </button>
                                <button onclick="setEngineerPeriod('month')" 
                                         class="text-[11.5px] font-bold px-3 py-1.5 rounded-lg transition cursor-pointer shadow-xs" 
                                         id="engPeriodMonth"
                                         style="background:linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%); color:#FFFFFF;">
                                    Bulan Ini
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="w-full overflow-x-auto">
                        <div style="height: 270px; min-width: 340px;">
                            <canvas id="engineerLoadChart"></canvas>
                        </div>
                    </div>

                    <div class="flex flex-wrap justify-center items-center gap-6 mt-4 pt-3 border-t border-[#F1F5F9] text-[12px] text-[#64748B]">
                        <span class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-sm" style="background: linear-gradient(135deg, #D31A2C 0%, #8C0B17 100%);"></span>
                            <span class="font-semibold text-[#1E293B]">Tugas & Kegiatan Aktif</span>
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-sm" style="background: linear-gradient(135deg, #34D399 0%, #059669 100%);"></span>
                            <span class="font-semibold text-[#1E293B]">Tugas Selesai</span>
                        </span>
                    </div>
                </div>

                {{-- 4. PROYEK BERJALAN & STATUS BREAKDOWN --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 anim-fade-up anim-delay-4">
                    <div class="lg:col-span-2 ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#E2E8F0]">
                                <div>
                                    <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                        <span class="ipnet-badge-dot"></span> PROGRES REALISASI
                                    </p>
                                    <h3 class="text-[16px] font-bold text-[#292929]">Progres Portofolio Proyek Utama</h3>
                                </div>
                                <a href="{{ route('projects.index') }}" class="text-xs font-bold text-[#8F0A0D] hover:underline">Lihat Semua &rarr;</a>
                            </div>
                            <div style="height: 230px;">
                                <canvas id="projectProgressChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#E2E8F0]">
                                <div>
                                    <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                        <span class="ipnet-badge-dot"></span> DISTRIBUSI TIKET
                                    </p>
                                    <h3 class="text-[16px] font-bold text-[#292929]">Status Beban Kerja</h3>
                                </div>
                            </div>
                            <div style="height: 230px;" class="flex items-center justify-center">
                                <canvas id="taskStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if(!($isExecutive ?? false))
    // Global data references untuk filter load chart engineer
    const engineerMonthData = @json($engineerLoadMonthData ?? []);
    const engineerWeekData  = @json($engineerLoadWeekData ?? []);
    let currentPeriod = 'month';
    let currentTeam   = '{{ $defaultTeamFilter ?? "All" }}';
    let engineerChartInstance = null;

    function getFilteredEngineerData() {
        const rawData = (currentPeriod === 'month') ? engineerMonthData : engineerWeekData;
        if (currentTeam === 'All') return rawData;
        return rawData.filter(item => item.division === currentTeam);
    }

    function filterEngineerTeam(team) {
        currentTeam = team;
        updateEngineerChart();
    }

    function updateEngineerChart() {
        if (!engineerChartInstance) return;
        const dataToUse = getFilteredEngineerData();
        
        engineerChartInstance.data.labels = dataToUse.map(d => d.name);
        engineerChartInstance.data.datasets[0].data = dataToUse.map(d => d.active);
        engineerChartInstance.data.datasets[1].data = dataToUse.map(d => d.completed);
        engineerChartInstance.update();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const ctxLoad = document.getElementById('engineerLoadChart');
        if (!ctxLoad) return;
        
        const activeBarGrad = ctxLoad.getContext('2d').createLinearGradient(0, 0, 300, 0);
        activeBarGrad.addColorStop(0, '#D31A2C');
        activeBarGrad.addColorStop(1, '#8C0B17');

        const completedBarGrad = ctxLoad.getContext('2d').createLinearGradient(0, 0, 300, 0);
        completedBarGrad.addColorStop(0, '#34D399');
        completedBarGrad.addColorStop(1, '#059669');

        const initialData = getFilteredEngineerData();

        engineerChartInstance = new Chart(ctxLoad.getContext('2d'), {
            type: 'bar',
            data: {
                labels: initialData.map(d => d.name),
                datasets: [
                    {
                        label: 'Tugas Aktif',
                        data: initialData.map(d => d.active),
                        backgroundColor: activeBarGrad,
                        borderRadius: 6,
                    },
                    {
                        label: 'Tugas Selesai',
                        data: initialData.map(d => d.completed),
                        backgroundColor: completedBarGrad,
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { stacked: true, ticks: { stepSize: 1 } },
                    y: { stacked: true }
                }
            }
        });

        // Project Progress Chart
        const ctxProj = document.getElementById('projectProgressChart');
        if (ctxProj) {
            const projectData = @json($projectProgressData ?? []);
            new Chart(ctxProj.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: projectData.map(d => d.name),
                    datasets: [{
                        label: 'Progress (%)',
                        data: projectData.map(d => d.progress),
                        backgroundColor: '#8F0A0D',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { min: 0, max: 100 } }
                }
            });
        }

        // Status Breakdown Doughnut Chart
        const ctxStatus = document.getElementById('taskStatusChart');
        if (ctxStatus) {
            const statusData = @json($statusData ?? []);
            new Chart(ctxStatus.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: statusData.map(d => d.name),
                    datasets: [{
                        data: statusData.map(d => d.value),
                        backgroundColor: ['#3B82F6', '#F59E0B', '#8B5CF6', '#10B981'],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%'
                }
            });
        }
    });

    function setEngineerPeriod(period) {
        currentPeriod = period;
        const isWeek = period === 'week';
        const weekBtn = document.getElementById('engPeriodWeek');
        const monthBtn = document.getElementById('engPeriodMonth');
        const activeGradient = 'linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%)';

        if (isWeek) {
            weekBtn.style.background = activeGradient;
            weekBtn.style.color = '#FFFFFF';
            monthBtn.style.background = 'transparent';
            monthBtn.style.color = '#64748B';
        } else {
            monthBtn.style.background = activeGradient;
            monthBtn.style.color = '#FFFFFF';
            weekBtn.style.background = 'transparent';
            weekBtn.style.color = '#64748B';
        }
        updateEngineerChart();
    }
    @endif
</script>
@endpush
@endsection