@extends('layouts.app')

@section('title', 'Dashboard Presales - PT IP Network Solusindo')

@push('styles')
<style>
    /* ========================================================
       IPNET Official Brand Design System (ipnetsolusindo.com)
       ======================================================== */
    [x-cloak] { display: none !important; }

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
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">
            
            {{-- ======================================================== --}}
            {{-- 1. EXECUTIVE HERO BANNER (IPNET BRAND PATTERN)           --}}
            {{-- ======================================================== --}}
            <div class="ipnet-hero-banner rounded-2xl px-5 py-4 sm:px-6 sm:py-4.5 text-white shadow-md shadow-red-950/15 relative anim-hero-reveal">
                {{-- Layered Geometric Faceted Red Planes --}}
                <div class="absolute inset-0 pointer-events-none overflow-hidden select-none">
                    <svg class="w-full h-full object-cover" viewBox="0 0 1440 200" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="psGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#C61828" />
                                <stop offset="100%" stop-color="#9E0E1D" />
                            </linearGradient>
                            <linearGradient id="psGrad2" x1="100%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#B01423" />
                                <stop offset="100%" stop-color="#7A0813" />
                            </linearGradient>
                            <linearGradient id="psGrad3" x1="0%" y1="100%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#940E1B" />
                                <stop offset="100%" stop-color="#5A040C" />
                            </linearGradient>
                            <linearGradient id="psGradHighlight" x1="0%" y1="0%" x2="100%" y2="50%">
                                <stop offset="0%" stop-color="#FFA8B2" stop-opacity="0.20" />
                                <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0" />
                            </linearGradient>
                            <filter id="psDropShadow" x="-10%" y="-10%" width="130%" height="130%">
                                <feDropShadow dx="-8" dy="10" stdDeviation="14" flood-color="#3A0207" flood-opacity="0.4" />
                            </filter>
                        </defs>

                        <!-- Base Background -->
                        <rect width="1440" height="200" fill="url(#psGrad1)" />

                        <!-- Top-Left Large Diagonal Angled Plane -->
                        <polygon points="0,0 650,0 280,200 0,200" fill="url(#psGrad1)" />

                        <!-- Intersecting Broad Diagonal Facet Strip -->
                        <polygon points="220,0 850,0 1300,200 600,200" fill="url(#psGrad2)" filter="url(#psDropShadow)" />

                        <!-- Crossing Foreground Diagonal Bright Red Plane -->
                        <polygon points="0,0 520,0 1080,200 480,200" fill="url(#psGrad1)" opacity="0.9" filter="url(#psDropShadow)" />

                        <!-- Right Edge Deeper Contrast Facet -->
                        <polygon points="780,0 1440,0 1440,200 1100,200" fill="url(#psGrad3)" filter="url(#psDropShadow)" />

                        <!-- Soft Angular Ambient Highlight Overlays -->
                        <polygon points="0,0 680,0 1120,200 380,200" fill="url(#psGradHighlight)" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center mb-1.5 px-2.5 py-0.5 text-[10.5px] font-bold text-white bg-white/15 backdrop-blur-md rounded-full border border-white/20 tracking-wider uppercase">
                            <span class="w-1.5 h-1.5 mr-1.5 bg-white rounded-full inline-block"></span>
                            PT IP NETWORK SOLUSINDO &bull; PRESALES ENGINEERING
                        </div>

                        <h1 class="text-[18px] sm:text-[21px] font-extrabold text-white tracking-tight leading-tight">
                            Selamat Datang, {{ auth()->user()->name }}
                        </h1>
                        <p class="mt-0.5 text-[12.5px] text-white/80 leading-relaxed line-clamp-1">
                            Kelola penyusunan proposal teknis, sizing BoQ, estimasi mandays, dan koordinasi agenda PoC.
                        </p>
                    </div>

                    {{-- Quick Action Buttons & Filter Tahun --}}
                    <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                        <form method="GET" action="{{ route('dashboard.presales') }}" id="yearFilterForm" class="shrink-0">
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

                        <a href="{{ route('sales.pipeline.index') }}" class="px-3.5 py-2 rounded-xl bg-white text-[#8F0A0D] font-bold text-[12.5px] hover:bg-[#FFF7F6] hover:scale-[1.02] transition-all shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <span>Daftar Project</span>
                        </a>

                        <a href="{{ route('schedules.index') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 hover:scale-[1.02] border border-white/25 text-white font-bold text-[12.5px] transition-all flex items-center gap-1.5 backdrop-blur-sm">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Jadwal PoC</span>
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
                            <span class="ipnet-badge-dot"></span> RINGKASAN PRE-SALES &amp; TENDER
                        </p>
                        <h2 class="text-[18px] font-bold text-[#292929] tracking-tight">Status &amp; Distribusi Proposal Teknis</h2>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
                    
                    {{-- Metric 1: Total Tender --}}
                    <a href="{{ route('presales.proposals.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Total Tender</span>
                            <div class="w-7 h-7 rounded-lg bg-[#8F0A0D]/10 text-[#8F0A0D] flex items-center justify-center group-hover:bg-[#8F0A0D] group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-[#292929] tracking-tight">{{ $totalTenderCount }}</div>
                        <p class="text-[11px] text-[#75727C] mt-0.5">Portofolio tender</p>
                    </a>

                    {{-- Metric 2: Perlu Proposal & BoQ --}}
                    <a href="{{ route('presales.proposals.index', ['tab' => 'pending']) }}" class="ipnet-metric-card group block anim-fade-up anim-delay-2">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Perlu Proposal</span>
                            <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center group-hover:bg-amber-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-amber-600 tracking-tight">{{ $totalProposalNeeded }}</div>
                        <p class="text-[11px] text-amber-600 font-semibold mt-0.5">Antrean perancangan</p>
                    </a>

                    {{-- Metric 3: Proposal Siap --}}
                    <a href="{{ route('presales.proposals.index', ['tab' => 'submitted']) }}" class="ipnet-metric-card group block anim-fade-up anim-delay-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Proposal Siap</span>
                            <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-emerald-600 tracking-tight">{{ $proposalsReadyCount }}</div>
                        <p class="text-[11px] text-[#75727C] mt-0.5">Siap tender / sales</p>
                    </a>

                    {{-- Metric 4: Tender Menang --}}
                    <a href="{{ route('presales.proposals.index', ['tab' => 'won']) }}" class="ipnet-metric-card group block anim-fade-up anim-delay-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Tender Menang</span>
                            <div class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-blue-600 tracking-tight">{{ $wonCount }}</div>
                        <p class="text-[11px] text-[#75727C] mt-0.5">Deal won / delivery</p>
                    </a>

                    {{-- Metric 5: Agenda PoC & Demo --}}
                    <a href="{{ route('schedules.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Agenda PoC</span>
                            <div class="w-7 h-7 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-purple-600 tracking-tight">{{ $pocSchedules->count() }}</div>
                        <p class="text-[11px] text-[#75727C] mt-0.5">Uji coba konsep</p>
                    </a>

                    {{-- Metric 6: Technical Win Rate --}}
                    <div class="ipnet-metric-card group block anim-fade-up anim-delay-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Win Rate Teknis</span>
                            <div class="w-7 h-7 rounded-lg bg-rose-500/10 text-rose-600 flex items-center justify-center group-hover:bg-rose-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[22px] font-extrabold text-[#8F0A0D] tracking-tight truncate">
                            {{ $technicalWinRate }}%
                        </div>
                        <p class="text-[11px] text-[#75727C] mt-0.5 truncate">Konversi tender won</p>
                    </div>

                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 3. CHARTS: TREN NILAI PROYEK & DISTRIBUSI PIPELINE       --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 anim-fade-up anim-delay-3">
                
                {{-- Chart 1: Tren Nilai Proyek Bulanan --}}
                <div class="lg:col-span-2 ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-wrap justify-between items-center gap-2 mb-4 pb-3 border-b border-[#E2E8F0]">
                            <div>
                                <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                    <span class="ipnet-badge-dot"></span> TREN VOLUME TENDER
                                </p>
                                <h3 class="text-[17px] font-bold text-[#1E293B] tracking-tight">Nilai Pipeline Pre-Sales per Bulan ({{ $selectedYear }})</h3>
                            </div>
                            <span class="text-[11.5px] font-medium text-[#64748B]">Akumulasi nilai tender (Juta Rp)</span>
                        </div>
                        <div class="w-full overflow-x-auto">
                            <div style="height: 230px; min-width: 280px;">
                                <canvas id="presalesMonthlyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Chart 2: Komposisi Distribusi Pipeline --}}
                <div class="ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="mb-4 pb-3 border-b border-[#E2E8F0]">
                            <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                <span class="ipnet-badge-dot"></span> DISTRIBUSI PIPELINE
                            </p>
                            <h3 class="text-[17px] font-bold text-[#1E293B] tracking-tight">Distribusi Status Nilai Solusi</h3>
                        </div>
                        <div style="height: 190px;" class="flex items-center justify-center">
                            <canvas id="presalesDistributionChart"></canvas>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2.5 justify-center mt-3 pt-3 border-t border-[#F1F5F9]">
                        <span class="text-[11.5px] font-medium flex items-center gap-1.5 text-[#64748B]">
                            <span class="w-2 h-2 rounded-xs bg-[#8F0A0D]"></span> Perlu Proposal
                        </span>
                        <span class="text-[11.5px] font-medium flex items-center gap-1.5 text-[#64748B]">
                            <span class="w-2 h-2 rounded-xs bg-[#3B82F6]"></span> Proposal Siap
                        </span>
                        <span class="text-[11.5px] font-medium flex items-center gap-1.5 text-[#64748B]">
                            <span class="w-2 h-2 rounded-xs bg-[#10B981]"></span> Deal Won
                        </span>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 4. RECENT ACTIVITY TABLE: PERMINTAAN PROPOSAL TERKINI     --}}
            {{-- ======================================================== --}}
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-4 sm:p-5 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-4.5 bg-[#8F0A0D] rounded-full inline-block"></span>
                        <div>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Pipeline Dokumen Proposal &amp; SOW Terkini</h3>
                            <p class="text-[11.5px] text-[#64748B]">Daftar tender aktif yang memerlukan kajian teknis, sizing BoQ, dan dokumen penawaran</p>
                        </div>
                    </div>
                    <a href="{{ route('presales.proposals.index') }}" class="px-3.5 py-1.5 text-xs font-bold text-[#8F0A0D] bg-white border border-[#CBD5E1] rounded-xl hover:bg-[#FFF7F6] hover:border-[#8F0A0D] transition shadow-2xs inline-flex items-center gap-1.5">
                        <span>Buka Manajemen SOW</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#F8FAFC] text-[#64748B] uppercase text-[11px] font-bold border-b border-[#E2E8F0]">
                            <tr>
                                <th class="py-3 px-4">Nama Tender &amp; Klien</th>
                                <th class="py-3 px-4">Sales PIC</th>
                                <th class="py-3 px-4 text-right">Nilai Kontrak</th>
                                <th class="py-3 px-4 text-center">Status Proposal</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F9] font-medium text-[#1E293B]">
                            @forelse($recentRequests as $rp)
                                <tr class="hover:bg-[#F8FAFC] transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-[#1E293B] text-[13px] line-clamp-1" title="{{ $rp->name }}">{{ $rp->name }}</div>
                                        <div class="text-[11px] text-[#64748B] mt-0.5">{{ $rp->client ?: 'Prospek Umum' }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-0.5 text-[11px] font-semibold rounded-md bg-[#F1F5F9] text-[#1E293B] border border-[#E2E8F0]">
                                            {{ $rp->sales_name ?: 'Sales Rep' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap font-extrabold text-[#1E293B]">
                                        {{ $rp->contract_value > 0 ? 'Rp ' . number_format($rp->contract_value / 1000000, 1, ',', '.') . ' Jt' : '—' }}
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($rp->proposal_file)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Proposal Siap
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                Perlu BoQ / SOW
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            @if($rp->proposal_file)
                                                <a href="{{ route('presales.proposals.download', $rp->id) }}" 
                                                   title="Unduh Berkas SOW"
                                                   class="p-1.5 rounded-lg text-emerald-700 hover:text-emerald-800 hover:bg-emerald-50 border border-emerald-200 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                </a>
                                            @endif
                                            <a href="{{ route('presales.proposals.index') }}" 
                                               title="Kelola SOW"
                                               class="p-1.5 rounded-lg text-[#1E293B] hover:text-[#8F0A0D] hover:bg-gray-100 border border-gray-200 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-[#64748B]">
                                        <p class="text-[12.5px] font-semibold">Tidak ada data tender aktif saat ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart 1: Monthly Trend Bar Chart with IPNET Crimson Gradient
        const monthlyCtx = document.getElementById('presalesMonthlyChart');
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
                        label: 'Nilai Pipeline (Juta Rp)',
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

        // Chart 2: Distribution Doughnut Chart
        const distCtx = document.getElementById('presalesDistributionChart');
        if (distCtx) {
            new Chart(distCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Perlu Proposal', 'Proposal Siap', 'Deal Won'],
                    datasets: [{
                        data: {!! json_encode($distributionData) !!},
                        backgroundColor: ['#8F0A0D', '#3B82F6', '#10B981'],
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
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': Rp ' + Number(context.raw).toLocaleString('id-ID') + ' Jt';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
