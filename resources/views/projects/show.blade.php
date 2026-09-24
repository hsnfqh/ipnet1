@extends('layouts.app')

@section('title', $project->name . ' - Detail Proyek')

@push('styles')
<style>
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1.5px solid #E2E8F0;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    }
    .text-ipnet-red {
        color: #8F0A0D;
    }
    .text-ipnet-red:hover {
        color: #700609;
    }
    .btn-ipnet-primary {
        background: linear-gradient(135deg, #8F0A0D 0%, #D62E3C 100%);
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(143, 10, 13, 0.25);
        transition: all 0.15s ease;
    }
    .btn-ipnet-primary:hover {
        filter: brightness(1.08);
        box-shadow: 0 4px 12px rgba(143, 10, 13, 0.35);
        color: #FFFFFF;
    }
    .stage-pill {
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .stage-pill.active {
        background: linear-gradient(135deg, #8F0A0D 0%, #D62E3C 100%);
        color: #FFFFFF;
        box-shadow: 0 2px 8px rgba(143, 10, 13, 0.3);
    }
    .stage-pill:not(.active) {
        background-color: #F1F5F9;
        color: #64748B;
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
    
    $leadershipUsers = \App\Models\User::orderBy('name')->get();
    $susantoUser = $leadershipUsers->first(fn($u) => str_contains(strtolower($u->name), 'susanto'));
    $hariyadiUser = $leadershipUsers->first(fn($u) => str_contains(strtolower($u->name), 'hariyadi'));

    $headApproval = $draftApprovals['head'] ?? [
        'assigned' => false,
        'approved' => false,
        'by' => $susantoUser ? $susantoUser->name : 'Pak Susanto Djaya',
        'date' => null,
        'notes' => null
    ];
    $directorApproval = $draftApprovals['director'] ?? [
        'assigned' => false,
        'approved' => false,
        'by' => $hariyadiUser ? $hariyadiUser->name : 'Pak Hariyadi',
        'date' => null,
        'notes' => null
    ];

    $isHeadAssigned = !empty($headApproval['assigned']) || !empty($headApproval['approved']);
    $isDirectorAssigned = !empty($directorApproval['assigned']) || !empty($directorApproval['approved']);
    $isBothApproved = !empty($headApproval['approved']) && !empty($directorApproval['approved']);
    $isAnyAssigned = $isHeadAssigned || $isDirectorAssigned;

    // Data Penugasan Tim Solusi Teknis (PIC BD, Presales Specialist & Solution Architect)
    $technicalAssignments = $handoverData['technical_assignments'] ?? [];
    
    // 1. PIC BD (Business Development / Product Manager)
    $bdmAssignment = array_merge([
        'assigned'         => false,
        'assigned_to'      => null,
        'assigned_user_id' => null,
        'assigned_by'      => null,
        'assigned_at'      => null,
        'status'           => 'Pending',
    ], is_array($technicalAssignments['bdm'] ?? null) ? $technicalAssignments['bdm'] : []);
    
    $isBdmAssigned = !empty($project->bdm_id) || !empty($bdmAssignment['assigned']);
    $bdmName = $project->bdm ? $project->bdm->name : ($bdmAssignment['assigned_to'] ?? null);

    // 2. Pre-Sales Specialist
    $presalesAssignment = array_merge([
        'assigned'         => false,
        'assigned_to'      => null,
        'assigned_user_id' => null,
        'assigned_by'      => null,
        'assigned_at'      => null,
        'sales_notes'      => null,
        'status'           => 'Pending',
        'completed_at'     => null,
        'document_path'    => null,
        'document_name'    => null,
        'document_title'   => null,
    ], is_array($technicalAssignments['presales'] ?? null) ? $technicalAssignments['presales'] : []);

    // 3. Solution Architect
    $architectAssignment = array_merge([
        'assigned'         => false,
        'assigned_to'      => null,
        'assigned_user_id' => null,
        'assigned_by'      => null,
        'assigned_at'      => null,
        'sales_notes'      => null,
        'status'           => 'Pending',
        'completed_at'     => null,
        'document_path'    => null,
        'document_name'    => null,
        'document_title'   => null,
    ], is_array($technicalAssignments['architect'] ?? null) ? $technicalAssignments['architect'] : []);

    // 4. Verifikasi Solusi oleh PIC BD
    $bdVerification = array_merge([
        'status'       => 'Pending Assignment', // 'Pending Assignment', 'Waiting Uploads', 'Pending Verification', 'Approved', 'Revision Needed'
        'submitted_at' => null,
        'submitted_by' => null,
        'verified_by'  => null,
        'verified_at'  => null,
        'notes'        => null,
    ], is_array($technicalAssignments['bd_verification'] ?? null) ? $technicalAssignments['bd_verification'] : []);

    $isPresalesAssigned = !empty($presalesAssignment['assigned']);
    $isArchitectAssigned = !empty($architectAssignment['assigned']);
    $isPresalesDone = !empty($presalesAssignment['document_path']) || ($presalesAssignment['status'] ?? '') === 'Completed';
    $isArchitectDone = !empty($architectAssignment['document_path']) || ($architectAssignment['status'] ?? '') === 'Completed';

    // Penyesuaian otomatis status verifikasi
    if (($isPresalesDone || $isArchitectDone) && in_array($bdVerification['status'], ['Pending Assignment', 'Waiting Uploads', null])) {
        $bdVerification['status'] = 'Pending Verification';
    } elseif ($isBdmAssigned && !$isPresalesDone && !$isArchitectDone && in_array($bdVerification['status'], ['Pending Assignment', null])) {
        $bdVerification['status'] = 'Waiting Uploads';
    }

    $isAnyTechnicalAssigned = $isBdmAssigned || $isPresalesAssigned || $isArchitectAssigned;
    $isAllTechnicalAssigned = $isBdmAssigned && $isPresalesAssigned && $isArchitectAssigned;

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
    $initialStage = match($currentStatus) {
        'Opportunity' => 'opportunity',
        'In Progress' => 'in_progress',
        'Pending' => 'pending',
        'Completed' => 'completed',
        default => 'draft',
    };

    // Hak otorisasi persetujuan pimpinan (Pak Susanto & Pak Hariyadi)
    $authUser = auth()->user();
    $userRoles = $authUser ? $authUser->roles->pluck('name')->toArray() : [];
    $canApproveHead = $authUser && (
        !empty(array_intersect(['Director', 'Direktur', 'Division Head', 'Head Divisi', 'Group Leader', 'HD / Direktur', 'Lead Divisi'], $userRoles)) 
        || str_contains(strtolower($authUser->name), 'susanto')
    );
    $canApproveDirector = $authUser && (
        !empty(array_intersect(['Director', 'Direktur', 'HD / Direktur'], $userRoles)) 
        || str_contains(strtolower($authUser->name), 'hariyadi')
    );

    // Ambil daftar user BD (Business Development Managers)
    $bdmUsers = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['BDM', 'BusDev', 'Business Development']))->orderBy('name')->get();
    if ($bdmUsers->isEmpty()) {
        $bdmUsers = \App\Models\User::where('position', 'like', '%Business Development%')->orWhere('name', 'like', '%Kurnijanto%')->orWhere('name', 'like', '%Novan%')->orderBy('name')->get();
    }

    // Ambil daftar user PMO secara aman (tanpa crash jika role belum ada di DB)
    $pmoUsers = \App\Models\User::whereHas('roles', function($q) {
        $q->whereIn('name', ['PMO', 'Project Manager', 'Lead Divisi', 'Group Leader', 'Direktur', 'HD / Direktur']);
    })->orWhere('name', 'like', '%Rizki%')->orWhere('name', 'like', '%Kuncoro%')->orderBy('name')->get();

    // Ambil daftar user Presales & Solution Architect
    $presalesUsers = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['Presales', 'Pre-Sales']))->orderBy('name')->get();
    if ($presalesUsers->isEmpty()) {
        $presalesUsers = \App\Models\User::where('name', 'like', '%Akbar%')->get();
    }
    $architectUsers = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['Solution Architect', 'Solutions Architect', 'SA', 'Tech Develop']))->orderBy('name')->get();
    if ($architectUsers->isEmpty()) {
        $architectUsers = \App\Models\User::where('name', 'like', '%Aris%')->get();
    }

    // Hak otorisasi PIC BD untuk me-review & memverifikasi dokumen solusi
    $canVerifyBD = $authUser && (
        ($project->bdm_id && $authUser->id == $project->bdm_id)
        || !empty(array_intersect(['BDM', 'BusDev', 'Business Development', 'Director', 'Direktur', 'HD / Direktur', 'Super Admin', 'Admin'], $userRoles))
        || str_contains(strtolower($authUser->name), 'kurnijanto')
        || str_contains(strtolower($authUser->name), 'novan')
        || str_contains(strtolower($authUser->name), 'kipsriyanto')
        || str_contains(strtolower($authUser->name), 'armen')
        || str_contains(strtolower($authUser->name), 'dony')
    );

    // Hak otorisasi unggah berkas teknis solusi:
    // Terbuka untuk akun penugasan, role Presales, Solution Architect, Sales (pemilik proyek), PMO, dan Manajemen.
    $canUploadPresales = $authUser && (
        (!empty($presalesAssignment['assigned_user_id']) && $authUser->id == $presalesAssignment['assigned_user_id'])
        || !empty(array_intersect(['Presales', 'Pre-Sales', 'Sales', 'Super Admin', 'Admin', 'PMO', 'Project Manager', 'Director', 'Direktur', 'HD / Direktur', 'Group Leader'], $userRoles))
        || str_contains(strtolower($authUser->name), 'akbar')
        || $authUser->id == ($project->creator_id ?? $project->created_by)
        || ($project->sales_name && str_contains(strtolower($authUser->name), strtolower($project->sales_name)))
    );

    $canUploadArchitect = $authUser && (
        (!empty($architectAssignment['assigned_user_id']) && $authUser->id == $architectAssignment['assigned_user_id'])
        || !empty(array_intersect(['Solution Architect', 'Solutions Architect', 'SA', 'Sales', 'Super Admin', 'Admin', 'PMO', 'Project Manager', 'Director', 'Direktur', 'HD / Direktur', 'Group Leader'], $userRoles))
        || str_contains(strtolower($authUser->name), 'aris')
        || $authUser->id == ($project->creator_id ?? $project->created_by)
        || ($project->sales_name && str_contains(strtolower($authUser->name), strtolower($project->sales_name)))
    );
