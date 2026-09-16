@extends('layouts.app')

@section('title', 'Managed Service - Service Delivery & Operate')

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
        padding: 18px 20px;
        position: relative;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
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

    .btn-ipnet-gradient {
        background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
        color: #FFFFFF;
        transition: all 0.2s ease;
    }

    .btn-ipnet-gradient:hover {
        background: linear-gradient(135deg, #7A080B 0%, #9E0E1D 100%);
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.25);
        transform: translateY(-1px);
    }

    .btn-ipnet-gradient:active {
        transform: translateY(0);
    }

    /* Fluid Entrance Animations */
    @keyframes fadeUpStagger {
        0% {
            opacity: 0;
            transform: translateY(16px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .anim-fade-up {
        animation: fadeUpStagger 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-delay-1 { animation-delay: 0.06s !important; }
    .anim-delay-2 { animation-delay: 0.12s !important; }
    .anim-delay-3 { animation-delay: 0.18s !important; }
    .anim-delay-4 { animation-delay: 0.24s !important; }
    .anim-delay-5 { animation-delay: 0.30s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="msDashboard()" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Managed Service (Operate & SLA)'])
        
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

            {{-- ========================================================== --}}
            {{-- SECTION HEADER & CONTROLS TOOLBAR                          --}}
            {{-- ========================================================== --}}
            <div class="ipnet-card p-5 sm:p-6 anim-fade-up anim-delay-1">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MANAJEMEN OPERASIONAL & SERVICE LEVEL AGREEMENT
                        </p>
                        <h2 class="text-[20px] font-bold text-[#1E293B] tracking-tight mt-0.5">Managed Service (Operate & SLA)</h2>
                        <p class="text-[13px] text-[#64748B] mt-0.5">Monitoring kepatuhan SLA, antrean tiket gangguan, status kesehatan aset CI, dan jadwal preventive maintenance</p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2.5 self-start lg:self-auto">
                        <button @click="isTicketModalOpen = true" 
                                class="btn-ipnet-gradient px-4 py-2.5 rounded-xl font-bold text-[12.5px] flex items-center gap-2 cursor-pointer shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Buat Tiket Baru</span>
                        </button>
                        <button @click="isAssetModalOpen = true" 
                                class="px-4 py-2.5 rounded-xl border border-[#CBD5E1] bg-white text-[#1E293B] hover:bg-[#F8FAFC] hover:border-[#94A3B8] font-bold text-[12.5px] transition-all shadow-xs flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-[#64748B]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah CI Aset</span>
                        </button>
                    </div>
                </div>

                {{-- Quick Module Navigation Pills --}}
                <div class="flex items-center gap-2 pt-4 mt-4 border-t border-[#E2E8F0] overflow-x-auto text-[12px]">
                    <a href="{{ route('ms.tickets.index') }}" class="px-3 py-1.5 rounded-lg bg-[#F8FAFC] hover:bg-[#F1F5F9] text-[#475569] font-bold border border-[#E2E8F0] transition flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        <span>Daftar Tiket & SLA</span>
                    </a>
                    <a href="{{ route('ms.assets.index') }}" class="px-3 py-1.5 rounded-lg bg-[#F8FAFC] hover:bg-[#F1F5F9] text-[#475569] font-bold border border-[#E2E8F0] transition flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                        <span>Aset Perangkat (CI)</span>
                    </a>
                    <a href="{{ route('ms.maintenance.index') }}" class="px-3 py-1.5 rounded-lg bg-[#F8FAFC] hover:bg-[#F1F5F9] text-[#475569] font-bold border border-[#E2E8F0] transition flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Preventive Maintenance</span>
                    </a>
                    <a href="{{ route('ms.reports.index') }}" class="px-3 py-1.5 rounded-lg bg-[#F8FAFC] hover:bg-[#F1F5F9] text-[#475569] font-bold border border-[#E2E8F0] transition flex items-center gap-1.5 whitespace-nowrap">
                        <svg class="w-3.5 h-3.5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Laporan Bulanan SLA</span>
                    </a>
                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- 4 PRIMARY KPI METRIC CARDS                                 --}}
            {{-- ========================================================== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Card 1: SLA Score --}}
                <div class="ipnet-metric-card flex flex-col justify-between space-y-4 anim-fade-up anim-delay-1">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Target 99.5%
                            </span>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Pencapaian Kepatuhan SLA</p>
                            <h3 class="text-[26px] font-black text-emerald-600 tracking-tight mt-0.5">
                                {{ $slaScore }}%
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[11.5px] pt-3 border-t border-[#F1F5F9] text-[#64748B]">
                        <span class="font-bold text-emerald-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            SLA Terjaga
                        </span>
                        <span class="text-[#94A3B8]">Operasional 24/7</span>
                    </div>
                </div>

                {{-- Card 2: Tiket Insiden Aktif --}}
                <div class="ipnet-metric-card flex flex-col justify-between space-y-4 anim-fade-up anim-delay-2">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-md {{ $criticalTickets > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200 animate-pulse' : 'bg-[#F1F5F9] text-[#64748B] border border-[#E2E8F0]' }}">
                                {{ $criticalTickets > 0 ? $criticalTickets . ' P1 Critical' : '0 P1 Critical' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Tiket Aktif Penanganan</p>
                            <h3 class="text-[26px] font-black text-[#1E293B] tracking-tight mt-0.5">
                                {{ $openTickets }} <span class="text-[14px] font-bold text-[#64748B]">Tiket</span>
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[11.5px] pt-3 border-t border-[#F1F5F9] text-[#64748B]">
                        <span class="font-bold text-[#1E293B]">{{ $resolvedTickets }} Selesai</span>
                        <span class="text-[#94A3B8]">Bulan Berjalan</span>
                    </div>
                </div>

                {{-- Card 3: Status CI Aset Klien --}}
                <div class="ipnet-metric-card flex flex-col justify-between space-y-4 anim-fade-up anim-delay-3">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-md {{ $warningAssets > 0 ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                {{ $warningAssets > 0 ? $warningAssets . ' Perhatian' : 'Semua Normal' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Aset & Configuration (CI)</p>
                            <h3 class="text-[26px] font-black text-blue-600 tracking-tight mt-0.5">
                                {{ $onlineAssets }} <span class="text-[15px] font-bold text-[#64748B]">/ {{ $totalAssets }} Online</span>
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[11.5px] pt-3 border-t border-[#F1F5F9] text-[#64748B]">
                        <span class="font-bold text-[#1E293B]">Data Center & Site</span>
                        <span class="text-[#94A3B8]">Klien Terpasang</span>
                    </div>
                </div>

                {{-- Card 4: Kontrak Operasional --}}
                <div class="ipnet-metric-card flex flex-col justify-between space-y-4 anim-fade-up anim-delay-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center shadow-2xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 border border-purple-200">
                                Stage 4: Operate
                            </span>
                        </div>
                        <div>
                            <p class="text-[11.5px] font-bold text-[#64748B] uppercase tracking-wider">Kontrak Layanan Berjalan</p>
                            <h3 class="text-[26px] font-black text-purple-700 tracking-tight mt-0.5">
                                {{ $operateProjects->count() }} <span class="text-[14px] font-bold text-[#64748B]">Klien Aktif</span>
                            </h3>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[11.5px] pt-3 border-t border-[#F1F5F9] text-[#64748B]">
                        <span class="font-bold text-purple-700">SLA Maintenance</span>
                        <span class="text-[#94A3B8]">Tahunan / Bulanan</span>
                    </div>
                </div>

            </div>

            {{-- ========================================================== --}}
            {{-- 2 MAIN COLUMNS: LIVE INCIDENT TICKETS & CI ASSET MONITORING --}}
            {{-- ========================================================== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 anim-fade-up anim-delay-5">
                
                {{-- Left 2 Cols: Antrean Tiket Insiden & Permintaan Layanan --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="ipnet-card overflow-hidden">
                        <div class="p-5 sm:px-6 sm:py-4.5 border-b border-[#E2E8F0] flex items-center justify-between bg-white">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D] animate-pulse"></span>
                                    <h3 class="font-bold text-[#1E293B] text-[15px] tracking-tight">Antrean Tiket Insiden & Permintaan Layanan</h3>
                                </div>
                                <p class="text-[12.5px] text-[#64748B] mt-0.5">Monitoring status respon tiket gangguan dan tenggat waktu kepatuhan SLA</p>
                            </div>
                            <a href="{{ route('ms.tickets.index') }}" class="text-[12.5px] font-extrabold text-[#8F0A0D] hover:underline flex items-center gap-1 group">
                                <span>Lihat Semua Tiket</span>
                                <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                            </a>
                        </div>

                        <div class="divide-y divide-[#F1F5F9]">
                            @forelse($tickets->take(5) as $ticket)
                                <div class="p-4 sm:p-5 hover:bg-[#F8FAFC] transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1.5 flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="font-mono text-[11.5px] font-extrabold text-[#1E293B] bg-[#F1F5F9] px-2 py-0.5 rounded-md border border-[#E2E8F0]">
                                                {{ $ticket->ticket_number }}
                                            </span>
                                            
                                            {{-- Type Badge --}}
                                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold 
                                                {{ $ticket->type === 'Incident' ? 'bg-red-50 text-red-700 border border-red-200' : ($ticket->type === 'Change Request' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-blue-700 border border-blue-200') }}">
                                                {{ $ticket->type }}
                                            </span>

                                            {{-- Priority Badge --}}
                                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold 
                                                {{ str_contains($ticket->priority, 'P1') ? 'bg-rose-100 text-rose-800 border border-rose-200' : (str_contains($ticket->priority, 'P2') ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-[#F1F5F9] text-[#475569] border border-[#CBD5E1]') }}">
                                                {{ $ticket->priority }}
                                            </span>

                                            {{-- Status Badge --}}
                                            <span class="px-2 py-0.5 rounded-md text-[10.5px] font-bold 
                                                {{ $ticket->status === 'Resolved' || $ticket->status === 'Closed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                                {{ $ticket->status }}
                                            </span>
                                        </div>

                                        <h4 class="font-bold text-[#1E293B] text-[14px] truncate hover:text-[#8F0A0D] transition cursor-pointer">
                                            {{ $ticket->title }}
                                        </h4>
                                        
                                        <div class="flex items-center gap-2.5 text-[12px] text-[#64748B] flex-wrap">
                                            <span class="font-bold text-[#334155]">{{ $ticket->client_name }}</span>
                                            @if($ticket->asset)
                                                <span>• Perangkat: <strong class="text-[#334155]">{{ $ticket->asset->device_name }}</strong></span>
                                            @endif
                                            <span>• PIC: <strong class="text-[#8F0A0D] bg-[#FEF2F2] px-1.5 py-0.5 rounded border border-[#FECACA]">{{ $ticket->assignedEngineer?->name ?: 'Belum Ditugaskan' }}</strong></span>
                                        </div>
                                    </div>

                                    <div class="text-left sm:text-right whitespace-nowrap space-y-1 flex-shrink-0 bg-[#F8FAFC] sm:bg-transparent p-2.5 sm:p-0 rounded-xl border border-[#E2E8F0] sm:border-0">
                                        <div class="text-[10.5px] font-bold uppercase tracking-wider text-[#94A3B8]">SLA Deadline</div>
                                        <div class="text-[12.5px] font-extrabold {{ $ticket->sla_deadline && now()->gt($ticket->sla_deadline) && !in_array($ticket->status, ['Resolved', 'Closed']) ? 'text-red-600' : 'text-[#1E293B]' }}">
                                            {{ $ticket->sla_deadline ? $ticket->sla_deadline->format('d M, H:i') : '—' }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-12 text-center text-[#64748B] text-[13px]">
                                    <div class="w-12 h-12 rounded-2xl bg-[#F8FAFC] border border-[#E2E8F0] flex items-center justify-center mx-auto mb-2 text-[#94A3B8]">
                                        <svg class="w-6 h-6 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <p class="font-bold text-[#1E293B]">Tidak Ada Tiket Insiden Aktif</p>
                                    <p class="text-[12px] text-[#94A3B8] mt-0.5">Semua layanan dan operasional klien terpantau normal.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Right 1 Col: Live Assets Health & Support Team --}}
                <div class="space-y-6">
                    
                    {{-- Card CI Assets Overview --}}
                    <div class="ipnet-card p-5 sm:p-6 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0]">
                            <div>
                                <h3 class="font-bold text-[#1E293B] text-[14px]">Status Perangkat Aset (CI)</h3>
                                <p class="text-[11.5px] text-[#64748B]">Kesehatan perangkat klien terpasang</p>
                            </div>
                            <a href="{{ route('ms.assets.index') }}" class="text-[12px] font-extrabold text-[#8F0A0D] hover:underline flex items-center gap-0.5 group">
                                <span>Kelola</span>
                                <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                            </a>
                        </div>

                        <div class="space-y-2.5 text-[12px]">
                            @forelse($assets->take(4) as $a)
                                <div class="p-3 rounded-xl bg-[#F8FAFC] hover:bg-[#F1F5F9] border border-[#E2E8F0] flex items-center justify-between transition">
                                    <div class="space-y-0.5 min-w-0 pr-2">
                                        <div class="font-bold text-[#1E293B] truncate">{{ $a->device_name }}</div>
                                        <div class="text-[11px] text-[#64748B] truncate">{{ $a->brand }} {{ $a->model }} &bull; {{ $a->client_name }}</div>
                                    </div>
                                    <span class="px-2 py-0.5 text-[10.5px] font-bold rounded-md whitespace-nowrap flex-shrink-0
                                        {{ $a->status === 'Online' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($a->status === 'Warning' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-red-50 text-red-700 border border-red-200') }}">
                                        {{ $a->status }}
                                    </span>
                                </div>
                            @empty
                                <div class="py-6 text-center text-[#94A3B8] text-[12px]">
                                    Belum ada data perangkat CI.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Card Agenda Preventive Maintenance Terdekat --}}
                    <div class="ipnet-card p-5 sm:p-6 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0]">
                            <div>
                                <h3 class="font-bold text-[#1E293B] text-[14px]">Agenda Preventive Maintenance</h3>
                                <p class="text-[11.5px] text-[#64748B]">Jadwal inspeksi & pemeliharaan rutin</p>
                            </div>
                            <a href="{{ route('ms.maintenance.index') }}" class="text-[12px] font-extrabold text-[#8F0A0D] hover:underline flex items-center gap-0.5 group">
                                <span>Jadwal</span>
                                <span class="group-hover:translate-x-0.5 transition-transform">&rarr;</span>
                            </a>
                        </div>

                        <div class="space-y-2.5 text-[12px]">
                            @forelse($upcomingPmSchedules as $sch)
                                <div class="p-3 rounded-xl bg-[#F8FAFC] hover:bg-[#F1F5F9] border border-[#E2E8F0] space-y-1.5 transition">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-bold text-[#1E293B] truncate">{{ $sch->title }}</span>
                                        <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md bg-blue-50 text-blue-700 border border-blue-200 whitespace-nowrap flex-shrink-0">
                                            {{ $sch->date ? $sch->date->format('d M') : 'Terjadwal' }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-[#64748B] truncate">
                                        {{ $sch->project ? $sch->project->name : ($sch->client_name ?: 'Klien Regular') }}
                                    </div>
                                    <div class="text-[10.5px] text-[#94A3B8]">
                                        Teknisi: <strong class="text-[#8F0A0D]">{{ ($sch->engineers && $sch->engineers->isNotEmpty()) ? $sch->engineers->pluck('name')->implode(', ') : ($sch->engineer?->name ?: 'Doris / Tim Maintenance') }}</strong>
                                    </div>
                                </div>
                            @empty
                                <div class="py-6 text-center text-[#94A3B8] text-[12px]">
                                    Belum ada agenda pemeliharaan terjadwal.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL CREATE TICKET                                          --}}
    {{-- ============================================================ --}}
    <template x-teleport="body">
        <div x-show="isTicketModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-[#E2E8F0] space-y-4 max-h-[90vh] overflow-y-auto" @click.away="isTicketModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3.5">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">Tiket Baru</p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]">Buat Tiket Insiden / Permintaan Layanan</h3>
                    </div>
                    <button @click="isTicketModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('ms.tickets.store') }}" method="POST" class="space-y-3.5 text-[12.5px]">
                    @csrf
                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Nama Instansi / Klien <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" name="client_name" required placeholder="Contoh: Bank Mandiri, PT Telkom"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Judul Tiket / Insiden <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" name="title" required placeholder="Contoh: Flapping Link SFP Port 24"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Tipe Layanan <span class="text-[#8F0A0D]">*</span></label>
                            <select name="type" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                <option value="Incident">Incident (Gangguan)</option>
                                <option value="Service Request">Service Request (Permintaan)</option>
                                <option value="Change Request">Change Request (Perubahan)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Tingkat Prioritas (SLA) <span class="text-[#8F0A0D]">*</span></label>
                            <select name="priority" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                <option value="P1 - Critical">P1 - Critical (SLA 1 Jam)</option>
                                <option value="P2 - Major" selected>P2 - Major (SLA 4 Jam)</option>
                                <option value="P3 - Minor">P3 - Minor (SLA 8 Jam)</option>
                                <option value="P4 - Low">P4 - Low (SLA 24 Jam)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Perangkat CI Terkait (Opsional)</label>
                        <select name="asset_id" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                            <option value="">-- Pilih Perangkat (Opsional) --</option>
                            @foreach($assets as $a)
                                <option value="{{ $a->id }}">{{ $a->device_name }} ({{ $a->client_name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Nama Pelapor</label>
                            <input type="text" name="reported_by" placeholder="Bpk. Hendra (IT Ops)"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">No. Kontak / WA</label>
                            <input type="text" name="contact_phone" placeholder="0812-xxxx-xxxx"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Tugaskan ke Engineer PIC</label>
                        <select name="assigned_to" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                            <option value="">-- Pilih Teknisi (Opsional) --</option>
                            @foreach($maintenanceEngineers as $eng)
                                <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Rincian Deskripsi Masalah</label>
                        <textarea name="description" rows="3" placeholder="Jelaskan kronologi kendala atau permintaan..."
                                  class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3.5 border-t border-[#E2E8F0]">
                        <button type="button" @click="isTicketModalOpen = false" class="px-4 py-2.5 text-[12.5px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 text-[12.5px] font-bold rounded-xl shadow-md cursor-pointer">
                            Terbitkan Tiket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- ============================================================ --}}
    {{-- MODAL CREATE ASSET                                           --}}
    {{-- ============================================================ --}}
    <template x-teleport="body">
        <div x-show="isAssetModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-[#E2E8F0] space-y-4 max-h-[90vh] overflow-y-auto" @click.away="isAssetModalOpen = false">
                <div class="flex items-center justify-between border-b border-[#E2E8F0] pb-3.5">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">Aset CI Baru</p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]">Tambah Aset Configuration Item (CI) Baru</h3>
                    </div>
                    <button @click="isAssetModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('ms.assets.store') }}" method="POST" class="space-y-3.5 text-[12.5px]">
                    @csrf
                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Nama Instansi / Klien <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" name="client_name" required placeholder="Contoh: Bank Mandiri, PT Telkom"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                    </div>

                    <div>
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Nama Perangkat & Hostname <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" name="device_name" required placeholder="Contoh: Core DC Switch FortiGate 600E"
                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Kategori <span class="text-[#8F0A0D]">*</span></label>
                            <select name="category" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                <option value="Switch">Switch</option>
                                <option value="Router">Router</option>
                                <option value="Firewall">Firewall</option>
                                <option value="Server">Server</option>
                                <option value="Access Point">Access Point</option>
                                <option value="UPS">UPS</option>
                                <option value="Storage">Storage</option>
                                <option value="Other">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Status Kesehatan <span class="text-[#8F0A0D]">*</span></label>
                            <select name="status" class="w-full px-3.5 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                                <option value="Online">Online</option>
                                <option value="Warning">Warning</option>
                                <option value="Offline">Offline</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Brand / Merek</label>
                            <input type="text" name="brand" placeholder="Cisco, Fortinet, Mikrotik"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Model / Tipe</label>
                            <input type="text" name="model" placeholder="FG-600E, C9500"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Serial Number</label>
                            <input type="text" name="serial_number" placeholder="SN-123456"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">IP Address Management</label>
                            <input type="text" name="ip_address" placeholder="10.240.1.1"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Lokasi Site</label>
                            <input type="text" name="location_site" placeholder="Data Center Lt. 8"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>
                        <div>
                            <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px] mb-1.5">Posisi Rack</label>
                            <input type="text" name="rack_position" placeholder="Rack DC-04 (U18)"
                                   class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-3.5 border-t border-[#E2E8F0]">
                        <button type="button" @click="isAssetModalOpen = false" class="px-4 py-2.5 text-[12.5px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 text-[12.5px] font-bold rounded-xl shadow-md cursor-pointer">
                            Simpan Aset CI
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

</div>

<script>
    function msDashboard() {
        return {
            isTicketModalOpen: false,
            isAssetModalOpen: false,
        }
    }
</script>
@endsection
