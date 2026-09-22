@extends('layouts.app')

@section('title', $project->name . ' - Detail Proyek')

@push('styles')
<style>
    .bg-banner-peach {
        background-color: #FAF2ED;
        border: 1px solid #EFE4DC;
    }
    .badge-terracotta {
        background-color: #A2623D;
        color: #FFFFFF;
    }
    .text-ipnet-red {
        color: #D32F2F;
    }
    .text-ipnet-red:hover {
        color: #B71C1C;
    }
    .border-ipnet-red {
        border-color: #D32F2F;
    }
    .stage-switch-btn {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 600;
        transition: all 0.15s ease;
    }
    .stage-switch-btn.active {
        background-color: #A2623D;
        color: #FFFFFF;
    }
    .stage-switch-btn:not(.active) {
        background-color: #F3F4F6;
        color: #4B5563;
    }
    .stage-switch-btn:not(.active):hover {
        background-color: #E5E7EB;
        color: #111827;
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

<div class="flex h-screen overflow-hidden bg-white font-sans text-gray-800" 
     x-data="projectDetailPage('{{ $initialStage }}', '{{ $currentStatus }}')">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto bg-white">
        
        {{-- TOP HEADER BAR (Matching Screenshot 1-to-1) --}}
        <div class="border-b border-gray-200 px-6 py-3.5 flex items-center justify-between">
            {{-- Breadcrumb --}}
            <div class="flex items-center gap-2 text-xs font-medium text-gray-500">
                <a href="{{ route('sales.pipeline.index') }}" class="font-bold text-gray-900 hover:text-red-700 transition">Project</a>
                <span class="text-gray-400">&gt;</span>
                <span class="text-gray-700 font-semibold">{{ $project->name }}</span>
            </div>

            {{-- User Info Pill --}}
            <div class="flex items-center gap-4">
                <button type="button" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </button>
                <div class="flex items-center gap-3 pl-2 border-l border-gray-200">
                    <div class="text-right">
                        <div class="text-xs font-bold text-gray-900">{{ auth()->user()->name ?? 'Nabylla Berlianita' }}</div>
                        <div class="text-[10px] text-gray-400 font-medium leading-tight">Sales • IPNET 01</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mx-6 mt-4 p-3.5 rounded-xl bg-gray-900 text-white text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-gray-400 hover:text-white font-bold px-2 cursor-pointer">✕</button>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-4 p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold px-2 cursor-pointer">✕</button>
            </div>
        @endif

        {{-- MAIN 2-COLUMN LAYOUT (Matching Screenshot 1-to-1) --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 min-h-[calc(100vh-65px)]">
            
            {{-- LEFT SECTION (3 of 4 columns) --}}
            <div class="lg:col-span-3 p-6 lg:p-8 space-y-6 lg:border-r lg:border-gray-200">
                
                {{-- 1. Status Badge & Team Row (Matching Screenshot Top) --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3 flex-wrap">
                        {{-- Draft Badge with Play Arrow --}}
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold badge-terracotta shadow-2xs">
                            <span x-text="stageNameToDbStatus(activeStageTab)">{{ $currentStatus }}</span>
                            <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        </div>

                        {{-- Team Label & Badge --}}
                        <div class="flex items-center gap-2 text-xs">
                            <span class="text-[11px] font-medium text-gray-400">Team</span>
                            <span class="text-gray-700 font-medium text-xs">{{ $project->division ? $project->division->name : 'IPNET 01' }}</span>
                            
                            {{-- Circular Plus Button (Triggers Handover to PMO) --}}
                            <button type="button" 
                                    @click="isHandoverModalOpen = true" 
                                    class="w-5 h-5 rounded-full border border-red-400 text-red-600 flex items-center justify-center font-bold text-xs hover:bg-red-50 transition cursor-pointer"
                                    title="Handover ke PMO (Rizki)">
                                +
                            </button>
                        </div>

                        {{-- Clean Lifecycle Stage Switcher --}}
                        <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-lg ml-2">
                            <button type="button" @click="activeStageTab = 'draft'" :class="activeStageTab === 'draft' ? 'active' : ''" class="stage-switch-btn cursor-pointer">Draft</button>
                            <button type="button" @click="activeStageTab = 'opportunity'" :class="activeStageTab === 'opportunity' ? 'active' : ''" class="stage-switch-btn cursor-pointer">Opty</button>
                            <button type="button" @click="activeStageTab = 'in_progress'" :class="activeStageTab === 'in_progress' ? 'active' : ''" class="stage-switch-btn cursor-pointer">In Progress</button>
                            <button type="button" @click="activeStageTab = 'pending'" :class="activeStageTab === 'pending' ? 'active' : ''" class="stage-switch-btn cursor-pointer">Pending</button>
                            <button type="button" @click="activeStageTab = 'completed'" :class="activeStageTab === 'completed' ? 'active' : ''" class="stage-switch-btn cursor-pointer">Completed</button>
                        </div>
                    </div>

                    <div class="text-xs text-gray-400 font-normal">
                        Created by <span class="text-gray-600 font-medium">{{ $project->creator ? $project->creator->name : ($project->sales_name ?: 'Nabylla Berlianita') }}</span>, 
                        {{ \Carbon\Carbon::parse($project->created_at)->format('d M Y H:i') }}
                    </div>
                </div>

                {{-- 2. Project Title & Subtitle + Action Buttons (Matching Screenshot) --}}
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">{{ $project->name }}</h1>
                        <p class="text-xs text-gray-500 mt-1">{{ $project->description ?: $project->name }}</p>
                    </div>

                    {{-- Pending & Delete Action Buttons (Red text with simple icons, matching screenshot) --}}
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

                {{-- 3. Project Estimation Banner (Matching Screenshot Warm Beige Box) --}}
                <div class="bg-banner-peach rounded-xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
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
                                class="p-1.5 text-gray-600 hover:text-gray-900 transition cursor-pointer" 
                                title="Edit Estimasi Proyek">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </button>
                    </div>
                </div>

                {{-- 4. CONTEXTUAL CONTENT BERDASARKAN TAB AKTIF --}}

                {{-- TAB A: DRAFT APPROVAL (Persetujuan Pak Susanto Head & Pak Hariyadi Direktur) --}}
                <div x-show="activeStageTab === 'draft'" x-cloak class="p-4 rounded-xl border border-amber-200 bg-amber-50/40 space-y-4">
                    <div class="flex items-center justify-between border-b border-amber-200/60 pb-2.5">
                        <div>
                            <div class="text-xs font-bold text-gray-900">Alur Persetujuan Draft Proyek (Dual Sign-Off)</div>
                            <div class="text-[11px] text-gray-500">Draft butuh persetujuan dari <strong>Pak Susanto (Head Divisi)</strong> dan <strong>Pak Hariyadi (Direktur)</strong> sebelum masuk ke delivery PMO.</div>
                        </div>
                        <span class="text-[11px] font-semibold text-amber-800">
                            {{ (!empty($headApproval['approved']) && !empty($directorApproval['approved'])) ? '✓ Lengkap Disetujui' : 'Menunggu Approval' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        {{-- Reviewer 1: Pak Susanto --}}
                        <div class="p-3.5 rounded-lg bg-white border border-gray-200 space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase">HEAD DIVISI</div>
                                    <div class="font-bold text-gray-900">Pak Susanto Djaya</div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ !empty($headApproval['approved']) ? 'bg-gray-100 text-gray-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ !empty($headApproval['approved']) ? 'Disetujui' : 'Menunggu' }}
                                </span>
                            </div>
                            <div class="text-[11px] text-gray-500 italic">"{{ $headApproval['notes'] ?? 'Review kelayakan teknis & alokasi resource.' }}"</div>
                            <div class="text-right pt-1">
                                <button type="button" @click="openApproveModal('head')" class="text-xs font-bold text-ipnet-red hover:underline cursor-pointer">
                                    {{ empty($headApproval['approved']) ? '+ Beri Approval Pak Susanto' : 'Ubah Catatan' }}
                                </button>
                            </div>
                        </div>

                        {{-- Reviewer 2: Pak Hariyadi --}}
                        <div class="p-3.5 rounded-lg bg-white border border-gray-200 space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-[10px] font-bold text-gray-400 uppercase">DIREKTUR</div>
                                    <div class="font-bold text-gray-900">Pak Hariyadi</div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ !empty($directorApproval['approved']) ? 'bg-gray-100 text-gray-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ !empty($directorApproval['approved']) ? 'Disahkan' : 'Menunggu' }}
                                </span>
                            </div>
                            <div class="text-[11px] text-gray-500 italic">"{{ $directorApproval['notes'] ?? 'Otorisasi finansial & validasi kontrak.' }}"</div>
                            <div class="text-right pt-1">
                                <button type="button" @click="openApproveModal('director')" class="text-xs font-bold text-ipnet-red hover:underline cursor-pointer">
                                    {{ empty($directorApproval['approved']) ? '+ Beri Approval Pak Hariyadi' : 'Ubah Catatan' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB B: IN PROGRESS (Delivery ke PMO Rizki & Monitoring Engineer) --}}
                <div x-show="activeStageTab === 'in_progress'" x-cloak class="p-4 rounded-xl border border-gray-200 bg-gray-50/70 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-200 pb-2.5">
                        <div>
                            <div class="text-xs font-bold text-gray-900">Fase Delivery ke PMO & Tim Lapangan</div>
                            <div class="text-[11px] text-gray-500">Sales telah menyerahkan proyek ke PMO. Alokasi engineer diatur oleh PMO dan dipantau Sales di bawah ini.</div>
                        </div>
                        <button type="button" @click="isHandoverModalOpen = true" class="text-xs font-bold text-ipnet-red hover:underline cursor-pointer">
                            {{ $project->pm ? 'Ganti PMO (' . $project->pm->name . ')' : '+ Handover ke PMO (Rizki)' }}
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        {{-- PMO Info --}}
                        <div class="p-3.5 rounded-lg bg-white border border-gray-200 space-y-1.5">
                            <div class="text-[10px] font-bold text-gray-400 uppercase">PROJECT MANAGER (PMO)</div>
                            <div class="font-bold text-gray-900 text-sm">{{ $project->pm ? $project->pm->name : 'Belum Ada PM (Tunjuk Rizki)' }}</div>
                            <div class="text-[11px] text-gray-500">{{ $project->pm ? $project->pm->email : 'Sales silakan serahterimakan ke Rizki' }}</div>
                        </div>

                        {{-- Engineer Monitoring Info (Read-Only) --}}
                        <div class="p-3.5 rounded-lg bg-white border border-gray-200 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-gray-400 uppercase">TIM ENGINEER (READ-ONLY)</span>
                                <span class="text-[10px] font-bold text-gray-600">{{ $uniqueEngineers->count() }} Orang</span>
                            </div>
                            @if($uniqueEngineers->count() > 0)
                                <div class="space-y-1 pt-1">
                                    @foreach($uniqueEngineers as $eng)
                                        <div class="flex items-center justify-between text-[11.5px]">
                                            <span class="font-semibold text-gray-800">{{ $eng->name }}</span>
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

                {{-- TAB C: OPPORTUNITY / CRM --}}
                <div x-show="activeStageTab === 'opportunity'" x-cloak class="p-4 rounded-xl border border-gray-200 bg-gray-50/70 text-xs space-y-2">
                    <div class="font-bold text-gray-900">Pipeline Komersial Sales</div>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div>Stage: <strong class="text-gray-900">{{ $project->sales_stage ?: 'Proposal' }}</strong></div>
                        <div>Win Rate: <strong class="text-gray-900">{{ $project->win_probability ?: 25 }}%</strong></div>
                        <div>Closing: <strong class="text-gray-900">{{ $project->expected_closing_date ? \Carbon\Carbon::parse($project->expected_closing_date)->format('d M Y') : '30 Sep 2026' }}</strong></div>
                    </div>
                </div>

                {{-- TAB D: PENDING --}}
                <div x-show="activeStageTab === 'pending'" x-cloak class="p-4 rounded-xl border border-gray-200 bg-gray-50/70 text-xs space-y-2">
                    <div class="font-bold text-gray-900">Proyek Ditangguhkan (Pending)</div>
                    <p class="text-gray-500">Pengerjaan proyek sedang di-pause menunggu konfirmasi akses site atau barang distributor.</p>
                </div>

                {{-- TAB E: COMPLETED --}}
                <div x-show="activeStageTab === 'completed'" x-cloak class="p-4 rounded-xl border border-gray-200 bg-gray-50/70 text-xs space-y-2">
                    <div class="font-bold text-gray-900">Proyek Selesai & BAST Terbit</div>
                    <p class="text-gray-500">Seluruh milestone selesai dan berkas Berita Acara Serah Terima telah disahkan.</p>
                </div>

                {{-- 5. Client Info Section (Matching Screenshot) --}}
                <div class="pt-4 border-t border-gray-100 space-y-3">
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

                {{-- 6. Milestone Section (Matching Screenshot) --}}
                <div class="pt-6 border-t border-gray-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-gray-900">Milestone</h3>
                        <div class="text-xs text-gray-400 font-normal">
                            Complete ({{ $project->tasks->where('status', 'Completed')->count() }}/{{ $project->tasks->count() }})
                        </div>
                    </div>

                    {{-- Daftar Task Jika Ada --}}
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

                {{-- 7. Attachments Section (Matching Screenshot) --}}
                <div class="pt-6 border-t border-gray-100 space-y-3">
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

                    {{-- List Dokumen Jika Ada --}}
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

            {{-- RIGHT SECTION (1 of 4 columns, Matching Screenshot) --}}
            <div class="p-6 lg:p-8 space-y-6 bg-white">
                
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
                                <strong class="font-medium text-gray-900">{{ $project->creator ? $project->creator->name : 'Nabylla Berlianita' }}</strong> created this project
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
                                    Tanggung jawab delivery dialihkan
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

    {{-- ======================================================== --}}
    {{-- MODALS: ALL PROPERLY CENTERED & TELEPORTED TO BODY       --}}
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
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold bg-gray-900 hover:bg-gray-800 text-white cursor-pointer transition">
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
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold bg-[#A2623D] hover:bg-[#884D2B] text-white cursor-pointer transition">
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
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold bg-gray-900 hover:bg-gray-800 text-white cursor-pointer transition">
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
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold bg-gray-900 hover:bg-gray-800 text-white cursor-pointer transition">
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
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold bg-gray-900 hover:bg-gray-800 text-white cursor-pointer transition">
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