@endphp

<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans text-slate-800" 
     x-data="projectDetailPage('{{ $initialStage ?? 'draft' }}', '{{ $currentStatus }}')">
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

            @if(isset($errors) && $errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-xs font-semibold space-y-1.5 shadow-xs">
                    <div class="flex items-center justify-between font-bold text-red-900">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Terjadi kendala pada pengunggahan / formulir:</span>
                        </div>
                        <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold px-2 cursor-pointer">✕</button>
                    </div>
                    <ul class="list-disc list-inside pl-6 text-[11.5px] space-y-0.5 text-red-700">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 1. BREADCRUMBS --}}
            <div class="flex items-center gap-2 text-[12.5px] font-semibold text-[#64748B]">
                <a href="{{ route('sales.pipeline.index') }}" class="hover:text-[#8F0A0D] transition font-bold text-[#1E293B]">Project</a>
                <svg class="w-3 h-3 text-[#CBD5E1]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                <span class="text-[#64748B] truncate max-w-md">{{ $project->name }}</span>
            </div>

            {{-- 2. MAIN 2-COLUMN BALANCED GRID (SEPERTI CLIENT VIEW) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                {{-- ══ LEFT COLUMN: PROJECT DETAILS CARD (col-span-8) ══ --}}
                <div class="lg:col-span-8">
                    <div class="ipnet-card p-6 sm:p-8 space-y-6">
                        
                        {{-- A. Top Info Bar: Team & Creator Info (Filter stage dihapus) --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-1">
                            <div class="flex items-center gap-2 text-xs">
                                <span class="text-[11px] font-bold text-[#94A3B8] uppercase tracking-wider">TEAM</span>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-[#F8FAFC] text-[#1E293B] border border-[#E2E8F0] shadow-2xs">
                                    {{ $project->client_department ?: 'IPNET 01' }}
                                </span>
                            </div>

                            <div class="text-xs text-[#94A3B8] font-normal shrink-0">
                                Created by <span class="text-[#1E293B] font-bold">{{ $project->creator ? $project->creator->name : ($project->sales_name ?: 'Sales Team') }}</span>, 
                                {{ \Carbon\Carbon::parse($project->created_at)->format('d M Y H:i') }}
                            </div>
                        </div>

                        {{-- B. Project Title & Action Buttons --}}
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold text-[#1E293B] tracking-tight">{{ $project->name }}</h1>
                                <p class="text-xs text-[#64748B] mt-1">{{ $project->description ?: $project->name }}</p>
                            </div>

                            {{-- Pending & Delete Action Buttons --}}
                            <div class="flex items-center gap-3 shrink-0 pt-1">
                                @if($currentStatus === 'In Progress')
                                    <form action="{{ route('projects.stage_update', $project->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="Completed">
                                        <button type="submit" onclick="return confirm('Tandai proyek {{ addslashes($project->name) }} sebagai Completed?')"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-2xs cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            <span>Tandai Selesai (Completed)</span>
                                        </button>
                                    </form>
                                @endif

                                <form action="{{ route('projects.stage_update', $project->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="{{ $currentStatus === 'Pending' ? 'In Progress' : 'Pending' }}">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#E2E8F0] bg-white text-xs font-bold text-[#64748B] hover:text-[#8F0A0D] hover:bg-red-50 hover:border-red-200 transition cursor-pointer shadow-2xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"></circle><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5l3 2"/></svg>
                                        <span>{{ $currentStatus === 'Pending' ? 'Resume' : 'Pending' }}</span>
                                    </button>
                                </form>

                                <button type="button" @click="confirmDeleteProject()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#E2E8F0] bg-white text-xs font-bold text-[#EF4444] hover:bg-red-50 hover:border-red-200 transition cursor-pointer shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Delete</span>
                                </button>
                            </div>
                        </div>

                        {{-- C. Project Estimation Banner (IPNET Modern Metric Box) --}}
                        <div class="p-5 rounded-2xl border border-[#E2E8F0] bg-gradient-to-r from-[#F8FAFC] to-[#FFFFFF] shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 flex-1 text-xs">
                                <div>
                                    <div class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider">PROJECT ESTIMATION</div>
                                    <div class="text-[17px] font-extrabold text-[#8F0A0D] mt-1">
                                        Rp {{ number_format($project->contract_value ?: 0, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider">PROJECT START</div>
                                    <div class="text-[13px] font-bold text-[#1E293B] mt-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : '-' }}
                                    </div>
                                </div>

                                <div>
                                    <div class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider">PROJECT END</div>
                                    <div class="text-[13px] font-bold text-[#1E293B] mt-1 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-[#94A3B8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : ($project->expected_closing_date ? \Carbon\Carbon::parse($project->expected_closing_date)->format('d M Y') : '-') }}
                                    </div>
                                </div>
                            </div>

                            <div class="shrink-0">
                                <button type="button" 
                                        @click="isEditMetaModalOpen = true" 
                                        class="p-2.5 rounded-xl border border-[#CBD5E1] bg-white hover:bg-[#F8FAFC] text-[#475569] hover:text-[#8F0A0D] hover:border-[#8F0A0D]/30 transition cursor-pointer shadow-2xs" 
                                        title="Edit Estimasi Proyek">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- D. Dynamic Contextual Panel: Sesuai Status Proyek Saat Ini --}}
                        @if($currentStatus === 'Draft')
                            {{-- DRAFT: Persetujuan Pimpinan (Head Divisi & Direktur) --}}
                            <div class="p-5 rounded-2xl border border-gray-200 bg-[#F8FAFC] space-y-4">
                                <div class="border-b border-gray-200 pb-2.5">
                                    <div class="text-[13px] font-bold text-gray-900 flex items-center gap-2">
                                        <span class="w-1.5 h-4 rounded-full bg-[#8F0A0D]"></span>
                                        Persetujuan Pimpinan (Review &amp; Sign-Off)
                                    </div>
                                    <div class="text-[11.5px] text-gray-500 mt-0.5">
                                        Review kelayakan teknis oleh Head Divisi &amp; otorisasi kontrak oleh Direktur sebelum diserahkan ke PMO.
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                    {{-- Reviewer 1: Pak Susanto --}}
                                    <div class="p-4 rounded-xl bg-white border border-gray-200 space-y-2.5 shadow-2xs flex flex-col justify-between">
                                        <div class="space-y-2.5">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">HEAD DIVISI</div>
                                                    <div class="font-bold text-gray-900 text-sm">{{ $headApproval['assigned_to'] ?? ($susantoUser ? $susantoUser->name : 'Pak Susanto Djaya') }}</div>
                                                </div>
                                                <span class="px-2.5 py-0.5 rounded text-[10.5px] font-bold {{ !empty($headApproval['approved']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($isHeadAssigned ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                                    {{ !empty($headApproval['approved']) ? 'Disetujui' : ($isHeadAssigned ? 'Menunggu Review' : 'Belum Di-assign') }}
                                                </span>
                                            </div>

                                            <div class="text-[11.5px] text-gray-600">
                                                @if(!empty($headApproval['approved']))
                                                    <span class="text-emerald-700 font-semibold">"{{ $headApproval['notes'] ?? 'Review kelayakan teknis & alokasi resource disetujui.' }}"</span>
                                                    <div class="text-[10.5px] text-gray-400 mt-1">Disetujui: {{ $headApproval['date'] ?? '-' }}</div>
                                                @elseif($isHeadAssigned)
                                                    <span class="italic text-gray-500">"Menunggu review kelayakan teknis dan alokasi resource dari {{ $headApproval['assigned_to'] ?? 'Pak Susanto Djaya' }}."</span>
                                                    @if(!empty($headApproval['assigned_at']))
                                                        <div class="text-[10.5px] text-gray-400 mt-1">Ditugaskan: {{ $headApproval['assigned_at'] }} (oleh {{ $headApproval['assigned_by'] ?? 'Sales' }})</div>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">Belum diajukan ke Head Divisi. Klik tombol di bawah untuk menugaskan review kelayakan teknis.</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="pt-2 border-t border-gray-100 flex items-center justify-between gap-2 mt-2">
                                            @if(!$isHeadAssigned)
                                                <button type="button" @click="openAssignModal('head')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                    <span>Assign ke Pak Susanto</span>
                                                </button>
                                            @elseif(!$headApproval['approved'])
                                                <button type="button" @click="openAssignModal('head')" class="text-[11px] font-semibold text-gray-500 hover:text-[#8F0A0D] cursor-pointer">
                                                    Ubah Penugasan
                                                </button>
                                            @else
                                                <div></div>
                                            @endif

                                            @if($canApproveHead && $isHeadAssigned)
                                                <button type="button" @click="openApproveModal('head')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white btn-ipnet-gradient shadow-xs cursor-pointer ml-auto">
                                                    <span>{{ empty($headApproval['approved']) ? '✓ Beri Approval (Pak Susanto)' : 'Ubah Catatan' }}</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Reviewer 2: Pak Hariyadi --}}
                                    <div class="p-4 rounded-xl bg-white border border-gray-200 space-y-2.5 shadow-2xs flex flex-col justify-between">
                                        <div class="space-y-2.5">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">DIREKTUR</div>
                                                    <div class="font-bold text-gray-900 text-sm">{{ $directorApproval['assigned_to'] ?? ($hariyadiUser ? $hariyadiUser->name : 'Pak Hariyadi') }}</div>
                                                </div>
                                                <span class="px-2.5 py-0.5 rounded text-[10.5px] font-bold {{ !empty($directorApproval['approved']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($isDirectorAssigned ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                                    {{ !empty($directorApproval['approved']) ? 'Disahkan' : ($isDirectorAssigned ? 'Menunggu Otorisasi' : 'Belum Di-assign') }}
                                                </span>
                                            </div>

                                            <div class="text-[11.5px] text-gray-600">
                                                @if(!empty($directorApproval['approved']))
                                                    <span class="text-emerald-700 font-semibold">"{{ $directorApproval['notes'] ?? 'Otorisasi finansial & validasi kontrak disahkan.' }}"</span>
                                                    <div class="text-[10.5px] text-gray-400 mt-1">Disahkan: {{ $directorApproval['date'] ?? '-' }}</div>
                                                @elseif($isDirectorAssigned)
                                                    <span class="italic text-gray-500">"Menunggu otorisasi finansial dan persetujuan kontrak dari {{ $directorApproval['assigned_to'] ?? 'Pak Hariyadi' }}."</span>
                                                    @if(!empty($directorApproval['assigned_at']))
                                                        <div class="text-[10.5px] text-gray-400 mt-1">Ditugaskan: {{ $directorApproval['assigned_at'] }} (oleh {{ $directorApproval['assigned_by'] ?? 'Sales' }})</div>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">Belum diajukan ke Direktur. Klik tombol di bawah untuk menugaskan otorisasi kontrak.</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="pt-2 border-t border-gray-100 flex items-center justify-between gap-2 mt-2">
                                            @if(!$isDirectorAssigned)
                                                <button type="button" @click="openAssignModal('director')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                    <span>Assign ke Pak Hariyadi</span>
                                                </button>
                                            @elseif(!$directorApproval['approved'])
                                                <button type="button" @click="openAssignModal('director')" class="text-[11px] font-semibold text-gray-500 hover:text-[#8F0A0D] cursor-pointer">
                                                    Ubah Penugasan
                                                </button>
                                            @else
                                                <div></div>
                                            @endif

                                            @if($canApproveDirector && $isDirectorAssigned)
                                                <button type="button" @click="openApproveModal('director')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white btn-ipnet-gradient shadow-xs cursor-pointer ml-auto">
                                                    <span>{{ empty($directorApproval['approved']) ? '✓ Beri Otorisasi (Pak Hariyadi)' : 'Ubah Catatan' }}</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($currentStatus === 'In Progress')
                            {{-- IN PROGRESS (Delivery ke PMO & Monitoring Tim Engineer) --}}
                            <div class="p-5 rounded-xl border border-gray-200 bg-gray-50/60 space-y-4">
                                <div class="flex items-center justify-between border-b border-gray-200 pb-2.5 flex-wrap gap-2">
                                    <div>
                                        <div class="text-xs font-bold text-gray-900">Fase Delivery ke PMO (In Progress)</div>
                                        <div class="text-[11px] text-gray-500">Sales menyerahkan proyek ke PMO. Alokasi personel teknis dikelola PMO dan dipantau Sales di bawah:</div>
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <form action="{{ route('projects.stage_update', $project->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="Completed">
                                            <button type="submit" onclick="return confirm('Tandai proyek {{ addslashes($project->name) }} sebagai Selesai (Completed)?')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-2xs cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                <span>✓ Selesaikan Proyek (Completed)</span>
                                            </button>
                                        </form>
                                        <button type="button" @click="isHandoverModalOpen = true" class="text-xs font-bold text-[#8F0A0D] hover:underline cursor-pointer">
                                            {{ $project->pm ? 'Ubah PMO (' . $project->pm->name . ')' : '+ Handover ke PMO (Rizki)' }}
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                    <div class="p-4 rounded-xl bg-white border border-gray-200 space-y-2">
                                        <div class="text-[10px] font-bold text-gray-400 uppercase">PROJECT MANAGER (PMO)</div>
                                        <div class="font-bold text-gray-900 text-sm">{{ $project->pm ? $project->pm->name : 'Belum Ada PM (Tunjuk Rizki)' }}</div>
                                        <div class="text-[11.5px] text-gray-500">{{ $project->pm ? $project->pm->email : 'Sales silakan serahkan delivery ke Rizki' }}</div>
                                    </div>
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
                        @elseif($currentStatus === 'Opportunity')
                            {{-- OPPORTUNITY / CRM --}}
                            <div class="p-5 rounded-2xl border border-[#E2E8F0] bg-white text-xs space-y-4 shadow-2xs">
                                <div class="flex items-center justify-between flex-wrap gap-2 border-b border-gray-100 pb-3">
                                    <div class="flex items-center gap-2.5">
                                        <span class="w-3 h-3 rounded-full shadow-xs" style="background: linear-gradient(135deg, #0EA5E9, #0284C7);"></span>
                                        <div>
                                            <span class="text-[13.5px] font-extrabold text-[#1E293B] block">Pipeline Sales &amp; Opportunity</span>
                                            <span class="text-[11px] text-gray-500">Pantau tahap prospek, peluang closing, dan kesiapan penawaran tender</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        {{-- Tombol Edit Stage / Pipeline --}}
                                        <button type="button" @click="openEditPipelineModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 transition cursor-pointer shadow-2xs">
                                            <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Edit Stage &amp; Prospek</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 pt-1">
                                    <div class="p-3.5 bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] shadow-2xs flex items-center justify-between">
                                        <div>
                                            <span class="text-[10px] font-bold text-[#64748B] uppercase tracking-wider block">Stage</span>
                                            <strong class="text-[13.5px] text-[#1E293B] font-extrabold mt-0.5 block">{{ $project->sales_stage ?: 'Qualification' }}</strong>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            Active Stage
                                        </span>
                                    </div>
                                    <div class="p-3.5 bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] shadow-2xs flex items-center justify-between">
                                        <div>
                                            <span class="text-[10px] font-bold text-[#64748B] uppercase tracking-wider block">Win Probability</span>
                                            <div class="flex items-center gap-1.5 mt-0.5">
                                                <span class="w-2.5 h-2.5 rounded-full" style="background: linear-gradient(135deg, #FDE047, #F59E0B);"></span>
                                                <strong class="text-[13.5px] text-[#8F0A0D] font-extrabold">{{ $project->win_probability ?: 10 }}%</strong>
                                            </div>
                                        </div>
                                        <div class="w-16 bg-gray-200 h-2 rounded-full overflow-hidden">
                                            <div class="bg-gradient-to-r from-amber-400 to-[#8F0A0D] h-full rounded-full" style="width: {{ min(100, max(5, $project->win_probability ?: 10)) }}%"></div>
                                        </div>
                                    </div>
                                    <div class="p-3.5 bg-[#F8FAFC] rounded-xl border border-[#E2E8F0] shadow-2xs flex items-center justify-between">
                                        <div>
                                            <span class="text-[10px] font-bold text-[#64748B] uppercase tracking-wider block">Target Closing</span>
                                            <strong class="text-[13.5px] text-[#1E293B] font-extrabold mt-0.5 block">{{ $project->expected_closing_date ? \Carbon\Carbon::parse($project->expected_closing_date)->format('d M Y') : 'Belum Ditentukan' }}</strong>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                </div>
                            </div>

                            {{-- 3-CARD GRID: KOLABORASI TIM SOLUSI (BD, PRESALES & SOLUTION ARCHITECT) --}}
                            <div class="p-5 rounded-2xl border border-gray-200 bg-[#F8FAFC] space-y-4">
                                <div class="border-b border-gray-200 pb-3 flex items-center justify-between flex-wrap gap-2">
                                    <div>
                                        <div class="text-[13.5px] font-extrabold text-gray-900 flex items-center gap-2">
                                            <span class="w-2 h-4 rounded-full bg-[#8F0A0D]"></span>
                                            Kolaborasi Tim Solusi (BD, Pre-Sales &amp; Solution Architect)
                                        </div>
                                        <p class="text-[11px] text-gray-500 mt-0.5">
                                            Alur: Sales tugaskan tim $\rightarrow$ Presales &amp; SA susun dokumen $\rightarrow$ Verifikasi PIC BD $\rightarrow$ Masuk ke Sales untuk penawaran klien.
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 text-xs">
                                    {{-- Card 1: Business Development (PIC / Product Manager & Verifikator) --}}
                                    <div class="p-4 rounded-xl bg-white border {{ ($bdVerification['status'] ?? '') === 'Approved' ? 'border-emerald-300 ring-1 ring-emerald-100' : (($bdVerification['status'] ?? '') === 'Revision Needed' ? 'border-rose-300 ring-1 ring-rose-100' : 'border-gray-200') }} space-y-3 shadow-2xs flex flex-col justify-between">
                                        <div class="space-y-2.5">
                                            <div class="flex items-start justify-between gap-2">
                                                <div>
                                                    <div class="text-[9.5px] font-bold text-gray-400 uppercase tracking-wider">BUSINESS DEVELOPMENT (PIC / PM)</div>
                                                    <div class="font-extrabold {{ $isBdmAssigned ? 'text-gray-900' : 'text-gray-400 italic' }} text-sm mt-0.5">
                                                        {{ $isBdmAssigned && !empty($bdmName) ? $bdmName : 'Belum Ditugaskan' }}
                                                    </div>
                                                </div>
                                                @php
                                                    $bdBadgeClass = match($bdVerification['status'] ?? '') {
                                                        'Approved'            => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                        'Revision Needed'     => 'bg-rose-50 text-rose-700 border-rose-200 animate-pulse',
                                                        'Pending Verification'=> 'bg-amber-50 text-amber-800 border-amber-300 animate-pulse',
                                                        'Waiting Uploads'     => 'bg-blue-50 text-blue-700 border-blue-200',
                                                        default               => 'bg-slate-100 text-slate-600 border-slate-200',
                                                    };
                                                    $bdBadgeLabel = match($bdVerification['status'] ?? '') {
                                                        'Approved'            => '✓ Disetujui BD',
                                                        'Revision Needed'     => '⚠ Perlu Revisi BD',
                                                        'Pending Verification'=> 'Menunggu Verifikasi BD',
                                                        'Waiting Uploads'     => 'Menunggu Berkas Teknis',
                                                        default               => 'Belum Di-assign',
                                                    };
                                                @endphp
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $bdBadgeClass }}">
                                                    {{ $bdBadgeLabel }}
                                                </span>
                                            </div>

                                            <div class="text-[11.5px] text-gray-600 space-y-2">
                                                <div class="font-medium text-gray-700">Tanggung Jawab: Validasi Kelayakan &amp; Verifikator Proposal.</div>
                                                
                                                {{-- Status Box Verifikasi BD --}}
                                                @if(($bdVerification['status'] ?? '') === 'Approved')
                                                    <div class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 space-y-1">
                                                        <div class="font-bold flex items-center gap-1.5 text-xs">
                                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                            <span>Proposal &amp; Desain Disetujui</span>
                                                        </div>
                                                        <div class="text-[10.5px] text-emerald-700">Diverifikasi oleh: <strong class="font-bold">{{ $bdVerification['verified_by'] ?? 'BD' }}</strong> ({{ $bdVerification['verified_at'] ?? '-' }})</div>
                                                        @if(!empty($bdVerification['notes']))
                                                            <div class="text-[11px] text-emerald-900 bg-white/70 p-1.5 rounded border border-emerald-100 italic">"{{ $bdVerification['notes'] }}"</div>
                                                        @endif
                                                        <div class="text-[10px] text-emerald-600 font-semibold mt-1">✓ Berkas final siap dikirimkan Sales ke Klien.</div>
                                                    </div>
                                                @elseif(($bdVerification['status'] ?? '') === 'Revision Needed')
                                                    <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 space-y-1">
                                                        <div class="font-bold flex items-center gap-1.5 text-xs text-rose-700">
                                                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                            <span>Catatan Revisi dari PIC BD:</span>
                                                        </div>
                                                        <div class="text-[11px] text-rose-900 bg-white/80 p-2 rounded border border-rose-100 italic">
                                                            "{{ $bdVerification['notes'] ?? 'Mohon lakukan revisi dan penyesuaian pada proposal/desain.' }}"
                                                        </div>
                                                        <div class="text-[10px] text-rose-600">Oleh: {{ $bdVerification['verified_by'] ?? 'BD' }} ({{ $bdVerification['verified_at'] ?? '-' }})</div>
                                                    </div>
                                                @elseif(($bdVerification['status'] ?? '') === 'Pending Verification')
                                                    <div class="p-2 rounded-lg bg-amber-50/80 border border-amber-200 text-amber-800 space-y-1">
                                                        <div class="font-semibold flex items-center gap-1.5">
                                                            <svg class="w-3.5 h-3.5 text-amber-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                            <span>Berkas Diunggah &amp; Menunggu Verifikasi BD</span>
                                                        </div>
                                                        <div class="text-[10.5px] text-amber-700">PIC BD silakan meninjau proposal teknis &amp; topologi di bawah.</div>
                                                    </div>
                                                @elseif($isBdmAssigned)
                                                    <div class="p-2 rounded-lg bg-slate-50 border border-slate-200 text-slate-600 text-[11px]">
                                                        <span>Menunggu tim Presales &amp; Solution Architect mengunggah berkas untuk diverifikasi.</span>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 italic">Belum ada PIC BD yang ditunjuk.</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="pt-2.5 border-t border-gray-100 flex items-center justify-between gap-2 mt-2">
                                            @if(!$isBdmAssigned)
                                                <button type="button" @click="openAssignTechnicalModal('bdm')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                                                    <span>+ Tunjuk PIC BD</span>
                                                </button>
                                            @else
                                                <button type="button" @click="openAssignTechnicalModal('bdm')" class="text-[11px] font-semibold text-gray-500 hover:text-[#8F0A0D] cursor-pointer">
                                                    Ubah PIC BD
                                                </button>
                                            @endif

                                            {{-- Tombol Aksi Verifikasi oleh BD --}}
                                            @if($canVerifyBD && ($isPresalesDone || $isArchitectDone))
                                                <button type="button" @click="openVerifyTechnicalModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white btn-ipnet-primary shadow-xs cursor-pointer ml-auto">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>Verifikasi Dokumen Solusi</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Card 2: Pre-Sales Specialist --}}
                                    <div class="p-4 rounded-xl bg-white border border-gray-200 space-y-3 shadow-2xs flex flex-col justify-between">
                                        <div class="space-y-2.5">
                                            <div class="flex items-start justify-between gap-2">
                                                <div>
                                                    <div class="text-[9.5px] font-bold text-gray-400 uppercase tracking-wider">PRE-SALES SPECIALIST</div>
                                                    <div class="font-extrabold {{ $isPresalesAssigned && !empty($presalesAssignment['assigned_to']) ? 'text-gray-900' : 'text-gray-400 italic' }} text-sm mt-0.5">
                                                        {{ $isPresalesAssigned && !empty($presalesAssignment['assigned_to']) ? $presalesAssignment['assigned_to'] : 'Belum Ditugaskan' }}
                                                    </div>
                                                </div>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $isPresalesDone ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($isPresalesAssigned ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                                    {{ $isPresalesDone ? 'Berkas Diunggah' : ($isPresalesAssigned ? 'Menunggu Proposal & BoQ' : 'Belum Di-assign') }}
                                                </span>
                                            </div>

                                            <div class="text-[11.5px] text-gray-600 space-y-2">
                                                <div class="font-medium text-gray-700">Tanggung Jawab: Proposal Teknis &amp; BoQ Komersial.</div>
                                                @if($isPresalesDone)
                                                    <div class="p-2.5 rounded-lg bg-emerald-50/70 border border-emerald-200 text-emerald-800 space-y-1">
                                                        <div class="font-bold flex items-center gap-1.5 text-xs">
                                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                            <span>{{ $presalesAssignment['document_title'] ?? 'Proposal Teknis & BoQ' }}</span>
                                                        </div>
                                                        <div class="text-[10.5px] text-emerald-700 flex items-center justify-between">
                                                            <span>Berkas: {{ $presalesAssignment['document_name'] ?? 'File terlampir' }}</span>
                                                            @if(!empty($presalesAssignment['document_path']))
                                                                <a href="{{ asset('storage/' . $presalesAssignment['document_path']) }}" target="_blank" class="font-bold underline text-emerald-800 hover:text-emerald-950">Unduh</a>
                                                            @endif
                                                        </div>
                                                        @if(!empty($presalesAssignment['completed_at']))
                                                            <div class="text-[10px] text-emerald-600">Diunggah: {{ $presalesAssignment['completed_at'] }}</div>
                                                        @endif
                                                    </div>
                                                @elseif($isPresalesAssigned)
                                                    <div class="p-2.5 rounded-lg bg-amber-50/60 border border-amber-200 text-amber-800">
                                                        <div class="font-semibold italic">"{{ !empty($presalesAssignment['sales_notes']) ? $presalesAssignment['sales_notes'] : 'Mohon segera dibuatkan proposal teknis dan BoQ estimasi proyek.' }}"</div>
                                                        <div class="text-[10.5px] text-amber-600 mt-1">Ditugaskan: {{ $presalesAssignment['assigned_at'] ?? '-' }} (oleh {{ $presalesAssignment['assigned_by'] ?? 'Sales' }})</div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 italic">Belum ada penugasan Pre-Sales.</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="pt-2.5 border-t border-gray-100 flex items-center justify-between gap-2 mt-2">
                                            @if(!$isPresalesAssigned)
                                                <button type="button" @click="openAssignTechnicalModal('presales')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                                                    <span>+ Tugaskan Presales</span>
                                                </button>
                                            @else
                                                <button type="button" @click="openAssignTechnicalModal('presales')" class="text-[11px] font-semibold text-gray-500 hover:text-[#8F0A0D] cursor-pointer">
                                                    Ubah Penugasan
                                                </button>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Card 3: Solution Architect --}}
                                    <div class="p-4 rounded-xl bg-white border border-gray-200 space-y-3 shadow-2xs flex flex-col justify-between">
                                        <div class="space-y-2.5">
                                            <div class="flex items-start justify-between gap-2">
                                                <div>
                                                    <div class="text-[9.5px] font-bold text-gray-400 uppercase tracking-wider">SOLUTION ARCHITECT</div>
                                                    <div class="font-extrabold {{ $isArchitectAssigned && !empty($architectAssignment['assigned_to']) ? 'text-gray-900' : 'text-gray-400 italic' }} text-sm mt-0.5">
                                                        {{ $isArchitectAssigned && !empty($architectAssignment['assigned_to']) ? $architectAssignment['assigned_to'] : 'Belum Ditugaskan' }}
                                                    </div>
                                                </div>
                                                @php
                                                    $isBdApproved = (($bdVerification['status'] ?? '') === 'Approved');
                                                    $saIsCompleted = $isArchitectDone || ($isBdApproved && $isArchitectAssigned);
                                                @endphp
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $saIsCompleted ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($isArchitectAssigned ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                                    {{ $isArchitectDone ? 'Desain Diunggah' : ($isBdApproved && $isArchitectAssigned ? '✓ Solusi Disetujui BD' : ($isArchitectAssigned ? 'Menunggu Desain Topologi' : 'Belum Di-assign')) }}
                                                </span>
                                            </div>

                                            <div class="text-[11.5px] text-gray-600 space-y-2">
                                                <div class="font-medium text-gray-700">Tanggung Jawab: Desain Arsitektur &amp; Topologi Solusi.</div>
                                                @if($isArchitectDone)
                                                    <div class="p-2.5 rounded-lg bg-emerald-50/70 border border-emerald-200 text-emerald-800 space-y-1">
                                                        <div class="font-bold flex items-center gap-1.5 text-xs">
                                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                            <span>{{ $architectAssignment['document_title'] ?? 'Desain Arsitektur & Topologi' }}</span>
                                                        </div>
                                                        <div class="text-[10.5px] text-emerald-700 flex items-center justify-between">
                                                            <span>Berkas: {{ $architectAssignment['document_name'] ?? 'File terlampir' }}</span>
                                                            @if(!empty($architectAssignment['document_path']))
                                                                <a href="{{ asset('storage/' . $architectAssignment['document_path']) }}" target="_blank" class="font-bold underline text-emerald-800 hover:text-emerald-950">Unduh</a>
                                                            @endif
                                                        </div>
                                                        @if(!empty($architectAssignment['completed_at']))
                                                            <div class="text-[10px] text-emerald-600">Diunggah: {{ $architectAssignment['completed_at'] }}</div>
                                                        @endif
                                                    </div>
                                                @elseif($isBdApproved && $isArchitectAssigned)
                                                    <div class="p-2.5 rounded-lg bg-emerald-50/70 border border-emerald-200 text-emerald-800 space-y-1">
                                                        <div class="font-bold flex items-center gap-1.5 text-xs">
                                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                            <span>Desain Solusi Disahkan</span>
                                                        </div>
                                                        <div class="text-[10.5px] text-emerald-700 flex items-center justify-between">
                                                            <span>Berkas: {{ $presalesAssignment['document_name'] ?? 'Proposal & SOW Teknis' }}</span>
                                                            @if(!empty($presalesAssignment['document_path']) || !empty($project->proposal_file))
                                                                <a href="{{ asset('storage/' . ($presalesAssignment['document_path'] ?? $project->proposal_file)) }}" target="_blank" class="font-bold underline text-emerald-800 hover:text-emerald-950">Unduh</a>
                                                            @endif
                                                        </div>
                                                        <div class="text-[10px] text-emerald-600">✓ Tercakup dalam verifikasi dokumen solusi oleh PIC BD.</div>
                                                    </div>
                                                @elseif($isArchitectAssigned)
                                                    <div class="p-2.5 rounded-lg bg-amber-50/60 border border-amber-200 text-amber-800">
                                                        <div class="font-semibold italic">"{{ !empty($architectAssignment['sales_notes']) ? $architectAssignment['sales_notes'] : 'Mohon dirancang diagram topologi arsitektur sistem dan validasi sizing teknis.' }}"</div>
                                                        <div class="text-[10.5px] text-amber-600 mt-1">Ditugaskan: {{ $architectAssignment['assigned_at'] ?? '-' }} (oleh {{ $architectAssignment['assigned_by'] ?? 'Sales' }})</div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 italic">Belum ada penugasan Solution Architect.</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="pt-2.5 border-t border-gray-100 flex items-center justify-between gap-2 mt-2">
                                            @if(!$isArchitectAssigned)
                                                <button type="button" @click="openAssignTechnicalModal('architect')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                                                    <span>+ Tugaskan SA</span>
                                                </button>
                                            @else
                                                <button type="button" @click="openAssignTechnicalModal('architect')" class="text-[11px] font-semibold text-gray-500 hover:text-[#8F0A0D] cursor-pointer">
                                                    Ubah Penugasan
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($currentStatus === 'Pending')
                            {{-- PENDING --}}
                            <div class="p-5 rounded-2xl border border-amber-200 bg-amber-50/60 text-xs space-y-2 shadow-2xs">
                                <div class="font-bold text-amber-900 text-[13px] flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                    Proyek Ditangguhkan (Pending)
                                </div>
                                <p class="text-amber-800">Pengerjaan proyek sedang di-pause menunggu konfirmasi akses site atau pengiriman barang.</p>
                            </div>
                        @elseif($currentStatus === 'Completed')
                            {{-- COMPLETED --}}
                            <div class="p-5 rounded-2xl border border-emerald-200 bg-emerald-50/60 text-xs space-y-2 shadow-2xs">
                                <div class="font-bold text-emerald-900 text-[13px] flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                    Proyek Selesai &amp; BAST Terbit
                                </div>
                                <p class="text-emerald-800">Seluruh milestone selesai 100% dan berkas Berita Acara Serah Terima telah disahkan.</p>
                            </div>
                        @endif

                        {{-- E. Client Info Section --}}
                        <div class="pt-6 border-t border-[#F1F5F9] space-y-3">
                            <h3 class="text-[13.5px] font-bold text-[#1E293B] flex items-center gap-2">
                                <span class="w-1.5 h-4 rounded-full" style="background: linear-gradient(135deg, #8F0A0D, #D62E3C);"></span>
                                Client Info
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                                <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                    <div class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider">CLIENT NAME</div>
                                    <div class="font-bold text-[#1E293B] text-[13px] mt-1">{{ $project->client ?: '-' }}</div>
                                </div>
                                <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                    <div class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider">CLIENT EMAIL</div>
                                    <div class="font-bold text-[#1E293B] text-[13px] mt-1 truncate">{{ $project->customer_pic_finance ?: ($project->customer_pic_technical ?: '-') }}</div>
                                </div>
                                <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                    <div class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider">PIC</div>
                                    <div class="font-bold text-[#1E293B] text-[13px] mt-1">{{ $project->customer_pic_name ?? ($project->sales_name ?? '-') }}</div>
                                </div>
                                <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0]">
                                    <div class="text-[10.5px] font-bold text-[#64748B] uppercase tracking-wider">CONTACT PIC</div>
                                    <div class="font-bold text-[#1E293B] text-[13px] mt-1">{{ $project->customer_pic_business ?: '-' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- F. Milestone Section --}}
                        <div class="pt-6 border-t border-[#F1F5F9] space-y-3">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-2.5">
                                    <h3 class="text-[13.5px] font-bold text-[#1E293B] flex items-center gap-2">
                                        <span class="w-1.5 h-4 rounded-full" style="background: linear-gradient(135deg, #FDE047, #F59E0B);"></span>
                                        Milestones
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-[#F1F5F9] text-[#64748B] border border-[#E2E8F0]">
                                        {{ $project->tasks->where('status', 'Completed')->count() }}/{{ $project->tasks->count() }} Selesai
                                    </span>
                                </div>
                                
                                <button type="button" 
                                        @click="isAddMilestoneModalOpen = true" 
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 transition cursor-pointer border border-red-200 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    <span>ADD MILESTONE</span>
                                </button>
                            </div>

                            @if($project->tasks->count() > 0)
                                <div class="space-y-2">
                                    @foreach($project->tasks as $task)
                                        <div class="p-3 rounded-xl border border-[#E2E8F0] bg-white flex items-center justify-between text-xs hover:border-[#CBD5E1] transition">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <input type="checkbox" {{ $task->status === 'Completed' ? 'checked' : '' }} disabled class="rounded text-[#8F0A0D] shrink-0">
                                                <div class="min-w-0">
                                                    <span class="font-bold text-[#1E293B] truncate block">{{ $task->title ?? $task->name }}</span>
                                                    @if($task->engineer)
                                                        <div class="text-[10.5px] text-[#64748B]">Engineer: {{ $task->engineer->name }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="px-2.5 py-0.5 rounded-lg text-[10.5px] font-bold {{ $task->status === 'Completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-[#F1F5F9] text-[#475569]' }}">
                                                {{ $task->status }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-3.5 rounded-xl border border-dashed border-[#CBD5E1] bg-[#F8FAFC]/50 text-center text-xs text-[#94A3B8]">
                                    Belum ada milestone. Klik tombol <span class="font-semibold text-[#8F0A0D]">ADD MILESTONE</span> untuk menambahkan target pekerjaan.
                                </div>
                            @endif
                        </div>

                        {{-- G. Attachments Section --}}
                        @php
                            $uploadedDocs = \App\Models\ProjectDocument::where('project_id', $project->id)
                                ->whereNotNull('file_path')
                                ->where('file_path', '!=', '')
                                ->latest()
                                ->get();
                        @endphp
                        <div class="pt-6 border-t border-[#F1F5F9] space-y-3">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-2.5">
                                    <h3 class="text-[13.5px] font-bold text-[#1E293B] flex items-center gap-2">
                                        <span class="w-1.5 h-4 rounded-full" style="background: linear-gradient(135deg, #0EA5E9, #0284C7);"></span>
                                        Attachments
                                    </h3>
                                    <span class="px-2 py-0.5 rounded-lg text-[11px] font-semibold bg-[#F1F5F9] text-[#64748B] border border-[#E2E8F0]">
                                        {{ $uploadedDocs->count() }} Berkas
                                    </span>
                                </div>
                                
                                <button type="button" @click="isUploadDocModalOpen = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 transition cursor-pointer border border-red-200 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    <span>ADD ATTACHMENT</span>
                                </button>
                            </div>

                            @if($uploadedDocs->count() > 0)
                                <div class="space-y-2">
                                    @foreach($uploadedDocs as $doc)
                                        <div class="p-3 rounded-xl border border-[#E2E8F0] bg-white flex items-center justify-between text-xs hover:border-[#CBD5E1] transition">
                                            <div class="flex items-center gap-2.5 min-w-0">
                                                <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-[#1E293B] truncate block">{{ $doc->document_title ?? ($doc->document_name ?? 'Attachment') }}</span>
                                                    @if($doc->file_name)
                                                        <span class="text-[11px] text-[#64748B] block truncate">{{ $doc->file_name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($doc->file_path)
                                                <div class="flex items-center gap-1.5 shrink-0">
                                                    <a href="{{ route('projects.documents.download', [$project->id, $doc->id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 transition no-underline" title="Download Berkas">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        <span>Download</span>
                                                    </a>
                                                    <form action="{{ route('projects.documents.delete', [$project->id, $doc->id]) }}" method="POST" onsubmit="return confirm('Hapus berkas lampiran ini?')" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition cursor-pointer" title="Hapus Berkas">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-3.5 rounded-xl border border-dashed border-[#CBD5E1] bg-[#F8FAFC]/50 text-center text-xs text-[#94A3B8]">
                                    Belum ada berkas lampiran. Klik tombol <span class="font-semibold text-[#8F0A0D]">ADD ATTACHMENT</span> untuk mengunggah BoQ atau dokumen pendukung.
                                </div>
                            @endif
                        </div>

                    </div>
                </div>

                {{-- ══ RIGHT COLUMN: 2 SEPARATE CARDS (col-span-4) ══ --}}
                <div class="lg:col-span-4 space-y-6">
                    
                    {{-- CARD 1: PROJECT STATUS --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <div class="text-[11px] font-bold text-[#64748B] uppercase tracking-wider">Project Status</div>
                        <div>
                            @php
                                $statusBadgeClass = match($currentStatus) {
                                    'Draft' => 'bg-gradient-to-r from-slate-600 to-slate-800 text-white',
                                    'Opportunity' => 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white',
                                    'In Progress' => 'bg-gradient-to-r from-amber-500 to-orange-600 text-white',
                                    'Pending' => 'bg-gradient-to-r from-purple-500 to-indigo-600 text-white',
                                    'Completed' => 'bg-gradient-to-r from-emerald-500 to-teal-600 text-white',
                                    default => 'bg-slate-700 text-white',
                                };
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-xl text-[11.5px] font-extrabold shadow-xs {{ $statusBadgeClass }}">
                                <span>{{ $currentStatus }}</span>
                            </span>
                        </div>
                    </div>

                    {{-- CARD 2: PROJECT ACTIVITIES --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-[13px] font-bold text-[#1E293B]">Project Activities</h3>
                            <span class="w-2 h-2 rounded-full bg-[#8F0A0D]/70 animate-pulse"></span>
                        </div>
                        
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
                                <div class="space-y-1 pt-2.5 border-t border-gray-100">
                                    <div class="font-normal text-gray-700">
                                        Handover ke PMO: <strong class="font-medium text-gray-900">{{ $project->pm->name }}</strong>
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        Delivery dipimpin oleh PMO
                                    </div>
                                </div>
                            @endif

                            @if(!empty($headApproval['approved']))
                                <div class="space-y-1 pt-2.5 border-t border-gray-100">
                                    <div class="font-normal text-gray-700">
                                        Approval Head: <strong class="font-medium text-gray-900">Pak Susanto</strong>
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ $headApproval['date'] ?? 'Disetujui' }}
                                    </div>
                                </div>
                            @endif

                            @if(!empty($directorApproval['approved']))
                                <div class="space-y-1 pt-2.5 border-t border-gray-100">
                                    <div class="font-normal text-gray-700">
                                        Approval Direktur: <strong class="font-medium text-gray-900">Pak Hariyadi</strong>
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ $directorApproval['date'] ?? 'Disahkan' }}
                                    </div>
                                </div>
                            @endif

                            @if($isBdmAssigned && !empty($bdmName))
                                <div class="space-y-1 pt-2.5 border-t border-gray-100">
                                    <div class="font-normal text-gray-700">
                                        Penunjukan PIC BD: <strong class="font-medium text-gray-900">{{ $bdmName }}</strong>
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        Product Manager / Verifikator Solusi
                                    </div>
                                </div>
                            @endif

                            @if(!empty($presalesAssignment['assigned']))
                                <div class="space-y-1 pt-2.5 border-t border-gray-100">
                                    <div class="font-normal text-gray-700">
                                        Penugasan Presales: <strong class="font-medium text-gray-900">{{ $presalesAssignment['assigned_to'] ?? 'Akbar' }}</strong>
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ $presalesAssignment['assigned_at'] ?? 'Ditugaskan' }} (oleh {{ $presalesAssignment['assigned_by'] ?? 'Sales' }})
                                    </div>
                                </div>
                            @endif

                            @if(!empty($architectAssignment['assigned']))
                                <div class="space-y-1 pt-2.5 border-t border-gray-100">
                                    <div class="font-normal text-gray-700">
                                        Penugasan Solution Architect: <strong class="font-medium text-gray-900">{{ $architectAssignment['assigned_to'] ?? 'Aris Sadewo' }}</strong>
                                    </div>
                                    <div class="text-[11px] text-gray-400">
                                        {{ $architectAssignment['assigned_at'] ?? 'Ditugaskan' }} (oleh {{ $architectAssignment['assigned_by'] ?? 'Sales' }})
                                    </div>
                                </div>
                            @endif

                            @if(!empty($presalesAssignment['document_path']))
                                <div class="space-y-1 pt-2.5 border-t border-gray-100">
                                    <div class="font-normal text-gray-700">
                                        Proposal Teknis &amp; BoQ diunggah oleh <strong class="font-medium text-gray-900">{{ $presalesAssignment['assigned_to'] ?? 'Pre-Sales' }}</strong>
                                    </div>
                                    <div class="text-[11px] text-emerald-600 font-semibold">
                                        ✓ Berkas Proposal &amp; SOW Terlampir ({{ $presalesAssignment['completed_at'] ?? 'Selesai' }})
                                    </div>
                                </div>
                            @endif

                            @if(!empty($architectAssignment['document_path']))
                                <div class="space-y-1 pt-2.5 border-t border-gray-100">
                                    <div class="font-normal text-gray-700">
                                        Desain Arsitektur &amp; Topologi diunggah oleh <strong class="font-medium text-gray-900">{{ $architectAssignment['assigned_to'] ?? 'Solution Architect' }}</strong>
                                    </div>
                                    <div class="text-[11px] text-emerald-600 font-semibold">
                                        ✓ Diagram Arsitektur Terlampir ({{ $architectAssignment['completed_at'] ?? 'Selesai' }})
                                    </div>
                                </div>
                            @endif

                            @if(!empty($bdVerification['status']) && $bdVerification['status'] !== 'Pending')
                                <div class="space-y-1 pt-2.5 border-t border-gray-100">
                                    <div class="font-normal text-gray-700">
                                        Verifikasi Solusi BD: <strong class="font-medium {{ $bdVerification['status'] === 'Approved' ? 'text-emerald-700' : 'text-rose-700' }}">{{ $bdVerification['status'] === 'Approved' ? 'Disetujui' : 'Perlu Revisi' }}</strong> oleh <strong class="font-medium text-gray-900">{{ $bdVerification['verified_by'] ?? ($bdmName ?: 'PIC BD') }}</strong>
                                    </div>
                                    <div class="text-[11px] {{ $bdVerification['status'] === 'Approved' ? 'text-emerald-600' : 'text-rose-600' }} font-semibold">
                                        {{ $bdVerification['verified_at'] ?? 'Selesai diverifikasi' }}
                                        @if(!empty($bdVerification['notes']))
                                            <div class="text-gray-500 font-normal italic mt-0.5">"{{ $bdVerification['notes'] }}"</div>
                                        @endif
                                    </div>
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
                    <h3 class="text-base font-bold text-gray-900">Add Milestone</h3>
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
                        <label class="block text-gray-700 mb-1 uppercase tracking-wider text-[10.5px]">PILIH BERKAS (BISA PILIH SEKALIGUS LEBIH DARI 1 BERKAS)</label>
                        <input type="file" name="document_files[]" multiple required 
                               class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer bg-gray-50 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-semibold file:bg-red-50 file:text-[#8F0A0D] hover:file:bg-red-100">
                        <p class="text-[10.5px] text-gray-400 mt-1">Format: PDF, XLSX, DOCX, ZIP, PNG, JPG (Dapat memilih sekaligus beberapa berkas)</p>
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

    {{-- MODAL ASSIGN APPROVAL KE PIMPINAN (HEAD & DIREKTUR) --}}
    <template x-teleport="body">
        <div x-show="isAssignModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-2xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isAssignModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900" 
                            x-text="assignRole === 'head' ? 'Assign Review ke Head Divisi' : (assignRole === 'director' ? 'Assign Otorisasi ke Direktur' : 'Assign Review ke Pimpinan')"></h3>
                        <p class="text-[11.5px] text-gray-500 mt-0.5">Tugaskan peninjauan draft proyek ke pimpinan yang berwenang</p>
                    </div>
                    <button type="button" @click="isAssignModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.assign_approver', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="role" :value="assignRole">

                    {{-- Pilihan Head Divisi --}}
                    <div x-show="assignRole === 'head' || assignRole === 'both'">
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[10.5px]">PILIH HEAD DIVISI (REVIEW TEKNIS)</label>
                        <select name="head_user_id" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white">
                            @foreach($leadershipUsers as $lu)
                                <option value="{{ $lu->id }}" {{ ($susantoUser && $susantoUser->id == $lu->id) ? 'selected' : '' }}>
                                    {{ $lu->name }} ({{ $lu->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pilihan Direktur --}}
                    <div x-show="assignRole === 'director' || assignRole === 'both'">
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[10.5px]">PILIH DIREKTUR (OTORISASI KONTRAK)</label>
                        <select name="director_user_id" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white">
                            @foreach($leadershipUsers as $lu)
                                <option value="{{ $lu->id }}" {{ ($hariyadiUser && $hariyadiUser->id == $lu->id) ? 'selected' : '' }}>
                                    {{ $lu->name }} ({{ $lu->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[10.5px]">CATATAN DARI SALES (OPSIONAL)</label>
                        <textarea name="notes" rows="3" 
                                  placeholder="Contoh: Mohon review kelayakan teknis jaringan dan validasi estimasi nilai kontrak untuk penawaran klien."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isAssignModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                            Tugaskan Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL EDIT PIPELINE SALES & OPPORTUNITY (STAGE, PROBABILITY, CLOSING DATE) --}}
    <template x-teleport="body">
        <div x-show="isEditPipelineModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-2xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isEditPipelineModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Ubah Stage &amp; Pipeline Sales</h3>
                        <p class="text-[11.5px] text-gray-500 mt-0.5">Perbarui progres tahapan prospek penjualan &amp; estimasi closing</p>
                    </div>
                    <button type="button" @click="isEditPipelineModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.update_pipeline', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf

                    <div>
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[10.5px]">SALES PIPELINE STAGE</label>
                        <select name="sales_stage" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white cursor-pointer font-bold">
                            @php
                                $stagesList = [
                                    'Qualification'        => '1. Qualification (Kualifikasi Awal)',
                                    'Discovery'            => '2. Discovery / Kebutuhan Klien',
                                    'Proposal / Quoting'   => '3. Proposal &amp; Quoting (Penawaran Resmi)',
                                    'Negotiation'          => '4. Negotiation / Pembahasan Kontrak',
                                    'Closed Won'           => '5. Closed Won (Menang / Deal)',
                                    'Closed Lost'          => '6. Closed Lost (Batal / Kalah Tender)',
                                ];
                                $currentSalesStage = $project->sales_stage ?: 'Qualification';
                            @endphp
                            @foreach($stagesList as $stKey => $stLabel)
                                <option value="{{ $stKey }}" {{ $currentSalesStage === $stKey ? 'selected' : '' }}>
                                    {!! $stLabel !!}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[10.5px]">WIN PROBABILITY (%)</label>
                            <select name="win_probability" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white cursor-pointer font-bold">
                                @foreach([10 => '10% (Tahap Awal)', 25 => '25% (Riset Spek)', 50 => '50% (Proposal Masuk)', 75 => '75% (Negosiasi Final)', 90 => '90% (Menunggu PO)', 100 => '100% (Deal / Won)'] as $pct => $pctLabel)
                                    <option value="{{ $pct }}" {{ ($project->win_probability ?: 10) == $pct ? 'selected' : '' }}>
                                        {{ $pctLabel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[10.5px]">TARGET CLOSING</label>
                            <input type="date" name="expected_closing_date" 
                                   value="{{ $project->expected_closing_date ? \Carbon\Carbon::parse($project->expected_closing_date)->format('Y-m-d') : '' }}" 
                                   class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[10.5px]">ESTIMASI NILAI PROYEK (RP)</label>
                        <input type="number" name="contract_value" value="{{ $project->contract_value ?: 0 }}" min="0" step="1000"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 font-bold">
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isEditPipelineModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                            Simpan Perubahan Stage
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL ASSIGN KE TIM SOLUSI (PIC BD, PRESALES & SOLUTION ARCHITECT) --}}
    <template x-teleport="body">
        <div x-show="isAssignTechnicalModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-2xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isAssignTechnicalModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900" 
                            x-text="assignTechnicalRole === 'bdm' ? 'Tunjuk PIC BD (Product Manager)' : (assignTechnicalRole === 'presales' ? 'Tugaskan Pre-Sales Specialist' : (assignTechnicalRole === 'architect' ? 'Tugaskan Solution Architect' : 'Tugaskan Tim Solusi &amp; BD'))"></h3>
                        <p class="text-[11.5px] text-gray-500 mt-0.5">Penugasan PIC BD verifikator, penyusun proposal, dan perancang topologi</p>
                    </div>
                    <button type="button" @click="isAssignTechnicalModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.assign_technical', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="role" :value="assignTechnicalRole">

                    {{-- Pilihan PIC BD --}}
                    <div x-show="assignTechnicalRole === 'bdm' || assignTechnicalRole === 'all' || assignTechnicalRole === 'both'">
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[10.5px]">PILIH PIC BUSINESS DEVELOPMENT (PRODUCT MANAGER &amp; VERIFIKATOR)</label>
                        <select name="bdm_user_id" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white cursor-pointer">
                            <option value="">-- Pilih PIC Business Development --</option>
                            @foreach($bdmUsers as $bu)
                                <option value="{{ $bu->id }}" {{ ($project->bdm_id == $bu->id || (isset($bdmAssignment['assigned_user_id']) && $bdmAssignment['assigned_user_id'] == $bu->id)) ? 'selected' : '' }}>
                                    (BD) {{ $bu->name }} ({{ $bu->email }})
                                </option>
                            @endforeach
                            @php
                                $otherUsersBD = ($allUsers ?? \App\Models\User::orderBy('name')->get())->whereNotIn('id', $bdmUsers->pluck('id'));
                            @endphp
                            @foreach($otherUsersBD as $ou)
                                <option value="{{ $ou->id }}" {{ ($project->bdm_id == $ou->id) ? 'selected' : '' }}>
                                    {{ $ou->name }} ({{ $ou->email }})
                                </option>
                            @endforeach
                        </select>
                        <span class="text-[10px] text-gray-400 mt-1 block">PIC BD akan menerima notifikasi dan memvalidasi berkas proposal sebelum diajukan ke klien.</span>
                    </div>

                    {{-- Pilihan Pre-Sales --}}
                    <div x-show="assignTechnicalRole === 'presales' || assignTechnicalRole === 'all' || assignTechnicalRole === 'both'">
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[10.5px]">PILIH PRE-SALES SPECIALIST (PROPOSAL &amp; BOQ)</label>
                        <select name="presales_user_id" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white cursor-pointer">
                            @foreach($presalesUsers as $pu)
                                <option value="{{ $pu->id }}" {{ (isset($presalesAssignment['assigned_user_id']) && $presalesAssignment['assigned_user_id'] == $pu->id) || str_contains(strtolower($pu->name), 'akbar') ? 'selected' : '' }}>
                                    (Pre-Sales) {{ $pu->name }} ({{ $pu->email }})
                                </option>
                            @endforeach
                            @php
                                $otherUsersPS = ($allUsers ?? \App\Models\User::orderBy('name')->get())->whereNotIn('id', $presalesUsers->pluck('id'));
                            @endphp
                            @foreach($otherUsersPS as $ou)
                                <option value="{{ $ou->id }}" {{ (isset($presalesAssignment['assigned_user_id']) && $presalesAssignment['assigned_user_id'] == $ou->id) ? 'selected' : '' }}>
                                    {{ $ou->name }} ({{ $ou->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Pilihan Solution Architect --}}
                    <div x-show="assignTechnicalRole === 'architect' || assignTechnicalRole === 'all' || assignTechnicalRole === 'both'">
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[10.5px]">PILIH SOLUTION ARCHITECT (DESAIN TOPOLOGI &amp; SIZING)</label>
                        <select name="architect_user_id" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white cursor-pointer">
                            @foreach($architectUsers as $au)
                                <option value="{{ $au->id }}" {{ (isset($architectAssignment['assigned_user_id']) && $architectAssignment['assigned_user_id'] == $au->id) || str_contains(strtolower($au->name), 'aris') ? 'selected' : '' }}>
                                    (Solution Architect) {{ $au->name }} ({{ $au->email }})
                                </option>
                            @endforeach
                            @php
                                $otherUsersSA = ($allUsers ?? \App\Models\User::orderBy('name')->get())->whereNotIn('id', $architectUsers->pluck('id'));
                            @endphp
                            @foreach($otherUsersSA as $ou)
                                <option value="{{ $ou->id }}" {{ (isset($architectAssignment['assigned_user_id']) && $architectAssignment['assigned_user_id'] == $ou->id) ? 'selected' : '' }}>
                                    {{ $ou->name }} ({{ $ou->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1.5 uppercase tracking-wider text-[10.5px]">INSTRUKSI &amp; CATATAN TEKNIS SALES</label>
                        <textarea name="notes" rows="3" 
                                  placeholder="Contoh: Tolong buatkan desain topologi redundant switch &amp; estimasi BoQ untuk kebutuhan penawaran tender klien."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isAssignTechnicalModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                            Simpan Penugasan Tim
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL VERIFIKASI SOLUSI OLEH PIC BD (APPROVE / REVISION) --}}
    <template x-teleport="body">
        <div x-show="isVerifyTechnicalModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-2xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isVerifyTechnicalModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 space-y-4 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Verifikasi Kelayakan Dokumen Solusi</h3>
                        <p class="text-[11.5px] text-gray-500 mt-0.5">Tinjau kesiapan proposal teknis, BoQ, dan desain topologi</p>
                    </div>
                    <button type="button" @click="isVerifyTechnicalModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.verify_technical', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                    @csrf

                    {{-- Ringkasan Berkas yang Terunggah --}}
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 space-y-2 text-[11.5px]">
                        <div class="font-bold text-gray-800 text-xs">Berkas yang Divalidasi:</div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Proposal &amp; BoQ (Pre-Sales):</span>
                            <span class="font-bold {{ $isPresalesDone ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $isPresalesDone ? ($presalesAssignment['document_name'] ?? 'Terunggah') : 'Belum Terunggah' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Desain Topologi (Solution Architect):</span>
                            <span class="font-bold {{ $isArchitectDone ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $isArchitectDone ? ($architectAssignment['document_name'] ?? 'Terunggah') : 'Belum Terunggah' }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-2 uppercase tracking-wider text-[10.5px]">KEPUTUSAN VERIFIKASI BD</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="p-3.5 rounded-xl border-2 border-emerald-200 bg-emerald-50/50 hover:bg-emerald-50 cursor-pointer flex flex-col justify-between transition">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="decision" value="approved" checked class="text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                    <span class="font-extrabold text-emerald-900 text-xs">✓ Disetujui (Approve)</span>
                                </div>
                                <p class="text-[10px] text-emerald-700 mt-1 pl-5">Dokumen sah dan diteruskan ke Sales untuk dikirim ke klien.</p>
                            </label>

                            <label class="p-3.5 rounded-xl border-2 border-rose-200 bg-rose-50/50 hover:bg-rose-50 cursor-pointer flex flex-col justify-between transition">
                                <div class="flex items-center gap-2">
                                    <input type="radio" name="decision" value="revision" class="text-rose-600 focus:ring-rose-500 cursor-pointer">
                                    <span class="font-extrabold text-rose-900 text-xs">⚠ Perlu Revisi</span>
                                </div>
                                <p class="text-[10px] text-rose-700 mt-1 pl-5">Kembalikan ke Presales &amp; SA dengan catatan revisi.</p>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1 uppercase tracking-wider text-[10.5px]">CATATAN / FEEDBACK VERIFIKASI (WAJIB DIISI JIKA REVISI)</label>
                        <textarea name="notes" rows="3" 
                                  placeholder="Contoh jika Disetujui: Spek BoQ dan margin sudah sesuai standar komersial.&#10;Contoh jika Revisi: BoQ switch core perlu disesuaikan dengan spek diskon terbaru."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isVerifyTechnicalModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                            Simpan Keputusan Verifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL UNGGAH BERKAS SOLUSI TEKNIS (PRESALES / SA) --}}
    <template x-teleport="body">
        <div x-show="isUploadTechnicalDocModalOpen" x-cloak 
             class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-black/50 backdrop-blur-2xs overflow-y-auto"
             style="display: none;">
            <div @click.away="isUploadTechnicalDocModalOpen = false" 
                 class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-100 space-y-4 m-auto">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <h3 class="text-base font-bold text-gray-900" 
                            x-text="uploadTechnicalRole === 'presales' ? 'Unggah Berkas Proposal &amp; BoQ (Pre-Sales)' : 'Unggah Desain Arsitektur &amp; Topologi (Solution Architect)'"></h3>
                        <p class="text-[11.5px] text-gray-500 mt-0.5">Unggah berkas dokumen teknis pendukung solusi proyek</p>
                    </div>
                    <button type="button" @click="isUploadTechnicalDocModalOpen = false" class="text-gray-400 hover:text-gray-700 text-lg font-bold cursor-pointer">✕</button>
                </div>

                <form action="{{ route('projects.upload_technical_doc', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                    @csrf
                    <input type="hidden" name="role_type" :value="uploadTechnicalRole">

                    <div>
                        <label class="block text-gray-700 mb-1 uppercase tracking-wider text-[10.5px]">NAMA / JUDUL DOKUMEN</label>
                        <input type="text" name="document_title" 
                               :placeholder="uploadTechnicalRole === 'presales' ? 'Contoh: Proposal Teknis &amp; BoQ Estimasi Rev 1' : 'Contoh: Diagram Topologi Arsitektur &amp; Sizing Switch'" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1 uppercase tracking-wider text-[10.5px]">PILIH BERKAS (BISA PILIH SEKALIGUS LEBIH DARI 1 BERKAS)</label>
                        <input type="file" name="document_files[]" multiple required
                               class="w-full text-xs text-gray-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-[#8F0A0D] hover:file:bg-red-100 cursor-pointer border border-gray-200 rounded-xl p-2 bg-gray-50/50">
                        <span class="text-[10px] text-gray-400 mt-1 block">Format: PDF, Word (DOCX), Excel (XLSX), Visio (VSDX), atau Gambar (PNG/JPG). Maks 50MB per file.</span>
                    </div>

                    <div>
                        <label class="block text-gray-700 mb-1 uppercase tracking-wider text-[10.5px]">CATATAN / RINGKASAN TEKNIS (OPSIONAL)</label>
                        <textarea name="notes" rows="2" 
                                  placeholder="Contoh: Dokumen telah disesuaikan dengan spek tender dan estimasi diskon prinsipal."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs text-gray-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t">
                        <button type="button" @click="isUploadTechnicalDocModalOpen = false" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                            Unggah Dokumen Teknis
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    {{-- MODAL KONFIRMASI HAPUS (SESUAI LEAD ENGINEER) --}}
    <template x-teleport="body">
        <div x-show="isDeleteModalOpen" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[99999] bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-4"
             @click.self="isDeleteModalOpen = false"
             @keydown.escape.window="isDeleteModalOpen = false">
            <div class="bg-white rounded-2xl w-[420px] max-w-full p-6 text-left shadow-[0_20px_60px_rgba(15,23,42,0.25)] border border-[#E2E8F0] animate-fade-in-up">
                <div class="w-12 h-12 rounded-full bg-[#FEF2F2] flex items-center justify-center mx-auto mb-4 text-[#8F0A0D]">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                
                <h3 class="text-center font-display text-[16px] font-bold text-[#1E293B] mb-1.5">Yakin Hapus Project?</h3>
                <p class="text-center text-[12.5px] text-[#64748B] mb-6 break-words">
                    Project <span class="font-semibold text-[#1E293B]">"{{ $project->name }}"</span> beserta seluruh task dan milestone terkait akan dihapus secara permanen.
                </p>

                <div class="flex gap-2.5">
                    <button type="button" @click="isDeleteModalOpen = false"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-white text-[#334155] border border-[#CBD5E1] font-bold text-[12.5px] hover:bg-[#F8FAFC] transition cursor-pointer text-center">
                        Batal
                    </button>
                    <button type="button" @click="document.getElementById('deleteProjForm').submit()"
                            class="flex-1 py-2.5 px-4 rounded-xl btn-ipnet-gradient font-bold text-[12.5px] transition cursor-pointer shadow-md text-white text-center">
                        Ya, Hapus
                    </button>
                </div>
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

            isAssignModalOpen: false,
            assignRole: 'both', // 'head', 'director', 'both'

            isEditPipelineModalOpen: false,
            isAssignTechnicalModalOpen: false,
            assignTechnicalRole: 'all', // 'bdm', 'presales', 'architect', 'all'
            isUploadTechnicalDocModalOpen: false,
            uploadTechnicalRole: 'presales', // 'presales' or 'architect'
            isVerifyTechnicalModalOpen: false,

            isEditMetaModalOpen: false,
            isAddMilestoneModalOpen: false,
            isUploadDocModalOpen: false,
            isDeleteModalOpen: false,

            openAssignModal(role = 'both') {
                this.assignRole = role;
                this.isAssignModalOpen = true;
            },

            openApproveModal(role = 'head') {
                this.approveRole = role;
                this.isApproveModalOpen = true;
            },

            openEditPipelineModal() {
                this.isEditPipelineModalOpen = true;
            },

            openAssignTechnicalModal(role = 'all') {
                this.assignTechnicalRole = role;
                this.isAssignTechnicalModalOpen = true;
            },

            openUploadTechnicalModal(role = 'presales') {
                this.uploadTechnicalRole = role;
                this.isUploadTechnicalDocModalOpen = true;
            },

            openVerifyTechnicalModal() {
                this.isVerifyTechnicalModalOpen = true;
            },

            selectStage(tabKey) {
                this.activeStageTab = tabKey;
                const newStatus = this.stageNameToDbStatus(tabKey);
                this.currentStatus = newStatus;

                fetch('{{ route("projects.stage_update", $project->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ status: newStatus })
                }).catch(err => console.error('Stage update error:', err));
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
                this.isDeleteModalOpen = true;
            }
        };
    }
</script>
@endsection
