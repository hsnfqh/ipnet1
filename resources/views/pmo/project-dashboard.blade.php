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
@php
    $authUser = $authUser ?? auth()->user();
    $isLeadUser = $isLeadUser ?? ($authUser && \App\Helpers\ScopeHelper::isTeamLeader($authUser));
    $isPmoUser = $isPmoUser ?? ($authUser && (\App\Helpers\ScopeHelper::isPmo($authUser) || ($project->pm_id && $authUser->id == $project->pm_id) || \App\Helpers\ScopeHelper::isExecutive($authUser)));
@endphp
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" 
     x-data="{ 
         activeMainTab: '{{ in_array(request('tab'), ['schedule', 'weekly_weight', 'actual_progress', 'scurve', 'expenses']) ? request('tab') : 'schedule' }}',
         sCurveMode: 'weekly',
         weeklyPage: {{ max(1, (int) ceil(($currentWeek ?? 1) / 6)) }},
         weeklyPerPage: 6,
         totalWeeksCount: {{ count($weeklyRecords ?? []) }},
         get totalWeeklyPages() {
             return Math.max(1, Math.ceil(this.totalWeeksCount / this.weeklyPerPage));
         },
         showProgressModal: false, 
         activeWeek: 1, 
         activeActual: '', 
         activeNotes: '',
         activePlan: '',
         activeRange: '',
         showClientModal: false,
         showTimelineModal: false,
         showDivisionModal: false,
         showAssignTeamModal: false,
         showQuickTaskModal: false,
         showImportExcelModal: false,
         selectedEngineerId: '',
         showEditTaskModal: false,
         editTaskId: null,
         editTaskTitle: '',
         editTaskDuration: 1,
         editTaskBobot: 0,
         editTaskProgress: 0,
         editTaskStatus: 'In Progress',
         editTaskPriority: 'Medium',
         editTaskEngineerId: '',
         editTaskDescription: '',
         openEditTaskModal(task) {
             this.editTaskId = task.id;
             this.editTaskTitle = task.title;
             this.editTaskDuration = task.duration_days ? parseInt(task.duration_days, 10) : 1;
             this.editTaskBobot = task.bobot;
             this.editTaskProgress = task.progress;
             this.editTaskStatus = task.status;
             this.editTaskPriority = task.priority || 'Medium';
             this.editTaskEngineerId = task.engineer_id || '';
             this.editTaskDescription = task.description || '';
             this.showEditTaskModal = true;
         },
         openProgressModal(week, actual, plan, notes, range) {
             this.activeWeek = week;
             this.activeActual = (actual !== null && actual !== undefined && actual !== 'null') ? actual : '';
             this.activePlan = (plan !== null && plan !== undefined && plan !== 'null') ? plan : '';
             this.activeNotes = notes || '';
             this.activeRange = range;
             this.showProgressModal = true;
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
            <div class="ipnet-card p-5 sm:p-6 space-y-4">
                {{-- Top Navigation & Action Bar --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    {{-- Left Breadcrumbs & Status Badges --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('pmo.implementations.index') }}" 
                           class="text-xs font-bold text-[#8F0A0D] hover:underline inline-flex items-center gap-1 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                            </svg>
                            <span>Kembali ke Portofolio PMO</span>
                        </a>
                        <span class="text-slate-300">&bull;</span>
                        @php
                            $estDuration = $project->handover_data['estimate_project_duration'] ?? null;
                            $isPpatkBadge = ($estDuration == 30) || str_contains(strtolower($project->name), 'ppat') || str_contains(strtolower($project->client ?? ''), 'ppat');
                        @endphp
                        @if($isPpatkBadge)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                Total {{ $totalProjectDays ?? 30 }} Hari ({{ $totalProjectWeeks ?? 5 }} Minggu Pelaksanaan) &bull; Kontrak s/d {{ \Carbon\Carbon::parse($project->deadline)->format('d M Y') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                Total {{ $totalProjectDays ?? $daysSpan }} Hari ({{ $totalProjectWeeks ?? 8 }} Minggu)
                            </span>
                            <a href="{{ route('pmo.projects.ppatk') }}" 
                               class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-300 hover:bg-amber-100 transition shadow-2xs" 
                               title="Buka Proyek PPATK">
                                <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <span>Buka Proyek PPATK (Peremajaan Router)</span>
                            </a>
                        @endif

                    </div>


                </div>

                {{-- Full-Width Title --}}
                <div>
                    <h1 class="text-[20px] sm:text-[23px] font-extrabold text-[#1E293B] tracking-tight leading-snug">
                        {{ $project->name }}
                    </h1>
                </div>

                {{-- Full-Width 4 Meta Chips: Client, Periode Tanggal, Divisi, PM --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1 text-xs">
                    
                    {{-- Chip 1: Klien & Lokasi --}}
                    <div class="p-2.5 bg-slate-50/80 rounded-xl border border-slate-200/80 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Klien Proyek</span>
                                <span class="font-bold text-slate-800 truncate block" title="{{ $project->client ?: 'Client IPNET' }}">{{ $project->client ?: 'Client IPNET' }}</span>
                                @php
                                    $picDisplay = $project->customer_pic_technical ?: ($project->customer_pic_business ?: ($project->handover_data['customer_pic'] ?? null));
                                @endphp
                                @if($picDisplay)
                                    <span class="text-[10px] text-slate-500 font-medium truncate block" title="{{ $picDisplay }}">PIC: {{ $picDisplay }}</span>
                                @endif
                            </div>
                        </div>
                        <button type="button" 
                                @click="showClientModal = true" 
                                title="Ubah Klien & PIC"
                                class="w-7 h-7 rounded-lg text-slate-500 hover:text-[#8F0A0D] bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 transition shrink-0 flex items-center justify-center cursor-pointer shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </div>

                    {{-- Chip 2: Periode Tanggal Proyek (Dari Berapa s/d Berapa) --}}
                    <div class="p-2.5 bg-slate-50/80 rounded-xl border border-slate-200/80 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-[#8F0A0D] flex items-center justify-center shrink-0">
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
                        <button type="button" 
                                @click="showTimelineModal = true" 
                                title="Ubah Periode Tanggal"
                                class="w-7 h-7 rounded-lg text-slate-500 hover:text-[#8F0A0D] bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 transition shrink-0 flex items-center justify-center cursor-pointer shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>
                    </div>

                    {{-- Chip 3: Divisi Pelaksana (Tampilan Rapih & Opsi Penetapan) --}}
                    <div class="p-2.5 bg-slate-50/80 rounded-xl border border-slate-200/80 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Tim Teknis Pelaksana</span>
                                @if(!empty($project->handover_data['assigned_teams']) && is_array($project->handover_data['assigned_teams']))
                                    <span class="font-bold text-slate-800 truncate block">{{ implode(' & ', $project->handover_data['assigned_teams']) }}</span>
                                @elseif($project->division)
                                    <span class="font-bold text-slate-800 truncate block">{{ preg_replace('/^Divisi\s+/i', 'Tim ', $project->division->name) }}</span>
                                @else
                                    <span class="font-bold text-amber-700 truncate block">Belum Ditetapkan</span>
                                @endif
                            </div>
                        </div>
                        <button type="button" 
                                @click="showDivisionModal = true" 
                                title="Ubah Tim Teknis"
                                class="w-7 h-7 rounded-lg text-slate-500 hover:text-[#8F0A0D] bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 transition shrink-0 flex items-center justify-center cursor-pointer shadow-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
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

                {{-- Metric 2: Durasi Proyek (Sesuai Kontrak / SPK) --}}
                <div class="ipnet-metric-card border-l-4 border-l-slate-700 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Durasi Project</span>
                            <div class="flex items-center gap-1.5">
                                @if($isPmoUser)
                                    <button type="button" 
                                            @click="showTimelineModal = true" 
                                            title="Ubah Durasi Proyek" 
                                            class="w-7 h-7 rounded-lg text-slate-500 hover:text-[#8F0A0D] bg-white hover:bg-red-50 border border-slate-200 hover:border-red-200 transition shrink-0 flex items-center justify-center cursor-pointer shadow-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                @endif
                                <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="text-[20px] font-extrabold text-[#1E293B] tracking-tight flex items-baseline justify-between">
                            <span>{{ number_format($totalProjectDays ?? $daysSpan, 0) }} <span class="text-xs font-semibold text-slate-500">Hari Total</span></span>
                            @if(($daysRemaining ?? 0) > 0)
                                <span class="text-xs font-bold text-emerald-600">({{ number_format($daysRemaining, 0) }} Hari Sisa)</span>
                            @elseif(($daysRemaining ?? 0) === 0)
                                <span class="text-xs font-bold text-amber-600">Hari Terakhir</span>
                            @else
                                <span class="text-xs font-bold text-rose-600">({{ number_format(abs($daysRemaining ?? 0), 0) }} Hari Lewat)</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-[11px] text-[#64748B] mt-2.5 pt-2 border-t border-slate-100 font-medium space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-normal">Rentang WBS:</span>
                            <strong class="text-slate-800 font-bold">{{ $wbsStartDate->format('d M') }} - {{ $wbsEndDate->format('d M Y') }}</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500 font-normal">Batas Kontrak SPK:</span>
                            <strong class="text-slate-800 font-bold">{{ $deadlineDate->format('d M Y') }}</strong>
                        </div>
                    </div>
                </div>

                {{-- Metric 3: Task Proyek & Status Workload --}}
                <div class="ipnet-metric-card border-l-4 border-l-blue-600 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Task Proyek</span>
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
                    <div class="mt-2.5 pt-2 border-t border-slate-100 text-[11px] text-[#64748B] font-medium space-y-1">
                        <div class="flex items-center justify-between">
                            <span>Task In Progress:</span>
                            <strong class="text-blue-700 font-bold">{{ $tasksInProgressCount }} task</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Task Completed :</span>
                            <strong class="text-emerald-700 font-bold">{{ $tasksCompletedCount }} task</strong>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Task Not started :</span>
                            <strong class="text-slate-600 font-bold">{{ $tasksNotStartedCount }} task</strong>
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
                        <span>Target: {{ $planValAtCurrentWeek !== null ? number_format($planValAtCurrentWeek, 1) . '%' : '-' }}</span>
                        <span class="font-bold text-slate-800">
                            Aktual: {{ number_format($currentProgressPercent, 1) }}%
                        </span>
                    </div>
                </div>

            </div>

                        <!-- ========================================================== -->
            <!-- 2.5 TAB NAVIGASI UTAMA DASHBOARD PROYEK (5 SHEET EXCEL)   -->
            <!-- ========================================================== -->
            <div class="flex items-center justify-between border-b border-slate-200 bg-white px-3 py-2 rounded-2xl shadow-2xs gap-3 flex-wrap">
                <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                    {{-- Tab 1: Schedule --}}
                    <button type="button" 
                            @click="activeMainTab = 'schedule'"
                            :class="activeMainTab === 'schedule' ? 'bg-[#8F0A0D] text-white font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-semibold hover:bg-slate-100'"
                            class="px-3.5 py-2 rounded-xl text-xs transition inline-flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Schedule</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                              :class="activeMainTab === 'schedule' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'">
                            {{ $totalTasks }}
                        </span>
                    </button>

                    {{-- Tab 2: Weekly Weight --}}
                    <button type="button" 
                            @click="activeMainTab = 'weekly_weight'"
                            :class="activeMainTab === 'weekly_weight' ? 'bg-[#8F0A0D] text-white font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-semibold hover:bg-slate-100'"
                            class="px-3.5 py-2 rounded-xl text-xs transition inline-flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Weekly Weight</span>
                    </button>

                    {{-- Tab 3: Actual Progress --}}
                    <button type="button" 
                            @click="activeMainTab = 'actual_progress'"
                            :class="activeMainTab === 'actual_progress' ? 'bg-[#8F0A0D] text-white font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-semibold hover:bg-slate-100'"
                            class="px-3.5 py-2 rounded-xl text-xs transition inline-flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Actual Progress</span>
                        <span id="tab-actual-progress-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                              :class="activeMainTab === 'actual_progress' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800'">
                            {{ number_format($currentProgressPercent, 1) }}%
                        </span>
                    </button>

                    {{-- Tab 4: S-Curve --}}
                    <button type="button" 
                            @click="activeMainTab = 'scurve'; $nextTick(() => { if (window.renderSCurveChart) { window.renderSCurveChart(); } else if (window.sCurveChartInstance) { window.sCurveChartInstance.resize(); window.sCurveChartInstance.update(); } })"
                            :class="activeMainTab === 'scurve' ? 'bg-[#8F0A0D] text-white font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-semibold hover:bg-slate-100'"
                            class="px-3.5 py-2 rounded-xl text-xs transition inline-flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <span>S-Curve</span>
                    </button>

                    {{-- Tab 5: Expenses --}}
                    <button type="button" 
                            @click="activeMainTab = 'expenses'"
                            :class="activeMainTab === 'expenses' ? 'bg-[#8F0A0D] text-white font-bold shadow-2xs' : 'text-slate-600 hover:text-slate-900 font-semibold hover:bg-slate-100'"
                            class="px-3.5 py-2 rounded-xl text-xs transition inline-flex items-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Expenses</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                              :class="activeMainTab === 'expenses' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-700'">
                            {{ $expenses->count() }}
                        </span>
                    </button>
                </div>


            </div>

            {{-- ========================================================== --}}
            {{-- TAB 1: SCHEDULE (WBS DELIVERABLES & TIMELINE)              --}}
            {{-- ========================================================== --}}
            <div x-show="activeMainTab === 'schedule'" class="space-y-6">
                <div class="ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[#E2E8F0]">
                        <div>
                            <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider inline-flex items-center">
                                <span class="ipnet-badge-dot"></span> PROJECT SPECIFICATION &amp; SCHEDULE
                            </p>
                            <h3 class="text-base font-bold text-[#1E293B]">Project Schedule &amp; WBS Deliverables</h3>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($isLeadUser)
                                <button type="button" @click="showAssignTeamModal = true" class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-[11px] font-bold text-slate-700 transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span>Kelola Personil Tim</span>
                                </button>
                            @endif
                            <button type="button" @click="showImportExcelModal = true" class="px-2.5 py-1.5 rounded-lg border border-emerald-600 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-[11px] font-bold shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Import Excel (WBS)</span>
                            </button>
                            <button type="button" @click="showQuickTaskModal = true" class="px-2.5 py-1.5 rounded-lg bg-[#8F0A0D] hover:bg-[#73080A] text-white text-[11px] font-bold shadow-2xs transition flex items-center gap-1.5 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Tambah Activity / Task</span>
                            </button>
                        </div>
                    </div>

                    {{-- Excel Metadata Header (Gambar 2 / Gambar 10) --}}
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-xs text-left">
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr class="bg-slate-50/70">
                                    <td class="w-64 py-2 px-4 font-bold text-slate-800 border-r border-slate-200">Project Name</td>
                                    <td class="py-2 px-4 font-medium text-slate-900">{{ $project->name }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-4 font-bold text-slate-800 border-r border-slate-200">Customer</td>
                                    <td class="py-2 px-4 font-medium text-slate-900">{{ $project->client ?: '-' }}</td>
                                </tr>
                                <tr class="bg-slate-50/70">
                                    <td class="py-2 px-4 font-bold text-slate-800 border-r border-slate-200">Customer's PIC</td>
                                    <td class="py-2 px-4 font-medium text-slate-900">{{ $project->customer_pic_technical ?: ($project->customer_pic_business ?: ($handoverData['customer_pic'] ?? '-')) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-4 font-bold text-slate-800 border-r border-slate-200">Contract/SPK/PO Number</td>
                                    <td class="py-2 px-4 font-mono font-bold text-slate-900">{{ $project->po_spk_number ?: ($handoverData['contract_spk_number'] ?? 'EP-01KYREAFEJH89MWPVDQ6W20NXV') }}</td>
                                </tr>
                                <tr class="bg-slate-50/70">
                                    <td class="py-2 px-4 font-bold text-slate-800 border-r border-slate-200">Start Date ( by Contract/SPK/PO)</td>
                                    <td class="py-2 px-4 font-medium text-slate-900">{{ $startDate->format('d-M-y') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-4 font-bold text-slate-800 border-r border-slate-200">Finish Date ( by Contract/SPK/PO)</td>
                                    <td class="py-2 px-4 font-medium text-slate-900">{{ $deadlineDate->format('d-M-y') }}</td>
                                </tr>
                                <tr class="bg-slate-50/70">
                                    <td class="py-2 px-4 font-bold text-slate-800 border-r border-slate-200">Planned Execution (WBS Start - Finish)</td>
                                    <td class="py-2 px-4 font-semibold text-slate-900">{{ $wbsStartDate->format('d-M-y') }} s/d {{ $wbsEndDate->format('d-M-y') }} ({{ $totalWeeks }} Minggu)</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-4 font-bold text-slate-800 border-r border-slate-200">Estimate Project Duration</td>
                                    <td class="py-2 px-4 font-bold text-[#8F0A0D]">
                                        <div class="flex items-center justify-between">
                                            <span>{{ $totalProjectDays }} Hari Pelaksanaan</span>
                                            <button type="button" @click="showTimelineModal = true" class="text-blue-600 hover:text-blue-800 text-[11px] font-semibold underline cursor-pointer">
                                                Edit Timeline &amp; Durasi
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Personil Tim Lapangan Chips --}}
                    @if(count($assignedEngineers) > 0)
                        <div class="p-2.5 bg-slate-50/80 rounded-xl border border-slate-200/80 flex items-center gap-2 flex-wrap text-xs">
                            <span class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wider">Personil Tim Lapangan:</span>
                            @foreach($assignedEngineers as $aeng)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-slate-200 font-bold text-slate-800 text-[11px]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    <span>{{ $aeng['name'] ?? 'Engineer' }}</span>
                                    <span class="text-[10px] text-slate-400 font-normal">({{ $aeng['position'] ?? 'Teknisi' }})</span>
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- Schedule WBS Table (Image 2 / 10 - NO progress bars!) --}}
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold text-[#64748B] uppercase tracking-wider">
                                    <th class="py-3 px-4 min-w-[240px]">Task Name</th>
                                    <th class="py-3 px-3 text-center whitespace-nowrap">Duration</th>
                                    <th class="py-3 px-3 text-center whitespace-nowrap">Starting Day</th>
                                    <th class="py-3 px-3 text-center whitespace-nowrap">Finish Day</th>
                                    <th class="py-3 px-3 text-center whitespace-nowrap">Week</th>
                                    <th class="py-3 px-3 text-right whitespace-nowrap">Activity Weight (%)</th>
                                    <th class="py-3 px-3 text-right whitespace-nowrap">Daily Weight (%)</th>
                                    <th class="py-3 px-3 text-right whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @php
                                    $totDur = 0;
                                    $totActW = 0.0;
                                @endphp
                                @forelse($weeklyWeightMatrix as $idx => $row)
                                    @php
                                        $totDur += $row['duration'];
                                        $totActW += $row['weight'];
                                        $t = $project->tasks->firstWhere('id', $row['id']) ?? $project->tasks->firstWhere('title', $row['title']);
                                        $dWeight = $row['duration'] > 0 ? round($row['weight'] / $row['duration'], 1) : 0.0;
                                        $wWeek = 1;
                                        if (isset($row['weeks'])) {
                                            foreach ($row['weeks'] as $wk => $wv) {
                                                if ($wv > 0) { $wWeek = $wk; break; }
                                            }
                                        }
                                        if ($t && preg_match('/Week:\s*(\d+)/i', $t->description ?? '', $wm)) {
                                            $wWeek = (int) $wm[1];
                                        }
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-2.5 px-4 font-bold text-slate-800">
                                            {{ $row['title'] }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center font-bold text-slate-700">
                                            {{ $row['duration'] }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center font-medium text-slate-600">
                                            {{ $row['starting_day'] }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center font-medium text-slate-600">
                                            {{ $row['finish_day'] }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $wWeek }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-bold text-blue-700">
                                            {{ $isPpatkBadge ? round($row['weight']) . '%' : number_format($row['weight'], 1) . '%' }}
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-medium text-slate-700">
                                            {{ $isPpatkBadge ? '3%' : number_format($dWeight, 1) . '%' }}
                                        </td>
                                        <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                            @if($t)
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" 
                                                            @click="openEditTaskModal({
                                                                id: {{ $t->id }},
                                                                title: '{{ addslashes($t->title) }}',
                                                                duration_days: {{ $row['duration'] }},
                                                                start_date: '{{ $t->start_date ? \Carbon\Carbon::parse($t->start_date)->format('Y-m-d') : '' }}',
                                                                deadline: '{{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('Y-m-d') : '' }}',
                                                                bobot: {{ $row['weight'] }},
                                                                progress: {{ (int)($t->progress ?: 0) }},
                                                                status: '{{ $t->status }}',
                                                                priority: '{{ $t->priority ?: 'Medium' }}',
                                                                engineer_id: '{{ $t->engineer_id }}',
                                                                description: '{{ addslashes($t->description ?? '') }}'
                                                            })"
                                                            title="Ubah Task"
                                                            class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 border border-slate-200 hover:border-amber-300 transition cursor-pointer shadow-2xs">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                        </svg>
                                                    </button>
                                                    <form action="{{ route('pmo.tasks.destroy_post', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus task ini?')">
                                                        @csrf
                                                        <button type="submit" 
                                                                title="Hapus Task"
                                                                class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-300 transition cursor-pointer shadow-2xs">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-slate-400 text-[11px]">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-6 text-slate-400">Belum ada task WBS.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-slate-50 border-t-2 border-slate-200 font-bold text-xs text-slate-800">
                                <tr>
                                    <td class="py-2.5 px-4 uppercase tracking-wider text-[11px] text-slate-600">
                                        Total Rekap Schedule ({{ count($weeklyWeightMatrix) }} Tasks)
                                    </td>
                                    <td class="py-2.5 px-3 text-center text-slate-800 font-bold text-xs">
                                        {{ $totDur }}
                                    </td>
                                    <td colspan="3" class="py-2.5 px-3 text-center text-slate-400 font-normal text-xs">
                                        Day 0 s/d Day {{ $totDur }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right text-blue-800 font-bold text-xs">
                                        {{ number_format($totActW, 1) }}%
                                    </td>
                                    <td class="py-2.5 px-3 text-right text-slate-700 font-bold text-xs">
                                        3.3%
                                    </td>
                                    <td class="py-2.5 px-3 text-right text-slate-500 font-normal text-[11px]">
                                        100% Selesai
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- TAB 2: WEEKLY WEIGHT (PLAN) PER TASK                       --}}
            {{-- ========================================================== --}}
            <div x-show="activeMainTab === 'weekly_weight'" class="space-y-6">
                <div class="ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[#E2E8F0]">
                        <div>
                            <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider inline-flex items-center">
                                <span class="ipnet-badge-dot"></span> SHEET 2: WEEKLY WEIGHT (PLAN)
                            </p>
                            <h3 class="text-base font-bold text-[#1E293B]">Weekly Weight (plan) per task</h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Rentang Pelaksanaan WBS: <span class="font-semibold text-slate-700">{{ $wbsStartDate->format('d M Y') }} s/d {{ $wbsEndDate->format('d M Y') }}</span> ({{ $totalProjectDays }} Hari / {{ $totalWeeks }} Minggu) &bull; <span class="text-slate-400">Batas Akhir Kontrak SPK: {{ $deadlineDate->format('d M Y') }}</span>
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 text-xs font-bold">
                                Total Bobot: 100.0%
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold text-[#64748B] uppercase tracking-wider">
                                    <th class="py-3 px-3.5 min-w-[220px]">Task Name</th>
                                    <th class="py-3 px-2.5 text-center whitespace-nowrap">Duration</th>
                                    <th class="py-3 px-2.5 text-center whitespace-nowrap">Starting Day</th>
                                    <th class="py-3 px-2.5 text-center whitespace-nowrap">Finish Day</th>
                                    <th class="py-3 px-3 text-right whitespace-nowrap">Activity Weight (%)</th>
                                    @for($w = 1; $w <= $totalWeeks; $w++)
                                        <th class="py-3 px-3 text-right whitespace-nowrap">Week {{ $w }}</th>
                                    @endfor
                                    <th class="py-3 px-3 text-right whitespace-nowrap">Check (=weight)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($weeklyWeightMatrix as $row)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-2.5 px-3.5 font-semibold text-slate-800">
                                            {{ $row['title'] }}
                                        </td>
                                        <td class="py-2.5 px-2.5 text-center font-bold text-slate-700">
                                            {{ $row['duration'] }}
                                        </td>
                                        <td class="py-2.5 px-2.5 text-center text-slate-600">
                                            {{ $row['starting_day'] }}
                                        </td>
                                        <td class="py-2.5 px-2.5 text-center text-slate-600">
                                            {{ $row['finish_day'] }}
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-bold text-slate-900">
                                            {{ number_format($row['weight'], 1) }}%
                                        </td>
                                        @for($w = 1; $w <= $totalWeeks; $w++)
                                            <td class="py-2.5 px-3 text-right text-xs {{ ($row['weeks'][$w] ?? 0) > 0 ? 'font-bold text-blue-700 bg-blue-50/30' : 'text-slate-400' }}">
                                                {{ number_format($row['weeks'][$w] ?? 0.0, 1) }}%
                                            </td>
                                        @endfor
                                        <td class="py-2.5 px-3 text-right font-bold text-slate-900 bg-slate-50/50">
                                            {{ number_format($row['check'] ?? $row['weight'], 1) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-t-2 border-slate-300 font-bold text-xs">
                                {{-- Row 1: Plan Weekly (%) --}}
                                <tr class="bg-slate-100 text-slate-900">
                                    <td class="py-2.5 px-3.5 font-bold uppercase text-[11px] text-slate-800">
                                        Plan Weekly (%)
                                    </td>
                                    <td colspan="3" class="py-2.5 px-2.5"></td>
                                    <td class="py-2.5 px-3 text-right font-bold text-slate-800 text-xs">
                                        100.0%
                                    </td>
                                    @for($w = 1; $w <= $totalWeeks; $w++)
                                        <td class="py-2.5 px-3 text-right font-bold text-slate-800 text-xs">
                                            {{ number_format($totalWeeklyWeightPlan[$w] ?? 0.0, 1) }}%
                                        </td>
                                    @endfor
                                    <td class="py-2.5 px-3"></td>
                                </tr>
                                {{-- Row 2: Plan Cumulative (%) --}}
                                <tr class="bg-slate-200/80 text-blue-900">
                                    <td class="py-2.5 px-3.5 font-bold uppercase text-[11px] text-blue-900">
                                        Plan Cumulative (%)
                                    </td>
                                    <td colspan="4" class="py-2.5 px-2.5"></td>
                                    @for($w = 1; $w <= $totalWeeks; $w++)
                                        <td class="py-2.5 px-3 text-right font-bold text-blue-800 text-xs">
                                            {{ number_format($totalWeeklyWeightCum[$w] ?? 0.0, 1) }}%
                                        </td>
                                    @endfor
                                    <td class="py-2.5 px-3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ========================================================== --}}
            {{-- TAB 3: ACTUAL PROGRESS (PROGRES AKTUAL PER TASK)           --}}
            {{-- ========================================================== --}}
            <div x-show="activeMainTab === 'actual_progress'" class="space-y-6"
                 x-data="{
                    weights: {
                        @foreach($actualProgressRows as $r)
                            {{ $r['id'] }}: {{ (float)$r['weight'] }},
                        @endforeach
                    },
                    taskProgress: {
                        @foreach($actualProgressRows as $r)
                            {{ $r['id'] }}: {{ (float)$r['progress'] }},
                        @endforeach
                    },
                    calcTotalActual() {
                        let sum = 0;
                        for (let id in this.weights) {
                            let w = this.weights[id] || 0;
                            let p = this.taskProgress[id] || 0;
                            sum += (w * p) / 100;
                        }
                        return sum.toFixed(1);
                    },
                    onProgressChanged() {
                        if (window.updateLiveSCurveActual) {
                            window.updateLiveSCurveActual(this.taskProgress);
                        }
                    }
                 }">
                <form action="{{ route('pmo.projects.actual_progress_matrix.update', $project->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="tab" value="actual_progress">
                    <div class="ipnet-card p-5 sm:p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[#E2E8F0]">
                            <div>
                                <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider inline-flex items-center">
                                    <span class="ipnet-badge-dot"></span> SHEET 3: ACTUAL PROGRESS
                                </p>
                                <h3 class="text-base font-bold text-[#1E293B]">Actual Progress per Task</h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Daftar capaian progres aktual per task WBS terhadap total bobot proyek (Actual = Activity Weight &times; Progress %)
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-800 border border-blue-200 text-xs font-bold">
                                    Actual Cumulative: <span x-text="calcTotalActual() + '%'">{{ number_format($totalActualProgressSum, 1) }}%</span>
                                </span>
                                <button type="submit" class="px-4 py-2 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white text-xs font-bold shadow-2xs transition inline-flex items-center gap-2 cursor-pointer active:scale-95">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Simpan Progres Aktual</span>
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold text-[#64748B] uppercase tracking-wider">
                                        <th class="py-3 px-4 min-w-[240px]">Task Name</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap">Activity Weight (%)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap min-w-[120px]">Progress (%)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap">Actual (%)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @php
                                        $totWeight = 0;
                                        $totActual = 0;
                                    @endphp
                                    @foreach($actualProgressRows as $row)
                                        @php
                                            $totWeight += $row['weight'];
                                            $totActual += $row['actual'];
                                            $t = $row['task'] ?? $project->tasks->firstWhere('id', $row['id']);
                                        @endphp
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-2.5 px-4 font-semibold text-slate-800">
                                                {{ $row['title'] }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-bold text-slate-900">
                                                {{ number_format($row['weight'], 1) }}%
                                            </td>
                                            <td class="py-2 px-3 text-right">
                                                <div class="inline-flex items-center justify-end gap-1">
                                                    <input type="number" 
                                                           step="any" 
                                                           min="0" 
                                                           max="100" 
                                                           name="tasks[{{ $row['id'] }}][progress]" 
                                                           value="{{ (float) $row['progress'] }}" 
                                                           x-model.number="taskProgress[{{ $row['id'] }}]"
                                                           @input="onProgressChanged()"
                                                           class="w-16 py-1 px-1.5 text-right font-bold text-slate-800 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:border-blue-600 focus:outline-hidden text-xs">
                                                    <span class="text-[11px] font-bold text-slate-500">%</span>
                                                </div>
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-bold text-slate-800">
                                                <span x-text="(((weights[{{ $row['id'] }}] || 0) * (taskProgress[{{ $row['id'] }}] || 0)) / 100).toFixed(1) + '%'">
                                                    {{ number_format($row['actual'], 1) }}%
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                                @if($t)
                                                    <div class="flex items-center justify-end gap-1.5">
                                                        <button type="button" 
                                                                @click="openEditTaskModal({
                                                                    id: {{ $t->id }},
                                                                    title: '{{ addslashes($t->title) }}',
                                                                    duration_days: {{ $t->duration ?? 1 }},
                                                                    start_date: '{{ $t->start_date ? \Carbon\Carbon::parse($t->start_date)->format('Y-m-d') : '' }}',
                                                                    deadline: '{{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('Y-m-d') : '' }}',
                                                                    bobot: {{ $row['weight'] }},
                                                                    progress: taskProgress[{{ $row['id'] }}] !== undefined ? taskProgress[{{ $row['id'] }}] : {{ (int)($t->progress ?: 0) }},
                                                                    status: '{{ $t->status }}',
                                                                    priority: '{{ $t->priority ?: 'Medium' }}',
                                                                    engineer_id: '{{ $t->engineer_id }}',
                                                                    description: '{{ addslashes($t->description ?? '') }}'
                                                                })"
                                                                title="Ubah Task"
                                                                class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 border border-slate-200 hover:border-amber-300 transition cursor-pointer shadow-2xs">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                            </svg>
                                                        </button>
                                                        <form action="{{ route('pmo.tasks.destroy_post', $t->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus task ini?')">
                                                            @csrf
                                                            <button type="submit" 
                                                                    title="Hapus Task"
                                                                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-300 transition cursor-pointer shadow-2xs">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <span class="text-slate-400 text-[11px]">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="border-t-2 border-slate-300 font-bold text-xs bg-slate-100 text-slate-800">
                                    <tr>
                                        <td class="py-2.5 px-4 uppercase text-[11px] font-bold text-slate-700">
                                            Total Rekap Actual Progress ({{ count($actualProgressRows) }} Tasks)
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-bold text-slate-900 text-xs">
                                            {{ number_format($totWeight, 1) }}%
                                        </td>
                                        <td class="py-2.5 px-3 text-center text-slate-400 text-xs font-normal">
                                            -
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-bold text-slate-900 text-xs">
                                            <span x-text="calcTotalActual() + '%'">{{ number_format($totActual, 1) }}%</span>
                                        </td>
                                        <td class="py-2.5 px-3 text-right text-slate-500 font-normal text-[11px]">
                                            100% Selesai
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ========================================================== --}}
            {{-- TAB 4: S-CURVE PROGRESS PROYEK                             --}}
            {{-- ========================================================== --}}
            <div x-show="activeMainTab === 'scurve'" class="space-y-6">
                <div class="ipnet-card p-5 sm:p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-[#E2E8F0]">
                        <div>
                            <p class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider inline-flex items-center">
                                <span class="ipnet-badge-dot"></span> SHEET 4: S-CURVE PROGRESS PROYEK
                            </p>
                            <h2 class="text-lg font-bold text-[#1E293B]">S-Curve Progress Proyek</h2>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $project->name }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                                <span class="w-3.5 h-1 rounded-full bg-[#1D4ED8]"></span>
                                <span>Plan Cumulative (%)</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                                <span class="w-3.5 h-1 rounded-full bg-[#C2410C]"></span>
                                <span>Chart helper (Actual)</span>
                            </div>
                        </div>
                    </div>

                    {{-- Layout Dibagi 2 Vertikal: Atas Tabel Full Width & Bawah Kurva S Full Width --}}
                    {{-- 1. BAGIAN ATAS: Tabel S-Curve Full Width (Tidak Terpotong / Sliced) --}}
                    <div class="space-y-3">
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold text-[#64748B] uppercase tracking-wider">
                                        <th class="py-3 px-3 text-center whitespace-nowrap">Week</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap">Plan Weekly (%)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap">Plan Cumulative (%)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap">Actual Weekly (%)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap">Actual Cumulative (%)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap">Deviation (Actual - Plan)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap">Chart helper (hidden pts = not reported)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($weeklyRecords as $rec)
                                        <tr class="hover:bg-slate-50/80 transition {{ $rec['is_current'] ? 'bg-amber-50/40' : '' }}">
                                            <td class="py-2.5 px-3 text-center font-bold text-slate-800">
                                                {{ $rec['week'] }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-medium text-slate-700">
                                                {{ number_format($rec['plan_weekly'], 1) }}%
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-bold text-blue-700">
                                                {{ number_format($rec['plan_percent'], 1) }}%
                                            </td>
                                            <td id="scurve-actual-weekly-w{{ $rec['week'] }}" class="py-2.5 px-3 text-right font-medium text-slate-800">
                                                {{ $rec['actual_weekly'] !== null ? number_format($rec['actual_weekly'], 1) . '%' : '' }}
                                            </td>
                                            <td id="scurve-actual-cum-w{{ $rec['week'] }}" class="py-2.5 px-3 text-right font-bold text-slate-800">
                                                {{ $rec['actual_percent'] !== null ? number_format($rec['actual_percent'], 1) . '%' : '' }}
                                            </td>
                                            <td id="scurve-variance-w{{ $rec['week'] }}" class="py-2.5 px-3 text-right font-bold {{ ($rec['variance'] ?? 0) < 0 ? 'text-rose-700' : 'text-slate-800' }}">
                                                @if($rec['variance'] !== null)
                                                    {{ $rec['variance'] > 0 ? '+' : '' }}{{ number_format($rec['variance'], 1) }}%
                                                @endif
                                            </td>
                                            <td id="scurve-helper-w{{ $rec['week'] }}" class="py-2.5 px-3 text-right text-xs {{ $rec['chart_helper'] === '#N/A' ? 'text-slate-400 italic' : 'font-bold text-slate-800' }}">
                                                {{ $rec['chart_helper'] !== '#N/A' ? number_format($rec['chart_helper'], 1) . '%' : '#N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Notes Box Persis Sesuai Gambar 5 --}}
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1">
                            <p class="font-bold text-slate-800">Notes</p>
                            <p>• Plan Weekly comes from the 'Weekly Weight' sheet (task weight spread over the days each task overlaps a week).</p>
                            <p>• Actual Cumulative = sum of (task completion % x activity weight) from the Actual Progress sheet. A week shows only once at least one task is filled in for it.</p>
                            <p>• Column G returns NA() on purpose so the chart skips unreported weeks instead of drawing 0%. Week 0 = baseline (0%).</p>
                        </div>
                    </div>

                    {{-- 2. BAGIAN BAWAH: Kurva S Dual Line Chart Full Width --}}
                    <div class="bg-white rounded-xl p-5 border border-slate-200 space-y-3">
                        <div class="border-b border-slate-100 pb-2.5 flex items-center justify-between">
                            <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                                S-Curve: Plan vs Actual Cumulative Progress
                            </h4>
                            <span class="text-[11px] font-bold text-slate-500">
                                Minggu ke-0 s/d {{ $totalWeeks }} ({{ $projectTimelineText }})
                            </span>
                        </div>
                        <div class="w-full" style="height: 380px;">
                            <canvas id="sCurveDualLineChart"></canvas>
                        </div>
                        <div class="flex items-center justify-center gap-6 pt-3 border-t border-slate-100 text-xs">
                            <div class="flex items-center gap-2">
                                <span class="w-3.5 h-3.5 rounded-full bg-[#1D4ED8]"></span>
                                <span class="font-semibold text-slate-700">Plan Cumulative (%)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3.5 h-3.5 rounded-sm bg-[#C2410C]"></span>
                                <span class="font-semibold text-slate-700">Chart helper (hidden pts = not reported)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
{{-- ========================================================== --}}
            {{-- TAB 5: EXPENSES & FINANCIAL TRACKING                       --}}
            {{-- ========================================================== --}}
            <div x-show="activeMainTab === 'expenses'" class="space-y-6">
{{-- RIGHT COLUMN: Log Categorized Expense & Riwayat --}}
                <div x-show="activeMainTab === 'expenses'" 
                     class="lg:col-span-12 space-y-5">
                    
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
            </div> {{-- End of Tab 5 Expenses --}}

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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Target Rencana (Plan %)</label>
                        <div class="relative">
                            <input type="number" 
                                   name="plan" 
                                   step="0.01" 
                                   min="0" 
                                   max="100" 
                                   x-model="activePlan"
                                   placeholder="Contoh: 15.00"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-extrabold text-blue-700 focus:border-[#8F0A0D] outline-none">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">%</span>
                        </div>
                        <p class="text-[10.5px] text-slate-400 mt-1">Target kumulatif</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Realisasi Aktual (%)</label>
                        <div class="relative">
                            <input type="number" 
                                   name="actual" 
                                   step="0.01" 
                                   min="0" 
                                   max="100" 
                                   x-model="activeActual"
                                   placeholder="Contoh: 12.50"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-extrabold text-[#8F0A0D] focus:border-[#8F0A0D] outline-none">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">%</span>
                        </div>
                        <p class="text-[10.5px] text-slate-400 mt-1">Realisasi akhir pekan</p>
                    </div>
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
    <!-- MODAL 0: UBAH KLIEN & PIC KLIEN PROYEK                     -->
    <!-- ========================================================== -->
    <div x-show="showClientModal" 
         x-transition.opacity
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-5 sm:p-6 space-y-4"
             @click.away="showClientModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Ubah Data Klien & PIC</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Informasi perusahaan dan PIC klien untuk diskusi PM</p>
                    </div>
                </div>
                <button type="button" @click="showClientModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('pmo.projects.update_client', $project->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Klien / Perusahaan <span class="text-[#8F0A0D]">*</span></label>
                        <input type="text" 
                               name="client" 
                               value="{{ $project->client }}" 
                               required
                               placeholder="Contoh: PT Angkasa Pura Support"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">PIC Klien (Nama, Jabatan & No HP)</label>
                        <input type="text" 
                               name="customer_pic" 
                               value="{{ $project->customer_pic_technical ?: ($project->customer_pic_business ?: ($project->handover_data['customer_pic'] ?? '')) }}" 
                               placeholder="Contoh: Bpk. Wayan Koster (Head of ICT Bandara IGNR Denpasar - 081238991122)"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 bg-white focus:border-[#8F0A0D] outline-none">
                        <span class="text-[10.5px] text-slate-400 mt-1 block">PIC dari pihak klien yang mendampingi dan berdiskusi langsung dengan Project Manager.</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showClientModal = false" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl btn-ipnet-primary transition shadow-xs cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================== -->
    <!-- MODAL PERIODE TANGGAL PROYEK (UBAH START DATE & DEADLINE)  -->
    <!-- ========================================================== -->
    <div x-show="showTimelineModal" 
         x-transition.opacity
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-5 sm:p-6 space-y-4"
             @click.away="showTimelineModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Ubah Durasi &amp; Periode Proyek</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Sesuaikan durasi hari pelaksanaan, jumlah minggu, dan rentang tanggal oleh PM</p>
                    </div>
                </div>
                <button type="button" @click="showTimelineModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('pmo.projects.update_timeline', $project->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Durasi Pelaksanaan (Hari)</label>
                            <input type="number" 
                                   name="estimate_project_duration" 
                                   value="{{ $estimateDuration ?? ($totalProjectDays ?? 30) }}" 
                                   min="1" 
                                   max="365"
                                   placeholder="Contoh: 30"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Minggu (Weeks)</label>
                            <input type="number" 
                                   name="execution_weeks" 
                                   value="{{ $executionWeeks ?? ($totalProjectWeeks ?? 5) }}" 
                                   min="1" 
                                   max="52"
                                   placeholder="Contoh: 5"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Mulai Proyek (Start Date) <span class="text-[#8F0A0D]">*</span></label>
                        <input type="date" 
                               name="start_date" 
                               value="{{ $startDate ? $startDate->format('Y-m-d') : ($project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '') }}" 
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Target Selesai Proyek (Deadline) <span class="text-[#8F0A0D]">*</span></label>
                        <input type="date" 
                               name="deadline" 
                               value="{{ $deadlineDate ? $deadlineDate->format('Y-m-d') : ($project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') : '') }}" 
                               required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none">
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-blue-50/70 border border-blue-200/60 text-xs text-blue-900">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-[11.5px] leading-relaxed">
                            Mengubah durasi dan periode proyek akan otomatis menyesuaikan durasi total, rentang mingguan pada Kurva S, dan indikator sisa hari pada dashboard PMO.
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showTimelineModal = false" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl btn-ipnet-primary transition shadow-xs cursor-pointer">
                        Simpan Perubahan
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
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#0F172A]/70 backdrop-blur-xs">
        <div class="bg-white rounded-2xl border border-[#CBD5E1] shadow-2xl max-w-lg w-full flex flex-col overflow-hidden"
             @click.away="showDivisionModal = false">
            {{-- Header Bar Konsisten Template Resmi IPNET --}}
            <div class="px-6 py-4 border-b border-[#E2E8F0] flex items-center justify-between bg-[#F8FAFC] shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#8F0A0D] shrink-0"></span>
                    <div>
                        <h3 class="text-[15px] font-bold text-[#0F172A]">Tetapkan Tim Teknis Pelaksana</h3>
                        <p class="text-[11px] text-[#64748B]">Pilih tim teknis yang bertanggung jawab atas implementasi</p>
                    </div>
                </div>
                <button type="button" @click="showDivisionModal = false" class="text-[#64748B] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#E2E8F0] transition cursor-pointer" title="Tutup">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('pmo.projects.assign_division', $project->id) }}" 
                  method="POST" 
                  x-data="{ 
                      selectedTeams: {{ json_encode(!empty($project->handover_data['assigned_teams']) ? $project->handover_data['assigned_teams'] : ($project->division_id == 4 ? ['Tim Network', 'Tim Security'] : ($project->division_id == 2 ? ['Tim Security'] : ['Tim Network']))) }},
                      newCustomTeam: '',
                      teamError: '',
                      toggleTeam(team) {
                          const idx = this.selectedTeams.indexOf(team);
                          if (idx > -1) {
                              this.selectedTeams.splice(idx, 1);
                          } else {
                              this.selectedTeams.push(team);
                          }
                          this.teamError = '';
                      },
                      hasTeam(team) {
                          return this.selectedTeams.includes(team);
                      },
                      addCustomTeam() {
                          let val = this.newCustomTeam.trim();
                          if (!val) return;
                          if (!/^tim\s+/i.test(val)) {
                              val = 'Tim ' + val;
                          }
                          if (!this.selectedTeams.includes(val)) {
                              this.selectedTeams.push(val);
                          }
                          this.newCustomTeam = '';
                          this.teamError = '';
                      },
                      removeTeam(team) {
                          this.selectedTeams = this.selectedTeams.filter(t => t !== team);
                      },
                      submitCheck(e) {
                          if (this.newCustomTeam.trim()) {
                              this.addCustomTeam();
                          }
                          if (this.selectedTeams.length === 0) {
                              e.preventDefault();
                              this.teamError = 'Pilih minimal satu tim teknis pelaksana.';
                              return false;
                          }
                      }
                  }"
                  @submit="submitCheck($event)"
                  class="flex flex-col m-0 p-0">
                @csrf
                <div class="p-5 sm:p-6 space-y-3">
                    <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold text-slate-700">Pilihan Tim Teknis</label>
                        <span class="text-[11px] text-slate-400">Pilih satu atau lebih</span>
                    </div>

                    {{-- Default Team Buttons: Tim Network & Tim Security --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" 
                                @click="toggleTeam('Tim Network')"
                                :class="hasTeam('Tim Network') 
                                    ? 'bg-red-50 border-[#8F0A0D] text-[#8F0A0D] font-bold shadow-2xs ring-1 ring-[#8F0A0D]/20' 
                                    : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold'"
                                class="px-3.5 py-2 rounded-xl border text-xs flex items-center gap-1.5 transition cursor-pointer select-none">
                            <template x-if="hasTeam('Tim Network')">
                                <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <template x-if="!hasTeam('Tim Network')">
                                <span class="w-3.5 h-3.5 rounded-full border border-slate-300 inline-block"></span>
                            </template>
                            <span>Tim Network</span>
                        </button>

                        <button type="button" 
                                @click="toggleTeam('Tim Security')"
                                :class="hasTeam('Tim Security') 
                                    ? 'bg-red-50 border-[#8F0A0D] text-[#8F0A0D] font-bold shadow-2xs ring-1 ring-[#8F0A0D]/20' 
                                    : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-semibold'"
                                class="px-3.5 py-2 rounded-xl border text-xs flex items-center gap-1.5 transition cursor-pointer select-none">
                            <template x-if="hasTeam('Tim Security')">
                                <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </template>
                            <template x-if="!hasTeam('Tim Security')">
                                <span class="w-3.5 h-3.5 rounded-full border border-slate-300 inline-block"></span>
                            </template>
                            <span>Tim Security</span>
                        </button>

                        {{-- Custom Team Tags --}}
                        <template x-for="t in selectedTeams.filter(x => x !== 'Tim Network' && x !== 'Tim Security')" :key="t">
                            <span class="px-3 py-2 rounded-xl border border-red-200 bg-red-50 text-[#8F0A0D] text-xs font-bold flex items-center gap-1.5 shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="t"></span>
                                <button type="button" @click="removeTeam(t)" class="text-red-400 hover:text-red-700 ml-1 cursor-pointer">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </span>
                        </template>
                    </div>

                    {{-- Input Ketik Manual Tim Baru --}}
                    <div class="flex items-center gap-1.5 pt-1">
                        <input type="text" 
                               x-model="newCustomTeam"
                               @keydown.enter.prevent="addCustomTeam()"
                               placeholder="+ Ketik nama tim lain lalu tekan Tambah..."
                               class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 bg-slate-50/60 focus:bg-white focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 outline-none transition">
                        <button type="button" 
                                @click="addCustomTeam()"
                                :disabled="!newCustomTeam.trim()"
                                :class="newCustomTeam.trim() ? 'bg-slate-800 text-white hover:bg-slate-900 cursor-pointer shadow-2xs' : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                                class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1 shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah</span>
                        </button>
                    </div>

                    {{-- Hidden inputs --}}
                    <template x-for="t in selectedTeams" :key="t">
                        <input type="hidden" name="teams[]" :value="t">
                    </template>

                    <p x-show="teamError" x-text="teamError" class="text-[11px] text-rose-600 font-semibold"></p>
                </div>

                </div>

                {{-- Footer Bar Edge-to-Edge --}}
                <div class="px-6 py-3.5 border-t border-[#E2E8F0] bg-[#F8FAFC] flex items-center justify-end gap-2.5 shrink-0">
                    <button type="button" @click="showDivisionModal = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold rounded-xl btn-ipnet-primary transition shadow-xs cursor-pointer hover:scale-[1.02] active:scale-95">
                        Tetapkan Tim Teknis
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
                    {{ preg_replace('/^Divisi\s+/i', 'Tim ', $project->division->name ?? 'Tim Teknis') }}
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
                            @endphp
                            <label class="p-2.5 rounded-xl bg-white border border-slate-200/80 hover:border-[#8F0A0D]/50 transition flex items-center justify-between cursor-pointer">
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
                                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 shrink-0">
                                    {{ preg_replace('/^Divisi\s+/i', 'Tim ', $eng->division->name ?? 'Tim Teknis') }}
                                </span>
                            </label>
                        @empty
                            <div class="p-3 text-center text-xs text-slate-400">
                                Tidak ada data engineer aktif pada tim ini.
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
                        <h3 class="text-sm font-bold text-slate-900">Tambah Task Teknis (WBS)</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Alokasikan rincian pekerjaan ke personil tim proyek</p>
                    </div>
                </div>
                <button type="button" @click="showQuickTaskModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('pmo.projects.tasks.store', $project->id) }}" 
                  method="POST" 
                  x-data="{ 
                      wbsTitle: '', 
                      isCustomTitle: false
                  }"
                  class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Judul Tugas / Rincian Pekerjaan (WBS) <span class="text-[#8F0A0D]">*</span>
                    </label>

                    {{-- Dropdown Standar Judul WBS --}}
                    <select x-model="wbsTitle" 
                            @change="isCustomTitle = (wbsTitle === '__custom__')"
                            :name="isCustomTitle ? '' : 'title'"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none cursor-pointer">
                        <option value="" disabled selected>-- Pilih Judul Tugas (WBS) --</option>
                        <option value="Implementasi Network">Implementasi Network</option>
                        <option value="Migrasi">Migrasi</option>
                        <option value="Standarisasi IP">Standarisasi IP</option>
                        <option value="Reconfigurasi Firewall Fortinet">Reconfigurasi Firewall Fortinet</option>
                        <option value="Implementasi TACACS">Implementasi TACACS</option>
                        <option value="Testing dan Komisioning">Testing dan Komisioning</option>
                        <option value="Fase Penutup">Fase Penutup</option>
                        <option value="__custom__">Judul Lainnya / Kustom (Ketik Manual)...</option>
                    </select>

                    {{-- Input Teks Tambahan jika memilih Kustom --}}
                    <div class="mt-2" x-show="isCustomTitle" x-cloak>
                        <input type="text" 
                               name="title_custom" 
                               :name="isCustomTitle ? 'title' : 'title_custom'"
                               :required="isCustomTitle"
                               placeholder="Contoh: Instalasi Rack &amp; Penarikan Kabel UTP / Konfigurasi Router" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-900 focus:border-[#8F0A0D] outline-none transition">
                    </div>
                </div>

                {{-- Row 1: Prioritas Tugas & Status Awal (2 Kolom Sejajar) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Prioritas Tugas</label>
                        <select name="priority" required 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none cursor-pointer">
                            <option value="High">High (Kritikal / Utama)</option>
                            <option value="Medium" selected>Medium (Standar)</option>
                            <option value="Low">Low (Dukungan / Pendukung)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Awal</label>
                        <select name="status" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none cursor-pointer">
                            <option value="Not Started" selected>Not Started</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>

                {{-- Row 2: Durasi & Bobot (2 Kolom Sejajar & Simetris) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Durasi (Hari)</label>
                        <input type="number" 
                               name="duration_days" 
                               value="1" 
                               min="1" 
                               max="365" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#8F0A0D] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Bobot (%)</label>
                        <input type="number" 
                               name="bobot" 
                               value="0" 
                               step="0.01" 
                               min="0" 
                               max="100" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-blue-700 focus:border-[#8F0A0D] outline-none">
                    </div>
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
                        Tambah Task
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL UBAH / EDIT TASK TEKNIS WBS OLEH PM --}}
    <div x-show="showEditTaskModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
        
        <div @click.outside="showEditTaskModal = false"
             class="bg-white rounded-2xl shadow-xl border border-slate-200 max-w-lg w-full p-6 space-y-4 max-h-[90vh] overflow-y-auto">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Ubah Task Teknis (WBS)</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Perbarui rincian deliverable, bobot, durasi &amp; progres aktual oleh PM</p>
                    </div>
                </div>
                <button type="button" @click="showEditTaskModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'{{ url('/pmo/tasks') }}/' + editTaskId + '/update'" 
                  method="POST" 
                  class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Judul Tugas / Rincian Pekerjaan (WBS) <span class="text-[#8F0A0D]">*</span>
                    </label>
                    <input type="text" 
                           name="title" 
                           x-model="editTaskTitle" 
                           required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white text-xs font-semibold text-slate-900 focus:border-[#8F0A0D] outline-none transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Durasi (Hari)</label>
                        <input type="number" 
                               name="duration_days" 
                               x-model.number="editTaskDuration" 
                               min="1" 
                               max="365" 
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#8F0A0D] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Bobot (%)</label>
                        <input type="number" 
                               name="bobot" 
                               x-model="editTaskBobot" 
                               step="0.01" 
                               min="0" 
                               max="100" 
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-blue-700 focus:border-[#8F0A0D] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Progres Aktual (%)</label>
                        <input type="number" 
                               name="progress" 
                               x-model="editTaskProgress" 
                               min="0" 
                               max="100" 
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-emerald-700 focus:border-[#8F0A0D] outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Pengerjaan</label>
                        <select name="status" 
                                x-model="editTaskStatus" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none cursor-pointer">
                            <option value="Not Started">Not Started (Belum Berjalan)</option>
                            <option value="In Progress">In Progress (Sedang Dikerjakan)</option>
                            <option value="Completed">Completed (Selesai 100%)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Prioritas Tugas</label>
                        <select name="priority" 
                                x-model="editTaskPriority" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none cursor-pointer">
                            <option value="High">High (Kritikal / Utama)</option>
                            <option value="Medium">Medium (Standar)</option>
                            <option value="Low">Low (Dukungan / Pendukung)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi &amp; Instruksi Teknis PM</label>
                    <textarea name="description" 
                              x-model="editTaskDescription" 
                              rows="2.5" 
                              placeholder="Rincian instruksi pelaksanaan atau catatan teknis..." 
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:border-[#8F0A0D] outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showEditTaskModal = false" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-xl btn-ipnet-primary transition shadow-xs cursor-pointer">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

        {{-- MODAL IMPORT TASK WBS DARI EXCEL SPREADSHEET (ENTERPRISE REDESIGN) --}}
    <div x-show="showImportExcelModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        
        <div @click.outside="showImportExcelModal = false"
             class="bg-white rounded-2xl shadow-2xl border border-slate-200/90 max-w-3xl lg:max-w-4xl w-full p-5 sm:p-7 space-y-4 my-auto max-h-[92vh] overflow-y-auto animate-fade-in-up">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between pb-3.5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-[#8F0A0D] border border-red-100 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Import Task Kolektif Proyek (WBS Breakdown)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Unggah dokumen spreadsheet rencana kerja untuk mengisi seluruh deliverable secara otomatis.</p>
                    </div>
                </div>
                <button type="button" @click="showImportExcelModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- 1. Template Excel Download Banner --}}
            <div class="p-3.5 bg-gradient-to-r from-emerald-50/70 via-slate-50 to-white rounded-xl border border-emerald-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-700 text-white flex items-center justify-center shrink-0 shadow-xs">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 4h7v5h5v11H6V4zm2 8h2.5l1.5 2.5 1.5-2.5H16l-2.25 3.5L16 19h-2.5L12 16.5 10.5 19H8l2.25-3.5L8 12z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-900 flex items-center gap-2">
                            <span>Template Resmi Excel WBS Proyek</span>
                            <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Format Rekomendasi</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Gunakan acuan template resmi untuk mempermudah pemetaan bobot (%) dan durasi otomatis.
                        </p>
                    </div>
                </div>
                <a href="{{ route('pmo.projects.tasks.template_excel', $project->id) }}" 
                   class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition hover:scale-[1.02] shrink-0 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Unduh Template (.xlsx)</span>
                </a>
            </div>

            {{-- 2. Visual Spreadsheet Columns Structure --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span>Struktur Kolom File Spreadsheet</span>
                    </span>
                    <span class="text-[10.5px] text-slate-400">Baris 1: Header / Nama Kolom</span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200 bg-slate-50/60 shadow-2xs">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-100/80 border-b border-slate-200 text-[10.5px] font-bold text-slate-600">
                                <th class="py-2 px-2.5 text-center w-12 text-slate-400 border-r border-slate-200">A</th>
                                <th class="py-2 px-2.5 border-r border-slate-200">B (Wajib)</th>
                                <th class="py-2 px-2.5 border-r border-slate-200">C</th>
                                <th class="py-2 px-2.5 border-r border-slate-200 text-center">D</th>
                                <th class="py-2 px-2.5 border-r border-slate-200 text-right">E</th>
                                <th class="py-2 px-2.5 text-right">F</th>
                            </tr>
                            <tr class="bg-white border-b border-slate-200 text-[11px] font-bold text-slate-800">
                                <td class="py-2 px-2.5 text-center text-slate-400 border-r border-slate-200 font-mono">No</td>
                                <td class="py-2 px-2.5 border-r border-slate-200 text-slate-900">
                                    <span>Nama Tugas (WBS)</span>
                                    <span class="text-rose-600 font-extrabold">*</span>
                                </td>
                                <td class="py-2 px-2.5 border-r border-slate-200 text-slate-700">Deskripsi Scope</td>
                                <td class="py-2 px-2.5 border-r border-slate-200 text-center text-slate-700">Durasi (Hari)</td>
                                <td class="py-2 px-2.5 border-r border-slate-200 text-right text-blue-700">Bobot (%)</td>
                                <td class="py-2 px-2.5 text-right text-emerald-700">Progress (%)</td>
                            </tr>
                        </thead>
                        <tbody class="text-[10.5px] text-slate-500 bg-white/70">
                            <tr>
                                <td class="py-1.5 px-2.5 text-center font-mono border-r border-slate-100">1</td>
                                <td class="py-1.5 px-2.5 font-medium text-slate-800 border-r border-slate-100">Instalasi Switch & Router</td>
                                <td class="py-1.5 px-2.5 truncate max-w-[140px] border-r border-slate-100">Pemasangan rak & patching IDF</td>
                                <td class="py-1.5 px-2.5 text-center border-r border-slate-100">5</td>
                                <td class="py-1.5 px-2.5 text-right font-bold text-blue-600 border-r border-slate-100">15.00</td>
                                <td class="py-1.5 px-2.5 text-right font-bold text-emerald-600">0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Callout Note --}}
                <div class="flex items-start gap-2 p-2.5 bg-amber-50/80 rounded-xl border border-amber-200/80 text-[11px] text-amber-900 leading-relaxed">
                    <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div>
                        <strong>Catatan Penugasan:</strong> Kolom personel / assignee tidak perlu dicantumkan pada file Excel, karena alokasi teknisi dilakukan secara terpisah oleh <strong>Lead Engineer / Tim Lapangan</strong>.
                    </div>
                </div>
            </div>

            {{-- 3. Form Upload & Pilihan Mode --}}
            <form action="{{ route('pmo.projects.tasks.import_excel', $project->id) }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  x-data="{ 
                      fileName: '', 
                      fileSize: '',
                      mode: 'append',
                      handleFileSelect(e) {
                          const file = e.target.files[0];
                          if (file) {
                              this.fileName = file.name;
                              this.fileSize = (file.size / 1024).toFixed(1) + ' KB';
                          } else {
                              this.fileName = '';
                              this.fileSize = '';
                          }
                      }
                  }"
                  class="space-y-4 pt-1">
                @csrf
                
                {{-- Input Periode Pelaksanaan Proyek (Task WBS) --}}
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/90 space-y-2.5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                        <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Periode Pelaksanaan Pekerjaan (Task WBS)</span>
                        </label>
                        <span class="text-[10.5px] font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200 self-start sm:self-auto">
                            Maks Selesai Kontrak: {{ $deadlineDate ? $deadlineDate->format('d M Y') : ($project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : 'Sesuai Kontrak') }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold text-slate-700">Tanggal Mulai Pelaksanaan <span class="text-[#8F0A0D]">*</span></span>
                            <input type="date" 
                                   name="execution_start_date" 
                                   value="{{ $startDate ? $startDate->format('Y-m-d') : ($project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : date('Y-m-d')) }}" 
                                   required
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none">
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold text-slate-700">Target Selesai Pelaksanaan <span class="text-[#8F0A0D]">*</span></span>
                            <input type="date" 
                                   name="execution_end_date" 
                                   value="{{ $deadlineDate ? $deadlineDate->format('Y-m-d') : ($project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') : '') }}" 
                                   max="{{ $deadlineDate ? $deadlineDate->format('Y-m-d') : ($project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') : '') }}"
                                   required
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none">
                        </div>
                    </div>
                    <p class="text-[10.5px] text-slate-500">
                        * Durasi pengerjaan task WBS berjalan di dalam rentang pelaksanaan ini dan <strong>tidak boleh melebihi batas akhir kontrak</strong> proyek.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    {{-- Opsi Tindakan Impor (5 cols) --}}
                    <div class="md:col-span-5 space-y-2">
                        <label class="block text-xs font-bold text-slate-800">
                            Opsi Pengolahan Data:
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                                   :class="mode === 'append' ? 'border-[#8F0A0D] bg-red-50/40 text-slate-900 shadow-2xs ring-1 ring-[#8F0A0D]/20' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-600'">
                                <input type="radio" name="mode" value="append" x-model="mode" class="mt-0.5 text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                <div class="text-xs leading-snug">
                                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                        <span>Tambahkan (Append)</span>
                                        <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold bg-emerald-100 text-emerald-800">Default</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Task baru ditambahkan, task lama tetap utuh.</div>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition"
                                   :class="mode === 'replace' ? 'border-rose-600 bg-rose-50/40 text-slate-900 shadow-2xs ring-1 ring-rose-600/20' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-600'">
                                <input type="radio" name="mode" value="replace" x-model="mode" class="mt-0.5 text-rose-600 focus:ring-rose-600">
                                <div class="text-xs leading-snug">
                                    <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                        <span>Timpa / Reset (Replace)</span>
                                        <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold bg-rose-100 text-rose-700">Hapus Lama</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Seluruh task lama dihapus dan diganti file baru.</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Upload Dropzone (7 cols) --}}
                    <div class="md:col-span-7 space-y-2">
                        <label class="block text-xs font-bold text-slate-800">
                            Pilih Berkas Spreadsheet <span class="text-rose-600">*</span>:
                        </label>
                        
                        <div class="relative border-2 border-dashed rounded-xl p-4 sm:p-5 text-center cursor-pointer transition"
                             :class="fileName ? 'border-emerald-500 bg-emerald-50/30' : 'border-slate-300 hover:border-[#8F0A0D] bg-slate-50/50 hover:bg-red-50/20'">
                            <input type="file" 
                                   name="excel_file" 
                                   required 
                                   accept=".xlsx,.xls,.csv" 
                                   @change="handleFileSelect($event)" 
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            
                            {{-- State: No file chosen --}}
                            <div x-show="!fileName" class="space-y-1.5 pointer-events-none">
                                <div class="w-10 h-10 mx-auto rounded-full bg-red-50 text-[#8F0A0D] flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>
                                <div class="text-xs font-bold text-slate-800">Pilih berkas spreadsheet atau seret ke sini</div>
                                <div class="text-[10.5px] text-slate-400">Mendukung format .XLSX, .XLS, .CSV (Maksimal 10 MB)</div>
                            </div>

                            {{-- State: File selected --}}
                            <div x-show="fileName" x-cloak class="space-y-1.5 pointer-events-none">
                                <div class="w-10 h-10 mx-auto rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="text-xs font-extrabold text-emerald-900 truncate max-w-xs mx-auto" x-text="fileName"></div>
                                <div class="text-[11px] text-emerald-700" x-text="'Ukuran: ' + fileSize"></div>
                                <div class="text-[10.5px] text-slate-400">Klik lagi untuk mengganti file</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                    <div class="text-[11px] text-slate-400 hidden sm:block">
                        Format didukung: <strong>.xlsx</strong>, <strong>.xls</strong>, <strong>.csv</strong>
                    </div>
                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <button type="button" @click="showImportExcelModal = false" class="px-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs font-bold rounded-xl btn-ipnet-primary transition shadow-xs flex items-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"/>
                            </svg>
                            <span>Unggah & Buat Task Otomatis</span>
                        </button>
                    </div>
                </div>
            </form>
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
        // 1. S-Curve Dual Line Chart (Plan Kumulatif vs Actual Kumulatif)
        const sCurveLabels = {!! json_encode($sCurveLabels ?? []) !!};
        const sCurvePlan = {!! json_encode($sCurvePlan ?? []) !!};
        const sCurveActual = {!! json_encode($sCurveActual ?? []) !!};

        const sCurveCanvas = document.getElementById('sCurveDualLineChart');
        window.sCurveChartInstance = null;
        let sCurveChartInstance = null;

        if (sCurveCanvas) {
            window.sCurveChartInstance = sCurveChartInstance = new Chart(sCurveCanvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: sCurveLabels,
                    datasets: [
                        {
                            label: 'Plan Cumulative (%)',
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
                            label: 'Chart helper (hidden pts = not reported)',
                            data: sCurveActual,
                            borderColor: '#C2410C', // Orange helper line
                            backgroundColor: 'transparent',
                            borderWidth: 2.5,
                            tension: 0.2,
                            pointStyle: 'rect',
                            pointRadius: 5,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#C2410C',
                            pointBorderColor: '#FFFFFF',
                            pointBorderWidth: 1.5,
                            spanGaps: false
                        }
                    ]
                },
                plugins: [{
                    id: 'sCurveDataLabels',
                    afterDatasetsDraw(chart) {
                        const ctx = chart.ctx;
                        ctx.save();
                        ctx.font = 'bold 11px sans-serif';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';

                        chart.data.datasets.forEach((dataset, dIdx) => {
                            const meta = chart.getDatasetMeta(dIdx);
                            if (meta.hidden) return;

                            meta.data.forEach((element, pIdx) => {
                                const val = dataset.data[pIdx];
                                if (val === null || val === undefined || isNaN(val)) return;

                                ctx.fillStyle = dataset.borderColor;
                                // At week 1: actual point is higher (26.7%), plan point is lower (23.3%)
                                let yOffset = -14;
                                if (dIdx === 0 && pIdx === 1) {
                                    yOffset = 16;
                                } else if (dIdx === 0) {
                                    yOffset = -14;
                                }
                                ctx.fillText(val.toFixed(1) + '%', element.x, element.y + yOffset);
                            });
                        });
                        ctx.restore();
                    }
                }],
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 20,
                            bottom: 10,
                            left: 10,
                            right: 15
                        }
                    },
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
                                text: 'Week',
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
                            max: 105,
                            ticks: {
                                stepSize: 10,
                                callback: function(value) {
                                    return value <= 100 ? value + '%' : '';
                                },
                                color: '#64748B',
                                font: { size: 11, weight: '600' }
                            },
                            title: {
                                display: true,
                                text: 'Cumulative Progress (%)',
                                color: '#64748B',
                                font: { size: 11, weight: 'bold' }
                            },
                            grid: {
                                color: '#E2E8F0',
                                borderDash: [2, 2]
                            }
                        }
                    }
                }
            });

            window.renderSCurveChart = function() {
                if (!sCurveChartInstance) return;
                sCurveChartInstance.data.labels = sCurveLabels;
                sCurveChartInstance.data.datasets[0].data = sCurvePlan;
                sCurveChartInstance.data.datasets[1].data = sCurveActual;
                sCurveChartInstance.data.datasets[0].pointRadius = 3.5;
                sCurveChartInstance.data.datasets[1].pointRadius = 5.0;
                sCurveChartInstance.options.scales.x.title.text = 'Minggu Pelaksanaan (Minggu ke-0 s/d {{ $totalWeeks }})';
                sCurveChartInstance.resize();
                sCurveChartInstance.update();
            };

            const taskWeeklyMatrix = {!! json_encode($weeklyWeightMatrix ?? []) !!};
            const totalWeeksCount = {{ (int) ($totalWeeks ?? 5) }};
            const planCumulativeMap = {!! json_encode($totalWeeklyWeightCum ?? []) !!};

            window.updateLiveSCurveActual = function(taskProgressMap) {
                if (!taskProgressMap) return;

                let weeklyActual = {};
                for (let w = 1; w <= totalWeeksCount; w++) {
                    weeklyActual[w] = 0.0;
                }

                let overallSum = 0.0;
                taskWeeklyMatrix.forEach(row => {
                    const p = taskProgressMap[row.id] !== undefined ? parseFloat(taskProgressMap[row.id]) : 0;
                    const progRatio = isNaN(p) ? 0 : Math.max(0, Math.min(100, p)) / 100.0;
                    const taskWeight = parseFloat(row.weight) || 0;
                    overallSum += (taskWeight * progRatio);

                    if (progRatio > 0 && row.weeks) {
                        for (let w = 1; w <= totalWeeksCount; w++) {
                            const wWeight = parseFloat(row.weeks[w]) || 0;
                            if (wWeight > 0) {
                                weeklyActual[w] += (wWeight * progRatio);
                            }
                        }
                    }
                });

                let maxActWeek = 0;
                for (let w = 1; w <= totalWeeksCount; w++) {
                    weeklyActual[w] = Math.round(weeklyActual[w] * 10) / 10;
                    if (weeklyActual[w] > 0) {
                        maxActWeek = w;
                    }
                }

                let accum = 0.0;
                let chartActualData = [0.0]; // Week 0 Baseline

                for (let w = 1; w <= totalWeeksCount; w++) {
                    accum = Math.round((accum + weeklyActual[w]) * 10) / 10;
                    const planCum = planCumulativeMap[w] !== undefined ? parseFloat(planCumulativeMap[w]) : 0.0;

                    const elActWeekly = document.getElementById(`scurve-actual-weekly-w${w}`);
                    const elActCum = document.getElementById(`scurve-actual-cum-w${w}`);
                    const elDev = document.getElementById(`scurve-variance-w${w}`);
                    const elHlp = document.getElementById(`scurve-helper-w${w}`);

                    if (w <= maxActWeek || (w === 1 && overallSum > 0)) {
                        const dev = Math.round((accum - planCum) * 10) / 10;
                        const devStr = (dev > 0 ? '+' : '') + dev.toFixed(1) + '%';

                        if (elActWeekly) elActWeekly.textContent = weeklyActual[w].toFixed(1) + '%';
                        if (elActCum) elActCum.textContent = accum.toFixed(1) + '%';
                        if (elDev) {
                            elDev.textContent = devStr;
                            elDev.className = dev < 0 ? 'py-2.5 px-3 text-right font-bold text-rose-700' : 'py-2.5 px-3 text-right font-bold text-slate-800';
                        }
                        if (elHlp) {
                            elHlp.textContent = accum.toFixed(1) + '%';
                            elHlp.className = 'py-2.5 px-3 text-right text-xs font-bold text-slate-800';
                        }
                        chartActualData.push(accum);
                    } else {
                        if (elActWeekly) elActWeekly.textContent = '';
                        if (elActCum) elActCum.textContent = '';
                        if (elDev) elDev.textContent = '';
                        if (elHlp) {
                            elHlp.textContent = '#N/A';
                            elHlp.className = 'py-2.5 px-3 text-right text-xs text-slate-400 italic';
                        }
                        chartActualData.push(null);
                    }
                }

                if (sCurveChartInstance && sCurveChartInstance.data.datasets[1]) {
                    sCurveChartInstance.data.datasets[1].data = chartActualData;
                    sCurveChartInstance.update();
                }

                const tab3Badge = document.getElementById('tab-actual-progress-badge');
                if (tab3Badge) tab3Badge.textContent = overallSum.toFixed(1) + '%';
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
