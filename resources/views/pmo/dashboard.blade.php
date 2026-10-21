@extends('layouts.app')

@section('title', 'Dashboard PMO - PT IP Network Solusindo')

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
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.25);
        transform: translateY(-1px);
    }

    @keyframes heroReveal {
        0% { opacity: 0; transform: translateY(18px) scale(0.99); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(16px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    .anim-hero-reveal {
        animation: heroReveal 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-fade-up {
        animation: fadeUpStagger 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-delay-1 { animation-delay: 0.06s !important; }
    .anim-delay-2 { animation-delay: 0.12s !important; }
    .anim-delay-3 { animation-delay: 0.18s !important; }
</style>
@endpush

@section('content')
@php
    $formattedProjects = $formattedProjects ?? collect();
    $yourProjects = $yourProjects ?? $formattedProjects;
    $totalBudgetSpend = $totalBudgetSpend ?? 0;
    $formattedTotalBudget = $formattedTotalBudget ?? ('Rp ' . number_format($totalBudgetSpend, 0, ',', '.'));
    $activeProjectsCount = $activeProjectsCount ?? $formattedProjects->whereIn('stage', ['Deliver', 'Operate'])->count();
    $totalTasksCount = $totalTasksCount ?? $formattedProjects->sum('total_tasks');
    $portfolioStatus = $portfolioStatus ?? 'Healthy';
    $portfolioStatusText = $portfolioStatusText ?? 'Active S-Curves';
    $handoverPendingCount = $handoverPendingCount ?? 0;
    $handoverConditionalCount = $handoverConditionalCount ?? 0;
    $taskShareList = $taskShareList ?? [];
    $taskShareChartData = $taskShareChartData ?? ['labels' => [], 'counts' => [], 'colors' => []];
@endphp
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="{}" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Dashboard PMO'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">
            
            <!-- ======================================================== -->
            <!-- 1. HERO BANNER (IDENTIK DENGAN DASHBOARD LEAD ENGINEER)  -->
            <!-- ======================================================== -->
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
                        </defs>
                        <rect width="1440" height="200" fill="url(#redGrad1)" />
                        <polygon points="0,0 650,0 280,200 0,200" fill="url(#redGrad1)" />
                        <polygon points="220,0 850,0 1300,200 600,200" fill="url(#redGrad2)" />
                        <polygon points="0,0 520,0 1080,200 480,200" fill="url(#redGrad1)" opacity="0.9" />
                        <polygon points="780,0 1440,0 1440,200 1100,200" fill="url(#redGrad3)" />
                        <polygon points="0,0 680,0 1120,200 380,200" fill="url(#redGradHighlight)" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="max-w-xl">
                        <div class="inline-flex items-center mb-1.5 px-2.5 py-0.5 text-[10.5px] font-bold text-white bg-white/15 backdrop-blur-md rounded-full border border-white/20 tracking-wider uppercase">
                            <span class="w-1.5 h-1.5 mr-1.5 bg-white rounded-full inline-block"></span>
                            PT IP NETWORK SOLUSINDO &bull; DASHBOARD PMO
                        </div>

                        <h1 class="text-[18px] sm:text-[21px] font-extrabold text-white tracking-tight leading-tight">
                            Selamat Datang, {{ auth()->user()->name }}
                        </h1>
                        <p class="mt-0.5 text-[12.5px] text-white/80 leading-relaxed line-clamp-1">
                            Pusat pengawasan implementasi proyek, serapan anggaran, kurva S realisasi, dan tata kelola deliverable.
                        </p>
                    </div>

                    {{-- Quick Action Buttons --}}
                    <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                        <a href="{{ route('pmo.implementations.index') }}" class="px-3.5 py-2 rounded-xl bg-white text-[#8F0A0D] font-bold text-[12.5px] hover:bg-[#FFF7F6] hover:scale-[1.02] transition-all shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <span>Halaman Projects</span>
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

            <!-- ======================================================== -->
            <!-- 2. METRIC SUMMARY CARDS (PERSIS DENGAN LEAD ENGINEER)   -->
            <!-- ======================================================== -->
            <div class="anim-fade-up anim-delay-1">
                <div class="flex items-center justify-between mb-3.5">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> RINGKASAN PORTOFOLIO PMO
                        </p>
                        <h2 class="text-[18px] font-bold text-[#1E293B] tracking-tight">Kesehatan Proyek & Alokasi Anggaran</h2>
                    </div>
                    <span class="text-xs font-semibold text-slate-500 bg-white px-3 py-1.5 rounded-xl border border-slate-200">
                        Periode Aktif {{ date('Y') }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    
                    {{-- 1. Total Anggaran Portofolio --}}
                    <div class="ipnet-metric-card border-l-4 border-l-[#8F0A0D] flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Total Anggaran</span>
                                <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-[20px] xl:text-[22px] font-black text-[#1E293B] tracking-tight truncate" title="{{ $formattedTotalBudget }}">
                                {{ $formattedTotalBudget }}
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-[#64748B] mt-2.5 pt-2 border-t border-slate-100 font-medium">
                            <span>Seluruh Portofolio</span>
                            <span class="font-bold text-slate-800">{{ $formattedProjects->count() }} Proyek</span>
                        </div>
                    </div>

                    {{-- 2. Proyek Aktif Implementasi --}}
                    <div class="ipnet-metric-card border-l-4 border-l-slate-700 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Proyek Aktif</span>
                                <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-[20px] xl:text-[22px] font-black text-[#1E293B] tracking-tight">
                                {{ $activeProjectsCount }}
                                <span class="text-xs font-semibold text-slate-500">Active</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-[#64748B] mt-2.5 pt-2 border-t border-slate-100 font-medium">
                            <span>Fase Deliver / Operate</span>
                            <span class="font-bold text-slate-800">Sesuai Kontrak</span>
                        </div>
                    </div>

                    {{-- 3. Total Task List --}}
                    <div class="ipnet-metric-card border-l-4 border-l-blue-600 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Total Tugas Tim</span>
                                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-[20px] xl:text-[22px] font-black text-[#1E293B] tracking-tight">
                                {{ $totalTasksCount }}
                                <span class="text-xs font-semibold text-slate-500">Tasks</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-[#64748B] mt-2.5 pt-2 border-t border-slate-100 font-medium">
                            <span>Workload</span>
                            <span class="font-bold text-slate-800">Tim Lapangan</span>
                        </div>
                    </div>

                    {{-- 4. Status Kesehatan Portofolio --}}
                    <div class="ipnet-metric-card border-l-4 border-l-emerald-600 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Kesehatan Portofolio</span>
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $portfolioStatus === 'Healthy' ? 'bg-emerald-500' : ($portfolioStatus === 'At-Risk' ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                                <span class="text-[20px] xl:text-[22px] font-black text-[#1E293B] tracking-tight">{{ $portfolioStatus }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-[#64748B] mt-2.5 pt-2 border-t border-slate-100 font-medium">
                            <span>{{ $portfolioStatusText }}</span>
                            <span class="font-bold {{ $portfolioStatus === 'Healthy' ? 'text-emerald-700' : ($portfolioStatus === 'At-Risk' ? 'text-amber-700' : 'text-rose-700') }}">Sesuai SLA</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ======================================================== -->
            <!-- 3. PERINGATAN HANDOVER SALES JIKA ADA                    -->
            <!-- ======================================================== -->
            @if($handoverPendingCount > 0 || $handoverConditionalCount > 0)
                <div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-4 anim-fade-up">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-red-50 text-[#8F0A0D] border border-red-200 flex items-center justify-center shrink-0">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-bold text-slate-900">Verifikasi Berkas Handover Proyek dari Sales</h4>
                                <span class="px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-[#8F0A0D] text-white">
                                    {{ $handoverPendingCount }} Berkas Baru
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Terdapat proyek yang telah Closed Won dari Sales dan menunggu verifikasi kelengkapan berkas oleh PMO.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('pmo.implementations.index', ['tab' => 'incoming']) }}" 
                       class="px-3.5 py-1.5 bg-[#8F0A0D] hover:bg-[#73080A] text-white text-xs font-bold rounded-xl transition shadow-xs whitespace-nowrap self-start sm:self-center">
                        Tinjau Handover &rarr;
                    </a>
                </div>
            @endif

            <!-- ======================================================== -->
            <!-- 4. DAFTAR PORTOFOLIO IMPLEMENTASI & KURVA S LAPANGAN     -->
            <!-- ======================================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start anim-fade-up anim-delay-2">
                
                {{-- LEFT/MAIN COLUMN: Clean Projects Table (Like Sales & Lead Dashboard) --}}
                <div class="lg:col-span-8 ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-[#E2E8F0]">
                        <div>
                            <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">
                                <span class="ipnet-badge-dot"></span> PORTOFOLIO IMPLEMENTASI AKTIF
                            </p>
                            <h3 class="text-[16px] font-bold text-[#1E293B]">Daftar Proyek &amp; Kontrol S-Curve Lapangan</h3>
                        </div>
                        <a href="{{ route('pmo.implementations.index') }}" class="text-xs font-bold text-[#8F0A0D] hover:underline">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                    <th class="pb-2.5">Proyek &amp; Klien</th>
                                    <th class="pb-2.5">Divisi &amp; PIC PM</th>
                                    <th class="pb-2.5">Tenggat &amp; Progres EV</th>
                                    <th class="pb-2.5">Status SLA</th>
                                    <th class="pb-2.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($yourProjects as $p)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3.5 pr-3">
                                            <div class="font-extrabold text-slate-900 text-[13px] line-clamp-1">{{ $p['name'] }}</div>
                                            <div class="text-[11.5px] text-slate-500 font-medium flex items-center gap-1.5 mt-0.5">
                                                <span>{{ $p['client'] }}</span>
                                                <span class="text-slate-300">&bull;</span>
                                                <span class="text-slate-700 font-bold">{{ $p['contract_value_formatted'] ?? 'Rp 0' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3 whitespace-nowrap">
                                            <div class="font-bold text-slate-800 text-[12px]">{{ $p['pm'] }}</div>
                                            <div class="text-[11px] text-slate-500">{{ $p['division'] }}</div>
                                        </td>
                                        <td class="py-3.5 px-3 min-w-[170px]">
                                            <div class="flex items-center justify-between text-[11px] font-semibold text-slate-700 mb-1">
                                                <span>Actual EV</span>
                                                <span class="font-bold text-slate-900">{{ $p['progress'] }}%</span>
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="h-full rounded-full transition-all duration-300 bg-[#8F0A0D]" 
                                                     style="width: {{ $p['progress'] }}%"></div>
                                            </div>
                                            <div class="text-[10px] text-slate-400 mt-1">
                                                {{ $p['days_span'] ?? 0 }} Hari Durasi
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-3 whitespace-nowrap">
                                            @if($p['health_status'] === 'On-Track')
                                                <span class="px-2.5 py-0.5 rounded-md text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    On-Track
                                                </span>
                                            @elseif($p['health_status'] === 'At-Risk')
                                                <span class="px-2.5 py-0.5 rounded-md text-[10.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    At-Risk
                                                </span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-md text-[10.5px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    Delayed
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 pl-3 text-right whitespace-nowrap">
                                            <a href="{{ route('pmo.projects.show', $p['id']) }}" 
                                               class="px-3 py-1.5 rounded-xl text-[11.5px] font-bold text-white btn-ipnet-primary transition inline-flex items-center gap-1 shadow-2xs">
                                                <span>Dashboard &amp; Kurva S</span>
                                                <span class="text-xs">&rarr;</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-400 text-xs font-medium">
                                            Belum ada portofolio implementasi aktif.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Task Share by Project (Donut Chart & Breakdown) --}}
                <div class="lg:col-span-4 ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="border-b border-[#E2E8F0] pb-3 flex items-center justify-between">
                        <div>
                            <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">
                                <span class="ipnet-badge-dot"></span> ALOKASI KERJA
                            </p>
                            <h3 class="text-sm font-bold text-[#1E293B]">Task Share by Project</h3>
                        </div>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $totalTasksCount }} Tasks
                        </span>
                    </div>

                    @if($totalTasksCount > 0)
                        <div style="height: 190px;" class="flex items-center justify-center relative">
                            <canvas id="pmoTaskShareChart"></canvas>
                        </div>
                    @else
                        <div class="h-[170px] flex flex-col items-center justify-center text-center p-4 border border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                            <p class="text-xs font-semibold text-slate-700">Belum Ada Tugas Teknis</p>
                            <p class="text-[11px] text-slate-400 max-w-[200px] mt-0.5 leading-relaxed">
                                Beban kerja akan tampil otomatis saat tugas dibuat pada proyek.
                            </p>
                        </div>
                    @endif

                    {{-- Task Breakdown List --}}
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        @forelse($taskShareList as $item)
                            <div class="flex items-center justify-between text-xs py-1.5 px-2 rounded-xl hover:bg-slate-50 transition">
                                <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $item['color'] }}"></span>
                                    <div class="truncate">
                                        <div class="font-bold text-slate-800 truncate" title="{{ $item['name'] }}">{{ $item['name'] }}</div>
                                        <div class="text-[10.5px] text-slate-400 truncate">{{ $item['client'] }}</div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-bold text-slate-700">{{ $item['tasks_count'] }} Tasks</span>
                                    <span class="text-[10.5px] text-slate-400 block font-medium">({{ $item['percentage'] }}%)</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-slate-400 text-xs font-medium">
                                Belum ada task yang teralokasi pada portofolio.
                            </div>
                        @endforelse
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
        const shareData = {!! json_encode($taskShareChartData ?? ['labels' => [], 'counts' => [], 'colors' => []]) !!};
        const ctx = document.getElementById('pmoTaskShareChart');
        if (ctx && shareData.labels && shareData.labels.length > 0) {
            new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: shareData.labels,
                    datasets: [{
                        data: shareData.counts,
                        backgroundColor: shareData.colors,
                        borderWidth: 2,
                        borderColor: '#FFFFFF',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '66%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const val = context.raw || 0;
                                    const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                                    return ` ${context.label}: ${val} Tasks (${pct}%)`;
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
