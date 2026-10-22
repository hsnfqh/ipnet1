@extends('layouts.app')

@section('title', 'Dashboard Proyek & Kurva S - ' . $project->name)

@push('styles')
<style>
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
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" 
     x-data="{ 
         sCurveMode: 'weekly',
         dailyPage: 1,
         dailyPerPage: 10,
         totalDaysCount: {{ $totalDays ?? 30 }},
         get totalDailyPages() {
             return Math.max(1, Math.ceil(this.totalDaysCount / this.dailyPerPage));
         },
         showProgressModal: false, 
         activeWeek: 1, 
         activeActual: 0, 
         activeNotes: '',
         activePlan: 0,
         activeRange: '',
         showDailyProgressModal: false,
         activeDay: 1,
         activeDayActual: 0,
         activeDayPlan: 0,
         activeDayNotes: '',
         activeDayDate: '',
         showDivisionModal: false,
         showAssignTeamModal: false,
         showQuickTaskModal: false,
         selectedEngineerId: '',
         openProgressModal(week, actual, plan, notes, range) {
             this.activeWeek = week;
             this.activeActual = actual !== null ? actual : 0;
             this.activePlan = plan;
             this.activeNotes = notes || '';
             this.activeRange = range;
             this.showProgressModal = true;
         },
         openDailyProgressModal(day, actual, plan, notes, date) {
             this.activeDay = day;
             this.activeDayActual = actual !== null ? actual : 0;
             this.activeDayPlan = plan;
             this.activeDayNotes = notes || '';
             this.activeDayDate = date;
             this.showDailyProgressModal = true;
         },
         switchSCurveMode(mode) {
             this.sCurveMode = mode;
             if (window.renderSCurveChart) {
                 window.renderSCurveChart(mode);
             }
         }
     }" 
     x-cloak>
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Project Dashboard & Kurva S'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">
            
            {{-- Flash Alert Notification --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-2xs">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <!-- ========================================================== -->
            <!-- 1. PROJECT HEADER & DETIL LENGKAP DIVISI & PERIODE          -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">
                    
                    {{-- Left Title & Badges --}}
                    <div class="space-y-2 flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="{{ route('pmo.implementations.index') }}" 
                               class="text-xs font-bold text-[#8F0A0D] hover:underline inline-flex items-center gap-1 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                                </svg>
                                <span>Kembali ke Portofolio PMO</span>
                            </a>
                            <span class="text-slate-300">&bull;</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                Total {{ $totalProjectDays ?? $daysSpan }} Hari ({{ $totalProjectWeeks ?? 8 }} Minggu)
                            </span>
                            @if($healthStatus === 'Healthy')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    On-Track
                                </span>
                            @elseif($healthStatus === 'At-Risk')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    At-Risk
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Delayed
                                </span>
                            @endif
                        </div>
                        
                        <h1 class="text-[20px] sm:text-[23px] font-extrabold text-[#1E293B] tracking-tight leading-snug">
                            {{ $project->name }}
                        </h1>
                        
                        {{-- Meta Chips: Client, Periode Tanggal, Divisi, PM --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 pt-2 text-xs">
                            
                            {{-- Chip 1: Klien & Lokasi --}}
                            <div class="p-2.5 bg-slate-50/80 rounded-xl border border-slate-200/80 flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-slate-600 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Klien Proyek</span>
                                    <span class="font-bold text-slate-800 truncate block">{{ $project->client ?: 'Client IPNET' }}</span>
                                </div>
                            </div>

                            {{-- Chip 2: Periode Tanggal Proyek (Dari Berapa s/d Berapa) --}}
                            <div class="p-2.5 bg-slate-50/80 rounded-xl border border-slate-200/80 flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-blue-600 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Periode Tanggal Proyek</span>
                                    <span class="font-bold text-slate-800 truncate block" title="{{ $projectTimelineText }}">
                                        {{ $projectTimelineText }}
                                    </span>
                                </div>
                            </div>

                            {{-- Chip 3: Divisi Pelaksana (Tampilan Rapih & Opsi Penetapan) --}}
                            <div class="p-2.5 bg-slate-50/80 rounded-xl border border-slate-200/80 flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-purple-600 flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Divisi Pelaksana</span>
                                        @if($project->division)
                                            <span class="font-bold text-slate-800 truncate block">{{ $project->division->name }}</span>
                                        @else
                                            <span class="font-bold text-amber-700 truncate block">Belum Ditetapkan</span>
                                        @endif
                                    </div>
                                </div>
                                <button type="button" 
                                        @click="showDivisionModal = true" 
                                        class="px-2 py-1 rounded-lg text-[10.5px] font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition shrink-0">
                                    {{ $project->division ? 'Ubah' : '+ Tetapkan' }}
                                </button>
                            </div>

                            {{-- Chip 4: Project Manager --}}
                            <div class="p-2.5 bg-slate-50/80 rounded-xl border border-slate-200/80 flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Project Manager</span>
                                    <span class="font-bold text-slate-800 truncate block">{{ $project->pm ? $project->pm->name : 'Rizki' }}</span>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Right Action Buttons --}}
                    <div class="flex items-center gap-2.5 shrink-0 flex-wrap">
                        <a href="{{ route('pmo.implementations.index') }}" 
                           class="px-4 py-2 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white text-xs font-bold transition shadow-2xs flex items-center gap-2 active:scale-95">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span>Daftar Proyek</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 2. METRIC SUMMARY CARDS                                    -->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Metric 1: Realisasi Pengeluaran PM --}}
                <div class="ipnet-metric-card border-l-4 border-l-[#8F0A0D] flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Total Expenditure</span>
                            <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[20px] font-extrabold text-[#1E293B] tracking-tight">
                            Rp {{ number_format($totalSpend, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-[#64748B] mt-2.5 pt-2 border-t border-slate-100 font-medium">
                        <span>Serapan Anggaran</span>
                        <span class="font-bold text-slate-800">{{ $spendPercentage }}% Terpakai</span>
                    </div>
                </div>

                {{-- Metric 2: Sisa Waktu Pelaksanaan (Project Countdown) --}}
                <div class="ipnet-metric-card border-l-4 border-l-slate-700 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Sisa Waktu Proyek</span>
                            <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="text-[20px] font-extrabold text-[#1E293B] tracking-tight">
                            @if(($daysRemaining ?? 0) > 0)
                                {{ number_format($daysRemaining, 0) }} <span class="text-xs font-semibold text-slate-500">Hari Tersisa</span>
                            @elseif(($daysRemaining ?? 0) === 0)
                                <span class="text-amber-600">Hari Terakhir</span>
                            @else
                                <span class="text-rose-600">{{ number_format(abs($daysRemaining ?? 0), 0) }} Hari Terlewat</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-[#64748B] mt-2.5 pt-2 border-t border-slate-100 font-medium">
                        <span>Minggu ke-{{ $currentWeek }} dari {{ $totalWeeks }} Minggu</span>
                        <span class="font-bold text-slate-800">{{ number_format($daysSpan, 0) }} Hari Total</span>
                    </div>
                </div>

                {{-- Metric 3: Total Tugas & Actual EV --}}
                <div class="ipnet-metric-card border-l-4 border-l-blue-600 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Workload Proyek</span>
                            <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-[20px] font-extrabold text-[#1E293B] tracking-tight">{{ $totalTasks }}</span>
                            <span class="text-xs text-slate-500 font-medium">Tasks</span>
                            <span class="text-xs font-bold text-slate-800 ml-auto">Actual: {{ number_format($currentProgressPercent, 1) }}%</span>
                        </div>
                    </div>
                    <div class="mt-2.5 pt-2 border-t border-slate-100">
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full transition-all duration-300" style="width: {{ $currentProgressPercent }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Metric 4: Status Kinerja Jadwal (Schedule Status Kurva S) --}}
                @php
                    $isStatusOnTrack = ($currentVariance ?? 0) >= -2.0;
                    $isStatusAtRisk = ($currentVariance ?? 0) < -2.0 && ($currentVariance ?? 0) >= -10.0;
                @endphp
                <div class="ipnet-metric-card border-l-4 {{ $isStatusOnTrack ? 'border-l-emerald-600' : ($isStatusAtRisk ? 'border-l-amber-500' : 'border-l-rose-600') }} flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Status Jadwal (Kurva S)</span>
                            <div class="w-7 h-7 rounded-lg {{ $isStatusOnTrack ? 'bg-emerald-50 text-emerald-600' : ($isStatusAtRisk ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }} flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full {{ $isStatusOnTrack ? 'bg-emerald-500' : ($isStatusAtRisk ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                            <span class="text-[20px] font-extrabold text-[#1E293B] tracking-tight">
                                {{ $isStatusOnTrack ? 'On-Track' : ($isStatusAtRisk ? 'At-Risk' : 'Delayed') }}
                            </span>
                            <span class="text-xs font-bold {{ $isStatusOnTrack ? 'text-emerald-700' : ($isStatusAtRisk ? 'text-amber-700' : 'text-rose-700') }}">
                                ({{ ($currentVariance ?? 0) >= 0 ? '+' : '' }}{{ number_format($currentVariance ?? 0, 1) }}%)
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-[#64748B] mt-2.5 pt-2 border-t border-slate-100 font-medium">
                        <span>Target: {{ number_format($planValAtCurrentWeek ?? 0, 1) }}%</span>
                        <span class="font-bold text-slate-800">
                            Aktual: {{ number_format($currentProgressPercent, 1) }}%
                        </span>
                    </div>
                </div>

            </div>

            <!-- ========================================================== -->
            <!-- 3. KURVA S: PLAN KUMULATIF VS ACTUAL KUMULATIF (MINGGUAN / HARIAN) -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 pb-3 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[12px] font-bold inline-flex items-center uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> MONITORING IMPLEMENTASI PMO
                        </p>
                        <h2 class="text-[17px] font-bold text-[#1E293B]">
                            <span x-text="sCurveMode === 'weekly' ? 'Plan Kumulatif Mingguan VS Actual Kumulatif Mingguan' : 'Plan Kumulatif Harian VS Actual Kumulatif Harian'">Plan Kumulatif Mingguan VS Actual Kumulatif Mingguan</span>
                        </h2>
                        <p class="text-xs text-[#64748B] mt-0.5" x-show="sCurveMode === 'weekly'">
                            Rentang Evaluasi: <strong>Minggu ke-0 s/d Minggu ke-{{ $totalWeeks }}</strong> (Periode: {{ $projectTimelineText }})
                        </p>
                        <p class="text-xs text-[#64748B] mt-0.5" x-show="sCurveMode === 'daily'" style="display: none;">
                            Rentang Evaluasi: <strong>Hari ke-1 s/d Hari ke-{{ $totalDays }}</strong> (Periode: {{ $projectTimelineText }})
                        </p>
                    </div>

                    {{-- Mode Switcher & Legend --}}
                    <div class="flex items-center gap-3 flex-wrap">
                        {{-- Segmented Mode Toggle Pill --}}
                        <div class="inline-flex items-center p-1 rounded-xl bg-slate-100 border border-slate-200">
                            <button type="button" 
                                    @click="switchSCurveMode('weekly')" 
                                    :class="sCurveMode === 'weekly' ? 'bg-[#8F0A0D] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                    class="px-3 py-1 text-xs rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>Mingguan</span>
                            </button>
                            <button type="button" 
                                    @click="switchSCurveMode('daily')" 
                                    :class="sCurveMode === 'daily' ? 'bg-[#8F0A0D] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                    class="px-3 py-1 text-xs rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Harian</span>
                            </button>
                        </div>

                        {{-- Legend Pills --}}
                        <div class="flex items-center gap-3 pl-2 border-l border-slate-200">
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                                <span class="w-3.5 h-1 rounded-full bg-[#2563EB]"></span>
                                <span>Plan Kumulatif</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                                <span class="w-3.5 h-1 rounded-full bg-[#8F0A0D]"></span>
                                <span>Actual Kumulatif</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- S-Curve Grid: Left Metrics & Right Chart Area --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-center">
                    
                    {{-- Left Metrics Box --}}
                    <div class="lg:col-span-4 bg-slate-50/80 rounded-xl p-4 sm:p-5 border border-slate-200/90 space-y-3.5">
                        <div class="border-b border-slate-200 pb-2.5">
                            <span class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wider">Ringkasan Status Kurva</span>
                            <h3 class="text-xs font-bold text-slate-800 mt-0.5">Indikator Kinerja Pelaksanaan</h3>
                        </div>

                        {{-- Item 1: Progress Pekerjaan --}}
                        <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-2xs">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-600">Progress Pekerjaan</span>
                                <span x-show="sCurveMode === 'weekly'" class="px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    Minggu ke-{{ $currentWeek }}
                                </span>
                                <span x-show="sCurveMode === 'daily'" style="display: none;" class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Hari ke-{{ $currentDay }} dari {{ $totalDays }}
                                </span>
                            </div>
                            <div class="mt-2 flex items-baseline justify-between">
                                <span class="text-[22px] font-black text-[#1E293B]">{{ number_format($currentProgressPercent, 2) }}%</span>
                                <span class="text-[11px] text-slate-400 font-semibold">dari 100% target</span>
                            </div>
                        </div>

                        {{-- Item 2: Total Expenditure --}}
                        <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-2xs">
                            <div class="text-xs font-bold text-slate-600">Total Expenditure</div>
                            <div class="mt-1.5 text-[17px] font-black text-[#8F0A0D]">
                                Rp {{ number_format($totalSpend, 0, ',', '.') }}
                            </div>
                            <div class="text-[10.5px] text-slate-400 mt-0.5">
                                Realisasi biaya tercatat oleh PM
                            </div>
                        </div>

                        {{-- Item 3: Tanggal Update --}}
                        <div class="p-3 bg-white rounded-xl border border-slate-200 shadow-2xs flex items-center justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Tanggal Update</span>
                                <span class="text-xs font-bold text-slate-800">{{ $sCurveUpdateDate }}</span>
                            </div>
                            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Right Chart Area: S-Curve Dual Line Chart --}}
                    <div class="lg:col-span-8 bg-white rounded-xl p-3 sm:p-4 border border-slate-200">
                        <div class="w-full" style="height: 310px;">
                            <canvas id="sCurveDualLineChart"></canvas>
                        </div>
                        <div class="text-center mt-1 text-[11px] text-slate-400 font-semibold tracking-wider uppercase">
                            <span x-show="sCurveMode === 'weekly'">Sumbu Horisontal: Minggu ke-0 s/d Minggu ke-{{ $totalWeeks }} (Rentang Tanggal Proyek: {{ $projectTimelineText }})</span>
                            <span x-show="sCurveMode === 'daily'" style="display: none;">Sumbu Horisontal: Hari ke-1 s/d Hari ke-{{ $totalDays }} (Rentang Tanggal Proyek: {{ $projectTimelineText }})</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ========================================================== -->
            <!-- 3.5 TABEL TRACKING & INPUT PROGRES (MINGGUAN & HARIAN)     -->
            <!-- ========================================================== -->
            <div class="ipnet-card p-5 sm:p-6 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-[#E2E8F0]">
                    <div>
                        <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">
                            <span class="ipnet-badge-dot"></span> TATA KELOLA PMO
                        </p>
                        <h3 class="text-[16px] font-bold text-[#1E293B]">
                            <span x-text="sCurveMode === 'weekly' ? 'Tabel Progres Mingguan Kurva S (Weekly S-Curve Tracking)' : 'Tabel Progres Harian Kurva S (Daily S-Curve Tracking)'">Tabel Progres Mingguan Kurva S</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            <span x-show="sCurveMode === 'weekly'">Rincian target mingguan, rekomendasi penyelesaian tugas teknis, dan input progres aktual resmi oleh PM.</span>
                            <span x-show="sCurveMode === 'daily'" style="display: none;">Rincian target harian (Hari 1 s/d {{ $totalDays }}), status lapangan, dan update progres harian oleh PM.</span>
                        </p>
                    </div>

                    {{-- Mode Toggle Tab --}}
                    <div class="inline-flex items-center p-1 rounded-xl bg-slate-100 border border-slate-200 shrink-0">
                        <button type="button" 
                                @click="switchSCurveMode('weekly')" 
                                :class="sCurveMode === 'weekly' ? 'bg-[#8F0A0D] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="px-3 py-1 text-xs rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Mode Mingguan</span>
                        </button>
                        <button type="button" 
                                @click="switchSCurveMode('daily')" 
                                :class="sCurveMode === 'daily' ? 'bg-[#8F0A0D] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="px-3 py-1 text-xs rounded-lg transition-all flex items-center gap-1.5 cursor-pointer">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Mode Harian</span>
                        </button>
                    </div>
                </div>

                {{-- TABEL MINGGUAN --}}
                <div x-show="sCurveMode === 'weekly'" class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-3">Minggu</th>
                                <th class="py-3 px-3">Rentang Tanggal</th>
                                <th class="py-3 px-3">Target Rencana (Plan)</th>
                                <th class="py-3 px-3">Realisasi Aktual</th>
                                <th class="py-3 px-3">Deviasi</th>
                                <th class="py-3 px-3">Task Lapangan</th>
                                <th class="py-3 px-3">Catatan PM</th>
                                <th class="py-3 px-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($weeklyRecords as $rec)
                                <tr class="hover:bg-slate-50/80 transition {{ $rec['is_current'] ? 'bg-amber-50/40' : '' }}">
                                    
                                    {{-- Kolom Minggu --}}
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-slate-900 text-[12.5px]">Minggu {{ $rec['week'] }}</span>
                                            @if($rec['is_current'])
                                                <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-[#8F0A0D] text-white">
                                                    Berjalan
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Kolom Rentang Tanggal --}}
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <span class="font-semibold text-slate-700">{{ $rec['date_range'] }}</span>
                                    </td>

                                    {{-- Kolom Target Plan --}}
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        <span class="font-bold text-blue-700">{{ number_format($rec['plan_percent'], 2) }}%</span>
                                    </td>

                                    {{-- Kolom Realisasi Aktual --}}
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        @if($rec['actual_percent'] !== null)
                                            <div class="flex items-center gap-1.5">
                                                <span class="font-extrabold text-[#8F0A0D] text-[12.5px]">
                                                    {{ number_format($rec['actual_percent'], 2) }}%
                                                </span>
                                                @if($rec['is_recorded'])
                                                    <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200" title="Diinput manual oleh PM">
                                                        PM Validated
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-slate-400 font-medium italic">Belum Berjalan</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Deviasi (Variance) --}}
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        @if($rec['variance'] !== null)
                                            @if($rec['variance'] >= 0)
                                                <span class="text-emerald-700 font-bold text-[11px] bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                                    +{{ number_format($rec['variance'], 2) }}%
                                                </span>
                                            @else
                                                <span class="text-rose-700 font-bold text-[11px] bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                                                    {{ number_format($rec['variance'], 2) }}%
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Rekomendasi Task --}}
                                    <td class="py-3 px-3 whitespace-nowrap">
                                        @if($rec['tasks_count'] > 0)
                                            <span class="px-2 py-0.5 rounded text-[10.5px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                                {{ $rec['tasks_count'] }} Task Tuntas
                                            </span>
                                        @else
                                            <span class="text-slate-400 text-[11px]">0 Task</span>
                                        @endif
                                    </td>

                                    {{-- Kolom Catatan PM --}}
                                    <td class="py-3 px-3 max-w-[220px]">
                                        <span class="text-slate-600 text-[11.5px] truncate block" title="{{ $rec['notes'] ?? '-' }}">
                                            {{ $rec['notes'] ?: '-' }}
                                        </span>
                                    </td>

                                    {{-- Kolom Aksi --}}
                                    <td class="py-3 px-3 text-right whitespace-nowrap">
                                        <button type="button" 
                                                @click="openProgressModal({{ $rec['week'] }}, {{ $rec['actual_percent'] ?? 0 }}, {{ $rec['plan_percent'] }}, '{{ addslashes($rec['notes'] ?? '') }}', '{{ $rec['date_range'] }}')"
                                                class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                                            Update Progres
                                        </button>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- TABEL HARIAN --}}
                <div x-show="sCurveMode === 'daily'" style="display: none;" class="space-y-4">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50/70 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="py-3 px-3">Hari</th>
                                    <th class="py-3 px-3">Tanggal &amp; Hari</th>
                                    <th class="py-3 px-3">Target Rencana (Plan)</th>
                                    <th class="py-3 px-3">Realisasi Aktual</th>
                                    <th class="py-3 px-3">Deviasi</th>
                                    <th class="py-3 px-3">Task Lapangan</th>
                                    <th class="py-3 px-3">Catatan PM</th>
                                    <th class="py-3 px-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($dailyRecords as $idx => $drec)
                                    <tr x-show="Math.ceil(({{ $idx }} + 1) / dailyPerPage) === dailyPage" 
                                        class="hover:bg-slate-50/80 transition {{ $drec['is_current'] ? 'bg-amber-50/40' : '' }}">
                                        
                                        {{-- Kolom Hari --}}
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <span class="font-extrabold text-slate-900 text-[12.5px]">Hari {{ $drec['day'] }}</span>
                                                @if($drec['is_current'])
                                                    <span class="px-2 py-0.2 rounded-full text-[10px] font-bold bg-[#8F0A0D] text-white">
                                                        Hari Ini
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        {{-- Kolom Tanggal & Hari --}}
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            <span class="font-semibold text-slate-700">{{ $drec['date'] }}</span>
                                            <span class="text-[11px] text-slate-400 block font-normal">{{ $drec['day_name'] }}</span>
                                        </td>

                                        {{-- Kolom Target Plan --}}
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            <span class="font-bold text-blue-700">{{ number_format($drec['plan_percent'], 2) }}%</span>
                                        </td>

                                        {{-- Kolom Realisasi Aktual --}}
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            @if($drec['actual_percent'] !== null)
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-extrabold text-[#8F0A0D] text-[12.5px]">
                                                        {{ number_format($drec['actual_percent'], 2) }}%
                                                    </span>
                                                    @if($drec['is_recorded'])
                                                        <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200" title="Diinput manual oleh PM">
                                                            PM Validated
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-slate-400 font-medium italic">Belum Berjalan</span>
                                            @endif
                                        </td>

                                        {{-- Kolom Deviasi (Variance) --}}
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            @if($drec['variance'] !== null)
                                                @if($drec['variance'] >= 0)
                                                    <span class="text-emerald-700 font-bold text-[11px] bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                                                        +{{ number_format($drec['variance'], 2) }}%
                                                    </span>
                                                @else
                                                    <span class="text-rose-700 font-bold text-[11px] bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">
                                                        {{ number_format($drec['variance'], 2) }}%
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>

                                        {{-- Kolom Rekomendasi Task --}}
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            @if($drec['tasks_count'] > 0)
                                                <span class="px-2 py-0.5 rounded text-[10.5px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                                    {{ $drec['tasks_count'] }} Task Tuntas
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[11px]">0 Task</span>
                                            @endif
                                        </td>

                                        {{-- Kolom Catatan PM --}}
                                        <td class="py-3 px-3 max-w-[220px]">
                                            <span class="text-slate-600 text-[11.5px] truncate block" title="{{ $drec['notes'] ?? '-' }}">
                                                {{ $drec['notes'] ?: '-' }}
                                            </span>
                                        </td>

                                        {{-- Kolom Aksi --}}
                                        <td class="py-3 px-3 text-right whitespace-nowrap">
                                            <button type="button" 
                                                    @click="openDailyProgressModal({{ $drec['day'] }}, {{ $drec['actual_percent'] ?? 0 }}, {{ $drec['plan_percent'] }}, '{{ addslashes($drec['notes'] ?? '') }}', '{{ $drec['date'] }} ({{ $drec['day_name'] }})')"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                                                Update Progres
                                            </button>
                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Daily Pagination Controls --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-slate-100 text-xs">
                        <div class="text-slate-500 font-medium">
                            Menampilkan <span class="font-bold text-slate-800" x-text="((dailyPage - 1) * dailyPerPage) + 1"></span> s/d 
                            <span class="font-bold text-slate-800" x-text="Math.min(dailyPage * dailyPerPage, {{ count($dailyRecords) }})"></span> 
                            dari <span class="font-bold text-slate-800">{{ count($dailyRecords) }} Hari</span>
                        </div>
                        <div class="flex items-center gap-1.5 self-end sm:self-auto">
                            <button type="button" 
                                    @click="dailyPage = Math.max(1, dailyPage - 1)" 
                                    :disabled="dailyPage === 1"
                                    :class="dailyPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-100 text-slate-700 cursor-pointer'"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 font-bold text-xs transition">
                                &larr; Sebelumnya
                            </button>
                            <span class="px-3 py-1.5 text-xs font-bold text-slate-700 bg-slate-50 rounded-lg border border-slate-200">
                                Halaman <span x-text="dailyPage"></span> / <span x-text="totalDailyPages"></span>
                            </span>
                            <button type="button" 
                                    @click="dailyPage = Math.min(totalDailyPages, dailyPage + 1)" 
                                    :disabled="dailyPage >= totalDailyPages"
                                    :class="dailyPage >= totalDailyPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-100 text-slate-700 cursor-pointer'"
                                    class="px-3 py-1.5 rounded-lg border border-slate-200 font-bold text-xs transition">
                                Berikutnya &rarr;
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================== -->
            <!-- 4. LOG CATEGORIZED EXPENSE & TASK WORKLOAD BREAKDOWN        -->
            <!-- ========================================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                {{-- LEFT COLUMN: Task Share by Engineer & Team Resource Allocation --}}
                <div class="lg:col-span-5 ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="border-b border-[#E2E8F0] pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                        <div>
                            <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">
                                <span class="ipnet-badge-dot"></span> DISTRIBUSI TIM
                            </p>
                            <h3 class="text-sm font-bold text-[#1E293B]">Beban Tugas Tim Lapangan</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Delegasi personil oleh Lead &amp; alokasi tugas oleh PM</p>
                        </div>
                        <div class="flex items-center gap-1.5 flex-wrap">
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                {{ count($assignedEngineers) }} Personil
                            </span>
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $totalTasks }} Tasks
                            </span>
                            
                            {{-- Button Lead Delegasikan Tim --}}
                            <button type="button" 
                                    @click="showAssignTeamModal = true" 
                                    class="text-[11px] font-bold px-2.5 py-1 rounded-lg bg-[#8F0A0D] hover:bg-[#73080A] text-white transition flex items-center gap-1 shadow-xs cursor-pointer"
                                    title="Tugaskan / kelola personil tim teknis proyek">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                <span>Tugaskan Tim (Lead)</span>
                            </button>
                        </div>
                    </div>

                    {{-- Status Banner Penugasan Lead --}}
                    @if(count($assignedEngineers) > 0)
                        <div class="p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-200/60 flex items-start justify-between gap-2">
                            <div class="flex items-start gap-2 min-w-0">
                                <div class="w-6 h-6 rounded-md bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <p class="text-[11.5px] font-bold text-emerald-900">Tim Resmi Ditetapkan Lead</p>
                                        <span class="text-[10px] px-1.5 py-0.2 rounded bg-emerald-200/60 text-emerald-800 font-semibold">
                                            {{ $teamDelegatedByName ?? 'Lead' }} &bull; {{ $teamDelegatedAt ?? '-' }}
                                        </span>
                                    </div>
                                    @if(!empty($leadNotes))
                                        <p class="text-[11px] text-emerald-800/80 mt-0.5 italic line-clamp-1">"{{ $leadNotes }}"</p>
                                    @endif
                                </div>
                            </div>
                            <button type="button" 
                                    @click="showAssignTeamModal = true" 
                                    class="text-[10.5px] font-bold text-emerald-700 hover:text-emerald-900 shrink-0 hover:underline">
                                Edit
                            </button>
                        </div>
                    @else
                        <div class="p-2.5 rounded-xl bg-amber-50/80 border border-amber-200/70 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0 animate-pulse"></span>
                                <p class="text-[11.5px] text-amber-900 font-medium truncate">
                                    <strong class="font-bold">Menunggu Lead:</strong> Belum ada personil lapangan yang ditugaskan.
                                </p>
                            </div>
                            <button type="button" 
                                    @click="showAssignTeamModal = true" 
                                    class="text-[10.5px] font-bold text-amber-800 hover:text-amber-950 shrink-0 underline">
                                Tugaskan
                            </button>
                        </div>
                    @endif

                    {{-- Visualisasi Donut Chart atau Status Penugasan --}}
                    @if($totalTasks > 0)
                        <div style="height: 185px;" class="flex items-center justify-center relative">
                            <canvas id="projectTaskShareChart"></canvas>
                        </div>
                    @else
                        <div class="py-5 px-4 flex flex-col items-center justify-center text-center border border-dashed border-slate-200 rounded-xl bg-slate-50/50 space-y-2">
                            <div class="w-10 h-10 rounded-full bg-red-50 text-[#8F0A0D] flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">
                                    {{ count($assignedEngineers) > 0 ? 'Tim Siap Menerima Tugas' : 'Belum Ada Task Terdaftar' }}
                                </p>
                                <p class="text-[11px] text-slate-400 max-w-[260px] mx-auto mt-0.5 leading-relaxed">
                                    {{ count($assignedEngineers) > 0 
                                        ? 'Lead telah menunjuk ' . count($assignedEngineers) . ' personil. Klik tombol di bawah untuk membagikan rincian tugas teknis (WBS).' 
                                        : 'Beban kerja engineer akan terhitung otomatis saat tugas teknis dibuat oleh PM.' }}
                                </p>
                            </div>
                            @if(count($assignedEngineers) > 0)
                                <button type="button" 
                                        @click="showQuickTaskModal = true" 
                                        class="mt-1 px-3 py-1.5 rounded-lg bg-[#8F0A0D] hover:bg-[#73080A] text-white text-[11px] font-bold shadow-xs transition flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    <span>Bagi Task ke Tim (PM)</span>
                                </button>
                            @endif
                        </div>
                    @endif

                    {{-- Breakdown List of Engineers & Workload Share --}}
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <div class="flex items-center justify-between text-[11px] font-bold text-slate-400 uppercase tracking-wider px-1">
                            <span>Personil Tim</span>
                            <div class="flex items-center gap-2">
                                <span>Alokasi Beban</span>
                                <button type="button" @click="showQuickTaskModal = true" class="text-[#8F0A0D] hover:underline normal-case font-bold">+ Tambah Task</button>
                            </div>
                        </div>

                        @forelse($taskShareList as $item)
                            <div class="p-3 rounded-xl border border-slate-200/90 bg-white hover:border-slate-300 transition space-y-2.5">
                                {{-- Top Row: Engineer Info & Task Stats --}}
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <span class="w-3 h-3 rounded-full shrink-0" style="background-color: {{ $item['color'] }}"></span>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <span class="font-bold text-slate-900 text-xs">{{ $item['name'] }}</span>
                                                @if($item['is_assigned'])
                                                    <span class="text-[9.5px] px-1.5 py-0.2 rounded-full bg-red-50 text-[#8F0A0D] font-extrabold border border-red-200/60 shrink-0">Tim Resmi</span>
                                                @endif
                                                @if(!empty($item['role']))
                                                    <span class="text-[10px] px-2 py-0.5 rounded-md bg-blue-50 text-blue-800 font-bold border border-blue-200/60 truncate">
                                                        Fokus: {{ $item['role'] }}
                                                    </span>
                                                @endif
                                            </div>
                                            <span class="text-[10.5px] text-slate-400 block">{{ $item['position'] ?? 'Field Engineer' }}</span>
                                        </div>
                                    </div>

                                    <div class="text-right shrink-0">
                                        @if($item['count'] > 0)
                                            <span class="font-extrabold text-slate-900 text-xs">{{ $item['count'] }} Tasks</span>
                                            <span class="text-[10px] text-slate-500 block font-medium">
                                                ({{ $item['percentage'] }}% &bull; {{ $item['completed'] }}/{{ $item['count'] }} Selesai)
                                            </span>
                                        @else
                                            <span class="text-[10.5px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200/60">
                                                0 Task
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Task List Assigned by PM to this Engineer (Rincian Pekerjaan) --}}
                                <div class="pt-2 border-t border-slate-100 space-y-1.5">
                                    <div class="flex items-center justify-between text-[10.5px] font-bold text-slate-500">
                                        <span class="flex items-center gap-1 text-slate-600">
                                            <svg class="w-3 h-3 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2"/></svg>
                                            Rincian Tugas dari PM (WBS):
                                        </span>
                                        <button type="button" 
                                                @click="selectedEngineerId = '{{ $item['id'] }}'; showQuickTaskModal = true;"
                                                class="text-[10.5px] font-bold text-[#8F0A0D] hover:underline flex items-center gap-0.5 cursor-pointer">
                                            <span>+ Beri Tugas</span>
                                        </button>
                                    </div>

                                    @if(!empty($item['tasks']) && count($item['tasks']) > 0)
                                        <div class="space-y-1">
                                            @foreach($item['tasks'] as $tsk)
                                                <div class="flex items-center justify-between p-1.5 px-2.5 rounded-lg bg-slate-50 border border-slate-200/60 text-xs hover:bg-slate-100/70 transition">
                                                    <div class="flex items-center gap-2 min-w-0 pr-2">
                                                        <span class="w-1.5 h-1.5 rounded-full {{ in_array($tsk['status'], ['Done', 'Completed']) ? 'bg-emerald-500' : 'bg-blue-500' }} shrink-0"></span>
                                                        <span class="font-bold text-slate-800 text-[11.5px] truncate" title="{{ $tsk['title'] }}">
                                                            {{ $tsk['title'] }}
                                                        </span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5 shrink-0">
                                                        @if($tsk['deadline'])
                                                            <span class="text-[10px] text-slate-400 font-medium hidden sm:inline">{{ $tsk['deadline'] }}</span>
                                                        @endif
                                                        <span class="text-[9.5px] font-bold px-1.5 py-0.5 rounded {{ in_array($tsk['status'], ['Done', 'Completed']) ? 'bg-emerald-100 text-emerald-800' : (in_array($tsk['status'], ['In Progress', 'in_progress']) ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-700') }}">
                                                            {{ $tsk['status'] }}
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="p-2 rounded-lg bg-amber-50/60 border border-amber-200/50 text-[11px] text-amber-800 flex items-center justify-between">
                                            <span>Belum ada pembagian tugas teknis dari PM untuk personil ini.</span>
                                            <button type="button" 
                                                    @click="selectedEngineerId = '{{ $item['id'] }}'; showQuickTaskModal = true;"
                                                    class="text-[10.5px] font-bold text-[#8F0A0D] underline cursor-pointer">
                                                Alokasikan Tugas
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-slate-400 text-xs font-medium border border-dashed border-slate-200 rounded-xl">
                                Belum ada personil tim yang ditugaskan oleh Lead.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- RIGHT COLUMN: Log Categorized Expense & Riwayat --}}
                <div class="lg:col-span-7 space-y-5">
                    
                    {{-- Form Box "Log Categorized Expense" (Clean, Professional, No Emojis) --}}
                    <div class="ipnet-card p-5 sm:p-6 space-y-4">
                        <div class="border-b border-[#E2E8F0] pb-3 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-[#1E293B]">Catat Realisasi Pengeluaran (Log Categorized Expense)</h3>
                                    <p class="text-xs text-slate-400 mt-0.5">Input realisasi biaya operasional &amp; belanja proyek oleh Project Manager</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-slate-700 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200">
                                Total: Rp {{ number_format($totalSpend, 0, ',', '.') }}
                            </span>
                        </div>

                        <form action="{{ route('pmo.projects.expenses.store', $project->id) }}" method="POST" class="space-y-3.5">
                            @csrf
                            
                            {{-- Field 1: Category --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Pengeluaran</label>
                                <select name="category" required 
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white hover:border-slate-300 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition outline-none cursor-pointer">
                                    <option value="Hardware & Perangkat">Hardware &amp; Perangkat</option>
                                    <option value="Transport & Operasional">Transport &amp; Akomodasi Lapangan</option>
                                    <option value="Konsumsi & Tim">Konsumsi &amp; Operasional Tim</option>
                                    <option value="Jasa Subkontraktor">Jasa Subkontraktor &amp; Vendor</option>
                                    <option value="Perizinan & Legal">Perizinan &amp; Legal</option>
                                    <option value="Others" selected>Others / Lain-lain</option>
                                </select>
                            </div>

                            {{-- Field 2: Amount (IDR) --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Biaya (IDR)</label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="number" 
                                           name="amount" 
                                           min="1" 
                                           step="1000"
                                           placeholder="Contoh: 1500000" 
                                           required 
                                           class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 bg-white hover:border-slate-300 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition outline-none">
                                </div>
                            </div>

                            {{-- Field 3: Description & Date --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi / No. Nota</label>
                                    <input type="text" 
                                           name="description" 
                                           placeholder="Keterangan pengeluaran..." 
                                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 bg-white hover:border-slate-300 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Transaksi</label>
                                    <input type="date" 
                                           name="expense_date" 
                                           value="{{ date('Y-m-d') }}"
                                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white hover:border-slate-300 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition outline-none cursor-pointer">
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <div class="pt-1">
                                <button type="submit" 
                                        class="w-full py-2.5 rounded-xl btn-ipnet-primary font-bold text-xs transition flex items-center justify-center gap-2 cursor-pointer shadow-xs">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span>Simpan Pengeluaran</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Riwayat Pengeluaran (Expense Logs Table) --}}
                    <div class="ipnet-card p-5 sm:p-6 space-y-3">
                        <div class="flex items-center justify-between pb-2.5 border-b border-[#E2E8F0]">
                            <h4 class="text-xs font-bold text-[#1E293B] uppercase tracking-wider">Riwayat Pengeluaran Proyek</h4>
                            <span class="text-xs font-semibold text-slate-400">{{ $expenses->count() }} Transaksi</span>
                        </div>

                        <div class="space-y-2">
                            @forelse($expenses as $exp)
                                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50/70 border border-slate-200/60 hover:bg-slate-50 transition">
                                    <div class="min-w-0 pr-3">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-[10.5px] font-bold bg-slate-200/80 text-slate-800">
                                                {{ $exp['category'] }}
                                            </span>
                                            <span class="text-[11px] text-slate-400">{{ $exp['expense_date'] }}</span>
                                        </div>
                                        <p class="text-xs font-semibold text-slate-800 mt-1 truncate">
                                            {{ $exp['description'] }}
                                        </p>
                                        <span class="text-[10.5px] text-slate-400 block">Dicatat oleh: {{ $exp['creator_name'] ?? 'PM' }}</span>
                                    </div>

                                    <div class="flex items-center gap-3 shrink-0">
                                        <span class="text-xs font-extrabold text-[#1E293B]">
                                            {{ $exp['formatted'] }}
                                        </span>
                                        <form action="{{ route('pmo.projects.expenses.destroy', [$project->id, $exp['id']]) }}" method="POST" onsubmit="return confirm('Hapus catatan pengeluaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition" 
                                                    title="Hapus Pengeluaran">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-slate-400 text-xs font-medium border border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                                    Belum ada catatan pengeluaran untuk proyek ini.
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- ========================================================== -->
    <!-- MODAL 1: UPDATE PROGRES MINGGUAN (PM INPUT)                -->
    <!-- ========================================================== -->
    <div x-show="showProgressModal" 
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-5 sm:p-6 space-y-4"
             @click.away="showProgressModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        Update Progres Minggu ke-<span x-text="activeWeek"></span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5" x-text="activeRange"></p>
                </div>
                <button type="button" @click="showProgressModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('pmo.projects.weekly_progress.update', $project->id) }}" method="POST" class="space-y-3.5">
                @csrf
                <input type="hidden" name="week" :value="activeWeek">

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-semibold">Target Rencana (Plan):</span>
                    <span class="font-extrabold text-blue-700 text-sm" x-text="activePlan + '%'"></span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Capaian Realisasi Aktual (%)</label>
                    <div class="relative">
                        <input type="number" 
                               name="actual" 
                               step="0.01" 
                               min="0" 
                               max="100" 
                               x-model="activeActual"
                               required 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-extrabold text-slate-900 focus:border-[#8F0A0D] outline-none">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">%</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Masukkan persentase pencapaian kumulatif pada akhir pekan ini.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Capaian / Kendala Lapangan</label>
                    <textarea name="notes" 
                              rows="2" 
                              x-model="activeNotes"
                              placeholder="Contoh: Instalasi rack selesai, konfigurasi router mencapai 80%..." 
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-[#8F0A0D] outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showProgressModal = false" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl btn-ipnet-primary transition shadow-xs">
                        Simpan Progres
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================== -->
    <!-- MODAL 1B: UPDATE PROGRES HARIAN (PM INPUT)                 -->
    <!-- ========================================================== -->
    <div x-show="showDailyProgressModal" 
         x-transition.opacity
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-5 sm:p-6 space-y-4"
             @click.away="showDailyProgressModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">
                        Update Progres Hari ke-<span x-text="activeDay"></span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5" x-text="activeDayDate"></p>
                </div>
                <button type="button" @click="showDailyProgressModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('pmo.projects.daily_progress.update', $project->id) }}" method="POST" class="space-y-3.5">
                @csrf
                <input type="hidden" name="day" :value="activeDay">

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 flex items-center justify-between text-xs">
                    <span class="text-slate-500 font-semibold">Target Rencana (Plan):</span>
                    <span class="font-extrabold text-blue-700 text-sm" x-text="activeDayPlan + '%'"></span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Capaian Realisasi Aktual (%)</label>
                    <div class="relative">
                        <input type="number" 
                               name="actual" 
                               step="0.01" 
                               min="0" 
                               max="100" 
                               x-model="activeDayActual"
                               required 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-extrabold text-slate-900 focus:border-[#8F0A0D] outline-none">
                        <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">%</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Masukkan persentase kumulatif penyelesaian proyek hingga hari ini.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Capaian / Pekerjaan Hari Ini</label>
                    <textarea name="notes" 
                              rows="2" 
                              x-model="activeDayNotes"
                              placeholder="Contoh: Penarikan kabel FO 200m tuntas, crimping RJ45..." 
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-[#8F0A0D] outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showDailyProgressModal = false" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl btn-ipnet-primary transition shadow-xs cursor-pointer">
                        Simpan Progres Harian
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================== -->
    <!-- MODAL 2: TETAPKAN / UBAH DIVISI PELAKSANA                  -->
    <!-- ========================================================== -->
    <div x-show="showDivisionModal" 
         x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-5 sm:p-6 space-y-4"
             @click.away="showDivisionModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Tetapkan Divisi Pelaksana</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pilih divisi teknis yang bertanggung jawab atas implementasi</p>
                </div>
                <button type="button" @click="showDivisionModal = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('pmo.projects.assign_division', $project->id) }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Divisi Teknis</label>
                    <select name="division_id" required 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none cursor-pointer">
                        <option value="">-- Pilih Divisi Pelaksana --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ $project->division_id == $div->id ? 'selected' : '' }}>
                                {{ $div->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showDivisionModal = false" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl btn-ipnet-primary transition shadow-xs">
                        Tetapkan Divisi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================== -->
    <!-- MODAL 3: DELEGASIKAN PERSONIL TIM LAPANGAN OLEH LEAD       -->
    <!-- ========================================================== -->
    <div x-show="showAssignTeamModal" 
         x-transition.opacity
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-5 sm:p-6 space-y-4 max-h-[90vh] overflow-y-auto"
             @click.away="showAssignTeamModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Delegasi Tim Lapangan (Lead)</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Tentukan personil engineer yang diterjunkan untuk proyek ini</p>
                    </div>
                </div>
                <button type="button" @click="showAssignTeamModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 text-xs text-slate-600 flex items-center justify-between">
                <div>
                    <span class="text-slate-400 block text-[10.5px]">Proyek Pelaksanaan:</span>
                    <strong class="text-slate-900 font-bold">{{ $project->name }}</strong>
                </div>
                <span class="px-2 py-1 rounded-md bg-white border border-slate-200 text-[#8F0A0D] font-bold text-[11px]">
                    {{ $project->division->name ?? 'Teknis' }}
                </span>
            </div>

            <form action="{{ route('pmo.projects.assign_team', $project->id) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-700">Pilih Engineer Lapangan (Multi-select)</label>
                        <span class="text-[11px] text-slate-400">Centang 1 atau lebih</span>
                    </div>

                    @php
                        $assignedMapById = collect($assignedEngineers)->keyBy('id');
                        $assignedIds = $assignedMapById->keys()->toArray();
                    @endphp

                    <div class="space-y-2 max-h-64 overflow-y-auto p-1.5 border border-slate-200 rounded-xl bg-slate-50/50">
                        @forelse($availableEngineers as $eng)
                            @php
                                $isAssigned = in_array($eng->id, $assignedIds);
                                $existingRole = $assignedMapById[$eng->id]['role'] ?? '';
                            @endphp
                            <div class="p-2.5 rounded-xl bg-white border border-slate-200/80 hover:border-[#8F0A0D]/50 transition space-y-1.5">
                                <label class="flex items-center justify-between cursor-pointer">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <input type="checkbox" 
                                               name="engineer_ids[]" 
                                               value="{{ $eng->id }}" 
                                               {{ $isAssigned ? 'checked' : '' }}
                                               class="w-4 h-4 rounded text-[#8F0A0D] focus:ring-[#8F0A0D] border-slate-300">
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 truncate">{{ $eng->name }}</p>
                                            <p class="text-[10.5px] text-slate-400 truncate">{{ $eng->position ?? 'Field Engineer' }}</p>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">
                                        {{ $eng->division->name ?? 'Teknis' }}
                                    </span>
                                </label>
                                
                                {{-- Input Peran / Tanggung Jawab Spesifik dari Lead --}}
                                <div class="pl-6.5">
                                    <input type="text" 
                                           name="roles[{{ $eng->id }}]" 
                                           value="{{ $existingRole }}"
                                           placeholder="Tugas/peran personil, misal: Staging APS / Penarikan Kabel..." 
                                           class="w-full px-2.5 py-1 rounded-lg border border-slate-200 text-[11px] text-slate-800 focus:border-[#8F0A0D] outline-none bg-slate-50/50 focus:bg-white transition">
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-center text-xs text-slate-400">
                                Tidak ada data engineer aktif pada divisi ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan / Arahan Khusus dari Lead (Opsional)</label>
                    <textarea name="notes" 
                              rows="2" 
                              placeholder="Contoh: Fokus pada instalasi core router dan konfigurasi VLAN minggu pertama..." 
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-[#8F0A0D] outline-none">{{ $leadNotes ?? '' }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showAssignTeamModal = false" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl btn-ipnet-primary transition shadow-xs cursor-pointer">
                        Simpan &amp; Tetapkan Tim Proyek
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================== -->
    <!-- MODAL 4: BUAT & DELEGASIKAN TASK TEKNIS (WBS) OLEH PM      -->
    <!-- ========================================================== -->
    <div x-show="showQuickTaskModal" 
         x-transition.opacity
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-lg w-full p-5 sm:p-6 space-y-4 max-h-[90vh] overflow-y-auto"
             @click.away="showQuickTaskModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Buat &amp; Alokasikan Tugas Teknis (PM)</h3>
                        <p class="text-xs text-slate-400 mt-0.5">PM membagi pekerjaan (WBS) ke personil yang ditunjuk Lead</p>
                    </div>
                </div>
                <button type="button" @click="showQuickTaskModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('pmo.projects.quick_task', $project->id) }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Judul Tugas / Rincian Pekerjaan (WBS)</label>
                    <input type="text" 
                           name="title" 
                           required 
                           placeholder="Contoh: Instalasi Rack &amp; Penarikan Kabel UTP / Konfigurasi Router" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-900 focus:border-[#8F0A0D] outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tugaskan ke Engineer</label>
                        <select name="engineer_id" x-model="selectedEngineerId" required 
                                class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none cursor-pointer">
                            <option value="">-- Pilih Engineer --</option>
                            @if(count($assignedEngineers) > 0)
                                <optgroup label="⭐ Tim Resmi Proyek (Ditugaskan Lead)">
                                    @foreach($assignedEngineers as $ae)
                                        <option value="{{ $ae['id'] }}">{{ $ae['name'] }} ({{ $ae['position'] ?? 'Engineer' }}){{ !empty($ae['role']) ? ' • Fokus: ' . $ae['role'] : '' }}</option>
                                    @endforeach
                                </optgroup>
                            @endif
                            <optgroup label="Semua Personil Divisi">
                                @foreach($availableEngineers as $eng)
                                    <option value="{{ $eng->id }}">{{ $eng->name }} ({{ $eng->position ?? 'Engineer' }})</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Prioritas Tugas</label>
                        <select name="priority" required 
                                class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none cursor-pointer">
                            <option value="High">High (Kritikal / Utama)</option>
                            <option value="Medium" selected>Medium (Standar)</option>
                            <option value="Low">Low (Dukungan / Pendukung)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Target Batas Waktu (Deadline Task)</label>
                    <input type="date" 
                           name="deadline" 
                           value="{{ $project->deadline ? $project->deadline->format('Y-m-d') : date('Y-m-d', strtotime('+14 days')) }}"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:border-[#8F0A0D] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi &amp; Instruksi Teknis PM (Opsional)</label>
                    <textarea name="description" 
                              rows="2.5" 
                              placeholder="Rincian instruksi pelaksanaan, checklist perangkat, atau port mapping yang perlu disiapkan..." 
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-[#8F0A0D] outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showQuickTaskModal = false" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl btn-ipnet-primary transition shadow-xs cursor-pointer">
                        Delegasikan Task ke Engineer
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. S-Curve Dual Line Chart (Plan Kumulatif vs Actual Kumulatif)
        const sCurveLabels = {!! json_encode($sCurveLabels ?? [0, 1, 2, 3, 4, 5, 6, 7, 8]) !!};
        const sCurvePlan = {!! json_encode($sCurvePlan ?? [0, 5.5, 14, 28.5, 51, 72.5, 86, 94.5, 100]) !!};
        const sCurveActual = {!! json_encode($sCurveActual ?? [0, 5, 19.82, null, null, null, null, null, null]) !!};

        const sCurveDailyLabels = {!! json_encode($sCurveDailyLabels ?? []) !!};
        const sCurveDailyPlan = {!! json_encode($sCurveDailyPlan ?? []) !!};
        const sCurveDailyActual = {!! json_encode($sCurveDailyActual ?? []) !!};

        const sCurveCanvas = document.getElementById('sCurveDualLineChart');
        let sCurveChartInstance = null;

        if (sCurveCanvas) {
            sCurveChartInstance = new Chart(sCurveCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: sCurveLabels,
                    datasets: [
                        {
                            label: 'Plan Kumulatif',
                            data: sCurvePlan,
                            borderColor: '#2563EB', // Blue line
                            backgroundColor: 'transparent',
                            borderWidth: 2.5,
                            tension: 0.35,
                            pointRadius: 3.5,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#2563EB',
                            pointBorderColor: '#FFFFFF',
                            pointBorderWidth: 1.5,
                            spanGaps: false
                        },
                        {
                            label: 'Actual Kumulatif',
                            data: sCurveActual,
                            borderColor: '#8F0A0D', // IPNET Red line
                            backgroundColor: 'transparent',
                            borderWidth: 3,
                            tension: 0.35,
                            pointRadius: 4.5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#8F0A0D',
                            pointBorderColor: '#FFFFFF',
                            pointBorderWidth: 2,
                            spanGaps: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = context.raw;
                                    if (val === null || val === undefined) return null;
                                    return ` ${context.dataset.label}: ${val.toFixed(2)}%`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Minggu Pelaksanaan (Minggu ke-0 s/d {{ $totalWeeks }})',
                                color: '#64748B',
                                font: { size: 11, weight: 'bold' }
                            },
                            grid: {
                                color: '#F1F5F9'
                            },
                            ticks: {
                                color: '#64748B',
                                font: { size: 11, weight: '600' }
                            }
                        },
                        y: {
                            min: 0,
                            max: 120,
                            ticks: {
                                stepSize: 20,
                                callback: function(value) {
                                    return value.toFixed(2) + '%';
                                },
                                color: '#64748B',
                                font: { size: 11, weight: '600' }
                            },
                            grid: {
                                color: '#E2E8F0',
                                borderDash: [2, 2]
                            }
                        }
                    }
                }
            });

            window.renderSCurveChart = function(mode) {
                if (!sCurveChartInstance) return;
                if (mode === 'daily') {
                    sCurveChartInstance.data.labels = sCurveDailyLabels;
                    sCurveChartInstance.data.datasets[0].data = sCurveDailyPlan;
                    sCurveChartInstance.data.datasets[1].data = sCurveDailyActual;
                    sCurveChartInstance.data.datasets[0].pointRadius = sCurveDailyLabels.length > 45 ? 1 : 2.5;
                    sCurveChartInstance.data.datasets[1].pointRadius = sCurveDailyLabels.length > 45 ? 1.5 : 3;
                    sCurveChartInstance.options.scales.x.title.text = 'Hari Pelaksanaan (Hari ke-1 s/d {{ $totalDays }})';
                } else {
                    sCurveChartInstance.data.labels = sCurveLabels;
                    sCurveChartInstance.data.datasets[0].data = sCurvePlan;
                    sCurveChartInstance.data.datasets[1].data = sCurveActual;
                    sCurveChartInstance.data.datasets[0].pointRadius = 3.5;
                    sCurveChartInstance.data.datasets[1].pointRadius = 4.5;
                    sCurveChartInstance.options.scales.x.title.text = 'Minggu Pelaksanaan (Minggu ke-0 s/d {{ $totalWeeks }})';
                }
                sCurveChartInstance.update();
            };
        }

        // 2. Task Share by Engineer Donut Chart
        const shareData = {!! json_encode($taskShareChartData ?? ['labels' => [], 'counts' => [], 'colors' => []]) !!};
        const taskCanvas = document.getElementById('projectTaskShareChart');
        if (taskCanvas && shareData.labels && shareData.labels.length > 0) {
            new Chart(taskCanvas.getContext('2d'), {
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
