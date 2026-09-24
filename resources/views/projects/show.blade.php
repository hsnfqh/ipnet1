@extends('layouts.app')

@section('title', $project->name . ' - Detail Proyek')

@push('styles')
<style>
    .ipnet-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 16px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04), 0 1px 2px -1px rgba(0, 0, 0, 0.04);
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .ipnet-card:hover {
        border-color: #CBD5E1;
    }
    .text-ipnet-red {
        color: #8F0A0D;
    }
    .btn-ipnet-primary {
        background: linear-gradient(135deg, #8F0A0D 0%, #B81525 100%);
        color: #FFFFFF;
        box-shadow: 0 2px 4px rgba(143, 10, 13, 0.2);
        transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .btn-ipnet-primary:hover {
        background: linear-gradient(135deg, #7A080A 0%, #A0121F 100%);
        box-shadow: 0 4px 10px rgba(143, 10, 13, 0.3);
        color: #FFFFFF;
    }
    .btn-ipnet-secondary {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        color: #334155;
        transition: all 0.15s ease;
    }
    .btn-ipnet-secondary:hover {
        background-color: #F8FAFC;
        border-color: #CBD5E1;
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

    // Hak otorisasi unggah berkas teknis solusi
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

<script>
    window.openModal = function(modalId) {
        const el = document.getElementById(modalId);
        if (el) {
            el.style.setProperty('display', 'flex', 'important');
            el.removeAttribute('x-cloak');
            el.classList.remove('hidden');
        }
    };

    window.closeModal = function(modalId) {
        const el = document.getElementById(modalId);
        if (el) {
            el.style.setProperty('display', 'none', 'important');
            el.classList.add('hidden');
        }
    };

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
                window.openModal('modal-assign');
            },

            openApproveModal(role = 'head') {
                this.approveRole = role;
                this.isApproveModalOpen = true;
                window.openModal('modal-approve');
            },

            openEditPipelineModal() {
                this.isEditPipelineModalOpen = true;
                window.openModal('modal-edit-pipeline');
            },

            openAssignTechnicalModal(role = 'all') {
                this.assignTechnicalRole = role;
                this.isAssignTechnicalModalOpen = true;
                window.openModal('modal-assign-technical');
            },

            openUploadTechnicalModal(role = 'presales') {
                this.uploadTechnicalRole = role;
                this.isUploadTechnicalDocModalOpen = true;
                window.openModal('modal-upload-technical-doc');
            },

            openVerifyTechnicalModal() {
                this.isVerifyTechnicalModalOpen = true;
                window.openModal('modal-verify-technical');
            },

            openEditMetaModal() {
                this.isEditMetaModalOpen = true;
                window.openModal('modal-edit-meta');
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
                window.openModal('modal-delete');
            }
        };
    }
    window.projectDetailPage = projectDetailPage;

    if (window.Alpine) {
        Alpine.data('projectDetailPage', (initialStage, currentDbStatus) => projectDetailPage(initialStage, currentDbStatus));
    } else {
        document.addEventListener('alpine:init', () => {
            Alpine.data('projectDetailPage', (initialStage, currentDbStatus) => projectDetailPage(initialStage, currentDbStatus));
        });
    }
</script>

<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans text-slate-800" 
     x-data="projectDetailPage('{{ $initialStage ?? 'draft' }}', '{{ $currentStatus }}')">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Detail Proyek'])
        
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold px-2 cursor-pointer">✕</button>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-2 cursor-pointer">✕</button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1.5 shadow-sm">
                    <div class="flex items-center justify-between font-bold text-rose-900">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Terjadi kendala pada formulir:</span>
                        </div>
                        <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-rose-500 hover:text-rose-700 font-bold px-2 cursor-pointer">✕</button>
                    </div>
                    <ul class="list-disc list-inside pl-6 text-[11.5px] space-y-0.5 text-rose-700">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 1. BREADCRUMBS & TOP NAVIGATION --}}
            <nav class="flex items-center gap-2 text-xs font-medium text-slate-500">
                <a href="{{ route('sales.pipeline.index') }}" class="hover:text-[#8F0A0D] transition text-slate-600 font-semibold flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Pipeline Sales</span>
                </a>
                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-slate-800 font-semibold truncate max-w-md">{{ $project->name }}</span>
            </nav>

            {{-- 2. EXECUTIVE HERO BANNER & METRICS --}}
            <div class="ipnet-card p-6 sm:p-7 space-y-6">
                
                {{-- A. Top Header Strip --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200 uppercase tracking-wider">
                            {{ $project->client_department ?: 'IPNET 01' }}
                        </span>
                        <span class="text-slate-300">|</span>
                        <span class="text-xs text-slate-500">
                            Dibuat oleh <strong class="text-slate-800 font-semibold">{{ $project->creator ? $project->creator->name : ($project->sales_name ?: 'Sales Team') }}</strong>
                        </span>
                        <span class="text-slate-400 text-[11px]">
                            ({{ \Carbon\Carbon::parse($project->created_at)->format('d M Y H:i') }})
                        </span>
                    </div>

                    {{-- Status Pill --}}
                    @php
                        $statusBadgeClass = match($currentStatus) {
                            'Draft'       => 'bg-slate-100 text-slate-700 border-slate-200',
                            'Opportunity' => 'bg-sky-50 text-sky-700 border-sky-200',
                            'In Progress' => 'bg-amber-50 text-amber-800 border-amber-200',
                            'Pending'     => 'bg-purple-50 text-purple-700 border-purple-200',
                            'Completed'   => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            default       => 'bg-slate-100 text-slate-700 border-slate-200',
                        };
                    @endphp
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $statusBadgeClass }} shadow-2xs">
                        <span class="w-1.5 h-1.5 rounded-full {{ $currentStatus === 'Completed' ? 'bg-emerald-500' : ($currentStatus === 'In Progress' ? 'bg-amber-500' : ($currentStatus === 'Opportunity' ? 'bg-sky-500' : 'bg-slate-400')) }}"></span>
                        <span>{{ $currentStatus }}</span>
                    </div>
                </div>

                {{-- B. Project Title & Quick Actions --}}
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                    <div class="space-y-1">
                        <h1 class="text-2xl sm:text-[26px] font-extrabold text-slate-900 tracking-tight leading-tight">
                            {{ $project->name }}
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-3xl">
                            {{ $project->description ?: 'Proyek pengadaan infrastruktur dan solusi teknologi terintegrasi.' }}
                        </p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2 shrink-0">
                        @if($currentStatus === 'In Progress')
                            <form action="{{ route('projects.stage_update', $project->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="Completed">
                                <button type="submit" onclick="return confirm('Tandai proyek {{ addslashes($project->name) }} sebagai Selesai (Completed)?')"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <span>Tandai Selesai</span>
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('projects.stage_update', $project->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status" value="{{ $currentStatus === 'Pending' ? 'In Progress' : 'Pending' }}">
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-600 hover:text-[#8F0A0D] hover:bg-red-50 hover:border-red-200 transition cursor-pointer shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9" stroke-width="2"></circle><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5l3 2"/></svg>
                                <span>{{ $currentStatus === 'Pending' ? 'Resume' : 'Pending' }}</span>
                            </button>
                        </form>

                        <button type="button" @click="confirmDeleteProject()" class="p-2 rounded-xl border border-slate-200 bg-white text-slate-400 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition cursor-pointer shadow-2xs" title="Hapus Proyek">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>

                {{-- C. Project Estimation Banner (Clean Metric Strip) --}}
                <div class="p-4 sm:p-5 rounded-xl bg-slate-50/70 border border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 flex-1">
                        <div>
                            <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">NILAI ESTIMASI PROYEK</span>
                            <div class="text-[20px] font-extrabold text-[#8F0A0D] tracking-tight mt-0.5">
                                Rp {{ number_format($project->contract_value ?: 0, 0, ',', '.') }}
                            </div>
                        </div>

                        <div>
                            <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">PROJECT START</span>
                            <div class="text-xs font-bold text-slate-800 mt-1.5 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : '-' }}</span>
                            </div>
                        </div>

                        <div>
                            <span class="text-[10.5px] font-bold text-slate-400 uppercase tracking-wider block">ESTIMASI SELESAI / CLOSING</span>
                            <div class="text-xs font-bold text-slate-800 mt-1.5 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : ($project->expected_closing_date ? \Carbon\Carbon::parse($project->expected_closing_date)->format('d M Y') : '-') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200">
                        <button type="button" 
                                @click="isEditMetaModalOpen = true; openEditMetaModal()" 
                                onclick="window.openModal('modal-edit-meta')"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold transition cursor-pointer shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span>Edit Estimasi</span>
                        </button>
                    </div>
                </div>

            </div>

            {{-- 3. MAIN 2-COLUMN STRUCTURE (Lead Engineer Dashboard Layout) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                {{-- ══ LEFT MAIN COLUMN (lg:col-span-8) ══ --}}
                <div class="lg:col-span-8 space-y-6">
                    
                    {{-- WORKFLOW SECTION BY PROJECT STATUS --}}
                    @if($currentStatus === 'Opportunity')
                        {{-- 1. Pipeline Sales & Opportunity Card --}}
                        <div class="ipnet-card p-6 space-y-4">
                            <div class="flex items-center justify-between flex-wrap gap-2 border-b border-slate-100 pb-3">
                                <div>
                                    <p class="text-[#8F0A0D] text-[11px] font-bold inline-flex items-center uppercase tracking-wider mb-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D] inline-block mr-1.5"></span> PIPELINE &amp; PROSPEK PENJUALAN
                                    </p>
                                    <h3 class="text-sm font-bold text-slate-900">Tahapan Sales &amp; Estimasi Closing</h3>
                                </div>
                                <button type="button" 
                                        @click="isEditPipelineModalOpen = true; openEditPipelineModal()" 
                                        onclick="window.openModal('modal-edit-pipeline')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-700 bg-slate-50 hover:bg-slate-100 border border-slate-200 transition cursor-pointer shadow-2xs">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Edit Stage &amp; Prospek</span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/80 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">STAGE SAAT INI</span>
                                        <strong class="text-xs font-bold text-slate-900 mt-0.5 block">{{ $project->sales_stage ?: 'Qualification' }}</strong>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                        Active Stage
                                    </span>
                                </div>

                                <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/80 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">WIN PROBABILITY</span>
                                        <div class="text-xs font-extrabold text-[#8F0A0D] mt-0.5">
                                            {{ $project->win_probability ?: 10 }}% Peluang
                                        </div>
                                    </div>
                                    <div class="w-16 bg-slate-200 h-2 rounded-full overflow-hidden">
                                        <div class="bg-gradient-to-r from-[#DC2626] to-[#8F0A0D] h-full rounded-full" style="width: {{ min(100, max(5, $project->win_probability ?: 10)) }}%"></div>
                                    </div>
                                </div>

                                <div class="p-3.5 bg-slate-50/70 rounded-xl border border-slate-200/80 flex items-center justify-between">
                                    <div>
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TARGET CLOSING</span>
                                        <strong class="text-xs font-bold text-slate-900 mt-0.5 block">{{ $project->expected_closing_date ? \Carbon\Carbon::parse($project->expected_closing_date)->format('d M Y') : 'Belum Ditentukan' }}</strong>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Kolaborasi Tim Solusi (Clean 3-Card Grid - Executive Architecture) --}}
                        <div class="ipnet-card p-6 space-y-5">
                            
                            {{-- Header & Status --}}
                            <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-3">
                                <div>
                                    <p class="text-[#8F0A0D] text-[11px] font-bold inline-flex items-center uppercase tracking-wider mb-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D] inline-block mr-1.5"></span> TIM SOLUSI TEKNIS
                                    </p>
                                    <h3 class="text-sm sm:text-base font-bold text-slate-900">Kolaborasi Tim Solusi (BD, Pre-Sales &amp; Solution Architect)</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Workflow verifikasi kelayakan teknis, proposal SOW &amp; desain topologi arsitektur.</p>
                                </div>
                            </div>

                            {{-- 3 Collaborative Person Workspace Cards --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs items-stretch">
                                
                                {{-- ══ CARD 1: BUSINESS DEVELOPMENT (PIC / PM) ══ --}}
                                <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition">
                                    <div class="space-y-3">
                                        
                                        {{-- Header --}}
                                        <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider whitespace-nowrap">
                                                PIC BD
                                            </span>
                                            @php
                                                $bdBadgeClass = match($bdVerification['status'] ?? '') {
                                                    'Approved'            => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                    'Revision Needed'     => 'bg-rose-50 text-rose-700 border-rose-200',
                                                    'Pending Verification'=> 'bg-amber-50 text-amber-800 border-amber-300',
                                                    'Waiting Uploads'     => 'bg-slate-100 text-slate-700 border-slate-200',
                                                    default               => 'bg-slate-100 text-slate-500 border-slate-200',
                                                };
                                                $bdBadgeLabel = match($bdVerification['status'] ?? '') {
                                                    'Approved'            => '✓ Disetujui BD',
                                                    'Revision Needed'     => '⚠ Perlu Revisi',
                                                    'Pending Verification'=> 'Menunggu Verifikasi',
                                                    'Waiting Uploads'     => 'Menunggu Berkas',
                                                    default               => 'Belum Di-assign',
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $bdBadgeClass }}">
                                                {{ $bdBadgeLabel }}
                                            </span>
                                        </div>

                                        {{-- Person --}}
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                                {{ $isBdmAssigned && !empty($bdmName) ? strtoupper(substr($bdmName, 0, 2)) : 'BD' }}
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="font-bold text-xs {{ $isBdmAssigned ? 'text-slate-900' : 'text-slate-400 italic' }} truncate" title="{{ $isBdmAssigned && !empty($bdmName) ? $bdmName : '' }}">
                                                    {{ $isBdmAssigned && !empty($bdmName) ? $bdmName : 'Belum Ditugaskan' }}
                                                </h4>
                                                <p class="text-[10.5px] text-slate-500 truncate">Product Manager &amp; Verifikator</p>
                                            </div>
                                        </div>

                                        {{-- Verification Result Box --}}
                                        <div>
                                            @if(($bdVerification['status'] ?? '') === 'Approved')
                                                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-2">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <div class="flex items-center gap-2 min-w-0 flex-1">
                                                            <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <div class="font-bold text-slate-900 text-xs truncate">Proposal Disetujui</div>
                                                                <div class="text-[10px] text-slate-500 truncate">{{ !empty($bdVerification['notes']) ? '"' . $bdVerification['notes'] . '"' : 'Verifikasi Valid & Lolos' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if(!empty($bdVerification['verified_at']))
                                                        <div class="text-[10px] text-slate-400 pt-1.5 border-t border-slate-200/80 text-right font-mono">
                                                            {{ $bdVerification['verified_at'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif(($bdVerification['status'] ?? '') === 'Revision Needed')
                                                <div class="p-3 rounded-lg bg-rose-50/80 border border-rose-200 text-rose-900 space-y-1">
                                                    <div class="font-bold text-[11px] text-rose-800">Catatan Revisi:</div>
                                                    <div class="text-[11px] text-rose-950 bg-white/90 p-2 rounded border border-rose-100 italic">
                                                        "{{ $bdVerification['notes'] ?? 'Mohon lakukan perbaikan.' }}"
                                                    </div>
                                                </div>
                                            @elseif(($bdVerification['status'] ?? '') === 'Pending Verification')
                                                <div class="p-3 rounded-lg bg-amber-50/70 border border-amber-200 text-amber-900 space-y-1">
                                                    <div class="font-bold text-[11px] text-amber-800">Menunggu Verifikasi</div>
                                                    <p class="text-[10.5px] text-amber-800 leading-relaxed">Berkas telah siap untuk ditinjau oleh PIC BD.</p>
                                                </div>
                                            @elseif($isBdmAssigned)
                                                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 text-slate-500 text-[10.5px]">
                                                    Menunggu upload dari Pre-Sales &amp; SA.
                                                </div>
                                            @else
                                                <div class="p-3 rounded-lg border border-dashed border-slate-200 text-slate-400 text-center text-[10.5px]">
                                                    Belum ada PIC BD yang ditunjuk.
                                                </div>
                                            @endif
                                        </div>

                                    </div>

                                    {{-- Actions Footer --}}
                                    @if(!$isBdmAssigned || ($canVerifyBD && ($isPresalesDone || $isArchitectDone)))
                                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                                            @if(!$isBdmAssigned)
                                                <button type="button" @click="openAssignTechnicalModal('bdm')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    <span>Tunjuk PIC BD</span>
                                                </button>
                                            @endif

                                            @if($canVerifyBD && ($isPresalesDone || $isArchitectDone))
                                                <button type="button" @click="openVerifyTechnicalModal()" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-white btn-ipnet-primary shadow-xs cursor-pointer ml-auto">
                                                    <span>Verifikasi Dokumen</span>
                                                </button>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                {{-- ══ CARD 2: PRE-SALES SPECIALIST ══ --}}
                                <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition">
                                    <div class="space-y-3">
                                        
                                        {{-- Header --}}
                                        <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider whitespace-nowrap">
                                                PRE-SALES
                                            </span>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $isPresalesDone ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($isPresalesAssigned ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200') }}">
                                                {{ $isPresalesDone ? '✓ Berkas Diunggah' : ($isPresalesAssigned ? 'Menunggu Proposal' : 'Belum Di-assign') }}
                                            </span>
                                        </div>

                                        {{-- Person --}}
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                                {{ $isPresalesAssigned && !empty($presalesAssignment['assigned_to']) ? strtoupper(substr($presalesAssignment['assigned_to'], 0, 2)) : 'PS' }}
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="font-bold text-xs {{ $isPresalesAssigned && !empty($presalesAssignment['assigned_to']) ? 'text-slate-900' : 'text-slate-400 italic' }} truncate" title="{{ $isPresalesAssigned && !empty($presalesAssignment['assigned_to']) ? $presalesAssignment['assigned_to'] : '' }}">
                                                    {{ $isPresalesAssigned && !empty($presalesAssignment['assigned_to']) ? $presalesAssignment['assigned_to'] : 'Belum Ditugaskan' }}
                                                </h4>
                                                <p class="text-[10.5px] text-slate-500 truncate">Proposal &amp; BoQ Proyek</p>
                                            </div>
                                        </div>

                                        {{-- Document Deliverable Box --}}
                                        <div>
                                            @if($isPresalesDone)
                                                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-2">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <div class="flex items-center gap-2 min-w-0 flex-1">
                                                            <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <div class="font-bold text-slate-900 text-xs truncate" title="{{ $presalesAssignment['document_title'] ?? 'Proposal Teknis' }}">{{ $presalesAssignment['document_title'] ?? 'Proposal Teknis' }}</div>
                                                                <div class="text-[10px] text-slate-500 font-mono truncate" title="{{ $presalesAssignment['document_name'] ?? 'Berkas terlampir' }}">{{ $presalesAssignment['document_name'] ?? 'Berkas terlampir' }}</div>
                                                            </div>
                                                        </div>
                                                        @if(!empty($presalesAssignment['document_path']))
                                                            <a href="{{ asset('storage/' . $presalesAssignment['document_path']) }}" target="_blank" 
                                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition shrink-0 shadow-2xs" title="Unduh Proposal">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                                <span>Unduh</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                    @if(!empty($presalesAssignment['completed_at']))
                                                        <div class="text-[10px] text-slate-400 pt-1.5 border-t border-slate-200/80 text-right font-mono">
                                                            {{ $presalesAssignment['completed_at'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($isPresalesAssigned)
                                                <div class="p-3 rounded-lg bg-amber-50/70 border border-amber-200 text-amber-900 space-y-1">
                                                    <div class="font-medium italic text-[11px]">"{{ !empty($presalesAssignment['sales_notes']) ? $presalesAssignment['sales_notes'] : 'Mohon dibuatkan proposal teknis.' }}"</div>
                                                    <div class="text-[10px] text-amber-700 mt-1">Ditugaskan: {{ $presalesAssignment['assigned_at'] ?? '-' }}</div>
                                                </div>
                                            @else
                                                <div class="p-3 rounded-lg border border-dashed border-slate-200 text-slate-400 text-center text-[10.5px]">
                                                    Belum ada penugasan Pre-Sales.
                                                </div>
                                            @endif
                                        </div>

                                    </div>

                                    {{-- Actions Footer --}}
                                    @if(!$isPresalesAssigned)
                                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                                            <button type="button" @click="openAssignTechnicalModal('presales')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                <span>Tugaskan Presales</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                {{-- ══ CARD 3: SOLUTION ARCHITECT ══ --}}
                                <div class="p-4 rounded-xl bg-white border border-slate-200 shadow-2xs flex flex-col justify-between space-y-3.5 hover:border-slate-300 transition">
                                    <div class="space-y-3">
                                        
                                        {{-- Header --}}
                                        <div class="flex items-center justify-between gap-1 pb-2 border-b border-slate-100">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200 uppercase tracking-wider whitespace-nowrap">
                                                SOL. ARCHITECT
                                            </span>
                                            @php
                                                $isBdApproved = (($bdVerification['status'] ?? '') === 'Approved');
                                                $saIsCompleted = $isArchitectDone || ($isBdApproved && $isArchitectAssigned);
                                            @endphp
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border whitespace-nowrap {{ $saIsCompleted ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($isArchitectAssigned ? 'bg-amber-50 text-amber-800 border-amber-200' : 'bg-slate-100 text-slate-500 border-slate-200') }}">
                                                {{ $isArchitectDone ? '✓ Desain Diunggah' : ($isBdApproved && $isArchitectAssigned ? '✓ Solusi Disahkan' : ($isArchitectAssigned ? 'Menunggu Desain' : 'Belum Di-assign')) }}
                                            </span>
                                        </div>

                                        {{-- Person --}}
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] text-white font-extrabold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                                {{ $isArchitectAssigned && !empty($architectAssignment['assigned_to']) ? strtoupper(substr($architectAssignment['assigned_to'], 0, 2)) : 'SA' }}
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <h4 class="font-bold text-xs {{ $isArchitectAssigned && !empty($architectAssignment['assigned_to']) ? 'text-slate-900' : 'text-slate-400 italic' }} truncate" title="{{ $isArchitectAssigned && !empty($architectAssignment['assigned_to']) ? $architectAssignment['assigned_to'] : '' }}">
                                                    {{ $isArchitectAssigned && !empty($architectAssignment['assigned_to']) ? $architectAssignment['assigned_to'] : 'Belum Ditugaskan' }}
                                                </h4>
                                                <p class="text-[10.5px] text-slate-500 truncate">Desain Topologi Arsitektur</p>
                                            </div>
                                        </div>

                                        {{-- Document Deliverable Box --}}
                                        <div>
                                            @if($isArchitectDone)
                                                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-2">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <div class="flex items-center gap-2 min-w-0 flex-1">
                                                            <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/></svg>
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <div class="font-bold text-slate-900 text-xs truncate" title="{{ $architectAssignment['document_title'] ?? 'Desain Arsitektur' }}">{{ $architectAssignment['document_title'] ?? 'Desain Arsitektur' }}</div>
                                                                <div class="text-[10px] text-slate-500 font-mono truncate" title="{{ $architectAssignment['document_name'] ?? 'Berkas terlampir' }}">{{ $architectAssignment['document_name'] ?? 'Berkas terlampir' }}</div>
                                                            </div>
                                                        </div>
                                                        @if(!empty($architectAssignment['document_path']))
                                                            <a href="{{ asset('storage/' . $architectAssignment['document_path']) }}" target="_blank" 
                                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition shrink-0 shadow-2xs" title="Unduh Desain">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                                <span>Unduh</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                    @if(!empty($architectAssignment['completed_at']))
                                                        <div class="text-[10px] text-slate-400 pt-1.5 border-t border-slate-200/80 text-right font-mono">
                                                            {{ $architectAssignment['completed_at'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($isBdApproved && $isArchitectAssigned)
                                                <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-2">
                                                    <div class="flex items-center justify-between gap-2">
                                                        <div class="flex items-center gap-2 min-w-0 flex-1">
                                                            <div class="w-7 h-7 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                            </div>
                                                            <div class="min-w-0 flex-1">
                                                                <div class="font-bold text-slate-900 text-xs truncate" title="Desain Disahkan">Desain Disahkan</div>
                                                                <div class="text-[10px] text-slate-500 font-mono truncate" title="{{ $presalesAssignment['document_name'] ?? 'Proposal & SOW' }}">{{ $presalesAssignment['document_name'] ?? 'Proposal & SOW' }}</div>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $saDlPath = !empty($architectAssignment['document_path']) ? $architectAssignment['document_path'] : (!empty($presalesAssignment['document_path']) ? $presalesAssignment['document_path'] : ($project->proposal_file ?? null));
                                                        @endphp
                                                        @if(!empty($saDlPath))
                                                            <a href="{{ asset('storage/' . $saDlPath) }}" target="_blank" 
                                                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition shrink-0 shadow-2xs" title="Unduh Berkas">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                                <span>Unduh</span>
                                                            </a>
                                                        @endif
                                                    </div>
                                                    @if(!empty($bdVerification['verified_at']))
                                                        <div class="text-[10px] text-slate-400 pt-1.5 border-t border-slate-200/80 text-right font-mono">
                                                            {{ $bdVerification['verified_at'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @elseif($isArchitectAssigned)
                                                <div class="p-3 rounded-lg bg-amber-50/70 border border-amber-200 text-amber-900 space-y-1">
                                                    <div class="font-medium italic text-[11px]">"{{ !empty($architectAssignment['sales_notes']) ? $architectAssignment['sales_notes'] : 'Mohon dirancang topologi sistem.' }}"</div>
                                                    <div class="text-[10px] text-amber-700 mt-1">Ditugaskan: {{ $architectAssignment['assigned_at'] ?? '-' }}</div>
                                                </div>
                                            @else
                                                <div class="p-3 rounded-lg border border-dashed border-slate-200 text-slate-400 text-center text-[10.5px]">
                                                    Belum ada penugasan Solution Architect.
                                                </div>
                                            @endif
                                        </div>

                                    </div>

                                    {{-- Actions Footer --}}
                                    @if(!$isArchitectAssigned)
                                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                                            <button type="button" @click="openAssignTechnicalModal('architect')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                <span>Tugaskan SA</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>

                        {{-- 3. Persetujuan Pimpinan (Review & Sign-Off) --}}
                        @include('projects.partials.workflow-leadership')

                    @elseif($currentStatus === 'Draft')
                        {{-- DRAFT: Persetujuan Pimpinan (Review & Sign-Off) --}}
                        <div class="ipnet-card p-6 space-y-5">
                            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                                <div>
                                    <p class="text-[#8F0A0D] text-[11px] font-bold inline-flex items-center uppercase tracking-wider mb-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D] inline-block mr-1.5"></span> OTORISASI PIMPINAN
                                    </p>
                                    <h3 class="text-sm sm:text-base font-bold text-slate-900">Persetujuan Pimpinan (Review &amp; Sign-Off)</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">Review kelayakan teknis oleh Head Divisi &amp; otorisasi kontrak oleh Direktur.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                {{-- Reviewer 1: Pak Susanto --}}
                                <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200 space-y-3 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">HEAD DIVISI</div>
                                                <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $headApproval['assigned_to'] ?? ($susantoUser ? $susantoUser->name : 'Pak Susanto Djaya') }}</div>
                                            </div>
                                            <span class="px-2.5 py-0.5 rounded text-[10.5px] font-bold {{ !empty($headApproval['approved']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($isHeadAssigned ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                                {{ !empty($headApproval['approved']) ? '✓ Disetujui' : ($isHeadAssigned ? 'Menunggu Review' : 'Belum Di-assign') }}
                                            </span>
                                        </div>

                                        <div class="text-[11.5px] text-slate-600">
                                            @if(!empty($headApproval['approved']))
                                                <div class="p-2.5 rounded-lg bg-emerald-50/80 border border-emerald-200 text-emerald-800 font-medium">
                                                    "{{ $headApproval['notes'] ?? 'Review kelayakan teknis & alokasi resource disetujui.' }}"
                                                </div>
                                                <div class="text-[10.5px] text-slate-400 mt-1">Disetujui: {{ $headApproval['date'] ?? '-' }}</div>
                                            @elseif($isHeadAssigned)
                                                <div class="p-2.5 rounded-lg bg-amber-50/60 border border-amber-200 text-amber-800 italic">
                                                    "Menunggu review dari {{ $headApproval['assigned_to'] ?? 'Pak Susanto Djaya' }}."
                                                </div>
                                                @if(!empty($headApproval['assigned_at']))
                                                    <div class="text-[10.5px] text-slate-400 mt-1">Ditugaskan: {{ $headApproval['assigned_at'] }}</div>
                                                @endif
                                            @else
                                                <span class="text-slate-400">Belum diajukan ke Head Divisi.</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="pt-2 border-t border-slate-200 flex items-center justify-between gap-2">
                                        @if(!$isHeadAssigned)
                                            <button type="button" @click="openAssignModal('head')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                                                <span>+ Assign ke Pak Susanto</span>
                                            </button>
                                        @elseif(!$headApproval['approved'])
                                            <button type="button" @click="openAssignModal('head')" class="text-xs font-semibold text-slate-500 hover:text-[#8F0A0D] cursor-pointer">
                                                Ubah Penugasan
                                            </button>
                                        @endif

                                        @if($canApproveHead && $isHeadAssigned)
                                            <button type="button" @click="openApproveModal('head')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-white btn-ipnet-primary shadow-xs cursor-pointer ml-auto">
                                                <span>{{ empty($headApproval['approved']) ? '✓ Beri Approval' : 'Ubah Catatan' }}</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Reviewer 2: Pak Hariyadi --}}
                                <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200 space-y-3 flex flex-col justify-between">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">DIREKTUR</div>
                                                <div class="font-bold text-slate-900 text-sm mt-0.5">{{ $directorApproval['assigned_to'] ?? ($hariyadiUser ? $hariyadiUser->name : 'Pak Hariyadi') }}</div>
                                            </div>
                                            <span class="px-2.5 py-0.5 rounded text-[10.5px] font-bold {{ !empty($directorApproval['approved']) ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($isDirectorAssigned ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-slate-100 text-slate-600 border border-slate-200') }}">
                                                {{ !empty($directorApproval['approved']) ? '✓ Disahkan' : ($isDirectorAssigned ? 'Menunggu Otorisasi' : 'Belum Di-assign') }}
                                            </span>
                                        </div>

                                        <div class="text-[11.5px] text-slate-600">
                                            @if(!empty($directorApproval['approved']))
                                                <div class="p-2.5 rounded-lg bg-emerald-50/80 border border-emerald-200 text-emerald-800 font-medium">
                                                    "{{ $directorApproval['notes'] ?? 'Otorisasi finansial & validasi kontrak disahkan.' }}"
                                                </div>
                                                <div class="text-[10.5px] text-slate-400 mt-1">Disahkan: {{ $directorApproval['date'] ?? '-' }}</div>
                                            @elseif($isDirectorAssigned)
                                                <div class="p-2.5 rounded-lg bg-amber-50/60 border border-amber-200 text-amber-800 italic">
                                                    "Menunggu otorisasi finansial dari {{ $directorApproval['assigned_to'] ?? 'Pak Hariyadi' }}."
                                                </div>
                                                @if(!empty($directorApproval['assigned_at']))
                                                    <div class="text-[10.5px] text-slate-400 mt-1">Ditugaskan: {{ $directorApproval['assigned_at'] }}</div>
                                                @endif
                                            @else
                                                <span class="text-slate-400">Belum diajukan ke Direktur.</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="pt-2 border-t border-slate-200 flex items-center justify-between gap-2">
                                        @if(!$isDirectorAssigned)
                                            <button type="button" @click="openAssignModal('director')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 border border-red-200 transition cursor-pointer">
                                                <span>+ Assign ke Pak Hariyadi</span>
                                            </button>
                                        @elseif(!$directorApproval['approved'])
                                            <button type="button" @click="openAssignModal('director')" class="text-xs font-semibold text-slate-500 hover:text-[#8F0A0D] cursor-pointer">
                                                Ubah Penugasan
                                            </button>
                                        @endif

                                        @if($canApproveDirector && $isDirectorAssigned)
                                            <button type="button" @click="openApproveModal('director')" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold text-white btn-ipnet-primary shadow-xs cursor-pointer ml-auto">
                                                <span>{{ empty($directorApproval['approved']) ? '✓ Beri Otorisasi' : 'Ubah Catatan' }}</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                    @elseif($currentStatus === 'In Progress')
                        {{-- IN PROGRESS: Delivery PMO/MS + Riwayat Pimpinan + Riwayat Solusi Teknis + Ringkasan Pipeline --}}
                        @include('projects.partials.workflow-delivery')
                        @include('projects.partials.workflow-leadership')
                        @include('projects.partials.workflow-technical-solution')
                        @include('projects.partials.workflow-pipeline')

                    @elseif($currentStatus === 'Pending')
                        {{-- PENDING --}}
                        <div class="ipnet-card p-6 border-amber-200 bg-amber-50/50 space-y-2">
                            <div class="font-bold text-amber-900 text-sm flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                Proyek Ditangguhkan (Pending)
                            </div>
                            <p class="text-xs text-amber-800 leading-relaxed">
                                Pengerjaan proyek sedang di-pause sementara waktu menunggu konfirmasi akses site, perizinan, atau kelengkapan berkas kontrak.
                            </p>
                        </div>
                        @include('projects.partials.workflow-delivery')
                        @include('projects.partials.workflow-leadership')
                        @include('projects.partials.workflow-technical-solution')

                    @elseif($currentStatus === 'Completed')
                        {{-- COMPLETED --}}
                        <div class="ipnet-card p-6 border-emerald-200 bg-emerald-50/50 space-y-2">
                            <div class="font-bold text-emerald-900 text-sm flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                Proyek Selesai &amp; BAST Terbit (Completed)
                            </div>
                            <p class="text-xs text-emerald-800 leading-relaxed">
                                Seluruh target milestone teknis telah selesai 100% dan Berita Acara Serah Terima (BAST) pekerjaan telah disahkan bersama klien.
                            </p>
                        </div>
                        @include('projects.partials.workflow-delivery')
                        @include('projects.partials.workflow-leadership')
                        @include('projects.partials.workflow-technical-solution')
                        @include('projects.partials.workflow-pipeline')
                    @endif

                    {{-- MILESTONES CARD --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2.5">
                                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-2 h-4 rounded-full bg-[#8F0A0D]"></span>
                                    Milestones Pekerjaan
                                </h3>
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $project->tasks->where('status', 'Completed')->count() }}/{{ $project->tasks->count() }} Selesai
                                </span>
                            </div>
                            
                            <button type="button" 
                                    @click="isAddMilestoneModalOpen = true" 
                                    onclick="window.openModal('modal-add-milestone')"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 transition cursor-pointer border border-red-200 shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                <span>Tambah Milestone</span>
                            </button>
                        </div>

                        @if($project->tasks->count() > 0)
                            <div class="space-y-2">
                                @foreach($project->tasks as $task)
                                    <div class="p-3.5 rounded-xl border border-slate-200 bg-white flex items-center justify-between text-xs hover:border-slate-300 transition shadow-2xs">
                                        <div class="flex items-center gap-3 min-w-0">
                                             <input type="checkbox" {{ $task->status === 'Completed' ? 'checked' : '' }} disabled class="w-4 h-4 rounded text-[#8F0A0D] shrink-0 border-slate-300">
                                            <div class="min-w-0">
                                                <span class="font-bold text-slate-900 truncate block">{{ $task->title ?? $task->name }}</span>
                                                @if($task->engineer)
                                                    <div class="text-[11px] text-slate-500">Engineer: {{ $task->engineer->name }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-0.5 rounded-md text-[10.5px] font-bold {{ $task->status === 'Completed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                            {{ $task->status }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-5 rounded-xl border border-dashed border-slate-200 bg-slate-50/50 text-center text-xs text-slate-400">
                                Belum ada milestone yang dibuat. Klik <strong class="text-[#8F0A0D] font-semibold">Tambah Milestone</strong> untuk menyusun target proyek.
                            </div>
                        @endif
                    </div>

                    {{-- ATTACHMENTS CARD --}}
                    @php
                        $uploadedDocs = \App\Models\ProjectDocument::where('project_id', $project->id)
                            ->whereNotNull('file_path')
                            ->where('file_path', '!=', '')
                            ->latest()
                            ->get();
                    @endphp
                    <div class="ipnet-card p-6 space-y-4">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2.5">
                                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-2 h-4 rounded-full bg-[#8F0A0D]"></span>
                                    Berkas Lampiran Pendukung
                                </h3>
                                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $uploadedDocs->count() }} Berkas
                                </span>
                            </div>
                            
                            <button type="button" 
                                    @click="isUploadDocModalOpen = true" 
                                    onclick="window.openModal('modal-upload-doc')"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 transition cursor-pointer border border-red-200 shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                <span>Upload Berkas</span>
                            </button>
                        </div>

                        @if($uploadedDocs->count() > 0)
                            <div class="space-y-2">
                                @foreach($uploadedDocs as $doc)
                                    <div class="p-3.5 rounded-xl border border-slate-200 bg-white flex items-center justify-between text-xs hover:border-slate-300 transition shadow-2xs">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="font-bold text-slate-900 truncate block">{{ $doc->document_title ?? ($doc->document_name ?? 'Lampiran Proyek') }}</span>
                                                @if($doc->file_name)
                                                    <span class="text-[11px] text-slate-500 block truncate">{{ $doc->file_name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($doc->file_path)
                                            <div class="flex items-center gap-2 shrink-0">
                                                <a href="{{ route('projects.documents.download', [$project->id, $doc->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 transition" title="Unduh Berkas">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    <span>Unduh</span>
                                                </a>
                                                <form action="{{ route('projects.documents.delete', [$project->id, $doc->id]) }}" method="POST" onsubmit="return confirm('Hapus berkas lampiran ini?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition cursor-pointer" title="Hapus Berkas">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-5 rounded-xl border border-dashed border-slate-200 bg-slate-50/50 text-center text-xs text-slate-400">
                                Belum ada berkas lampiran. Klik <strong class="text-[#8F0A0D] font-semibold">Upload Berkas</strong> untuk mengunggah dokumen pendukung.
                            </div>
                        @endif
                    </div>

                </div>

                {{-- ══ RIGHT SIDEBAR COLUMN (lg:col-span-4) ══ --}}
                <div class="lg:col-span-4 space-y-6">
                    
                    {{-- 1. PROJECT ACTIVITIES TIMELINE (Directly visible at top right) --}}
                    <div class="ipnet-card p-6 space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#8F0A0D]"></span>
                                Project Activities
                            </h3>
                            <span class="text-[11px] font-semibold text-slate-400">Live Timeline</span>
                        </div>
                        
                        {{-- Timeline Flow with Vertical Connector --}}
                        <div class="relative pl-6 space-y-5 before:absolute before:left-[5px] before:top-2 before:bottom-2 before:w-[2px] before:bg-slate-200 text-xs">
                            
                            {{-- 1. Project Creation --}}
                            <div class="relative">
                                <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs flex items-center justify-center">
                                    <div class="w-1 h-1 rounded-full bg-white"></div>
                                </div>
                                <div class="font-normal text-slate-700">
                                    <strong class="font-semibold text-slate-900">{{ $project->creator ? $project->creator->name : ($project->sales_name ?: 'Sales Team') }}</strong> membuat proyek ini
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                    {{ \Carbon\Carbon::parse($project->created_at)->format('d M Y H:i') }}
                                </div>
                            </div>

                            {{-- 2. Handover PMO --}}
                            @if($project->pm)
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Handover Delivery ke PMO: <strong class="font-semibold text-slate-900">{{ $project->pm->name }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Pengerjaan &amp; alokasi tim dipimpin oleh PMO
                                    </div>
                                </div>
                            @endif

                            {{-- 3. Head Approval --}}
                            @if(!empty($headApproval['approved']))
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Approval Head Divisi: <strong class="font-semibold text-slate-900">Pak Susanto</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        {{ $headApproval['date'] ?? 'Disetujui' }}
                                    </div>
                                </div>
                            @endif

                            {{-- 4. Director Approval --}}
                            @if(!empty($directorApproval['approved']))
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Approval Direktur: <strong class="font-semibold text-slate-900">Pak Hariyadi</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        {{ $directorApproval['date'] ?? 'Disahkan' }}
                                    </div>
                                </div>
                            @endif

                            {{-- 5. BD Appointment --}}
                            @if($isBdmAssigned && !empty($bdmName))
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Penunjukan PIC BD: <strong class="font-semibold text-slate-900">{{ $bdmName }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Product Manager / Verifikator Solusi
                                    </div>
                                </div>
                            @endif

                            {{-- 6. Presales Assignment --}}
                            @if(!empty($presalesAssignment['assigned']))
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Penugasan Pre-Sales: <strong class="font-semibold text-slate-900">{{ $presalesAssignment['assigned_to'] ?? 'Akbar' }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        {{ $presalesAssignment['assigned_at'] ?? 'Ditugaskan' }} (oleh {{ $presalesAssignment['assigned_by'] ?? 'Sales' }})
                                    </div>
                                </div>
                            @endif

                            {{-- 7. Solution Architect Assignment --}}
                            @if(!empty($architectAssignment['assigned']))
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Penugasan Solution Architect: <strong class="font-semibold text-slate-900">{{ $architectAssignment['assigned_to'] ?? 'Aris Sadewo' }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        {{ $architectAssignment['assigned_at'] ?? 'Ditugaskan' }} (oleh {{ $architectAssignment['assigned_by'] ?? 'Sales' }})
                                    </div>
                                </div>
                            @endif

                            {{-- 8. Presales Doc Uploaded --}}
                            @if(!empty($presalesAssignment['document_path']))
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Proposal Teknis &amp; BoQ diunggah oleh <strong class="font-semibold text-slate-900">{{ $presalesAssignment['assigned_to'] ?? 'Pre-Sales' }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        Dokumen Proposal &amp; SOW Terlampir ({{ $presalesAssignment['completed_at'] ?? 'Selesai' }})
                                    </div>
                                </div>
                            @endif

                            {{-- 9. Architect Doc Uploaded --}}
                            @if(!empty($architectAssignment['document_path']))
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Desain Topologi diunggah oleh <strong class="font-semibold text-slate-900">{{ $architectAssignment['assigned_to'] ?? 'Solution Architect' }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        Diagram Arsitektur Terlampir ({{ $architectAssignment['completed_at'] ?? 'Selesai' }})
                                    </div>
                                </div>
                            @endif

                            {{-- 10. BD Solution Verification --}}
                            @if(!empty($bdVerification['status']) && $bdVerification['status'] !== 'Pending')
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Verifikasi Solusi BD: <strong class="font-semibold text-slate-900">{{ $bdVerification['status'] === 'Approved' ? 'Disetujui' : 'Perlu Revisi' }}</strong> oleh <strong class="font-semibold text-slate-900">{{ $bdVerification['verified_by'] ?? ($bdmName ?: 'PIC BD') }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        {{ $bdVerification['verified_at'] ?? 'Selesai diverifikasi' }}
                                        @if(!empty($bdVerification['notes']))
                                            <div class="text-slate-600 font-normal italic mt-1 bg-slate-50 p-2 rounded border border-slate-200">"{{ $bdVerification['notes'] }}"</div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- 2. INFORMASI KLIEN CARD --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-4 rounded-full bg-[#8F0A0D]"></span>
                            Informasi Klien
                        </h3>
                        <div class="space-y-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">CLIENT NAME</span>
                                <div class="font-bold text-slate-900 text-xs mt-0.5">{{ $project->client ?: '-' }}</div>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">CLIENT EMAIL</span>
                                <div class="font-bold text-slate-900 text-xs mt-0.5 truncate">{{ $project->customer_pic_finance ?: ($project->customer_pic_technical ?: '-') }}</div>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PIC KLIEN</span>
                                <div class="font-bold text-slate-900 text-xs mt-0.5">{{ $project->customer_pic_name ?? ($project->sales_name ?? '-') }}</div>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">CONTACT PIC</span>
                                <div class="font-bold text-slate-900 text-xs mt-0.5">{{ $project->customer_pic_business ?: '-' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. RINGKASAN PORTOFOLIO --}}
                    <div class="ipnet-card p-6 space-y-3">
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-4 rounded-full bg-[#8F0A0D]"></span>
                            Ringkasan Proyek
                        </h3>
                        <div class="space-y-2 text-xs">
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                                <span class="text-slate-500">Divisi</span>
                                <span class="font-bold text-slate-800">{{ $project->client_department ?: 'IPNET 01' }}</span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                                <span class="text-slate-500">Sales Owner</span>
                                <span class="font-bold text-slate-800">{{ $project->creator ? $project->creator->name : ($project->sales_name ?: 'Sales Team') }}</span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                                <span class="text-slate-500">Status Penyelesaian</span>
                                <span class="font-bold text-[#8F0A0D]">{{ $project->tasks->count() > 0 ? round(($project->tasks->where('status', 'Completed')->count() / $project->tasks->count()) * 100) : 0 }}% Selesai</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>                    </div>

                </div>

            </div>

        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODALS: CENTERED & CLEAN                                --}}
    {{-- ======================================================== --}}

    {{-- 1. ASSIGN / HANDOVER TO PMO OR MANAGED SERVICE MODAL --}}
    <div id="modal-handover" x-show="isHandoverModalOpen" x-cloak 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div @click.away="isHandoverModalOpen = false; window.closeModal('modal-handover')" 
             class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto"
             x-data="{ targetType: '{{ ($project->handover_target === 'managed_service' || $project->stage === 'Operate') ? 'managed_service' : 'pmo' }}' }">
            
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Serah Terima &amp; Handover Proyek</h3>
                    <p class="text-[11.5px] text-slate-500 mt-0.5">Tentukan jalur eksekusi proyek (Delivery PMO atau Operasional Managed Service)</p>
                </div>
                <button type="button" @click="isHandoverModalOpen = false; window.closeModal('modal-handover')" onclick="window.closeModal('modal-handover')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.assign', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="role_type" value="pm">

                {{-- Pilihan Target Handover --}}
                <div>
                    <label class="block text-slate-700 mb-2 uppercase tracking-wider text-[10.5px]">PILIH JALUR EKSEKUSI (TARGET HANDOVER)</label>
                    <div class="grid grid-cols-2 gap-2.5">
                        <label :class="targetType === 'pmo' ? 'border-[#8F0A0D] bg-red-50/50 text-[#8F0A0D]' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
                               class="p-3 rounded-xl border flex flex-col gap-1 cursor-pointer transition">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="handover_target" value="pmo" x-model="targetType" class="text-[#8F0A0D] focus:ring-[#8F0A0D]">
                                <span class="font-bold text-xs">PMO Delivery</span>
                            </div>
                            <span class="text-[10px] text-slate-500 font-normal">Implementasi &amp; Deployment</span>
                        </label>

                        <label :class="targetType === 'managed_service' ? 'border-purple-600 bg-purple-50/50 text-purple-900' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
                               class="p-3 rounded-xl border flex flex-col gap-1 cursor-pointer transition">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="handover_target" value="managed_service" x-model="targetType" class="text-purple-600 focus:ring-purple-500">
                                <span class="font-bold text-xs">Managed Service</span>
                            </div>
                            <span class="text-[10px] text-slate-500 font-normal">Operasional, Helpdesk &amp; SLA</span>
                        </label>
                    </div>
                </div>

                {{-- User Selection --}}
                <div>
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[11px]" 
                           x-text="targetType === 'managed_service' ? 'PILIH LEAD MANAGED SERVICE / OPERASIONAL' : 'PILIH PROJECT MANAGER (PMO)'"></label>
                    <select name="user_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer bg-white font-bold">
                        <option value="">-- Pilih Penanggung Jawab --</option>
                        @foreach($pmoUsers as $pmo)
                            <option value="{{ $pmo->id }}" {{ ($project->pm_id == $pmo->id || (empty($project->pm_id) && str_contains(strtolower($pmo->name), 'rizki'))) ? 'selected' : '' }}>
                                (PMO / Lead) {{ $pmo->name }}
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

                {{-- SLA Tier for Managed Service --}}
                <div x-show="targetType === 'managed_service'" x-cloak>
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[10.5px]">SLA TIER KONTRAK</label>
                    <select name="sla_tier" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500 cursor-pointer bg-white font-bold">
                        <option value="Gold" {{ ($project->sla_tier ?: 'Gold') === 'Gold' ? 'selected' : '' }}>Gold (SLA 99.5% - Response 15-30 Menit)</option>
                        <option value="Platinum" {{ ($project->sla_tier ?? '') === 'Platinum' ? 'selected' : '' }}>Platinum (SLA 99.9% - Response 15 Menit 24x7)</option>
                        <option value="Silver" {{ ($project->sla_tier ?? '') === 'Silver' ? 'selected' : '' }}>Silver (SLA 99.0% - Response 1-2 Jam 8x5)</option>
                        <option value="Bronze" {{ ($project->sla_tier ?? '') === 'Bronze' ? 'selected' : '' }}>Bronze (SLA 98.0% - Best Effort)</option>
                    </select>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isHandoverModalOpen = false; window.closeModal('modal-handover')" onclick="window.closeModal('modal-handover')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                        Simpan Handover
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 2. DRAFT APPROVAL MODAL --}}
    <div id="modal-approve" x-show="isApproveModalOpen" x-cloak 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div @click.away="isApproveModalOpen = false; window.closeModal('modal-approve')" 
             class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-base font-bold text-slate-900" x-text="approveRole === 'head' ? 'Approval Head Divisi (Pak Susanto)' : 'Approval Direktur (Pak Hariyadi)'"></h3>
                <button type="button" @click="isApproveModalOpen = false; window.closeModal('modal-approve')" onclick="window.closeModal('modal-approve')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.approve_draft', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="approval_role" :value="approveRole">

                <div>
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[11px]">CATATAN PERSETUJUAN</label>
                    <textarea name="notes" rows="3" 
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"
                              :placeholder="approveRole === 'head' ? 'Contoh: Kelayakan teknis & alokasi resource disetujui.' : 'Contoh: Otorisasi anggaran & kontrak disahkan.'"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="auto_advance" id="auto_advance" value="1" checked class="rounded text-red-600">
                    <label for="auto_advance" class="text-slate-600 font-normal">Otomatis ubah status saat kedua approval lengkap</label>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isApproveModalOpen = false; window.closeModal('modal-approve')" onclick="window.closeModal('modal-approve')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                        Simpan Approval
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 3. ADD MILESTONE MODAL --}}
    <div id="modal-add-milestone" x-show="isAddMilestoneModalOpen" x-cloak 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div @click.away="isAddMilestoneModalOpen = false; window.closeModal('modal-add-milestone')" 
             class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-base font-bold text-slate-900">Tambah Milestone</h3>
                <button type="button" @click="isAddMilestoneModalOpen = false; window.closeModal('modal-add-milestone')" onclick="window.closeModal('modal-add-milestone')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="hidden" name="status" value="Pending">

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">NAMA MILESTONE</label>
                    <input type="text" name="title" required placeholder="Contoh: Pengiriman Aruba AP-505 dan APC" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">PRIORITAS</label>
                        <select name="priority" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white">
                            <option value="Low">Low</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">DEADLINE</label>
                        <input type="date" name="deadline" value="{{ date('Y-m-d', strtotime('+7 days')) }}" 
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isAddMilestoneModalOpen = false; window.closeModal('modal-add-milestone')" onclick="window.closeModal('modal-add-milestone')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                        Simpan Milestone
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 4. EDIT META ESTIMATION MODAL --}}
    <div id="modal-edit-meta" x-show="isEditMetaModalOpen" x-cloak 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div @click.away="isEditMetaModalOpen = false; window.closeModal('modal-edit-meta')" 
             class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-base font-bold text-slate-900">Edit Estimasi &amp; Tanggal</h3>
                <button type="button" @click="isEditMetaModalOpen = false; window.closeModal('modal-edit-meta')" onclick="window.closeModal('modal-edit-meta')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.meta_update', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">NILAI ESTIMASI (RP)</label>
                    <input type="number" name="contract_value" value="{{ $project->contract_value ?: 300000000 }}" required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 font-bold">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">PROJECT START</label>
                        <input type="date" name="start_date" value="{{ $project->start_date ? $project->start_date->format('Y-m-d') : date('Y-m-d') }}" 
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">PROJECT END</label>
                        <input type="date" name="deadline" value="{{ $project->deadline ? $project->deadline->format('Y-m-d') : '' }}" 
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isEditMetaModalOpen = false; window.closeModal('modal-edit-meta')" onclick="window.closeModal('modal-edit-meta')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 5. UPLOAD DOCUMENT MODAL --}}
    <div id="modal-upload-doc" x-show="isUploadDocModalOpen" x-cloak 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div @click.away="isUploadDocModalOpen = false; window.closeModal('modal-upload-doc')" 
             class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <h3 class="text-base font-bold text-slate-900">Upload Berkas Lampiran</h3>
                <button type="button" @click="isUploadDocModalOpen = false; window.closeModal('modal-upload-doc')" onclick="window.closeModal('modal-upload-doc')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.documents.upload', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="stage_number" value="1">
                <input type="hidden" name="document_key" value="lampiran_pendukung">

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">PILIH BERKAS</label>
                    <input type="file" name="document_files[]" multiple required 
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer bg-slate-50 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-[#8F0A0D] hover:file:bg-red-100">
                    <p class="text-[10.5px] text-slate-400 mt-1">Format: PDF, XLSX, DOCX, ZIP, PNG, JPG (Maks 50MB)</p>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">CATATAN DOKUMEN</label>
                    <input type="text" name="notes" placeholder="Contoh: BoQ dan Penawaran Resmi" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isUploadDocModalOpen = false; window.closeModal('modal-upload-doc')" onclick="window.closeModal('modal-upload-doc')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                        Upload Berkas
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 6. MODAL ASSIGN APPROVAL KE PIMPINAN (HEAD & DIREKTUR) --}}
    <div id="modal-assign" x-show="isAssignModalOpen" x-cloak 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div @click.away="isAssignModalOpen = false; window.closeModal('modal-assign')" 
             class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900" 
                        x-text="assignRole === 'head' ? 'Assign Review ke Head Divisi' : (assignRole === 'director' ? 'Assign Otorisasi ke Direktur' : 'Assign Review ke Pimpinan')"></h3>
                    <p class="text-[11.5px] text-slate-500 mt-0.5">Tugaskan peninjauan draft proyek ke pimpinan yang berwenang</p>
                </div>
                <button type="button" @click="isAssignModalOpen = false; window.closeModal('modal-assign')" onclick="window.closeModal('modal-assign')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.assign_approver', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="role" :value="assignRole">

                {{-- Pilihan Head Divisi --}}
                <div x-show="assignRole === 'head' || assignRole === 'both'">
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[10.5px]">PILIH HEAD DIVISI (REVIEW TEKNIS)</label>
                    <select name="head_user_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white">
                        @foreach($leadershipUsers as $lu)
                            <option value="{{ $lu->id }}" {{ ($susantoUser && $susantoUser->id == $lu->id) ? 'selected' : '' }}>
                                {{ $lu->name }} ({{ $lu->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Pilihan Direktur --}}
                <div x-show="assignRole === 'director' || assignRole === 'both'">
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[10.5px]">PILIH DIREKTUR (OTORISASI KONTRAK)</label>
                    <select name="director_user_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white">
                        @foreach($leadershipUsers as $lu)
                            <option value="{{ $lu->id }}" {{ ($hariyadiUser && $hariyadiUser->id == $lu->id) ? 'selected' : '' }}>
                                {{ $lu->name }} ({{ $lu->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[10.5px]">CATATAN DARI SALES (OPSIONAL)</label>
                    <textarea name="notes" rows="3" 
                              placeholder="Contoh: Mohon review kelayakan teknis jaringan dan validasi estimasi nilai kontrak untuk penawaran klien."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isAssignModalOpen = false; window.closeModal('modal-assign')" onclick="window.closeModal('modal-assign')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                        Tugaskan Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 7. MODAL EDIT PIPELINE SALES & OPPORTUNITY --}}
    <div id="modal-edit-pipeline" x-show="isEditPipelineModalOpen" x-cloak 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div @click.away="isEditPipelineModalOpen = false; window.closeModal('modal-edit-pipeline')" 
             class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Ubah Stage &amp; Pipeline Sales</h3>
                    <p class="text-[11.5px] text-slate-500 mt-0.5">Perbarui progres tahapan prospek penjualan &amp; estimasi closing</p>
                </div>
                <button type="button" @click="isEditPipelineModalOpen = false; window.closeModal('modal-edit-pipeline')" onclick="window.closeModal('modal-edit-pipeline')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            @php
                $currentSalesStage = $project->sales_stage ?: 'Qualification';
                $currentProb = $project->win_probability ?? 10;
            @endphp
            <form action="{{ route('projects.update_pipeline', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold"
                  x-data="{
                      stageVal: '{{ $currentSalesStage }}',
                      probVal: {{ (int)$currentProb }},
                      onStageChange(val) {
                          this.stageVal = val;
                          if (val === 'Closed Won') this.probVal = 100;
                          else if (val === 'Closed Lost') this.probVal = 0;
                          else if (val === 'Negotiation') this.probVal = 75;
                          else if (val === 'Proposal / Quoting') this.probVal = 50;
                          else if (val === 'Discovery') this.probVal = 25;
                          else if (val === 'Qualification') this.probVal = 10;
                      }
                  }">
                @csrf

                <div>
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[10.5px]">SALES PIPELINE STAGE</label>
                    <select name="sales_stage" x-model="stageVal" @change="onStageChange($event.target.value)" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white cursor-pointer font-bold">
                        @php
                            $stagesList = [
                                'Qualification'        => '1. Qualification (Kualifikasi Awal)',
                                'Discovery'            => '2. Discovery / Kebutuhan Klien',
                                'Proposal / Quoting'   => '3. Proposal &amp; Quoting (Penawaran Resmi)',
                                'Negotiation'          => '4. Negotiation / Pembahasan Kontrak',
                                'Closed Won'           => '5. Closed Won (Menang / Deal)',
                                'Closed Lost'          => '6. Closed Lost (Batal / Kalah Tender)',
                            ];
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
                        <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[10.5px]">WIN PROBABILITY (%)</label>
                        <select name="win_probability" x-model="probVal" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white cursor-pointer font-bold">
                            @foreach([0 => '0% (Lost / Batal)', 10 => '10% (Tahap Awal)', 25 => '25% (Riset Spek)', 50 => '50% (Proposal Masuk)', 75 => '75% (Negosiasi Final)', 90 => '90% (Menunggu PO)', 100 => '100% (Deal / Won)'] as $pct => $pctLabel)
                                <option value="{{ $pct }}" {{ ((int)$currentProb) === $pct ? 'selected' : '' }}>
                                    {{ $pctLabel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[10.5px]">TARGET CLOSING</label>
                        <input type="date" name="expected_closing_date" 
                               value="{{ $project->expected_closing_date ? \Carbon\Carbon::parse($project->expected_closing_date)->format('Y-m-d') : '' }}" 
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[10.5px]">ESTIMASI NILAI PROYEK (RP)</label>
                    <input type="number" name="contract_value" value="{{ $project->contract_value ?: 0 }}" min="0" step="1000"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 font-bold">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isEditPipelineModalOpen = false; window.closeModal('modal-edit-pipeline')" onclick="window.closeModal('modal-edit-pipeline')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                        Simpan Perubahan Stage
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 8. MODAL ASSIGN KE TIM SOLUSI (PIC BD, PRESALES & SA) --}}
    <div id="modal-assign-technical" x-show="isAssignTechnicalModalOpen" x-cloak 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div @click.away="isAssignTechnicalModalOpen = false; window.closeModal('modal-assign-technical')" 
             class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900" 
                        x-text="assignTechnicalRole === 'bdm' ? 'Tunjuk PIC BD (Product Manager)' : (assignTechnicalRole === 'presales' ? 'Tugaskan Pre-Sales Specialist' : (assignTechnicalRole === 'architect' ? 'Tugaskan Solution Architect' : 'Tugaskan Tim Solusi &amp; BD'))"></h3>
                    <p class="text-[11.5px] text-slate-500 mt-0.5">Penugasan PIC BD verifikator, penyusun proposal, dan perancang topologi</p>
                </div>
                <button type="button" @click="isAssignTechnicalModalOpen = false; window.closeModal('modal-assign-technical')" onclick="window.closeModal('modal-assign-technical')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.assign_technical', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="role" :value="assignTechnicalRole">

                {{-- Pilihan PIC BD --}}
                <div x-show="assignTechnicalRole === 'bdm' || assignTechnicalRole === 'all' || assignTechnicalRole === 'both'">
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[10.5px]">PILIH PIC BUSINESS DEVELOPMENT (PRODUCT MANAGER &amp; VERIFIKATOR)</label>
                    <select name="bdm_user_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white cursor-pointer">
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
                    <span class="text-[10px] text-slate-400 mt-1 block">PIC BD akan menerima notifikasi dan memvalidasi berkas proposal sebelum diajukan ke klien.</span>
                </div>

                {{-- Pilihan Pre-Sales --}}
                <div x-show="assignTechnicalRole === 'presales' || assignTechnicalRole === 'all' || assignTechnicalRole === 'both'">
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[10.5px]">PILIH PRE-SALES SPECIALIST (PROPOSAL &amp; BOQ)</label>
                    <select name="presales_user_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white cursor-pointer">
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
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[10.5px]">PILIH SOLUTION ARCHITECT (DESAIN TOPOLOGI &amp; SIZING)</label>
                    <select name="architect_user_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white cursor-pointer">
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
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[10.5px]">INSTRUKSI &amp; CATATAN TEKNIS SALES</label>
                    <textarea name="notes" rows="3" 
                              placeholder="Contoh: Tolong buatkan desain topologi redundant switch &amp; estimasi BoQ untuk kebutuhan penawaran tender klien."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isAssignTechnicalModalOpen = false; window.closeModal('modal-assign-technical')" onclick="window.closeModal('modal-assign-technical')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                        Simpan Penugasan Tim
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 9. MODAL VERIFIKASI SOLUSI OLEH PIC BD --}}
    <div id="modal-verify-technical" x-show="isVerifyTechnicalModalOpen" x-cloak 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div @click.away="isVerifyTechnicalModalOpen = false; window.closeModal('modal-verify-technical')" 
             class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Verifikasi Kelayakan Dokumen Solusi</h3>
                    <p class="text-[11.5px] text-slate-500 mt-0.5">Tinjau kesiapan proposal teknis, BoQ, dan desain topologi</p>
                </div>
                <button type="button" @click="isVerifyTechnicalModalOpen = false; window.closeModal('modal-verify-technical')" onclick="window.closeModal('modal-verify-technical')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.verify_technical', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf

                {{-- Ringkasan Berkas yang Terunggah --}}
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2 text-[11.5px]">
                    <div class="font-bold text-slate-800 text-xs">Berkas yang Divalidasi:</div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Proposal &amp; BoQ (Pre-Sales):</span>
                        <span class="font-bold {{ $isPresalesDone ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ $isPresalesDone ? ($presalesAssignment['document_name'] ?? 'Terunggah') : 'Belum Terunggah' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Desain Topologi (Solution Architect):</span>
                        <span class="font-bold {{ $isArchitectDone ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ $isArchitectDone ? ($architectAssignment['document_name'] ?? 'Terunggah') : 'Belum Terunggah' }}
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 mb-2 uppercase tracking-wider text-[10.5px]">KEPUTUSAN VERIFIKASI BD</label>
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
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">CATATAN / FEEDBACK VERIFIKASI (WAJIB JIKA REVISI)</label>
                    <textarea name="notes" rows="3" 
                              placeholder="Contoh jika Disetujui: Spek BoQ dan margin sudah sesuai standar komersial.&#10;Contoh jika Revisi: BoQ switch core perlu disesuaikan dengan spek diskon terbaru."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isVerifyTechnicalModalOpen = false; window.closeModal('modal-verify-technical')" onclick="window.closeModal('modal-verify-technical')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                        Simpan Keputusan Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 10. MODAL UNGGAH BERKAS SOLUSI TEKNIS (PRESALES / SA) --}}
    <div id="modal-upload-technical-doc" x-show="isUploadTechnicalDocModalOpen" x-cloak 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div @click.away="isUploadTechnicalDocModalOpen = false; window.closeModal('modal-upload-technical-doc')" 
             class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900" 
                        x-text="uploadTechnicalRole === 'presales' ? 'Unggah Berkas Proposal &amp; BoQ (Pre-Sales)' : 'Unggah Desain Arsitektur &amp; Topologi (Solution Architect)'"></h3>
                    <p class="text-[11.5px] text-slate-500 mt-0.5">Unggah berkas dokumen teknis pendukung solusi proyek</p>
                </div>
                <button type="button" @click="isUploadTechnicalDocModalOpen = false; window.closeModal('modal-upload-technical-doc')" onclick="window.closeModal('modal-upload-technical-doc')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.upload_technical_doc', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="role_type" :value="uploadTechnicalRole">

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">NAMA / JUDUL DOKUMEN</label>
                    <input type="text" name="document_title" 
                           :placeholder="uploadTechnicalRole === 'presales' ? 'Contoh: Proposal Teknis &amp; BoQ Estimasi Rev 1' : 'Contoh: Diagram Topologi Arsitektur &amp; Sizing Switch'" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">PILIH BERKAS</label>
                    <input type="file" name="document_files[]" multiple required
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-[#8F0A0D] hover:file:bg-red-100 cursor-pointer border border-slate-200 rounded-xl p-2 bg-slate-50/50">
                    <span class="text-[10px] text-slate-400 mt-1 block">Format: PDF, DOCX, XLSX, VSDX, PNG, JPG (Maks 50MB)</span>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">CATATAN / RINGKASAN TEKNIS (OPSIONAL)</label>
                    <textarea name="notes" rows="2" 
                              placeholder="Contoh: Dokumen telah disesuaikan dengan spek tender dan estimasi diskon prinsipal."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isUploadTechnicalDocModalOpen = false; window.closeModal('modal-upload-technical-doc')" onclick="window.closeModal('modal-upload-technical-doc')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                        Unggah Dokumen Teknis
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 11. MODAL KONFIRMASI HAPUS --}}
    <div id="modal-delete" x-show="isDeleteModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[99999] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         @click.self="isDeleteModalOpen = false; window.closeModal('modal-delete')"
         @keydown.escape.window="isDeleteModalOpen = false; window.closeModal('modal-delete')">
        <div class="bg-white rounded-2xl w-[420px] max-w-full p-6 text-left shadow-2xl border border-slate-200">
            <div class="w-12 h-12 rounded-full bg-rose-50 text-[#8F0A0D] flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            
            <h3 class="text-center text-base font-bold text-slate-900 mb-1.5">Yakin Hapus Project?</h3>
            <p class="text-center text-xs text-slate-500 mb-6 leading-relaxed">
                Project <strong class="text-slate-800">"{{ $project->name }}"</strong> beserta seluruh task dan milestone terkait akan dihapus secara permanen.
            </p>

            <div class="flex gap-2.5">
                <button type="button" @click="isDeleteModalOpen = false; window.closeModal('modal-delete')" onclick="window.closeModal('modal-delete')"
                        class="flex-1 py-2.5 px-4 rounded-xl bg-white text-slate-600 border border-slate-300 font-bold text-xs hover:bg-slate-50 transition cursor-pointer text-center">
                    Batal
                </button>
                <button type="button" @click="document.getElementById('deleteProjForm').submit()"
                        class="flex-1 py-2.5 px-4 rounded-xl btn-ipnet-primary font-bold text-xs transition cursor-pointer shadow-sm text-white text-center">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    {{-- DELETE PROJECT FORM --}}
    <form id="deleteProjForm" action="{{ route('projects.destroy', $project->id) }}" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

</div>
@endsection
