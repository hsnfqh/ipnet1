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
                            PT IP NETWORK SOLUSINDO &bull; MANAGED SERVICE & SLA
                        </div>

                        <h1 class="text-[18px] sm:text-[21px] font-extrabold text-white tracking-tight leading-tight">
                            Selamat Datang, {{ auth()->user()->name }}
                        </h1>
                        <p class="mt-0.5 text-[12.5px] text-white/80 leading-relaxed line-clamp-1">
                            Pantau kepatuhan SLA operasional, antrean tiket gangguan, status kesehatan aset CI, dan jadwal preventive maintenance.
                        </p>
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="flex items-center gap-2.5 shrink-0">
                        <a href="{{ route('ms.tickets.index') }}" class="px-3.5 py-2 rounded-xl bg-white text-[#8F0A0D] font-bold text-[12.5px] hover:bg-[#FFF7F6] hover:scale-[1.02] transition-all shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Daftar Tiket SLA</span>
                        </a>
                        <a href="{{ route('ms.maintenance.index') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 hover:scale-[1.02] border border-white/25 text-white font-bold text-[12.5px] transition-all flex items-center gap-1.5 backdrop-blur-sm">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Jadwal Maintenance</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 2. METRIC SUMMARY CARDS (6 METRIC CARDS)                 --}}
            {{-- ======================================================== --}}
            <div class="anim-fade-up anim-delay-1">
                <div class="flex items-center justify-between mb-3.5">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> RINGKASAN OPERASIONAL
                        </p>
                        <h2 class="text-[18px] font-bold text-[#292929] tracking-tight">Status & Distribusi Penugasan SLA</h2>
                    </div>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
                    
                    {{-- Metric 1: Klien Kontrak SLA --}}
                    <div class="ipnet-metric-card group block anim-fade-up anim-delay-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Kontrak SLA</span>
                            <div class="w-7 h-7 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[24px] font-extrabold text-purple-700 tracking-tight">{{ $operateProjects->count() }}</div>
                        <p class="text-[11px] text-[#75727C] mt-0.5">Klien Maintenance</p>
                    </div>

                    {{-- Metric 2: Jadwal PM Bulan Ini --}}
                    <a href="{{ route('ms.maintenance.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-2">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Agenda PM</span>
                            <div class="w-7 h-7 rounded-lg bg-blue-500/10 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[24px] font-extrabold text-blue-600 tracking-tight">{{ $totalPmSchedulesCount ?? 0 }}</div>
                        <p class="text-[11px] text-[#75727C] mt-0.5">Kunjungan bulan ini</p>
                    </a>

                    {{-- Metric 3: Total Tiket Aktif --}}
                    <a href="{{ route('ms.tickets.index') }}" class="ipnet-metric-card group block anim-fade-up anim-delay-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Tiket Aktif</span>
                            <div class="w-7 h-7 rounded-lg {{ $openTickets > 0 ? 'bg-rose-500/10 text-rose-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center group-hover:bg-[#8F0A0D] group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[24px] font-extrabold text-[#292929] tracking-tight">{{ $openTickets }}</div>
                        <p class="text-[11px] font-semibold mt-0.5 {{ $criticalTickets > 0 ? 'text-rose-600' : 'text-[#75727C]' }}">
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
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Tiket Selesai</span>
                            <div class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[24px] font-extrabold text-emerald-600 tracking-tight">{{ $resolvedTickets }}</div>
                        <p class="text-[11px] text-emerald-700 font-semibold mt-0.5">Bulan berjalan</p>
                    </a>

                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 2.5 INCOMING SERVICE HANDOVERS (DARI SALES & PMO GATE 4)  --}}
            {{-- ======================================================== --}}
            @if(isset($incomingHandovers) && $incomingHandovers->count() > 0)
                <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-2">
                    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center font-bold shrink-0 shadow-xs">
                                <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-[16px] font-bold text-gray-900">Serah Terima Kontrak Layanan Masuk (Incoming Service Handovers)</h3>
                                    @if(($pendingHandoversCount ?? 0) > 0)
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                            {{ $pendingHandoversCount }} Menunggu Aktivasi
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[12px] text-gray-500 mt-0.5">
                                    Kontrak Managed Service dari Sales (Tipe 2) dan pelimpahan pasca-BAST dari PMO Delivery (Gate 4)
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-[10.5px] font-bold">
                                <tr>
                                    <th class="py-3 px-4">Proyek &amp; Klien</th>
                                    <th class="py-3 px-4">Asal Serah Terima</th>
                                    <th class="py-3 px-4">SLA Tier &amp; Coverage</th>
                                    <th class="py-3 px-4">Jadwal PM &amp; Periode</th>
                                    <th class="py-3 px-4 text-center">Status Onboarding</th>
                                    <th class="py-3 px-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                                @foreach($incomingHandovers as $ho)
                                    @php
                                        $isFromSales = ($ho->handover_target === 'managed_service' && $ho->sales_stage === 'Closed Won');
                                        $isAccepted = ($ho->ms_handover_status === 'Accepted');
                                    @endphp
                                    <tr class="hover:bg-gray-50/80 transition-colors {{ !$isAccepted ? 'bg-amber-50/20' : '' }}">
                                        {{-- Proyek & Klien --}}
                                        <td class="py-3.5 px-4">
                                            <a href="{{ route('projects.show', $ho->id) }}" class="font-bold text-gray-900 hover:text-[#8F0A0D] transition max-w-sm block hover:underline">
                                                {{ $ho->name }}
                                            </a>
                                            <div class="text-[11px] text-gray-400 font-medium mt-0.5">{{ $ho->client }}</div>
                                        </td>

                                        {{-- Asal Handover --}}
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            @if($isFromSales)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                                    <span>Sales Direct Handover</span>
                                                </span>
                                                <div class="text-[10px] text-gray-400 mt-0.5">PIC: {{ $ho->sales_name ?: 'Sales' }}</div>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                                    <span>PMO Delivery (Gate 4)</span>
                                                </span>
                                                <div class="text-[10px] text-gray-400 mt-0.5">PM: {{ $ho->pm ? $ho->pm->name : 'PMO' }}</div>
                                            @endif
                                        </td>

                                        {{-- SLA Tier & Coverage --}}
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                                <span>{{ $ho->sla_tier ?: 'Gold' }} Tier</span>
                                            </span>
                                            <div class="text-[10.5px] text-gray-500 font-medium mt-0.5">
                                                Support: {{ $ho->sla_coverage_hours ?: '24x7' }}
                                            </div>
                                        </td>

                                        {{-- Jadwal & Periode --}}
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <div class="font-semibold text-gray-800">{{ $ho->maintenance_frequency ?: 'Bulanan (Monthly)' }}</div>
                                            <div class="text-[11px] text-gray-400 mt-0.5">
                                                {{ $ho->service_start_date ? \Carbon\Carbon::parse($ho->service_start_date)->format('d M Y') : 'Mulai: ' . date('d M Y') }}
                                                @if($ho->service_end_date)
                                                    &ndash; {{ \Carbon\Carbon::parse($ho->service_end_date)->format('d M Y') }}
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Status Onboarding --}}
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if($isAccepted)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span>Layanan Aktif (Operate)</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span>Menunggu Aktivasi</span>
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Aksi --}}
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                @php
                                                    $formattedValue = $ho->contract_value ? 'Rp ' . number_format($ho->contract_value, 0, ',', '.') : '—';
                                                    $startDateVal = $ho->service_start_date ? \Carbon\Carbon::parse($ho->service_start_date)->format('Y-m-d') : date('Y-m-d');
                                                    $endDateVal = $ho->service_end_date ? \Carbon\Carbon::parse($ho->service_end_date)->format('Y-m-d') : '';
                                                @endphp
                                                @if(!$isAccepted)
                                                    <button type="button" 
                                                            data-id="{{ $ho->id }}"
                                                            data-name="{{ $ho->name }}"
                                                            data-client="{{ $ho->client ?: '-' }}"
                                                            data-value="{{ $formattedValue }}"
                                                            data-tier="{{ $ho->sla_tier ?: 'Gold' }}"
                                                            data-coverage="{{ $ho->sla_coverage_hours ?: '24x7' }}"
                                                            data-freq="{{ $ho->maintenance_frequency ?: 'Monthly' }}"
                                                            data-start="{{ $startDateVal }}"
                                                            data-end="{{ $endDateVal }}"
                                                            data-notes="{{ $ho->special_notes ?: '' }}"
                                                            onclick="window.triggerAcceptModal(this)"
                                                            class="px-2.5 py-1 bg-[#8F0A0D] hover:bg-[#73080A] text-white text-[11px] font-bold rounded-lg shadow-xs transition cursor-pointer flex items-center gap-1 active:scale-95">
                                                        <span>Terima &amp; Aktivasi</span>
                                                    </button>
                                                @else
                                                    <button type="button" 
                                                            data-id="{{ $ho->id }}"
                                                            data-name="{{ $ho->name }}"
                                                            data-client="{{ $ho->client ?: '-' }}"
                                                            data-value="{{ $formattedValue }}"
                                                            data-tier="{{ $ho->sla_tier ?: 'Gold' }}"
                                                            data-coverage="{{ $ho->sla_coverage_hours ?: '24x7' }}"
                                                            data-freq="{{ $ho->maintenance_frequency ?: 'Monthly' }}"
                                                            data-start="{{ $startDateVal }}"
                                                            data-end="{{ $endDateVal }}"
                                                            data-notes="{{ $ho->special_notes ?: '' }}"
                                                            onclick="window.triggerAcceptModal(this)"
                                                            class="px-2.5 py-1 bg-white hover:bg-gray-50 text-[#8F0A0D] text-[11px] font-bold rounded-lg border border-red-200 shadow-xs transition cursor-pointer active:scale-95">
                                                        <span>Edit SLA</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- ======================================================== --}}
            {{-- 3. STATUS KESEHATAN & OPERASIONAL PELANGGAN (HEALTH STATUS) --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 anim-fade-up anim-delay-3">
                
                {{-- Left: Customer Operational Status List (7 Cols) --}}
                <div class="lg:col-span-7 ipnet-card flex flex-col justify-between overflow-hidden">
                    <div>
                        <div class="p-5 sm:p-6 pb-4 border-b border-[#E2E8F0] flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                    <span class="ipnet-badge-dot"></span> STATUS PELANGGAN
                                </p>
                                <h3 class="text-[17px] font-bold text-[#1E293B] tracking-tight">Kesehatan Operasional Klien SLA</h3>
                                <p class="text-[12.5px] text-[#75727C] mt-0.5">Monitoring kondisi layanan real-time per pelanggan aktif</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg text-[11.5px] font-bold bg-[#F8FAFC] text-[#64748B] border border-[#E2E8F0]">
                                    {{ $clientHealthList->count() }} Klien Terdaftar
                                </span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-[13px]">
                                <thead>
                                    <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[#64748B] text-[11px] uppercase tracking-wider">
                                        <th class="py-3 px-5 font-bold">Pelanggan / Proyek</th>
                                        <th class="py-3 px-4 font-bold text-center">Tier SLA</th>
                                        <th class="py-3 px-4 font-bold text-center">Status Layanan</th>
                                        <th class="py-3 px-5 font-bold text-right">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#F1F5F9]">
                                    @forelse($clientHealthList as $client)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3.5 px-5">
                                            <div class="font-bold text-[#1E293B] leading-snug">{{ $client['name'] }}</div>
                                            <div class="text-[11.5px] text-[#64748B] truncate max-w-[220px]">{{ $client['projectName'] }}</div>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            @php
                                                $tier = $client['sla_tier'];
                                                $tierStyle = match($tier) {
                                                    'Platinum' => 'bg-purple-50 text-purple-700 border-purple-200',
                                                    'Gold' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                    'Silver' => 'bg-slate-100 text-slate-700 border-slate-300',
                                                    default => 'bg-orange-50 text-orange-700 border-orange-200',
                                                };
                                            @endphp
                                            <span class="px-2.5 py-0.5 rounded-full text-[10.5px] font-bold border {{ $tierStyle }}">
                                                {{ $tier }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-center">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $client['badge_bg'] }}">
                                                <span class="w-2 h-2 rounded-full {{ $client['dot_color'] }}"></span>
                                                {{ $client['status_label'] }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-5 text-right font-medium text-[12px] text-[#64748B]">
                                            {{ $client['status_desc'] }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-10 text-[#64748B]">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <svg class="w-9 h-9 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                <span class="text-[13px] font-medium">Belum ada kontrak klien Managed Service aktif</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Right: Customer Status Pie Chart (5 Cols) --}}
                <div class="lg:col-span-5 ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="mb-4 pb-3 border-b border-[#E2E8F0] flex items-center justify-between">
                            <div>
                                <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                    <span class="ipnet-badge-dot"></span> DISTRIBUSI KESEHATAN
                                </p>
                                <h3 class="text-[17px] font-bold text-[#1E293B] tracking-tight">Status Layanan Customer</h3>
                            </div>
                            <span class="text-[11px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-md">
                                Realtime
                            </span>
                        </div>

                        {{-- Pie / Donut Chart --}}
                        <div class="relative flex items-center justify-center my-2" style="height: 200px;">
                            <canvas id="customerHealthChart"></canvas>
                        </div>
                    </div>

                    {{-- Legend & Detailed Counts --}}
                    <div class="mt-4 pt-3 border-t border-[#F1F5F9] space-y-2">
                        <div class="grid grid-cols-3 gap-2 text-center">
                            {{-- Aman --}}
                            <div class="p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-100">
                                <div class="flex items-center justify-center gap-1.5 text-emerald-700 text-[11px] font-bold uppercase tracking-wider">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Aman
                                </div>
                                <div class="text-[18px] font-extrabold text-emerald-800 mt-0.5">{{ $clientHealthChart['count_aman'] }}</div>
                                <div class="text-[10px] text-emerald-600 font-medium">Stabil / Normal</div>
                            </div>

                            {{-- PM --}}
                            <div class="p-2.5 rounded-xl bg-blue-50/70 border border-blue-100">
                                <div class="flex items-center justify-center gap-1.5 text-blue-700 text-[11px] font-bold uppercase tracking-wider">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Siklus PM
                                </div>
                                <div class="text-[18px] font-extrabold text-blue-800 mt-0.5">{{ $clientHealthChart['count_pm'] }}</div>
                                <div class="text-[10px] text-blue-600 font-medium">Perawatan Rutin</div>
                            </div>

                            {{-- CM --}}
                            <div class="p-2.5 rounded-xl bg-rose-50/70 border border-rose-100">
                                <div class="flex items-center justify-center gap-1.5 text-rose-700 text-[11px] font-bold uppercase tracking-wider">
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> Insiden (CM)
                                </div>
                                <div class="text-[18px] font-extrabold text-rose-800 mt-0.5">{{ $clientHealthChart['count_cm'] }}</div>
                                <div class="text-[10px] text-rose-600 font-medium">Penanganan Tiket</div>
                            </div>
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
                        <div class="flex items-center justify-between gap-3 p-3 rounded-xl hover:bg-[#F8FAFC] transition-colors">
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-bold text-[#1E293B] truncate" title="{{ $ticket->title }}">
                                    <span class="font-mono text-[11px] text-[#8F0A0D] mr-1">{{ $ticket->ticket_number }}</span> {{ $ticket->title }}
                                </div>
                                <div class="text-[11.5px] text-[#64748B] truncate mt-0.5" title="{{ $ticket->client_name }}">
                                    {{ $ticket->client_name }} &bull; PIC: <strong class="text-[#8F0A0D]">{{ $ticket->assignedEngineer?->name ?: 'Belum Ditugaskan' }}</strong>
                                </div>
                            </div>
                            <div class="shrink-0">
                                <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold 
                                    {{ $ticket->status === 'Resolved' || $ticket->status === 'Closed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                    {{ $ticket->status }}
                                </span>
                            </div>
                        </div>
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
                            $dateLabel = $sch->date ? ($isToday ? 'Hari ini' : $sch->date->format('d M')) : '-';
                            $timeLabel = $sch->start_time ? substr($sch->start_time, 0, 5) . ' WIB' : '';
                        @endphp
                        <div class="flex items-center justify-between gap-3 p-3 rounded-xl hover:bg-[#F8FAFC] transition-colors">
                            <div class="min-w-0 flex-1">
                                <div class="text-[13px] font-bold text-[#292929] truncate" title="{{ $sch->title }}">
                                    {{ $sch->title }}
                                </div>
                                <div class="text-[11.5px] text-[#75727C] flex items-center gap-1.5 mt-0.5 min-w-0">
                                    <span class="shrink-0 whitespace-nowrap {{ $isToday ? 'text-[#8F0A0D] font-bold' : 'font-medium' }}">
                                        {{ $dateLabel }}{{ $timeLabel ? ', ' . $timeLabel : '' }}
                                    </span>
                                    <span class="text-gray-400 shrink-0">&bull;</span>
                                    <span class="truncate text-[#8F0A0D] font-semibold">
                                        {{ ($sch->engineers && $sch->engineers->isNotEmpty()) ? $sch->engineers->pluck('name')->implode(', ') : ($sch->engineer?->name ?: 'Doris / Tim Maintenance') }}
                                    </span>
                                </div>
                            </div>
                            <div class="shrink-0">
                                <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    Preventive
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8 text-[#75727C]">
                            <p class="text-[13px]">Belum ada agenda kunjungan</p>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- MODAL TERIMA & AKTIVASI LAYANAN MANAGED SERVICE --}}
    <div id="modal-accept-handover" 
         class="fixed inset-0 z-[99999] bg-[#0F172A]/60 backdrop-blur-xs items-center justify-center p-3 sm:p-5"
         style="display: none;"
         onclick="if(event.target === this) window.closeAcceptModal()">
        <div class="bg-white rounded-2xl w-[680px] max-w-full max-h-[92vh] flex flex-col overflow-hidden shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up">
            
            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                <div>
                    <div class="flex items-center gap-2 text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider mb-0.5">
                        <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                        <span>Onboarding &amp; Aktivasi Kontrak Managed Service</span>
                    </div>
                    <h3 id="accept-modal-title" class="text-[16px] font-bold text-[#1E293B]">Aktivasi Layanan</h3>
                </div>
                <button type="button" onclick="window.closeAcceptModal()" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Form Body --}}
            <form id="form-accept-handover" 
                  action="" 
                  method="POST" 
                  class="flex flex-col flex-1 min-h-0 overflow-hidden">
                @csrf

                <div class="p-5 sm:p-6 overflow-y-auto space-y-4 text-[12.5px] flex-1">
                    
                    {{-- Info Klien --}}
                    <div class="p-3.5 bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] flex flex-wrap items-center justify-between gap-2 text-xs">
                        <div>
                            <span class="text-[#64748B] block font-medium">Klien / Instansi:</span>
                            <span id="accept-modal-client" class="font-bold text-[#1E293B] text-[13px]">—</span>
                        </div>
                        <div class="text-right">
                            <span class="text-[#64748B] block font-medium">Nilai Kontrak:</span>
                            <span id="accept-modal-contract-value" class="font-bold text-[#8F0A0D] text-[13px]">—</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                SLA Tier Layanan <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <select id="accept-modal-sla-tier" name="sla_tier" required
                                    class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer">
                                <option value="Platinum">Platinum (24x7 MTTR 2 Jam, Uptime 99.9%)</option>
                                <option value="Gold">Gold (8x5 MTTR 4 Jam, Uptime 99.5%)</option>
                                <option value="Silver">Silver (8x5 Next Business Day, Uptime 99.0%)</option>
                                <option value="Bronze">Bronze (Best Effort On-Call Support)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Support Coverage Hours
                            </label>
                            <select id="accept-modal-sla-coverage" name="sla_coverage_hours"
                                    class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-semibold text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer">
                                <option value="24x7">24x7 (24 Jam Termasuk Libur)</option>
                                <option value="8x5">8x5 (Jam Kerja Senin-Jumat 08:00 - 17:00)</option>
                                <option value="12x7">12x7 (08:00 - 20:00 Setiap Hari)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Frekuensi Kunjungan PM
                            </label>
                            <select id="accept-modal-maint-freq" name="maintenance_frequency"
                                    class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer">
                                <option value="Monthly">Bulanan (Monthly)</option>
                                <option value="Quarterly">Triwulanan (3 Bulan)</option>
                                <option value="Bi-Annual">Semesteran (6 Bulan)</option>
                                <option value="Annual">Tahunan</option>
                                <option value="On-Demand">On-Demand</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Mulai Tanggal Layanan
                            </label>
                            <input type="date" id="accept-modal-start-date" name="service_start_date"
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer">
                        </div>

                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                                Akhir Kontrak Layanan
                            </label>
                            <input type="date" id="accept-modal-end-date" name="service_end_date"
                                   class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">
                            Catatan Khusus Operasional / SLA Baseline
                        </label>
                        <textarea id="accept-modal-notes" name="special_notes" rows="2.5" placeholder="Contoh: Akun monitoring NOC telah disiapkan, kontak darurat PIC klien: Bpk. Budi 0812..."
                                  class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#FAF9F8] shrink-0">
                    <button type="button" 
                            onclick="window.closeAcceptModal()" 
                            class="px-4 py-2 text-[12.5px] font-bold text-[#475569] hover:bg-gray-100 border border-gray-300 rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 text-[12.5px] font-bold text-white bg-[#8F0A0D] hover:bg-[#73080A] rounded-xl shadow-xs transition cursor-pointer">
                        Konfirmasi &amp; Aktifkan Layanan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

{{-- Standalone Modal Controller Script --}}
<script>
    window.triggerAcceptModal = function(btn) {
        if (!btn) return;
        var p = {
            id: btn.getAttribute('data-id'),
            name: btn.getAttribute('data-name'),
            client: btn.getAttribute('data-client'),
            contract_value_formatted: btn.getAttribute('data-value'),
            sla_tier: btn.getAttribute('data-tier'),
            sla_coverage_hours: btn.getAttribute('data-coverage'),
            maintenance_frequency: btn.getAttribute('data-freq'),
            service_start_date: btn.getAttribute('data-start'),
            service_end_date: btn.getAttribute('data-end'),
            special_notes: btn.getAttribute('data-notes')
        };
        window.openAcceptModal(p);
    };

    window.openAcceptModal = function(p) {
        if (!p) return;
        var modal = document.getElementById('modal-accept-handover');
        if (!modal) return;
        
        var form = document.getElementById('form-accept-handover');
        if (form) {
            form.action = '/managed-service/handovers/' + p.id + '/accept';
        }
        var titleEl = document.getElementById('accept-modal-title');
        if (titleEl) titleEl.textContent = 'Aktivasi: ' + (p.name || '');
        var clientEl = document.getElementById('accept-modal-client');
        if (clientEl) clientEl.textContent = p.client || '—';
        var valEl = document.getElementById('accept-modal-contract-value');
        if (valEl) valEl.textContent = p.contract_value_formatted || '—';
        var slaTierEl = document.getElementById('accept-modal-sla-tier');
        if (slaTierEl) slaTierEl.value = p.sla_tier || 'Gold';
        var slaCoverageEl = document.getElementById('accept-modal-sla-coverage');
        if (slaCoverageEl) slaCoverageEl.value = p.sla_coverage_hours || '24x7';
        var maintFreqEl = document.getElementById('accept-modal-maint-freq');
        if (maintFreqEl) maintFreqEl.value = p.maintenance_frequency || 'Monthly';
        var startDateEl = document.getElementById('accept-modal-start-date');
        if (startDateEl) startDateEl.value = p.service_start_date ? p.service_start_date.split('T')[0] : '{{ date('Y-m-d') }}';
        var endDateEl = document.getElementById('accept-modal-end-date');
        if (endDateEl) endDateEl.value = p.service_end_date ? p.service_end_date.split('T')[0] : '';
        var notesEl = document.getElementById('accept-modal-notes');
        if (notesEl) notesEl.value = p.special_notes || '';

        modal.style.display = 'flex';
    };

    window.closeAcceptModal = function() {
        var modal = document.getElementById('modal-accept-handover');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeAcceptModal();
        }
    });
</script>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctxHealth = document.getElementById('customerHealthChart');
        if (ctxHealth) {
            const chartData = @json($clientHealthChart);
            const totalCount = chartData.total || 0;
            const ctxHealthCanvas = ctxHealth.getContext('2d');

            const hasData = totalCount > 0;
            const dataCounts = hasData ? chartData.counts : [1];
            const dataColors = hasData ? ['#10B981', '#3B82F6', '#EF4444'] : ['#E2E8F0'];
            const dataLabels = hasData ? chartData.labels : ['Belum Ada Klien'];

            new Chart(ctxHealthCanvas, {
                type: 'doughnut',
                data: {
                    labels: dataLabels,
                    datasets: [{
                        data: dataCounts,
                        backgroundColor: dataColors,
                        borderWidth: 2,
                        borderColor: '#FFFFFF',
                        hoverOffset: hasData ? 4 : 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: hasData,
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed;
                                    const percentage = totalCount > 0 ? Math.round((value / totalCount) * 100) : 0;
                                    return ' ' + context.label + ': ' + value + ' Klien (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
    });

    function msDashboard() {
        return {};
    }
</script>
@endpush

