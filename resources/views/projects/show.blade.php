@extends('layouts.app')

@section('title', $project->name . ' - Detail Proyek')

@push('styles')
<style>
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 14px;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.03);
    }
    .btn-action-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        transition: all 0.15s ease;
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
    .stage-pill-btn {
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 600;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .stage-pill-btn.active {
        background-color: #0F172A;
        color: #FFFFFF;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .stage-pill-btn:not(.active) {
        background-color: transparent;
        color: #475569;
    }
    .stage-pill-btn:not(.active):hover {
        background-color: #F1F5F9;
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

    // Koleksi engineer pelaksana yang ditugaskan oleh PMO/Lead pada tasks proyek
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
        $initialStage = 'in_progress';
    }

    // Ambil daftar user PMO (Rizki, Kuncoro, dsb)
    $pmoUsers = \App\Models\User::role(['PMO', 'Project Manager'])->orWhere('name', 'like', '%Rizki%')->orderBy('name')->get();
@endphp

<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans text-slate-800" 
     x-data="projectDetailPage('{{ $initialStage }}', '{{ $currentStatus }}')">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Project'])
        
        <div class="p-5 sm:p-7 max-w-7xl mx-auto space-y-6">
            
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
            <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <a href="{{ route('sales.pipeline.index') }}" class="hover:text-slate-800 transition">Project</a>
                <span>/</span>
                <span class="text-slate-900 font-semibold">{{ $project->name }}</span>
            </div>

            {{-- 2. TOP HEADER & METADATA BAR --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-slate-200">
                <div class="flex items-center gap-3 flex-wrap">
                    {{-- Status Badge --}}
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold
                        @if(in_array($currentStatus, ['Draft', 'Planning'])) bg-amber-100 text-amber-900 border border-amber-200
                        @elseif($currentStatus === 'Opportunity') bg-sky-100 text-sky-900 border border-sky-200
                        @elseif(in_array($currentStatus, ['In Progress', 'On Progress'])) bg-slate-900 text-white
                        @elseif($currentStatus === 'Pending') bg-orange-100 text-orange-900 border border-orange-200
                        @elseif($currentStatus === 'Completed') bg-slate-800 text-white
                        @else bg-slate-100 text-slate-800 border border-slate-200 @endif">
                        <span>{{ $currentStatus }}</span>
                    </div>

                    {{-- Division / Team Badge --}}
                    <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        <span class="text-slate-400 font-medium">Divisi:</span>
                        <span class="font-bold text-slate-800">{{ $project->division ? $project->division->name : 'IPNET 01' }}</span>
                    </div>

                    {{-- PMO Delivery Status Badge --}}
                    <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        <span class="text-slate-400 font-medium">PMO PIC:</span>
                        @if($project->pm)
                            <span class="font-bold text-slate-900">{{ $project->pm->name }}</span>
                        @else
                            <span class="font-semibold text-amber-700">Belum diserahterimakan ke PMO</span>
                        @endif
                    </div>
                </div>

                <div class="text-xs text-slate-500 font-medium">
                    Dibuat oleh <strong class="text-slate-700">{{ $project->creator ? $project->creator->name : ($project->sales_name ?: 'Sales Team') }}</strong> • 
                    {{ \Carbon\Carbon::parse($project->created_at)->format('d M Y H:i') }}
                </div>
            </div>

            {{-- 3. TITLE & ACTION BAR --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $project->name }}</h1>
                    <p class="text-xs text-slate-500 mt-1">{{ $project->description ?: 'Pengadaan dan implementasi perangkat jaringan untuk klien.' }}</p>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-2.5 shrink-0">
                    {{-- Tombol Handover ke PMO untuk Sales --}}
                    <button type="button" 
                            @click="isHandoverModalOpen = true"
                            class="btn-action-pill btn-ipnet-primary cursor-pointer shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        <span>{{ $project->pm ? 'Ubah PMO (' . $project->pm->name . ')' : 'Handover ke PMO (Rizki)' }}</span>
                    </button>

                    {{-- Toggle Pending --}}
                    <form action="{{ route('projects.stage_update', $project->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="{{ $currentStatus === 'Pending' ? 'In Progress' : 'Pending' }}">
                        <button type="submit" class="btn-action-pill border border-slate-300 text-slate-700 hover:bg-slate-100 cursor-pointer">
                            <span>{{ $currentStatus === 'Pending' ? 'Resume Proyek' : 'Pending' }}</span>
                        </button>
                    </form>

                    {{-- Delete Proyek --}}
                    <button type="button" @click="confirmDeleteProject()" class="btn-action-pill border border-slate-200 text-slate-500 hover:text-red-700 hover:bg-red-50 cursor-pointer">
                        <span>Hapus</span>
                    </button>
                </div>
            </div>

            {{-- 4. CLEAN LIFECYCLE STAGE TABS (SEGMENTED CONTROL) --}}
            <div class="bg-white p-2 rounded-2xl border border-slate-200 shadow-2xs">
                <div class="flex items-center justify-between px-3 py-1 mb-2 border-b border-slate-100 flex-wrap gap-2 text-xs">
                    <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">Lifecycle Proyek</span>
                    <span class="text-slate-400">Pilih tahapan di bawah untuk melihat rincian operasional:</span>
                </div>

                <div class="flex items-center gap-1.5 overflow-x-auto p-1 bg-slate-100/80 rounded-xl">
                    <button type="button" 
                            @click="activeStageTab = 'draft'"
                            :class="activeStageTab === 'draft' ? 'active' : ''"
                            class="stage-pill-btn cursor-pointer">
                        <span>1. Draft (Persetujuan)</span>
                    </button>

                    <button type="button" 
                            @click="activeStageTab = 'opportunity'"
                            :class="activeStageTab === 'opportunity' ? 'active' : ''"
                            class="stage-pill-btn cursor-pointer">
                        <span>2. Opportunity (Pipeline)</span>
                    </button>

                    <button type="button" 
                            @click="activeStageTab = 'in_progress'"
                            :class="activeStageTab === 'in_progress' ? 'active' : ''"
                            class="stage-pill-btn cursor-pointer">
                        <span>3. In Progress (Delivery ke PMO)</span>
                    </button>

                    <button type="button" 
                            @click="activeStageTab = 'pending'"
                            :class="activeStageTab === 'pending' ? 'active' : ''"
                            class="stage-pill-btn cursor-pointer">
                        <span>4. Pending</span>
                    </button>

                    <button type="button" 
                            @click="activeStageTab = 'completed'"
                            :class="activeStageTab === 'completed' ? 'active' : ''"
                            class="stage-pill-btn cursor-pointer">
                        <span>5. Completed (BAST)</span>
                    </button>
                </div>
            </div>

            {{-- 5. CONTEXTUAL STAGE PANELS --}}

            {{-- PANEL 1: IN PROGRESS (Fokus Utama: Delivery ke PMO & Monitoring Engineer) --}}
            <div x-show="activeStageTab === 'in_progress'" x-cloak class="space-y-4">
                <div class="ipnet-card p-6 space-y-6">
                    
                    {{-- Header Panel --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Fase Delivery & Implementasi Lapangan (In Progress)</h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Proyek sedang berjalan. Sales menyerahkan delivery ke <strong>PMO / Project Manager (Rizki)</strong>. Alokasi personel teknis dikelola oleh PMO dan dipantau oleh Sales secara langsung.
                            </p>
                        </div>

                        <div class="shrink-0">
                            <button type="button" 
                                    @click="isHandoverModalOpen = true"
                                    class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-900 text-white hover:bg-slate-800 transition cursor-pointer">
                                {{ $project->pm ? 'Kelola PMO / Handover' : '+ Serahkan ke PMO (Rizki)' }}
                            </button>
                        </div>
                    </div>

                    {{-- 2 Sub-Cards: PM Card & Engineer Monitoring Table --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                        
                        {{-- Card A: PMO / Project Manager --}}
                        <div class="ipnet-card p-5 bg-slate-50/60 border border-slate-200 flex flex-col justify-between gap-4">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">PROJECT MANAGER (PMO)</span>
                                    @if($project->pm)
                                        <span class="px-2.5 py-0.5 rounded text-[10.5px] font-bold bg-slate-200 text-slate-800">
                                            Aktif Bertugas
                                        </span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded text-[10.5px] font-bold bg-amber-100 text-amber-800">
                                            Belum Di-Assign
                                        </span>
                                    @endif
                                </div>

                                @if($project->pm)
                                    <div class="flex items-center gap-3 pt-1">
                                        <div class="w-11 h-11 rounded-xl bg-slate-800 text-white flex items-center justify-center font-bold text-sm">
                                            {{ strtoupper(substr($project->pm->name, 0, 2)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-bold text-slate-900 truncate">{{ $project->pm->name }}</div>
                                            <div class="text-xs text-slate-500 truncate">{{ $project->pm->email }}</div>
                                            <div class="text-[11px] text-slate-600 font-medium mt-0.5">Penanggung Jawab Delivery Teknis</div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-600 bg-white p-3 rounded-lg border border-slate-200/80 leading-relaxed">
                                        PMO bertindak sebagai penanggung jawab utama jadwal, koordinasi lapangan, dan penugasan personel teknis engineer.
                                    </p>
                                @else
                                    <div class="p-4 rounded-xl bg-white border border-slate-200 text-xs text-slate-700 space-y-2">
                                        <div class="font-bold text-slate-900">Belum Ada PMO yang Ditugaskan</div>
                                        <p class="text-slate-500 text-[11.5px] leading-relaxed">
                                            Sales perlu melakukan delivery / serah terima proyek ini ke tim PMO (misal: <strong>Rizki</strong>) agar proses alokasi engineer dan jadwal implementasi dapat segera dijalankan.
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <div class="pt-2 border-t border-slate-200">
                                <button type="button" 
                                        @click="isHandoverModalOpen = true"
                                        class="w-full py-2 rounded-lg text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 transition cursor-pointer text-center">
                                    {{ $project->pm ? 'Ubah Penugasan PMO' : '+ Handover ke PMO (Rizki)' }}
                                </button>
                            </div>
                        </div>

                        {{-- Card B: Tim Engineer Pelaksana (Sales Monitoring - Read Only) --}}
                        <div class="lg:col-span-2 ipnet-card p-5 border border-slate-200 flex flex-col justify-between gap-4">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">Tim Engineer Pelaksana</h3>
                                        <p class="text-xs text-slate-500">Personel teknis yang dialokasikan oleh PMO pada milestone pengerjaan (Monitoring Real-Time):</p>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $uniqueEngineers->count() }} Engineer Bertugas
                                    </span>
                                </div>

                                {{-- Daftar Engineer Jika Sudah Dialokasikan PMO --}}
                                @if($uniqueEngineers->count() > 0)
                                    <div class="divide-y divide-slate-100">
                                        @foreach($uniqueEngineers as $eng)
                                            @php
                                                $engTasks = $project->tasks->where('engineer_id', $eng->id);
                                                $roleName = $eng->roles->pluck('name')->first() ?? 'Network Engineer';
                                            @endphp
                                            <div class="py-3 flex items-center justify-between gap-3 text-xs">
                                                <div class="flex items-center gap-3 min-w-0">
                                                    <div class="w-9 h-9 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center font-bold shrink-0">
                                                        {{ strtoupper(substr($eng->name, 0, 2)) }}
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="font-bold text-slate-900 truncate">{{ $eng->name }}</div>
                                                        <div class="text-slate-500 text-[11px] truncate">{{ $roleName }}</div>
                                                    </div>
                                                </div>

                                                <div class="text-right shrink-0">
                                                    <div class="font-medium text-slate-700">
                                                        {{ $engTasks->count() }} Milestone Task
                                                    </div>
                                                    <div class="text-[10.5px] text-slate-400">Dialokasikan oleh PMO</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    {{-- Info Bersih Jika Belum Ada Engineer --}}
                                    <div class="p-5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1.5 text-center">
                                        <div class="font-bold text-slate-800">Menunggu Alokasi Engineer oleh PMO</div>
                                        <p class="text-slate-500 text-[11.5px] max-w-lg mx-auto">
                                            Saat ini belum ada engineer yang ditugaskan oleh PMO. Setelah Project Manager (Rizki) mengalokasikan personel pada milestone pekerjaan, daftar engineer yang bekerja akan langsung terupdate di sini.
                                        </p>
                                    </div>
                                @endif
                            </div>

                            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-[11.5px] text-slate-500 flex items-center justify-between">
                                <span>Alokasi & pembagian tugas engineer diatur sepenuhnya oleh Tim PMO & Lead Engineer.</span>
                                <span class="font-semibold text-slate-700">Read-Only Monitoring</span>
                            </div>
                        </div>

                    </div>

                    {{-- Progres Deliverable --}}
                    @php
                        $totalTasksCount = $project->tasks->count();
                        $completedTasksCount = $project->tasks->where('status', 'Completed')->count();
                        $calculatedProgress = $totalTasksCount > 0 ? round(($completedTasksCount / $totalTasksCount) * 100) : ($project->progress ?: 0);
                    @endphp
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs">
                        <div class="space-y-1.5 flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-800">Progres Deliverables Proyek:</span>
                                <span class="font-bold text-slate-900">{{ $calculatedProgress }}% ({{ $completedTasksCount }}/{{ $totalTasksCount }} Milestone Selesai)</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-slate-900 h-2 rounded-full transition-all duration-300" style="width: {{ $calculatedProgress }}%"></div>
                            </div>
                        </div>

                        <div class="text-slate-500 text-right shrink-0 border-l border-slate-200 pl-4">
                            <div>Target Selesai: <strong class="text-slate-800">{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : 'Sesuai SOW' }}</strong></div>
                            <div>Lokasi: <strong class="text-slate-800">{{ $project->location ?: 'On-Site Client' }}</strong></div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- PANEL 2: DRAFT (Persetujuan Head Pak Susanto & Direktur Pak Hariyadi) --}}
            <div x-show="activeStageTab === 'draft'" x-cloak class="space-y-4">
                <div class="ipnet-card p-6 space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Alur Persetujuan Draft Proyek</h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Proyek baru membutuhkan persetujuan berjenjang dari <strong>Head Divisi (Pak Susanto)</strong> dan <strong>Direktur (Pak Hariyadi)</strong> sebelum diserahkan ke PMO.
                            </p>
                        </div>

                        <div>
                            @if(!empty($headApproval['approved']) && !empty($directorApproval['approved']))
                                <span class="px-3 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-800 border border-slate-300">
                                    Disetujui Lengkap
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-900 border border-amber-200">
                                    Menunggu Persetujuan
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- 2 Approval Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        {{-- Reviewer 1: Pak Susanto (Head Divisi) --}}
                        <div class="p-5 rounded-xl bg-slate-50/70 border border-slate-200 space-y-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wider">Reviewer 1 • Head Divisi</div>
                                    <h3 class="text-sm font-bold text-slate-900 mt-0.5">Pak Susanto Djaya</h3>
                                    <p class="text-xs text-slate-500">Kelayakan Teknis & Alokasi Resource</p>
                                </div>
                                <div>
                                    @if(!empty($headApproval['approved']))
                                        <span class="px-2.5 py-1 rounded text-xs font-bold bg-white text-slate-900 border border-slate-300">
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            Menunggu Review
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-3 rounded-lg bg-white border border-slate-200 text-xs text-slate-600 space-y-1">
                                <div class="font-semibold text-slate-700">Catatan Persetujuan:</div>
                                <div class="italic text-slate-600">"{{ $headApproval['notes'] ?? 'Menunggu review teknis.' }}"</div>
                                @if(!empty($headApproval['date']))
                                    <div class="text-[10px] text-slate-400 pt-1">Disahkan: {{ $headApproval['date'] }}</div>
                                @endif
                            </div>

                            <div class="text-right">
                                <button type="button" 
                                        @click="openApproveModal('head')" 
                                        class="px-4 py-2 rounded-lg text-xs font-bold btn-ipnet-primary cursor-pointer">
                                    {{ empty($headApproval['approved']) ? 'Beri Persetujuan (Pak Susanto)' : 'Perbarui Catatan' }}
                                </button>
                            </div>
                        </div>

                        {{-- Reviewer 2: Pak Hariyadi (Direktur) --}}
                        <div class="p-5 rounded-xl bg-slate-50/70 border border-slate-200 space-y-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-[10.5px] font-bold text-slate-500 uppercase tracking-wider">Reviewer 2 • Direktur</div>
                                    <h3 class="text-sm font-bold text-slate-900 mt-0.5">Pak Hariyadi</h3>
                                    <p class="text-xs text-slate-500">Otorisasi Anggaran & Kontrak Resmi</p>
                                </div>
                                <div>
                                    @if(!empty($directorApproval['approved']))
                                        <span class="px-2.5 py-1 rounded text-xs font-bold bg-white text-slate-900 border border-slate-300">
                                            Disahkan
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            Menunggu Otorisasi
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="p-3 rounded-lg bg-white border border-slate-200 text-xs text-slate-600 space-y-1">
                                <div class="font-semibold text-slate-700">Catatan Otorisasi:</div>
                                <div class="italic text-slate-600">"{{ $directorApproval['notes'] ?? 'Menunggu review anggaran.' }}"</div>
                                @if(!empty($directorApproval['date']))
                                    <div class="text-[10px] text-slate-400 pt-1">Disahkan: {{ $directorApproval['date'] }}</div>
                                @endif
                            </div>

                            <div class="text-right">
                                <button type="button" 
                                        @click="openApproveModal('director')" 
                                        class="px-4 py-2 rounded-lg text-xs font-bold btn-ipnet-primary cursor-pointer">
                                    {{ empty($directorApproval['approved']) ? 'Beri Otorisasi (Pak Hariyadi)' : 'Perbarui Catatan' }}
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- PANEL 3: OPPORTUNITY (Pipeline Sales CRM) --}}
            <div x-show="activeStageTab === 'opportunity'" x-cloak class="space-y-4">
                <div class="ipnet-card p-6 space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Pipeline Sales CRM & Opportunity</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Data prospek komersial, estimasi nilai kontrak, dan closing deal.</p>
                        </div>
                        <span class="px-3 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            Stage: {{ $project->sales_stage ?: 'Qualification' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="text-slate-400 font-bold uppercase text-[10.5px]">WIN PROBABILITY</div>
                            <div class="text-xl font-bold text-slate-900 mt-1">{{ $project->win_probability ?: 25 }}%</div>
                            <div class="text-slate-500 text-[11px] mt-0.5">Peluang Closing</div>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="text-slate-400 font-bold uppercase text-[10.5px]">ESTIMASI KONTRAK</div>
                            <div class="text-xl font-bold text-slate-900 mt-1">
                                Rp {{ number_format($project->contract_value ?: 300000000, 0, ',', '.') }}
                            </div>
                            <div class="text-slate-500 text-[11px] mt-0.5">Nilai Deal</div>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="text-slate-400 font-bold uppercase text-[10.5px]">SALES PIC</div>
                            <div class="text-sm font-bold text-slate-900 mt-1">{{ $project->sales_name ?: 'Sales Team' }}</div>
                            <div class="text-slate-500 text-[11px] mt-0.5">Account Manager</div>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="text-slate-400 font-bold uppercase text-[10.5px]">TARGET CLOSING</div>
                            <div class="text-sm font-bold text-slate-900 mt-1">
                                {{ $project->expected_closing_date ? \Carbon\Carbon::parse($project->expected_closing_date)->format('d M Y') : '30 Sep 2026' }}
                            </div>
                            <div class="text-slate-500 text-[11px] mt-0.5">Tenggat Waktu</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PANEL 4: PENDING (Penangguhan) --}}
            <div x-show="activeStageTab === 'pending'" x-cloak class="space-y-4">
                <div class="ipnet-card p-6 space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base font-bold text-slate-900">Proyek Ditangguhkan (Pending / On-Hold)</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Status sementara proyek jika terdapat kendala site access dari klien atau ketersediaan unit distributor.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200 text-xs text-amber-900 space-y-2">
                        <div class="font-bold">Informasi Penangguhan Proyek</div>
                        <p class="text-amber-800 leading-relaxed">
                            Proyek saat ini berada di status pending. Jika kendala telah diselesaikan, Anda dapat langsung mengklik tombol di bawah untuk melanjutkan aktivitas delivery ke PMO.
                        </p>
                    </div>

                    <form action="{{ route('projects.stage_update', $project->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status" value="In Progress">
                        <button type="submit" class="px-4 py-2 rounded-lg text-xs font-bold btn-ipnet-primary cursor-pointer">
                            Lanjutkan Proyek (Resume to In Progress)
                        </button>
                    </form>
                </div>
            </div>

            {{-- PANEL 5: COMPLETED (Selesai & BAST) --}}
            <div x-show="activeStageTab === 'completed'" x-cloak class="space-y-4">
                <div class="ipnet-card p-6 space-y-4">
                    <div class="border-b border-slate-100 pb-3">
                        <h2 class="text-base font-bold text-slate-900">Proyek Selesai & Serah Terima (Completed)</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Seluruh deliverable telah terselesaikan 100%, pengujian UAT disetujui klien, dan BAST diterbitkan.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="font-bold text-slate-500 uppercase text-[10.5px]">STATUS BAST</div>
                            <div class="text-sm font-bold text-slate-900 mt-1">Ditandatangani</div>
                            <div class="text-slate-400 text-[11px] mt-0.5">Berita Acara Serah Terima</div>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="font-bold text-slate-500 uppercase text-[10.5px]">HANDOVER OPERASIONAL</div>
                            <div class="text-sm font-bold text-slate-900 mt-1">Managed Service</div>
                            <div class="text-slate-400 text-[11px] mt-0.5">Monitoring Berjalan</div>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="font-bold text-slate-500 uppercase text-[10.5px]">MASA GARANSI</div>
                            <div class="text-sm font-bold text-slate-900 mt-1">12 Bulan Resmi</div>
                            <div class="text-slate-400 text-[11px] mt-0.5">Garansi Perangkat</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 6. PROJECT ESTIMATION & TIMELINE BANNER --}}
            <div class="ipnet-card p-6 bg-slate-50/70 border border-slate-200">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 flex-1 text-xs">
                        <div>
                            <div class="font-bold text-slate-400 uppercase tracking-wider text-[10.5px]">NILAI ESTIMASI PROYEK</div>
                            <div class="text-xl font-bold text-slate-900 mt-1">
                                Rp {{ number_format($project->contract_value ?: 300000000, 0, ',', '.') }}
                            </div>
                        </div>

                        <div>
                            <div class="font-bold text-slate-400 uppercase tracking-wider text-[10.5px]">PROJECT START</div>
                            <div class="text-sm font-bold text-slate-900 mt-1.5">
                                {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : '24 Aug 2026' }}
                            </div>
                        </div>

                        <div>
                            <div class="font-bold text-slate-400 uppercase tracking-wider text-[10.5px]">PROJECT END</div>
                            <div class="text-sm font-bold text-slate-900 mt-1.5">
                                {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0">
                        <button type="button" 
                                @click="isEditMetaModalOpen = true" 
                                class="px-3 py-2 rounded-lg bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 text-xs font-semibold shadow-2xs transition cursor-pointer">
                            Edit Estimasi & Tanggal
                        </button>
                    </div>
                </div>
            </div>

            {{-- 7. 2-COLUMN MAIN DETAILS --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- LEFT COLUMN (2 spans) --}}
                <div class="lg:col-span-2 space-y-6">
                    
                    {{-- Client Info --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900">Informasi Klien</h3>
                            <span class="text-xs text-slate-400 font-medium">Customer Details</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                            <div>
                                <div class="font-bold text-slate-400 uppercase tracking-wider text-[10.5px]">NAMA KLIEN</div>
                                <div class="font-bold text-slate-900 mt-1">{{ $project->client ?: 'Sarana Kreasi Teknoart' }}</div>
                            </div>
                            <div>
                                <div class="font-bold text-slate-400 uppercase tracking-wider text-[10.5px]">EMAIL PIC</div>
                                <div class="font-bold text-slate-900 mt-1 truncate">{{ $project->customer_pic_finance ?: ($project->customer_pic_technical ?: 'sales@saranateknoart.com') }}</div>
                            </div>
                            <div>
                                <div class="font-bold text-slate-400 uppercase tracking-wider text-[10.5px]">KONTAK PIC</div>
                                <div class="font-bold text-slate-900 mt-1">{{ $project->customer_pic_business ?: '081299771333' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Milestone Section --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900">Milestone Pekerjaan</h3>
                            <div class="text-xs text-slate-500 font-medium">
                                Selesai ({{ $project->tasks->where('status', 'Completed')->count() }}/{{ $project->tasks->count() }})
                            </div>
                        </div>

                        <div class="space-y-2">
                            @forelse($project->tasks as $task)
                                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 flex items-center justify-between text-xs gap-3">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-2 h-2 rounded-full {{ $task->status === 'Completed' ? 'bg-slate-900' : 'bg-slate-300' }} shrink-0"></div>
                                        <div class="min-w-0">
                                            <span class="font-bold text-slate-900 truncate block">{{ $task->title ?? $task->name }}</span>
                                            @if($task->engineer)
                                                <div class="text-[11px] text-slate-500 font-medium">Engineer: {{ $task->engineer->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded text-[10.5px] font-bold shrink-0 bg-white text-slate-700 border border-slate-200">
                                        {{ $task->status }}
                                    </span>
                                </div>
                            @empty
                                <div class="py-6 text-center text-xs text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                    Belum ada milestone pekerjaan yang terdaftar.
                                </div>
                            @endforelse
                        </div>

                        <div>
                            <button type="button" 
                                    @click="isAddMilestoneModalOpen = true" 
                                    class="text-xs font-bold text-slate-700 hover:text-slate-900 flex items-center gap-1 cursor-pointer">
                                <span>+ Tambah Milestone</span>
                            </button>
                        </div>
                    </div>

                    {{-- Attachments Section --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900">Lampiran & Dokumen Pendukung</h3>
                            <span class="text-xs text-slate-400 font-medium">File Repository</span>
                        </div>
                        
                        <div class="space-y-2">
                            @if($project->relationLoaded('projectDocuments') && $project->projectDocuments->count() > 0)
                                @foreach($project->projectDocuments as $doc)
                                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="font-bold text-slate-900 truncate">{{ $doc->document_title ?? ($doc->document_name ?? 'Document') }}</span>
                                        </div>
                                        @if($doc->file_path)
                                            <a href="{{ route('projects.documents.download', [$project->id, $doc->id]) }}" class="text-slate-700 font-bold hover:underline shrink-0">
                                                Download
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="py-4 text-center text-xs text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                                    Belum ada berkas lampiran yang diunggah.
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-3 pt-1 text-xs">
                            <button type="button" @click="isUploadDocModalOpen = true" class="font-bold text-slate-700 hover:text-slate-900 cursor-pointer">
                                + Upload Lampiran
                            </button>
                            <span class="text-slate-300">•</span>
                            <button type="button" @click="isUploadDocModalOpen = true" class="font-bold text-slate-700 hover:text-slate-900 cursor-pointer">
                                + Upload Dokumen COGS
                            </button>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN (1 span) --}}
                <div class="space-y-6">
                    
                    {{-- Ringkasan Status --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <h3 class="text-sm font-bold text-slate-900">Ringkasan Status Proyek</h3>
                        <div class="flex items-center justify-between text-xs">
                            <span class="px-2.5 py-1 rounded bg-slate-100 text-slate-800 font-bold">
                                {{ $currentStatus }}
                            </span>
                            <span class="text-slate-500 font-medium">
                                Progres {{ $project->progress ?: 0 }}%
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-slate-800 h-2 rounded-full" style="width: {{ $project->progress ?: 0 }}%"></div>
                        </div>
                    </div>

                    {{-- Riwayat Aktivitas --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <h3 class="text-sm font-bold text-slate-900">Aktivitas Proyek</h3>
                        
                        <div class="space-y-3 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                                <div class="font-bold text-slate-900">
                                    {{ $project->creator ? $project->creator->name : 'Sales Team' }} membuat proyek ini
                                </div>
                                <div class="text-[10.5px] text-slate-400">
                                    {{ \Carbon\Carbon::parse($project->created_at)->format('d M Y H:i') }}
                                </div>
                            </div>

                            @if($project->pm)
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                                    <div class="font-bold text-slate-900">Diserahterimakan ke PMO</div>
                                    <div class="text-[11px] text-slate-600">PM Ditunjuk: <strong>{{ $project->pm->name }}</strong></div>
                                </div>
                            @endif

                            @if(!empty($headApproval['approved']))
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                                    <div class="font-bold text-slate-900">Persetujuan Head Divisi (Pak Susanto)</div>
                                    <div class="text-[10.5px] text-slate-500">{{ $headApproval['date'] ?? 'Disetujui' }}</div>
                                </div>
                            @endif

                            @if(!empty($directorApproval['approved']))
                                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                                    <div class="font-bold text-slate-900">Otorisasi Direktur (Pak Hariyadi)</div>
                                    <div class="text-[10.5px] text-slate-500">{{ $directorApproval['date'] ?? 'Disahkan' }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODALS: CENTERED & CLEAN                                --}}
    {{-- ======================================================== --}}

    {{-- 1. HANDOVER KE PMO MODAL (TELEPORTED & CENTERED) --}}
    <template x-teleport="body">
        <div x-show="isHandoverModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isHandoverModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-5 m-auto">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Handover Proyek ke PMO</h3>
                        <p class="text-xs text-slate-500">Pilih Project Manager untuk memimpin delivery & alokasi teknis</p>
                    </div>
                    <button type="button" @click="isHandoverModalOpen = false" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.assign', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="role_type" value="pm">

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 font-normal leading-relaxed">
                        Sales menyerahkan tanggung jawab pelaksanaan pengerjaan ke Tim PMO. Project Manager yang dipilih akan mengatur jadwal dan mengalokasikan tim engineer di lapangan.
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[11px]">PILIH PROJECT MANAGER (PMO)</label>
                        <select name="user_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-slate-400 cursor-pointer bg-white">
                            <option value="">-- Pilih PMO / Project Manager --</option>
                            @foreach($pmoUsers as $pmo)
                                <option value="{{ $pmo->id }}" {{ ($project->pm_id == $pmo->id || (empty($project->pm_id) && str_contains(strtolower($pmo->name), 'rizki'))) ? 'selected' : '' }}>
                                    {{ $pmo->name }} (PMO / Project Manager)
                                </option>
                            @endforeach
                            @php
                                $otherUsers = ($allUsers ?? \App\Models\User::orderBy('name')->get())->whereNotIn('id', $pmoUsers->pluck('id'));
                            @endphp
                            @foreach($otherUsers as $ou)
                                <option value="{{ $ou->id }}" {{ $project->pm_id == $ou->id ? 'selected' : '' }}>
                                    {{ $ou->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="isHandoverModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer">
                            Konfirmasi Handover ke PMO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- 2. DRAFT APPROVAL MODAL (TELEPORTED & CENTERED) --}}
    <template x-teleport="body">
        <div x-show="isApproveModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isApproveModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-5 m-auto">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Persetujuan Draft Proyek</h3>
                        <p class="text-xs text-slate-500" x-text="approveRole === 'head' ? 'Persetujuan Head Divisi (Pak Susanto)' : 'Otorisasi Direktur (Pak Hariyadi)'"></p>
                    </div>
                    <button type="button" @click="isApproveModalOpen = false" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.approve_draft', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="approval_role" :value="approveRole">

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-xs font-normal leading-relaxed">
                        <span x-show="approveRole === 'head'">
                            Memberikan persetujuan teknis dan alokasi resource atas nama <strong>Pak Susanto Djaya (Head Divisi)</strong>.
                        </span>
                        <span x-show="approveRole === 'director'">
                            Memberikan otorisasi anggaran finansial dan kontrak atas nama <strong>Pak Hariyadi (Direktur)</strong>.
                        </span>
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[11px]">CATATAN / INSTRUKSI TAMBAHAN</label>
                        <textarea name="notes" rows="3" 
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-slate-400"
                                  :placeholder="approveRole === 'head' ? 'Contoh: Disetujui untuk dialokasikan resource.' : 'Contoh: Disetujui untuk kontrak resmi.'"></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="auto_advance" id="auto_advance" value="1" checked class="rounded text-slate-900">
                        <label for="auto_advance" class="text-slate-600 font-normal">Majukan ke tahap Opportunity setelah kedua persetujuan lengkap</label>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="isApproveModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer">
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
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isAddMilestoneModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-5 m-auto">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">+ Tambah Milestone Proyek</h3>
                    <button type="button" @click="isAddMilestoneModalOpen = false" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                    <input type="hidden" name="status" value="Pending">

                    <div>
                        <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[11px]">NAMA MILESTONE / DELIVERABLE</label>
                        <input type="text" name="title" required placeholder="Contoh: Pengiriman Aruba AP-505 & APC" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-slate-400">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[11px]">PRIORITAS</label>
                            <select name="priority" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-slate-400 bg-white cursor-pointer">
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[11px]">DEADLINE</label>
                            <input type="date" name="deadline" value="{{ date('Y-m-d', strtotime('+7 days')) }}" 
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-slate-400">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="isAddMilestoneModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer">
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
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isEditMetaModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-5 m-auto">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Edit Estimasi & Jadwal Proyek</h3>
                    <button type="button" @click="isEditMetaModalOpen = false" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.meta_update', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <div>
                        <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[11px]">NILAI ESTIMASI KONTRAK (RP)</label>
                        <input type="number" name="contract_value" value="{{ $project->contract_value ?: 300000000 }}" required 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-slate-400">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[11px]">PROJECT START</label>
                            <input type="date" name="start_date" value="{{ $project->start_date ? $project->start_date->format('Y-m-d') : date('Y-m-d') }}" 
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-slate-400">
                        </div>
                        <div>
                            <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[11px]">PROJECT END</label>
                            <input type="date" name="deadline" value="{{ $project->deadline ? $project->deadline->format('Y-m-d') : '' }}" 
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-slate-400">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="isEditMetaModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer">
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
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isUploadDocModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-200 space-y-5 m-auto">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-base font-bold text-slate-900">Upload Lampiran / Dokumen</h3>
                    <button type="button" @click="isUploadDocModalOpen = false" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.documents.upload', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="stage_number" value="1">
                    <input type="hidden" name="document_key" value="lampiran_pendukung">

                    <div>
                        <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[11px]">PILIH BERKAS (PDF, XLSX, DOCX, ZIP)</label>
                        <input type="file" name="document_file" required 
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-slate-400 cursor-pointer bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[11px]">KETERANGAN DOKUMEN</label>
                        <input type="text" name="notes" placeholder="Contoh: BoQ dan Penawaran Resmi" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-slate-400">
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="isUploadDocModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer">
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
            activeStageTab: initialStage || 'in_progress',
            currentStatus: currentDbStatus || 'In Progress',
            
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

            confirmDeleteProject() {
                if (confirm('Apakah Anda yakin ingin menghapus project ini beserta seluruh task-nya?')) {
                    document.getElementById('deleteProjForm').submit();
                }
            }
        };
    }
</script>
@endsection
