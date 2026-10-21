@extends('layouts.app')

@section('title', 'Dashboard - PMO Pusat Pengawasan & Portofolio')

@push('styles')
<style>
    .ipnet-hero-banner {
        background: linear-gradient(125deg, #B81525 0%, #9E0E1D 40%, #830B17 75%, #63050F 100%);
        position: relative;
        overflow: hidden;
    }

    @keyframes heroReveal {
        0% { opacity: 0; transform: translateY(18px) scale(0.99); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    .anim-hero-reveal {
        animation: heroReveal 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    }
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
    $portfolioStatusClass = $portfolioStatusClass ?? 'text-emerald-600';
    $portfolioBadgeClass = $portfolioBadgeClass ?? 'bg-emerald-50 text-emerald-700 border-emerald-200';
    $handoverPendingCount = $handoverPendingCount ?? 0;
    $handoverConditionalCount = $handoverConditionalCount ?? 0;
    $taskShareList = $taskShareList ?? [];
    $taskShareChartData = $taskShareChartData ?? ['labels' => [], 'counts' => [], 'colors' => []];
@endphp
<div class="flex h-screen overflow-hidden" x-data="{}" x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-[#F8FAFC]">
        @include('components.topbar', ['title' => 'Dashboard'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-[1680px] mx-auto">
            
            <!-- ========================================================== -->
            <!-- 1. EXECUTIVE HERO BANNER                                    -->
            <!-- ========================================================== -->
            <div class="ipnet-hero-banner rounded-2xl px-5 py-4 sm:px-6 sm:py-5 text-white shadow-md shadow-red-950/15 relative anim-hero-reveal">
                {{-- Layered Geometric Background --}}
                <div class="absolute inset-0 pointer-events-none overflow-hidden select-none">
                    <svg class="w-full h-full object-cover" viewBox="0 0 1440 200" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="pmoRedGrad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#C61828" />
                                <stop offset="100%" stop-color="#9E0E1D" />
                            </linearGradient>
                            <linearGradient id="pmoRedGrad2" x1="100%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="#B01423" />
                                <stop offset="100%" stop-color="#7A0813" />
                            </linearGradient>
                            <linearGradient id="pmoRedGrad3" x1="0%" y1="100%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#940E1B" />
                                <stop offset="100%" stop-color="#5A040C" />
                            </linearGradient>
                            <linearGradient id="pmoRedGradHighlight" x1="0%" y1="0%" x2="100%" y2="50%">
                                <stop offset="0%" stop-color="#FFA8B2" stop-opacity="0.20" />
                                <stop offset="100%" stop-color="#FFFFFF" stop-opacity="0" />
                            </linearGradient>
                            <filter id="pmoFacetDropShadow" x="-10%" y="-10%" width="130%" height="130%">
                                <feDropShadow dx="-8" dy="10" stdDeviation="14" flood-color="#3A0207" flood-opacity="0.4" />
                            </filter>
                        </defs>
                        <rect width="1440" height="200" fill="url(#pmoRedGrad1)" />
                        <polygon points="0,0 650,0 280,200 0,200" fill="url(#pmoRedGrad1)" />
                        <polygon points="220,0 850,0 1300,200 600,200" fill="url(#pmoRedGrad2)" filter="url(#pmoFacetDropShadow)" />
                        <polygon points="0,0 520,0 1080,200 480,200" fill="url(#pmoRedGrad1)" opacity="0.9" filter="url(#pmoFacetDropShadow)" />
                        <polygon points="780,0 1440,0 1440,200 1100,200" fill="url(#pmoRedGrad3)" filter="url(#pmoFacetDropShadow)" />
                        <polygon points="0,0 680,0 1120,200 380,200" fill="url(#pmoRedGradHighlight)" />
                    </svg>
                </div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="inline-flex items-center mb-1.5 px-2.5 py-0.5 text-[10.5px] font-bold text-white bg-white/15 backdrop-blur-md rounded-full border border-white/20 tracking-wider uppercase">
                            <span class="w-1.5 h-1.5 mr-1.5 bg-white rounded-full inline-block"></span>
                            PT IP NETWORK SOLUSINDO &bull; PROJECT MANAGEMENT OFFICE
                        </div>
                        <h1 class="text-[19px] sm:text-[22px] font-extrabold text-white tracking-tight leading-tight">
                            Pusat Kendali Pengawasan Proyek
                        </h1>
                        <p class="mt-0.5 text-[12px] sm:text-[13px] text-white/85 leading-relaxed truncate">
                            Monitoring alokasi anggaran, beban task tim lapangan, status kesehatan S-Curve, dan portofolio implementasi aktif.
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                        <a href="{{ route('pmo.implementations.index') }}" 
                           class="px-4 py-2.5 rounded-xl bg-white text-[#8F0A0D] font-bold text-xs hover:bg-[#FFF7F6] hover:scale-[1.02] transition-all shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                            <span>Buka Halaman Projects</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 2. 4 EXECUTIVE KPI METRIC CARDS                            -->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
                
                {{-- Card 1: Total Budget Spend --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-blue-200 transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Budget Spend</span>
                            <h3 class="text-xs font-bold text-slate-700">Anggaran Proyek</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-[18px] sm:text-[21px] font-extrabold text-slate-900 tracking-tight">{{ $formattedTotalBudget }}</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Across all projects</span>
                            <span class="font-bold text-blue-600">Total Portofolio</span>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Project Number --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-purple-200 transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Project Number</span>
                            <h3 class="text-xs font-bold text-slate-700">Jumlah Proyek</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-purple-50 border border-purple-100 text-purple-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-[22px] sm:text-[24px] font-extrabold text-slate-900 tracking-tight">{{ $activeProjectsCount }}</span>
                            <span class="text-xs font-bold text-purple-600">Active</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Portfolios</span>
                            <span class="font-bold text-purple-700">{{ $formattedProjects->count() }} Total Proyek</span>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Total Task List --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-amber-200 transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Task List</span>
                            <h3 class="text-xs font-bold text-slate-700">Daftar Tugas Tim</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-[22px] sm:text-[24px] font-extrabold text-slate-900 tracking-tight">{{ $totalTasksCount }}</span>
                            <span class="text-xs font-bold text-amber-700">Tasks</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Workload</span>
                            <span class="font-bold text-amber-800">Alokasi Tim Lapangan</span>
                        </div>
                    </div>
                </div>

                {{-- Card 4: Portfolio Status --}}
                <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-emerald-200 transition-all flex flex-col justify-between group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="space-y-0.5">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Portfolio Status</span>
                            <h3 class="text-xs font-bold text-slate-700">Kesehatan Portofolio</h3>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform shadow-2xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-[22px] sm:text-[24px] font-extrabold {{ $portfolioStatusClass }} tracking-tight">{{ $portfolioStatus }}</span>
                        </div>
                        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                            <span>{{ $portfolioStatusText }}</span>
                            <span class="font-bold text-emerald-700">Sesuai SLA</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================== -->
            <!-- 3. HANDOVER PENDING ALERT BANNER (IF ANY)                  -->
            <!-- ========================================================== -->
            @if($handoverPendingCount > 0 || $handoverConditionalCount > 0)
                <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50/70 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-xs">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-xs font-bold text-amber-950 uppercase tracking-wide">Pemberitahuan Gatekeeper Handover Proyek</h4>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-200/80 text-amber-900">{{ $handoverPendingCount }} Menunggu Review</span>
                            </div>
                            <p class="text-xs text-amber-800 mt-1 leading-relaxed">
                                Terdapat <strong class="text-amber-950 font-bold">{{ $handoverPendingCount }}</strong> berkas serah terima dari Sales yang siap diverifikasi PMO, dan <strong class="text-amber-950 font-bold">{{ $handoverConditionalCount }}</strong> berkas revisi bersyarat.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('pmo.implementations.index', ['tab' => 'incoming']) }}" 
                       class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-xl transition shadow-xs whitespace-nowrap self-start sm:self-center flex items-center gap-2">
                        <span>Tinjau Antrean Handover</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- 4. MAIN WORKSPACE: TASK SHARE (LEFT) & YOUR PROJECTS (RIGHT)-->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                {{-- LEFT COLUMN: Task Share by Project (Donut Chart & Breakdown) --}}
                <div class="lg:col-span-5 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/90 shadow-2xs space-y-5">
                    <div class="border-b border-slate-100 pb-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900 tracking-tight">Task Share by Project</h3>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">
                                {{ $totalTasksCount }} Total Tasks
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Porsi alokasi tugas teknis per proyek</p>
                    </div>

                    {{-- Chart Canvas --}}
                    <div style="height: 220px;" class="flex items-center justify-center relative">
                        <canvas id="pmoTaskShareChart"></canvas>
                    </div>

                    {{-- Task Breakdown List --}}
                    <div class="space-y-2.5 pt-2 border-t border-slate-100">
                        @forelse($taskShareList as $item)
                            <div class="flex items-center justify-between text-xs py-1.5 px-2 rounded-xl hover:bg-slate-50 transition">
                                <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                    <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $item['color'] }}"></span>
                                    <div class="truncate">
                                        <div class="font-bold text-slate-800 truncate" title="{{ $item['name'] }}">{{ $item['name'] }}</div>
                                        <div class="text-[10.5px] text-slate-400 truncate">{{ $item['client'] }}</div>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-extrabold text-slate-800">{{ $item['tasks_count'] }} Tasks</span>
                                    <span class="text-[10.5px] text-slate-400 block font-semibold">({{ $item['percentage'] }}%)</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs font-medium">
                                Belum ada task yang teralokasi pada proyek aktif.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- RIGHT COLUMN: Your Projects (List of Project Cards) --}}
                <div class="lg:col-span-7 space-y-4">
                    <div class="flex items-center justify-between pb-1">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D]"></span>
                                Your Projects
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">Daftar portofolio implementasi aktif &amp; kontrol progres EV</p>
                        </div>
                        <a href="{{ route('pmo.implementations.index') }}" 
                           class="text-xs font-bold text-[#8F0A0D] hover:underline inline-flex items-center gap-1 cursor-pointer">
                            <span>Click to view details</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>

                    {{-- List of Projects --}}
                    <div class="space-y-3.5">
                        @forelse($yourProjects as $p)
                            <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-red-200 transition-all flex flex-col justify-between group">
                                
                                {{-- Card Top Row: Days Span badge & Health status --}}
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ $p['days_span'] ?? 0 }} Days Span
                                        </span>
                                        @if(($p['sla_tier'] ?? '') !== '')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-100 text-slate-700">
                                                SLA: {{ $p['sla_tier'] }}
                                            </span>
                                        @endif
                                    </div>

                                    <div>
                                        @if($p['health_status'] === 'On-Track')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                On-Track
                                            </span>
                                        @elseif($p['health_status'] === 'At-Risk')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                At-Risk
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10.5px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                Delayed
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Card Middle: Project Title & Client & Spend --}}
                                <div class="mt-2.5">
                                    <h4 class="text-[15px] font-bold text-slate-900 group-hover:text-[#8F0A0D] transition-colors leading-snug">
                                        {{ $p['name'] }}
                                    </h4>
                                    <div class="text-[12px] text-slate-500 mt-1 flex flex-wrap items-center gap-2">
                                        <span class="font-semibold text-slate-700">{{ $p['client'] }}</span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span>{{ $p['division'] }}</span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span>PM: <strong class="text-slate-700 font-semibold">{{ $p['pm'] }}</strong></span>
                                    </div>
                                    <div class="mt-2 text-xs font-semibold text-slate-500">
                                        <span class="text-slate-400 font-normal">Spend:</span>
                                        <span class="text-slate-900 font-bold">{{ $p['contract_value_formatted'] ?? 'Rp 0' }}</span>
                                    </div>
                                </div>

                                {{-- Card Bottom Row: Actual EV (Progress) & Link --}}
                                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3 flex-1 min-w-0 max-w-[260px]">
                                        <span class="text-[11.5px] font-bold text-slate-700 shrink-0">
                                            Actual EV: <span class="text-emerald-700 font-extrabold">{{ $p['progress'] }}%</span>
                                        </span>
                                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-300" 
                                                 style="background: linear-gradient(90deg, #8F0A0D, #E11D48); width: {{ $p['progress'] }}%"></div>
                                        </div>
                                    </div>

                                    <a href="{{ route('projects.show', $p['id']) }}" 
                                       class="text-xs font-bold text-[#8F0A0D] hover:text-[#B81525] hover:underline inline-flex items-center gap-1 shrink-0">
                                        <span>View Dashboard</span>
                                        <span class="text-sm">&rarr;</span>
                                    </a>
                                </div>

                            </div>
                        @empty
                            <div class="bg-white p-8 rounded-2xl border border-slate-200/90 text-center text-slate-400 text-xs font-medium">
                                Belum ada proyek dalam portofolio Anda.
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
        // Chart: Task Share by Project (Donut Chart)
        const taskShareData = @json($taskShareChartData ?? ['labels' => [], 'counts' => [], 'colors' => []]);
        const ctxShare = document.getElementById('pmoTaskShareChart');
        if (ctxShare && taskShareData.labels && taskShareData.labels.length > 0) {
            new Chart(ctxShare.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: taskShareData.labels,
                    datasets: [{
                        data: taskShareData.counts,
                        backgroundColor: taskShareData.colors,
                        borderWidth: 2,
                        borderColor: '#FFFFFF',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '66%',
                    plugins: {
                        legend: {
                            display: false // Displayed in the custom clean list below chart
                        },
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
