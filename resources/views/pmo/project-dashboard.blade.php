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
         showExportCustomModal: false,
         customShowWeekly: true,
         customShowScurve: true,
         customShowExpenses: true,
         customReportNo: {{ $nextExportNumber ?? 1 }},
         getCustomExportUrl() {
             let url = '{{ route('pmo.projects.export_pdf', $project->id) }}?type=custom';
             url += '&show_weekly=' + (this.customShowWeekly ? '1' : '0');
             url += '&show_scurve=' + (this.customShowScurve ? '1' : '0');
             url += '&show_expenses=' + (this.customShowExpenses ? '1' : '0');
             if (this.customReportNo) {
                 url += '&report_no=' + this.customReportNo;
             }
             return url;
         },
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
         scheduleType: '{{ $scheduleType ?? 'waterfall' }}',
         newTaskStartingDay: {{ $maxTaskFinishDay ?? 0 }},
         newTaskPlannedStart: {{ $maxTaskFinishDay ?? 0 }},
         newTaskDuration: 1,
         newTaskBobot: 10.0,
         newTaskDailyWeight: 1.03,
         manualNewDailyWeight: false,
         newTaskFinishDay: {{ ($maxTaskFinishDay ?? 0) + 1 }},
         newTaskWeek: {{ max(1, (int) ceil((($maxTaskFinishDay ?? 0) + 1) / 7)) }},
         showEditTaskModal: false,
         editTaskId: null,
         editTaskTitle: '',
         editTaskDuration: 1,
         editTaskStartingDay: 0,
         editTaskPlannedStart: 0,
         editTaskFinishDay: 1,
         editTaskWeek: 1,
         editTaskBobot: 0,
         editTaskDailyWeight: 0,
         manualEditDailyWeight: false,
         editTaskProgress: 0,
         editTaskStatus: 'In Progress',
         editTaskPriority: 'Medium',
         editTaskEngineerId: '',
         editTaskDescription: '',
         openEditTaskModal(task) {
             this.editTaskId = task.id;
             this.editTaskTitle = task.title;
             this.editTaskStartingDay = task.starting_day !== undefined ? parseInt(task.starting_day, 10) : 0;
             this.editTaskPlannedStart = this.editTaskStartingDay;
             this.editTaskDuration = task.duration_days ? parseInt(task.duration_days, 10) : 1;
             this.editTaskFinishDay = task.finish_day !== undefined ? parseInt(task.finish_day, 10) : (this.editTaskStartingDay + this.editTaskDuration);
             this.editTaskWeek = (task.week !== undefined && task.week !== null && task.week !== '') 
                 ? parseInt(task.week, 10) 
                 : Math.max(1, Math.ceil(this.editTaskFinishDay / 7));
             this.editTaskBobot = task.bobot !== undefined ? task.bobot : 0;
             this.editTaskDailyWeight = (task.daily_weight !== undefined && task.daily_weight !== null) 
                 ? task.daily_weight 
                 : 1.03;
             this.manualEditDailyWeight = true;
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
         },
         init() {
             this.$watch('showTimelineModal', val => { if(val) setTimeout(() => window.initFlatpickr && window.initFlatpickr(), 50); });
             this.$watch('showImportExcelModal', val => { if(val) setTimeout(() => window.initFlatpickr && window.initFlatpickr(), 50); });
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
                        @php
                            $estDuration = $project->handover_data['estimate_project_duration'] ?? null;
                            $isPpatkBadge = ($estDuration == 30) || str_contains(strtolower($project->name), 'ppat') || str_contains(strtolower($project->client ?? ''), 'ppat');
                        @endphp

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

                    {{-- Chip 2: Periode Tanggal Proyek (Dari Berapa s/d Berapa Berdasarkan Kontrak) --}}
                    <div class="p-2.5 bg-slate-50/80 rounded-xl border border-slate-200/80 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-7 h-7 rounded-lg bg-white border border-slate-200 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">Periode Tanggal Proyek (Kontrak)</span>
                                <span class="font-bold text-slate-800 truncate block" title="{{ $startDate->format('d M Y') }} s/d {{ $deadlineDate->format('d M Y') }}">
                                    {{ $startDate->format('d M Y') }} s/d {{ $deadlineDate->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                        <button type="button" 
                                @click="showTimelineModal = true" 
                                title="Ubah Periode Tanggal & Durasi"
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
                            <span id="header-metric-actual-text-1" class="text-xs font-bold text-slate-800 ml-auto">Actual: {{ number_format($currentProgressPercent, 1) }}%</span>
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
                <div id="header-metric-status-card" class="ipnet-metric-card border-l-4 {{ $isStatusOnTrack ? 'border-l-emerald-600' : ($isStatusAtRisk ? 'border-l-amber-500' : 'border-l-rose-600') }} flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-[#75727C] uppercase tracking-wider">Status Jadwal (Kurva S)</span>
                            <div id="header-metric-status-icon-wrap" class="w-7 h-7 rounded-lg {{ $isStatusOnTrack ? 'bg-emerald-50 text-emerald-600' : ($isStatusAtRisk ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }} flex items-center justify-center">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span id="header-metric-status-dot" class="w-2.5 h-2.5 rounded-full {{ $isStatusOnTrack ? 'bg-emerald-500' : ($isStatusAtRisk ? 'bg-amber-500' : 'bg-rose-500') }}"></span>
                            <span id="header-metric-status-label" class="text-[20px] font-extrabold text-[#1E293B] tracking-tight">
                                {{ $isStatusOnTrack ? 'On-Track' : ($isStatusAtRisk ? 'At-Risk' : 'Delayed') }}
                            </span>
                            <span id="header-metric-status-variance" class="text-xs font-bold {{ $isStatusOnTrack ? 'text-emerald-700' : ($isStatusAtRisk ? 'text-amber-700' : 'text-rose-700') }}">
                                ({{ ($currentVariance ?? 0) >= 0 ? '+' : '' }}{{ number_format($currentVariance ?? 0, 1) }}%)
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-[#64748B] mt-2.5 pt-2 border-t border-slate-100 font-medium">
                        <span>Target: {{ $planValAtCurrentWeek !== null ? number_format($planValAtCurrentWeek, 1) . '%' : '-' }}</span>
                        <span id="header-metric-actual-text-2" class="font-bold text-slate-800">
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
                              :class="activeMainTab === 'actual_progress' ? 'bg-white/20 text-white' : 'bg-red-50 text-[#8F0A0D] border border-red-200'">
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

                {{-- Tombol Utama Export PDF (Pilihan: Lengkap / Internal PMO vs Klien) --}}
                <div class="flex items-center gap-2 shrink-0 relative" x-data="{ openExportMain: false }">
                    <button type="button" 
                            @click="openExportMain = !openExportMain" 
                            @click.outside="openExportMain = false"
                            title="Unduh Laporan Proyek (PDF)"
                            class="px-3.5 py-2 rounded-xl bg-white hover:bg-red-50 text-slate-700 hover:text-[#8F0A0D] border border-slate-200 hover:border-red-200 text-xs font-bold shadow-2xs transition inline-flex items-center gap-2 cursor-pointer active:scale-95">
                        <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Export PDF</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openExportMain }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>

                    <div x-show="openExportMain" 
                         x-cloak 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 top-full mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 z-50 text-xs">
                                                <div class="px-3 py-2 border-b border-slate-100 bg-slate-50/70 rounded-t-xl flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Kategori Laporan PDF</span>
                            <span class="text-[10px] font-semibold text-slate-400">4 Pilihan</span>
                        </div>

                        {{-- 1. Weekly Progress --}}
                        <a href="{{ route('pmo.projects.export_pdf', ['project' => $project->id, 'type' => 'weekly']) }}&_t={{ time() }}" 
                           target="_blank" 
                           @click="openExportMain = false"
                           class="flex items-start gap-2.5 px-3 py-2 hover:bg-slate-50 transition text-slate-700 hover:text-slate-900 border-b border-slate-100">
                            <div class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[11px]">
                                1
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-[11.5px] flex items-center gap-1.5">
                                    Weekly Progress
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 font-semibold">WBS</span>
                                </div>
                                <div class="text-[10.5px] text-slate-500">Jadwal kerja &amp; deliverable mingguan</div>
                            </div>
                        </a>

                        {{-- 2. Curva S --}}
                        <a href="{{ route('pmo.projects.export_pdf', ['project' => $project->id, 'type' => 'scurve']) }}&_t={{ time() }}" 
                           target="_blank" 
                           @click="openExportMain = false"
                           class="flex items-start gap-2.5 px-3 py-2 hover:bg-slate-50 transition text-slate-700 hover:text-slate-900 border-b border-slate-100">
                            <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[11px]">
                                2
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-[11.5px] flex items-center gap-1.5">
                                    Curva S
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 font-semibold">Grafik</span>
                                </div>
                                <div class="text-[10.5px] text-slate-500">Grafik Kurva S &amp; matriks kumulatif</div>
                            </div>
                        </a>

                        {{-- 3. Expenses --}}
                        <a href="{{ route('pmo.projects.export_pdf', ['project' => $project->id, 'type' => 'expenses']) }}&_t={{ time() }}" 
                           target="_blank" 
                           @click="openExportMain = false"
                           class="flex items-start gap-2.5 px-3 py-2 hover:bg-slate-50 transition text-slate-700 hover:text-slate-900 border-b border-slate-100">
                            <div class="w-6 h-6 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[11px]">
                                3
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-[11.5px] flex items-center gap-1.5">
                                    Expenses
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-semibold">Biaya</span>
                                </div>
                                <div class="text-[10.5px] text-slate-500">Rekap budget &amp; realisasi pengeluaran</div>
                            </div>
                        </a>

                        {{-- 4. Versi Lengkap --}}
                        <a href="{{ route('pmo.projects.export_pdf', ['project' => $project->id, 'type' => 'full']) }}&_t={{ time() }}" 
                           target="_blank" 
                           @click="openExportMain = false"
                           class="flex items-start gap-2.5 px-3 py-2 hover:bg-slate-50 transition text-slate-700 hover:text-slate-900 border-b border-slate-100">
                            <div class="w-6 h-6 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0 mt-0.5 font-bold text-[11px]">
                                4
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-[11.5px] flex items-center gap-1.5">
                                    Versi Lengkap
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-red-100 text-[#8F0A0D] font-semibold">Full</span>
                                </div>
                                <div class="text-[10.5px] text-slate-500">Semua: Kurva S, WBS &amp; Expenses</div>
                            </div>
                        </a>

                        {{-- Opsi Kustomisasi --}}
                        <button type="button" 
                                @click="openExportMain = false; showExportCustomModal = true"
                                class="w-full flex items-center justify-between px-3 py-2 hover:bg-slate-50 transition text-slate-600 hover:text-slate-900 text-left cursor-pointer">
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                <span class="text-[11px] font-semibold">Pilih Bagian (Kustom)...</span>
                            </div>
                            <span class="text-[10px] text-slate-400">&rarr;</span>
                        </button>
                    </div>
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
                        @if($isLeadUser)
                            <div class="flex items-center gap-2 flex-wrap">
                                <button type="button" @click="showAssignTeamModal = true" class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-[11px] font-bold text-slate-700 transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    <span>Kelola Personil Tim</span>
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Excel Metadata Header (Gambar 2 / Gambar 10 & Schedule Type Selector) --}}
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-xs text-left">
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="w-64 py-2.5 px-4 font-bold text-slate-800 border-r border-slate-200 align-middle">
                                        Project Schedule Type
                                    </td>
                                    <td class="py-2.5 px-4">
                                        <form action="{{ route('pmo.projects.update_schedule_type', $project->id) }}" method="POST" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                            @csrf
                                            <div class="flex items-center gap-6">
                                                <label class="inline-flex items-center gap-2 cursor-pointer group">
                                                    <input type="radio" 
                                                           name="schedule_type" 
                                                           value="waterfall" 
                                                           {{ $scheduleType === 'waterfall' ? 'checked' : '' }} 
                                                           onchange="this.form.submit()" 
                                                           class="w-4 h-4 text-[#8F0A0D] border-slate-300 focus:ring-[#8F0A0D] cursor-pointer accent-[#8F0A0D]">
                                                    <span class="text-xs font-bold {{ $scheduleType === 'waterfall' ? 'text-slate-900' : 'text-slate-600 group-hover:text-slate-900' }}">
                                                        Waterfall
                                                    </span>
                                                    <span class="text-[11px] text-slate-400 font-normal">(Sequential)</span>
                                                </label>

                                                <label class="inline-flex items-center gap-2 cursor-pointer group">
                                                    <input type="radio" 
                                                           name="schedule_type" 
                                                           value="hybrid" 
                                                           {{ $scheduleType === 'hybrid' ? 'checked' : '' }} 
                                                           onchange="this.form.submit()" 
                                                           class="w-4 h-4 text-[#8F0A0D] border-slate-300 focus:ring-[#8F0A0D] cursor-pointer accent-[#8F0A0D]">
                                                    <span class="text-xs font-bold {{ $scheduleType === 'hybrid' ? 'text-[#8F0A0D]' : 'text-slate-600 group-hover:text-slate-900' }}">
                                                        Hybrid
                                                    </span>
                                                    <span class="text-[11px] text-slate-400 font-normal">(Overlapping)</span>
                                                </label>
                                            </div>

                                            <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $scheduleType === 'hybrid' ? 'bg-blue-600' : 'bg-slate-400' }}"></span>
                                                <span>
                                                    {{ $scheduleType === 'hybrid' 
                                                        ? 'Pekerjaan dapat berjalan overlapping / paralel sesuai Planned Start' 
                                                        : 'Pekerjaan berjalan berurutan sekuensial (Finish-to-Start otomatis)' }}
                                                </span>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
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
                                    <td class="py-2 px-4 font-medium text-slate-900">{{ $customerPic ?? ($project->customer_pic_technical ?: ($project->customer_pic_business ?: ($handoverData['customer_pic'] ?? '-'))) }}</td>
                                </tr>
                                <tr>
                                    <td class="py-2 px-4 font-bold text-slate-800 border-r border-slate-200">Contract/SPK/PO Number</td>
                                    <td class="py-2 px-4 font-mono font-bold text-slate-900">{{ $contractSpkNumber ?? ($project->po_spk_number ?: ($handoverData['contract_spk_number'] ?? '-')) }}</td>
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

                    {{-- Personil Tim Lapangan Chips (Dihilangkan/Hide sesuai permintaan) --}}
                    @if(false && count($assignedEngineers) > 0)
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

                    {{-- Schedule WBS Table (Waterfall vs Hybrid Adaptive) --}}
                    <div class="overflow-x-auto overflow-y-auto max-h-[500px] rounded-xl border border-slate-200 relative shadow-2xs">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="sticky top-0 z-10 bg-[#F8FAFC] shadow-xs">
                                <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold text-[#64748B] uppercase tracking-wider">
                                    <th class="py-3 px-4 min-w-[240px] bg-[#F8FAFC]">Task Name</th>
                                    <th class="py-3 px-3 text-center whitespace-nowrap bg-[#F8FAFC]">Duration (Days)</th>
                                    @if($scheduleType === 'hybrid')
                                        <th class="py-3 px-3 text-center whitespace-nowrap bg-blue-50 text-blue-800">Planned Start</th>
                                        <th class="py-3 px-3 text-center whitespace-nowrap bg-[#F8FAFC]">Start Offset</th>
                                        <th class="py-3 px-3 text-center whitespace-nowrap bg-[#F8FAFC]">Finish Offset</th>
                                    @else
                                        <th class="py-3 px-3 text-center whitespace-nowrap bg-[#F8FAFC]">Starting Day</th>
                                        <th class="py-3 px-3 text-center whitespace-nowrap bg-[#F8FAFC]">Finish Day</th>
                                    @endif
                                    <th class="py-3 px-3 text-center whitespace-nowrap bg-[#F8FAFC]">Week</th>
                                    <th class="py-3 px-3 text-right whitespace-nowrap bg-[#F8FAFC]">Activity Weight (%)</th>
                                    <th class="py-3 px-3 text-right whitespace-nowrap bg-[#F8FAFC]">Daily Weight (%)</th>
                                    <th class="py-3 px-3 text-right whitespace-nowrap bg-[#F8FAFC]">Aksi</th>
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
                                        $dWeight = isset($row['daily_weight']) && $row['daily_weight'] !== null
                                            ? (float) $row['daily_weight']
                                            : ($t && preg_match('/DailyWeight:\s*([\d\.]+)%/i', $t->description ?? '', $dwm)
                                                ? (float) $dwm[1]
                                                : 1.03);
                                        $wWeek = $row['week'] ?? 1;
                                        if (!$wWeek || $wWeek <= 0) {
                                            if (isset($row['weeks'])) {
                                                foreach ($row['weeks'] as $wk => $wv) {
                                                    if ($wv > 0) { $wWeek = $wk; break; }
                                                }
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
                                        @if($scheduleType === 'hybrid')
                                            <td class="py-2.5 px-3 text-center font-bold text-blue-700 bg-blue-50/40">
                                                {{ $row['planned_start'] ?? $row['starting_day'] }}
                                            </td>
                                            <td class="py-2.5 px-3 text-center font-medium text-slate-600">
                                                {{ $row['starting_day'] }}
                                            </td>
                                            <td class="py-2.5 px-3 text-center font-medium text-slate-600">
                                                {{ $row['finish_day'] }}
                                            </td>
                                        @else
                                            <td class="py-2.5 px-3 text-center font-medium text-slate-600">
                                                {{ $row['starting_day'] }}
                                            </td>
                                            <td class="py-2.5 px-3 text-center font-medium text-slate-600">
                                                {{ $row['finish_day'] }}
                                            </td>
                                        @endif
                                        <td class="py-2.5 px-3 text-center">
                                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $wWeek }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-bold text-blue-700">
                                            {{ number_format($row['weight'], 2) }}%
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-medium text-slate-700">
                                            {{ number_format($dWeight, 2) }}%
                                        </td>
                                        <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                            @if($t)
                                                <div class="flex items-center justify-end gap-1.5">
                                                    <button type="button" 
                                                            @click="openEditTaskModal({
                                                                id: {{ $t->id }},
                                                                title: {{ json_encode($t->title) }},
                                                                duration_days: {{ $row['duration'] }},
                                                                planned_start: {{ $row['planned_start'] ?? $row['starting_day'] }},
                                                                starting_day: {{ $row['starting_day'] }},
                                                                finish_day: {{ $row['finish_day'] }},
                                                                week: {{ $row['week'] ?? $wWeek }},
                                                                start_date: '{{ $t->start_date ? \Carbon\Carbon::parse($t->start_date)->format('Y-m-d') : '' }}',
                                                                deadline: '{{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('Y-m-d') : '' }}',
                                                                bobot: {{ $row['weight'] }},
                                                                daily_weight: {{ $dWeight }},
                                                                progress: {{ (int)($t->progress ?: 0) }},
                                                                status: '{{ $t->status }}',
                                                                priority: '{{ $t->priority ?: 'Medium' }}',
                                                                engineer_id: '{{ $t->engineer_id }}',
                                                                description: {{ json_encode($t->description ?? '') }}
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
                                        <td colspan="{{ $scheduleType === 'hybrid' ? 9 : 8 }}" class="text-center py-6 text-slate-400">Belum ada task WBS.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="sticky bottom-0 z-10 bg-slate-50 border-t-2 border-slate-200 font-bold text-xs text-slate-800 shadow-xs">
                                <tr>
                                    <td class="py-2.5 px-4 uppercase tracking-wider text-[11px] text-slate-600">
                                        Total Rekap Schedule ({{ count($weeklyWeightMatrix) }} Tasks)
                                    </td>
                                    <td class="py-2.5 px-3 text-center text-slate-800 font-bold text-xs">
                                        {{ $totDur }} {{ $scheduleType === 'hybrid' ? 'Mandays' : 'Hari' }}
                                    </td>
                                    @if($scheduleType === 'hybrid')
                                        <td colspan="4" class="py-2.5 px-3 text-center text-slate-600 font-semibold text-xs">
                                            Kalender: Day 0 s/d Day {{ $totalProjectDays }} ({{ $totalProjectDays }} Hari Pelaksanaan / {{ $totalWeeks }} Minggu)
                                        </td>
                                    @else
                                        <td colspan="3" class="py-2.5 px-3 text-center text-slate-400 font-normal text-xs">
                                            Day 0 s/d Day {{ $totDur }} ({{ $totalWeeks }} Minggu)
                                        </td>
                                    @endif
                                    <td class="py-2.5 px-3 text-right text-blue-800 font-bold text-xs">
                                        {{ number_format($totActW, 1) }}%
                                    </td>
                                    @php
                                        $sampleDailyWeight = null;
                                        foreach($weeklyWeightMatrix as $r) {
                                            if (!empty($r['daily_weight'])) {
                                                $sampleDailyWeight = (float) $r['daily_weight'];
                                                break;
                                            }
                                        }
                                        $footerDailyWeight = $sampleDailyWeight !== null ? $sampleDailyWeight : 1.03;
                                    @endphp
                                    <td class="py-2.5 px-3 text-right text-slate-700 font-bold text-xs">
                                        {{ number_format($footerDailyWeight, 2) }}%
                                    </td>
                                    <td class="py-2.5 px-3 text-right text-slate-500 font-normal text-[11px]">
                                        100% Selesai
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- WBS Action Footer --}}
                    <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <span class="text-xs text-slate-500 font-medium">
                            Struktur Rincian Kerja (WBS) Proyek
                        </span>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <button type="button" 
                                    @click="showImportExcelModal = true" 
                                    class="px-3.5 py-2 rounded-xl border border-emerald-600 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer active:scale-95">
                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Import Excel (WBS)</span>
                            </button>
                            <button type="button" 
                                    @click="showQuickTaskModal = true" 
                                    class="px-3.5 py-2 rounded-xl bg-[#8F0A0D] hover:bg-[#73080A] text-white text-xs font-bold shadow-xs transition inline-flex items-center justify-center gap-1.5 cursor-pointer active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                <span>Tambah Aktivitas WBS</span>
                            </button>
                        </div>
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
                                Total Bobot: {{ number_format(array_sum(array_column($weeklyWeightMatrix, 'weight')), 1) }}%
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto overflow-y-auto max-h-[500px] rounded-xl border border-slate-200 relative shadow-2xs">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="sticky top-0 z-10 bg-[#F8FAFC] shadow-xs">
                                <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold text-[#64748B] uppercase tracking-wider">
                                    <th class="py-3 px-3.5 min-w-[220px] bg-[#F8FAFC]">Task Name</th>
                                    <th class="py-3 px-2.5 text-center whitespace-nowrap bg-[#F8FAFC]">Duration</th>
                                    @if($scheduleType === 'hybrid')
                                        <th class="py-3 px-2 text-center whitespace-nowrap bg-blue-50 text-blue-800">Planned Start</th>
                                        <th class="py-3 px-2 text-center whitespace-nowrap bg-[#F8FAFC]">Start Offset</th>
                                        <th class="py-3 px-2 text-center whitespace-nowrap bg-[#F8FAFC]">Finish Offset</th>
                                    @else
                                        <th class="py-3 px-2.5 text-center whitespace-nowrap bg-[#F8FAFC]">Starting Day</th>
                                        <th class="py-3 px-2.5 text-center whitespace-nowrap bg-[#F8FAFC]">Finish Day</th>
                                    @endif
                                    <th class="py-3 px-3 text-right whitespace-nowrap bg-[#F8FAFC]">Activity Weight (%)</th>
                                    @for($w = 1; $w <= $totalWeeks; $w++)
                                        <th class="py-3 px-3 text-right whitespace-nowrap bg-[#F8FAFC]">Week {{ $w }}</th>
                                    @endfor
                                    <th class="py-3 px-3 text-right whitespace-nowrap bg-slate-200/50">Check (=weight)</th>
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
                                        @if($scheduleType === 'hybrid')
                                            <td class="py-2.5 px-2 text-center font-bold text-blue-700 bg-blue-50/30">
                                                {{ $row['planned_start'] ?? $row['starting_day'] }}
                                            </td>
                                            <td class="py-2.5 px-2 text-center text-slate-600">
                                                {{ $row['starting_day'] }}
                                            </td>
                                            <td class="py-2.5 px-2 text-center text-slate-600">
                                                {{ $row['finish_day'] }}
                                            </td>
                                        @else
                                            <td class="py-2.5 px-2.5 text-center text-slate-600">
                                                {{ $row['starting_day'] }}
                                            </td>
                                            <td class="py-2.5 px-2.5 text-center text-slate-600">
                                                {{ $row['finish_day'] }}
                                            </td>
                                        @endif
                                        <td class="py-2.5 px-3 text-right font-bold text-slate-900">
                                            {{ number_format($row['weight'], 2) }}%
                                        </td>
                                        @for($w = 1; $w <= $totalWeeks; $w++)
                                            <td class="py-2.5 px-3 text-right text-xs {{ ($row['weeks'][$w] ?? 0) > 0 ? 'font-bold text-blue-700 bg-blue-50/30' : 'text-slate-400' }}">
                                                {{ number_format($row['weeks'][$w] ?? 0.0, 2) }}%
                                            </td>
                                        @endfor
                                        <td class="py-2.5 px-3 text-right font-bold text-slate-900 bg-slate-50/50">
                                            {{ number_format($row['check'] ?? $row['weight'], 2) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="sticky bottom-0 z-10 border-t-2 border-slate-300 font-bold text-xs shadow-xs">
                                {{-- Row 1: Plan Weekly (%) --}}
                                <tr class="bg-slate-100 text-slate-900">
                                    <td class="py-2.5 px-3.5 font-bold uppercase text-[11px] text-slate-800">
                                        Plan Weekly (%)
                                    </td>
                                    <td colspan="{{ $scheduleType === 'hybrid' ? 4 : 3 }}" class="py-2.5 px-2.5"></td>
                                    <td class="py-2.5 px-3 text-right font-bold text-slate-800 text-xs">
                                        {{ number_format(array_sum(array_column($weeklyWeightMatrix, 'weight')), 1) }}%
                                    </td>
                                    @for($w = 1; $w <= $totalWeeks; $w++)
                                        <td class="py-2.5 px-3 text-right font-bold text-slate-800 text-xs">
                                            {{ number_format($totalWeeklyWeightPlan[$w] ?? 0.0, 2) }}%
                                        </td>
                                    @endfor
                                    <td class="py-2.5 px-3 text-right font-bold text-slate-800 text-xs bg-slate-200/50">
                                        {{ number_format(array_sum(array_column($weeklyWeightMatrix, 'check')), 1) }}%
                                    </td>
                                </tr>
                                {{-- Row 2: Plan Cumulative (%) --}}
                                <tr class="bg-slate-200/80 text-blue-900">
                                    <td class="py-2.5 px-3.5 font-bold uppercase text-[11px] text-blue-900">
                                        Plan Cumulative (%)
                                    </td>
                                    <td colspan="{{ $scheduleType === 'hybrid' ? 5 : 4 }}" class="py-2.5 px-2.5"></td>
                                    @for($w = 1; $w <= $totalWeeks; $w++)
                                        <td class="py-2.5 px-3 text-right font-bold text-blue-800 text-xs">
                                            {{ number_format($totalWeeklyWeightCum[$w] ?? 0.0, 2) }}%
                                        </td>
                                    @endfor
                                    <td class="py-2.5 px-3"></td>
                                </tr>
                                {{-- Row 3: Actual Cumulative (%) --}}
                                <tr class="bg-amber-50/80 text-amber-900">
                                    <td class="py-2.5 px-3.5 font-bold uppercase text-[11px] text-amber-900">
                                        Actual Cumulative (%)
                                    </td>
                                    <td colspan="{{ $scheduleType === 'hybrid' ? 5 : 4 }}" class="py-2.5 px-2.5"></td>
                                    @for($w = 1; $w <= $totalWeeks; $w++)
                                        <td id="tab2-actual-cum-w{{ $w }}" class="py-2.5 px-3 text-right font-bold text-amber-800 text-xs">
                                            {{ isset($actualCumulativePerWeek[$w]) && $actualCumulativePerWeek[$w] !== null ? number_format($actualCumulativePerWeek[$w], 2) . '%' : '-' }}
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
                    saveStatus: 'idle',
                    saveTimeout: null,
                    resetSavedTimeout: null,
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
                        if (this.saveTimeout) clearTimeout(this.saveTimeout);
                        this.saveStatus = 'saving';
                        this.saveTimeout = setTimeout(() => {
                            this.autoSaveProgress();
                        }, 500);
                    },
                    autoSaveProgress() {
                        if (this.saveTimeout) {
                            clearTimeout(this.saveTimeout);
                            this.saveTimeout = null;
                        }
                        this.saveStatus = 'saving';

                        const formData = new FormData();
                        formData.append('_token', '{{ csrf_token() }}');
                        formData.append('tab', 'actual_progress');
                        for (let id in this.taskProgress) {
                            let val = this.taskProgress[id];
                            if (val === '' || val === null || isNaN(val)) {
                                val = 0;
                            }
                            formData.append(`tasks[${id}][progress]`, val);
                        }

                        fetch('{{ route('pmo.projects.actual_progress_matrix.update', $project->id) }}', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            return res.json();
                        })
                        .then(data => {
                            this.saveStatus = 'saved';
                            if (this.resetSavedTimeout) clearTimeout(this.resetSavedTimeout);
                            this.resetSavedTimeout = setTimeout(() => {
                                if (this.saveStatus === 'saved') {
                                    this.saveStatus = 'idle';
                                }
                            }, 3000);
                        })
                        .catch(err => {
                            console.error('Auto-save progress error:', err);
                            this.saveStatus = 'error';
                        });
                    }
                 }">
                <form action="{{ route('pmo.projects.actual_progress_matrix.update', $project->id) }}" method="POST" @submit.prevent="autoSaveProgress()">
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
                                
                                {{-- Auto-save status indicator --}}
                                <div class="flex items-center text-xs font-medium">
                                    <template x-if="saveStatus === 'idle'">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-50 text-slate-600 border border-slate-200 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 00-9.78 2.096A4.001 4.001 0 003 15z"/>
                                            </svg>
                                            <span>Auto-save aktif</span>
                                        </span>
                                    </template>
                                    <template x-if="saveStatus === 'saving'">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 animate-pulse shadow-2xs">
                                            <svg class="w-3.5 h-3.5 animate-spin text-amber-600" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                            <span>Menyimpan otomatis...</span>
                                        </span>
                                    </template>
                                    <template x-if="saveStatus === 'saved'">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs transition">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            <span>Tersimpan otomatis</span>
                                        </span>
                                    </template>
                                    <template x-if="saveStatus === 'error'">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            <span>Gagal menyimpan</span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto overflow-y-auto max-h-[500px] rounded-xl border border-slate-200 relative shadow-2xs">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="sticky top-0 z-10 bg-[#F8FAFC] shadow-xs">
                                    <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold text-[#64748B] uppercase tracking-wider">
                                        <th class="py-3 px-4 min-w-[240px] bg-[#F8FAFC]">Task Name</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap bg-[#F8FAFC]">Activity Weight (%)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap min-w-[120px] bg-[#F8FAFC]">Progress (%)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap bg-[#F8FAFC]">Actual (%)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap bg-[#F8FAFC]">Aksi</th>
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
                                                           @change="autoSaveProgress()"
                                                           @blur="autoSaveProgress()"
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
                                                                    title: {{ json_encode($t->title) }},
                                                                    duration_days: {{ $t->duration ?? 1 }},
                                                                    start_date: '{{ $t->start_date ? \Carbon\Carbon::parse($t->start_date)->format('Y-m-d') : '' }}',
                                                                    deadline: '{{ $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('Y-m-d') : '' }}',
                                                                    bobot: {{ $row['weight'] }},
                                                                daily_weight: {{ $dWeight }},
                                                                    progress: taskProgress[{{ $row['id'] }}] !== undefined ? taskProgress[{{ $row['id'] }}] : {{ (int)($t->progress ?: 0) }},
                                                                    status: '{{ $t->status }}',
                                                                    priority: '{{ $t->priority ?: 'Medium' }}',
                                                                    engineer_id: '{{ $t->engineer_id }}',
                                                                    description: {{ json_encode($t->description ?? '') }}
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
                                <tfoot class="sticky bottom-0 z-10 border-t-2 border-slate-300 font-bold text-xs bg-slate-100 text-slate-800 shadow-xs">
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
                            <div class="flex items-center gap-2">
                                <h2 class="text-lg font-bold text-[#1E293B]">S-Curve Progress Proyek</h2>
                                <span class="px-2 py-0.5 rounded text-[10.5px] font-bold {{ $scheduleType === 'hybrid' ? 'bg-blue-100 text-blue-800 border border-blue-300' : 'bg-slate-100 text-slate-700 border border-slate-300' }}">
                                    {{ $scheduleType === 'hybrid' ? 'Hybrid (Concurrent)' : 'Waterfall (Sequential)' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $project->name }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                                <span class="w-3.5 h-1 rounded-full bg-[#1D4ED8]"></span>
                                <span>Plan Cumulative (%)</span>
                            </div>
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                                <span class="w-3.5 h-1 rounded-full bg-[#C2410C]"></span>
                                <span>Actual Cumulative (%)</span>
                            </div>
                            <div class="relative inline-block" x-data="{ openExportTab4: false }">
                                <button type="button" 
                                        @click="openExportTab4 = !openExportTab4" 
                                        @click.outside="openExportTab4 = false"
                                        title="Export Laporan S-Curve (PDF)"
                                        class="px-3 py-1.5 rounded-xl bg-white hover:bg-red-50 text-slate-700 hover:text-[#8F0A0D] border border-slate-200 hover:border-red-200 text-xs font-bold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Export PDF</span>
                                    <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openExportTab4 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>

                                <div x-show="openExportTab4" 
                                     x-cloak 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 top-full mt-1.5 w-68 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 z-50 text-xs">
                                                            <div class="px-3 py-2 border-b border-slate-100 bg-slate-50/70 rounded-t-xl flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Kategori Laporan PDF</span>
                            <span class="text-[10px] font-semibold text-slate-400">4 Pilihan</span>
                        </div>

                        {{-- 1. Weekly Progress --}}
                        <a href="{{ route('pmo.projects.export_pdf', ['project' => $project->id, 'type' => 'weekly']) }}&_t={{ time() }}" 
                           target="_blank" 
                           @click="openExportTab4 = false"
                           class="flex items-start gap-2.5 px-3 py-2 hover:bg-slate-50 transition text-slate-700 hover:text-slate-900 border-b border-slate-100">
                            <div class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[11px]">
                                1
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-[11.5px] flex items-center gap-1.5">
                                    Weekly Progress
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 font-semibold">WBS</span>
                                </div>
                                <div class="text-[10.5px] text-slate-500">Jadwal kerja &amp; deliverable mingguan</div>
                            </div>
                        </a>

                        {{-- 2. Curva S --}}
                        <a href="{{ route('pmo.projects.export_pdf', ['project' => $project->id, 'type' => 'scurve']) }}&_t={{ time() }}" 
                           target="_blank" 
                           @click="openExportTab4 = false"
                           class="flex items-start gap-2.5 px-3 py-2 hover:bg-slate-50 transition text-slate-700 hover:text-slate-900 border-b border-slate-100">
                            <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[11px]">
                                2
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-[11.5px] flex items-center gap-1.5">
                                    Curva S
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 font-semibold">Grafik</span>
                                </div>
                                <div class="text-[10.5px] text-slate-500">Grafik Kurva S &amp; matriks kumulatif</div>
                            </div>
                        </a>

                        {{-- 3. Expenses --}}
                        <a href="{{ route('pmo.projects.export_pdf', ['project' => $project->id, 'type' => 'expenses']) }}&_t={{ time() }}" 
                           target="_blank" 
                           @click="openExportTab4 = false"
                           class="flex items-start gap-2.5 px-3 py-2 hover:bg-slate-50 transition text-slate-700 hover:text-slate-900 border-b border-slate-100">
                            <div class="w-6 h-6 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[11px]">
                                3
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-[11.5px] flex items-center gap-1.5">
                                    Expenses
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-semibold">Biaya</span>
                                </div>
                                <div class="text-[10.5px] text-slate-500">Rekap budget &amp; realisasi pengeluaran</div>
                            </div>
                        </a>

                        {{-- 4. Versi Lengkap --}}
                        <a href="{{ route('pmo.projects.export_pdf', ['project' => $project->id, 'type' => 'full']) }}&_t={{ time() }}" 
                           target="_blank" 
                           @click="openExportTab4 = false"
                           class="flex items-start gap-2.5 px-3 py-2 hover:bg-slate-50 transition text-slate-700 hover:text-slate-900 border-b border-slate-100">
                            <div class="w-6 h-6 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0 mt-0.5 font-bold text-[11px]">
                                4
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-[11.5px] flex items-center gap-1.5">
                                    Versi Lengkap
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-red-100 text-[#8F0A0D] font-semibold">Full</span>
                                </div>
                                <div class="text-[10.5px] text-slate-500">Semua: Kurva S, WBS &amp; Expenses</div>
                            </div>
                        </a>

                        {{-- Opsi Kustomisasi --}}
                        <button type="button" 
                                @click="openExportTab4 = false; showExportCustomModal = true"
                                class="w-full flex items-center justify-between px-3 py-2 hover:bg-slate-50 transition text-slate-600 hover:text-slate-900 text-left cursor-pointer">
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                <span class="text-[11px] font-semibold">Pilih Bagian (Kustom)...</span>
                            </div>
                            <span class="text-[10px] text-slate-400">&rarr;</span>
                        </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Layout Dibagi 2 Vertikal: Atas Tabel Full Width & Bawah Kurva S Full Width --}}
                    {{-- 1. BAGIAN ATAS: Tabel S-Curve Full Width (Tidak Terpotong / Sliced) --}}
                    <div class="space-y-3">
                        <div class="overflow-x-auto overflow-y-auto max-h-[500px] rounded-xl border border-slate-200 relative shadow-2xs">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="sticky top-0 z-10 bg-[#F8FAFC] shadow-xs">
                                    <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC] text-[11px] font-bold text-[#64748B] uppercase tracking-wider">
                                        <th class="py-3 px-3 text-center whitespace-nowrap bg-[#F8FAFC]">Week</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap bg-[#F8FAFC]">Plan Weekly (%)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap bg-[#F8FAFC]">Plan Cumulative (%)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap bg-[#F8FAFC]">Actual Weekly (%)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap bg-[#F8FAFC]">Actual Cumulative (%)</th>
                                        <th class="py-3 px-3 text-right whitespace-nowrap bg-[#F8FAFC]">Actual Cumulative (Plot %)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 bg-white">
                                    @foreach($weeklyRecords as $rec)
                                        <tr class="hover:bg-slate-50/80 transition {{ $rec['is_current'] ? 'bg-amber-50/40' : '' }}">
                                            <td class="py-2.5 px-3 text-center font-bold text-slate-800">
                                                {{ $rec['week'] }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-medium text-slate-700">
                                                {{ number_format($rec['plan_weekly'], 2) }}%
                                            </td>
                                            <td class="py-2.5 px-3 text-right font-bold text-blue-700">
                                                {{ number_format($rec['plan_percent'], 2) }}%
                                            </td>
                                            <td id="scurve-actual-weekly-w{{ $rec['week'] }}" class="py-2.5 px-3 text-right font-medium text-slate-800">
                                                {{ $rec['actual_weekly'] !== null ? number_format($rec['actual_weekly'], 2) . '%' : '-' }}
                                            </td>
                                            <td id="scurve-actual-cum-w{{ $rec['week'] }}" class="py-2.5 px-3 text-right font-bold text-slate-800">
                                                {{ $rec['actual_percent'] !== null ? number_format($rec['actual_percent'], 2) . '%' : '-' }}
                                            </td>
                                            <td id="scurve-helper-w{{ $rec['week'] }}" class="py-2.5 px-3 text-right text-xs {{ $rec['chart_helper'] === '#N/A' ? 'text-slate-400 italic' : 'font-bold text-slate-800' }}">
                                                {{ $rec['chart_helper'] !== '#N/A' ? number_format($rec['chart_helper'], 2) . '%' : '#N/A' }}
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
                            <p>• Column G (Actual Cumulative Plot) returns NA() on purpose so the chart skips unreported weeks instead of drawing 0%. Hari ke-0 = baseline (0%), minggu pelaksanaan berjalan mulai Minggu 1.</p>
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
                                <span class="font-semibold text-slate-700">Actual Cumulative (%)</span>
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
                            <div class="flex items-center gap-2">
                                <div class="relative inline-block" x-data="{ openExportTab5: false }">
                                    <button type="button" 
                                            @click="openExportTab5 = !openExportTab5" 
                                            @click.outside="openExportTab5 = false"
                                            title="Export Laporan Pengeluaran & S-Curve (PDF)"
                                            class="px-3 py-1.5 rounded-xl bg-white hover:bg-red-50 text-slate-700 hover:text-[#8F0A0D] border border-slate-200 hover:border-red-200 text-xs font-bold shadow-2xs transition inline-flex items-center gap-1.5 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>Export PDF</span>
                                        <svg class="w-3 h-3 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': openExportTab5 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>

                                    <div x-show="openExportTab5" 
                                         x-cloak 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 top-full mt-1.5 w-68 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 z-50 text-xs">
                                                                <div class="px-3 py-2 border-b border-slate-100 bg-slate-50/70 rounded-t-xl flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Kategori Laporan PDF</span>
                            <span class="text-[10px] font-semibold text-slate-400">4 Pilihan</span>
                        </div>

                        {{-- 1. Weekly Progress --}}
                        <a href="{{ route('pmo.projects.export_pdf', ['project' => $project->id, 'type' => 'weekly']) }}&_t={{ time() }}" 
                           target="_blank" 
                           @click="openExportTab5 = false"
                           class="flex items-start gap-2.5 px-3 py-2 hover:bg-slate-50 transition text-slate-700 hover:text-slate-900 border-b border-slate-100">
                            <div class="w-6 h-6 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[11px]">
                                1
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-[11.5px] flex items-center gap-1.5">
                                    Weekly Progress
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 font-semibold">WBS</span>
                                </div>
                                <div class="text-[10.5px] text-slate-500">Jadwal kerja &amp; deliverable mingguan</div>
                            </div>
                        </a>

                        {{-- 2. Curva S --}}
                        <a href="{{ route('pmo.projects.export_pdf', ['project' => $project->id, 'type' => 'scurve']) }}&_t={{ time() }}" 
                           target="_blank" 
                           @click="openExportTab5 = false"
                           class="flex items-start gap-2.5 px-3 py-2 hover:bg-slate-50 transition text-slate-700 hover:text-slate-900 border-b border-slate-100">
                            <div class="w-6 h-6 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[11px]">
                                2
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-[11.5px] flex items-center gap-1.5">
                                    Curva S
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 font-semibold">Grafik</span>
                                </div>
                                <div class="text-[10.5px] text-slate-500">Grafik Kurva S &amp; matriks kumulatif</div>
                            </div>
                        </a>

                        {{-- 3. Expenses --}}
                        <a href="{{ route('pmo.projects.export_pdf', ['project' => $project->id, 'type' => 'expenses']) }}&_t={{ time() }}" 
                           target="_blank" 
                           @click="openExportTab5 = false"
                           class="flex items-start gap-2.5 px-3 py-2 hover:bg-slate-50 transition text-slate-700 hover:text-slate-900 border-b border-slate-100">
                            <div class="w-6 h-6 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 mt-0.5 font-bold text-[11px]">
                                3
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-[11.5px] flex items-center gap-1.5">
                                    Expenses
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-semibold">Biaya</span>
                                </div>
                                <div class="text-[10.5px] text-slate-500">Rekap budget &amp; realisasi pengeluaran</div>
                            </div>
                        </a>

                        {{-- 4. Versi Lengkap --}}
                        <a href="{{ route('pmo.projects.export_pdf', ['project' => $project->id, 'type' => 'full']) }}&_t={{ time() }}" 
                           target="_blank" 
                           @click="openExportTab5 = false"
                           class="flex items-start gap-2.5 px-3 py-2 hover:bg-slate-50 transition text-slate-700 hover:text-slate-900 border-b border-slate-100">
                            <div class="w-6 h-6 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0 mt-0.5 font-bold text-[11px]">
                                4
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-[11.5px] flex items-center gap-1.5">
                                    Versi Lengkap
                                    <span class="text-[9px] px-1.5 py-0.2 rounded bg-red-100 text-[#8F0A0D] font-semibold">Full</span>
                                </div>
                                <div class="text-[10.5px] text-slate-500">Semua: Kurva S, WBS &amp; Expenses</div>
                            </div>
                        </a>

                        {{-- Opsi Kustomisasi --}}
                        <button type="button" 
                                @click="openExportTab5 = false; showExportCustomModal = true"
                                class="w-full flex items-center justify-between px-3 py-2 hover:bg-slate-50 transition text-slate-600 hover:text-slate-900 text-left cursor-pointer">
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                <span class="text-[11px] font-semibold">Pilih Bagian (Kustom)...</span>
                            </div>
                            <span class="text-[10px] text-slate-400">&rarr;</span>
                        </button>
                                    </div>
                                </div>
                                <span class="text-xs font-bold text-slate-700 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200">
                                    Total: Rp {{ number_format($totalSpend, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <form action="{{ route('pmo.projects.expenses.store', $project->id) }}" 
                              method="POST" 
                              x-data="{
                                  rawAmount: '',
                                  displayAmount: '',
                                  formatNominal(e) {
                                      let val = e.target.value.replace(/\D/g, '');
                                      this.rawAmount = val;
                                      this.displayAmount = val ? parseInt(val, 10).toLocaleString('id-ID') : '';
                                  }
                              }"
                              class="space-y-3.5">
                            @csrf
                            <input type="hidden" name="amount" :value="rawAmount">
                            
                            {{-- Field 1: Category (Input Teks Bebas) --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Pengeluaran <span class="text-[#8F0A0D]">*</span></label>
                                <input type="text" 
                                       name="category" 
                                       required 
                                       placeholder="Ketik kategori pengeluaran (misal: Hardware, Transport, Konsumsi, Vendor, dll.)" 
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white hover:border-slate-300 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition outline-none">
                            </div>

                            {{-- Field 2: Amount (IDR) Otomatis Format Nominal Ribuan/Jutaan --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Biaya (IDR) <span class="text-[#8F0A0D]">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="text" 
                                           x-model="displayAmount"
                                           @input="formatNominal($event)"
                                           placeholder="Contoh: 56.666 atau 1.500.000" 
                                           required 
                                           class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 bg-white hover:border-slate-300 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition outline-none">
                                </div>
                                <span class="text-[10.5px] text-slate-400 mt-1 block">Otomatis terformat titik ribuan Rupiah saat diketik.</span>
                            </div>

                            {{-- Field 3: Description (Tanggal Transaksi Dihapus sesuai Permintaan) --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi / No. Nota (Opsional)</label>
                                <input type="text" 
                                       name="description" 
                                       placeholder="Keterangan pengeluaran, nomor nota, atau bukti..." 
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 bg-white hover:border-slate-300 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition outline-none">
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

                    {{-- Riwayat Pengeluaran (Expense Logs Table) dengan Freeze Header --}}
                    <div class="ipnet-card p-5 sm:p-6 space-y-3">
                        <div class="flex items-center justify-between pb-2.5 border-b border-[#E2E8F0]">
                            <h4 class="text-xs font-bold text-[#1E293B] uppercase tracking-wider">Riwayat Pengeluaran Proyek</h4>
                            <span class="text-xs font-semibold text-slate-400">{{ $expenses->count() }} Transaksi</span>
                        </div>

                        <div class="space-y-2 max-h-[380px] overflow-y-auto pr-1">
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
    <!-- MODAL CUSTOM EXPORT PDF: PILIH MAU DISHOW APA AJA          -->
    <!-- ========================================================== -->
    <div x-show="showExportCustomModal" 
         x-transition.opacity
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full p-5 sm:p-6 space-y-4"
             @click.away="showExportCustomModal = false">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Kustomisasi Export Laporan PDF</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Pilih bagian yang ingin ditampilkan di dokumen PDF</p>
                    </div>
                </div>
                <button type="button" @click="showExportCustomModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-3 py-1">
                <p class="text-xs text-slate-500">Centang bagian laporan yang ingin di-show di dalam PDF:</p>

                {{-- Opsi 1: Weekly Progress --}}
                <label class="flex items-start gap-3 p-3 rounded-xl border transition cursor-pointer"
                       :class="customShowWeekly ? 'border-emerald-300 bg-emerald-50/40' : 'border-slate-200 hover:bg-slate-50'">
                    <input type="checkbox" x-model="customShowWeekly" class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                    <div>
                        <div class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            1. Weekly Progress
                            <span class="text-[9.5px] px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 font-semibold">WBS Schedule</span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Tabel jadwal kerja, durasi, bobot deliverable, dan pencapaian mingguan WBS per task.</div>
                    </div>
                </label>

                {{-- Opsi 2: Curva S --}}
                <label class="flex items-start gap-3 p-3 rounded-xl border transition cursor-pointer"
                       :class="customShowScurve ? 'border-blue-300 bg-blue-50/40' : 'border-slate-200 hover:bg-slate-50'">
                    <input type="checkbox" x-model="customShowScurve" class="mt-0.5 rounded text-blue-600 focus:ring-blue-500 w-4 h-4">
                    <div>
                        <div class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            2. Curva S
                            <span class="text-[9.5px] px-1.5 py-0.2 rounded bg-blue-100 text-blue-800 font-semibold">Grafik &amp; Kumulatif</span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Visual grafik Kurva S (Plan vs Actual), metrik EV, dan tabel matriks kumulatif mingguan.</div>
                    </div>
                </label>

                {{-- Opsi 3: Expenses --}}
                <label class="flex items-start gap-3 p-3 rounded-xl border transition cursor-pointer"
                       :class="customShowExpenses ? 'border-amber-300 bg-amber-50/40' : 'border-slate-200 hover:bg-slate-50'">
                    <input type="checkbox" x-model="customShowExpenses" class="mt-0.5 rounded text-amber-600 focus:ring-amber-500 w-4 h-4">
                    <div>
                        <div class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            3. Expenses
                            <span class="text-[9.5px] px-1.5 py-0.2 rounded bg-amber-100 text-amber-800 font-semibold">Biaya Operasional</span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">Rekapitulasi anggaran nilai kontrak, sisa budget, dan daftar rincian pengeluaran biaya proyek.</div>
                    </div>
                </label>

                {{-- Pengaturan Nomor Edisi Laporan (Urutan Export) --}}
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 mt-2">
                    <label class="block text-xs font-bold text-slate-800 mb-1">
                        Nomor Edisi Laporan PDF:
                    </label>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-mono font-semibold text-slate-500">IPNET/PMO-RPT/</span>
                        <input type="number" 
                               min="1" 
                               x-model="customReportNo" 
                               class="w-20 px-2.5 py-1 text-xs font-bold border border-slate-300 rounded-lg text-center font-mono focus:border-[#8F0A0D] outline-none bg-white">
                        <span class="text-xs font-mono font-semibold text-slate-500">/XI/{{ now()->format('y') }}</span>
                    </div>
                    <p class="text-[10.5px] text-slate-400 mt-1">Otomatis bertambah (1, 2, 3...) setiap kali dokumen PDF di-export.</p>
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <button type="button" 
                        @click="customShowWeekly = true; customShowScurve = true; customShowExpenses = true" 
                        class="text-[11px] font-semibold text-slate-500 hover:text-slate-800 cursor-pointer">
                    Reset (Pilih Semua)
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" @click="showExportCustomModal = false" class="px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition cursor-pointer">
                        Batal
                    </button>
                    <a :href="getCustomExportUrl()" 
                       target="_blank" 
                       @click="showExportCustomModal = false"
                       class="px-4 py-2 text-xs font-bold rounded-xl bg-[#8F0A0D] hover:bg-[#72080a] text-white shadow-xs transition cursor-pointer inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export PDF
                    </a>
                </div>
            </div>
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
    <!-- MODAL PERIODE TANGGAL & SPESIFIKASI TIMELINE PROYEK        -->
    <!-- ========================================================== -->
    <div x-show="showTimelineModal" 
         x-transition.opacity
         style="display: none;"
         @click.self="showTimelineModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-xl w-full p-5 sm:p-6 space-y-4 my-auto max-h-[92vh] overflow-y-auto"
             x-data="{
                // Periode Kontrak SPK / Legal (Atas)
                mStartDate: '{{ $startDate ? $startDate->format('Y-m-d') : ($project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : now()->format('Y-m-d')) }}',
                mDeadline: '{{ $deadlineDate ? $deadlineDate->format('Y-m-d') : ($project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') : now()->addDays(30)->format('Y-m-d')) }}',

                // Jadwal Pelaksanaan Implementasi WBS (Bawah - Terpisah Mandiri)
                mExecStart: '{{ $wbsStartDate ? $wbsStartDate->format('Y-m-d') : ($startDate ? $startDate->format('Y-m-d') : now()->format('Y-m-d')) }}',
                mExecEnd: '{{ $wbsEndDate ? $wbsEndDate->format('Y-m-d') : ($deadlineDate ? $deadlineDate->format('Y-m-d') : now()->addDays(30)->format('Y-m-d')) }}',
                mDuration: {{ (int)($totalProjectDays ?? 30) }},
                mWeeks: {{ (int)($totalProjectWeeks ?? 5) }},

                calcExecFromDates() {
                    if (!this.mExecStart || !this.mExecEnd) return;
                    const s = new Date(this.mExecStart);
                    const d = new Date(this.mExecEnd);
                    const diffTime = d - s;
                    if (diffTime >= 0) {
                        const days = Math.round(diffTime / (1000 * 60 * 60 * 24)) + 1;
                        this.mDuration = days;
                        this.mWeeks = Math.max(1, Math.ceil(days / 7));
                    }
                },
                calcExecFromDuration() {
                    if (!this.mExecStart || !this.mDuration) return;
                    const s = new Date(this.mExecStart);
                    s.setDate(s.getDate() + parseInt(this.mDuration, 10) - 1);
                    this.mExecEnd = s.toISOString().split('T')[0];
                    this.mWeeks = Math.max(1, Math.ceil(this.mDuration / 7));
                },
                syncExecEndToContract() {
                    this.mExecEnd = this.mDeadline;
                    this.calcExecFromDates();
                }
             }">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Ubah Spesifikasi &amp; Timeline Proyek</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Sesuaikan rentang tanggal kontrak, jadwal pelaksanaan implementasi, durasi, PIC, dan nomor SPK</p>
                    </div>
                </div>
                <button type="button" @click="showTimelineModal = false" class="text-slate-400 hover:text-slate-600 cursor-pointer p-1 rounded-lg hover:bg-slate-100 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('pmo.projects.update_timeline', $project->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="space-y-4">
                    {{-- Bagian 1: Periode Kontrak / SPK Legal (Atas) --}}
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                                1. Periode Tanggal Kontrak / SPK (Legal)
                            </span>
                            <span class="text-[10px] text-slate-400 font-medium">Dasar Kontrak PO/SPK</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Mulai Kontrak (Start Date SPK) <span class="text-[#8F0A0D]">*</span></label>
                                <input type="date" 
                                       name="start_date" 
                                       x-model="mStartDate"
                                       required
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Batas Akhir / Deadline SPK <span class="text-[#8F0A0D]">*</span></label>
                                <input type="date" 
                                       name="deadline" 
                                       x-model="mDeadline"
                                       required
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none cursor-pointer">
                            </div>
                        </div>
                    </div>

                    {{-- Bagian 2: Rencana Pelaksanaan Implementasi (WBS & Kurva S - Bawah, Terpisah Bebas) --}}
                    <div class="p-3.5 bg-blue-50/40 rounded-xl border border-blue-100 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-950 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                2. Jadwal Pelaksanaan Implementasi (WBS)
                            </span>
                            <button type="button" 
                                    @click="syncExecEndToContract()"
                                    title="Samakan tanggal akhir pelaksanaan dengan batas kontrak SPK"
                                    class="text-[10.5px] font-bold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer">
                                Samakan Akhir dgn Batas SPK
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Mulai Pelaksanaan WBS</label>
                                <input type="date" 
                                       name="execution_start_date" 
                                       x-model="mExecStart"
                                       @change="calcExecFromDates()"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-blue-600 outline-none cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Target Selesai WBS</label>
                                <input type="date" 
                                       name="execution_end_date" 
                                       x-model="mExecEnd"
                                       @change="calcExecFromDates()"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-blue-600 outline-none cursor-pointer">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-[11px] font-semibold text-slate-700">Durasi Pelaksanaan (Hari)</label>
                                    @if(($maxTaskFinishDay ?? 0) > 0)
                                        <button type="button" 
                                                @click="mDuration = {{ $maxTaskFinishDay }}; mWeeks = {{ max(1, (int)ceil($maxTaskFinishDay / 7)) }}; calcExecFromDuration();" 
                                                class="text-[10px] font-bold text-blue-600 hover:text-blue-800 hover:underline cursor-pointer">
                                            Ikuti WBS ({{ $maxTaskFinishDay }} Hari)
                                        </button>
                                    @endif
                                </div>
                                <input type="number" 
                                       id="input_estimate_duration"
                                       name="estimate_project_duration" 
                                       x-model.number="mDuration"
                                       @input="calcExecFromDuration()"
                                       min="1" 
                                       max="365"
                                       onwheel="this.blur()"
                                       placeholder="Contoh: 30"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-blue-600 outline-none">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Jumlah Minggu (Weeks)</label>
                                <input type="number" 
                                       id="input_execution_weeks"
                                       name="execution_weeks" 
                                       x-model.number="mWeeks"
                                       min="1" 
                                       max="52"
                                       onwheel="this.blur()"
                                       placeholder="Contoh: 5"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-blue-600 outline-none">
                            </div>
                        </div>
                    </div>

                    {{-- Bagian 3: Customer PIC & Nomor Kontrak/SPK/PO --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Customer's PIC</label>
                            <input type="text" 
                                   name="customer_pic" 
                                   value="{{ $customerPic !== '-' ? $customerPic : ($project->customer_pic_technical ?: '') }}" 
                                   placeholder="Nama PIC Klien (contoh: Richi Michael)"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Kontrak / SPK / PO</label>
                            <input type="text" 
                                   name="contract_spk_number" 
                                   value="{{ $contractSpkNumber !== '-' ? $contractSpkNumber : ($project->po_spk_number ?: '') }}" 
                                   placeholder="Contoh: EP-01KYREAFEJH89..."
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none">
                        </div>
                    </div>

                    {{-- Bagian 4: Metodologi Penjadwalan Proyek --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Metodologi Penjadwalan Proyek</label>
                        <select name="schedule_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none cursor-pointer">
                            <option value="waterfall" {{ $scheduleType === 'waterfall' ? 'selected' : '' }}>Waterfall (Sequential, Finish-to-Start)</option>
                            <option value="hybrid" {{ $scheduleType === 'hybrid' ? 'selected' : '' }}>Hybrid (Concurrent / Overlapping Tasks)</option>
                        </select>
                        <span class="text-[10.5px] text-slate-400 mt-1 block">Mode Hybrid memungkinkan task berjalan paralel berdasarkan Planned Start.</span>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showTimelineModal = false" class="px-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold rounded-xl btn-ipnet-primary transition shadow-xs cursor-pointer">
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
                        <option value="Stagging">Stagging</option>
                        <option value="Instalasi dan Konfigurasi">Instalasi dan Konfigurasi</option>
                        <option value="Integrasi">Integrasi</option>
                        <option value="Migrasi">Migrasi</option>
                        <option value="Testing dan Komisioning">Testing dan Komisioning</option>
                        <option value="User Acceptance Test">User Acceptance Test</option>
                        <option value="Monitoring">Monitoring</option>
                        <option value="Dokumentasi">Dokumentasi</option>
                        <option value="Sharing Knowledge">Sharing Knowledge</option>
                        <option value="BAST">BAST</option>
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

                {{-- Row 2: Starting Day, Durasi, Finish Day & Week (4 Kolom Manual Bebas Atur) --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <span x-show="scheduleType === 'hybrid'">Planned Start</span>
                            <span x-show="scheduleType !== 'hybrid'">Starting Day</span>
                        </label>
                        <input type="number" 
                               name="starting_day" 
                               x-model.number="newTaskStartingDay"
                               @input="newTaskPlannedStart = newTaskStartingDay; if(newTaskDuration > 0){ newTaskFinishDay = newTaskStartingDay + newTaskDuration; newTaskWeek = Math.max(1, Math.ceil(newTaskFinishDay / 7)); }"
                               min="0" 
                               max="365" 
                               onwheel="this.blur()"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#8F0A0D] outline-none">
                        <input type="hidden" name="planned_start" :value="newTaskStartingDay">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Durasi (Hari)</label>
                        <input type="number" 
                               name="duration_days" 
                               x-model.number="newTaskDuration"
                               @input="newTaskFinishDay = newTaskStartingDay + newTaskDuration; newTaskWeek = Math.max(1, Math.ceil(newTaskFinishDay / 7));"
                               min="1" 
                               max="365" 
                               onwheel="this.blur()"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#8F0A0D] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Finish Day</label>
                        <input type="number" 
                               name="finish_day" 
                               x-model.number="newTaskFinishDay"
                               @input="if(newTaskFinishDay > newTaskStartingDay) { newTaskDuration = newTaskFinishDay - newTaskStartingDay; newTaskWeek = Math.max(1, Math.ceil(newTaskFinishDay / 7)); }"
                               min="1" 
                               max="365" 
                               onwheel="this.blur()"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#8F0A0D] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Week (Minggu)</label>
                        <input type="number" 
                               name="week" 
                               x-model.number="newTaskWeek"
                               min="1" 
                               max="52" 
                               onwheel="this.blur()"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-blue-700 focus:border-[#8F0A0D] outline-none">
                    </div>
                </div>

                {{-- Row 3: Bobot (%) & Daily Weight (%) --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Bobot (%)</label>
                        <input type="number" 
                               name="bobot" 
                               x-model="newTaskBobot"
                               step="any" 
                               min="0" 
                               max="100" 
                               onwheel="this.blur()"
                               placeholder="Contoh: 10.0 atau 3.05"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-blue-700 focus:border-[#8F0A0D] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Daily Weight (%)</label>
                        <input type="number" 
                               name="daily_weight" 
                               x-model="newTaskDailyWeight"
                               @input="manualNewDailyWeight = true"
                               step="any" 
                               min="0" 
                               max="100" 
                               onwheel="this.blur()"
                               placeholder="Contoh: 3.33 atau 1.25"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#8F0A0D] outline-none">
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

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            <span x-show="scheduleType === 'hybrid'">Planned Start</span>
                            <span x-show="scheduleType !== 'hybrid'">Starting Day</span>
                        </label>
                        <input type="number" 
                               name="starting_day" 
                               x-model.number="editTaskStartingDay" 
                               @input="editTaskPlannedStart = editTaskStartingDay; if(editTaskDuration > 0){ editTaskFinishDay = editTaskStartingDay + editTaskDuration; editTaskWeek = Math.max(1, Math.ceil(editTaskFinishDay / 7)); }"
                               min="0" 
                               max="365" 
                               onwheel="this.blur()"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#8F0A0D] outline-none">
                        <input type="hidden" name="planned_start" :value="editTaskStartingDay">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Durasi (Hari)</label>
                        <input type="number" 
                               name="duration_days" 
                               x-model.number="editTaskDuration" 
                               @input="editTaskFinishDay = editTaskStartingDay + editTaskDuration; editTaskWeek = Math.max(1, Math.ceil(editTaskFinishDay / 7));"
                               min="1" 
                               max="365" 
                               onwheel="this.blur()"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#8F0A0D] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Finish Day</label>
                        <input type="number" 
                               name="finish_day" 
                               x-model.number="editTaskFinishDay" 
                               @input="if(editTaskFinishDay > editTaskStartingDay) { editTaskDuration = editTaskFinishDay - editTaskStartingDay; editTaskWeek = Math.max(1, Math.ceil(editTaskFinishDay / 7)); }"
                               min="1" 
                               max="365" 
                               onwheel="this.blur()"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#8F0A0D] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Week (Minggu)</label>
                        <input type="number" 
                               name="week" 
                               x-model.number="editTaskWeek" 
                               min="1" 
                               max="52" 
                               onwheel="this.blur()"
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-blue-700 focus:border-[#8F0A0D] outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Bobot (%)</label>
                        <input type="number" 
                               name="bobot" 
                               x-model="editTaskBobot" 
                               step="any" 
                               min="0" 
                               max="100" 
                               onwheel="this.blur()"
                               placeholder="Contoh: 10.0"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-blue-700 focus:border-[#8F0A0D] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Daily Weight (%)</label>
                        <input type="number" 
                               name="daily_weight" 
                               x-model="editTaskDailyWeight" 
                               @input="manualEditDailyWeight = true"
                               step="any" 
                               min="0" 
                               max="100" 
                               onwheel="this.blur()"
                               placeholder="Contoh: 3.33"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:border-[#8F0A0D] outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Progres Aktual (%)</label>
                        <input type="number" 
                               name="progress" 
                               x-model="editTaskProgress" 
                               step="any"
                               min="0" 
                               max="100" 
                               onwheel="this.blur()"
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

        {{-- MODAL IMPORT TASK WBS DARI EXCEL SPREADSHEET (ENTERPRISE PROFESSIONAL REDESIGN) --}}
    <div x-show="showImportExcelModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         style="display: none;"
         class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 bg-slate-900/60 backdrop-blur-xs overflow-hidden">
        
        <form action="{{ route('pmo.projects.tasks.import_excel', $project->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              @click.outside="showImportExcelModal = false"
              x-data="{ 
                  fileName: '', 
                  fileSize: '',
                  mode: 'append',
                  showFormatGuide: false,
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
              class="bg-white rounded-2xl shadow-2xl border border-slate-200 max-w-2xl lg:max-w-3xl w-full flex flex-col h-[85vh] max-h-[640px] overflow-hidden select-none">
            @csrf
            
            {{-- Fixed Header (Tidak Ikut Ter-scroll) --}}
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-white shrink-0 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-[#8F0A0D] border border-red-100 flex items-center justify-center shrink-0 shadow-2xs">
                        <svg class="w-5 h-5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-[15px] sm:text-base font-extrabold text-slate-900 tracking-tight">Import Task Kolektif Proyek (WBS Breakdown)</h3>
                        <p class="text-[11.5px] text-slate-500 mt-0.5">Unggah dokumen spreadsheet rencana kerja untuk mengisi seluruh deliverable secara otomatis.</p>
                    </div>
                </div>
                <button type="button" @click="showImportExcelModal = false" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Scrollable Modal Body (Hanya Bagian Ini yang Menggulung) --}}
            <div class="flex-1 min-h-0 overflow-y-auto px-5 sm:px-6 py-4 space-y-3.5 custom-scrollbar text-xs">
                
                {{-- 1. Template Resmi Download Card --}}
                <div class="p-3 sm:p-3.5 bg-gradient-to-r from-emerald-50/80 via-slate-50 to-white rounded-xl border border-emerald-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM6 4h7v5h5v11H6V4zm2 8h2.5l1.5 2.5 1.5-2.5H16l-2.25 3.5L16 19h-2.5L12 16.5 10.5 19H8l2.25-3.5L8 12z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-slate-900 flex items-center gap-2">
                                <span>Template Resmi Excel WBS Proyek</span>
                                <span class="px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Format Standar</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                Gunakan template acuan untuk pemetaan otomatis nama tugas, bobot (%), dan durasi.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('pmo.projects.tasks.template_excel', $project->id) }}" 
                       class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition hover:scale-[1.02] shrink-0 cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        <span>Unduh Template (.xlsx)</span>
                    </a>
                </div>

                {{-- 2. Panduan Format Kolom (Clean & Collapsible) --}}
                <div class="rounded-xl border border-slate-200 overflow-hidden bg-slate-50/50">
                    <button type="button" 
                            @click="showFormatGuide = !showFormatGuide"
                            class="w-full px-3.5 py-2.5 flex items-center justify-between text-left hover:bg-slate-100/60 transition cursor-pointer select-none">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="font-bold text-slate-700 text-[11.5px]">Panduan Susunan Kolom Spreadsheet</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-500">
                            <span x-text="showFormatGuide ? 'Sembunyikan' : 'Lihat Susunan Kolom'"></span>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showFormatGuide ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>
                    </button>
                    
                    <div x-show="showFormatGuide" x-cloak class="p-3 bg-white border-t border-slate-200">
                        <div class="overflow-x-auto rounded-lg border border-slate-200">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-slate-100/80 border-b border-slate-200 text-[10px] font-bold text-slate-500">
                                        <th class="py-1 px-2.5 text-center w-10 border-r border-slate-200">A</th>
                                        <th class="py-1 px-2.5 border-r border-slate-200">B (Wajib)</th>
                                        <th class="py-1 px-2.5 border-r border-slate-200">C</th>
                                        <th class="py-1 px-2.5 border-r border-slate-200 text-center">D</th>
                                        <th class="py-1 px-2.5 border-r border-slate-200 text-right">E</th>
                                        <th class="py-1 px-2.5 text-right">F</th>
                                    </tr>
                                    <tr class="bg-white border-b border-slate-200 text-[11px] font-bold text-slate-800">
                                        <td class="py-1.5 px-2.5 text-center text-slate-400 border-r border-slate-200 font-mono">No</td>
                                        <td class="py-1.5 px-2.5 border-r border-slate-200 text-slate-900">Nama Tugas (WBS) *</td>
                                        <td class="py-1.5 px-2.5 border-r border-slate-200 text-slate-700">Deskripsi Scope</td>
                                        <td class="py-1.5 px-2.5 border-r border-slate-200 text-center text-slate-700">Durasi (Hari)</td>
                                        <td class="py-1.5 px-2.5 border-r border-slate-200 text-right text-blue-700">Bobot (%)</td>
                                        <td class="py-1.5 px-2.5 text-right text-emerald-700">Progress (%)</td>
                                    </tr>
                                </thead>
                                <tbody class="text-[10.5px] text-slate-500">
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
                    </div>
                </div>

                {{-- 3. Periode Pelaksanaan WBS --}}
                <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200 space-y-2.5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5">
                        <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Periode Pelaksanaan Pekerjaan (Task WBS)</span>
                        </label>
                        <span class="text-[10.5px] font-semibold text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200 self-start sm:self-auto">
                            Maks Kontrak: {{ $deadlineDate ? $deadlineDate->format('d M Y') : ($project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : 'Sesuai Kontrak') }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold text-slate-700 block">Tanggal Mulai Pelaksanaan <span class="text-[#8F0A0D]">*</span></span>
                            <input type="text" 
                                   name="execution_start_date" 
                                   value="{{ $startDate ? $startDate->format('Y-m-d') : ($project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : date('Y-m-d')) }}" 
                                   required
                                   placeholder="Pilih Tanggal Mulai"
                                   class="ipnet-datepicker w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none">
                        </div>
                        <div class="space-y-1">
                            <span class="text-[11px] font-bold text-slate-700 block">Target Selesai Pelaksanaan <span class="text-[#8F0A0D]">*</span></span>
                            <input type="text" 
                                   name="execution_end_date" 
                                   value="{{ $deadlineDate ? $deadlineDate->format('Y-m-d') : ($project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') : '') }}" 
                                   max="{{ $deadlineDate ? $deadlineDate->format('Y-m-d') : ($project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') : '') }}"
                                   required
                                   placeholder="Pilih Tanggal Selesai"
                                   class="ipnet-datepicker w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:border-[#8F0A0D] outline-none">
                        </div>
                    </div>
                </div>

                {{-- 4. Upload File Spreadsheet Dropzone --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">
                        Pilih Berkas Spreadsheet <span class="text-rose-600">*</span>
                    </label>
                    
                    <div class="relative border-2 border-dashed rounded-xl p-4 text-center cursor-pointer transition"
                         :class="fileName ? 'border-emerald-500 bg-emerald-50/30' : 'border-slate-300 hover:border-[#8F0A0D] bg-slate-50/60 hover:bg-red-50/20'">
                        <input type="file" 
                               name="excel_file" 
                               required 
                               accept=".xlsx,.xls,.csv" 
                               @change="handleFileSelect($event)" 
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        
                        {{-- State: No file chosen --}}
                        <div x-show="!fileName" class="space-y-1.5 pointer-events-none">
                            <div class="w-9 h-9 mx-auto rounded-full bg-red-50 text-[#8F0A0D] flex items-center justify-center">
                                <svg class="w-5 h-5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0-12l-4 4m4-4l4 4"/>
                                </svg>
                            </div>
                            <div class="text-xs font-bold text-slate-800">Pilih berkas spreadsheet atau seret file ke sini</div>
                            <div class="text-[10.5px] text-slate-400">Format .XLSX, .XLS, atau .CSV (Maksimal 10 MB)</div>
                        </div>

                        {{-- State: File selected --}}
                        <div x-show="fileName" x-cloak class="space-y-1 pointer-events-none">
                            <div class="w-8 h-8 mx-auto rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="text-xs font-extrabold text-emerald-900 truncate max-w-sm mx-auto" x-text="fileName"></div>
                            <div class="text-[11px] text-emerald-700" x-text="'Ukuran: ' + fileSize"></div>
                            <div class="text-[10px] text-slate-400">Klik area untuk mengganti file</div>
                        </div>
                    </div>
                </div>

                {{-- 5. Opsi Tindakan Impor (Append vs Replace) --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">
                        Opsi Pengolahan Data:
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <label class="flex items-start gap-2.5 p-2.5 rounded-xl border cursor-pointer transition select-none"
                               :class="mode === 'append' ? 'border-[#8F0A0D] bg-red-50/40 text-slate-900 shadow-2xs ring-1 ring-[#8F0A0D]/20' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-600'">
                            <input type="radio" name="mode" value="append" x-model="mode" class="mt-0.5 text-[#8F0A0D] focus:ring-[#8F0A0D]">
                            <div class="text-xs leading-snug">
                                <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                    <span>Tambahkan (Append)</span>
                                    <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold bg-emerald-100 text-emerald-800">Default</span>
                                </div>
                                <div class="text-[10.5px] text-slate-500 mt-0.5">Task baru ditambahkan, task lama tetap utuh.</div>
                            </div>
                        </label>

                        <label class="flex items-start gap-2.5 p-2.5 rounded-xl border cursor-pointer transition select-none"
                               :class="mode === 'replace' ? 'border-rose-600 bg-rose-50/40 text-slate-900 shadow-2xs ring-1 ring-rose-600/20' : 'border-slate-200 bg-white hover:bg-slate-50 text-slate-600'">
                            <input type="radio" name="mode" value="replace" x-model="mode" class="mt-0.5 text-rose-600 focus:ring-rose-600">
                            <div class="text-xs leading-snug">
                                <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                    <span>Timpa / Reset (Replace)</span>
                                    <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold bg-rose-100 text-rose-700">Hapus Lama</span>
                                </div>
                                <div class="text-[10.5px] text-slate-500 mt-0.5">Seluruh task lama diganti oleh berkas baru.</div>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

            {{-- Fixed Footer (Tidak Ikut Ter-scroll) --}}
            <div class="px-5 sm:px-6 py-3.5 border-t border-slate-100 bg-slate-50/95 flex items-center justify-between shrink-0 z-10">
                <div class="text-[11px] text-slate-400 hidden sm:block">
                    Format didukung: <strong>.xlsx</strong>, <strong>.xls</strong>, <strong>.csv</strong>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" @click="showImportExcelModal = false" class="px-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-600 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-bold rounded-xl bg-gradient-to-r from-[#8F0A0D] to-[#A80D11] hover:from-[#A80D11] hover:to-[#B91C1C] text-white shadow-xs transition flex items-center gap-2 cursor-pointer active:scale-95">
                        <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0-12l-4 4m4-4l4 4"/>
                        </svg>
                        <span>Unggah &amp; Buat Task Otomatis</span>
                    </button>
                </div>
            </div>
        </form>
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
                            label: 'Actual Cumulative (%)',
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
                                const otherVal = dIdx === 0 ? (chart.data.datasets[1]?.data[pIdx] ?? null) : null;
                                let yOffset = (dIdx === 0 && otherVal !== null && Math.abs(val - otherVal) < 15) ? 16 : -14;
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
            const currentTargetPlan = {{ $planValAtCurrentWeek !== null ? (float)$planValAtCurrentWeek : 'null' }};

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
                let chartActualData = [0.0]; // Week 0 selalu dimulai dari titik nol (0.0%)

                for (let w = 1; w <= totalWeeksCount; w++) {
                    accum = Math.round((accum + weeklyActual[w]) * 10) / 10;
                    const planCum = planCumulativeMap[w] !== undefined ? parseFloat(planCumulativeMap[w]) : 0.0;

                    const elActWeekly = document.getElementById(`scurve-actual-weekly-w${w}`);
                    const elActCum = document.getElementById(`scurve-actual-cum-w${w}`);
                    const elDev = document.getElementById(`scurve-variance-w${w}`);
                    const elHlp = document.getElementById(`scurve-helper-w${w}`);
                    const elTab2Cum = document.getElementById(`tab2-actual-cum-w${w}`);

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
                        if (elTab2Cum) {
                            elTab2Cum.textContent = accum.toFixed(2) + '%';
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
                        if (elTab2Cum) {
                            elTab2Cum.textContent = '-';
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

                const elAct1 = document.getElementById('header-metric-actual-text-1');
                if (elAct1) elAct1.textContent = 'Actual: ' + overallSum.toFixed(1) + '%';

                const elAct2 = document.getElementById('header-metric-actual-text-2');
                if (elAct2) elAct2.textContent = 'Aktual: ' + overallSum.toFixed(1) + '%';

                if (currentTargetPlan !== null) {
                    const curVar = Math.round((overallSum - currentTargetPlan) * 10) / 10;
                    const isTrack = curVar >= -2.0;
                    const isRisk = curVar < -2.0 && curVar >= -10.0;
                    
                    const cardEl = document.getElementById('header-metric-status-card');
                    const iconEl = document.getElementById('header-metric-status-icon-wrap');
                    const dotEl = document.getElementById('header-metric-status-dot');
                    const labelEl = document.getElementById('header-metric-status-label');
                    const varEl = document.getElementById('header-metric-status-variance');

                    if (labelEl) {
                        labelEl.textContent = isTrack ? 'On-Track' : (isRisk ? 'At-Risk' : 'Delayed');
                    }
                    if (varEl) {
                        varEl.textContent = '(' + (curVar >= 0 ? '+' : '') + curVar.toFixed(1) + '%)';
                        varEl.className = 'text-xs font-bold ' + (isTrack ? 'text-emerald-700' : (isRisk ? 'text-amber-700' : 'text-rose-700'));
                    }
                    if (dotEl) {
                        dotEl.className = 'w-2.5 h-2.5 rounded-full ' + (isTrack ? 'bg-emerald-500' : (isRisk ? 'bg-amber-500' : 'bg-rose-500'));
                    }
                    if (iconEl) {
                        iconEl.className = 'w-7 h-7 rounded-lg ' + (isTrack ? 'bg-emerald-50 text-emerald-600' : (isRisk ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600')) + ' flex items-center justify-center';
                    }
                    if (cardEl) {
                        cardEl.className = 'ipnet-metric-card border-l-4 ' + (isTrack ? 'border-l-emerald-600' : (isRisk ? 'border-l-amber-500' : 'border-l-rose-600')) + ' flex flex-col justify-between';
                    }
                }
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
