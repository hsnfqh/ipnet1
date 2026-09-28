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

    $isHeadApproved = !empty($headApproval['approved']);
    $isDirectorApproved = !empty($directorApproval['approved']);
    $isBothApproved = $isHeadApproved && $isDirectorApproved;
    $isAnyApproved = $isHeadApproved || $isDirectorApproved;
    $isHeadAssigned = !empty($headApproval['assigned']) || $isHeadApproved;
    $isDirectorAssigned = !empty($directorApproval['assigned']) || $isDirectorApproved;
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
    $isPresalesDone = !empty($presalesAssignment['document_path']);
    $isArchitectDone = !empty($architectAssignment['document_path']);

    // Penyesuaian otomatis status verifikasi
    if (($isPresalesDone || $isArchitectDone) && in_array($bdVerification['status'], ['Pending Assignment', 'Waiting Uploads', null])) {
        $bdVerification['status'] = 'Pending Verification';
    } elseif ($isBdmAssigned && !$isPresalesDone && !$isArchitectDone && in_array($bdVerification['status'], ['Pending Assignment', null])) {
        $bdVerification['status'] = 'Waiting Uploads';
    }

    $isBdApproved = (($bdVerification['status'] ?? '') === 'Approved');
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
    $isSusanto = $authUser && (str_contains(strtolower($authUser->name), 'susanto') || !empty(array_intersect(['Division Head', 'Head Divisi', 'Group Leader', 'HD / Direktur', 'Group Leader Delivery & Operation', 'Group Leader Commercial & Solution', 'Lead Divisi'], $userRoles)));
    $isHariyadi = $authUser && (str_contains(strtolower($authUser->name), 'hariyadi') || !empty(array_intersect(['Director', 'Direktur', 'HD / Direktur'], $userRoles)));
    $isExecutive = $isSusanto || $isHariyadi || \App\Helpers\ScopeHelper::isExecutive($authUser);

    // Pimpinan tertinggi hanya menyetujui, assignment dilakukan oleh tim Sales/AM/Admin
    $canAssignSales = !$isExecutive && $authUser && (
        !empty(array_intersect(['Sales', 'Account Manager', 'Admin', 'Super Admin', 'Admin Support'], $userRoles))
        || ($project->created_by == $authUser->id)
        || ($project->sales_name && str_contains(strtolower($project->sales_name), strtolower($authUser->name)))
    );

    $canApproveHead = $isSusanto;
    $canApproveDirector = $isHariyadi;

    // Ambil daftar user BD (Business Development Managers)
    $bdmUsers = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['BDM', 'BusDev', 'Business Development']))->orderBy('name')->get();
    if ($bdmUsers->isEmpty()) {
        $bdmUsers = \App\Models\User::where('position', 'like', '%Business Development%')->orWhere('name', 'like', '%Kurnijanto%')->orWhere('name', 'like', '%Novan%')->orderBy('name')->get();
    }

    // Ambil daftar user PMO secara aman (tanpa crash jika role belum ada di DB)
    $pmoUsers = \App\Models\User::whereHas('roles', function($q) {
        $q->whereIn('name', ['PMO', 'Project Manager', 'Lead Divisi', 'Group Leader', 'Direktur', 'HD / Direktur']);
    })->orWhere('name', 'like', '%Rizki%')->orWhere('name', 'like', '%Kuncoro%')->orderBy('name')->get();

    // Ambil daftar user Presales & Solution Architect (Terkoneksi ke Sales, BD, SA, Direktur, Head Divisi)
    $presalesUsers = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['Presales', 'Pre-Sales']))->orderBy('name')->get();
    if ($presalesUsers->isEmpty()) {
        $presalesUsers = \App\Models\User::where('email', 'akbar@ipnetsolusindo.com')->orWhere('position', 'like', '%Pre-Sales%')->orWhere('position', 'like', '%Presales%')->get();
    }
    $architectUsers = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['Solution Architect', 'Solutions Architect', 'SA']))->orderBy('name')->get();
    if ($architectUsers->isEmpty()) {
        $architectUsers = \App\Models\User::where('email', 'like', '%aris%')->orWhere('position', 'like', '%Architect%')->get();
    }

    // Hak otorisasi PIC BD / BDM untuk me-review & memverifikasi dokumen solusi (Eksklusif tim BD/PIC BD, bukan Pimpinan Eksekutif)
    $canVerifyBD = !$isExecutive && $authUser && (
        ($project->bdm_id && $authUser->id == $project->bdm_id)
        || (!empty($bdmAssignment['assigned_user_id']) && $authUser->id == $bdmAssignment['assigned_user_id'])
        || !empty(array_intersect(['BDM', 'BusDev', 'Business Development', 'Product Manager'], $userRoles))
        || str_contains(strtolower($authUser->name), 'kurnijanto')
        || str_contains(strtolower($authUser->name), 'novan')
        || str_contains(strtolower($authUser->name), 'kipsriyanto')
        || str_contains(strtolower($authUser->name), 'armen')
    );

    // Hak otorisasi unggah berkas teknis solusi (Eksklusif: Presales upload Proposal, SA upload Topologi - Sales TIDAK BISA unggah)
    $canUploadPresales = $authUser && (
        (!empty($presalesAssignment['assigned_user_id']) && $authUser->id == $presalesAssignment['assigned_user_id'])
        || !empty(array_intersect(['Presales', 'Pre-Sales', 'Super Admin', 'Admin'], $userRoles))
        || (in_array(strtolower($authUser->position ?? ''), ['presales', 'pre-sales', 'pre sales']) && empty(array_intersect(['Sales', 'Account Manager'], $userRoles)))
    );

    $canUploadArchitect = $authUser && (
        (!empty($architectAssignment['assigned_user_id']) && $authUser->id == $architectAssignment['assigned_user_id'])
        || !empty(array_intersect(['Solution Architect', 'Solutions Architect', 'SA', 'Super Admin', 'Admin'], $userRoles))
        || (in_array(strtolower($authUser->position ?? ''), ['solution architect', 'solutions architect', 'sa', 'architect']) && empty(array_intersect(['Sales', 'Account Manager'], $userRoles)))
    );

    $isPresalesOrSaOnly = $authUser && (
        !empty(array_intersect(['Presales', 'Pre-Sales', 'Solution Architect', 'Solutions Architect', 'SA'], $userRoles))
        || in_array(strtolower($authUser->position ?? ''), ['presales', 'pre-sales', 'solution architect', 'sa', 'pre sales'])
        || str_contains(strtolower($authUser->email ?? ''), 'akbar')
        || str_contains(strtolower($authUser->email ?? ''), 'aris')
        || str_contains(strtolower($authUser->name ?? ''), 'akbar')
        || str_contains(strtolower($authUser->name ?? ''), 'aris')
    ) && empty(array_intersect(['Sales', 'Account Manager', 'Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Head Divisi', 'Group Leader Commercial & Solution', 'Super Admin', 'Admin'], $userRoles));
@endphp

