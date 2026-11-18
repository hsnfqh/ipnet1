@extends('layouts.app')

@section('title', 'Dashboard Managed Service - PT IP Network Solusindo')

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
    .kpi-card {
        background: #FFFFFF;
        border: 1px solid #E8EDF3;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 130px;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        border-color: #CBD5E1;
        box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    }
    .pill-group {
        display: inline-flex;
        background: #F1F5F9;
        border-radius: 10px;
        padding: 3px;
        gap: 2px;
    }
    .pill-btn {
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s ease;
        color: #64748B;
        border: 1px solid transparent;
        background: transparent;
        text-decoration: none;
    }
    .pill-btn.active {
        background: #FFFFFF;
        color: #8F0A0D;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        border-color: #E2E8F0;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="msDashboard()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-[13px] font-semibold shadow-xs anim-fade-up">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            {{-- ======================================================== --}}
            {{-- 1. EXECUTIVE HERO BANNER (IPNET OFFICIAL BRAND PATTERN)  --}}
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
                        <rect width="1440" height="200" fill="url(#redGrad1)" />
                        <polygon points="0,0 650,0 280,200 0,200" fill="url(#redGrad1)" />
                        <polygon points="220,0 850,0 1300,200 600,200" fill="url(#redGrad2)" filter="url(#facetDropShadow)" />
                        <polygon points="0,0 520,0 1080,200 480,200" fill="url(#redGrad1)" opacity="0.9" filter="url(#facetDropShadow)" />
                        <polygon points="780,0 1440,0 1440,200 1100,200" fill="url(#redGrad3)" filter="url(#facetDropShadow)" />
                        <polygon points="0,0 680,0 1120,200 380,200" fill="url(#redGradHighlight)" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center mb-1.5 px-2.5 py-0.5 text-[10.5px] font-bold text-white bg-white/15 backdrop-blur-md rounded-full border border-white/20 tracking-wider uppercase">
                            <span class="w-1.5 h-1.5 mr-1.5 bg-white rounded-full inline-block"></span>
                            MONITORING SLA &bull; MANAGED SERVICE
                        </div>

                        <h1 class="text-[18px] sm:text-[21px] font-extrabold text-white tracking-tight leading-tight">
                            Selamat Datang, {{ auth()->user()->name }}
                        </h1>
                        <p class="mt-0.5 text-[12.5px] text-white/80 leading-relaxed line-clamp-1">
                            Pantau kepatuhan SLA operasional, antrean tiket gangguan, status operasional aset CI, dan jadwal preventive maintenance.
                        </p>
                    </div>

                    {{-- Quick Action Button: Hanya Daftar Proyek Saja --}}
                    <div class="flex items-center gap-2.5 shrink-0">
                        <a href="{{ route('ms.projects.index') }}" 
                           class="px-4 py-2.5 rounded-xl bg-white text-[#8F0A0D] font-bold text-[13px] hover:bg-[#FFF7F6] hover:scale-[1.02] transition-all shadow-sm flex items-center gap-2 cursor-pointer active:scale-95"
                           title="Buka Daftar Seluruh Proyek Managed Service">
                            <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span>Daftar Proyek</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- INCOMING HANDOVER ALERT (SIAPA CEPAT JADI PIC)            --}}
            {{-- ======================================================== --}}
            @if(isset($pendingHandoversCount) && $pendingHandoversCount > 0)
                <div class="p-3.5 sm:p-4 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-xs anim-fade-up">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-bold text-amber-950">Ada {{ $pendingHandoversCount }} Proyek Serah Terima Masuk (Incoming Handover) Menunggu Diterima</h4>
                            <p class="text-[11.5px] text-amber-800">Tim Managed Service berdiri mandiri tanpa hirarki kaku. Personil yang pertama kali menerima &amp; menandatangani serah terima akan otomatis menjadi PIC resmi proyek.</p>
                        </div>
                    </div>
                    <a href="{{ route('ms.projects.index', ['tab' => 'incoming']) }}" class="shrink-0 px-3.5 py-1.5 rounded-lg bg-[#8F0A0D] hover:bg-[#73080A] text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer active:scale-95">
                        <span>Lihat &amp; Terima Proyek</span>
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            @endif

            {{-- ======================================================== --}}
            {{-- 2. METRIC SUMMARY CARDS (6 METRIC CARDS)                 --}}
            {{-- ======================================================== --}}
            <div class="anim-fade-up anim-delay-1">
                <div class="flex items-center justify-between mb-3.5">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> RINGKASAN OPERASIONAL
                        </p>
                        <h2 class="text-[18px] font-bold text-[#292929] tracking-tight">Status Project dan Distribusi Layanan</h2>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5 sm:gap-4">
                    
                    {{-- Metric 1: Proyek Aktif --}}
                    <a href="{{ route('ms.projects.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-1" title="Buka & Kelola Seluruh Daftar Proyek Managed Service">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#64748B] group-hover:text-[#8F0A0D] uppercase tracking-wider transition-colors">Proyek Aktif</span>
                            <div class="w-7 h-7 rounded-lg bg-[#F1F5F9] text-[#475569] flex items-center justify-center border border-[#E2E8F0] group-hover:bg-[#8F0A0D] group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[24px] font-extrabold text-[#1E293B] group-hover:text-[#8F0A0D] transition-colors tracking-tight">{{ $operateProjects->count() }}</div>
                        <div class="flex items-center justify-between mt-0.5">
                            <p class="text-[11px] text-[#64748B]">Proyek Maintenance</p>
                            <span class="text-[10.5px] font-bold text-[#8F0A0D] opacity-0 group-hover:opacity-100 transition-opacity">Kelola &rarr;</span>
                        </div>
                    </a>

                    {{-- Metric 2: Jadwal PM Bulan Ini --}}
                    <a href="{{ route('ms.maintenance.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-2">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Agenda PM</span>
                            <div class="w-7 h-7 rounded-lg bg-[#F1F5F9] text-[#475569] flex items-center justify-center border border-[#E2E8F0] group-hover:bg-[#1E293B] group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[24px] font-extrabold text-[#1E293B] tracking-tight">{{ $totalPmSchedulesCount ?? 0 }}</div>
                        <p class="text-[11px] text-[#64748B] mt-0.5">Kunjungan bulan ini</p>
                    </a>

                    {{-- Metric 3: Total Tiket Aktif --}}
                    <a href="{{ route('ms.tickets.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Tiket Aktif</span>
                            <div class="w-7 h-7 rounded-lg {{ $openTickets > 0 ? 'bg-[#FEF2F2] text-[#8F0A0D] border border-red-100' : 'bg-[#F1F5F9] text-[#475569] border border-[#E2E8F0]' }} flex items-center justify-center group-hover:bg-[#8F0A0D] group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[24px] font-extrabold text-[#1E293B] tracking-tight">{{ $openTickets }}</div>
                        <p class="text-[11px] mt-0.5 {{ $criticalTickets > 0 ? 'text-[#8F0A0D] font-bold' : 'text-[#64748B]' }}">
                            @if($criticalTickets > 0)
                                {{ $criticalTickets }} Tiket Kritis (P1)
                            @elseif($openTickets > 0)
                                Antrean gangguan aktif
                            @else
                                Tidak ada antrean tiket
                            @endif
                        </p>
                    </a>

                    {{-- Metric 4: Tiket Selesai --}}
                    <a href="{{ route('ms.tickets.index', ['status' => 'Resolved']) }}" class="ipnet-metric-card group block anim-fade-up anim-delay-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Tiket Selesai</span>
                            <div class="w-7 h-7 rounded-lg bg-[#F1F5F9] text-[#475569] flex items-center justify-center border border-[#E2E8F0] group-hover:bg-[#1E293B] group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[24px] font-extrabold text-[#1E293B] tracking-tight">{{ $resolvedTickets }}</div>
                        <p class="text-[11px] text-[#64748B] mt-0.5">Bulan berjalan</p>
                    </a>

                    {{-- Metric 5: SLA Availability (Network Uptime) --}}
                    <a href="{{ route('ms.monitoring.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-5" title="Buka Detail Monitoring SLA & Kepatuhan Layanan">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#64748B] group-hover:text-[#8F0A0D] uppercase tracking-wider transition-colors">SLA Availability</span>
                            <div class="w-7 h-7 rounded-lg {{ $slaUptimeScore >= $targetSla ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-[#8F0A0D] border border-red-200' }} flex items-center justify-center group-hover:bg-[#8F0A0D] group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[24px] font-extrabold text-[#1E293B] group-hover:text-[#8F0A0D] transition-colors tracking-tight">{{ number_format($slaUptimeScore, 1) }}%</div>
                        <div class="flex items-center justify-between mt-0.5">
                            <p class="text-[11px] {{ $slaUptimeScore >= $targetSla ? 'text-emerald-700 font-semibold' : 'text-[#8F0A0D] font-bold' }}">
                                {{ $totalDowntimeHours > 0 ? number_format($totalDowntimeHours, 1) . ' Jam Down' : 'Target ' . number_format($targetSla, 1) . '%' }}
                            </p>
                            <span class="text-[10.5px] font-bold text-[#8F0A0D] opacity-0 group-hover:opacity-100 transition-opacity">Pantau &rarr;</span>
                        </div>
                    </a>

                </div>
            </div>


            {{-- ======================================================== --}}
            {{-- 3. PORTOFOLIO KLIEN & STATUS OPERASIONAL LAYANAN         --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 anim-fade-up anim-delay-3">
                
                {{-- Left: Customer Operational Status List (lg:col-span-2) --}}
                <div class="lg:col-span-2 ipnet-card p-5 sm:p-6 flex flex-col justify-between"
                     x-data="{ 
                         viewMode: 'grid', 
                         selectedProject: null 
                     }">
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4 pb-3 border-b border-[#E2E8F0]">
                            <div>
                                <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                    <span class="ipnet-badge-dot"></span> INFORMASI CUSTOMER
                                </p>
                                <h3 class="text-[16px] font-bold text-[#292929]">Status Layanan Customer</h3>
                            </div>
                            <div class="flex items-center gap-2.5">
                                {{-- Switch View: Blok Grid (Gambar 2) / Tabel Detail --}}
                                <div class="inline-flex items-center bg-slate-100 p-0.5 rounded-lg border border-slate-200 text-xs">
                                    <button type="button" 
                                            @click="viewMode = 'grid'" 
                                            :class="viewMode === 'grid' ? 'bg-[#8F0A0D] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                            class="px-2.5 py-1 rounded-md transition flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                        <span>Blok Grid</span>
                                    </button>
                                    <button type="button" 
                                            @click="viewMode = 'table'" 
                                            :class="viewMode === 'table' ? 'bg-[#8F0A0D] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                                            class="px-2.5 py-1 rounded-md transition flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                        <span>Tabel Detail</span>
                                    </button>
                                </div>
                                <a href="{{ route('ms.projects.index') }}" class="text-xs font-bold text-[#8F0A0D] hover:underline shrink-0 ml-1" title="Lihat Seluruh Daftar Proyek Managed Service">Lihat Semua &rarr;</a>
                            </div>
                        </div>

                        {{-- TAMPILAN 1: BLOK GRID MATRIX MERAH (SESUAI GAMBAR 2 - RAPIH & PRESISI) --}}
                        <div x-show="viewMode === 'grid'" x-cloak>
                            <div class="mb-2 text-[11px] text-slate-500 flex items-center justify-between">
                                <span>Menampilkan <strong>{{ $clientHealthList->count() }}</strong> proyek aktif (klik kotak untuk detail):</span>
                                <span class="hidden sm:inline-block text-[10.5px] text-slate-400">Scroll untuk lainnya &darr;</span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-2.5 max-h-[300px] overflow-y-auto pr-1 py-0.5 custom-scrollbar">
                                @forelse($clientHealthList as $client)
                                <button type="button"
                                        @click="selectedProject = {{ json_encode($client) }}"
                                        title="{{ $client['projectName'] }} &bull; {{ $client['name'] }} &bull; Realisasi PM: {{ $client['pm_completed_count'] }}/{{ $client['pm_cycles_count'] }} ({{ $client['pm_progress_pct'] }}%)"
                                        class="group relative p-2.5 rounded-xl bg-gradient-to-br from-[#8F0A0D] to-[#75080A] hover:from-[#A80D11] hover:to-[#8F0A0D] text-white text-left flex flex-col justify-between shadow-2xs hover:shadow-md transition-all duration-200 border border-red-900/40 hover:border-red-600/50 cursor-pointer select-none">
                                    
                                    {{-- Baris Atas: Inisial Proyek & Status Dot --}}
                                    <div class="flex items-center justify-between gap-1.5 w-full mb-2">
                                        <span class="truncate font-bold text-[11.5px] text-white tracking-tight" title="{{ $client['projectName'] }}">
                                            {{ $client['projectInitial'] }}
                                        </span>
                                        <span class="w-2 h-2 rounded-full shrink-0 {{ $client['status_key'] === 'cm' ? 'bg-amber-400 ring-2 ring-white/30 animate-pulse' : ($client['status_key'] === 'pm' ? 'bg-sky-300 ring-2 ring-white/30' : 'bg-emerald-400 ring-2 ring-white/30') }}"
                                              title="Status Operasional: {{ $client['status_label'] }}"></span>
                                    </div>

                                    {{-- Baris Bawah: Micro Progress Bar & Angka PM --}}
                                    <div class="w-full">
                                        <div class="w-full bg-black/30 rounded-full h-1.5 overflow-hidden mb-1 ring-1 ring-white/10">
                                            <div class="h-full rounded-full transition-all duration-300 {{ $client['pm_progress_pct'] >= 100 ? 'bg-emerald-400' : 'bg-white/95' }}"
                                                 style="width: {{ min(100, $client['pm_progress_pct']) }}%;"></div>
                                        </div>
                                        <div class="flex items-center justify-between text-[10px] leading-tight">
                                            <span class="font-extrabold text-white/95">{{ $client['pm_progress_pct'] }}%</span>
                                            <span class="font-medium text-white/70 text-[9.5px]">{{ $client['pm_completed_count'] }}/{{ $client['pm_cycles_count'] }} PM</span>
                                        </div>
                                    </div>
                                </button>
                                @empty
                                <div class="col-span-full py-8 text-center text-slate-400 text-xs">
                                    Belum ada portofolio klien SLA terdaftar.
                                </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- TAMPILAN 2: TABEL LENGKAP --}}
                        <div x-show="viewMode === 'table'" x-cloak class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                        <th class="pb-2.5">Proyek &amp; Pelanggan</th>
                                        <th class="pb-2.5">Urgensi Tiket (Severity)</th>
                                        <th class="pb-2.5">Status Layanan</th>
                                        <th class="pb-2.5">Progress PM</th>
                                        <th class="pb-2.5">Realisasi SLA</th>
                                        <th class="pb-2.5">Keterangan</th>
                                        <th class="pb-2.5 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($clientHealthList as $client)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 pr-2">
                                            <a href="{{ route('ms.projects.show', $client['id']) }}#lokasi-site" class="group block" title="Buka Detail & Lokasi Operasional: {{ $client['projectName'] }}">
                                                <div class="font-extrabold text-slate-900 text-[12.5px] group-hover:text-[#8F0A0D] transition line-clamp-1 flex items-center gap-1.5" title="{{ $client['projectName'] }}">
                                                    <span>{{ $client['projectName'] }}</span>
                                                    <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 group-hover:translate-x-0.5 text-[#8F0A0D] transition-all shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </div>
                                                <div class="text-[11px] text-slate-500 font-medium group-hover:text-[#8F0A0D]/80 transition truncate max-w-[280px]">
                                                    {{ $client['name'] }} &bull; <span class="text-slate-600 font-semibold group-hover:text-[#8F0A0D]">Kontrak {{ $client['sla_tier'] }}</span>
                                                </div>
                                            </a>
                                        </td>
                                        <td class="py-3 px-2 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[10.5px] font-extrabold border {{ $client['severity_badge'] }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $client['severity_dot'] }}"></span>
                                                {{ $client['highest_severity'] }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-2 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10.5px] font-bold border {{ $client['badge_bg'] }}">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $client['dot_color'] }}"></span>
                                                {{ $client['status_label'] }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-2 whitespace-nowrap">
                                            <div class="w-28">
                                                <div class="w-full bg-slate-100 border border-slate-200 rounded-full h-1.5 overflow-hidden mb-1">
                                                    <div class="h-full rounded-full transition-all duration-300 {{ $client['pm_progress_pct'] >= 100 ? 'bg-emerald-500' : 'bg-[#8F0A0D]' }}"
                                                         style="width: {{ min(100, $client['pm_progress_pct']) }}%;"></div>
                                                </div>
                                                <div class="flex items-center justify-between text-[10px]">
                                                    <span class="font-extrabold text-slate-800">{{ $client['pm_progress_pct'] }}%</span>
                                                    <span class="text-slate-500 font-medium">{{ $client['pm_completed_count'] }}/{{ $client['pm_cycles_count'] }} PM</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-2 whitespace-nowrap">
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-mono font-extrabold text-[12px] {{ $client['sla_uptime'] >= $client['target_sla'] ? 'text-emerald-700' : 'text-[#8F0A0D]' }}">
                                                    {{ number_format($client['sla_uptime'], 1) }}%
                                                </span>
                                                <span class="text-[10px] text-slate-400">/ {{ number_format($client['target_sla'], 1) }}%</span>
                                            </div>
                                            <div class="text-[10.5px] text-slate-500 font-medium">
                                                {{ $client['downtime_hours'] > 0 ? $client['downtime_hours'] . ' Jam Down' : 'Zero Downtime' }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-2 text-[11.5px] text-slate-600 font-medium whitespace-nowrap">
                                            {{ $client['status_desc'] }}
                                        </td>
                                        <td class="py-3 pl-2 text-right whitespace-nowrap">
                                            <a href="{{ route('ms.projects.show', ['project' => $client['id'], 'tab' => 'sla']) }}" class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition inline-block">
                                                Lihat Perhitungan SLA &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-slate-400">Belum ada portofolio klien SLA terdaftar.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- POPUP MODAL DETAIL PROYEK SAAT BLOK DIKLIK (SESUAI GAMBAR 3) --}}
                    <div x-show="selectedProject !== null" 
                         x-cloak
                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs transition-opacity"
                         @click.self="selectedProject = null"
                         @keydown.escape.window="selectedProject = null">
                        
                        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full p-5 sm:p-6 transform transition-all anim-scale-up"
                             @click.stop>
                            {{-- Header Popup --}}
                            <div class="flex items-start justify-between gap-3 pb-3 border-b border-slate-100">
                                <div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[10.5px] font-bold bg-red-50 text-[#8F0A0D] border border-red-200 uppercase tracking-wider mb-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                        Detail Portofolio Proyek
                                    </span>
                                    <h4 class="text-[15px] sm:text-[16px] font-extrabold text-slate-900 leading-snug" x-text="selectedProject ? selectedProject.projectName : ''"></h4>
                                </div>
                                <button type="button" 
                                        @click="selectedProject = null" 
                                        class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition shrink-0 cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            {{-- Info Body --}}
                            <div class="py-4 space-y-3 text-xs" x-if="selectedProject">
                                <div class="grid grid-cols-2 gap-3 bg-slate-50/80 p-3.5 rounded-xl border border-slate-100">
                                    <div>
                                        <span class="text-[10.5px] uppercase font-bold text-slate-400 block mb-0.5">Pelanggan / Klien</span>
                                        <span class="font-extrabold text-slate-800 text-[12px]" x-text="selectedProject ? selectedProject.name : ''"></span>
                                        <span class="block text-[11px] text-slate-500" x-text="selectedProject ? 'Kontrak ' + selectedProject.sla_tier : ''"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10.5px] uppercase font-bold text-slate-400 block mb-0.5">Urgensi Tiket</span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-extrabold border"
                                              :class="selectedProject ? selectedProject.severity_badge : ''">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="selectedProject ? selectedProject.severity_dot : ''"></span>
                                            <span x-text="selectedProject ? selectedProject.highest_severity : ''"></span>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-[10.5px] uppercase font-bold text-slate-400 block mb-0.5">Status Layanan</span>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold border"
                                              :class="selectedProject ? selectedProject.badge_bg : ''">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="selectedProject ? selectedProject.dot_color : ''"></span>
                                            <span x-text="selectedProject ? selectedProject.status_label : ''"></span>
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-[10.5px] uppercase font-bold text-slate-400 block mb-0.5">Realisasi SLA</span>
                                        <span class="font-mono font-extrabold text-[13px] text-emerald-700" 
                                              x-text="selectedProject ? selectedProject.sla_uptime + '% / ' + selectedProject.target_sla + '%' : ''"></span>
                                        <span class="block text-[11px] text-slate-500" 
                                              x-text="selectedProject ? (selectedProject.downtime_hours > 0 ? selectedProject.downtime_hours + ' Jam Down' : 'Zero Downtime') : ''"></span>
                                    </div>
                                </div>

                                {{-- Progress Siklus Preventive Maintenance (PM) Sesuai Desain Rapi --}}
                                <div class="p-3.5 bg-slate-50/90 rounded-xl border border-slate-200/80 space-y-2">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-slate-700 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Progress Siklus PM (Preventive Maintenance)</span>
                                        </span>
                                        <span class="font-extrabold text-slate-900" x-text="selectedProject ? (selectedProject.pm_completed_count + '/' + selectedProject.pm_cycles_count + ' Siklus PM') : ''"></span>
                                    </div>
                                    <div class="w-full bg-[#E2E8F0] rounded-full h-2 overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-300"
                                             :style="'background: linear-gradient(90deg, #8F0A0D, #D62E3C); width: ' + (selectedProject ? selectedProject.pm_progress_pct : 0) + '%;'"></div>
                                    </div>
                                    <div class="flex items-center justify-between text-[11px]">
                                        <span class="font-extrabold text-[#8F0A0D] text-xs" x-text="selectedProject ? (selectedProject.pm_progress_pct + '% Realisasi') : ''"></span>
                                        <span class="text-slate-500 text-[10.5px]">Dari total target siklus kontrak</span>
                                    </div>
                                </div>

                                <div class="p-3 bg-amber-50/60 border border-amber-100 rounded-xl flex items-center gap-2.5 text-amber-900">
                                    <svg class="w-4 h-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <div class="text-[11.5px] leading-tight">
                                        <strong>Keterangan:</strong> <span x-text="selectedProject ? selectedProject.status_desc : ''"></span>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions Footer --}}
                            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                                <button type="button" 
                                        @click="selectedProject = null" 
                                        class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition cursor-pointer">
                                    Tutup
                                </button>
                                <a :href="selectedProject ? '/managed-service/projects/' + selectedProject.id + '?tab=sla' : '#'" 
                                   class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-[#8F0A0D] border border-red-200 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                                    <span>Perhitungan SLA</span>
                                </a>
                                <a :href="selectedProject ? '/managed-service/projects/' + selectedProject.id + '#lokasi-site' : '#'" 
                                   class="px-4 py-2 rounded-xl bg-gradient-to-r from-[#8F0A0D] to-[#A80D11] hover:from-[#A80D11] hover:to-[#B91C1C] text-white font-bold text-xs shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                                    <span>Buka Detail Proyek</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Customer Status Doughnut Chart (lg:col-span-1) --}}
                <div class="ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#E2E8F0]">
                            <div>
                                <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                    <span class="ipnet-badge-dot"></span> DISTRIBUSI LAYANAN
                                </p>
                                <h3 class="text-[16px] font-bold text-[#292929]">Status Layanan Klien</h3>
                            </div>
                        </div>
                        <div style="height: 260px;" class="flex items-center justify-center">
                            <canvas id="customerHealthChart"></canvas>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ======================================================== --}}
            {{-- 4. AKTIVITAS OPERASIONAL: TIKET INSIDEN & JADWAL PM      --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 anim-fade-up anim-delay-4">
                
                {{-- Recent Tickets (Corrective Maintenance) --}}
                <div class="ipnet-card overflow-hidden flex flex-col">
                    <div class="flex justify-between items-center gap-2 p-4 sm:p-5 pb-3 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 bg-[#8F0A0D] rounded-full inline-block"></span>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Tiket Insiden &amp; Layanan (CM)</h3>
                        </div>
                        <a href="{{ route('ms.tickets.index') }}" class="text-[#8F0A0D] text-[12px] font-bold hover:underline">Lihat semua</a>
                    </div>
                    <div class="p-2 divide-y divide-[#F1F5F9] flex-1">
                        @forelse($tickets->take(5) as $ticket)
                        @php
                            $ticketLink = $ticket->project_id 
                                ? route('ms.projects.show', ['project' => $ticket->project_id, 'tab' => 'cm']) 
                                : route('ms.tickets.index', ['search' => $ticket->ticket_number]);
                        @endphp
                        <a href="{{ $ticketLink }}" class="flex items-center justify-between gap-3 p-3 rounded-xl hover:bg-[#F8FAFC] transition-colors group" title="Buka Detail Tiket / Proyek">
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-bold text-[#1E293B] group-hover:text-[#8F0A0D] transition-colors truncate" title="{{ $ticket->title }}">
                                    <span class="font-mono text-[11px] text-[#8F0A0D] mr-1">{{ $ticket->ticket_number }}</span> {{ $ticket->title }}
                                </div>
                                <div class="text-[11.5px] text-[#64748B] truncate mt-0.5" title="{{ $ticket->client_name }}">
                                    {{ $ticket->client_name }} &bull; PIC: <strong class="text-[#8F0A0D]">{{ $ticket->assignedEngineer?->name ?: 'Belum Ditugaskan' }}</strong>
                                </div>
                            </div>
                            <div class="shrink-0 flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold 
                                    {{ $ticket->status === 'Resolved' || $ticket->status === 'Closed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $ticket->status }}
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-[#8F0A0D] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </a>
                        @empty
                        <div class="text-center py-8 text-[#64748B]">
                            <p class="text-[13px]">Belum ada tiket insiden</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent PM Schedules (Preventive Maintenance) --}}
                <div class="ipnet-card overflow-hidden flex flex-col">
                    <div class="flex justify-between items-center gap-2 p-4 sm:p-5 pb-3 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 bg-blue-600 rounded-full inline-block"></span>
                            <h3 class="text-[15px] font-bold text-[#1E293B]">Jadwal Maintenance Terdekat (PM)</h3>
                        </div>
                        <a href="{{ route('ms.maintenance.index') }}" class="text-[#8F0A0D] text-[12px] font-bold hover:underline">Lihat semua</a>
                    </div>
                    <div class="p-2 divide-y divide-[#F1F5F9] flex-1">
                        @forelse($upcomingPmSchedules->take(5) as $sch)
                        @php
                            $isToday = $sch->date && $sch->date->isToday();
                            $dateLabel = $sch->date ? $sch->date->format('d M Y') : ($sch->target_date ?? '-');
                            $cleanTitle = preg_replace('/^PM\s+PM\s+/i', 'PM ', $sch->title);
                            $schLink = $sch->project_id 
                                ? route('ms.projects.show', ['project' => $sch->project_id, 'tab' => 'pm']) 
                                : '#';
                        @endphp
                        <a href="{{ $schLink }}" class="flex items-center justify-between gap-3 p-3 rounded-xl hover:bg-[#F8FAFC] transition-colors group" title="Buka Detail Jadwal PM / Proyek: {{ $cleanTitle }}">
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-bold text-[#292929] group-hover:text-[#8F0A0D] transition-colors truncate" title="{{ $cleanTitle }}">
                                    {{ $cleanTitle }}
                                </div>
                                <div class="text-[11.5px] text-[#75727C] flex items-center gap-1.5 mt-0.5 min-w-0">
                                    <span class="inline-flex items-center gap-1 text-[11.5px] font-medium text-slate-600">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span>Target: <strong class="{{ $isToday ? 'text-[#8F0A0D]' : 'text-slate-800 font-bold' }}">{{ $dateLabel }}</strong></span>
                                    </span>
                                </div>
                            </div>
                            <div class="shrink-0 flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    Preventive
                                </span>
                                <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-[#8F0A0D] group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </a>
                        @empty
                        <div class="text-center py-8 text-[#75727C]">
                            <p class="text-[13px]">Belum ada target jadwal PM</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- =========================================================================
                 WORK SCHEDULES - VIEW MONTHLY (KALENDER BULANAN INTERAKTIF DASHBOARD)
                 ========================================================================= --}}
            <div class="ipnet-card overflow-hidden flex flex-col mt-6 shadow-sm border border-[#E2E8F0]" x-data="monthlyWorkSchedule()">
                {{-- Calendar Header Bar (Satu Baris Sejajar & Proporsional) --}}
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 p-5 pb-4 border-b border-[#E2E8F0] bg-white">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-4 bg-[#8F0A0D] rounded-full inline-block"></span>
                            <h3 class="text-[16px] font-bold text-[#1E293B]">Work Schedules (Monthly View)</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-[#8F0A0D]/10 text-[#8F0A0D] border border-[#8F0A0D]/20">
                                Agenda Bulanan
                            </span>
                        </div>
                        <p class="text-[12px] text-[#64748B] mt-0.5 pl-4">
                            Monitoring terintegrasi jadwal kerja Field Engineer, siklus Preventive Maintenance (PM), dan penanganan insiden (CM).
                        </p>
                    </div>

                    {{-- Navigation & Actions (Satu Baris Rapi di Kanan) --}}
                    <div class="flex items-center gap-2.5 self-start lg:self-center">
                        {{-- Month Navigator --}}
                        <div class="flex items-center bg-[#F8FAFC] p-1 rounded-xl border border-slate-200 shadow-2xs">
                            <button type="button" @click="prevMonth()" class="p-1.5 rounded-lg hover:bg-white text-slate-600 hover:text-slate-900 transition cursor-pointer" title="Bulan Sebelumnya">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <span class="px-3 text-[13px] font-bold text-slate-800 min-w-[135px] text-center" x-text="monthYearLabel"></span>
                            <button type="button" @click="nextMonth()" class="p-1.5 rounded-lg hover:bg-white text-slate-600 hover:text-slate-900 transition cursor-pointer" title="Bulan Berikutnya">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>

                        {{-- Tombol Hari Ini --}}
                        <button type="button" @click="goToToday()" class="px-3 py-2 text-xs font-bold text-slate-700 hover:text-[#8F0A0D] bg-[#F8FAFC] hover:bg-red-50 border border-slate-200 hover:border-[#8F0A0D]/30 rounded-xl transition shadow-2xs cursor-pointer">
                            Hari Ini
                        </button>

                        {{-- Tombol ke Full Schedule --}}
                        <a href="{{ route('schedules.index') }}" class="px-4 py-2 text-xs font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                            <span>Buka Work Schedule</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Toolbar Filter & Legend Terpadu (Menyatu, Tidak Redundan) --}}
                <div class="px-5 py-2.5 bg-[#F8FAFC] border-b border-[#E2E8F0] flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex items-center flex-wrap gap-2">
                        <span class="text-slate-500 font-semibold text-[11px] uppercase tracking-wider mr-1">Filter &amp; Kategori:</span>
                        
                        {{-- Filter Semua --}}
                        <button type="button" @click="filterType = 'all'" 
                                :class="filterType === 'all' ? 'bg-[#8F0A0D] text-white shadow-2xs font-bold' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 font-medium'"
                                class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center gap-1.5">
                            <span>Semua</span>
                            <span class="opacity-80 font-bold" x-text="'(' + countEvents('all') + ')'"></span>
                        </button>

                        {{-- Filter Field Schedule (Hijau) --}}
                        <button type="button" @click="filterType = 'schedule'" 
                                :class="filterType === 'schedule' ? 'bg-emerald-600 text-white shadow-2xs font-bold' : 'bg-white text-slate-700 hover:bg-emerald-50/60 border border-slate-200 font-medium'"
                                class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Field Schedules</span>
                            <span class="opacity-80 font-bold" x-text="'(' + countEvents('schedule') + ')'"></span>
                        </button>

                        {{-- Filter Preventive (Biru) --}}
                        <button type="button" @click="filterType = 'pm'" 
                                :class="filterType === 'pm' ? 'bg-blue-600 text-white shadow-2xs font-bold' : 'bg-white text-slate-700 hover:bg-blue-50/60 border border-slate-200 font-medium'"
                                class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span>Preventive Maintenance</span>
                            <span class="opacity-80 font-bold" x-text="'(' + countEvents('pm') + ')'"></span>
                        </button>

                        {{-- Filter Tiket CM (Rose/Merah) --}}
                        <button type="button" @click="filterType = 'cm'" 
                                :class="filterType === 'cm' ? 'bg-rose-600 text-white shadow-2xs font-bold' : 'bg-white text-slate-700 hover:bg-rose-50/60 border border-slate-200 font-medium'"
                                class="px-3 py-1.5 rounded-lg text-xs transition cursor-pointer flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            <span>Tiket CM</span>
                            <span class="opacity-80 font-bold" x-text="'(' + countEvents('cm') + ')'"></span>
                        </button>
                    </div>

                    <div class="text-[11.5px] text-slate-500 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Klik pada kotak tanggal untuk melihat rincian agenda harian.</span>
                    </div>
                </div>

                {{-- Monthly Calendar Grid (Layout Alami Terstruktur Dari Atas) --}}
                <div class="p-4 sm:p-5 bg-white">
                    {{-- Weekday Header --}}
                    <div class="grid grid-cols-7 gap-2 mb-2 text-center text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                        <div class="py-2 bg-slate-50 rounded-lg border border-slate-100">Senin</div>
                        <div class="py-2 bg-slate-50 rounded-lg border border-slate-100">Selasa</div>
                        <div class="py-2 bg-slate-50 rounded-lg border border-slate-100">Rabu</div>
                        <div class="py-2 bg-slate-50 rounded-lg border border-slate-100">Kamis</div>
                        <div class="py-2 bg-slate-50 rounded-lg border border-slate-100">Jumat</div>
                        <div class="py-2 bg-slate-50 rounded-lg border border-slate-100">Sabtu</div>
                        <div class="py-2 bg-slate-50 rounded-lg border border-slate-100">Minggu</div>
                    </div>

                    {{-- Days Grid (35-42 boxes) --}}
                    <div class="grid grid-cols-7 gap-2">
                        <template x-for="(cell, index) in calendarDays" :key="index">
                            <div class="min-h-[115px] p-2 rounded-xl border transition-all flex flex-col justify-start cursor-pointer"
                                 :class="{
                                     'bg-slate-50/40 border-slate-100/90 text-slate-400': !cell.isCurrentMonth,
                                     'bg-white border-slate-200/90 hover:border-[#8F0A0D]/50 hover:bg-slate-50/30 hover:shadow-2xs': cell.isCurrentMonth && !cell.isToday,
                                     'bg-red-50/20 border-[#8F0A0D]/60 ring-1 ring-[#8F0A0D]/20 shadow-xs': cell.isToday
                                 }"
                                 @click="openDayModal(cell)">
                                {{-- Date Number Header (Pojok Kanan Atas) --}}
                                <div class="flex items-center justify-end mb-1.5 shrink-0">
                                    <span class="inline-flex items-center justify-center text-[11.5px] font-bold w-6 h-6 rounded-full transition"
                                          :class="{
                                              'bg-[#8F0A0D] text-white shadow-xs': cell.isToday,
                                              'text-slate-800 font-extrabold': cell.isCurrentMonth && !cell.isToday,
                                              'text-slate-300 font-normal': !cell.isCurrentMonth
                                          }"
                                          x-text="cell.date">
                                    </span>
                                </div>

                                {{-- Events Stack (Menempel Langsung di Bawah Tanggal Secara Alami) --}}
                                <div class="space-y-1 w-full flex-1">
                                    <template x-for="(evt, evtIdx) in cell.events.slice(0, 3)" :key="evt.id">
                                        <div class="px-2 py-1 rounded-md text-[11px] font-semibold truncate flex items-center gap-1.5 transition hover:brightness-95 shadow-2xs border"
                                             :class="{
                                                 'bg-emerald-50 text-emerald-900 border-emerald-200': evt.type === 'schedule',
                                                 'bg-blue-50 text-blue-900 border-blue-200': evt.type === 'pm',
                                                 'bg-rose-50 text-rose-900 border-rose-200': evt.type === 'cm'
                                             }"
                                             :title="evt.title + ' (' + evt.time + ')'"
                                             @click.stop="openEventModal(evt, cell)">
                                            <span class="w-1.5 h-1.5 rounded-full shrink-0"
                                                  :class="{
                                                      'bg-emerald-500': evt.type === 'schedule',
                                                      'bg-blue-500': evt.type === 'pm',
                                                      'bg-rose-500': evt.type === 'cm'
                                                  }"></span>
                                            <span class="truncate" x-text="evt.title"></span>
                                        </div>
                                    </template>

                                    {{-- Overflow Indicator (+X agenda lainnya) --}}
                                    <div x-show="cell.events.length > 3" 
                                         class="text-[10px] font-bold text-slate-500 hover:text-[#8F0A0D] px-1 py-0.5 rounded text-left">
                                        +<span x-text="cell.events.length - 3"></span> agenda lainnya
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Modal Detail Agenda Hari Terpilih --}}
                <div x-show="isDayModalOpen" 
                     x-cloak 
                     class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden" @click.away="isDayModalOpen = false">
                        <div class="p-5 pb-3 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                            <div>
                                <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                    <span>Work Schedules Detail</span>
                                </p>
                                <h4 class="text-[16px] font-bold text-slate-900 mt-0.5" x-text="selectedDayLabel"></h4>
                            </div>
                            <button @click="isDayModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-200 transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="p-5 max-h-[60vh] overflow-y-auto space-y-3">
                            <template x-if="selectedDayEvents.length === 0">
                                <div class="text-center py-8 text-slate-400">
                                    <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <p class="text-xs font-semibold">Tidak ada jadwal agenda atau penugasan pada tanggal ini.</p>
                                </div>
                            </template>

                            <template x-for="item in selectedDayEvents" :key="item.id">
                                <div class="p-3.5 rounded-xl border transition-all"
                                     :class="{
                                         'bg-emerald-50/50 border-emerald-200': item.type === 'schedule',
                                         'bg-blue-50/50 border-blue-200': item.type === 'pm',
                                         'bg-rose-50/50 border-rose-200': item.type === 'cm'
                                     }">
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider mb-1"
                                                  :class="{
                                                      'bg-emerald-100 text-emerald-800': item.type === 'schedule',
                                                      'bg-blue-100 text-blue-800': item.type === 'pm',
                                                      'bg-rose-100 text-rose-800': item.type === 'cm'
                                                  }"
                                                  x-text="item.badge">
                                            </span>
                                            <h5 class="text-[13.5px] font-bold text-slate-900 leading-snug" x-text="item.title"></h5>
                                        </div>
                                        <span class="text-[11px] font-mono font-bold text-slate-600 shrink-0 bg-white px-2 py-0.5 rounded border border-slate-200" x-text="item.time"></span>
                                    </div>

                                    <div class="mt-2.5 pt-2 border-t border-slate-200/60 grid grid-cols-2 gap-2 text-[11.5px] text-slate-600">
                                        <div>
                                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Klien / Proyek</span>
                                            <span class="font-semibold text-slate-800 truncate block" x-text="item.client + ' &bull; ' + item.project"></span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Teknisi / PIC</span>
                                            <span class="font-bold text-[#8F0A0D] truncate block" x-text="item.engineer"></span>
                                        </div>
                                        <div class="col-span-2 flex items-center justify-between pt-1">
                                            <span class="text-slate-500 truncate" x-text="'Lokasi: ' + item.location"></span>
                                            <a :href="item.link" class="text-[#8F0A0D] font-bold hover:underline flex items-center gap-1 text-[11px]">
                                                <span>Buka Detail</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                            <button type="button" @click="isDayModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-800 text-white font-bold text-xs hover:bg-slate-900 transition cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

