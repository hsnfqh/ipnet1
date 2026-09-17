@extends('layouts.app')

@section('title', 'Dashboard BusDev & Strategy - PT IP Network Solusindo')

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

    .ipnet-card:hover {
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
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background-color: #8F0A0D;
        display: inline-block;
        margin-right: 6px;
    }

    .btn-ipnet-primary {
        background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
        color: #FFFFFF;
        font-weight: 600;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(143, 10, 13, 0.15);
    }

    .btn-ipnet-primary:hover {
        background: linear-gradient(135deg, #7A080B 0%, #9E0E1D 100%);
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.28);
        transform: translateY(-1px);
    }

    /* Fluid Entrance Animations */
    @keyframes heroReveal {
        0% { opacity: 0; transform: translateY(22px) scale(0.985); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(18px); }
        100% { opacity: 1; transform: translateY(0); }
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
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- ======================================================== --}}
            {{-- 1. EXECUTIVE HERO BANNER                                 --}}
            {{-- ======================================================== --}}
            <div class="ipnet-hero-banner rounded-2xl px-5 py-4 sm:px-6 sm:py-4.5 text-white shadow-md shadow-red-950/15 relative anim-hero-reveal">
                {{-- Layered Geometric Faceted Red Planes --}}
                <div class="absolute inset-0 pointer-events-none overflow-hidden select-none">
                    <svg class="w-full h-full object-cover" viewBox="0 0 1440 200" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="bdmRedGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#C61828" />
                                <stop offset="100%" stop-color="#9E0E1D" />
                            </linearGradient>
                            <linearGradient id="bdmRedGrad2" x1="100%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#B01423" />
                                <stop offset="100%" stop-color="#7A0813" />
                            </linearGradient>
                            <linearGradient id="bdmRedGrad3" x1="0%" y1="100%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#940E1B" />
                                <stop offset="100%" stop-color="#5A040C" />
                            </linearGradient>
                            <linearGradient id="bdmRedGradHighlight" x1="0%" y1="0%" x2="100%" y2="50%">
                                <stop offset="0%" stop-color="#FFA8B2" stop-opacity="0.20" />
                                <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0" />
                            </linearGradient>
                            <filter id="bdmFacetDropShadow" x="-10%" y="-10%" width="130%" height="130%">
                                <feDropShadow dx="-8" dy="10" stdDeviation="14" flood-color="#3A0207" flood-opacity="0.4" />
                            </filter>
                        </defs>

                        <!-- Base Background -->
                        <rect width="1440" height="200" fill="url(#bdmRedGrad1)" />

                        <!-- Top-Left Large Diagonal Angled Plane -->
                        <polygon points="0,0 650,0 280,200 0,200" fill="url(#bdmRedGrad1)" />

                        <!-- Intersecting Broad Diagonal Facet Strip -->
                        <polygon points="220,0 850,0 1300,200 600,200" fill="url(#bdmRedGrad2)" filter="url(#bdmFacetDropShadow)" />

                        <!-- Crossing Foreground Diagonal Bright Red Plane -->
                        <polygon points="0,0 520,0 1080,200 480,200" fill="url(#bdmRedGrad1)" opacity="0.9" filter="url(#bdmFacetDropShadow)" />

                        <!-- Right Edge Deeper Contrast Facet -->
                        <polygon points="780,0 1440,0 1440,200 1100,200" fill="url(#bdmRedGrad3)" filter="url(#bdmFacetDropShadow)" />

                        <!-- Soft Angular Ambient Highlight Overlays -->
                        <polygon points="0,0 680,0 1120,200 380,200" fill="url(#bdmRedGradHighlight)" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center mb-1.5 px-2.5 py-0.5 text-[10px] font-semibold text-white bg-white/15 backdrop-blur-md rounded-full border border-white/20 tracking-wider uppercase">
                            <span class="w-1.5 h-1.5 mr-1.5 bg-white rounded-full inline-block"></span>
                            PT IP NETWORK SOLUSINDO &bull; BUSINESS DEVELOPMENT &amp; STRATEGY
                        </div>

                        <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight leading-tight">
                            Selamat Datang, {{ auth()->user()->name }}
                        </h1>
                        <p class="mt-0.5 text-xs text-white/80 leading-relaxed line-clamp-1">
                            Kelola inisiasi peluang pasar, serah terima prospek tender ke tim Sales, dan perluas ekosistem mitra teknologi.
                        </p>
                    </div>

                    {{-- Actions & Filter --}}
                    <div class="flex items-center gap-2.5 shrink-0">
                        <form method="GET" action="{{ route('dashboard.bdm') }}" id="yearFilterForm" class="flex items-center">
                            <div class="relative">
                                <select name="year" onchange="document.getElementById('yearFilterForm').submit()" 
                                        class="appearance-none bg-white/10 hover:bg-white/20 border border-white/25 text-white font-medium text-xs rounded-xl px-3.5 py-2 pr-7 focus:outline-none focus:ring-2 focus:ring-white/30 cursor-pointer backdrop-blur-sm transition-all">
                                    @foreach([2024, 2025, 2026, 2027] as $y)
                                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }} class="text-gray-900 bg-white">Tahun {{ $y }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white/80">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </form>

                        <a href="{{ route('bdm.opportunities.index') }}" class="px-3.5 py-2 rounded-xl bg-white text-[#8F0A0D] font-semibold text-xs hover:bg-[#FFF7F6] hover:scale-[1.02] transition-all shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Inisiasi Peluang</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 2. METRIC SUMMARY CARDS (5 KPI CARDS)                    --}}
            {{-- ======================================================== --}}
            <div class="anim-fade-up anim-delay-1">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-semibold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> RINGKASAN INISIASI &amp; STRATEGI
                        </p>
                        <h2 class="text-base font-bold text-gray-800 tracking-tight">Status Portofolio &amp; Ekosistem Kemitraan</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                    
                    {{-- Card 1: Total Nilai Pipeline --}}
                    <div class="ipnet-metric-card flex flex-col justify-between space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Total Pipeline</span>
                            <div class="w-7 h-7 rounded-lg bg-red-500/10 text-[#8F0A0D] flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-gray-800 tracking-tight">
                                Rp {{ $totalNilaiProject > 0 ? number_format($totalNilaiProject / 1000000, 1, ',', '.') . 'Jt' : '0,0Jt' }}
                            </div>
                            <p class="text-[11px] text-gray-400 mt-0.5">{{ $totalProjectCount }} Total Proyek Terdata</p>
                        </div>
                    </div>

                    {{-- Card 2: Peluang Baru (TOR/RFP) --}}
                    <div class="ipnet-metric-card flex flex-col justify-between space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Peluang Baru</span>
                            <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-amber-800 tracking-tight">
                                Rp {{ $totalNilaiOpportunity > 0 ? number_format($totalNilaiOpportunity / 1000000, 1, ',', '.') . 'Jt' : '0,0Jt' }}
                            </div>
                            <p class="text-[11px] text-amber-600 font-medium mt-0.5">{{ $totalOpportunityCount }} Prospek TOR/RFP</p>
                        </div>
                    </div>

                    {{-- Card 3: Diserahkan ke Sales --}}
                    <div class="ipnet-metric-card flex flex-col justify-between space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Handover Sales</span>
                            <div class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-blue-700 tracking-tight">
                                {{ $totalHandoverCount }} Peluang
                            </div>
                            <p class="text-[11px] text-blue-600 font-medium mt-0.5">Konversi {{ $conversionRate }}% ke Tim Sales</p>
                        </div>
                    </div>

                    {{-- Card 4: Market Intelligence --}}
                    <div class="ipnet-metric-card flex flex-col justify-between space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Market Intel</span>
                            <div class="w-7 h-7 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-purple-800 tracking-tight">
                                {{ $intelCount }} Laporan
                            </div>
                            <p class="text-[11px] text-purple-600 font-medium mt-0.5">Riset &amp; Tren Industri</p>
                        </div>
                    </div>

                    {{-- Card 5: Jaringan Mitra & Prinsipal --}}
                    <div class="ipnet-metric-card flex flex-col justify-between space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Mitra &amp; Prinsipal</span>
                            <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-xl font-bold text-emerald-800 tracking-tight">
                                {{ $partnerCount }} Vendor Aktif
                            </div>
                            <p class="text-[11px] text-emerald-600 font-medium mt-0.5">Channel Ekosistem Resmi</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 3. CHARTS GRID                                           --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 anim-fade-up anim-delay-2">
                
                {{-- Chart 1: Tren Nilai Pipeline Bulanan --}}
                <div class="lg:col-span-2 ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800 tracking-tight">Tren Nilai Peluang Pipeline ({{ $selectedYear }})</h3>
                            <p class="text-xs text-gray-400">Akumulasi estimasi nilai kontrak tender per bulan (Juta Rupiah)</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-0.5 bg-red-50 text-[#8F0A0D] border border-red-100 rounded-md">
                            Tahap Acquire
                        </span>
                    </div>
                    <div class="h-64 w-full">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>

                {{-- Chart 2: Distribusi Peluang per Sektor Klien --}}
                <div class="ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-sm font-bold text-gray-800 tracking-tight">Sektor Industri Klien</h3>
                            <p class="text-xs text-gray-400">Penyebaran portofolio target pasar</p>
                        </div>
                    </div>
                    <div class="h-64 w-full flex items-center justify-center">
                        <canvas id="sectorDoughnutChart"></canvas>
                    </div>
                </div>

            </div>

            {{-- ======================================================== --}}
            {{-- 4. BOTTOM SECTION: PELUANG TERBARU & WIDGETS             --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 anim-fade-up anim-delay-3">
                
                {{-- Left 2-Col: Daftar Peluang Tender Terbaru --}}
                <div class="lg:col-span-2 ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                            <h3 class="text-sm font-bold text-gray-800 tracking-tight">Peluang &amp; Tender Prospek Terkini</h3>
                        </div>
                        <a href="{{ route('bdm.opportunities.index') }}" class="text-xs font-semibold text-[#8F0A0D] hover:underline flex items-center gap-1">
                            <span>Buka Inisiasi Peluang</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50/80 text-gray-500 uppercase text-[10.5px] font-semibold tracking-wider">
                                <tr>
                                    <th class="py-3 px-4 rounded-l-lg">Peluang / Tender</th>
                                    <th class="py-3 px-4">Klien &amp; Sektor</th>
                                    <th class="py-3 px-4 text-right">Nilai Kontrak</th>
                                    <th class="py-3 px-4 text-center">Status Handover</th>
                                    <th class="py-3 px-4 rounded-r-lg text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-normal text-gray-600">
                                @forelse($recentOpportunities as $rp)
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3.5 px-4">
                                            <div class="font-semibold text-gray-800">{{ $rp->name }}</div>
                                            <div class="text-[11px] text-gray-400 mt-0.5">Target: {{ $rp->target_timeline_type ?: 'Q3 2026' }}</div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-medium text-gray-800">{{ $rp->client ?: 'Umum / Prospek' }}</div>
                                            <div class="text-[11px] text-gray-400 mt-0.5">{{ $rp->opportunity_source ?: 'Direct Inbound' }}</div>
                                        </td>
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap font-bold text-gray-800">
                                            Rp {{ number_format($rp->contract_value ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if($rp->bdm_handover_status === 'Handed Over to Sales')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10.5px] font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-md">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                                    Diserahkan: {{ $rp->sales_name ?: 'Sales' }}
                                                </span>
                                            @elseif($rp->bdm_handover_status === 'Accepted by Sales')
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10.5px] font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-md">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Diterima Sales
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 text-[10.5px] font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-md">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                                    Draft Inisiasi
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <a href="{{ route('projects.show', $rp->id) }}" 
                                               class="px-2.5 py-1.5 bg-white hover:bg-gray-50 text-gray-700 text-xs font-medium rounded-lg border border-gray-200 shadow-xs transition inline-flex items-center gap-1">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-400 text-xs font-medium">Belum ada peluang tender tercatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Right 1-Col: Market Intel & Direktori Mitra --}}
                <div class="space-y-6">
                    
                    {{-- Market Intelligence Terbaru --}}
                    <div class="ipnet-card p-5 space-y-3.5">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                                <h3 class="text-xs font-bold text-gray-800">Market Intelligence Terbaru</h3>
                            </div>
                            <a href="{{ route('bdm.intelligence.index') }}" class="text-xs font-semibold text-purple-600 hover:underline">Kelola &rarr;</a>
                        </div>

                        <div class="divide-y divide-gray-50">
                            @forelse($recentIntels as $intel)
                                <div class="py-2.5 space-y-1">
                                    <div class="font-semibold text-gray-800 text-xs truncate">{{ $intel->title }}</div>
                                    <div class="text-[11px] text-gray-400 flex items-center justify-between">
                                        <span>{{ $intel->industry_sector }}</span>
                                        <span class="font-medium text-purple-700 px-2 py-0.5 rounded-md bg-purple-50 text-[10px]">{{ $intel->impact_level }} Impact</span>
                                    </div>
                                </div>
                            @empty
                                <div class="py-4 text-center text-gray-400 text-xs font-medium">Belum ada data market intelligence.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Mitra Vendor & Prinsipal --}}
                    <div class="ipnet-card p-5 space-y-3.5">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                <h3 class="text-xs font-bold text-gray-800">Mitra Vendor &amp; Prinsipal</h3>
                            </div>
                            <a href="{{ route('bdm.partnerships.index') }}" class="text-xs font-semibold text-emerald-600 hover:underline">Kelola &rarr;</a>
                        </div>

                        <div class="divide-y divide-gray-50">
                            @forelse($recentPartners as $partner)
                                <div class="py-2 flex items-center justify-between text-xs">
                                    <div>
                                        <div class="font-semibold text-gray-800">{{ $partner->partner_name }}</div>
                                        <div class="text-[10.5px] text-gray-400">{{ $partner->partner_type }}</div>
                                    </div>
                                    <span class="text-[10.5px] font-medium text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                        {{ $partner->tier_level }}
                                    </span>
                                </div>
                            @empty
                                <div class="py-3 text-center text-gray-400 text-xs font-medium">Belum ada vendor terdaftar.</div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Chart Tren Nilai Pipeline Bulanan
        const monthlyData = @json($monthlyDataInMillions);
        const ctxTrend = document.getElementById('monthlyTrendChart').getContext('2d');
        
        const gradient = ctxTrend.createLinearGradient(0, 0, 0, 240);
        gradient.addColorStop(0, 'rgba(143, 10, 13, 0.28)');
        gradient.addColorStop(1, 'rgba(143, 10, 13, 0.00)');

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Nilai Pipeline (Juta Rp)',
                    data: monthlyData,
                    borderColor: '#8F0A0D',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#8F0A0D',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 3.5,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: 0.35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ' Rp ' + ctx.parsed.y.toLocaleString('id-ID') + ' Juta';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, family: "'Inter', sans-serif" }, color: '#9CA3AF' }
                    },
                    y: {
                        grid: { color: '#F3F4F6' },
                        ticks: {
                            font: { size: 10.5, family: "'Inter', sans-serif" },
                            color: '#9CA3AF',
                            callback: function(val) { return 'Rp ' + val + 'Jt'; }
                        }
                    }
                }
            }
        });

        // 2. Chart Distribusi Sektor Klien
        const sectorData = @json($sectorDataInMillions);
        const sectorLabels = @json(array_keys($sectorCounts));
        const ctxSector = document.getElementById('sectorDoughnutChart').getContext('2d');

        new Chart(ctxSector, {
            type: 'doughnut',
            data: {
                labels: sectorLabels,
                datasets: [{
                    data: sectorData,
                    backgroundColor: [
                        '#8F0A0D', // Primary IPNET Red
                        '#2563EB', // Blue
                        '#10B981', // Emerald
                        '#F59E0B'  // Amber
                    ],
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            font: { size: 10.5, family: "'Inter', sans-serif" },
                            color: '#4B5563',
                            padding: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.label + ': Rp ' + ctx.parsed.toLocaleString('id-ID') + ' Jt';
                            }
                        }
                    }
                },
                cutout: '68%'
            }
        });
    });
</script>
@endpush