<script>
    window.getProjectData = function() {
        try {
            const projectRoot = document.querySelector('[x-data*="projectDetailPage"]');
            if (projectRoot && window.Alpine && typeof window.Alpine.$data === 'function') {
                return window.Alpine.$data(projectRoot);
            }
            const modalEl = document.getElementById('modal-handover');
            if (modalEl && window.Alpine && typeof window.Alpine.$data === 'function') {
                return window.Alpine.$data(modalEl);
            }
        } catch(e) {}
        return null;
    };

    window.openModal = function(modalId) {
        const el = document.getElementById(modalId);
        if (el) {
            el.removeAttribute('x-cloak');
            el.classList.remove('hidden');
            el.style.setProperty('display', 'flex', 'important');
        }
        const data = window.getProjectData();
        if (data) {
            if (modalId === 'modal-handover') data.isHandoverModalOpen = true;
            if (modalId === 'modal-assign-division') data.isAssignDivisionModalOpen = true;
            if (modalId === 'modal-assign-engineer') data.isAssignEngineerModalOpen = true;
            if (modalId === 'modal-edit-pipeline') data.isEditPipelineModalOpen = true;
            if (modalId === 'modal-assign') data.isAssignModalOpen = true;
            if (modalId === 'modal-assign-technical') data.isAssignTechnicalModalOpen = true;
            if (modalId === 'modal-upload-technical-doc') data.isUploadTechnicalDocModalOpen = true;
            if (modalId === 'modal-verify-technical') data.isVerifyTechnicalModalOpen = true;
            if (modalId === 'modal-approve') data.isApproveModalOpen = true;
            if (modalId === 'modal-edit-meta') data.isEditMetaModalOpen = true;
            if (modalId === 'modal-add-milestone') data.isAddMilestoneModalOpen = true;
            if (modalId === 'modal-upload-doc') data.isUploadDocModalOpen = true;
            if (modalId === 'modal-upload-sales-doc') data.isUploadSalesDocModalOpen = true;
            if (modalId === 'modal-delete') data.isDeleteModalOpen = true;
            if (modalId === 'modal-complete') data.isCompleteModalOpen = true;
        }
    };

    window.closeModal = function(modalId) {
        const el = document.getElementById(modalId);
        if (el) {
            el.style.setProperty('display', 'none', 'important');
            el.classList.add('hidden');
        }
        const data = window.getProjectData();
        if (data) {
            if (modalId === 'modal-handover') data.isHandoverModalOpen = false;
            if (modalId === 'modal-assign-division') data.isAssignDivisionModalOpen = false;
            if (modalId === 'modal-assign-engineer') data.isAssignEngineerModalOpen = false;
            if (modalId === 'modal-edit-pipeline') data.isEditPipelineModalOpen = false;
            if (modalId === 'modal-assign') data.isAssignModalOpen = false;
            if (modalId === 'modal-assign-technical') data.isAssignTechnicalModalOpen = false;
            if (modalId === 'modal-upload-technical-doc') data.isUploadTechnicalDocModalOpen = false;
            if (modalId === 'modal-verify-technical') data.isVerifyTechnicalModalOpen = false;
            if (modalId === 'modal-approve') data.isApproveModalOpen = false;
            if (modalId === 'modal-edit-meta') data.isEditMetaModalOpen = false;
            if (modalId === 'modal-add-milestone') data.isAddMilestoneModalOpen = false;
            if (modalId === 'modal-upload-doc') data.isUploadDocModalOpen = false;
            if (modalId === 'modal-upload-sales-doc') data.isUploadSalesDocModalOpen = false;
            if (modalId === 'modal-delete') data.isDeleteModalOpen = false;
            if (modalId === 'modal-complete') data.isCompleteModalOpen = false;
        }
    };

    window.openHandoverModalCustom = function(target) {
        const data = window.getProjectData();
        if (data) {
            if (target === 'both') {
                data.handoverTargets = ['pmo', 'managed_service'];
                data.handoverTargetType = 'both';
            } else if (target === 'managed_service') {
                data.handoverTargets = ['managed_service'];
                data.handoverTargetType = 'managed_service';
            } else if (target === 'pmo') {
                data.handoverTargets = ['pmo'];
                data.handoverTargetType = 'pmo';
            }
            data.isHandoverModalOpen = true;
        }
        window.openModal('modal-handover');
    };

    window.startEditTitle = function() {
        const display = document.getElementById('title-display-container');
        const form = document.getElementById('title-edit-form');
        const input = document.getElementById('project-title-input');
        if (display && form && input) {
            display.classList.add('hidden');
            form.classList.remove('hidden');
            form.classList.add('flex');
            input.focus();
            input.select();
        }
    };

    window.cancelEditTitle = function() {
        const display = document.getElementById('title-display-container');
        const form = document.getElementById('title-edit-form');
        if (display && form) {
            form.classList.add('hidden');
            form.classList.remove('flex');
            display.classList.remove('hidden');
        }
    };

    window.openAssignModalCustom = function(role) {
        const roleInput = document.getElementById('assign-role-input');
        const headBox = document.getElementById('assign-head-box');
        const directorBox = document.getElementById('assign-director-box');
        const titleEl = document.getElementById('assign-modal-title');
        const submitBtn = document.getElementById('assign-submit-btn');

        if (roleInput) roleInput.value = role;

        if (role === 'head') {
            if (headBox) headBox.style.setProperty('display', 'block', 'important');
            if (directorBox) directorBox.style.setProperty('display', 'none', 'important');
            if (titleEl) titleEl.innerText = 'Assign Review ke Head Divisi (Pak Susanto)';
            if (submitBtn) submitBtn.innerText = 'Tugaskan ke Head Divisi';
        } else if (role === 'director') {
            if (headBox) headBox.style.setProperty('display', 'none', 'important');
            if (directorBox) directorBox.style.setProperty('display', 'block', 'important');
            if (titleEl) titleEl.innerText = 'Assign Otorisasi ke Direktur (Pak Hariyadi)';
            if (submitBtn) submitBtn.innerText = 'Tugaskan ke Direktur';
        } else {
            if (headBox) headBox.style.setProperty('display', 'block', 'important');
            if (directorBox) directorBox.style.setProperty('display', 'block', 'important');
            if (titleEl) titleEl.innerText = 'Assign Review ke Pimpinan';
            if (submitBtn) submitBtn.innerText = 'Tugaskan Sekarang';
        }

        window.openModal('modal-assign');
    };

    window.openAssignTechnicalModalCustom = function(role) {
        const roleInput = document.getElementById('assign-technical-role-input');
        const boxBdm = document.getElementById('box-assign-bdm');
        const boxPresales = document.getElementById('box-assign-presales');
        const boxArchitect = document.getElementById('box-assign-architect');
        const titleEl = document.getElementById('assign-technical-modal-title');
        const subtitleEl = document.getElementById('assign-technical-modal-subtitle');
        const submitBtn = document.getElementById('assign-technical-submit-btn');

        if (roleInput) roleInput.value = role;

        if (boxBdm) boxBdm.style.setProperty('display', role === 'bdm' ? 'block' : 'none', 'important');
        if (boxPresales) boxPresales.style.setProperty('display', role === 'presales' ? 'block' : 'none', 'important');
        if (boxArchitect) boxArchitect.style.setProperty('display', role === 'architect' ? 'block' : 'none', 'important');

        if (role === 'bdm') {
            if (titleEl) titleEl.innerText = 'Penunjukan PIC Business Development';
            if (subtitleEl) subtitleEl.innerText = 'Penetapan Product Manager & Penanggung Jawab Verifikasi Solusi Teknis';
            if (submitBtn) submitBtn.innerText = 'Simpan Penunjukan PIC BD';
        } else if (role === 'presales') {
            if (titleEl) titleEl.innerText = 'Penugasan Pre-Sales Specialist';
            if (subtitleEl) subtitleEl.innerText = 'Penetapan Personel Pre-Sales untuk Penyusunan Proposal & BoQ';
            if (submitBtn) submitBtn.innerText = 'Simpan Penugasan Pre-Sales';
        } else if (role === 'architect') {
            if (titleEl) titleEl.innerText = 'Penugasan Solution Architect';
            if (subtitleEl) subtitleEl.innerText = 'Penetapan Solution Architect untuk Desain Topologi & Sizing Solusi';
            if (submitBtn) submitBtn.innerText = 'Simpan Penugasan Solution Architect';
        }

        window.openModal('modal-assign-technical');
    };

    window.openApproveModalCustom = function(role) {
        const input = document.getElementById('modal-approve-role-input');
        const title = document.getElementById('modal-approve-title');
        if (input) input.value = role;
        if (title) {
            title.innerText = role === 'director' ? 'Approval Direktur (Pak Hariyadi)' : 'Approval Head Divisi (Pak Susanto)';
        }
        window.openModal('modal-approve');
    };

    window.openUploadTechnicalModalCustom = function(role) {
        const input = document.getElementById('modal-upload-technical-role-input');
        const title = document.getElementById('modal-upload-technical-title');
        if (input) input.value = role;
        if (title) {
            title.innerText = role === 'presales' ? 'Unggah Berkas Proposal & BoQ (Pre-Sales)' : 'Unggah Desain Arsitektur & Topologi (Solution Architect)';
        }
        window.openModal('modal-upload-technical-doc');
    };

    function projectDetailPage(initialStage, currentDbStatus) {
        return {
            activeStageTab: initialStage || 'draft',
            currentStatus: currentDbStatus || 'Draft',
            
            isHandoverModalOpen: false,
            handoverTargetType: '{{ $project->handover_target ?: (($project->stage === 'Operate') ? 'managed_service' : 'pmo') }}',
            handoverTargets: {!! ($project->handover_target === 'both') ? "['pmo', 'managed_service']" : (($project->handover_target === 'managed_service' || $project->stage === 'Operate') ? "['managed_service']" : "['pmo']") !!},

            toggleHandoverTarget(val) {
                if (this.handoverTargets.includes(val)) {
                    if (this.handoverTargets.length > 1) {
                        this.handoverTargets = this.handoverTargets.filter(t => t !== val);
                    }
                } else {
                    this.handoverTargets.push(val);
                }
                if (this.handoverTargets.includes('pmo') && this.handoverTargets.includes('managed_service')) {
                    this.handoverTargetType = 'both';
                } else if (this.handoverTargets.includes('managed_service')) {
                    this.handoverTargetType = 'managed_service';
                } else {
                    this.handoverTargetType = 'pmo';
                }
            },
            isApproveModalOpen: false,
            approveRole: 'head', // 'head' (Susanto) or 'director' (Hariyadi)

            isAssignModalOpen: false,
            assignRole: 'head', // 'head', 'director', 'both'

            isEditPipelineModalOpen: false,
            isAssignTechnicalModalOpen: false,
            assignTechnicalRole: 'bdm', // 'bdm', 'presales', 'architect'
            isUploadTechnicalDocModalOpen: false,
            uploadTechnicalRole: 'presales', // 'presales' or 'architect'
            isVerifyTechnicalModalOpen: false,

            isEditMetaModalOpen: false,
            isAddMilestoneModalOpen: false,
            isUploadDocModalOpen: false,
            isUploadSalesDocModalOpen: false,
            isDeleteModalOpen: false,
            isCompleteModalOpen: false,
            isAssignDivisionModalOpen: false,
            isAssignEngineerModalOpen: false,

            openHandoverModal(target = null) {
                if (target === 'both') {
                    this.handoverTargets = ['pmo', 'managed_service'];
                    this.handoverTargetType = 'both';
                } else if (target === 'managed_service') {
                    this.handoverTargets = ['managed_service'];
                    this.handoverTargetType = 'managed_service';
                } else if (target === 'pmo') {
                    this.handoverTargets = ['pmo'];
                    this.handoverTargetType = 'pmo';
                }
                this.isHandoverModalOpen = true;
                window.openModal('modal-handover');
            },

            openAssignDivisionModal() {
                this.isAssignDivisionModalOpen = true;
                window.openModal('modal-assign-division');
            },

            openAssignEngineerModal() {
                this.isAssignEngineerModalOpen = true;
                window.openModal('modal-assign-engineer');
            },

            confirmCompleteProject() {
                this.isCompleteModalOpen = true;
                window.openModal('modal-complete');
            },

            openAssignModal(role = 'head') {
                this.assignRole = role;
                this.isAssignModalOpen = true;
                window.openAssignModalCustom(role);
            },

            openApproveModal(role = 'head') {
                this.approveRole = role;
                this.isApproveModalOpen = true;
                window.openApproveModalCustom(role);
            },

            openEditPipelineModal() {
                this.isEditPipelineModalOpen = true;
                window.openModal('modal-edit-pipeline');
            },

            openAssignTechnicalModal(role = 'bdm') {
                this.assignTechnicalRole = role;
                this.isAssignTechnicalModalOpen = true;
                window.openAssignTechnicalModalCustom(role);
            },

            openUploadTechnicalModal(role = 'presales') {
                this.uploadTechnicalRole = role;
                this.isUploadTechnicalDocModalOpen = true;
                window.openUploadTechnicalModalCustom(role);
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
                    <div class="space-y-1 flex-1 min-w-0">
                        {{-- 1. Display Mode (Default) --}}
                        <div class="flex items-center gap-2.5 flex-wrap" id="title-display-container">
                            <h1 class="text-2xl sm:text-[26px] font-extrabold text-slate-900 tracking-tight leading-tight @if(!$isPresalesOrSaOnly) cursor-pointer hover:text-[#8F0A0D] transition group @endif flex items-center gap-2"
                                @if(!$isPresalesOrSaOnly) onclick="window.startEditTitle()" title="Klik untuk edit nama proyek" @endif>
                                <span>{{ $project->name }}</span>
                                @if(!$isPresalesOrSaOnly)
                                    <span class="p-1.5 text-slate-400 group-hover:text-[#8F0A0D] group-hover:bg-red-50 border border-slate-200 group-hover:border-red-200 rounded-lg transition shadow-2xs">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </span>
                                @endif
                            </h1>
                        </div>

                        {{-- 2. Direct Inline Edit Mode --}}
                        @if(!$isPresalesOrSaOnly)
                        <form id="title-edit-form" action="{{ route('projects.meta_update', $project->id) }}" method="POST" class="hidden items-center gap-2 flex-wrap pb-1">
                            @csrf
                            <input type="text" name="name" id="project-title-input" value="{{ $project->name }}" required
                                   class="text-lg sm:text-xl font-bold text-slate-900 px-3.5 py-1.5 rounded-xl border-2 border-[#8F0A0D] focus:outline-none focus:ring-2 focus:ring-red-500/30 bg-white shadow-inner min-w-[260px] sm:min-w-[380px]"
                                   onkeydown="if(event.key==='Escape') window.cancelEditTitle();">
                            <button type="submit" class="px-4 py-2 rounded-xl btn-ipnet-primary text-xs font-bold transition shadow-xs cursor-pointer flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span>Simpan</span>
                            </button>
                            <button type="button" onclick="window.cancelEditTitle()" class="px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-slate-600 hover:bg-slate-100 text-xs font-semibold cursor-pointer">
                                Batal
                            </button>
                        </form>
                        @endif

                        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed max-w-3xl">
                            {{ $project->description ?: 'Proyek pengadaan infrastruktur dan solusi teknologi terintegrasi.' }}
                        </p>
                    </div>

                    {{-- Action Buttons --}}
                    @if(!$isPresalesOrSaOnly && ($canAssignSales ?? false))
                    <div class="flex items-center gap-2 shrink-0">
                        @if($currentStatus === 'In Progress')
                            <button type="button" @click="confirmCompleteProject()"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl btn-ipnet-primary text-xs font-bold transition shadow-sm cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                <span>Tandai Selesai</span>
                            </button>
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
                    @endif
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

                    @if(!$isPresalesOrSaOnly)
                    <div class="shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-200">
                        <button type="button" 
                                @click="isEditMetaModalOpen = true; openEditMetaModal()" 
                                onclick="window.openModal('modal-edit-meta')"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 text-xs font-semibold transition cursor-pointer shadow-2xs">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            <span>Edit Estimasi</span>
                        </button>
                    </div>
                    @endif
                </div>

            </div>

            {{-- 3. MAIN 2-COLUMN STRUCTURE (Lead Engineer Dashboard Layout) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                {{-- ══ LEFT MAIN COLUMN (lg:col-span-8) ══ --}}
                <div class="lg:col-span-8 space-y-6">
                           {{-- WORKFLOW STATUS NOTICE (FOR PENDING / COMPLETED) --}}
                    @if($currentStatus === 'Pending')
                        <div class="ipnet-card p-6 border-amber-200 bg-amber-50/50 space-y-2">
                            <div class="font-bold text-amber-900 text-sm flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                Proyek Ditangguhkan (Pending)
                            </div>
                            <p class="text-xs text-amber-800 leading-relaxed">
                                Pengerjaan proyek sedang di-pause sementara waktu menunggu konfirmasi akses site, perizinan, atau kelengkapan berkas kontrak.
                            </p>
                        </div>
                    @elseif($currentStatus === 'Completed')
                        <div class="ipnet-card p-6 border-emerald-200 bg-emerald-50/50 space-y-2">
                            <div class="font-bold text-emerald-900 text-sm flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                Proyek Selesai &amp; BAST Terbit (Completed)
                            </div>
                            <p class="text-xs text-emerald-800 leading-relaxed">
                                Seluruh target milestone teknis telah selesai 100% dan Berita Acara Serah Terima (BAST) pekerjaan telah disahkan bersama klien.
                            </p>
                        </div>
                    @endif

                    {{-- 1. Status Pipeline & Penjualan --}}
                    @include('projects.partials.workflow-pipeline')

                    {{-- 2. Otorisasi Pimpinan (Pak Susanto Djaya & Pak Hariyadi) --}}
                    @include('projects.partials.workflow-leadership')

                    {{-- 3. Tim Solusi Teknis (PIC BD, Presales Akbar, SA Aris Sadewo) --}}
                    @include('projects.partials.workflow-technical-solution')

                    {{-- 4. Serah Terima & Divisi Pelaksana (PMO Rizki & Lead Engineer Nugraha Pratama) --}}
                    @include('projects.partials.workflow-delivery')

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
                            
                            @php
                                $canAddMilestone = $authUser && (
                                    $project->created_by === $authUser->id 
                                    || $project->sales_name === $authUser->name 
                                    || ($project->sales_id && $project->sales_id === $authUser->id)
                                    || !empty(array_intersect(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Head Divisi', 'Group Leader', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager', 'Super Admin', 'Admin', 'Sales', 'Account Manager'], $userRoles))
                                ) && empty(array_intersect(['Presales', 'Pre-Sales', 'Solution Architect', 'Solutions Architect', 'SA'], $userRoles));
                            @endphp
                            @if($canAddMilestone)
                            <button type="button" 
                                    @click="isAddMilestoneModalOpen = true" 
                                    onclick="window.openModal('modal-add-milestone')"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 transition cursor-pointer border border-red-200 shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                <span>Tambah Milestone</span>
                            </button>
                            @endif
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
                                Belum ada milestone yang dibuat.
                            </div>
                        @endif
                    </div>

                    {{-- ═══ 1. BERKAS SALES CARD (CONFIDENTIAL - HANYA SALES & MANAGEMENT) ═══ --}}
                    @php
                        $authUser = auth()->user();
                        $canAccessSalesDocs = $authUser && $project->canAccessSalesDocs($authUser);

                        $salesDocs = \App\Models\ProjectDocument::where('project_id', $project->id)
                            ->where(function($q) {
                                $q->where('stage_name', 'Sales')
                                  ->orWhere('document_key', 'like', 'sales_berkas%');
                            })
                            ->whereNotNull('file_path')
                            ->where('file_path', '!=', '')
                            ->latest()
                            ->get();
                    @endphp

                    @if($canAccessSalesDocs)
                        <div class="ipnet-card p-6 space-y-4">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-2.5">
                                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                        <span class="w-2 h-4 rounded-full bg-[#8F0A0D]"></span>
                                        Berkas Sales
                                    </h3>
                                    <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        {{ $salesDocs->count() }} Berkas
                                    </span>
                                </div>
                                
                                <button type="button" 
                                        @click="isUploadSalesDocModalOpen = true" 
                                        onclick="window.openModal('modal-upload-sales-doc')"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 transition cursor-pointer border border-red-200 shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    <span>Upload Berkas Sales</span>
                                </button>
                            </div>

                            @if($salesDocs->count() > 0)
                                <div class="space-y-2">
                                    @foreach($salesDocs as $doc)
                                        <div class="p-3.5 rounded-xl border border-slate-200 bg-white flex items-center justify-between text-xs hover:border-slate-300 transition shadow-2xs">
                                            <div class="flex items-center gap-3 min-w-0">
                                                <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                </div>
                                                <div class="min-w-0">
                                                    <span class="font-bold text-slate-900 truncate block">{{ $doc->document_title ?? ($doc->document_name ?? 'Berkas Sales') }}</span>
                                                    <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500 truncate">
                                                        @if($doc->file_name)
                                                            <span class="truncate font-mono">{{ $doc->file_name }}</span>
                                                        @endif
                                                        @if($doc->file_size)
                                                            <span>&bull; {{ round($doc->file_size / 1024, 1) }} KB</span>
                                                        @endif
                                                        @if($doc->notes)
                                                            <span class="text-slate-400 italic">({{ $doc->notes }})</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @if($doc->file_path)
                                                <div class="flex items-center gap-2 shrink-0">
                                                    <a href="{{ route('projects.documents.download', [$project->id, $doc->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 transition" title="Unduh Berkas Sales">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        <span>Unduh</span>
                                                    </a>
                                                    <form action="{{ route('projects.documents.delete', [$project->id, $doc->id]) }}" method="POST" onsubmit="return confirm('Hapus berkas sales ini?')" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition cursor-pointer" title="Hapus Berkas Sales">
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
                                    Belum ada berkas sales.
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- ═══ 2. ATTACHMENTS CARD (BERKAS PENDUKUNG - BISA DILIHAT OLEH SEMUA ROLE) ═══ --}}
                    @php
                        $uploadedDocs = \App\Models\ProjectDocument::where('project_id', $project->id)
                            ->where(function($q) {
                                $q->where('stage_name', '!=', 'Sales')
                                  ->where('document_key', 'not like', 'sales_berkas%');
                            })
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
                                                    <span class="text-[11px] text-slate-500 block truncate font-mono">{{ $doc->file_name }}</span>
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
                                Belum ada dokumen pendukung.
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
                            
                            {{-- 1. Inisiasi Proyek (Opportunity Creation) --}}
                            <div class="relative">
                                <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs flex items-center justify-center">
                                    <div class="w-1 h-1 rounded-full bg-white"></div>
                                </div>
                                <div class="font-normal text-slate-700">
                                    Inisiasi Proyek: <strong class="font-semibold text-slate-900">{{ $project->creator ? $project->creator->name : ($project->sales_name ?: 'Sales Team') }}</strong>
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                    {{ \Carbon\Carbon::parse($project->created_at)->format('d M Y H:i') }} (Opportunity Terbuka)
                                </div>
                            </div>

                            {{-- 2. Assign & Persetujuan Pimpinan (Head Divisi & Direktur) --}}
                            @if(!empty($headApproval['assigned']) && empty($headApproval['approved']))
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Diajukan ke Head Divisi: <strong class="font-semibold text-slate-900">{{ $headApproval['assigned_to'] ?? 'Susanto Djaya' }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        {{ $headApproval['assigned_at'] ?? 'Menunggu review' }} (oleh {{ $headApproval['assigned_by'] ?? 'Sales' }})
                                    </div>
                                </div>
                            @endif

                            @if(!empty($directorApproval['assigned']) && empty($directorApproval['approved']))
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Diajukan ke Direktur: <strong class="font-semibold text-slate-900">{{ $directorApproval['assigned_to'] ?? 'Hariyadi' }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        {{ $directorApproval['assigned_at'] ?? 'Menunggu otorisasi' }} (oleh {{ $directorApproval['assigned_by'] ?? 'Sales' }})
                                    </div>
                                </div>
                            @endif

                            @if(!empty($headApproval['approved']))
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Approval Head Divisi: <strong class="font-semibold text-slate-900">{{ $headApproval['assigned_to'] ?? 'Susanto Djaya' }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        {{ $headApproval['date'] ?? 'Disetujui' }}
                                    </div>
                                </div>
                            @endif

                            @if(!empty($directorApproval['approved']))
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Approval Direktur: <strong class="font-semibold text-slate-900">{{ $directorApproval['assigned_to'] ?? 'Hariyadi' }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        {{ $directorApproval['date'] ?? 'Disahkan' }}
                                    </div>
                                </div>
                            @endif

                            {{-- 3. Status Tender Menang / Kontrak PO Terbit --}}
                            @php
                                $isWon = ($project->sales_stage === 'Closed Won' || !empty($project->po_spk_number) || !empty($project->po_number) || in_array($project->status, ['In Progress', 'Completed']));
                                $isLost = ($project->sales_stage === 'Closed Lost');
                            @endphp
                            @if($isWon)
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Status Tender / Deal: <strong class="font-semibold text-slate-900">Menang (Closed Won)</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        @if(!empty($project->po_spk_number) || !empty($project->po_number))
                                            PO/SPK Kontrak: {{ $project->po_spk_number ?: $project->po_number }}
                                        @else
                                            Kontrak &amp; PO Resmi Terbit
                                        @endif
                                    </div>
                                </div>
                            @elseif($isLost)
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-slate-400 ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Status Tender / Deal: <strong class="font-semibold text-rose-600">Batal / Kalah (Closed Lost)</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Tender atau prospek tidak berlanjut
                                    </div>
                                </div>
                            @endif

                            {{-- 4. Penunjukan PIC BD --}}
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

                            {{-- 5. Penugasan Tim Solusi Teknis (Pre-Sales & SA) --}}
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

                            {{-- 6. Unggah Dokumen Proposal & Desain Topologi --}}
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

                            {{-- 7. Verifikasi Solusi BD --}}
                            @if(!empty($bdVerification['verified_at']) || (!empty($bdVerification['status']) && in_array($bdVerification['status'], ['Approved', 'Revision Needed']) && !empty($bdVerification['verified_by'])))
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

                            {{-- 8. Handover Sales ke PMO --}}
                            @if($project->pm)
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Handover Delivery ke PMO: <strong class="font-semibold text-slate-900">{{ $project->pm->name }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Pengerjaan &amp; alokasi manajemen proyek dipimpin oleh PMO
                                    </div>
                                </div>
                            @endif

                            {{-- 9. Disposisi Divisi Pelaksana (PMO ke Lead Engineer) --}}
                            @if(!empty($project->division_id) && !empty($project->division))
                                @php
                                    $divName = $project->division->name;
                                    $leadName = 'Lead Engineering Delivery';
                                    if (str_contains(strtolower($divName), 'net') && !str_contains(strtolower($divName), 'lintas') && !str_contains(strtolower($divName), 'security')) {
                                        $leadName = 'Nugraha Pratama (Lead Network)';
                                    } elseif (str_contains(strtolower($divName), 'sec') && !str_contains(strtolower($divName), 'lintas') && !str_contains(strtolower($divName), 'network')) {
                                        $leadName = 'Ignatius Rizky (Lead Security)';
                                    } else {
                                        $leadName = 'Lead Network & Lead Security (Lintas Divisi)';
                                    }
                                @endphp
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Disposisi Divisi: <strong class="font-semibold text-slate-900">{{ $divName }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Didelegasikan ke {{ $leadName }}
                                    </div>
                                </div>
                            @endif

                            {{-- 10. Penugasan Teknisi Lapangan (Hanya muncul jika sudah ada Field Engineer yang dialokasikan) --}}
                            @if(!empty($project->division_id) && !empty($project->division) && $uniqueEngineers->count() > 0)
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Penugasan Teknisi Lapangan: <strong class="font-semibold text-slate-900">{{ $uniqueEngineers->pluck('name')->join(', ') }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        Alokasi pengerjaan teknis &amp; implementasi lapangan aktif ({{ $project->tasks->count() }} Tugas)
                                    </div>
                                </div>
                            @endif

                            {{-- 11. Handover Lanjutan ke Divisi Managed Service (Untuk Proyek Dual Scope / Managed Service) --}}
                            @php
                                $msHandoverTimeline = $handoverData['ms_handover'] ?? [];
                            @endphp
                            @if(!empty($msHandoverTimeline['handed_over']))
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-purple-700 to-indigo-700 ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Handover Lanjutan: <strong class="font-semibold text-purple-900">Divisi Managed Service ({{ $msHandoverTimeline['ms_lead_name'] ?? 'Lead MS' }})</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        {{ $msHandoverTimeline['handed_over_at'] ?? '-' }} (oleh {{ $msHandoverTimeline['handed_over_by'] ?? 'PMO' }})
                                        @if(!empty($msHandoverTimeline['notes']))
                                            <div class="text-slate-600 font-normal italic mt-1 bg-slate-50 p-2 rounded border border-slate-200">"{{ $msHandoverTimeline['notes'] }}"</div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            {{-- 12. Penugasan Teknisi Managed Service --}}
                            @if(!empty($msHandoverTimeline['ms_engineers']) && count($msHandoverTimeline['ms_engineers']) > 0)
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-purple-700 to-indigo-700 ring-4 ring-white shadow-2xs"></div>
                                    <div class="font-normal text-slate-700">
                                        Teknisi Managed Service: <strong class="font-semibold text-purple-900">{{ implode(', ', $msHandoverTimeline['ms_engineers']) }}</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        {{ $msHandoverTimeline['assigned_at'] ?? 'Ditugaskan' }} (Operasional &amp; SLA Aktif)
                                    </div>
                                </div>
                            @endif

                            {{-- 13. Status Akhir: Proyek Selesai (Completed / Pengadaan Barang Selesai) --}}
                            @php
                                $completionData = $handoverData['completion'] ?? [];
                                $isProjectCompleted = ($currentStatus === 'Completed' || $project->status === 'Completed' || !empty($completionData['completed']));
                                $completedBy = $completionData['completed_by'] ?? ($project->sales_name ?: ($project->creator ? $project->creator->name : 'Sales'));
                                $completedAt = $completionData['completed_at'] ?? \Carbon\Carbon::parse($project->updated_at)->format('d M Y H:i');
                            @endphp
                            @if($isProjectCompleted)
                                <div class="relative">
                                    <div class="absolute -left-[24px] top-1 w-3 h-3 rounded-full bg-gradient-to-tr from-[#8F0A0D] to-[#BA1B1D] ring-4 ring-white shadow-2xs flex items-center justify-center">
                                        <div class="w-1 h-1 rounded-full bg-white"></div>
                                    </div>
                                    <div class="font-normal text-slate-700">
                                        Proyek Selesai: <strong class="font-semibold text-slate-900">Completed</strong>
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 font-mono">
                                        {{ $completedAt }} (oleh {{ $completedBy }})
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    @php
                        $clientRecord = $project->clientRecord;

                        if (!$clientRecord || empty($clientRecord->pic_name)) {
                            // Search by department or name words inside project name / client string
                            $searchWords = array_filter(explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', ' ', $project->name . ' ' . $project->client)));
                            foreach ($searchWords as $w) {
                                if (strlen($w) >= 3) {
                                    $found = \App\Models\Client::where('department', 'like', "%{$w}%")
                                        ->orWhere('name', 'like', "%{$w}%")
                                        ->whereNotNull('pic_name')
                                        ->first();
                                    if ($found) {
                                        $clientRecord = $found;
                                        break;
                                    }
                                }
                            }
                        }

                        if (!$clientRecord) {
                            $clientRecord = \App\Models\Client::where('name', $project->client)
                                ->orWhere('department', $project->client)
                                ->first();
                        }

                        if (!$clientRecord && \App\Models\Client::whereNotNull('pic_name')->count() === 1) {
                            $clientRecord = \App\Models\Client::whereNotNull('pic_name')->first();
                        }

                        $clientDisplayName = $clientRecord ? $clientRecord->name : ($project->client ?: '-');
                        $clientDept = $clientRecord && $clientRecord->department ? $clientRecord->department : ($project->client_department ?: null);
                        $clientEmail = $clientRecord && !empty($clientRecord->email) ? $clientRecord->email : ($project->customer_pic_finance ?: ($project->customer_pic_technical ?: '-'));
                        $clientPicName = $clientRecord && !empty($clientRecord->pic_name) ? $clientRecord->pic_name : ($project->customer_pic_name ?: '-');
                        $clientPhone = $clientRecord && !empty($clientRecord->phone) ? $clientRecord->phone : ($project->customer_pic_business ?: '-');
                    @endphp
                    {{-- 2. INFORMASI KLIEN CARD --}}
                    <div class="ipnet-card p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-2 h-4 rounded-full bg-[#8F0A0D]"></span>
                                Informasi Klien
                            </h3>
                            <div class="flex items-center gap-2">
                                @if(!$isPresalesOrSaOnly && ($canAssignSales ?? false))
                                <button type="button" onclick="window.openModal('modal-edit-client')" class="px-2.5 py-1 bg-white hover:bg-slate-50 text-slate-700 hover:text-[#8F0A0D] border border-slate-200 rounded-lg text-[11px] font-bold shadow-2xs transition flex items-center gap-1 cursor-pointer">
                                    <svg class="w-3 h-3 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    <span>Edit / Hubungkan</span>
                                </button>
                                @endif
                                @if($clientRecord)
                                    <a href="{{ route('clients.index') }}" class="text-[11px] font-bold text-slate-400 hover:text-[#8F0A0D] transition flex items-center gap-0.5" title="Lihat Database Klien">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="space-y-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">CLIENT NAME / INSTANSI</span>
                                <div class="font-bold text-slate-900 text-xs mt-0.5">
                                    {{ $clientDisplayName }}
                                    @if($clientDept)
                                        <span class="text-slate-500 font-semibold text-[11px]">({{ $clientDept }})</span>
                                    @endif
                                </div>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">CLIENT EMAIL</span>
                                <div class="font-bold text-slate-900 text-xs mt-0.5 truncate">{{ $clientEmail }}</div>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">PIC KLIEN (CUSTOMER)</span>
                                <div class="font-bold text-slate-900 text-xs mt-0.5">{{ $clientPicName }}</div>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50/70 border border-slate-200">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">CONTACT / PHONE PIC</span>
                                <div class="font-bold text-slate-900 text-xs mt-0.5">{{ $clientPhone }}</div>
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
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODALS: CENTERED & CLEAN                                --}}
    {{-- ======================================================== --}}

    {{-- 1. FORMULIR SERAH TERIMA PROYEK & PENUGASAN MODAL --}}
    <div id="modal-handover" x-show="isHandoverModalOpen" x-cloak 
         @click.self="isHandoverModalOpen = false; window.closeModal('modal-handover')"
         onclick="if(event.target === this) window.closeModal('modal-handover')"
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div @click.stop class="relative bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-slate-200 m-auto max-h-[90vh] flex flex-col overflow-hidden">
            
            {{-- Header (Fixed / Non-Scrollable) --}}
            <div class="flex items-center justify-between border-b border-slate-100 p-5 sm:p-6 shrink-0 bg-white">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">Serah Terima &amp; Alokasi Kategori Proyek</h3>
                    <p class="text-[12px] text-slate-500 mt-0.5">Penetapan legalitas kontrak PO, pemilihan alokasi tipe kategori proyek &amp; penugasan tim pelaksana.</p>
                </div>
                <button type="button" @click="isHandoverModalOpen = false; window.closeModal('modal-handover')" onclick="window.closeModal('modal-handover')" class="text-slate-400 hover:text-[#8F0A0D] text-xl font-bold p-1 cursor-pointer transition">✕</button>
            </div>

            {{-- Form with Scrollable Body & Fixed Footer --}}
            <form action="{{ route('projects.assign', $project->id) }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0 overflow-hidden text-xs font-semibold">
                @csrf
                <input type="hidden" name="role_type" value="pm">

                {{-- Scrollable Body --}}
                <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-5">
                    
                    {{-- 1. PILIHAN KARAKTERISTIK & TARGET SERAH TERIMA --}}
                    <div class="space-y-2">
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px]">
                            1. Pilih Karakteristik &amp; Target Serah Terima <span class="text-[#8F0A0D]">*</span>
                        </label>
                        <input type="hidden" name="handover_target" :value="handoverTargetType">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Option 1: PMO Implementasi --}}
                            <label @click="toggleHandoverTarget('pmo')"
                                   class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition shadow-2xs select-none"
                                   :class="handoverTargets.includes('pmo') ? '!border-[#8F0A0D] !bg-red-50/40 text-gray-900 shadow-xs ring-1 ring-[#8F0A0D]/30' : 'border-slate-200 hover:border-slate-400 text-gray-600 bg-white'">
                                <input type="checkbox" value="pmo" :checked="handoverTargets.includes('pmo')" @click.stop="toggleHandoverTarget('pmo')" class="mt-0.5 w-4 h-4 rounded text-[#8F0A0D] accent-[#8F0A0D] cursor-pointer">
                                <div>
                                    <div class="font-bold text-xs flex items-center gap-1.5">
                                        <span :class="handoverTargets.includes('pmo') ? 'text-[#8F0A0D] font-extrabold' : 'text-slate-800'">Proyek Implementasi</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 font-bold">PMO Delivery</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-1 leading-snug font-normal">
                                        Capex / Deployment: Pengadaan perangkat, instalasi kabel/rack, konfigurasi, migrasi, UAT, dan BAST 1.
                                    </p>
                                </div>
                            </label>

                            {{-- Option 2: Managed Service --}}
                            <label @click="toggleHandoverTarget('managed_service')"
                                   class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition shadow-2xs select-none"
                                   :class="handoverTargets.includes('managed_service') ? '!border-[#8F0A0D] !bg-red-50/40 text-gray-900 shadow-xs ring-1 ring-[#8F0A0D]/30' : 'border-slate-200 hover:border-slate-400 text-gray-600 bg-white'">
                                <input type="checkbox" value="managed_service" :checked="handoverTargets.includes('managed_service')" @click.stop="toggleHandoverTarget('managed_service')" class="mt-0.5 w-4 h-4 rounded text-[#8F0A0D] accent-[#8F0A0D] cursor-pointer">
                                <div>
                                    <div class="font-bold text-xs flex items-center gap-1.5">
                                        <span :class="handoverTargets.includes('managed_service') ? 'text-[#8F0A0D] font-extrabold' : 'text-slate-800'">Kontrak Managed Service</span>
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 font-bold">Operate MS</span>
                                    </div>
                                    <p class="text-[11px] text-gray-500 mt-1 leading-snug font-normal">
                                        Opex / Retainer: Pemeliharaan rutin, monitoring, Preventive Maintenance berkala, SLA Uptime, &amp; On-Call Support.
                                    </p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- 2. LEGALITAS KONTRAK & FINANSIAL --}}
                    <div class="pt-3 border-t border-gray-100 space-y-3">
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px]">
                            2. Legalitas Kontrak &amp; Finansial
                        </label>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                    Nomor PO / SPK / Kontrak <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="text" name="po_spk_number" value="{{ $project->po_spk_number }}" required
                                       class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] hover:border-slate-400 focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>

                            <div>
                                <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                    Tanggal PO / SPK <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <input type="date" name="po_spk_date" value="{{ $project->po_spk_date ? \Carbon\Carbon::parse($project->po_spk_date)->format('Y-m-d') : date('Y-m-d') }}" required
                                       class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] hover:border-slate-400 focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition cursor-pointer">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            {{-- Formatted Rupiah Input --}}
                            <div x-data="{
                                rawVal: '{{ $project->contract_value ? (int)$project->contract_value : '' }}',
                                displayVal: '{{ $project->contract_value ? number_format((float)$project->contract_value, 0, ',', '.') : '' }}',
                                formatRupiah(val) {
                                    let num = val.replace(/[^0-9]/g, '');
                                    this.rawVal = num;
                                    this.displayVal = num ? new Intl.NumberFormat('id-ID').format(num) : '';
                                }
                            }">
                                <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                    Nilai Final Kontrak (Rp) <span class="text-[#8F0A0D]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs pointer-events-none">Rp</span>
                                    <input type="text" 
                                           x-model="displayVal" 
                                           @input="formatRupiah($event.target.value)" 
                                           required
                                           class="w-full pl-10 pr-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-[#1E293B] hover:border-slate-400 focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                                    <input type="hidden" name="contract_value" :value="rawVal">
                                </div>
                            </div>

                            <div>
                                <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                    Upload PO / Kontrak (PDF/ZIP)
                                </label>
                                <input type="file" name="po_spk_file" accept=".pdf,.docx,.xlsx,.zip,.rar"
                                       class="w-full px-3 py-1.5 bg-white border border-[#CBD5E1] rounded-xl text-[11px] text-[#64748B] hover:border-slate-400 focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-[#8F0A0D] file:text-white hover:file:bg-[#73080A] cursor-pointer transition">
                                @if(!empty($project->po_spk_file))
                                    <div class="text-[10px] text-emerald-600 mt-1 font-semibold">✓ Berkas PO tersimpan di sistem</div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                Billing Terms (Termin &amp; Skema Pembayaran) <span class="text-[#8F0A0D]">*</span>
                            </label>
                            <textarea name="billing_terms" rows="2" required
                                      class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] hover:border-slate-400 focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none">{{ $project->billing_terms }}</textarea>
                        </div>
                    </div>

                    {{-- 3. PENUGASAN & KOMITMEN SCOPE PROYEK --}}
                    <div class="pt-3 border-t border-gray-100 space-y-3.5">
                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[11px]">
                            3. Penugasan &amp; Komitmen Layanan
                        </label>

                        {{-- User Selection --}}
                        <div>
                            <label class="block font-semibold text-[#64748B] text-[11px] mb-1" 
                                   x-text="handoverTargetType === 'managed_service' ? 'PILIH LEAD MAINTENANCE / MANAGED SERVICE *' : 'PILIH PROJECT MANAGER (PMO) *'"></label>
                            <select name="user_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-[#CBD5E1] text-xs text-slate-900 hover:border-slate-400 focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer bg-white font-bold transition">
                                <option value="">-- Pilih Penanggung Jawab --</option>
                                @foreach($pmoUsers as $pmo)
                                    <option value="{{ $pmo->id }}" {{ ($project->pm_id == $pmo->id || (empty($project->pm_id) && str_contains(strtolower($pmo->name), 'rizki'))) ? 'selected' : '' }}>
                                        (Lead) {{ $pmo->name }}
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

                        {{-- Khusus Managed Service Parameter --}}
                        <div x-show="handoverTargetType === 'managed_service' || handoverTargetType === 'both'" x-cloak class="p-4 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] space-y-3.5">
                            <div class="flex items-center gap-2 text-gray-800 font-bold text-xs uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                <span>Parameter Khusus Layanan Managed Service &amp; SLA</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div>
                                    <label class="block font-semibold text-gray-700 text-[11px] mb-1">
                                        SLA Tier <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <select name="sla_tier"
                                            class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-bold text-gray-800 hover:border-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer transition">
                                        <option value="Platinum" {{ ($project->sla_tier ?? '') === 'Platinum' ? 'selected' : '' }}>Platinum (24x7 MTTR 2 Jam, Uptime 99.9%)</option>
                                        <option value="Gold" {{ ($project->sla_tier ?: 'Gold') === 'Gold' ? 'selected' : '' }}>Gold (8x5 MTTR 4 Jam, Uptime 99.5%)</option>
                                        <option value="Silver" {{ ($project->sla_tier ?? '') === 'Silver' ? 'selected' : '' }}>Silver (8x5 Next Business Day, Uptime 99.0%)</option>
                                        <option value="Bronze" {{ ($project->sla_tier ?? '') === 'Bronze' ? 'selected' : '' }}>Bronze (Best Effort On-Call Support)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-semibold text-gray-700 text-[11px] mb-1">
                                        Coverage Support Hours
                                    </label>
                                    <select name="sla_coverage_hours"
                                            class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-semibold text-gray-800 hover:border-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer transition">
                                        <option value="24x7">24x7 (24 Jam 7 Hari - Termasuk Hari Libur)</option>
                                        <option value="8x5">8x5 (Jam Kerja Senin - Jumat 08:00 - 17:00)</option>
                                        <option value="12x7">12x7 (08:00 - 20:00 Setiap Hari)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                                <div>
                                    <label class="block font-semibold text-gray-700 text-[11px] mb-1">
                                        Frekuensi Preventive Maint.
                                    </label>
                                    <select name="maintenance_frequency"
                                            class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-gray-800 hover:border-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer transition">
                                        <option value="Monthly">Bulanan (Monthly Routine)</option>
                                        <option value="Quarterly">Triwulanan (Quarterly - 3 Bulan)</option>
                                        <option value="Bi-Annual">Semesteran (Bi-Annual - 6 Bulan)</option>
                                        <option value="Annual">Tahunan (Annual)</option>
                                        <option value="On-Demand">On-Demand / Incident Based</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-semibold text-gray-700 text-[11px] mb-1">
                                        Tanggal Mulai Layanan
                                    </label>
                                    <input type="date" name="service_start_date" value="{{ $project->service_start_date ? \Carbon\Carbon::parse($project->service_start_date)->format('Y-m-d') : '' }}"
                                           class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] hover:border-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer transition">
                                </div>

                                <div>
                                    <label class="block font-semibold text-gray-700 text-[11px] mb-1">
                                        Tanggal Akhir Kontrak
                                    </label>
                                    <input type="date" name="service_end_date" value="{{ $project->service_end_date ? \Carbon\Carbon::parse($project->service_end_date)->format('Y-m-d') : '' }}"
                                           class="w-full px-3.5 py-2 bg-white border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] hover:border-slate-400 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 cursor-pointer transition">
                                </div>
                            </div>
                        </div>

                        {{-- SLA & Terms --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                    SLA &amp; Garansi Komitmen
                                </label>
                                <input type="text" name="sla_commitment" value="{{ $project->sla_commitment }}"
                                       class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] hover:border-slate-400 focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>

                            <div>
                                <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                    Commercial Terms
                                </label>
                                <input type="text" name="commercial_terms" value="{{ $project->commercial_terms }}"
                                       class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] hover:border-slate-400 focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                    Komitmen Khusus dari Sales
                                </label>
                                <textarea name="special_commitment" rows="2"
                                          class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] hover:border-slate-400 focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none">{{ $project->special_commitment }}</textarea>
                            </div>

                            <div>
                                <label class="block font-semibold text-[#64748B] text-[11px] mb-1">
                                    Exclusions (Batasan di Luar Scope)
                                </label>
                                <textarea name="exclusions" rows="2"
                                          class="w-full px-3.5 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] hover:border-slate-400 focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]/20 transition resize-none">{{ $project->exclusions }}</textarea>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer (Fixed / Non-Scrollable) --}}
                <div class="flex items-center justify-end p-4 sm:p-5 border-t border-slate-100 bg-slate-50/70 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <button type="button" @click="isHandoverModalOpen = false; window.closeModal('modal-handover')" onclick="window.closeModal('modal-handover')" class="px-4 py-2 text-xs rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-white hover:border-slate-300 cursor-pointer transition">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 text-xs rounded-xl font-bold btn-ipnet-primary cursor-pointer transition shadow-sm">
                            Simpan &amp; Serah Terimakan Proyek
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DISPOSISI KE DIVISI PELAKSANA (BISA PILIH NETWORK, SECURITY, ATAU KEDUANYA) --}}
    <div id="modal-assign-division" x-show="isAssignDivisionModalOpen" x-cloak 
         @click.self="isAssignDivisionModalOpen = false; window.closeModal('modal-assign-division')"
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div @click.stop class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Delegasi Divisi Pelaksana Teknis</h3>
                    <p class="text-[11.5px] text-slate-500 mt-0.5">Pilih alokasi divisi penanggung jawab untuk diserahkan ke Lead Engineer terkait.</p>
                </div>
                <button type="button" @click="isAssignDivisionModalOpen = false; window.closeModal('modal-assign-division')" onclick="window.closeModal('modal-assign-division')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.assign_division', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold"
                  x-data="{
                      selectedDivs: {{ ($project->division && str_contains(strtolower($project->division->name), 'sec')) && !str_contains(strtolower($project->division->name), 'net') ? "['security']" : (($project->division && str_contains(strtolower($project->division->name), 'net') && !str_contains(strtolower($project->division->name), 'sec')) ? "['network']" : "['network', 'security']") }},
                      toggle(val) {
                          if (this.selectedDivs.includes(val)) {
                              if (this.selectedDivs.length > 1) {
                                  this.selectedDivs = this.selectedDivs.filter(d => d !== val);
                              }
                          } else {
                              this.selectedDivs.push(val);
                          }
                      },
                      get targetDivisionVal() {
                          if (this.selectedDivs.includes('network') && this.selectedDivs.includes('security')) return 'both';
                          if (this.selectedDivs.includes('security')) return 'security';
                          return 'network';
                      }
                  }">
                @csrf
                <input type="hidden" name="target_division" :value="targetDivisionVal">
                
                <div class="space-y-2.5">
                    <label class="block font-bold text-slate-700 uppercase tracking-wider text-[11px]">
                        PILIH DIVISI PENANGGUNG JAWAB <span class="text-[#8F0A0D]">*</span>
                    </label>

                    <div class="space-y-2.5">
                        {{-- Opsi 1: Divisi Network --}}
                        <label @click="toggle('network')"
                               class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition shadow-2xs select-none"
                               :class="selectedDivs.includes('network') ? '!border-[#8F0A0D] !bg-red-50/40 shadow-xs ring-1 ring-[#8F0A0D]/30' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <input type="checkbox" value="network" :checked="selectedDivs.includes('network')" @click.stop="toggle('network')" class="mt-1 w-4 h-4 rounded text-[#8F0A0D] accent-[#8F0A0D] cursor-pointer">
                            <div>
                                <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                    <span :class="selectedDivs.includes('network') ? 'text-[#8F0A0D] font-extrabold' : 'text-slate-900'">Divisi Jaringan &amp; Infrastruktur (Network)</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-bold">Lead: Nugraha Pratama</span>
                                </div>
                                <p class="text-[11px] text-slate-500 font-normal mt-1 leading-relaxed">
                                    Implementasi routing, switching, wireless enterprise, cabling structure, gateway, dan konfigurasi network infrastructure.
                                </p>
                            </div>
                        </label>

                        {{-- Opsi 2: Divisi Security --}}
                        <label @click="toggle('security')"
                               class="flex items-start gap-3 p-3.5 rounded-xl border-2 cursor-pointer transition shadow-2xs select-none"
                               :class="selectedDivs.includes('security') ? '!border-[#8F0A0D] !bg-red-50/40 shadow-xs ring-1 ring-[#8F0A0D]/30' : 'border-slate-200 hover:border-slate-300 bg-white'">
                            <input type="checkbox" value="security" :checked="selectedDivs.includes('security')" @click.stop="toggle('security')" class="mt-1 w-4 h-4 rounded text-[#8F0A0D] accent-[#8F0A0D] cursor-pointer">
                            <div>
                                <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                    <span :class="selectedDivs.includes('security') ? 'text-[#8F0A0D] font-extrabold' : 'text-slate-900'">Divisi Keamanan Siber (Security)</span>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 font-bold">Lead: Ignatius Rizky</span>
                                </div>
                                <p class="text-[11px] text-slate-500 font-normal mt-1 leading-relaxed">
                                    Implementasi Next-Generation Firewall (NGFW), SIEM/SOC, Endpoint Protection (EDR), Vulnerability Assessment, &amp; Hardening.
                                </p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="isAssignDivisionModalOpen = false; window.closeModal('modal-assign-division')" onclick="window.closeModal('modal-assign-division')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition shadow-xs">
                        Konfirmasi &amp; Delegasikan Divisi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL PENUGASAN TEKNISI LAPANGAN (FIELD ENGINEER) OLEH LEAD ENGINEER --}}
    <div id="modal-assign-engineer" x-show="isAssignEngineerModalOpen" x-cloak 
         @click.self="isAssignEngineerModalOpen = false; window.closeModal('modal-assign-engineer')"
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div @click.stop class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Pilih Teknisi Pelaksana Lapangan</h3>
                    <p class="text-[11.5px] text-slate-500 mt-0.5">Pilih Field Engineer penanggung jawab (PIC) untuk mengeksekusi proyek ini.</p>
                </div>
                <button type="button" @click="isAssignEngineerModalOpen = false; window.closeModal('modal-assign-engineer')" onclick="window.closeModal('modal-assign-engineer')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.assign', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="role_type" value="engineer">

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[11px]">PILIH FIELD ENGINEER (PIC) <span class="text-[#8F0A0D]">*</span></label>
                    <select name="user_id" required class="w-full px-3 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-[#8F0A0D]">
                        <option value="">-- Pilih Teknisi Pelaksana --</option>
                        @php
                            $engineers = ($allUsers ?? \App\Models\User::orderBy('name')->get())->filter(function($u) {
                                return $u->hasAnyRole(['Engineer', 'Field Engineer', 'Network Engineer', 'Security Engineer', 'Team Leader Engineering', 'Team Leader', 'Lead Engineer', 'Lead Divisi', 'Managed Service']);
                            });
                        @endphp
                        @foreach($engineers as $eng)
                            <option value="{{ $eng->id }}">{{ $eng->name }} ({{ $eng->roles->pluck('name')->first() ?? 'Engineer' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[11px]">Judul Tugas / Pekerjaan Lapangan</label>
                    <input type="text" name="task_title" value="Implementasi Teknis: {{ $project->name }}" class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-[#8F0A0D]">
                </div>

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[11px]">Target Selesai / Deadline</label>
                    <input type="date" name="deadline" value="{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') : date('Y-m-d', strtotime('+14 days')) }}" class="w-full px-3 py-2 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-xs font-medium text-slate-900 focus:bg-white focus:outline-none focus:border-[#8F0A0D]">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="isAssignEngineerModalOpen = false; window.closeModal('modal-assign-engineer')" onclick="window.closeModal('modal-assign-engineer')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition shadow-xs">
                        Tugaskan Engineer
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL SERAH TERIMA DARI PMO KE MANAGED SERVICE (FASE 2) --}}
    <div id="modal-handover-ms" x-cloak 
         class="fixed inset-0 z-[99999] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                <div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 uppercase tracking-wider">
                        SERAH TERIMA FASE 2
                    </span>
                    <h3 class="text-base font-bold text-slate-900 mt-1">Handover ke Tim Managed Service</h3>
                    <p class="text-[11.5px] text-slate-500 mt-0.5">Serahkan kelanjutan operasional &amp; SLA ke Lead Managed Service setelah implementasi fisik tuntas.</p>
                </div>
                <button type="button" onclick="window.closeModal('modal-handover-ms')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.handover_to_ms', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[11px]">PILIH LEAD MANAGED SERVICE / MAINTENANCE <span class="text-[#8F0A0D]">*</span></label>
                    <select name="ms_lead_id" required class="w-full px-3 py-2.5 bg-white border border-[#CBD5E1] rounded-xl text-xs font-semibold text-slate-900 focus:outline-none focus:border-purple-600">
                        @php
                            $msLeaders = ($allUsers ?? \App\Models\User::orderBy('name')->get())->filter(function($u) {
                                return $u->hasAnyRole(['Managed Service', 'Lead Engineer', 'Lead Divisi', 'Team Leader', 'Team Leader Engineering', 'Engineer', 'Super Admin']);
                            });
                        @endphp
                        <option value="">-- Pilih Lead Managed Service --</option>
                        @foreach($msLeaders as $msl)
                            <option value="{{ $msl->id }}" {{ str_contains(strtolower($msl->name), 'nugraha') || str_contains(strtolower($msl->name), 'rizky') ? 'selected' : '' }}>
                                {{ $msl->name }} ({{ $msl->roles->pluck('name')->first() ?? 'Staff' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[11px]">CATATAN HANDOVER DARI PMO</label>
                    <textarea name="notes" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"
                              placeholder="Contoh: Pekerjaan fisik & deployment jaringan selesai dan sudah UAT bersama klien. Diserahkan untuk SLA & pemeliharaan berkala."></textarea>
                </div>

                <div class="p-3 rounded-xl bg-purple-50/60 border border-purple-200 text-purple-900 text-[11.5px] leading-relaxed">
                    <strong>Catatan Alur:</strong> Status proyek di Sales tetap <em>In Progress</em> dan baru berubah menjadi <em>Completed</em> saat seluruh masa kontrak/SLA benar-benar selesai.
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="window.closeModal('modal-handover-ms')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold text-white bg-gradient-to-r from-purple-700 to-indigo-700 hover:from-purple-800 hover:to-indigo-800 cursor-pointer transition shadow-xs">
                        Konfirmasi Serah Terima ke MS
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL PENUGASAN TEKNISI MANAGED SERVICE (FASE 2) --}}
    <div id="modal-assign-ms-engineer" x-cloak 
         class="fixed inset-0 z-[99999] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div class="relative bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                <div>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 uppercase tracking-wider">
                        PENUGASAN MANAGED SERVICE
                    </span>
                    <h3 class="text-base font-bold text-slate-900 mt-1">Tugaskan Teknisi Managed Service</h3>
                    <p class="text-[11.5px] text-slate-500 mt-0.5">Pilih satu atau beberapa teknisi untuk pemeliharaan rutin, penanganan insiden &amp; SLA.</p>
                </div>
                <button type="button" onclick="window.closeModal('modal-assign-ms-engineer')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.assign_ms_engineer', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf

                <div>
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[11px]">PILIH TEKNISI MANAGED SERVICE <span class="text-[#8F0A0D]">*</span></label>
                    <div class="max-h-48 overflow-y-auto space-y-1.5 p-3 rounded-xl border border-slate-200 bg-slate-50">
                        @php
                            $currentMsEngNames = $handoverData['ms_handover']['ms_engineers'] ?? [];
                            $allEngs = ($allUsers ?? \App\Models\User::orderBy('name')->get())->filter(function($u) {
                                return $u->hasAnyRole(['Engineer', 'Field Engineer', 'Network Engineer', 'Security Engineer', 'Managed Service', 'Lead Engineer']);
                            });
                        @endphp
                        @foreach($allEngs as $e)
                            <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-slate-200 hover:border-purple-300 transition cursor-pointer select-none">
                                <input type="checkbox" name="engineer_ids[]" value="{{ $e->id }}" 
                                       {{ in_array($e->name, $currentMsEngNames) ? 'checked' : '' }}
                                       class="w-4 h-4 rounded text-purple-600 accent-purple-600">
                                <span class="text-slate-900 font-bold text-xs">{{ $e->name }}</span>
                                <span class="text-[10px] text-slate-400">({{ $e->roles->pluck('name')->first() ?? 'Engineer' }})</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[11px]">INSTRUKSI KERJA / JADWAL SLA</label>
                    <textarea name="notes" rows="2.5" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"
                              placeholder="Contoh: Preventive maintenance berkala setiap 2 minggu sekali dan standby 24/7 untuk insiden kritis."></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="window.closeModal('modal-assign-ms-engineer')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold text-white bg-gradient-to-r from-purple-700 to-indigo-700 hover:from-purple-800 hover:to-indigo-800 cursor-pointer transition shadow-xs">
                        Simpan Penugasan Teknisi
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
                <h3 id="modal-approve-title" class="text-base font-bold text-slate-900" x-text="approveRole === 'head' ? 'Approval Head Divisi (Pak Susanto)' : 'Approval Direktur (Pak Hariyadi)'">Approval Head Divisi (Pak Susanto)</h3>
                <button type="button" @click="isApproveModalOpen = false; window.closeModal('modal-approve')" onclick="window.closeModal('modal-approve')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.approve_draft', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="approval_role" id="modal-approve-role-input" :value="approveRole" value="head">

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
                <div>
                    <h3 class="text-base font-bold text-slate-900">Edit Nama &amp; Informasi Proyek</h3>
                    <p class="text-[11px] text-slate-500 mt-0.5">Ubah nama proyek, deskripsi, nilai estimasi, atau tanggal target</p>
                </div>
                <button type="button" @click="isEditMetaModalOpen = false; window.closeModal('modal-edit-meta')" onclick="window.closeModal('modal-edit-meta')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.meta_update', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">NAMA PROYEK <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ $project->name }}" required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 font-bold"
                           placeholder="Masukkan nama proyek...">
                </div>

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">DESKRIPSI PROYEK</label>
                    <textarea name="description" rows="2" 
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500" 
                              placeholder="Deskripsi singkat proyek...">{{ $project->description }}</textarea>
                </div>

                <div x-data="{
                    rawVal: '{{ $project->contract_value ? (int)$project->contract_value : '' }}',
                    displayVal: '{{ $project->contract_value ? number_format((float)$project->contract_value, 0, ',', '.') : '' }}',
                    formatRupiah(val) {
                        let clean = val.replace(/\D/g, '');
                        this.rawVal = clean;
                        this.displayVal = clean ? new Intl.NumberFormat('id-ID').format(clean) : '';
                    }
                }">
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">NILAI ESTIMASI (RP)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs pointer-events-none">Rp</span>
                        <input type="text"
                               x-model="displayVal"
                               @input="formatRupiah($event.target.value)"
                               autocomplete="off"
                               class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 font-bold"
                               placeholder="Contoh: 300.000.000">
                        <input type="hidden" name="contract_value" :value="rawVal">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">PROJECT START</label>
                        <input type="date" name="start_date" value="{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : date('Y-m-d') }}" 
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                    </div>
                    <div>
                        <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">PROJECT END</label>
                        <input type="date" name="deadline" value="{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') : '' }}" 
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

    {{-- 5B. UPLOAD SALES DOCUMENT MODAL (CONFIDENTIAL) --}}
    <div id="modal-upload-sales-doc" x-show="isUploadSalesDocModalOpen" x-cloak 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-2xs overflow-y-auto">
        <div @click.away="isUploadSalesDocModalOpen = false; window.closeModal('modal-upload-sales-doc')" 
             class="relative bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4 m-auto">
            
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <span class="w-2 h-4 rounded-full bg-[#8F0A0D]"></span>
                        Upload Berkas Sales
                    </h3>
                    <p class="text-[11px] text-amber-700 mt-0.5 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Akses terbatas: Sales PIC, Pak Susanto &amp; Pak Hariyadi
                    </p>
                </div>
                <button type="button" @click="isUploadSalesDocModalOpen = false; window.closeModal('modal-upload-sales-doc')" onclick="window.closeModal('modal-upload-sales-doc')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.documents.upload', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="stage_number" value="1">
                <input type="hidden" name="stage_name" value="Sales">
                <input type="hidden" name="document_category" value="sales">
                <input type="hidden" name="document_key" value="sales_berkas">

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">PILIH BERKAS SALES</label>
                    <input type="file" name="document_files[]" multiple required 
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 cursor-pointer bg-slate-50 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-[#8F0A0D] hover:file:bg-red-100">
                    <p class="text-[10.5px] text-slate-400 mt-1">Format: PDF, XLSX, DOCX, ZIP, PNG, JPG (Maks 50MB)</p>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1 uppercase tracking-wider text-[10.5px]">CATATAN / KETERANGAN DOKUMEN</label>
                    <input type="text" name="notes" placeholder="Contoh: BoQ Kesepakatan, Penawaran Final, PO Klien" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t">
                    <button type="button" @click="isUploadSalesDocModalOpen = false; window.closeModal('modal-upload-sales-doc')" onclick="window.closeModal('modal-upload-sales-doc')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
                        Upload Berkas Sales
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
                    <h3 id="assign-modal-title" class="text-base font-bold text-slate-900" 
                        x-text="assignRole === 'head' ? 'Assign Review ke Head Divisi (Pak Susanto)' : (assignRole === 'director' ? 'Assign Otorisasi ke Direktur (Pak Hariyadi)' : 'Assign Review ke Pimpinan')">
                        Assign Review ke Head Divisi (Pak Susanto)
                    </h3>
                    <p class="text-[11.5px] text-slate-500 mt-0.5">Tugaskan peninjauan draft proyek ke pimpinan yang berwenang</p>
                </div>
                <button type="button" @click="isAssignModalOpen = false; window.closeModal('modal-assign')" onclick="window.closeModal('modal-assign')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.assign_approver', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="role" id="assign-role-input" :value="assignRole" value="head">

                {{-- Pilihan Head Divisi --}}
                <div id="assign-head-box" x-show="assignRole === 'head' || assignRole === 'both'">
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
                <div id="assign-director-box" x-show="assignRole === 'director' || assignRole === 'both'" style="display:none;">
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
                    <button type="submit" id="assign-submit-btn" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition">
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

                <div x-data="{
                    rawVal: '{{ $project->contract_value ? (int)$project->contract_value : '' }}',
                    displayVal: '{{ $project->contract_value ? number_format((float)$project->contract_value, 0, ',', '.') : '' }}',
                    formatRupiah(val) {
                        let clean = val.replace(/\D/g, '');
                        this.rawVal = clean;
                        this.displayVal = clean ? new Intl.NumberFormat('id-ID').format(clean) : '';
                    }
                }">
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[10.5px]">ESTIMASI NILAI PROYEK (RP)</label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs pointer-events-none">Rp</span>
                        <input type="text"
                               x-model="displayVal"
                               @input="formatRupiah($event.target.value)"
                               autocomplete="off"
                               class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 font-bold"
                               placeholder="0">
                        <input type="hidden" name="contract_value" :value="rawVal">
                    </div>
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
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                <div>
                    <h3 id="assign-technical-modal-title" class="text-base font-bold text-slate-900" 
                        x-text="assignTechnicalRole === 'bdm' ? 'Penunjukan PIC Business Development' : (assignTechnicalRole === 'presales' ? 'Penugasan Pre-Sales Specialist' : 'Penugasan Solution Architect')">
                        Penunjukan Tim Solusi Teknis
                    </h3>
                    <p id="assign-technical-modal-subtitle" class="text-[11.5px] text-slate-500 mt-0.5"
                       x-text="assignTechnicalRole === 'bdm' ? 'Penetapan Product Manager & Penanggung Jawab Verifikasi Solusi Teknis' : (assignTechnicalRole === 'presales' ? 'Penetapan Personel Pre-Sales untuk Penyusunan Proposal & BoQ' : 'Penetapan Solution Architect untuk Desain Topologi & Sizing Solusi')">
                        Penugasan personel teknis untuk perancangan &amp; validasi solusi proyek.
                    </p>
                </div>
                <button type="button" @click="isAssignTechnicalModalOpen = false; window.closeModal('modal-assign-technical')" onclick="window.closeModal('modal-assign-technical')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.assign_technical', $project->id) }}" method="POST" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="role" id="assign-technical-role-input" :value="assignTechnicalRole">

                {{-- Pilihan PIC BD --}}
                <div id="box-assign-bdm" x-show="assignTechnicalRole === 'bdm'" class="space-y-1.5">
                    <label class="block text-slate-700 uppercase tracking-wider text-[10.5px]">PILIH PIC BUSINESS DEVELOPMENT (PRODUCT MANAGER &amp; VERIFIKATOR) <span class="text-[#8F0A0D]">*</span></label>
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
                    <span class="text-[10.5px] text-slate-500 leading-relaxed block">
                        PIC BD yang ditunjuk akan menerima notifikasi dan bertanggung jawab memverifikasi kelayakan solusi teknis sebelum diajukan ke tahap serah terima.
                    </span>
                </div>

                {{-- Pilihan Pre-Sales --}}
                <div id="box-assign-presales" x-show="assignTechnicalRole === 'presales'" class="space-y-1.5">
                    <label class="block text-slate-700 uppercase tracking-wider text-[10.5px]">PILIH PRE-SALES SPECIALIST (PROPOSAL &amp; BOQ) <span class="text-[#8F0A0D]">*</span></label>
                    <select name="presales_user_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white cursor-pointer">
                        <option value="">-- Pilih Pre-Sales Specialist --</option>
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
                    <span class="text-[10.5px] text-slate-500 leading-relaxed block">
                        Pre-Sales Specialist bertugas menyusun proposal teknis, bill of quantity (BoQ), dan scope of work (SOW) penawaran.
                    </span>
                </div>

                {{-- Pilihan Solution Architect --}}
                <div id="box-assign-architect" x-show="assignTechnicalRole === 'architect'" class="space-y-1.5">
                    <label class="block text-slate-700 uppercase tracking-wider text-[10.5px]">PILIH SOLUTION ARCHITECT (DESAIN TOPOLOGI &amp; SIZING) <span class="text-[#8F0A0D]">*</span></label>
                    <select name="architect_user_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500 bg-white cursor-pointer">
                        <option value="">-- Pilih Solution Architect --</option>
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
                    <span class="text-[10.5px] text-slate-500 leading-relaxed block">
                        Solution Architect bertugas merancang skema arsitektur sistem, topologi jaringan, serta validasi kompatibilitas teknis.
                    </span>
                </div>

                <div>
                    <label class="block text-slate-700 mb-1.5 uppercase tracking-wider text-[10.5px]">
                        <span x-show="assignTechnicalRole === 'bdm'">CATATAN / INSTRUKSI PENUGASAN PIC BD (OPSIONAL)</span>
                        <span x-show="assignTechnicalRole === 'presales'">INSTRUKSI &amp; CATATAN TEKNIS SALES</span>
                        <span x-show="assignTechnicalRole === 'architect'">INSTRUKSI &amp; SPESIFIKASI TEKNIS SALES</span>
                    </label>
                    <textarea name="notes" rows="3" 
                              :placeholder="assignTechnicalRole === 'bdm' ? 'Contoh: Mohon koordinasikan verifikasi kelayakan produk dan review margin penawaran sebelum diserahkan ke klien.' : (assignTechnicalRole === 'presales' ? 'Contoh: Mohon buatkan estimasi BoQ dan rincian spesifikasi perangkat sesuai kebutuhan penawaran tender klien.' : 'Contoh: Tolong buatkan desain topologi redundant switch &amp; estimasi sizing kapasitas untuk kebutuhan penawaran tender.')"
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-900 focus:ring-2 focus:ring-red-500/20 focus:border-red-500"></textarea>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="isAssignTechnicalModalOpen = false; window.closeModal('modal-assign-technical')" onclick="window.closeModal('modal-assign-technical')" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="assign-technical-submit-btn" class="px-5 py-2 rounded-xl font-bold btn-ipnet-primary cursor-pointer transition text-white"
                            x-text="assignTechnicalRole === 'bdm' ? 'Simpan Penunjukan PIC BD' : (assignTechnicalRole === 'presales' ? 'Simpan Penugasan Pre-Sales' : 'Simpan Penugasan Solution Architect')">
                        Simpan Penugasan
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
                              placeholder="Masukkan catatan / feedback verifikasi..."
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
                    <h3 id="modal-upload-technical-title" class="text-base font-bold text-slate-900" 
                        x-text="uploadTechnicalRole === 'presales' ? 'Unggah Berkas Proposal &amp; BoQ (Pre-Sales)' : 'Unggah Desain Arsitektur &amp; Topologi (Solution Architect)'">Unggah Berkas Solusi Teknis</h3>
                    <p class="text-[11.5px] text-slate-500 mt-0.5">Unggah berkas dokumen teknis pendukung solusi proyek</p>
                </div>
                <button type="button" @click="isUploadTechnicalDocModalOpen = false; window.closeModal('modal-upload-technical-doc')" onclick="window.closeModal('modal-upload-technical-doc')" class="text-slate-400 hover:text-slate-700 text-lg font-bold cursor-pointer">✕</button>
            </div>

            <form action="{{ route('projects.upload_technical_doc', $project->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 text-xs font-semibold">
                @csrf
                <input type="hidden" name="role_type" id="modal-upload-technical-role-input" :value="uploadTechnicalRole" value="presales">

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

            <form id="deleteProjForm" action="{{ route('projects.destroy', $project->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex gap-2.5">
                    <button type="button" @click="isDeleteModalOpen = false; window.closeModal('modal-delete')" onclick="window.closeModal('modal-delete')"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-white text-slate-600 border border-slate-300 font-bold text-xs hover:bg-slate-50 transition cursor-pointer text-center">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 px-4 rounded-xl btn-ipnet-primary font-bold text-xs transition cursor-pointer shadow-sm text-white text-center">
                        Ya, Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 11B. MODAL KONFIRMASI TANDAI SELESAI --}}
    <div id="modal-complete" x-show="isCompleteModalOpen" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[99999] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         @click.self="isCompleteModalOpen = false; window.closeModal('modal-complete')"
         @keydown.escape.window="isCompleteModalOpen = false; window.closeModal('modal-complete')">
        <div class="bg-white rounded-2xl w-[440px] max-w-full p-6 text-left shadow-2xl border border-slate-200">
            <div class="w-12 h-12 rounded-full bg-rose-50 text-[#8F0A0D] flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            
            <h3 class="text-center text-base font-bold text-slate-900 mb-1.5">Konfirmasi Penyelesaian Proyek</h3>
            <p class="text-center text-xs text-slate-500 mb-6 leading-relaxed">
                Apakah Anda yakin ingin menandai proyek <strong class="text-slate-800">"{{ $project->name }}"</strong> sebagai <strong class="text-slate-800">Selesai (Completed)</strong>? Pastikan seluruh ruang lingkup pekerjaan, penugasan teknisi lapangan, dan serah terima hasil pekerjaan telah rampung sepenuhnya.
            </p>

            <form action="{{ route('projects.stage_update', $project->id) }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="Completed">
                <div class="flex gap-2.5">
                    <button type="button" @click="isCompleteModalOpen = false; window.closeModal('modal-complete')" onclick="window.closeModal('modal-complete')"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-white text-slate-600 border border-slate-300 font-bold text-xs hover:bg-slate-50 transition cursor-pointer text-center">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 py-2.5 px-4 rounded-xl btn-ipnet-primary font-bold text-xs transition cursor-pointer shadow-sm text-white text-center flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span>Ya, Tandai Selesai</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 12. MODAL EDIT & HUBUNGKAN INFORMASI KLIEN --}}
    @php
        $clientsCollection = isset($allClients) ? $allClients : \App\Models\Client::orderBy('name')->get();
    @endphp
    <div id="modal-edit-client" style="display:none;"
         class="fixed inset-0 z-[99999] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-[540px] max-w-full p-6 text-left shadow-2xl border border-slate-200 anim-fade-up"
             x-data="{
                 clientsData: {{ Js::from($clientsCollection) }},
                 clientName: '{{ addslashes($clientDisplayName !== '-' ? $clientDisplayName : $project->client) }}',
                 dept: '{{ addslashes($clientDept ?: '') }}',
                 picName: '{{ addslashes($clientPicName !== '-' ? $clientPicName : '') }}',
                 email: '{{ addslashes($clientEmail !== '-' ? $clientEmail : '') }}',
                 phone: '{{ addslashes($clientPhone !== '-' ? $clientPhone : '') }}',
                 onSelectChange(nameVal) {
                     const found = this.clientsData.find(c => c.name === nameVal);
                     if (found) {
                         this.clientName = found.name;
                         this.dept = found.department || '';
                         this.picName = found.pic_name || '';
                         this.email = found.email || '';
                         this.phone = found.phone || '';
                     }
                 }
             }">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3.5 mb-4">
                <div>
                    <span class="text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider">DATABASE KLIEN CRM</span>
                    <h3 class="text-base font-bold text-slate-900">Hubungkan / Edit Informasi Klien</h3>
                </div>
                <button type="button" onclick="window.closeModal('modal-edit-client')" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('projects.update_client', $project->id) }}" method="POST" class="space-y-3.5 text-xs">
                @csrf
                
                {{-- Quick Picker from Database Klien --}}
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Pilih Cepat dari Database Klien
                    </label>
                    <select @change="onSelectChange($event.target.value)" 
                            class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] cursor-pointer">
                        <option value="">-- Pilih Rekanan / Klien Terdaftar --</option>
                        @foreach($clientsCollection as $cl)
                            <option value="{{ $cl->name }}" {{ ($clientRecord && $clientRecord->id == $cl->id) ? 'selected' : '' }}>
                                {{ $cl->name }} {{ $cl->department ? "({$cl->department})" : "" }} - PIC: {{ $cl->pic_name ?: '-' }} ({{ $cl->phone ?: '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nama Klien / Instansi Perusahaan <span class="text-[#8F0A0D]">*</span>
                    </label>
                    <input type="text" name="client" x-model="clientName" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Departemen / Divisi Klien
                        </label>
                        <input type="text" name="client_department" x-model="dept"
                               class="w-full px-3.5 py-2.5 bg-slate-50 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama PIC Klien (Customer)
                        </label>
                        <input type="text" name="customer_pic_name" x-model="picName"
                               class="w-full px-3.5 py-2.5 bg-slate-50 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Email PIC Klien
                        </label>
                        <input type="email" name="customer_pic_finance" x-model="email"
                               class="w-full px-3.5 py-2.5 bg-slate-50 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                            No. Telepon / WA PIC Klien
                        </label>
                        <input type="text" name="customer_pic_business" x-model="phone"
                               class="w-full px-3.5 py-2.5 bg-slate-50 focus:bg-white border border-slate-300 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D] transition">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 mt-4">
                    <button type="button" onclick="window.closeModal('modal-edit-client')"
                            class="px-4 py-2.5 rounded-xl bg-white text-slate-600 border border-slate-300 font-bold text-xs hover:bg-slate-50 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl btn-ipnet-primary font-bold text-xs transition cursor-pointer shadow-md text-white flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan &amp; Hubungkan Klien</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
