@extends('layouts.app')

@section('title', 'Dashboard Solution Architect - PT IP Network Solusindo')

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
        background-color: #8F0A0D;
        color: #FFFFFF;
        transition: all 0.2s ease;
    }

    .btn-ipnet-primary:hover {
        background-color: #73080A;
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.3);
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
    .anim-delay-4 { animation-delay: 0.26s !important; }
    .anim-delay-5 { animation-delay: 0.32s !important; }
    .anim-delay-6 { animation-delay: 0.38s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="architectDashboard()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">
            
            {{-- ======================================================== --}}
            {{-- 1. EXECUTIVE HERO BANNER (IPNET OFFICIAL BRAND PATTERN)  --}}
            {{-- ======================================================== --}}
            <div class="ipnet-hero-banner rounded-2xl px-5 py-4 sm:px-6 sm:py-4.5 text-white shadow-md shadow-red-950/15 relative anim-hero-reveal">
                {{-- Layered Geometric Faceted Red Planes --}}
                <div class="absolute inset-0 pointer-events-none overflow-hidden select-none">
                    <svg class="w-full h-full object-cover" viewBox="0 0 1440 200" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="saGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#C61828" />
                                <stop offset="100%" stop-color="#9E0E1D" />
                            </linearGradient>
                            <linearGradient id="saGrad2" x1="100%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#B01423" />
                                <stop offset="100%" stop-color="#7A0813" />
                            </linearGradient>
                            <linearGradient id="saGrad3" x1="0%" y1="100%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#940E1B" />
                                <stop offset="100%" stop-color="#5A040C" />
                            </linearGradient>
                            <linearGradient id="saGradHighlight" x1="0%" y1="0%" x2="100%" y2="50%">
                                <stop offset="0%" stop-color="#FFA8B2" stop-opacity="0.20" />
                                <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0" />
                            </linearGradient>
                            <filter id="saDropShadow" x="-10%" y="-10%" width="130%" height="130%">
                                <feDropShadow dx="-8" dy="10" stdDeviation="14" flood-color="#3A0207" flood-opacity="0.4" />
                            </filter>
                        </defs>

                        <!-- Base Background -->
                        <rect width="1440" height="200" fill="url(#saGrad1)" />

                        <!-- Top-Left Large Diagonal Angled Plane -->
                        <polygon points="0,0 650,0 280,200 0,200" fill="url(#saGrad1)" />

                        <!-- Intersecting Broad Diagonal Facet Strip -->
                        <polygon points="220,0 850,0 1300,200 600,200" fill="url(#saGrad2)" filter="url(#saDropShadow)" />

                        <!-- Crossing Foreground Diagonal Bright Red Plane -->
                        <polygon points="0,0 520,0 1080,200 480,200" fill="url(#saGrad1)" opacity="0.9" filter="url(#saDropShadow)" />

                        <!-- Right Edge Deeper Contrast Facet -->
                        <polygon points="780,0 1440,0 1440,200 1100,200" fill="url(#saGrad3)" filter="url(#saDropShadow)" />

                        <!-- Soft Angular Ambient Highlight Overlays -->
                        <polygon points="0,0 680,0 1120,200 380,200" fill="url(#saGradHighlight)" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center mb-1.5 px-2.5 py-0.5 text-[10.5px] font-bold text-white bg-white/15 backdrop-blur-md rounded-full border border-white/20 tracking-wider uppercase">
                            <span class="w-1.5 h-1.5 mr-1.5 bg-white rounded-full inline-block"></span>
                            PT IP NETWORK SOLUSINDO &bull; SOLUTION ARCHITECT
                        </div>

                        <h1 class="text-[18px] sm:text-[21px] font-extrabold text-white tracking-tight leading-tight">
                            Selamat Datang, {{ auth()->user()->name }}
                        </h1>
                        <p class="mt-0.5 text-[12.5px] text-white/80 leading-relaxed line-clamp-1">
                            Pantau perancangan arsitektur sistem, validasi BoQ/SOW, dan agenda pengujian konsep lab PoC.
                        </p>
                    </div>

                    {{-- Quick Action Buttons & Filter Tahun --}}
                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <form method="GET" action="{{ route('dashboard.architect') }}" id="yearFilterForm" class="shrink-0">
                            <div class="relative">
                                <select name="year" onchange="document.getElementById('yearFilterForm').submit()" 
                                        class="appearance-none bg-white/15 hover:bg-white/25 border border-white/30 text-white font-bold text-[12px] rounded-xl px-3.5 py-2 pr-7 shadow-xs outline-none backdrop-blur-md cursor-pointer transition-all">
                                    @foreach([2024, 2025, 2026, 2027] as $y)
                                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }} class="text-[#1E293B] bg-white">Tahun {{ $y }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white/80">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </div>
                        </form>

                        <a href="{{ route('presales.proposals.index') }}" class="px-3.5 py-2 rounded-xl bg-white text-[#8F0A0D] font-bold text-[12.5px] hover:bg-[#FFF7F6] hover:scale-[1.02] transition-all shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Kelola Desain &amp; SOW</span>
                        </a>

                        <a href="{{ route('schedules.index') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 hover:scale-[1.02] border border-white/25 text-white font-bold text-[12.5px] transition-all flex items-center gap-1.5 backdrop-blur-sm">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Jadwal Kerja</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 2. METRIC SUMMARY CARDS (6 CARDS GRID - IPNET STYLE)     --}}
            {{-- ======================================================== --}}
            <div class="anim-fade-up anim-delay-1">
                <div class="flex items-center justify-between mb-3.5">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> RINGKASAN ARSITEKTUR &amp; SOW
                        </p>
                        <h2 class="text-[18px] font-bold text-[#292929] tracking-tight">Status &amp; Distribusi Desain Solusi</h2>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
                    
                    {{-- Metric 1: Total Proyek --}}
                    <a href="{{ route('presales.proposals.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Total Proyek</span>
                            <div class="w-7 h-7 rounded-lg bg-[#8F0A0D]/10 text-[#8F0A0D] flex items-center justify-center group-hover:bg-[#8F0A0D] group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-[#292929] tracking-tight">{{ $totalProjectsCount }}</div>
                        <p class="text-[11px] text-[#75727C] mt-0.5">Portofolio peluang</p>
                    </a>

                    {{-- Metric 2: Perlu BoQ & SOW --}}
                    <a href="{{ route('presales.proposals.index', ['tab' => 'pending']) }}" class="ipnet-metric-card group block anim-fade-up anim-delay-2">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Perlu BoQ &amp; SOW</span>
                            <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-amber-600 tracking-tight">{{ $designPendingCount }}</div>
                        <p class="text-[11px] text-amber-600 font-semibold mt-0.5">Antrean perancangan</p>
                    </a>

                    {{-- Metric 3: Desain SOW Siap --}}
                    <a href="{{ route('presales.proposals.index', ['tab' => 'submitted']) }}" class="ipnet-metric-card group block anim-fade-up anim-delay-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">SOW Siap</span>
                            <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-emerald-600 tracking-tight">{{ $proposalsReadyCount }}</div>
                        <p class="text-[11px] text-[#75727C] mt-0.5">Dokumen siap tender</p>
                    </a>

                    {{-- Metric 4: Proyek Berjalan --}}
                    <a href="{{ route('presales.proposals.index', ['tab' => 'won']) }}" class="ipnet-metric-card group block anim-fade-up anim-delay-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Implementasi</span>
                            <div class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-blue-600 tracking-tight">{{ $activeProjectsCount }}</div>
                        <p class="text-[11px] text-[#75727C] mt-0.5">Desain diimplementasi</p>
                    </a>

                    {{-- Metric 5: Agenda PoC & Lab --}}
                    <a href="{{ route('schedules.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Sesi PoC &amp; Lab</span>
                            <div class="w-7 h-7 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-purple-600 tracking-tight">{{ $pocSchedules->count() }}</div>
                        <p class="text-[11px] text-[#75727C] mt-0.5">Uji coba konsep lab</p>
                    </a>

                    {{-- Metric 6: Total Nilai Pipeline --}}
                    <div class="ipnet-metric-card group block anim-fade-up anim-delay-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Nilai Solusi</span>
                            <div class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-600 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[20px] font-extrabold text-[#8F0A0D] tracking-tight truncate">
                            Rp {{ number_format($totalPipelineValue / 1000000000, 1, ',', '.') }} M
                        </div>
                        <p class="text-[11px] text-[#75727C] mt-0.5 truncate">Total estimasi proyek</p>
                    </div>

                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 3. CHARTS: TREN VOLUME SOLUSI & DOMAIN PORTOFOLIO        --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 anim-fade-up anim-delay-3">
                
                {{-- Chart 1: Tren Nilai Solusi Bulanan --}}
                <div class="lg:col-span-2 ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-wrap justify-between items-center gap-2 mb-4 pb-3 border-b border-[#E2E8F0]">
                            <div>
                                <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                    <span class="ipnet-badge-dot"></span> TREN VOLUME DESAIN
                                </p>
                                <h3 class="text-[17px] font-bold text-[#1E293B] tracking-tight">Tren Perancangan Solusi Arsitektur ({{ $selectedYear }})</h3>
                            </div>
                            <span class="text-[11.5px] font-medium text-[#64748B]">Akumulasi nilai solusi teknis (Juta Rp)</span>
                        </div>
                        <div class="w-full overflow-x-auto">
                            <div style="height: 230px; min-width: 280px;">
                                <canvas id="architectMonthlyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Chart 2: Komposisi Domain Teknologi --}}
                <div class="ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="mb-4 pb-3 border-b border-[#E2E8F0]">
                            <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                <span class="ipnet-badge-dot"></span> PORTOFOLIO TEKNOLOGI
                            </p>
                            <h3 class="text-[17px] font-bold text-[#1E293B] tracking-tight">Domain Solusi Jaringan</h3>
                        </div>
                        <div style="height: 190px;" class="flex items-center justify-center">
                            <canvas id="domainDoughnutChart"></canvas>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2.5 justify-center mt-3 pt-3 border-t border-[#F1F5F9]">
                        <span class="text-[11.5px] font-medium flex items-center gap-1.5 text-[#64748B]">
                            <span class="w-2 h-2 rounded-xs bg-[#8F0A0D]"></span> Campus Network
                        </span>
                        <span class="text-[11.5px] font-medium flex items-center gap-1.5 text-[#64748B]">
                            <span class="w-2 h-2 rounded-xs bg-[#3B82F6]"></span> Security &amp; SOC
                        </span>
                        <span class="text-[11.5px] font-medium flex items-center gap-1.5 text-[#64748B]">
                            <span class="w-2 h-2 rounded-xs bg-[#10B981]"></span> Data Center
                        </span>
                        <span class="text-[11.5px] font-medium flex items-center gap-1.5 text-[#64748B]">
                            <span class="w-2 h-2 rounded-xs bg-[#8B5CF6]"></span> SD-WAN Cloud
                        </span>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 4. RECENT ACTIVITY: 3-COLUMN BALANCED WIDGETS            --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 anim-fade-up anim-delay-4">
                
                {{-- Column 1: Proyek Solusi Terbaru --}}
                <div class="ipnet-card overflow-hidden flex flex-col">
                    <div class="flex justify-between items-center gap-2 p-4 sm:p-5 pb-3 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 bg-[#8F0A0D] rounded-full inline-block"></span>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Proyek Desain Terbaru</h3>
                        </div>
                        <a href="{{ route('presales.proposals.index') }}" class="text-[#8F0A0D] text-[12px] font-bold hover:underline">Lihat semua</a>
                    </div>
                    <div class="p-2 divide-y divide-[#F1F5F9] flex-1">
                        @forelse($recentDesignProjects as $project)
                        <div @click="openSpecModal({{ json_encode($project) }})" 
                             class="flex items-center justify-between gap-3 p-3 rounded-xl hover:bg-[#F8FAFC] transition-colors cursor-pointer group">
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-bold text-[#1E293B] group-hover:text-[#8F0A0D] transition-colors truncate" title="{{ $project->name }}">{{ $project->name }}</div>
                                <div class="text-[11.5px] text-[#64748B] truncate mt-0.5" title="{{ $project->client }}">
                                    {{ $project->client ?: 'Prospek Umum' }}
                                    @if($project->division)
                                        <span class="text-gray-400">·</span> {{ $project->division->name }}
                                    @endif
                                </div>
                            </div>
                            <div class="shrink-0">
                                <x-status-badge status="{{ $project->status }}" />
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-[#64748B]">
                            <p class="text-[13px]">Belum ada proyek desain</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Column 2: Antrean BoQ & SOW --}}
                <div class="ipnet-card overflow-hidden flex flex-col">
                    <div class="flex justify-between items-center gap-2 p-4 sm:p-5 pb-3 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 bg-amber-500 rounded-full inline-block"></span>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Antrean BoQ &amp; SOW</h3>
                        </div>
                        <a href="{{ route('presales.proposals.index', ['tab' => 'pending']) }}" class="text-[#8F0A0D] text-[12px] font-bold hover:underline">Lihat semua</a>
                    </div>
                    <div class="p-2 divide-y divide-[#F1F5F9] flex-1">
                        @forelse($pendingSowProjects as $pTask)
                        <div @click="openSpecModal({{ json_encode($pTask) }})"
                             class="flex items-center justify-between gap-3 p-3 rounded-xl hover:bg-[#F8FAFC] transition-colors cursor-pointer group">
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-bold text-[#1E293B] group-hover:text-[#8F0A0D] transition-colors truncate" title="{{ $pTask->name }}">{{ $pTask->name }}</div>
                                <div class="text-[11.5px] text-[#64748B] truncate mt-0.5">
                                    {{ $pTask->sales_name ? 'PIC: ' . $pTask->sales_name : ($pTask->client ?: 'Prospek Baru') }}
                                    @if($pTask->contract_value)
                                        <span class="text-gray-400">·</span> Rp {{ number_format($pTask->contract_value / 1000000, 0, ',', '.') }} Jt
                                    @endif
                                </div>
                            </div>
                            <div class="shrink-0">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10.5px] font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-md">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Perlu BoQ
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-[#64748B]">
                            <p class="text-[13px]">Tidak ada antrean BoQ</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Column 3: Agenda PoC & Uji Lab --}}
                <div class="ipnet-card overflow-hidden flex flex-col">
                    <div class="flex justify-between items-center gap-2 p-4 sm:p-5 pb-3 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 bg-emerald-600 rounded-full inline-block"></span>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Jadwal PoC &amp; Lab</h3>
                        </div>
                        <a href="{{ route('schedules.index') }}" class="text-[#8F0A0D] text-[12px] font-bold hover:underline">Lihat semua</a>
                    </div>
                    <div class="p-2 divide-y divide-[#F1F5F9] flex-1">
                        @forelse($pocSchedules as $sch)
                        @php
                            $isToday = $sch->date && $sch->date->isToday();
                            $dateLabel = $sch->date ? ($isToday ? 'Hari ini' : $sch->date->format('d M')) : '-';
                            $timeLabel = $sch->start_time ? substr($sch->start_time, 0, 5) . ' WIB' : '';
                            $badgeCategory = $sch->category ?: 'Sesi PoC & Lab';
                            
                            $engineerNames = '';
                            if ($sch->relationLoaded('engineers') && $sch->engineers->isNotEmpty()) {
                                $engineerNames = $sch->engineers->pluck('name')->join(', ');
                            } elseif ($sch->engineer) {
                                $engineerNames = $sch->engineer->name;
                            }
                        @endphp
                        <a href="{{ route('schedules.index') }}" class="flex items-center justify-between gap-3 p-3 rounded-xl hover:bg-[#F8FAFC] transition-colors group">
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-bold text-[#292929] group-hover:text-[#8F0A0D] transition-colors truncate" title="{{ $sch->title }}">
                                    {{ $sch->title }}
                                </div>
                                <div class="text-[11.5px] text-[#75727C] flex items-center gap-1.5 mt-0.5 min-w-0">
                                    <span class="shrink-0 whitespace-nowrap {{ $isToday ? 'text-[#8F0A0D] font-bold' : 'font-medium' }}">
                                        {{ $dateLabel }}{{ $timeLabel ? ', ' . $timeLabel : '' }}
                                    </span>
                                    @if($engineerNames || $sch->project)
                                        <span class="text-gray-400 shrink-0">·</span>
                                        <span class="truncate" title="{{ $engineerNames ?: $sch->project?->name }}">
                                             {{ $engineerNames ?: $sch->project?->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="shrink-0">
                                <x-status-badge :status="$badgeCategory" />
                            </div>
                        </a>
                        @empty
                        <div class="text-center py-8 text-[#75727C]">
                            <p class="text-[13px]">Belum ada jadwal PoC</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

    {{-- MODAL SPESIFIKASI PROYEK SOLUTION ARCHITECT --}}
    <template x-teleport="body">
        <div x-show="isSpecModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0E0D12]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto" @click.away="isSpecModalOpen = false">
                <div class="flex items-start justify-between border-b border-gray-100 pb-3">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#8F0A0D]">Detail Spesifikasi Proyek &amp; Solusi</span>
                        <h3 class="text-base font-bold text-[#1E293B]" x-text="activeProject.name || '-'"></h3>
                        <p class="text-xs text-[#64748B]" x-text="(activeProject.client ? activeProject.client + ' • ' : '') + (activeProject.project_code || '')"></p>
                    </div>
                    <button @click="isSpecModalOpen = false" class="text-gray-400 hover:text-gray-600 p-1 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="space-y-3.5 text-xs text-[#1E293B]">
                    <div class="grid grid-cols-2 gap-3 p-3 bg-[#F8FAFC] rounded-xl border border-gray-100">
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Klien / Instansi</span>
                            <span class="font-semibold text-gray-800" x-text="activeProject.client || '-'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Sales PIC</span>
                            <span class="font-semibold text-gray-800" x-text="activeProject.sales_name || '-'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Nilai Kontrak / Estimasi</span>
                            <span class="font-extrabold text-[#8F0A0D]" x-text="activeProject.contract_value ? 'Rp ' + Number(activeProject.contract_value).toLocaleString('id-ID') : 'Rp 0'"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Estimasi Mandays</span>
                            <span class="font-semibold text-gray-800" x-text="activeProject.mandays ? activeProject.mandays + ' Mandays' : 'Belum diisi'"></span>
                        </div>
                    </div>

                    <div>
                        <span class="text-gray-500 font-bold block mb-1">Deskripsi Proyek:</span>
                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-gray-700 leading-relaxed max-h-36 overflow-y-auto whitespace-pre-line" x-text="activeProject.description || 'Tidak ada deskripsi spesifik.'"></div>
                    </div>

                    <div x-show="activeProject.proposal_notes">
                        <span class="text-gray-500 font-bold block mb-1">Ruang Lingkup Teknis / SOW (Catatan SA):</span>
                        <div class="p-3 bg-red-50/50 rounded-xl border border-red-100 text-gray-800 leading-relaxed whitespace-pre-line" x-text="activeProject.proposal_notes"></div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                        <button type="button" @click="isSpecModalOpen = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition">
                            Tutup
                        </button>
                        <a :href="'/presales/proposals'" class="px-4 py-2 bg-[#8F0A0D] hover:bg-[#73080A] text-white font-bold rounded-xl text-xs transition">
                            Kelola Dokumen SOW
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </template>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function architectDashboard() {
        return {
            isSpecModalOpen: false,
            activeProject: {},
            openSpecModal(project) {
                this.activeProject = project;
                this.isSpecModalOpen = true;
            }
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Chart 1: Monthly Chart with IPNET Crimson Gradient
        const monthlyCtx = document.getElementById('architectMonthlyChart');
        if (monthlyCtx) {
            const ctx = monthlyCtx.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 230);
            gradient.addColorStop(0, '#8F0A0D');
            gradient.addColorStop(1, '#C61828');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Nilai Solusi (Juta Rp)',
                        data: {!! json_encode($monthlyChartData) !!},
                        backgroundColor: gradient,
                        hoverBackgroundColor: '#73080A',
                        borderRadius: 6,
                        maxBarThickness: 26
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 11 },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + Number(context.raw).toLocaleString('id-ID') + ' Juta';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#F1F5F9' },
                            ticks: {
                                font: { size: 10, family: 'sans-serif' },
                                color: '#64748B',
                                callback: function(value) { return 'Rp ' + value + 'M'; }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { 
                                font: { size: 10, family: 'sans-serif' },
                                color: '#64748B'
                            }
                        }
                    }
                }
            });
        }

        // Chart 2: Domain Doughnut Chart
        const domainCtx = document.getElementById('domainDoughnutChart');
        if (domainCtx) {
            new Chart(domainCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($domainLabels) !!},
                    datasets: [{
                        data: {!! json_encode($domainChartData) !!},
                        backgroundColor: ['#8F0A0D', '#3B82F6', '#10B981', '#8B5CF6'],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1E293B',
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 11 },
                            padding: 10,
                            cornerRadius: 8
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
