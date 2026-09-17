@extends('layouts.app')

@section('title', 'Dashboard Sales - PT IP Network Solusindo')

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
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #8F0A0D;
        display: inline-block;
        margin-right: 8px;
    }

    .btn-ipnet-primary {
        background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
        color: #FFFFFF;
        font-weight: 700;
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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="salesDashboard()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- ======================================================== --}}
            {{-- 1. EXECUTIVE HERO BANNER (EXACT IPNET PATTERN)            --}}
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
                            PT IP NETWORK SOLUSINDO &bull; COMMERCIAL &amp; SALES CRM
                        </div>

                        <h1 class="text-[18px] sm:text-[21px] font-extrabold text-white tracking-tight leading-tight">
                            Selamat Datang, {{ auth()->user()->name }}
                        </h1>
                        <p class="mt-0.5 text-[12.5px] text-white/80 leading-relaxed line-clamp-1">
                            Pantau performa pipeline penjualan, evaluasi peluang komersial, dan akselerasi deal enterprise.
                        </p>
                    </div>

                    {{-- Actions & Filter --}}
                    <div class="flex items-center gap-2.5 shrink-0">
                        <form method="GET" action="{{ route('dashboard.sales') }}" id="yearFilterForm" class="flex items-center">
                            <div class="relative">
                                <select name="year" onchange="document.getElementById('yearFilterForm').submit()" 
                                        class="appearance-none bg-white/10 hover:bg-white/20 border border-white/25 text-white font-bold text-[12px] rounded-xl px-3.5 py-2 pr-7 focus:outline-none focus:ring-2 focus:ring-white/30 cursor-pointer backdrop-blur-sm transition-all">
                                    @foreach([2024, 2025, 2026, 2027] as $y)
                                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }} class="text-gray-900 bg-white">Tahun {{ $y }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white/80">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </form>

                        <a href="{{ route('sales.pipeline.index') }}" class="px-3.5 py-2 rounded-xl bg-white text-[#8F0A0D] font-bold text-[12.5px] hover:bg-[#FFF7F6] hover:scale-[1.02] transition-all shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Daftar Peluang</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 2. METRIC SUMMARY CARDS (5 KPI CARDS)                    --}}
            {{-- ======================================================== --}}
            <div class="anim-fade-up anim-delay-1">
                <div class="flex items-center justify-between mb-3.5">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> RINGKASAN KOMERSIAL
                        </p>
                        <h2 class="text-[18px] font-bold text-[#292929] tracking-tight">Status &amp; Distribusi Pipeline Penjualan</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
                    
                    {{-- Card 1: Total Nilai Pipeline --}}
                    <div class="ipnet-metric-card flex flex-col justify-between space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Total Pipeline</span>
                            <div class="w-7 h-7 rounded-lg bg-red-500/10 text-[#8F0A0D] flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-[20px] font-extrabold text-[#292929] tracking-tight">
                                Rp {{ $totalPipelineValue > 0 ? number_format($totalPipelineValue / 1000000, 1, ',', '.') . 'Jt' : '0,0Jt' }}
                            </div>
                            <p class="text-[11px] text-[#75727C] mt-0.5">{{ $totalPipelineCount }} Peluang Aktif</p>
                        </div>
                    </div>

                    {{-- Card 2: Weighted Forecast --}}
                    <div class="ipnet-metric-card flex flex-col justify-between space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Weighted Forecast</span>
                            <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-[20px] font-extrabold text-amber-900 tracking-tight">
                                Rp {{ $totalWeightedForecast > 0 ? number_format($totalWeightedForecast / 1000000, 1, ',', '.') . 'Jt' : '0,0Jt' }}
                            </div>
                            <p class="text-[11px] text-amber-700 font-semibold mt-0.5">Bobot Probabilitas</p>
                        </div>
                    </div>

                    {{-- Card 3: Closing Horizon --}}
                    <div class="ipnet-metric-card flex flex-col justify-between space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Closing Horizon</span>
                            <div class="w-7 h-7 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-[20px] font-extrabold text-[#292929] tracking-tight">
                                Rp {{ $totalNegotiationValue > 0 ? number_format($totalNegotiationValue / 1000000, 1, ',', '.') . 'Jt' : '0,0Jt' }}
                            </div>
                            <p class="text-[11px] text-purple-700 font-semibold mt-0.5">{{ $totalNegotiationCount }} Negosiasi / SPK</p>
                        </div>
                    </div>

                    {{-- Card 4: Won Deals YTD --}}
                    <div class="ipnet-metric-card flex flex-col justify-between space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Won Deals (YTD)</span>
                            <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-[20px] font-extrabold text-emerald-700 tracking-tight">
                                Rp {{ $totalWonValue > 0 ? number_format($totalWonValue / 1000000, 1, ',', '.') . 'Jt' : '0,0Jt' }}
                            </div>
                            <p class="text-[11px] text-emerald-700 font-semibold mt-0.5">{{ $totalWonCount }} Kontrak Won</p>
                        </div>
                    </div>

                    {{-- Card 5: Win Rate --}}
                    <div class="ipnet-metric-card flex flex-col justify-between space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Win Rate &amp; CRM</span>
                            <div class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="text-[20px] font-extrabold text-[#292929] tracking-tight">
                                {{ $winRate }}%
                            </div>
                            <p class="text-[11px] text-gray-500 mt-0.5">{{ $crmActivitiesCount }} Log Bulan Ini</p>
                        </div>
                    </div>

                </div>
            </div>


            {{-- ======================================================== --}}
            {{-- 4. CHARTS SECTION                                        --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 anim-fade-up anim-delay-2">
                
                {{-- Chart 1: Tren Nilai Penjualan Bulanan (Left 2-Col) --}}
                <div class="lg:col-span-2 ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-gray-900">Tren Penjualan &amp; Forecast Bulanan ({{ $selectedYear }})</h3>
                            <p class="text-[11.5px] text-gray-500">Perbandingan nilai estimasi berbobot probabilitas vs deal Closed Won (Juta Rp)</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-1 bg-red-50 text-[#8F0A0D] rounded-lg border border-red-100">
                            Commercial Data
                        </span>
                    </div>
                    <div class="h-64 w-full">
                        <canvas id="monthlyTrendChart"></canvas>
                    </div>
                </div>

                {{-- Chart 2: Komposisi Nilai Deals (Right 1-Col) --}}
                <div class="ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-sm sm:text-base font-bold text-gray-900">Komposisi Pipeline Nilai Deals</h3>
                            <p class="text-[11.5px] text-gray-500">Porsi nominal proyek per tahapan</p>
                        </div>
                    </div>
                    <div class="h-64 w-full flex items-center justify-center">
                        <canvas id="stageDoughnutChart"></canvas>
                    </div>
                </div>

            </div>

            {{-- ======================================================== --}}
            {{-- 5. PELUANG PRIORITAS & AKTIVITAS CRM / HANDOVER          --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 anim-fade-up anim-delay-3">
                
                {{-- Left 2-Col: Daftar Peluang Prioritas (High-Value Deals) --}}
                <div class="lg:col-span-2 ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></div>
                            <h3 class="text-sm sm:text-base font-bold text-gray-900">Peluang &amp; Pipeline Prioritas</h3>
                        </div>
                        <a href="{{ route('sales.pipeline.index') }}" class="text-xs font-bold text-[#8F0A0D] hover:underline flex items-center gap-1">
                            <span>Buka Menu Peluang</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-[10.5px] font-bold">
                                <tr>
                                    <th class="py-3 px-3.5 rounded-l-lg">Peluang / Prospek</th>
                                    @if($isManagerial)
                                        <th class="py-3 px-3.5">Sales PIC</th>
                                    @else
                                        <th class="py-3 px-3.5">BDM Origin</th>
                                    @endif
                                    <th class="py-3 px-3.5 text-right">Nilai Kontrak</th>
                                    <th class="py-3 px-3.5 text-center">Tahap / Prob</th>
                                    <th class="py-3 px-3.5 rounded-r-lg text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                                @forelse($priorityDeals as $deal)
                                    @php
                                        $stageMeta = $stages[$deal->sales_stage] ?? ['color' => '#6B7280', 'bg' => '#F3F4F6'];
                                    @endphp
                                    <tr class="hover:bg-gray-50/80 transition-colors">
                                        <td class="py-3.5 px-3.5">
                                            <div class="font-bold text-gray-900">{{ $deal->name }}</div>
                                            <div class="text-[11px] text-gray-400 font-medium">{{ $deal->client }}</div>
                                        </td>
                                        @if($isManagerial)
                                            <td class="py-3.5 px-3.5 whitespace-nowrap">
                                                <span class="font-semibold text-gray-800">{{ $deal->sales_name ?: 'Belum Assign' }}</span>
                                            </td>
                                        @else
                                            <td class="py-3.5 px-3.5 whitespace-nowrap">
                                                <span class="font-semibold text-gray-800">{{ $deal->bdm->name ?? ($deal->creator->name ?? 'BDM') }}</span>
                                            </td>
                                        @endif
                                        <td class="py-3.5 px-3.5 text-right whitespace-nowrap font-bold text-gray-900">
                                            {{ $deal->contract_value > 0 ? 'Rp ' . number_format($deal->contract_value, 0, ',', '.') : '—' }}
                                        </td>
                                        <td class="py-3.5 px-3.5 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[10.5px] font-bold rounded-lg" 
                                                  style="background-color: {{ $stageMeta['bg'] }}; color: {{ $stageMeta['color'] }};">
                                                <span>{{ $deal->sales_stage }}</span>
                                                <span class="text-[10px] opacity-80">({{ $deal->win_probability ?? 10 }}%)</span>
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3.5 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <button type="button" 
                                                        @click="openStageModal({{ json_encode($deal) }})"
                                                        class="px-2.5 py-1 bg-white hover:bg-red-50 text-[#8F0A0D] text-[11px] font-bold rounded-lg border border-red-200 shadow-xs hover:border-[#8F0A0D] transition cursor-pointer">
                                                    Update
                                                </button>
                                                <a href="{{ route('projects.show', $deal->id) }}" 
                                                   class="px-2.5 py-1 bg-white hover:bg-gray-50 text-gray-700 text-[11px] font-semibold rounded-lg border border-gray-200 shadow-xs transition cursor-pointer">
                                                    Detail
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-gray-400 text-xs">Belum ada peluang aktif dalam pipeline.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Right 1-Col: Aktivitas CRM & Commercial Handover --}}
                <div class="space-y-5">
                    
                    {{-- Log Aktivitas CRM Terbaru --}}
                    <div class="ipnet-card p-5 space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                                <h3 class="text-sm font-bold text-gray-900">Aktivitas CRM Terkini</h3>
                            </div>
                            <a href="{{ route('sales.activities.index') }}" class="text-[11px] font-bold text-purple-600 hover:underline">Kelola</a>
                        </div>

                        <div class="divide-y divide-gray-50">
                            @forelse($recentActivities as $act)
                                <div class="py-2.5 space-y-1">
                                    <div class="flex items-center justify-between text-xs">
                                        <div class="font-bold text-gray-900 truncate max-w-[180px]">{{ $act->subject }}</div>
                                        <span class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($act->activity_date)->format('d M') }}</span>
                                    </div>
                                    <div class="text-[11px] text-gray-400 flex items-center justify-between">
                                        <span class="truncate">{{ $act->project->name ?? 'Proyek' }}</span>
                                        <span class="font-semibold text-gray-700">{{ $act->sales->name ?? 'Sales' }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="py-4 text-center text-gray-400 text-xs">Belum ada aktivitas CRM.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Commercial Handover Siap Kirim --}}
                    <div class="ipnet-card p-5 space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                <h3 class="text-sm font-bold text-gray-900">Serah Terima Proyek</h3>
                            </div>
                            <a href="{{ route('sales.handover.index') }}" class="text-[11px] font-bold text-emerald-600 hover:underline">Kelola</a>
                        </div>

                        <div class="divide-y divide-gray-50">
                            @php
                                $wonHandoverDeals = $priorityDeals->whereIn('sales_stage', ['Contract / PO / SPK', 'Closed Won', 'Approval'])->take(3);
                            @endphp
                            @forelse($wonHandoverDeals as $wDeal)
                                <div class="py-2.5 flex items-center justify-between text-xs">
                                    <div class="min-w-0 pr-2">
                                        <div class="font-bold text-gray-900 truncate">{{ $wDeal->name }}</div>
                                        <div class="text-[10.5px] text-gray-400">{{ $wDeal->client }}</div>
                                    </div>
                                    <a href="{{ route('sales.handover.index') }}" class="text-[10.5px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-100 flex-shrink-0 hover:bg-emerald-100 transition">
                                        Serah Terima
                                    </a>
                                </div>
                            @empty
                                <div class="py-4 text-center text-gray-400 text-xs">Belum ada deal siap handover.</div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    {{-- MODAL QUICK UPDATE STAGE & FORECAST --}}
    <template x-teleport="body">
        <div x-show="isStageModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5">
            <div class="bg-white rounded-2xl w-[600px] sm:w-[680px] max-w-full max-h-[85vh] flex flex-col overflow-hidden shadow-2xl border border-gray-100 my-auto anim-fade-up"
                 @click.away="isStageModalOpen = false">
                
                {{-- Header Modal --}}
                <div class="px-6 py-4.5 border-b border-gray-100 flex-shrink-0 bg-white flex items-center justify-between">
                    <div>
                        <span class="text-[10.5px] font-bold uppercase tracking-wider text-[#8F0A0D]">Sales CRM Lifecycle</span>
                        <h3 class="text-base font-bold text-gray-900" x-text="'Update Stage: ' + (selectedDeal.name || '')"></h3>
                    </div>
                    <button type="button" @click="isStageModalOpen = false" class="text-gray-400 hover:text-gray-700 p-1.5 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Form Body --}}
                <form :action="'/sales/pipeline/' + selectedDeal.id + '/stage'" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    
                    <div class="px-6 py-5 overflow-y-auto flex-1 space-y-4 text-xs">
                        
                        {{-- Ringkasan Peluang --}}
                        <div class="p-3.5 bg-[#F8FAFC] rounded-xl border border-gray-200 space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Klien / Instansi:</span>
                                <span class="font-bold text-gray-900" x-text="selectedDeal.client || '—'"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Sales Commercial PIC:</span>
                                <span class="font-bold text-[#8F0A0D]" x-text="selectedDeal.sales_name || 'Belum Ditugaskan'"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-gray-800 mb-1">Tahapan Siklus Penjualan (Sales Stage) *</label>
                                <select name="sales_stage" x-model="formStage" @change="syncProbability()" required
                                        class="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                                    <option value="Qualification">Qualification</option>
                                    <option value="Qualified Opportunity">Qualified Opportunity</option>
                                    <option value="Proposal Request">Proposal Request</option>
                                    <option value="Quotation">Quotation Submitted</option>
                                    <option value="Negotiation">Negotiation</option>
                                    <option value="Approval">Internal Approval</option>
                                    <option value="Contract / PO / SPK">Contract / PO / SPK</option>
                                    <option value="Closed Won">Closed Won (Handover)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block font-bold text-gray-800 mb-1">Win Probability (%) *</label>
                                <input type="number" name="win_probability" x-model="formProb" min="0" max="100" required
                                       class="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block font-bold text-gray-800 mb-1">Nilai Kontrak Estimasi (Rp)</label>
                                <input type="number" name="contract_value" x-model="formContractValue" min="0" step="1000"
                                       class="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                            </div>

                            <div>
                                <label class="block font-bold text-gray-800 mb-1">Expected Closing Date</label>
                                <input type="date" name="expected_closing_date" x-model="formClosingDate"
                                       class="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-gray-800 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]">
                            </div>
                        </div>

                        {{-- Quotation Details --}}
                        <div class="p-4 rounded-xl border border-gray-200 bg-[#F8FAFC] space-y-3">
                            <span class="text-[11px] font-bold text-gray-900 uppercase tracking-wider block">Dokumen Penawaran Harga (Quotation)</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-medium text-gray-500 mb-1">Nomor Quotation</label>
                                    <input type="text" name="quotation_number" x-model="formQuotationNum" placeholder="Contoh: QUO-IPNET/2026/09/001"
                                           class="w-full bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs text-gray-800">
                                </div>
                                <div>
                                    <label class="block font-medium text-gray-500 mb-1">Upload File Quotation Resmi</label>
                                    <input type="file" name="quotation_file" accept=".pdf,.docx,.xlsx,.zip"
                                           class="w-full bg-white border border-gray-200 rounded-xl px-2.5 py-1.5 text-xs text-gray-500">
                                </div>
                            </div>
                        </div>

                        {{-- Lost Reason if Closed Lost --}}
                        <div x-show="formStage === 'Closed Lost'" class="p-4 rounded-xl border border-rose-200 bg-rose-50 space-y-3">
                            <span class="text-[11px] font-bold text-rose-800 uppercase tracking-wider block">Alasan Deal Drop / Kalah Tender</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-medium text-rose-900 mb-1">Kompetitor Pemenang</label>
                                    <input type="text" name="lost_competitor" placeholder="Contoh: PT Integrator Lain"
                                           class="w-full bg-white border border-rose-300 rounded-xl px-3 py-2 text-xs">
                                </div>
                                <div>
                                    <label class="block font-medium text-rose-900 mb-1">Alasan Utama</label>
                                    <input type="text" name="lost_reason" placeholder="Contoh: Harga lebih tinggi, Spek tidak masuk"
                                           class="w-full bg-white border border-rose-300 rounded-xl px-3 py-2 text-xs">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block font-bold text-gray-800 mb-1">Catatan Progres Negosiasi / Follow-up</label>
                            <textarea name="sales_notes" rows="2" placeholder="Tuliskan ringkasan perkembangan negosiasi dengan klien..."
                                      class="w-full bg-[#F8FAFC] border border-gray-200 rounded-xl p-3 text-xs text-gray-800 focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D]"></textarea>
                        </div>

                    </div>

                    {{-- Footer Modal --}}
                    <div class="px-6 py-4 border-t border-gray-100 bg-[#F8FAFC] flex-shrink-0 flex items-center justify-end gap-2.5">
                        <button type="button" @click="isStageModalOpen = false" 
                                class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-white rounded-xl border border-gray-200 transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2 text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl shadow-md transition">
                            Simpan Perubahan Stage
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </template>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function salesDashboard() {
        return {
            isStageModalOpen: false,
            selectedDeal: {},
            formStage: 'Qualification',
            formProb: 10,
            formContractValue: 0,
            formClosingDate: '',
            formQuotationNum: '',

            openStageModal(deal) {
                this.selectedDeal = deal;
                this.formStage = deal.sales_stage || 'Qualification';
                this.formProb = deal.win_probability !== null ? deal.win_probability : 10;
                this.formContractValue = deal.contract_value || 0;
                this.formClosingDate = deal.expected_closing_date ? deal.expected_closing_date.split('T')[0] : '';
                this.formQuotationNum = deal.quotation_number || '';
                this.isStageModalOpen = true;
            },

            syncProbability() {
                const stageProbs = {
                    'Qualification': 10,
                    'Qualified Opportunity': 25,
                    'Proposal Request': 50,
                    'Quotation': 70,
                    'Negotiation': 85,
                    'Approval': 95,
                    'Contract / PO / SPK': 98,
                    'Closed Won': 100,
                    'Closed Lost': 0
                };
                if (stageProbs[this.formStage] !== undefined) {
                    this.formProb = stageProbs[this.formStage];
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // 1. Chart Tren Nilai Penjualan & Forecast Bulanan
        const forecastData = @json($monthlyForecastChart);
        const actualData = @json($monthlyActualChart);
        const ctxTrend = document.getElementById('monthlyTrendChart').getContext('2d');

        new Chart(ctxTrend, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [
                    {
                        type: 'bar',
                        label: 'Weighted Forecast (Juta Rp)',
                        data: forecastData,
                        backgroundColor: '#F59E0B',
                        borderRadius: 6,
                        maxBarThickness: 16,
                    },
                    {
                        type: 'bar',
                        label: 'Closed Won (Juta Rp)',
                        data: actualData,
                        backgroundColor: '#8F0A0D',
                        borderRadius: 6,
                        maxBarThickness: 16,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 10,
                            font: { size: 10.5, family: "'Inter', sans-serif" },
                            color: '#4B5563',
                            padding: 10
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID') + ' Jt';
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

        // 2. Chart Komposisi Nilai Deals
        const stageData = [
            {{ ($stageFunnel['Qualification']['value'] ?? 0) / 1000000 }},
            {{ ($stageFunnel['Proposal Request']['value'] ?? 0) / 1000000 }},
            {{ ($stageFunnel['Quotation']['value'] ?? 0) / 1000000 }},
            {{ ($stageFunnel['Negotiation']['value'] ?? 0) / 1000000 }},
            {{ ($stageFunnel['Contract / PO / SPK']['value'] ?? 0) / 1000000 }},
            {{ ($stageFunnel['Closed Won']['value'] ?? 0) / 1000000 }}
        ];
        const totalVal = stageData.reduce((a, b) => a + b, 0);

        const ctxDoughnut = document.getElementById('stageDoughnutChart').getContext('2d');
        new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Qualification', 'Proposal', 'Quotation', 'Negotiation', 'Contract/PO', 'Won'],
                datasets: [{
                    data: totalVal > 0 ? stageData : [1, 0, 0, 0, 0, 0],
                    backgroundColor: totalVal > 0 ? ['#3B82F6', '#8B5CF6', '#EAB308', '#F97316', '#10B981', '#8F0A0D'] : ['#E5E7EB', '#E5E7EB', '#E5E7EB', '#E5E7EB', '#E5E7EB', '#E5E7EB'],
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
                            padding: 10
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                if (totalVal === 0) return ' Belum ada data';
                                return ' ' + ctx.label + ': Rp ' + ctx.parsed.toFixed(1) + ' Jt';
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
