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
                            Pantau kepatuhan SLA operasional, antrean tiket gangguan, status operasional aset CI, dan jadwal preventive maintenance.
                        </p>
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="flex items-center gap-2.5 shrink-0">
                        <button type="button" 
                                @click="isCreateModalOpen = true" 
                                class="px-3.5 py-2 rounded-xl bg-white text-[#8F0A0D] font-bold text-[12.5px] hover:bg-[#FFF7F6] hover:scale-[1.02] transition-all shadow-sm flex items-center gap-1.5 cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Buat Tiket Baru</span>
                        </button>
                        <a href="{{ route('ms.tickets.index') }}" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 hover:scale-[1.02] border border-white/25 text-white font-bold text-[12.5px] transition-all flex items-center gap-1.5 backdrop-blur-sm">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
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
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3.5">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> RINGKASAN OPERASIONAL
                        </p>
                        <h2 class="text-[18px] font-bold text-[#292929] tracking-tight">Status & Distribusi Penugasan SLA</h2>
                    </div>
                    <a href="{{ url('/clean-maintenance-data?redirect=dashboard') }}" onclick="return confirm('Apakah Anda yakin ingin MENGOSONGKAN seluruh data transaksi Maintenance (tiket, proyek MS dummy, siklus PM, aset) untuk keperluan uji coba?')" class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 text-[#8F0A0D] font-bold text-xs inline-flex items-center gap-1.5 transition shrink-0 shadow-2xs self-start sm:self-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Bersihkan Semua Data Maintenance</span>
                    </a>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
                    
                    {{-- Metric 1: Klien Kontrak SLA --}}
                    <div class="ipnet-metric-card group block anim-fade-up anim-delay-1">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Kontrak SLA</span>
                            <div class="w-7 h-7 rounded-lg bg-[#F1F5F9] text-[#475569] flex items-center justify-center border border-[#E2E8F0]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[24px] font-extrabold text-[#1E293B] tracking-tight">{{ $operateProjects->count() }}</div>
                        <p class="text-[11px] text-[#64748B] mt-0.5">Klien Maintenance</p>
                    </div>

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

                </div>
            </div>


            {{-- ======================================================== --}}
            {{-- 3. PORTOFOLIO KLIEN & STATUS OPERASIONAL LAYANAN         --}}
            {{-- ======================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 anim-fade-up anim-delay-3">
                
                {{-- Left: Customer Operational Status List (lg:col-span-2) --}}
                <div class="lg:col-span-2 ipnet-card p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-[#E2E8F0]">
                            <div>
                                <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                                    <span class="ipnet-badge-dot"></span> PORTOFOLIO KLIEN SLA
                                </p>
                                <h3 class="text-[16px] font-bold text-[#292929]">Status Operasional Layanan SLA</h3>
                            </div>
                            <a href="{{ route('ms.tickets.index') }}" class="text-xs font-bold text-[#8F0A0D] hover:underline">Lihat Semua &rarr;</a>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                        <th class="pb-2.5">Pelanggan &amp; Proyek</th>
                                        <th class="pb-2.5">Urgensi Tiket (Severity)</th>
                                        <th class="pb-2.5">Status Layanan</th>
                                        <th class="pb-2.5">Keterangan</th>
                                        <th class="pb-2.5 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($clientHealthList as $client)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 pr-2">
                                            <div class="font-extrabold text-slate-900 text-[12.5px] line-clamp-1">{{ $client['name'] }}</div>
                                            <div class="text-[11px] text-slate-500 font-medium truncate max-w-[220px]">
                                                {{ $client['projectName'] }} &bull; <span class="text-slate-600 font-semibold">Kontrak {{ $client['sla_tier'] }}</span>
                                            </div>
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
                                        <td class="py-3 px-2 text-[11.5px] text-slate-600 font-medium whitespace-nowrap">
                                            {{ $client['status_desc'] }}
                                        </td>
                                        <td class="py-3 pl-2 text-right whitespace-nowrap">
                                            <a href="{{ route('ms.tickets.index', ['search' => $client['name']]) }}" class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition inline-block">
                                                Detail &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-slate-400">Belum ada portofolio klien SLA terdaftar.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
                                <h3 class="text-[16px] font-bold text-[#292929]">Status Layanan Customer</h3>
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
</script>
@endpush

