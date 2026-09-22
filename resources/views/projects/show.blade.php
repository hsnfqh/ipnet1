@extends('layouts.app')

@section('title', $project->name . ' - Detail Proyek')

@push('styles')
<style>
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }
    .btn-action-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        transition: all 0.15s ease;
    }
    .btn-ipnet-gradient {
        background: linear-gradient(135deg, #EF4444 0%, #B81525 55%, #8F0A0D 100%);
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(184, 21, 37, 0.35);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-ipnet-gradient:hover {
        background: linear-gradient(135deg, #DC2626 0%, #991B1B 55%, #7F1D1D 100%);
        box-shadow: 0 4px 12px rgba(184, 21, 37, 0.45);
        transform: translateY(-1px);
    }
    .stage-tab-btn {
        position: relative;
        transition: all 0.2s ease;
    }
    .stage-tab-btn.active {
        background-color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        border-color: #E2E8F0;
    }
    .bullet-glow-draft {
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
    }
    .bullet-glow-opportunity {
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
    }
    .bullet-glow-in-progress {
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }
    .bullet-glow-pending {
        box-shadow: 0 0 10px rgba(249, 115, 22, 0.5);
    }
    .bullet-glow-completed {
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.6);
    }
</style>
@endpush

@section('content')
@php
    $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
    $draftApprovals = $handoverData['draft_approvals'] ?? [];
    
    $headApproval = $draftApprovals['head'] ?? [
        'approved' => false,
        'by' => 'Susanto Djaya',
        'date' => null,
        'notes' => 'Menunggu review kelayakan teknis dan alokasi resource engineer.'
    ];
    $directorApproval = $draftApprovals['director'] ?? [
        'approved' => false,
        'by' => 'Hariyadi',
        'date' => null,
        'notes' => 'Menunggu otorisasi finansial dan persetujuan eksekusi kontrak.'
    ];

    // Koleksi seluruh engineer yang ditugaskan pada proyek (dari tasks.engineer & tasks.engineers)
    $allEngineers = collect();
    foreach($project->tasks as $t) {
        if ($t->engineer) {
            $allEngineers->push($t->engineer);
        }
        if ($t->relationLoaded('engineers') && $t->engineers) {
            foreach($t->engineers as $eng) {
                $allEngineers->push($eng);
            }
        }
    }
    $allEngineers = $allEngineers->unique('id');

    // Default stage selector
    $currentStatus = $project->status ?: 'Draft';
    $statusLower = strtolower($currentStatus);
    if (str_contains($statusLower, 'draft')) {
        $initialStage = 'draft';
    } elseif (str_contains($statusLower, 'opp') || str_contains($statusLower, 'qualif') || str_contains($statusLower, 'propos')) {
        $initialStage = 'opportunity';
    } elseif (str_contains($statusLower, 'progress') || str_contains($statusLower, 'deliver') || str_contains($statusLower, 'plan') || str_contains($statusLower, 'active')) {
        $initialStage = 'in_progress';
    } elseif (str_contains($statusLower, 'pend') || str_contains($statusLower, 'hold')) {
        $initialStage = 'pending';
    } elseif (str_contains($statusLower, 'complete') || str_contains($statusLower, 'won') || str_contains($statusLower, 'done')) {
        $initialStage = 'completed';
    } else {
        $initialStage = 'draft';
    }
@endphp

<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" 
     x-data="projectDetailPage('{{ $initialStage }}', '{{ $currentStatus }}')">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Project'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2 cursor-pointer">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold px-2 cursor-pointer">✕</button>
                </div>
            @endif

            {{-- 1. BREADCRUMBS --}}
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
                <a href="{{ route('sales.pipeline.index') }}" class="hover:text-gray-800 transition">Project</a>
                <span>&gt;</span>
                <span class="text-gray-900 font-bold">{{ $project->name }}</span>
            </div>

            {{-- 2. TOP STATUS BAR --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
                <div class="flex items-center gap-3 flex-wrap">
                    {{-- Status Badge --}}
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white shadow-xs
                        @if(in_array($currentStatus, ['Draft', 'Planning'])) bg-gradient-to-r from-[#A2623D] to-[#864421]
                        @elseif($currentStatus === 'Opportunity') bg-gradient-to-r from-blue-600 to-indigo-700
                        @elseif(in_array($currentStatus, ['In Progress', 'On Progress'])) bg-gradient-to-r from-teal-600 to-emerald-700
                        @elseif($currentStatus === 'Pending') bg-gradient-to-r from-amber-500 to-orange-600
                        @elseif($currentStatus === 'Completed') bg-gradient-to-r from-emerald-600 to-teal-800
                        @else bg-gray-700 @endif">
                        <span>{{ $currentStatus }}</span>
                        <svg class="w-3 h-3 text-white/90" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>

                    {{-- Team Badge + Assign Button --}}
                    <div class="flex items-center gap-2 bg-white px-2.5 py-1 rounded-lg border border-gray-200 shadow-2xs">
                        <span class="text-[10.5px] font-bold text-gray-400">Team</span>
                        <span class="text-xs font-bold text-gray-800">
                            {{ $project->division ? $project->division->name : 'IPNET 01' }}
                        </span>
                        <button type="button" 
                                @click="openAssignModal('engineer')" 
                                class="w-6 h-6 rounded-full border border-red-300 text-red-600 flex items-center justify-center font-bold text-xs hover:bg-red-50 hover:border-red-400 transition cursor-pointer" 
                                title="Tugaskan Tim / Engineer">
                            +
                        </button>
                    </div>

                    {{-- PM Badge indicator --}}
                    @if($project->pm)
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold">
                            <span class="text-[10px] uppercase text-indigo-400">PM:</span>
                            <span>{{ $project->pm->name }}</span>
                        </div>
                    @else
                        <button type="button" @click="openAssignModal('pm')" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold hover:bg-amber-100 transition cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>+ Tunjuk PM</span>
                        </button>
                    @endif
                </div>

                <div class="text-xs text-gray-500 font-medium">
                    Created by <span class="font-bold text-gray-800">{{ $project->creator ? $project->creator->name : ($project->sales_name ?: 'Nabylla Berlianita') }}</span>, 
                    {{ \Carbon\Carbon::parse($project->created_at)->format('d M Y H:i') }}
                </div>
            </div>

            {{-- 3. TITLE & ACTIONS ROW --}}
            <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $project->name }}</h1>
                    <p class="text-xs text-gray-500 mt-1">{{ $project->description ?: 'Proyek pengadaan & instalasi perangkat jaringan dan infrastruktur IPNET.' }}</p>
                </div>

                {{-- Quick Actions: Pending & Delete --}}
                <div class="flex items-center gap-2.5 shrink-0">
                    <form action="{{ route('projects.stage_update', $project->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="{{ $currentStatus === 'Pending' ? 'In Progress' : 'Pending' }}">
                        <button type="submit" class="btn-action-pill {{ $currentStatus === 'Pending' ? 'bg-amber-100 text-amber-800 border-amber-300 hover:bg-amber-200' : 'text-amber-700 hover:bg-amber-50 border border-amber-200' }} cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ $currentStatus === 'Pending' ? 'Resume Proyek' : 'Pending' }}</span>
                        </button>
                    </form>

                    <button type="button" @click="confirmDeleteProject()" class="btn-action-pill text-red-600 hover:bg-red-50 border border-red-200 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Delete</span>
                    </button>
                </div>
            </div>

            {{-- 4. INTERACTIVE LIFECYCLE STAGE STEPPER / TABS (Draft, Opportunity, In Progress, Pending, Completed) --}}
            <div class="bg-white rounded-2xl p-2 sm:p-3 border border-gray-200/80 shadow-xs">
                <div class="flex items-center justify-between px-2 pb-2 mb-2 border-b border-gray-100 flex-wrap gap-2">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-900 uppercase tracking-wider">Tahapan Siklus Proyek (Lifecycle Stages)</span>
                        <span class="text-[11px] font-semibold text-gray-400">Klik tab untuk melihat & mengelola detail fase:</span>
                    </div>

                    {{-- Form untuk Sinkronkan Status Resmi Proyek ke Tab yang Dipilih --}}
                    <form action="{{ route('projects.stage_update', $project->id) }}" method="POST" class="inline-flex items-center gap-2">
                        @csrf
                        <input type="hidden" name="status" :value="stageNameToDbStatus(activeStageTab)">
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold text-white btn-ipnet-gradient cursor-pointer"
                                :title="'Ubah status resmi proyek ke ' + stageNameToDbStatus(activeStageTab)">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Set Status Resmi: <strong x-text="stageNameToDbStatus(activeStageTab)"></strong></span>
                        </button>
                    </form>
                </div>

                {{-- 5 Stage Tabs Nav --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
                    
                    {{-- 1. DRAFT TAB --}}
                    <button type="button" 
                            @click="activeStageTab = 'draft'"
                            :class="activeStageTab === 'draft' ? 'active ring-2 ring-amber-500/20 bg-amber-50/60 border-amber-300' : 'bg-gray-50/70 border-gray-200 hover:bg-gray-100/70'"
                            class="stage-tab-btn p-3 rounded-xl border text-left flex flex-col justify-between gap-2 cursor-pointer transition">
                        <div class="flex items-center justify-between">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs"
                                 :class="activeStageTab === 'draft' ? 'bg-amber-500 text-white shadow-xs' : 'bg-gray-200 text-gray-600'">
                                1
                            </div>
                            <span class="w-2.5 h-2.5 rounded-full"
                                  :class="activeStageTab === 'draft' ? 'bg-amber-500 bullet-glow-draft' : 'bg-gray-300'"></span>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-gray-900 flex items-center gap-1">
                                <span>Draft</span>
                                @if(in_array($currentStatus, ['Draft', 'Planning']))
                                    <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold bg-amber-200 text-amber-900">Aktif</span>
                                @endif
                            </div>
                            <div class="text-[10.5px] text-gray-500 font-medium line-clamp-1">Dual Sign-Off (Head & Dir)</div>
                        </div>
                    </button>

                    {{-- 2. OPPORTUNITY TAB --}}
                    <button type="button" 
                            @click="activeStageTab = 'opportunity'"
                            :class="activeStageTab === 'opportunity' ? 'active ring-2 ring-blue-500/20 bg-blue-50/60 border-blue-300' : 'bg-gray-50/70 border-gray-200 hover:bg-gray-100/70'"
                            class="stage-tab-btn p-3 rounded-xl border text-left flex flex-col justify-between gap-2 cursor-pointer transition">
                        <div class="flex items-center justify-between">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs"
                                 :class="activeStageTab === 'opportunity' ? 'bg-blue-600 text-white shadow-xs' : 'bg-gray-200 text-gray-600'">
                                2
                            </div>
                            <span class="w-2.5 h-2.5 rounded-full"
                                  :class="activeStageTab === 'opportunity' ? 'bg-blue-600 bullet-glow-opportunity' : 'bg-gray-300'"></span>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-gray-900 flex items-center gap-1">
                                <span>Opportunity</span>
                                @if($currentStatus === 'Opportunity')
                                    <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold bg-blue-200 text-blue-900">Aktif</span>
                                @endif
                            </div>
                            <div class="text-[10.5px] text-gray-500 font-medium line-clamp-1">Sales CRM & Pipeline</div>
                        </div>
                    </button>

                    {{-- 3. IN PROGRESS TAB (Delivery & Team Execution) --}}
                    <button type="button" 
                            @click="activeStageTab = 'in_progress'"
                            :class="activeStageTab === 'in_progress' ? 'active ring-2 ring-emerald-500/20 bg-emerald-50/60 border-emerald-300' : 'bg-gray-50/70 border-gray-200 hover:bg-gray-100/70'"
                            class="stage-tab-btn p-3 rounded-xl border text-left flex flex-col justify-between gap-2 cursor-pointer transition">
                        <div class="flex items-center justify-between">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs"
                                 :class="activeStageTab === 'in_progress' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-gray-200 text-gray-600'">
                                3
                            </div>
                            <span class="w-2.5 h-2.5 rounded-full"
                                  :class="activeStageTab === 'in_progress' ? 'bg-emerald-600 bullet-glow-in-progress' : 'bg-gray-300'"></span>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-gray-900 flex items-center gap-1">
                                <span>In Progress</span>
                                @if(in_array($currentStatus, ['In Progress', 'On Progress']))
                                    <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold bg-emerald-200 text-emerald-900">Aktif</span>
                                @endif
                            </div>
                            <div class="text-[10.5px] text-gray-500 font-medium line-clamp-1">Delivery & Tim Engineer</div>
                        </div>
                    </button>

                    {{-- 4. PENDING TAB --}}
                    <button type="button" 
                            @click="activeStageTab = 'pending'"
                            :class="activeStageTab === 'pending' ? 'active ring-2 ring-orange-500/20 bg-orange-50/60 border-orange-300' : 'bg-gray-50/70 border-gray-200 hover:bg-gray-100/70'"
                            class="stage-tab-btn p-3 rounded-xl border text-left flex flex-col justify-between gap-2 cursor-pointer transition">
                        <div class="flex items-center justify-between">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs"
                                 :class="activeStageTab === 'pending' ? 'bg-orange-500 text-white shadow-xs' : 'bg-gray-200 text-gray-600'">
                                4
                            </div>
                            <span class="w-2.5 h-2.5 rounded-full"
                                  :class="activeStageTab === 'pending' ? 'bg-orange-500 bullet-glow-pending' : 'bg-gray-300'"></span>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-gray-900 flex items-center gap-1">
                                <span>Pending</span>
                                @if($currentStatus === 'Pending')
                                    <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold bg-orange-200 text-orange-900">Aktif</span>
                                @endif
                            </div>
                            <div class="text-[10.5px] text-gray-500 font-medium line-clamp-1">Kendala / On-Hold</div>
                        </div>
                    </button>

                    {{-- 5. COMPLETED TAB --}}
                    <button type="button" 
                            @click="activeStageTab = 'completed'"
                            :class="activeStageTab === 'completed' ? 'active ring-2 ring-teal-500/20 bg-teal-50/60 border-teal-300' : 'bg-gray-50/70 border-gray-200 hover:bg-gray-100/70'"
                            class="stage-tab-btn p-3 rounded-xl border text-left flex flex-col justify-between gap-2 cursor-pointer transition col-span-2 sm:col-span-1">
                        <div class="flex items-center justify-between">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs"
                                 :class="activeStageTab === 'completed' ? 'bg-teal-700 text-white shadow-xs' : 'bg-gray-200 text-gray-600'">
                                5
                            </div>
                            <span class="w-2.5 h-2.5 rounded-full"
                                  :class="activeStageTab === 'completed' ? 'bg-teal-700 bullet-glow-completed' : 'bg-gray-300'"></span>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-gray-900 flex items-center gap-1">
                                <span>Completed</span>
                                @if($currentStatus === 'Completed')
                                    <span class="px-1.5 py-0.2 rounded text-[9.5px] font-bold bg-teal-200 text-teal-900">Aktif</span>
                                @endif
                            </div>
                            <div class="text-[10.5px] text-gray-500 font-medium line-clamp-1">BAST & Serah Terima</div>
                        </div>
                    </button>

                </div>
            </div>

            {{-- 5. STAGE DYNAMIC CONTEXTUAL PANELS --}}

            {{-- PANEL A: DRAFT (Alur Persetujuan Head Pak Susanto & Direktur Pak Hariyadi) --}}
            <div x-show="activeStageTab === 'draft'" x-cloak class="space-y-4">
                <div class="bg-gradient-to-br from-[#FFF8F4] to-[#FDF2EC] rounded-2xl p-6 border border-[#EED7C8] shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#EED7C8]/70 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#A2623D] text-white flex items-center justify-center shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-gray-900">Alur Persetujuan Draft Proyek (Dual Sign-Off System)</h2>
                                <p class="text-xs text-gray-600">Proyek berstatus Draft memerlukan otorisasi resmi dari <strong>Head Divisi (Pak Susanto)</strong> dan <strong>Direktur (Pak Hariyadi)</strong> sebelum masuk ke pipeline komersial atau delivery lapangan.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if(!empty($headApproval['approved']) && !empty($directorApproval['approved']))
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Lengkap Disetujui
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-amber-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="4" class="opacity-25"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" class="opacity-75"></path></svg>
                                    Menunggu Persetujuan
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- 2 Approval Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        {{-- Reviewer 1: Pak Susanto (Head Divisi) --}}
                        <div class="bg-white rounded-xl p-5 border border-[#E5D2C5] shadow-2xs space-y-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-red-600 to-[#991B1B] text-white flex items-center justify-center font-bold text-sm shadow-xs">
                                        SD
                                    </div>
                                    <div>
                                        <div class="text-[10.5px] font-bold text-red-600 uppercase tracking-wider">Reviewer 1 • Head Divisi</div>
                                        <h3 class="text-sm font-bold text-gray-900">Pak Susanto Djaya</h3>
                                        <p class="text-[11px] text-gray-500">Kelayakan Teknis, SOW, & Alokasi Resource Engineer</p>
                                    </div>
                                </div>

                                <div>
                                    @if(!empty($headApproval['approved']))
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                            ✓ Disetujui
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1">
                                            ⏳ Menunggu Review
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100 text-xs text-gray-600 space-y-1">
                                <div class="font-semibold text-gray-700">Catatan Persetujuan Head Divisi:</div>
                                <div class="italic text-gray-600">"{{ $headApproval['notes'] ?? 'Menunggu review teknis.' }}"</div>
                                @if(!empty($headApproval['date']))
                                    <div class="text-[10px] text-gray-400 pt-1">Disahkan pada: {{ $headApproval['date'] }}</div>
                                @endif
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-1">
                                @if(empty($headApproval['approved']))
                                    <button type="button" 
                                            @click="openApproveModal('head')" 
                                            class="btn-ipnet-gradient px-4 py-2 rounded-xl text-xs font-bold cursor-pointer">
                                        Beri Persetujuan (Pak Susanto)
                                    </button>
                                @else
                                    <button type="button" 
                                            @click="openApproveModal('head')" 
                                            class="px-3.5 py-1.5 rounded-lg border border-gray-200 text-gray-600 text-xs font-bold hover:bg-gray-50 cursor-pointer">
                                        Perbarui Catatan
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Reviewer 2: Pak Hariyadi (Direktur) --}}
                        <div class="bg-white rounded-xl p-5 border border-[#E5D2C5] shadow-2xs space-y-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-gray-900 to-slate-800 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                                        HD
                                    </div>
                                    <div>
                                        <div class="text-[10.5px] font-bold text-gray-600 uppercase tracking-wider">Reviewer 2 • Direktur</div>
                                        <h3 class="text-sm font-bold text-gray-900">Pak Hariyadi</h3>
                                        <p class="text-[11px] text-gray-500">Otorisasi Anggaran, Finansial, & Validasi Kontrak</p>
                                    </div>
                                </div>

                                <div>
                                    @if(!empty($directorApproval['approved']))
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                            ✓ Disahkan
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1">
                                            ⏳ Menunggu Otorisasi
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100 text-xs text-gray-600 space-y-1">
                                <div class="font-semibold text-gray-700">Catatan Otorisasi Direktur:</div>
                                <div class="italic text-gray-600">"{{ $directorApproval['notes'] ?? 'Menunggu review otorisasi anggaran.' }}"</div>
                                @if(!empty($directorApproval['date']))
                                    <div class="text-[10px] text-gray-400 pt-1">Disahkan pada: {{ $directorApproval['date'] }}</div>
                                @endif
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-1">
                                @if(empty($directorApproval['approved']))
                                    <button type="button" 
                                            @click="openApproveModal('director')" 
                                            class="btn-ipnet-gradient px-4 py-2 rounded-xl text-xs font-bold cursor-pointer">
                                        Beri Otorisasi (Pak Hariyadi)
                                    </button>
                                @else
                                    <button type="button" 
                                            @click="openApproveModal('director')" 
                                            class="px-3.5 py-1.5 rounded-lg border border-gray-200 text-gray-600 text-xs font-bold hover:bg-gray-50 cursor-pointer">
                                        Perbarui Catatan
                                    </button>
                                @endif
                            </div>
                        </div>

                    </div>

                    {{-- Draft Requirements Checklist --}}
                    <div class="p-4 rounded-xl bg-white/80 border border-[#E5D2C5] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-4 flex-wrap text-gray-700 font-semibold">
                            <span class="font-bold text-gray-900">Checklist Kesiapan Draft:</span>
                            <span class="inline-flex items-center gap-1 text-emerald-700">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Estimasi Nilai (Rp {{ number_format($project->contract_value ?: 300000000, 0, ',', '.') }})
                            </span>
                            <span class="inline-flex items-center gap-1 text-emerald-700">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Data Klien ({{ $project->client ?: 'Sarana Kreasi Teknoart' }})
                            </span>
                            <span class="inline-flex items-center gap-1 {{ $allEngineers->count() > 0 ? 'text-emerald-700' : 'text-amber-700' }}">
                                @if($allEngineers->count() > 0)
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Tim Teknis: {{ $allEngineers->count() }} Engineer
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"></circle><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"></path></svg>
                                    Tim Teknis: Belum Ditugaskan
                                @endif
                            </span>
                        </div>

                        <button type="button" 
                                @click="openAssignModal('engineer')"
                                class="text-red-700 font-bold hover:underline shrink-0 cursor-pointer">
                            + Alokasikan Tim Sekarang
                        </button>
                    </div>
                </div>
            </div>

            {{-- PANEL B: IN PROGRESS (Delivery & Tim Engineer / PM) --}}
            <div x-show="activeStageTab === 'in_progress'" x-cloak class="space-y-4">
                <div class="bg-white rounded-2xl p-6 border border-emerald-200/80 shadow-xs space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-600 to-emerald-700 text-white flex items-center justify-center shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-gray-900">Fase Delivery & Implementasi Lapangan (In Progress)</h2>
                                <p class="text-xs text-gray-500">Proyek dalam status pengerjaan aktif & pengiriman barang. Dipantau oleh <strong>Project Manager</strong> dan dieksekusi oleh <strong>Tim Engineer</strong>.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="openAssignModal('engineer')"
                                    class="btn-ipnet-gradient px-3.5 py-1.5 rounded-xl text-xs font-bold cursor-pointer flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Assign Tim / Engineer</span>
                            </button>
                        </div>
                    </div>

                    {{-- Sub-Grid: PM Card & Engineer Team Display --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                        
                        {{-- 1. Project Manager (PM) Card --}}
                        <div class="ipnet-card p-5 border border-indigo-100 bg-gradient-to-br from-indigo-50/40 to-white flex flex-col justify-between gap-4">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[10.5px] font-bold text-indigo-700 uppercase tracking-wider">PROJECT MANAGER (PM)</span>
                                    @if($project->pm)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Belum Ada PM</span>
                                    @endif
                                </div>

                                @if($project->pm)
                                    <div class="flex items-center gap-3 pt-1">
                                        <div class="w-12 h-12 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-base shadow-xs">
                                            {{ strtoupper(substr($project->pm->name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <h4 class="text-sm font-bold text-gray-900 truncate">{{ $project->pm->name }}</h4>
                                            <p class="text-xs text-gray-500 truncate">{{ $project->pm->email }}</p>
                                            <div class="text-[11px] font-semibold text-indigo-600 mt-0.5">Penanggung Jawab Delivery</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-3 rounded-xl border border-dashed border-amber-300 bg-amber-50/60 text-xs text-amber-900 space-y-2">
                                        <div class="font-bold flex items-center gap-1.5 text-amber-800">
                                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <span>Project Manager Belum Ditugaskan</span>
                                        </div>
                                        <p class="text-[11px] text-amber-700">Tunjuk PM untuk mengkoordinasikan jadwal instalasi, resource teknis, dan komunikasi dengan klien.</p>
                                    </div>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-indigo-100/80">
                                <button type="button" 
                                        @click="openAssignModal('pm')" 
                                        class="w-full py-2 rounded-xl text-xs font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition cursor-pointer text-center">
                                    {{ $project->pm ? 'Ganti Project Manager' : '+ Tunjuk Project Manager Sekarang' }}
                                </button>
                            </div>
                        </div>

                        {{-- 2. Assigned Engineers ("sp yg kerjain / ad engineer ny ap ngga") --}}
                        <div class="lg:col-span-2 ipnet-card p-5 border border-gray-200 flex flex-col justify-between gap-4">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-[10.5px] font-bold text-gray-500 uppercase tracking-wider">TIM ENGINEER PELAKSANA</span>
                                        <div class="text-xs text-gray-500 mt-0.5">Daftar engineer yang bertugas mengerjakan implementasi di lapangan:</div>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $allEngineers->count() > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                        {{ $allEngineers->count() }} Engineer Ditugaskan
                                    </span>
                                </div>

                                {{-- Jika ADA Engineer yang Ditugaskan --}}
                                @if($allEngineers->count() > 0)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                                        @foreach($allEngineers as $eng)
                                            @php
                                                $engTasksCount = $project->tasks->where('engineer_id', $eng->id)->count();
                                                $roleName = $eng->roles->pluck('name')->first() ?? 'Network Engineer';
                                            @endphp
                                            <div class="p-3 rounded-xl border border-gray-200 bg-gray-50/80 flex items-center justify-between gap-3 hover:bg-white hover:border-gray-300 transition">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="relative w-10 h-10 rounded-xl bg-gradient-to-br from-red-600 to-[#8F0A0D] text-white flex items-center justify-center font-bold text-xs shadow-xs shrink-0">
                                                        {{ strtoupper(substr($eng->name, 0, 2)) }}
                                                        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="text-xs font-bold text-gray-900 truncate">{{ $eng->name }}</div>
                                                        <div class="text-[10.5px] text-gray-500 truncate">{{ $roleName }}</div>
                                                        <div class="text-[10px] text-gray-400 font-semibold">{{ $engTasksCount > 0 ? $engTasksCount . ' Milestone / Task' : 'Standby Support' }}</div>
                                                    </div>
                                                </div>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white text-emerald-700 border border-emerald-200 shrink-0">
                                                    Aktif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    {{-- KONDISI TIDAK ADA ENGINEER --}}
                                    <div class="p-4 rounded-xl border border-dashed border-red-300 bg-gradient-to-br from-red-50/60 to-amber-50/40 space-y-3">
                                        <div class="flex items-start gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-red-100 text-red-600 flex items-center justify-center shrink-0 mt-0.5">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-bold text-red-900">⚠️ Belum Ada Tim Engineer yang Ditugaskan untuk Proyek Ini</h4>
                                                <p class="text-[11.5px] text-red-700 mt-1 leading-relaxed">
                                                    Proyek saat ini sudah memasuki fase <strong>In Progress (Delivery)</strong>, namun belum ada engineer pelaksana yang dialokasikan pada milestone pekerjaan. Segera lakukan penugasan agar instalasi lapangan tidak mengalami delay.
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-end pt-1">
                                            <button type="button" 
                                                    @click="openAssignModal('engineer')" 
                                                    class="btn-ipnet-gradient px-4 py-2 rounded-xl text-xs font-bold cursor-pointer flex items-center gap-1.5 shadow-md">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                <span>+ Assign Engineer Sekarang</span>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-between pt-2 border-t border-gray-100 flex-wrap gap-2 text-xs">
                                <span class="text-gray-500 font-medium">Memerlukan penambahan tim teknis atau asistensi on-site?</span>
                                <button type="button" 
                                        @click="openAssignModal('engineer')"
                                        class="text-red-600 font-bold hover:underline cursor-pointer">
                                    + Tugaskan Engineer Tambahan
                                </button>
                            </div>
                        </div>

                    </div>

                    {{-- Delivery Progress Tracker Bar --}}
                    @php
                        $totalTasksCount = $project->tasks->count();
                        $completedTasksCount = $project->tasks->where('status', 'Completed')->count();
                        $calculatedProgress = $totalTasksCount > 0 ? round(($completedTasksCount / $totalTasksCount) * 100) : ($project->progress ?: 15);
                    @endphp
                    <div class="p-4 rounded-xl bg-gray-50 border border-gray-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-gray-800">Target Progres Delivery Fisik:</span>
                                <span class="font-bold text-emerald-700">{{ $calculatedProgress }}% ({{ $completedTasksCount }}/{{ $totalTasksCount }} Deliverables)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-gradient-to-r from-teal-500 to-emerald-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $calculatedProgress }}%"></div>
                            </div>
                        </div>

                        <div class="text-xs text-gray-500 font-medium shrink-0 border-l border-gray-200 pl-4">
                            <div>Target Selesai: <strong class="text-gray-900">{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : 'Sesuai SOW' }}</strong></div>
                            <div>Lokasi: <strong class="text-gray-900">{{ $project->location ?: 'On-Site Client' }}</strong></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PANEL C: OPPORTUNITY (Pipeline Sales CRM & Quotation) --}}
            <div x-show="activeStageTab === 'opportunity'" x-cloak class="space-y-4">
                <div class="bg-white rounded-2xl p-6 border border-blue-200 shadow-xs space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-gray-900">Pipeline Sales CRM & Commercial Opportunity</h2>
                                <p class="text-xs text-gray-500">Tahapan prospek komersial, negosiasi penawaran harga, dan probabilitas closing deal.</p>
                            </div>
                        </div>

                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200">
                            CRM Stage: {{ $project->sales_stage ?: 'Qualification' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                            <div class="text-[11px] font-bold text-gray-500 uppercase">WIN PROBABILITY</div>
                            <div class="text-xl font-bold text-blue-600 mt-1">{{ $project->win_probability ?: 25 }}%</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">Peluang Menang Tender</div>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                            <div class="text-[11px] font-bold text-gray-500 uppercase">WEIGHTED FORECAST</div>
                            <div class="text-xl font-bold text-gray-900 mt-1">
                                Rp {{ number_format(($project->contract_value ?: 300000000) * (($project->win_probability ?: 25) / 100), 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-gray-400 mt-0.5">Nilai Prospek Terbobot</div>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                            <div class="text-[11px] font-bold text-gray-500 uppercase">SALES PIC / BDM</div>
                            <div class="text-sm font-bold text-gray-900 mt-1">{{ $project->sales_name ?: 'Nabylla Berlianita' }}</div>
                            <div class="text-[10px] text-gray-400 mt-0.5">Account Manager</div>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                            <div class="text-[11px] font-bold text-gray-500 uppercase">CLOSING TARGET</div>
                            <div class="text-sm font-bold text-gray-900 mt-1">
                                {{ $project->expected_closing_date ? \Carbon\Carbon::parse($project->expected_closing_date)->format('d M Y') : '30 Sep 2026' }}
                            </div>
                            <div class="text-[10px] text-gray-400 mt-0.5">Estimasi Tanggal Closing</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PANEL D: PENDING (Kendala & Blocker) --}}
            <div x-show="activeStageTab === 'pending'" x-cloak class="space-y-4">
                <div class="bg-white rounded-2xl p-6 border border-orange-200 shadow-xs space-y-5">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                        <div class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Proyek Ditangguhkan (Pending / On-Hold)</h2>
                            <p class="text-xs text-gray-500">Pengerjaan proyek sedang ditahan sementara karena menunggu clearance perizinan atau supply sparepart.</p>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-orange-50/70 border border-orange-200 text-xs text-orange-900 space-y-2">
                        <div class="font-bold flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Informasi Status Penangguhan:</span>
                        </div>
                        <p class="text-gray-700 leading-relaxed">
                            Proyek ditandai pending. Jika kendala izin site access atau pengiriman unit sudah terselesaikan, Anda dapat langsung mengklik tombol di bawah untuk melanjutkan aktivitas ke tahap <strong>In Progress (Delivery)</strong>.
                        </p>
                    </div>

                    <form action="{{ route('projects.stage_update', $project->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="In Progress">
                        <button type="submit" class="btn-ipnet-gradient px-5 py-2.5 rounded-xl text-xs font-bold cursor-pointer flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                            <span>Lanjutkan Proyek Sekarang (Resume to In Progress)</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- PANEL E: COMPLETED (BAST & Serah Terima) --}}
            <div x-show="activeStageTab === 'completed'" x-cloak class="space-y-4">
                <div class="bg-white rounded-2xl p-6 border border-teal-200 shadow-xs space-y-5">
                    <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-700 text-white flex items-center justify-center shadow-xs">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-gray-900">Proyek Selesai & Serah Terima (Completed)</h2>
                            <p class="text-xs text-gray-500">Seluruh deliverable telah terselesaikan 100%, UAT disetujui klien, dan dokumen BAST telah diterbitkan.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200">
                            <div class="font-bold text-emerald-800">BAST STATUS</div>
                            <div class="text-sm font-bold text-emerald-900 mt-1">✓ Ditandatangani</div>
                            <div class="text-[10.5px] text-emerald-600 mt-0.5">Berita Acara Serah Terima Lengkap</div>
                        </div>

                        <div class="p-4 rounded-xl bg-teal-50 border border-teal-200">
                            <div class="font-bold text-teal-800">HANDOVER OPERASI</div>
                            <div class="text-sm font-bold text-teal-900 mt-1">Transferred to Managed Service</div>
                            <div class="text-[10.5px] text-teal-600 mt-0.5">Monitoring SLA Berjalan</div>
                        </div>

                        <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                            <div class="font-bold text-gray-700">GARANSI HARDWARE</div>
                            <div class="text-sm font-bold text-gray-900 mt-1">12 Bulan Resmi</div>
                            <div class="text-[10.5px] text-gray-400 mt-0.5">Masa Pemeliharaan Vendor</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6. PROJECT ESTIMATION & META BANNER --}}
            <div class="bg-[#F6EBE4] rounded-2xl p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-6 border border-[#E9D5C9]">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 flex-1">
                    <div>
                        <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">PROJECT ESTIMATION</div>
                        <div class="text-xl font-bold text-gray-900 mt-1">
                            Rp {{ number_format($project->contract_value ?: 300000000, 0, ',', '.') }}
                        </div>
                    </div>

                    <div>
                        <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">PROJECT START</div>
                        <div class="text-sm font-bold text-gray-900 mt-1.5">
                            {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : '24 Aug 2026' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">PROJECT END</div>
                        <div class="text-sm font-bold text-gray-900 mt-1.5">
                            {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}
                        </div>
                    </div>
                </div>

                <div class="shrink-0">
                    <button type="button" 
                            @click="isEditMetaModalOpen = true" 
                            class="p-2.5 rounded-xl bg-white/80 hover:bg-white text-gray-700 shadow-2xs hover:shadow-xs transition cursor-pointer" 
                            title="Edit Estimasi Proyek">
                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </button>
                </div>
            </div>

            {{-- 7. 2-COLUMN MAIN CONTENT --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- LEFT COLUMN (2 spans) --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Client Info --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900">Client Info</h3>
                            <span class="text-xs font-semibold text-gray-400">Customer Details</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                            <div>
                                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">CLIENT NAME</div>
                                <div class="font-bold text-gray-900 mt-1">{{ $project->client ?: 'Sarana Kreasi Teknoart' }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">CLIENT EMAIL</div>
                                <div class="font-bold text-gray-900 mt-1 truncate">{{ $project->customer_pic_finance ?: ($project->customer_pic_technical ?: 'sales@saranateknoart.com') }}</div>
                            </div>
                            <div>
                                <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">CONTACT PIC</div>
                                <div class="font-bold text-gray-900 mt-1">{{ $project->customer_pic_business ?: '081299771333' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Milestone Section --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900">Milestone</h3>
                            <div class="text-xs text-gray-500 font-semibold">
                                Complete ({{ $project->tasks->where('status', 'Completed')->count() }}/{{ $project->tasks->count() }})
                            </div>
                        </div>

                        <div class="space-y-2.5">
                            @forelse($project->tasks as $task)
                                <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50 hover:bg-white hover:border-gray-200 transition flex items-center justify-between text-xs gap-3">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <input type="checkbox" {{ $task->status === 'Completed' ? 'checked' : '' }} disabled class="rounded text-red-600 shrink-0">
                                        <div class="min-w-0">
                                            <span class="font-bold text-gray-800 truncate block">{{ $task->title ?? $task->name }}</span>
                                            @if($task->engineer)
                                                <div class="text-[10px] text-gray-400 font-medium">PIC: {{ $task->engineer->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold shrink-0
                                        @if($task->status === 'Completed') bg-emerald-50 text-emerald-700 border border-emerald-200
                                        @elseif($task->status === 'In Progress') bg-teal-50 text-teal-700 border border-teal-200
                                        @else bg-white text-gray-600 border border-gray-200 @endif">
                                        {{ $task->status }}
                                    </span>
                                </div>
                            @empty
                                <div class="py-6 text-center text-xs text-gray-400 bg-gray-50/50 rounded-xl border border-dashed border-gray-200">
                                    Belum ada milestone yang terdaftar untuk proyek ini.
                                </div>
                            @endforelse
                        </div>

                        <div>
                            <button type="button" 
                                    @click="isAddMilestoneModalOpen = true" 
                                    class="text-xs font-bold text-red-600 hover:text-red-800 flex items-center gap-1 cursor-pointer">
                                <span>+ ADD MILESTONE</span>
                            </button>
                        </div>
                    </div>

                    {{-- Attachments Section --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900">Attachments</h3>
                            <span class="text-xs font-semibold text-gray-400">Berkas Pendukung Proyek</span>
                        </div>
                        
                        <div class="space-y-2.5">
                            @if($project->relationLoaded('projectDocuments') && $project->projectDocuments->count() > 0)
                                @foreach($project->projectDocuments as $doc)
                                    <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50 flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            <span class="font-bold text-gray-800 truncate">{{ $doc->document_title ?? ($doc->document_name ?? 'Document') }}</span>
                                        </div>
                                        @if($doc->file_path)
                                            <a href="{{ route('projects.documents.download', [$project->id, $doc->id]) }}" class="text-red-600 font-bold hover:underline shrink-0">
                                                Download
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="py-4 text-center text-xs text-gray-400 bg-gray-50/50 rounded-xl border border-dashed border-gray-200">
                                    Belum ada berkas lampiran yang diunggah.
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-4 flex-wrap pt-1">
                            <button type="button" @click="isUploadDocModalOpen = true" class="text-xs font-bold text-red-600 hover:text-red-800 cursor-pointer">
                                + ADD ATTACHMENT
                            </button>
                            <button type="button" @click="isUploadDocModalOpen = true" class="text-xs font-bold text-red-600 hover:text-red-800 cursor-pointer">
                                + ADD COGS ATTACHMENT
                            </button>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN (1 span) --}}
                <div class="space-y-6">
                    
                    {{-- Project Status Overview --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <h3 class="text-sm font-bold text-gray-900">Project Status</h3>
                        <div class="flex items-center justify-between text-xs">
                            <span class="px-2.5 py-1 rounded bg-[#F6EBE4] text-[#A2623D] font-bold text-[11px]">
                                {{ $currentStatus }}
                            </span>
                            <span class="text-gray-500 font-medium">
                                Progress {{ $project->progress ?: 0 }}% ({{ $project->tasks->where('status', 'Completed')->count() }}/{{ $project->tasks->count() }} complete)
                            </span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-red-500 to-[#B81525] h-2 rounded-full" style="width: {{ $project->progress ?: 0 }}%"></div>
                        </div>
                    </div>

                    {{-- Project Activities --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <h3 class="text-sm font-bold text-gray-900">Project Activities</h3>
                        
                        <div class="space-y-3 text-xs">
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100 space-y-1">
                                <div class="font-bold text-gray-800">
                                    {{ $project->creator ? $project->creator->name : 'Nabylla Berlianita' }} created this project
                                </div>
                                <div class="text-[10.5px] text-gray-400">
                                    {{ \Carbon\Carbon::parse($project->created_at)->format('d M Y H:i') }}
                                </div>
                            </div>

                            @if(!empty($headApproval['approved']))
                                <div class="p-3.5 rounded-xl bg-emerald-50/50 border border-emerald-100 space-y-1">
                                    <div class="font-bold text-emerald-900">Persetujuan Head Divisi (Pak Susanto)</div>
                                    <div class="text-[10.5px] text-emerald-700">{{ $headApproval['date'] ?? 'Disetujui' }}</div>
                                </div>
                            @endif

                            @if(!empty($directorApproval['approved']))
                                <div class="p-3.5 rounded-xl bg-emerald-50/50 border border-emerald-100 space-y-1">
                                    <div class="font-bold text-emerald-900">Otorisasi Direktur (Pak Hariyadi)</div>
                                    <div class="text-[10.5px] text-emerald-700">{{ $directorApproval['date'] ?? 'Disahkan' }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODALS: ALL PROPERLY CENTERED & TELEPORTED TO BODY       --}}
    {{-- ======================================================== --}}

    {{-- 1. ASSIGN MODAL (TELEPORTED & CENTERED) --}}
    <template x-teleport="body">
        <div x-show="isAssignModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-[#0F172A]/70 backdrop-blur-xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isAssignModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-5 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Penugasan Tim Proyek (Assign)</h3>
                        <p class="text-xs text-gray-500">Tugaskan Lead/Engineer Pelaksana atau Project Manager</p>
                    </div>
                    <button type="button" @click="isAssignModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                {{-- Tab Switcher: Engineer vs Project Manager --}}
                <div class="flex items-center p-1 rounded-xl bg-gray-100 text-xs font-bold">
                    <button type="button" 
                            @click="assignRole = 'engineer'"
                            :class="assignRole === 'engineer' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-500 hover:text-gray-800'"
                            class="flex-1 py-2 rounded-lg text-center transition cursor-pointer">
                        Tim Engineer (Teknis)
                    </button>
                    <button type="button" 
                            @click="assignRole = 'pm'"
                            :class="assignRole === 'pm' ? 'bg-white text-gray-900 shadow-xs' : 'text-gray-500 hover:text-gray-800'"
                            class="flex-1 py-2 rounded-lg text-center transition cursor-pointer">
                        Project Manager (PM)
                    </button>
                </div>

                <form action="{{ route('projects.assign', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="role_type" :value="assignRole">

                    <div>
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">
                            <span x-text="assignRole === 'pm' ? 'PILIH PROJECT MANAGER' : 'PILIH ENGINEER'"></span>
                        </label>
                        <select name="user_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer bg-white">
                            <option value="">-- Pilih Pengguna --</option>
                            @php
                                $usersList = $allUsers ?? \App\Models\User::orderBy('name')->get();
                            @endphp
                            @foreach($usersList as $u)
                                @php
                                    $roleBadge = '(Engineer)';
                                    if ($u->hasAnyRole(['Director', 'Direktur', 'HD / Direktur'])) {
                                        $roleBadge = '(Direktur)';
                                    } elseif ($u->hasAnyRole(['Division Head', 'Head Divisi', 'Group Leader'])) {
                                        $roleBadge = '(Head)';
                                    } elseif ($u->hasAnyRole(['PMO', 'Project Manager'])) {
                                        $roleBadge = '(PM)';
                                    } elseif ($u->hasAnyRole(['Sales', 'Account Manager', 'BusDev'])) {
                                        $roleBadge = '(Sales)';
                                    }
                                @endphp
                                <option value="{{ $u->id }}" {{ $project->pm_id == $u->id ? 'selected' : '' }}>
                                    {{ $roleBadge }} {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Additional fields if assigning Engineer --}}
                    <div x-show="assignRole === 'engineer'" class="space-y-3">
                        <div>
                            <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">Judul Tugas / Milestone Delivery</label>
                            <input type="text" name="task_title" value="Delivery & Instalasi: {{ $project->name }}" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                        </div>

                        <div>
                            <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">Batas Waktu (Deadline Task)</label>
                            <input type="date" name="deadline" value="{{ $project->deadline ? $project->deadline->format('Y-m-d') : date('Y-m-d', strtotime('+7 days')) }}" 
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isAssignModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-5 py-2 rounded-xl font-bold text-white shadow-md hover:shadow-lg cursor-pointer">
                            Simpan Penugasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- 2. DRAFT APPROVAL MODAL (TELEPORTED & CENTERED) --}}
    <template x-teleport="body">
        <div x-show="isApproveModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-[#0F172A]/70 backdrop-blur-xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isApproveModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-5 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Form Persetujuan Draft</h3>
                        <p class="text-xs text-gray-500" x-text="approveRole === 'head' ? 'Persetujuan Head Divisi (Pak Susanto)' : 'Otorisasi Direktur (Pak Hariyadi)'"></p>
                    </div>
                    <button type="button" @click="isApproveModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.approve_draft', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="approval_role" :value="approveRole">

                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                        <span x-show="approveRole === 'head'">
                            Anda akan memberikan persetujuan sebagai <strong>Head Divisi (Pak Susanto Djaya)</strong> untuk kelayakan teknis dan alokasi resource engineer proyek ini.
                        </span>
                        <span x-show="approveRole === 'director'">
                            Anda akan memberikan otorisasi sebagai <strong>Direktur (Pak Hariyadi)</strong> untuk anggaran finansial dan persetujuan kontrak proyek ini.
                        </span>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">Catatan / Instruksi Tambahan</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"
                                  :placeholder="approveRole === 'head' ? 'Contoh: Disetujui, alokasikan 2 engineer network.' : 'Contoh: Disetujui, lanjutkan ke kontrak resmi.'"></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="auto_advance" id="auto_advance" value="1" checked class="rounded text-red-600">
                        <label for="auto_advance" class="text-gray-600 font-normal">Otomatis majukan ke tahap Opportunity jika kedua persetujuan lengkap</label>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isApproveModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-5 py-2 rounded-xl font-bold text-white shadow-md hover:shadow-lg cursor-pointer">
                            Kirim Persetujuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- 3. ADD MILESTONE MODAL (TELEPORTED & CENTERED) --}}
    <template x-teleport="body">
        <div x-show="isAddMilestoneModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-[#0F172A]/70 backdrop-blur-xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isAddMilestoneModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-5 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-base font-bold text-gray-900">+ Tambah Milestone Proyek</h3>
                    <button type="button" @click="isAddMilestoneModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="status" value="Pending">

                    <div>
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">NAMA MILESTONE / TUGAS</label>
                        <input type="text" name="title" required placeholder="Contoh: Pengiriman Aruba AP-505 dan APC" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">TUGASKAN KE ENGINEER</label>
                        <select name="engineer_id" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer bg-white">
                            <option value="">Pilih Engineer (Opsional)</option>
                            @foreach($usersList as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">PRIORITAS</label>
                            <select name="priority" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer bg-white">
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">DEADLINE</label>
                            <input type="date" name="deadline" value="{{ date('Y-m-d', strtotime('+7 days')) }}" 
                                   class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isAddMilestoneModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-5 py-2 rounded-xl font-bold text-white shadow-md hover:shadow-lg cursor-pointer">
                            Simpan Milestone
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- 4. EDIT META ESTIMATION MODAL (TELEPORTED & CENTERED) --}}
    <template x-teleport="body">
        <div x-show="isEditMetaModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-[#0F172A]/70 backdrop-blur-xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isEditMetaModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-5 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-base font-bold text-gray-900">Edit Estimasi & Jadwal Proyek</h3>
                    <button type="button" @click="isEditMetaModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.meta_update', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <div>
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">NILAI ESTIMASI PROYEK (RP)</label>
                        <input type="number" name="contract_value" value="{{ $project->contract_value ?: 300000000 }}" required 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">PROJECT START</label>
                            <input type="date" name="start_date" value="{{ $project->start_date ? $project->start_date->format('Y-m-d') : date('Y-m-d') }}" 
                                   class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">PROJECT END</label>
                            <input type="date" name="deadline" value="{{ $project->deadline ? $project->deadline->format('Y-m-d') : '' }}" 
                                   class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isEditMetaModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-5 py-2 rounded-xl font-bold text-white shadow-md hover:shadow-lg cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- 5. UPLOAD DOCUMENT MODAL (TELEPORTED & CENTERED) --}}
    <template x-teleport="body">
        <div x-show="isUploadDocModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-[#0F172A]/70 backdrop-blur-xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isUploadDocModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-5 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-base font-bold text-gray-900">+ Upload Lampiran / COGS</h3>
                    <button type="button" @click="isUploadDocModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.documents.upload', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="stage_number" value="1">
                    <input type="hidden" name="document_key" value="lampiran_pendukung">

                    <div>
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">PILIH BERKAS (PDF, XLSX, DOCX, ZIP)</label>
                        <input type="file" name="document_file" required 
                               class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">CATATAN DOKUMEN</label>
                        <input type="text" name="notes" placeholder="Contoh: BoQ dan COGS Hardware Aruba AP-505" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isUploadDocModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="btn-ipnet-gradient px-5 py-2 rounded-xl font-bold text-white shadow-md hover:shadow-lg cursor-pointer">
                            Upload Berkas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- DELETE PROJECT FORM --}}
    <form id="deleteProjForm" action="{{ route('projects.destroy', $project->id) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

</div>

<script>
    function projectDetailPage(initialStage, currentDbStatus) {
        return {
            activeStageTab: initialStage || 'draft',
            currentStatus: currentDbStatus || 'Draft',
            
            isAssignModalOpen: false,
            assignRole: 'engineer', // 'engineer' or 'pm'

            isApproveModalOpen: false,
            approveRole: 'head', // 'head' (Susanto) or 'director' (Hariyadi)

            isEditMetaModalOpen: false,
            isAddMilestoneModalOpen: false,
            isUploadDocModalOpen: false,

            openAssignModal(role = 'engineer') {
                this.assignRole = role;
                this.isAssignModalOpen = true;
            },

            openApproveModal(role = 'head') {
                this.approveRole = role;
                this.isApproveModalOpen = true;
            },

            stageNameToDbStatus(tabKey) {
                switch(tabKey) {
                    case 'draft': return 'Draft';
                    case 'opportunity': return 'Opportunity';
                    case 'in_progress': return 'In Progress';
                    case 'pending': return 'Pending';
                    case 'completed': return 'Completed';
                    default: return 'Draft';
                }
            },

            confirmDeleteProject() {
                if (confirm('Apakah Anda yakin ingin menghapus project ini beserta seluruh task-nya?')) {
                    document.getElementById('deleteProjForm').submit();
                }
            }
        };
    }
</script>
@endsection
