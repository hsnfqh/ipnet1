@extends('layouts.app')

@section('title', $project->name . ' - Detail Proyek')

@push('styles')
<style>
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 4px 12px rgba(0, 0, 0, 0.015);
    }
    .banner-peach {
        background-color: #FAF2ED;
        border: 1px solid #EFE4DC;
        border-radius: 12px;
    }
    .badge-terracotta {
        background-color: #A2623D;
        color: #FFFFFF;
    }
    .text-ipnet-red {
        color: #B81525;
    }
    .text-ipnet-red:hover {
        color: #8F0A0D;
    }
    .btn-ipnet-primary {
        background-color: #8F0A0D;
        color: #FFFFFF;
        transition: all 0.15s ease;
    }
    .btn-ipnet-primary:hover {
        background-color: #700609;
        color: #FFFFFF;
    }
    .stage-pill {
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .stage-pill.active {
        background-color: #0F172A;
        color: #FFFFFF;
        box-shadow: 0 1px 2px rgba(0,0,0,0.08);
    }
    .stage-pill:not(.active) {
        background-color: #F1F5F9;
        color: #475569;
    }
    .stage-pill:not(.active):hover {
        background-color: #E2E8F0;
        color: #0F172A;
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
        'notes' => 'Menunggu review kelayakan teknis dan alokasi resource.'
    ];
    $directorApproval = $draftApprovals['director'] ?? [
        'approved' => false,
        'by' => 'Hariyadi',
        'date' => null,
        'notes' => 'Menunggu otorisasi finansial dan persetujuan kontrak.'
    ];

    // Koleksi seluruh engineer pelaksana yang ditugaskan oleh PMO pada tasks proyek
    $assignedEngineers = collect();
    foreach($project->tasks as $t) {
        if ($t->engineer) {
            $assignedEngineers->push([
                'user' => $t->engineer,
                'task' => $t,
            ]);
        }
        if ($t->relationLoaded('engineers') && $t->engineers) {
            foreach($t->engineers as $eng) {
                $assignedEngineers->push([
                    'user' => $eng,
                    'task' => $t,
                ]);
            }
        }
    }
    $uniqueEngineers = $assignedEngineers->pluck('user')->unique('id');

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

    // Ambil daftar user PMO (Rizki, Kuncoro, dsb)
    $pmoUsers = \App\Models\User::role(['PMO', 'Project Manager'])->orWhere('name', 'like', '%Rizki%')->orderBy('name')->get();
@endphp

<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans text-slate-800" 
     x-data="projectDetailPage('{{ $initialStage }}', '{{ $currentStatus }}')">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Project'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-slate-900 text-white text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white font-bold px-2 cursor-pointer">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold px-2 cursor-pointer">✕</button>
                </div>
            @endif

            {{-- 1. BREADCRUMBS --}}
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-500">
                <a href="{{ route('sales.pipeline.index') }}" class="text-gray-900 hover:text-red-700 transition">Project</a>
                <span>&gt;</span>
                <span class="text-gray-600">{{ $project->name }}</span>
            </div>

            {{-- 2. MAIN CARD WRAPPER (Template IPNET) --}}
            <div class="ipnet-card overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-12 min-h-[580px]">
                    
                    {{-- ══ LEFT SECTION: PROJECT DETAILS (col-span-8 or 9) ══ --}}
                    <div class="lg:col-span-9 p-6 sm:p-8 space-y-6 lg:border-r lg:border-gray-200">
                        
                        {{-- A. Top Status Bar: Status Pill, Team + Add, Stage Selector, Creator info --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
                            <div class="flex items-center gap-2.5 flex-wrap">
                                {{-- Stage Selector Pills --}}
                                <div class="inline-flex items-center gap-1.5 p-1 rounded-xl bg-gray-100">
                                    <button type="button" @click="activeStageTab = 'draft'" :class="activeStageTab === 'draft' ? 'active' : ''" class="stage-pill cursor-pointer">
                                        <span>Draft</span>
                                    </button>
                                    <button type="button" @click="activeStageTab = 'opportunity'" :class="activeStageTab === 'opportunity' ? 'active' : ''" class="stage-pill cursor-pointer">
                                        <span>Opty</span>
                                    </button>
                                    <button type="button" @click="activeStageTab = 'in_progress'" :class="activeStageTab === 'in_progress' ? 'active' : ''" class="stage-pill cursor-pointer">
                                        <span>In Progress</span>
                                    </button>
                                    <button type="button" @click="activeStageTab = 'pending'" :class="activeStageTab === 'pending' ? 'active' : ''" class="stage-pill cursor-pointer">
                                        <span>Pending</span>
                                    </button>
                                    <button type="button" @click="activeStageTab = 'completed'" :class="activeStageTab === 'completed' ? 'active' : ''" class="stage-pill cursor-pointer">
                                        <span>Completed</span>
                                    </button>
                                </div>

                                {{-- Team Pill + Handover PMO Plus Button --}}
                                <div class="flex items-center gap-2 pl-2 text-xs">
                                    <span class="text-[11px] font-medium text-gray-400">Team</span>
                                    <span class="text-gray-700 font-semibold">{{ $project->division ? $project->division->name : 'IPNET 01' }}</span>
                                    <button type="button" 
                                            @click="isHandoverModalOpen = true" 
                                            class="w-5 h-5 rounded-full border border-red-300 text-red-600 flex items-center justify-center font-bold text-xs hover:bg-red-50 transition cursor-pointer"
                                            title="Handover ke PMO (Rizki)">
                                        +
                                    </button>
                                </div>
                            </div>

                            <div class="text-xs text-gray-400 font-normal shrink-0">
                                Created by <span class="text-gray-700 font-semibold">{{ $project->creator ? $project->creator->name : ($project->sales_name ?: 'Sales Team') }}</span>, 
                                {{ \Carbon\Carbon::parse($project->created_at)->format('d M Y H:i') }}
                            </div>
                        </div>

                        {{-- B. Project Title & Action Buttons --}}
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $project->name }}</h1>
                                <p class="text-xs text-gray-500 mt-1">{{ $project->description ?: $project->name }}</p>
                            </div>

                            {{-- Pending & Delete Action Buttons (Red text with simple icons) --}}
                            <div class="flex items-center gap-4 shrink-0 pt-1">
                                <form action="{{ route('projects.stage_update', $project->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $currentStatus === 'Pending' ? 'In Progress' : 'Pending' }}">
                                    <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-semibold text-ipnet-red hover:underline cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="1.8"></circle><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 7v5l3 2"/></svg>
                                        <span>{{ $currentStatus === 'Pending' ? 'Resume' : 'Pending' }}</span>
                                    </button>
                                </form>

                                <button type="button" @click="confirmDeleteProject()" class="inline-flex items-center gap-1.5 text-xs font-semibold text-ipnet-red hover:underline cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </div>

                        {{-- C. Project Estimation Banner (Warm Peach Box) --}}
                        <div class="banner-peach p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 flex-1 text-xs">
                                <div>
                                    <div class="text-[10.5px] font-bold text-gray-400 uppercase tracking-wider">PROJECT ESTIMATION</div>
                                    <div class="text-base font-bold text-gray-900 mt-1">
                                        Rp {{ number_format($project->contract_value ?: 300000000, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-[10.5px] font-bold text-gray-400 uppercase tracking-wider">PROJECT START</div>
                                    <div class="text-xs font-bold text-gray-900 mt-1.5">
                                        {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : '24 Aug 2026' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-[10.5px] font-bold text-gray-400 uppercase tracking-wider">PROJECT END</div>
                                    <div class="text-xs font-bold text-gray-900 mt-1.5">
                                        {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}
                                    </div>
                                </div>
                            </div>

                            <div class="shrink-0">
                                <button type="button" 
                                        @click="isEditMetaModalOpen = true" 
                                        class="p-2 rounded-lg bg-white/70 hover:bg-white text-gray-700 transition cursor-pointer shadow-2xs" 
                                        title="Edit Estimasi Proyek">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- D. Dynamic Contextual Panel: Draft, In Progress, Opty, etc. --}}

                        {{-- TAB 1: DRAFT (Persetujuan Pak Susanto Head & Pak Hariyadi Direktur) --}}
                        <div x-show="activeStageTab === 'draft'" x-cloak class="p-5 rounded-xl border border-gray-200 bg-gray-50/60 space-y-4">
                            <div class="flex items-center justify-between border-b border-gray-200 pb-2.5">
                                <div>
                                    <div class="text-xs font-bold text-gray-900">Alur Persetujuan Draft Proyek (Dual Sign-Off)</div>
                                    <div class="text-[11px] text-gray-500">Persetujuan berjenjang dari <strong>Pak Susanto (Head Divisi)</strong> dan <strong>Pak Hariyadi (Direktur)</strong> sebelum diserahkan ke PMO:</div>
                                </div>
                                <span class="text-[11px] font-bold {{ (!empty($headApproval['approved']) && !empty($directorApproval['approved'])) ? 'text-gray-900' : 'text-amber-800' }}">
                                    {{ (!empty($headApproval['approved']) && !empty($directorApproval['approved'])) ? 'Disetujui Lengkap' : 'Menunggu Approval' }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                {{-- Reviewer 1: Pak Susanto --}}
                                <div class="p-4 rounded-xl bg-white border border-gray-200 space-y-2.5">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-[10px] font-bold text-gray-400 uppercase">HEAD DIVISI</div>
                                            <div class="font-bold text-gray-900 text-sm">Pak Susanto Djaya</div>
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded text-[10.5px] font-bold {{ !empty($headApproval['approved']) ? 'bg-slate-100 text-slate-800 border border-slate-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                            {{ !empty($headApproval['approved']) ? 'Disetujui' : 'Menunggu' }}
                                        </span>
                                    </div>
                                    <div class="text-[11.5px] text-gray-600 italic">"{{ $headApproval['notes'] ?? 'Review kelayakan teknis & alokasi resource.' }}"</div>
                                    <div class="text-right pt-1 border-t border-gray-100">
                                        <button type="button" @click="openApproveModal('head')" class="text-xs font-bold text-ipnet-red hover:underline cursor-pointer">
                                            {{ empty($headApproval['approved']) ? '+ Beri Approval Pak Susanto' : 'Ubah Catatan' }}
                                        </button>
                                    </div>
                                </div>

                                {{-- Reviewer 2: Pak Hariyadi --}}
                                <div class="p-4 rounded-xl bg-white border border-gray-200 space-y-2.5">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-[10px] font-bold text-gray-400 uppercase">DIREKTUR</div>
                                            <div class="font-bold text-gray-900 text-sm">Pak Hariyadi</div>
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded text-[10.5px] font-bold {{ !empty($directorApproval['approved']) ? 'bg-slate-100 text-slate-800 border border-slate-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                                            {{ !empty($directorApproval['approved']) ? 'Disahkan' : 'Menunggu' }}
                                        </span>
                                    </div>
                                    <div class="text-[11.5px] text-gray-600 italic">"{{ $directorApproval['notes'] ?? 'Otorisasi finansial & validasi kontrak.' }}"</div>
                                    <div class="text-right pt-1 border-t border-gray-100">
                                        <button type="button" @click="openApproveModal('director')" class="text-xs font-bold text-ipnet-red hover:underline cursor-pointer">
                                            {{ empty($directorApproval['approved']) ? '+ Beri Approval Pak Hariyadi' : 'Ubah Catatan' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: IN PROGRESS (Delivery ke PMO Rizki & Monitoring Tim Engineer) --}}
                        <div x-show="activeStageTab === 'in_progress'" x-cloak class="p-5 rounded-xl border border-gray-200 bg-gray-50/60 space-y-4">
                            <div class="flex items-center justify-between border-b border-gray-200 pb-2.5">
                                <div>
                                    <div class="text-xs font-bold text-gray-900">Fase Delivery ke PMO (In Progress)</div>
                                    <div class="text-[11px] text-gray-500">Sales menyerahkan proyek ke PMO. Alokasi personel teknis dikelola PMO dan dipantau Sales di bawah:</div>
                                </div>
                                <button type="button" @click="isHandoverModalOpen = true" class="text-xs font-bold text-ipnet-red hover:underline cursor-pointer">
                                    {{ $project->pm ? 'Ubah PMO (' . $project->pm->name . ')' : '+ Handover ke PMO (Rizki)' }}
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                {{-- PMO Info --}}
                                <div class="p-4 rounded-xl bg-white border border-gray-200 space-y-2">
                                    <div class="text-[10px] font-bold text-gray-400 uppercase">PROJECT MANAGER (PMO)</div>
                                    <div class="font-bold text-gray-900 text-sm">{{ $project->pm ? $project->pm->name : 'Belum Ada PM (Tunjuk Rizki)' }}</div>
                                    <div class="text-[11.5px] text-gray-500">{{ $project->pm ? $project->pm->email : 'Sales silakan serahkan delivery ke Rizki' }}</div>
                                </div>

                                {{-- Engineer Monitoring Info (Read-Only) --}}
                                <div class="p-4 rounded-xl bg-white border border-gray-200 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-gray-400 uppercase">TIM ENGINEER PELAKSANA</span>
                                        <span class="text-[10px] font-bold text-gray-600">{{ $uniqueEngineers->count() }} Orang</span>
                                    </div>
                                    @if($uniqueEngineers->count() > 0)
                                        <div class="space-y-1.5 pt-1">
                                            @foreach($uniqueEngineers as $eng)
                                                <div class="flex items-center justify-between text-[11.5px]">
                                                    <span class="font-semibold text-gray-900">{{ $eng->name }}</span>
                                                    <span class="text-gray-400">{{ $eng->roles->pluck('name')->first() ?? 'Engineer' }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-[11.5px] text-gray-400 pt-1 italic">
                                            Menunggu alokasi tim teknis oleh PMO (Rizki).
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- TAB 3: OPPORTUNITY / CRM --}}
                        <div x-show="activeStageTab === 'opportunity'" x-cloak class="p-5 rounded-xl border border-gray-200 bg-gray-50/60 text-xs space-y-2">
                            <div class="font-bold text-gray-900">Pipeline Sales & Opportunity</div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1 text-gray-600">
                                <div>Stage: <strong class="text-gray-900">{{ $project->sales_stage ?: 'Proposal' }}</strong></div>
                                <div>Win Probability: <strong class="text-gray-900">{{ $project->win_probability ?: 25 }}%</strong></div>
                                <div>Target Closing: <strong class="text-gray-900">{{ $project->expected_closing_date ? \Carbon\Carbon::parse($project->expected_closing_date)->format('d M Y') : '30 Sep 2026' }}</strong></div>
                            </div>
                        </div>

                        {{-- TAB 4: PENDING --}}
                        <div x-show="activeStageTab === 'pending'" x-cloak class="p-5 rounded-xl border border-gray-200 bg-gray-50/60 text-xs space-y-2">
                            <div class="font-bold text-gray-900">Proyek Ditangguhkan (Pending)</div>
                            <p class="text-gray-500">Pengerjaan proyek sedang di-pause menunggu konfirmasi akses site atau pengiriman barang.</p>
                        </div>

                        {{-- TAB 5: COMPLETED --}}
                        <div x-show="activeStageTab === 'completed'" x-cloak class="p-5 rounded-xl border border-gray-200 bg-gray-50/60 text-xs space-y-2">
                            <div class="font-bold text-gray-900">Proyek Selesai & BAST Terbit</div>
                            <p class="text-gray-500">Seluruh milestone selesai 100% dan berkas Berita Acara Serah Terima telah disahkan.</p>
                        </div>

                        {{-- E. Client Info Section (4 Kolom Sesuai Template) --}}
                        <div class="pt-5 border-t border-gray-200 space-y-3">
                            <h3 class="text-sm font-bold text-gray-900">Client Info</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 text-xs">
                                <div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">CLIENT NAME</div>
                                    <div class="font-bold text-gray-900 mt-1">{{ $project->client ?: 'Sarana Kreasi Teknoart' }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">CLIENT EMAIL</div>
                                    <div class="font-bold text-gray-900 mt-1 truncate">{{ $project->customer_pic_finance ?: ($project->customer_pic_technical ?: 'sales@saranateknoart.com') }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">PIC</div>
                                    <div class="font-bold text-gray-900 mt-1">{{ $project->customer_pic_name ?? ($project->sales_name ?? 'Yanuar') }}</div>
                                </div>
                                <div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">CONTACT PIC</div>
                                    <div class="font-bold text-gray-900 mt-1">{{ $project->customer_pic_business ?: '081299771333' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- F. Milestone Section --}}
                        <div class="pt-6 border-t border-gray-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-gray-900">Milestone</h3>
                                <div class="text-xs text-gray-400 font-normal">
                                    Complete ({{ $project->tasks->where('status', 'Completed')->count() }}/{{ $project->tasks->count() }})
                                </div>
                            </div>

                            @if($project->tasks->count() > 0)
                                <div class="space-y-2">
                                    @foreach($project->tasks as $task)
                                        <div class="p-3 rounded-lg border border-gray-200 bg-white flex items-center justify-between text-xs">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <input type="checkbox" {{ $task->status === 'Completed' ? 'checked' : '' }} disabled class="rounded text-red-600 shrink-0">
                                                <div class="min-w-0">
                                                    <span class="font-bold text-gray-900 truncate block">{{ $task->title ?? $task->name }}</span>
                                                    @if($task->engineer)
                                                        <div class="text-[10.5px] text-gray-400">Engineer: {{ $task->engineer->name }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700">
                                                {{ $task->status }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div>
                                <button type="button" 
                                        @click="isAddMilestoneModalOpen = true" 
                                        class="text-xs font-bold text-ipnet-red hover:underline cursor-pointer">
                                    +ADD MILESTONE
                                </button>
                            </div>
                        </div>

                        {{-- G. Attachments Section --}}
                        <div class="pt-6 border-t border-gray-200 space-y-3">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <h3 class="text-sm font-bold text-gray-900">Attachments</h3>
                                
                                <div class="flex items-center gap-4 text-xs font-bold">
                                    <button type="button" @click="isUploadDocModalOpen = true" class="text-ipnet-red hover:underline cursor-pointer">
                                        + ADD ATTACHMENT
                                    </button>
                                    <button type="button" @click="isUploadDocModalOpen = true" class="text-ipnet-red hover:underline cursor-pointer">
                                        + ADD COGS ATTACHMENT
                                    </button>
                                </div>
                            </div>

                            @if($project->relationLoaded('projectDocuments') && $project->projectDocuments->count() > 0)
                                <div class="space-y-2">
                                    @foreach($project->projectDocuments as $doc)
                                        <div class="p-3 rounded-lg border border-gray-200 bg-white flex items-center justify-between text-xs">
                                            <span class="font-bold text-gray-900 truncate">{{ $doc->document_title ?? ($doc->document_name ?? 'Document') }}</span>
                                            @if($doc->file_path)
                                                <a href="{{ route('projects.documents.download', [$project->id, $doc->id]) }}" class="text-ipnet-red font-bold hover:underline">
                                                    Download
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                    </div>

                    {{-- ══ RIGHT SECTION: PROJECT STATUS & ACTIVITIES (col-span-3) ══ --}}
                    <div class="lg:col-span-3 p-6 sm:p-8 space-y-6 bg-white">
                        
                        {{-- Project Status --}}
                        <div class="space-y-2.5">
                            <div class="text-xs font-bold text-gray-400">Project Status</div>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold text-white badge-terracotta">
                                    <span x-text="stageNameToDbStatus(activeStageTab)">{{ $currentStatus }}</span>
                                </span>
                                <span class="text-gray-400 text-[11.5px]">
                                    Progress {{ $project->progress ?: 0 }}% ({{ $project->tasks->where('status', 'Completed')->count() }}/{{ $project->tasks->count() }} complete)
                                </span>
                            </div>
                        </div>

                        {{-- Project Activities --}}
                        <div class="pt-6 border-t border-gray-100 space-y-3">
                            <h3 class="text-sm font-bold text-gray-900">Project Activities</h3>
                            
                            <div class="space-y-3 text-xs">
                                <div class="space-y-1">
                                    <div class="font-normal text-gray-700">
                                        <strong class="font-medium text-gray-900">{{ $project->creator ? $project->creator->name : ($project->sales_name ?: 'Sales Team') }}</strong> created this project
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ \Carbon\Carbon::parse($project->created_at)->format('d M Y H:i') }}
                                    </div>
                                </div>

                                @if($project->pm)
                                    <div class="space-y-1 pt-2 border-t border-gray-100">
                                        <div class="font-normal text-gray-700">
                                            Handover ke PMO: <strong class="font-medium text-gray-900">{{ $project->pm->name }}</strong>
                                        </div>
                                        <div class="text-[11px] text-gray-400">
                                            Delivery dipimpin oleh PMO
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($headApproval['approved']))
                                    <div class="space-y-1 pt-2 border-t border-gray-100">
                                        <div class="font-normal text-gray-700">
                                            Approval Head: <strong class="font-medium text-gray-900">Pak Susanto</strong>
                                        </div>
                                        <div class="text-[11px] text-gray-400">
                                            {{ $headApproval['date'] ?? 'Disetujui' }}
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($directorApproval['approved']))
                                    <div class="space-y-1 pt-2 border-t border-gray-100">
                                        <div class="font-normal text-gray-700">
                                            Approval Direktur: <strong class="font-medium text-gray-900">Pak Hariyadi</strong>
                                        </div>
                                        <div class="text-[11px] text-gray-400">
                                            {{ $directorApproval['date'] ?? 'Disahkan' }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODALS: CENTERED & CLEAN                                --}}
    {{-- ======================================================== --}}

    {{-- 1. ASSIGN / HANDOVER TO PMO (RIZKI) MODAL --}}
    <template x-teleport="body">
        <div x-show="isHandoverModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-2xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isHandoverModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-base font-bold text-gray-900">Assign PMO (Handover)</h3>
                    <button type="button" @click="isHandoverModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.assign', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="role_type" value="pm">

                    <p class="text-gray-500 font-normal leading-relaxed">
                        Sales menyerahkan proyek ke PMO. Pilih Project Manager (misal: <strong>Rizki</strong>) yang akan mengatur teknis dan personel engineer.
                    </p>

                    <div>
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">PILIH USER PMO</label>
                        <select name="user_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer bg-white">
                            <option value="">Select a user</option>
                            @foreach($pmoUsers as $pmo)
                                <option value="{{ $pmo->id }}" {{ ($project->pm_id == $pmo->id || (empty($project->pm_id) && str_contains(strtolower($pmo->name), 'rizki'))) ? 'selected' : '' }}>
                                    (PMO) {{ $pmo->name }}
                                </option>
                            @endforeach
                            @php
                                $otherUsers = ($allUsers ?? \App\Models\User::orderBy('name')->get())->whereNotIn('id', $pmoUsers->pluck('id'));
                            @endphp
                            @foreach($otherUsers as $ou)
                                @php
                                    $prefix = $ou->hasAnyRole(['Director', 'Direktur', 'Division Head']) ? '(Head)' : ($ou->hasAnyRole(['Sales']) ? '(Sales)' : '(Engineer)');
                                @endphp
                                <option value="{{ $ou->id }}" {{ $project->pm_id == $ou->id ? 'selected' : '' }}>
                                    {{ $prefix }} {{ $ou->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isHandoverModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                            Assign ke PMO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- 2. DRAFT APPROVAL MODAL --}}
    <template x-teleport="body">
        <div x-show="isApproveModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-2xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isApproveModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-base font-bold text-gray-900" x-text="approveRole === 'head' ? 'Approval Head Divisi (Pak Susanto)' : 'Approval Direktur (Pak Hariyadi)'"></h3>
                    <button type="button" @click="isApproveModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.approve_draft', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="approval_role" :value="approveRole">

                    <div>
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[11px]">CATATAN PERSETUJUAN</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"
                                  :placeholder="approveRole === 'head' ? 'Contoh: Kelayakan teknis & alokasi resource disetujui.' : 'Contoh: Otorisasi anggaran & kontrak disahkan.'"></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="auto_advance" id="auto_advance" value="1" checked class="rounded text-red-600">
                        <label for="auto_advance" class="text-gray-600 font-normal">Otomatis ubah status saat kedua approval lengkap</label>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isApproveModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                            Simpan Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- 3. ADD MILESTONE MODAL --}}
    <template x-teleport="body">
        <div x-show="isAddMilestoneModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-2xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isAddMilestoneModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-base font-bold text-gray-900">+ Add Milestone</h3>
                    <button type="button" @click="isAddMilestoneModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="status" value="Pending">

                    <div>
                        <label class="block text-gray-700 mb-1 uppercase tracking-wider text-[10.5px]">NAMA MILESTONE</label>
                        <input type="text" name="title" required placeholder="Contoh: Pengiriman Aruba AP-505 dan APC" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-700 mb-1 uppercase tracking-wider text-[10.5px]">PRIORITAS</label>
                            <select name="priority" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white">
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1 uppercase tracking-wider text-[10.5px]">DEADLINE</label>
                            <input type="date" name="deadline" value="{{ date('Y-m-d', strtotime('+7 days')) }}" 
                                   class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isAddMilestoneModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                            Simpan Milestone
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- 4. EDIT META ESTIMATION MODAL --}}
    <template x-teleport="body">
        <div x-show="isEditMetaModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-2xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isEditMetaModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-base font-bold text-gray-900">Edit Estimasi & Tanggal</h3>
                    <button type="button" @click="isEditMetaModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.meta_update', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <div>
                        <label class="block text-gray-700 mb-1 uppercase tracking-wider text-[10.5px]">NILAI ESTIMASI (RP)</label>
                        <input type="number" name="contract_value" value="{{ $project->contract_value ?: 300000000 }}" required 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-700 mb-1 uppercase tracking-wider text-[10.5px]">PROJECT START</label>
                            <input type="date" name="start_date" value="{{ $project->start_date ? $project->start_date->format('Y-m-d') : date('Y-m-d') }}" 
                                   class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1 uppercase tracking-wider text-[10.5px]">PROJECT END</label>
                            <input type="date" name="deadline" value="{{ $project->deadline ? $project->deadline->format('Y-m-d') : '' }}" 
                                   class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isEditMetaModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- 5. UPLOAD DOCUMENT MODAL --}}
    <template x-teleport="body">
        <div x-show="isUploadDocModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-2xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isUploadDocModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-base font-bold text-gray-900">Upload Attachment</h3>
                    <button type="button" @click="isUploadDocModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.documents.upload', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="stage_number" value="1">
                    <input type="hidden" name="document_key" value="lampiran_pendukung">

                    <div>
                        <label class="block text-gray-700 mb-1 uppercase tracking-wider text-[10.5px]">PILIH BERKAS (PDF, XLSX, DOCX, ZIP)</label>
                        <input type="file" name="document_file" required 
                               class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1 uppercase tracking-wider text-[10.5px]">CATATAN DOKUMEN</label>
                        <input type="text" name="notes" placeholder="Contoh: BoQ dan Penawaran Resmi" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isUploadDocModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
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
            
            isHandoverModalOpen: false,
            isApproveModalOpen: false,
            approveRole: 'head', // 'head' (Susanto) or 'director' (Hariyadi)

            isEditMetaModalOpen: false,
            isAddMilestoneModalOpen: false,
            isUploadDocModalOpen: false,

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
