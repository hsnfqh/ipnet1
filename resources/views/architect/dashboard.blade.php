@extends('layouts.app')

@section('title', 'Dashboard Solution Architect - PT IP Network Solusindo')

@push('styles')
<style>
    /* ========================================================
       IPNET Solution Architect Dashboard Design System
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
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 4px 12px rgba(0, 0, 0, 0.015);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }

    .ipnet-metric-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        padding: 18px 20px;
        position: relative;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        transition: all 0.2s ease;
    }

    .ipnet-metric-card:hover {
        transform: translateY(-2px);
        border-color: #CBD5E1;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
    }

    .ipnet-badge-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #8F0A0D;
        display: inline-block;
        margin-right: 8px;
    }

    /* Animations */
    @keyframes heroReveal {
        0% { opacity: 0; transform: translateY(18px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(14px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    .anim-hero-reveal {
        animation: heroReveal 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-fade-up {
        animation: fadeUpStagger 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-delay-1 { animation-delay: 0.06s !important; }
    .anim-delay-2 { animation-delay: 0.12s !important; }
    .anim-delay-3 { animation-delay: 0.18s !important; }
    .anim-delay-4 { animation-delay: 0.24s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard Solution Architect'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">
            
            {{-- ======================================================== --}}
            {{-- 1. EXECUTIVE HERO BANNER (RINGKAS & FOKUS JOBDESK SA)    --}}
            {{-- ======================================================== --}}
            <div class="ipnet-hero-banner rounded-2xl px-5 py-4 sm:px-6 sm:py-4.5 text-white shadow-md shadow-red-950/15 relative anim-hero-reveal">
                {{-- Background Geometric Planes --}}
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
                            <filter id="saShadow" x="-10%" y="-10%" width="130%" height="130%">
                                <feDropShadow dx="-6" dy="8" stdDeviation="12" flood-color="#3A0207" flood-opacity="0.35" />
                            </filter>
                        </defs>
                        <rect width="1440" height="200" fill="url(#saGrad1)" />
                        <polygon points="0,0 650,0 280,200 0,200" fill="url(#saGrad1)" />
                        <polygon points="220,0 850,0 1300,200 600,200" fill="url(#saGrad2)" filter="url(#saShadow)" />
                        <polygon points="780,0 1440,0 1440,200 1100,200" fill="#63050F" opacity="0.6" filter="url(#saShadow)" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center mb-1.5 px-2.5 py-0.5 text-[10.5px] font-bold text-white bg-white/15 backdrop-blur-md rounded-full border border-white/20 tracking-wider uppercase">
                            <span class="w-1.5 h-1.5 mr-1.5 bg-white rounded-full inline-block"></span>
                            SOLUTION ARCHITECT &amp; TECHNICAL PRESALES
                        </div>

                        <h1 class="text-[19px] sm:text-[22px] font-extrabold text-white tracking-tight leading-tight">
                            Selamat Datang, {{ auth()->user()->name }}
                        </h1>
                        <p class="mt-0.5 text-[12.5px] text-white/85 leading-relaxed line-clamp-1">
                            Fokus pada perancangan arsitektur (HLD/LLD), kaji BoQ/SOW, dan validasi sesi PoC klien.
                        </p>
                    </div>

                    {{-- Quick Action SA & Filter Tahun --}}
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
                            <span>Agenda PoC &amp; Lab</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 2. 4 METRIK UTAMA SA (FOKUS, LEGA & TIDAK NUMPUK)        --}}
            {{-- ======================================================== --}}
            <div class="anim-fade-up anim-delay-1">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    {{-- 1. Antrean Kaji Teknis & Sizing BoQ (Kerjaan SA Utama) --}}
                    <div class="ipnet-metric-card group block">
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="text-[11.5px] font-bold text-[#75727C] uppercase tracking-wider">Antrean Kaji &amp; BoQ</span>
                            <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[26px] font-extrabold text-amber-600 tracking-tight leading-none mb-1">
                            {{ $designPendingCount }} <span class="text-xs font-bold text-amber-700/70">Proyek</span>
                        </div>
                        <p class="text-[12px] text-[#64748B]">Perlu sizing spesifikasi &amp; kalkulasi BoQ</p>
                    </div>

                    {{-- 2. Dokumen Desain & SOW Terbit --}}
                    <a href="{{ route('presales.proposals.index') }}" class="ipnet-metric-card group block">
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="text-[11.5px] font-bold text-[#75727C] uppercase tracking-wider">Desain SOW/HLD Terbit</span>
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[26px] font-extrabold text-emerald-600 tracking-tight leading-none mb-1">
                            {{ $proposalsReadyCount }} <span class="text-xs font-bold text-emerald-700/70">Dokumen</span>
                        </div>
                        <p class="text-[12px] text-emerald-700 font-medium">HLD, LLD &amp; SOW siap tender/deliver</p>
                    </a>

                    {{-- 3. Sesi PoC & Uji Coba Terjadwal --}}
                    <a href="{{ route('schedules.index') }}" class="ipnet-metric-card group block">
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="text-[11.5px] font-bold text-[#75727C] uppercase tracking-wider">Sesi Uji Coba (PoC)</span>
                            <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[26px] font-extrabold text-blue-600 tracking-tight leading-none mb-1">
                            {{ $pocSchedules->count() }} <span class="text-xs font-bold text-blue-700/70">Agenda</span>
                        </div>
                        <p class="text-[12px] text-[#64748B]">Validasi lab konsep bersama mitra/klien</p>
                    </a>

                    {{-- 4. Pipeline Nilai Solusi --}}
                    <div class="ipnet-metric-card group block">
                        <div class="flex items-center justify-between mb-2.5">
                            <span class="text-[11.5px] font-bold text-[#75727C] uppercase tracking-wider">Total Nilai Solusi</span>
                            <div class="w-8 h-8 rounded-xl bg-[#8F0A0D]/10 text-[#8F0A0D] flex items-center justify-center group-hover:bg-[#8F0A0D] group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[23px] font-extrabold text-[#1E293B] tracking-tight leading-none mb-1">
                            Rp {{ number_format($totalPipelineValue / 1000000000, 1, ',', '.') }} M
                        </div>
                        <p class="text-[12px] text-[#64748B]">{{ $totalProjectsCount }} Portofolio peluang solusi</p>
                    </div>

                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 3. WORKSPACE UTAMA SA: TABEL SOW & PANEL HANDOVER/POC     --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 anim-fade-up anim-delay-2">
                
                {{-- Kolom Kiri (8/12): Antrean Kaji Teknis & SOW Terkini --}}
                <div class="lg:col-span-8 ipnet-card overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 p-4 sm:p-5 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2.5 h-4.5 bg-[#8F0A0D] rounded-full inline-block"></span>
                                <div>
                                    <h3 class="text-[15px] font-bold text-[#1E293B]">Antrean Kaji Teknis &amp; Dokumen SOW</h3>
                                    <p class="text-[11.5px] text-[#64748B]">Daftar peluang yang membutuhkan kalkulasi BoQ dan penerbitan SOW</p>
                                </div>
                            </div>
                            <a href="{{ route('presales.proposals.index') }}" class="text-[#8F0A0D] text-[12px] font-bold hover:underline inline-flex items-center gap-1 self-start sm:self-auto">
                                <span>Lihat Semua SOW</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>

                        <div class="overflow-x-auto p-2">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-[#F8FAFC] text-[#64748B] uppercase text-[10.5px] font-bold border-b border-[#E2E8F0]">
                                    <tr>
                                        <th class="py-3 px-3.5 rounded-l-xl">Proyek &amp; Solusi</th>
                                        <th class="py-3 px-3.5">Klien</th>
                                        <th class="py-3 px-3.5 text-right">Nilai Estimasi</th>
                                        <th class="py-3 px-3.5 text-center">Status Desain</th>
                                        <th class="py-3 px-3.5 rounded-r-xl text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#F1F5F9] font-medium text-[#1E293B]">
                                    @forelse($recentDesignProjects as $rp)
                                        <tr class="hover:bg-[#F8FAFC] transition-colors">
                                            <td class="py-3 px-3.5">
                                                <div class="font-bold text-[#1E293B] text-[12.5px] line-clamp-1" title="{{ $rp->name }}">{{ $rp->name }}</div>
                                                <div class="text-[11px] text-[#64748B] mt-0.5">
                                                    {{ $rp->project_code ? $rp->project_code . ' • ' : '' }}{{ $rp->division ? $rp->division->name : ($rp->project_type ?: 'Network & Security') }}
                                                </div>
                                            </td>
                                            <td class="py-3 px-3.5 whitespace-nowrap">
                                                <div class="font-semibold text-[#1E293B] text-[12px]">{{ $rp->client ?: 'Prospek Umum' }}</div>
                                            </td>
                                            <td class="py-3 px-3.5 text-right whitespace-nowrap font-extrabold text-[#1E293B]">
                                                Rp {{ number_format($rp->contract_value ?? 0, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                                @if($rp->proposal_file)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                        HLD/SOW Selesai
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                                        Perlu BoQ &amp; Sizing
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3.5 text-right whitespace-nowrap">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    @if($rp->proposal_file)
                                                        <a href="{{ route('presales.proposals.download', $rp->id) }}" 
                                                           class="px-2.5 py-1 text-[11px] font-bold text-[#8F0A0D] bg-[#8F0A0D]/10 hover:bg-[#8F0A0D] hover:text-white rounded-lg transition-colors shadow-2xs">
                                                            SOW
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('projects.show', $rp->id) }}" 
                                                       class="px-3 py-1 bg-white hover:bg-[#F8FAFC] text-[#1E293B] text-[11px] font-semibold rounded-lg border border-[#CBD5E1] shadow-2xs transition">
                                                        Detail
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-10 text-center text-[#64748B]">
                                                <p class="text-[12.5px] font-semibold">Belum ada data proyek desain arsitektur</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan (4/12): Sesi PoC & Agenda Lab --}}
                <div class="lg:col-span-4 space-y-5">
                    
                    {{-- Widget: Agenda PoC & Uji Konsep Lab --}}
                    <div class="ipnet-card p-4 sm:p-5 flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3 mb-3.5">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-4.5 bg-blue-600 rounded-full inline-block"></span>
                                    <div>
                                        <h4 class="text-[13px] font-bold text-[#1E293B] uppercase tracking-wider">Agenda PoC &amp; Demo Lab</h4>
                                        <p class="text-[11px] text-[#64748B]">Sesi validasi konsep teknologi</p>
                                    </div>
                                </div>
                                <a href="{{ route('schedules.index') }}" class="text-[11px] font-bold text-[#8F0A0D] hover:underline">Semua</a>
                            </div>

                            <div class="space-y-3">
                                @forelse($pocSchedules as $poc)
                                    <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] hover:border-[#CBD5E1] transition flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex flex-col items-center justify-center shrink-0 text-xs font-bold leading-none">
                                            <span>{{ \Carbon\Carbon::parse($poc->date)->format('d') }}</span>
                                            <span class="text-[9px] font-normal uppercase mt-0.5">{{ \Carbon\Carbon::parse($poc->date)->format('M') }}</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="text-[12.5px] font-bold text-[#1E293B] truncate" title="{{ $poc->title }}">{{ $poc->title }}</div>
                                            <div class="text-[11px] text-[#64748B] truncate mt-0.5">{{ $poc->project ? $poc->project->name : 'Sesi Uji Konsep Jaringan' }}</div>
                                            <div class="text-[10.5px] text-[#8F0A0D] font-semibold mt-1 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>{{ substr($poc->start_time, 0, 5) }} WIB</span>
                                                @if($poc->engineer)
                                                    <span class="text-gray-300">•</span>
                                                    <span class="text-[#64748B] font-normal truncate">{{ $poc->engineer->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-10 text-xs text-[#64748B]">
                                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="font-semibold text-gray-500">Tidak ada jadwal PoC terdekat</p>
                                        <p class="text-[10.5px] text-gray-400 mt-0.5">Jadwal uji coba lab akan muncul di sini</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-[#F1F5F9]">
                            <a href="{{ route('schedules.index') }}" class="w-full py-2 px-3 rounded-xl bg-[#F8FAFC] hover:bg-[#F1F5F9] border border-[#E2E8F0] text-[11.5px] font-bold text-[#1E293B] flex items-center justify-center gap-1.5 transition">
                                <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Atur Jadwal PoC Baru</span>
                            </a>
                        </div>
                    </div>

                </div>

            </div>

            {{-- ======================================================== --}}
            {{-- 4. ANALISIS PORTOFOLIO: TREN DESAIN & DOMAIN TEKNOLOGI   --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 anim-fade-up anim-delay-3">
                
                {{-- Chart 1: Tren Nilai Solusi Bulanan --}}
                <div class="lg:col-span-2 ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-wrap justify-between items-center gap-2 mb-4 pb-3 border-b border-[#E2E8F0]">
                            <div>
                                <p class="text-[#8F0A0D] text-[11px] font-bold inline-flex items-center uppercase tracking-wider">
                                    <span class="ipnet-badge-dot"></span> TREN VOLUME DESAIN
                                </p>
                                <h3 class="text-[16px] font-bold text-[#1E293B] tracking-tight">Tren Perancangan Solusi Arsitektur ({{ $selectedYear }})</h3>
                                <p class="text-[11.5px] text-[#64748B] mt-0.5">Akumulasi nilai solusi teknis yang dirancang per bulan (Juta Rupiah)</p>
                            </div>
                        </div>
                        <div class="w-full overflow-x-auto">
                            <div style="height: 220px; min-width: 300px;">
                                <canvas id="architectMonthlyChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Chart 2: Komposisi Domain Teknologi --}}
                <div class="ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="mb-3 pb-3 border-b border-[#E2E8F0]">
                            <p class="text-[#8F0A0D] text-[11px] font-bold inline-flex items-center uppercase tracking-wider">
                                <span class="ipnet-badge-dot"></span> PORTOFOLIO TEKNOLOGI
                            </p>
                            <h3 class="text-[16px] font-bold text-[#1E293B] tracking-tight">Domain Solusi Jaringan</h3>
                            <p class="text-[11.5px] text-[#64748B] mt-0.5">Distribusi spesialisasi arsitektur</p>
                        </div>
                        <div style="height: 170px;" class="flex items-center justify-center">
                            <canvas id="domainDoughnutChart"></canvas>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 justify-center mt-2 pt-2 border-t border-[#F1F5F9] text-[10.5px] text-[#64748B]">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-[#8F0A0D]"></span> Campus Network</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-[#3B82F6]"></span> Security &amp; SOC</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-[#10B981]"></span> Data Center</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-[#8B5CF6]"></span> SD-WAN Cloud</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart 1: Monthly Chart with IPNET Crimson Gradient
        const monthlyCtx = document.getElementById('architectMonthlyChart');
        if (monthlyCtx) {
            const ctx = monthlyCtx.getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 220);
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