{{-- MODAL CREATE TICKET (STANDAR HELPDESK & SEVERITY IP-NET SESUAI GAMBAR 2) --}}
@include('managed_service.partials.modal_ticket_create')

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status Layanan Customer Doughnut Chart (Identik dengan Lead Engineer)
        const ctxHealth = document.getElementById('customerHealthChart');
        if (ctxHealth) {
            const statusData = @json($customerStatusData ?? []);
            new Chart(ctxHealth.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: statusData.map(d => d.name),
                    datasets: [{
                        data: statusData.map(d => d.value),
                        backgroundColor: statusData.map(d => d.color),
                        borderWidth: 2,
                        borderColor: '#FFFFFF',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                font: {
                                    size: 11,
                                    family: "'Inter', sans-serif"
                                },
                                color: '#4B5563',
                                padding: 14
                            }
                        }
                    }
                }
            });
        }
    });

    function msDashboard() {
        return {
            isCreateModalOpen: false,
        };
    }

    function monthlyWorkSchedule() {
        return {
            currentDate: new Date(),
            filterType: 'all',
            isDayModalOpen: false,
            selectedDayLabel: '',
            selectedDayEvents: [],
            events: @json($calendarEvents ?? []),
            monthNames: [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ],

            get currentYear() {
                return this.currentDate.getFullYear();
            },

            get currentMonth() {
                return this.currentDate.getMonth();
            },

            get monthYearLabel() {
                return this.monthNames[this.currentMonth] + ' ' + this.currentYear;
            },

            prevMonth() {
                this.currentDate = new Date(this.currentYear, this.currentMonth - 1, 1);
            },

            nextMonth() {
                this.currentDate = new Date(this.currentYear, this.currentMonth + 1, 1);
            },

            goToToday() {
                this.currentDate = new Date();
            },

            get deduplicatedEvents() {
                const seen = new Set();
                return this.events.filter(e => {
                    const normTitle = (e.title || '').trim().toLowerCase().replace(/^(pm\s+)+/i, 'pm ');
                    const key = normTitle + '|' + e.date;
                    if (seen.has(key)) return false;
                    seen.add(key);
                    return true;
                });
            },

            countEvents(type) {
                if (type === 'all') return this.deduplicatedEvents.length;
                return this.deduplicatedEvents.filter(e => e.type === type).length;
            },

            get filteredEvents() {
                if (this.filterType === 'all') return this.deduplicatedEvents;
                return this.deduplicatedEvents.filter(e => e.type === this.filterType);
            },

            get calendarDays() {
                const year = this.currentYear;
                const month = this.currentMonth;
                const firstDayOfMonth = new Date(year, month, 1);
                const lastDayOfMonth = new Date(year, month + 1, 0);
                const daysInMonth = lastDayOfMonth.getDate();

                // Monday = 0, Tuesday = 1, ..., Sunday = 6
                let startDay = (firstDayOfMonth.getDay() + 6) % 7;

                const days = [];
                const today = new Date();
                const todayStr = today.getFullYear() + '-' + 
                    String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                    String(today.getDate()).padStart(2, '0');

                // Days from previous month
                const prevMonthLastDate = new Date(year, month, 0).getDate();
                for (let i = startDay - 1; i >= 0; i--) {
                    const prevDate = prevMonthLastDate - i;
                    const prevMonthObj = new Date(year, month - 1, prevDate);
                    const dateStr = prevMonthObj.getFullYear() + '-' + 
                        String(prevMonthObj.getMonth() + 1).padStart(2, '0') + '-' + 
                        String(prevDate).padStart(2, '0');
                    const dayEvents = this.filteredEvents.filter(e => e.date === dateStr);

                    days.push({
                        date: prevDate,
                        fullDate: dateStr,
                        isCurrentMonth: false,
                        isToday: dateStr === todayStr,
                        isWeekend: prevMonthObj.getDay() === 0 || prevMonthObj.getDay() === 6,
                        dayName: prevMonthObj.getDay() === 0 ? 'Min' : (prevMonthObj.getDay() === 6 ? 'Sab' : ''),
                        events: dayEvents
                    });
                }

                // Days of current month
                for (let d = 1; d <= daysInMonth; d++) {
                    const dateObj = new Date(year, month, d);
                    const dateStr = year + '-' + 
                        String(month + 1).padStart(2, '0') + '-' + 
                        String(d).padStart(2, '0');
                    const dayEvents = this.filteredEvents.filter(e => e.date === dateStr);

                    days.push({
                        date: d,
                        fullDate: dateStr,
                        isCurrentMonth: true,
                        isToday: dateStr === todayStr,
                        isWeekend: dateObj.getDay() === 0 || dateObj.getDay() === 6,
                        dayName: dateObj.getDay() === 0 ? 'Min' : (dateObj.getDay() === 6 ? 'Sab' : ''),
                        events: dayEvents
                    });
                }

                // Days of next month to fill complete weeks (35 or 42 cells)
                const totalCells = days.length <= 35 ? 35 : 42;
                const remaining = totalCells - days.length;
                for (let n = 1; n <= remaining; n++) {
                    const nextMonthObj = new Date(year, month + 1, n);
                    const dateStr = nextMonthObj.getFullYear() + '-' + 
                        String(nextMonthObj.getMonth() + 1).padStart(2, '0') + '-' + 
                        String(n).padStart(2, '0');
                    const dayEvents = this.filteredEvents.filter(e => e.date === dateStr);

                    days.push({
                        date: n,
                        fullDate: dateStr,
                        isCurrentMonth: false,
                        isToday: dateStr === todayStr,
                        isWeekend: nextMonthObj.getDay() === 0 || nextMonthObj.getDay() === 6,
                        dayName: nextMonthObj.getDay() === 0 ? 'Min' : (nextMonthObj.getDay() === 6 ? 'Sab' : ''),
                        events: dayEvents
                    });
                }

                return days;
            },

            openDayModal(cell) {
                const parts = cell.fullDate.split('-');
                const dayNum = parseInt(parts[2], 10);
                const monthNum = parseInt(parts[1], 10) - 1;
                const yearNum = parseInt(parts[0], 10);
                this.selectedDayLabel = dayNum + ' ' + this.monthNames[monthNum] + ' ' + yearNum;
                this.selectedDayEvents = cell.events;
                this.isDayModalOpen = true;
            },

            openEventModal(evt, cell) {
                this.openDayModal(cell);
            }
        };
    }
</script>
@endpush

