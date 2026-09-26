<?php
// app/Http/Controllers/ProjectController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use App\Http\Requests\ProjectRequest;
use App\Helpers\FileUploadHelper;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user         = auth()->user();
        $isSusanto    = str_contains(strtolower($user->name ?? ''), 'susanto') || $user->hasAnyRole(['Division Head', 'Head Divisi', 'Group Leader', 'HD / Direktur', 'Group Leader Delivery & Operation', 'Group Leader Commercial & Solution']);
        $isHariyadi   = str_contains(strtolower($user->name ?? ''), 'hariyadi') || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur']);
        $isExecutive  = $isSusanto || $isHariyadi || \App\Helpers\ScopeHelper::isExecutive($user) || \App\Helpers\ScopeHelper::isGroupLeader($user);

        $isLead       = \App\Helpers\ScopeHelper::isManagerial($user);
        $isDirektur   = $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur']) || $isHariyadi;
        $isSupervisor = \App\Helpers\ScopeHelper::isGroupLeader($user) || $isSusanto;
        $isSales      = $user->hasAnyRole(['Sales', 'BusDev', 'Account Manager', 'BDM']);
        $isPmo        = $user->hasAnyRole(['PMO', 'Project Manager']);

        // Pimpinan eksekutif (Direktur & Head Divisi) tidak menambah project langsung (hanya approval draft & monitor)
        $canCreate    = !$isExecutive && (\App\Helpers\ScopeHelper::canCreateProjects($user) || $isSales);
        $canManage    = !$isExecutive && (\App\Helpers\ScopeHelper::isManagerial($user) || $isSales);
        $canEditProgress = !$isExecutive && (\App\Helpers\ScopeHelper::isTeamLeader($user) || \App\Helpers\ScopeHelper::isManagerial($user));
        $scopeIds     = \App\Helpers\ScopeHelper::getScopeUserIds($user);

        $baseQuery = Project::with(['tasks.engineer:id,name', 'creator:id,name'])
            ->where(function($q) {
                $q->whereNull('project_type')->orWhere('project_type', '!=', 'Meeting / Internal');
            });

        // JIKA USER ADALAH DIREKTUR / DIVISION HEAD (HARIYADI & SUSANTO): HANYA TAMPILKAN PROYEK SALES (PUTUS DARI ENGINEER / MAINTENANCE)
        if ($isExecutive) {
            $validSalesNames = ['Raiza', 'Nabylla Berlianita', 'Nabylla', 'raiza', 'nabylla'];
            $baseQuery->whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti', 'CUTI', 'Cuti'])
                ->where('client', '!=', 'Internal / Umum')
                ->where('name', 'not like', '%Preventive Maintenance%')
                ->where('name', 'not like', '%Corrective Maintenance%')
                ->where('name', 'not like', '%SLA%')
                ->where('name', 'not like', '%Training%')
                ->where('name', 'not like', '%Meeting%')
                ->where('name', 'not like', '%On Going Project%')
                ->where('name', 'not like', '%Closed Project%')
                ->where('name', 'not like', '%Pengadaaan%')
                ->where('name', 'not like', '%Pengadaan%')
                ->where(function($q) use ($validSalesNames) {
                    $q->whereIn('sales_name', $validSalesNames)
                      ->orWhere('sales_name', 'like', '%Raiza%')
                      ->orWhere('sales_name', 'like', '%Nabylla%')
                      ->orWhereHas('creator', function($c) {
                          $c->where('name', 'like', '%Raiza%')
                            ->orWhere('name', 'like', '%Nabylla%')
                            ->orWhere('email', 'like', '%raiza%')
                            ->orWhere('email', 'like', '%nabylla%');
                      });
                })
                ->where(function ($sn) {
                    $sn->where('sales_name', 'not like', '%Sales Team%')
                       ->where('sales_name', 'not like', '%Via%')
                       ->where('sales_name', 'not like', '%Widodo%')
                       ->where('sales_name', 'not like', '%widodo%')
                       ->where('sales_name', 'not like', '%Donny%')
                       ->where('sales_name', 'not like', '%donny%')
                       ->where('sales_name', 'not like', '%Dony%')
                       ->where('sales_name', 'not like', '%dony%')
                       ->where('sales_name', 'not like', '%Erie%')
                       ->where('sales_name', 'not like', '%Hendry%')
                       ->where('sales_name', 'not like', '%Nelvia%')
                       ->where('sales_name', 'not like', '%Ribka%')
                       ->where('sales_name', 'not like', '%Sabar%')
                       ->where('sales_name', 'not like', '%Antonius%')
                       ->where('sales_name', 'not like', '%antonius%')
                       ->where('sales_name', 'not like', '%Nugraha%')
                       ->where('sales_name', 'not like', '%nugraha%');
                })
                ->whereDoesntHave('creator', function($c) {
                    $c->where('name', 'like', '%Widodo%')
                      ->orWhere('name', 'like', '%widodo%')
                      ->orWhere('name', 'like', '%Donny%')
                      ->orWhere('name', 'like', '%donny%')
                      ->orWhere('name', 'like', '%Dony%')
                      ->orWhere('name', 'like', '%dony%')
                      ->orWhere('name', 'like', '%Antonius%')
                      ->orWhere('name', 'like', '%antonius%')
                      ->orWhere('name', 'like', '%Nugraha%')
                      ->orWhere('name', 'like', '%nugraha%')
                      ->orWhereHas('roles', function($r) {
                          $r->whereIn('name', ['Engineer', 'Field Engineer', 'Lead Maintenance', 'Maintenance', 'Lead Engineer', 'Network Engineer', 'Security Engineer', 'Managed Service', 'Team Leader Engineering', 'Team Leader', 'Lead Divisi', 'BDM', 'BusDev', 'Business Development']);
                      });
                });
        } elseif (!$isSales && !$isDirektur && !$isSupervisor) {
            // Jangan tampilkan project Draft sales ke Lead Engineer / Engineer biasa
            $baseQuery->whereNotIn('status', ['Draft', 'draft'])
                      ->where('stage', '!=', 'Draft');
        }

        if ($request->filled('status')) {
            $baseQuery->where(function($q) use ($request) {
                $q->where('status', $request->status)
                  ->orWhere('stage', $request->status);
            });
        }

        if ($isExecutive || $isSales || $isPmo) {
            // Direktur, Group Leader, Sales, PMO: Memantau seluruh portofolio proyek yang relevan
            $projects = $baseQuery->latest()->get();
        } elseif ($user->hasRole('Team Leader') && $user->division_id) {
            // Team Leader: Proyek divisi, proyek yang dibuatnya, atau yang ada task anggotanya
            $teamUserIds = \App\Helpers\ScopeHelper::getScopeUserIds($user) ?? [];
            $projectIdsWithTeamTasks = \App\Models\Task::whereIn('engineer_id', $teamUserIds)->pluck('project_id')->filter()->unique();

            $projects = $baseQuery
                ->where(function($q) use ($user, $projectIdsWithTeamTasks) {
                    $q->where('division_id', $user->division_id)
                      ->orWhere('created_by', $user->id);
                    if ($projectIdsWithTeamTasks->isNotEmpty()) {
                        $q->orWhereIn('id', $projectIdsWithTeamTasks);
                    }
                })
                ->latest()
                ->get();
        } elseif ($isLead) {
            $projects = $baseQuery->latest()->get();
        } else {
            // Engineer non-lead: Hanya project yang ada task untuk dirinya
            $projectIds = \App\Models\Task::whereIn('engineer_id', $scopeIds)
                ->pluck('project_id')
                ->unique();
            $projects = $baseQuery
                ->where(function($q) use ($projectIds, $user) {
                    $q->whereIn('id', $projectIds)
                      ->orWhere('created_by', $user->id);
                })
                ->latest()
                ->get();
        }

        if ($isExecutive) {
            $isValidSalesProject = function($p) {
                $pName = strtolower($p->name ?? '');
                $sName = strtolower($p->sales_name ?? '');
                $cName = strtolower($p->creator->name ?? '');
                $blacklisted = ['widodo', 'pengadaaan', 'pengadaan', 'donny', 'dony', 'antonius', 'nugraha', 'sales team', 'via', 'erie', 'hendry', 'nelvia', 'ribka', 'sabar'];
                foreach ($blacklisted as $bl) {
                    if (str_contains($sName, $bl) || str_contains($cName, $bl) || str_contains($pName, 'pengadaaan')) {
                        return false;
                    }
                }
                return str_contains($sName, 'raiza') || str_contains($sName, 'nabylla') || str_contains($cName, 'raiza') || str_contains($cName, 'nabylla');
            };
            $projects = $projects->filter($isValidSalesProject)->values();
        }

        return view('projects.index', compact('projects', 'isLead', 'canManage', 'canCreate', 'isDirektur', 'isSupervisor', 'canEditProgress'));
    }

    public function store(ProjectRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['status'] = $data['status'] ?? 'Draft';
        if (auth()->user()->division_id) {
            $data['division_id'] = auth()->user()->division_id;
        }

        $project = Project::create($data);

        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json($project->load(['tasks:id,project_id,progress,status']), 201);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project berhasil dibuat!');
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $data = $request->validated();
        $project->update($data);

        // Jika status tidak diisi secara eksplisit oleh lead engineer, hitung ulang status berdasarkan tasks
        if (!$request->filled('status')) {
            $this->recalculateStatus($project);
        }

        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json($project->fresh()->load(['tasks:id,project_id,progress,status']));
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project berhasil diperbarui!');
    }

    public function destroy(Project $project)
    {
        abort_unless(\App\Helpers\ScopeHelper::canCreateProjects(auth()->user()), 403, 'Anda tidak memiliki hak akses untuk menghapus project.');

        try {
            // Cascade soft delete tasks & schedules
            $project->tasks()->delete();
            $project->schedules()->delete();
            $project->delete();

            if (request()->wantsJson() || request()->isJson() || request()->ajax()) {
                return response()->json(['message' => 'Project berhasil dihapus!'], 200);
            }

            return redirect()->route('projects.index')
                ->with('success', 'Project berhasil dihapus!');
        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->isJson() || request()->ajax()) {
                return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
            }
            return redirect()->route('projects.index')
                ->with('error', 'Gagal menghapus project: ' . $e->getMessage());
        }
    }

    public function show(Project $project)
    {
        $relations = ['tasks.engineer', 'creator', 'division', 'pm', 'bdm'];
        if (\Illuminate\Support\Facades\Schema::hasTable('task_user')) {
            $relations[] = 'tasks.engineers';
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('project_documents')) {
            // Bersihkan placeholder dokumen kosong tanpa file
            \App\Models\ProjectDocument::where('project_id', $project->id)
                ->where(function($q) {
                    $q->whereNull('file_path')->orWhere('file_path', '');
                })->delete();

            $relations['projectDocuments'] = function ($q) {
                $q->whereNotNull('file_path')->where('file_path', '!=', '')->with(['uploader', 'verifier']);
            };
        }
        $project->load($relations);

        $documentFlow = [];
        if (request()->wantsJson() || request()->isJson() || request()->ajax()) {
            if (\Illuminate\Support\Facades\Schema::hasTable('project_documents')) {
                $documentFlow = \App\Services\ProjectDocumentFlowService::getProjectDocumentProgress($project);
            }
            return response()->json([
                'project' => $project,
                'document_flow' => $documentFlow,
                'all_users' => $allUsers ?? User::with('roles')->orderBy('name')->get(),
            ]);
        }

        $allUsers = User::with('roles')->orderBy('name')->get();
        $allClients = \App\Models\Client::orderBy('name')->get();

        return view('projects.show', compact('project', 'documentFlow', 'allUsers', 'allClients'));
    }

    /**
     * Update & Hubungkan Informasi Klien Proyek dengan Database Klien
     */
    public function updateClientInfo(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client'                 => 'required|string|max:255',
            'client_department'      => 'nullable|string|max:255',
            'customer_pic_name'      => 'nullable|string|max:255',
            'customer_pic_business'  => 'nullable|string|max:255',
            'customer_pic_finance'   => 'nullable|string|max:255',
        ]);

        $clientRecord = \App\Models\Client::where('name', $validated['client'])
            ->orWhere('department', $validated['client'])
            ->first();

        if ($clientRecord) {
            $clientRecord->update([
                'department' => $validated['client_department'] ?: $clientRecord->department,
                'pic_name'   => $validated['customer_pic_name'] ?: $clientRecord->pic_name,
                'phone'      => $validated['customer_pic_business'] ?: $clientRecord->phone,
                'email'      => $validated['customer_pic_finance'] ?: $clientRecord->email,
            ]);
            $clientName = $clientRecord->name;
        } else {
            $newClient = \App\Models\Client::create([
                'name'        => $validated['client'],
                'department'  => $validated['client_department'] ?? null,
                'pic_name'    => $validated['customer_pic_name'] ?? null,
                'phone'       => $validated['customer_pic_business'] ?? null,
                'email'       => $validated['customer_pic_finance'] ?? null,
                'created_by'  => auth()->id(),
            ]);
            $clientName = $newClient->name;
        }

        $project->update([
            'client'                 => $clientName,
            'customer_pic_name'      => $validated['customer_pic_name'] ?? ($clientRecord->pic_name ?? null),
            'customer_pic_business'  => $validated['customer_pic_business'] ?? ($clientRecord->phone ?? null),
            'customer_pic_finance'   => $validated['customer_pic_finance'] ?? ($clientRecord->email ?? null),
            'customer_pic_technical' => $validated['customer_pic_finance'] ?? ($clientRecord->email ?? null),
        ]);

        return redirect()->back()->with('success', 'Informasi klien proyek berhasil diperbarui dan disinkronkan ke Database Klien!');
    }

    /**
     * Penugasan Tim (Project Manager atau Engineer / Serah Terima PMO atau Managed Service)
     */
    public function assignTeam(Request $request, Project $project)
    {
        $validated = $request->validate([
            'role_type'             => 'required|in:pm,engineer',
            'user_id'               => 'required|exists:users,id',
            'task_title'            => 'nullable|string|max:255',
            'deadline'              => 'nullable|date',
            'handover_target'       => 'nullable|string|in:pmo,managed_service',
            'po_spk_number'         => 'nullable|string|max:255',
            'po_spk_date'           => 'nullable|date',
            'contract_value'        => 'nullable|numeric|min:0',
            'po_spk_file'           => FileUploadHelper::fileValidationRule(25600),
            'billing_terms'         => 'nullable|string',
            'sla_commitment'        => 'nullable|string',
            'commercial_terms'      => 'nullable|string',
            'special_commitment'    => 'nullable|string',
            'exclusions'            => 'nullable|string',
            'sla_tier'              => 'nullable|string|max:50',
            'sla_coverage_hours'    => 'nullable|string|max:100',
            'maintenance_frequency' => 'nullable|string|max:100',
            'service_start_date'    => 'nullable|date',
            'service_end_date'      => 'nullable|date',
        ]);

        $user = User::findOrFail($validated['user_id']);

        if ($validated['role_type'] === 'pm') {
            $target = $validated['handover_target'] ?? ($project->handover_target ?: 'pmo');
            $updateFields = [
                'pm_id'           => $user->id,
                'handover_target' => $target,
            ];

            if ($request->filled('po_spk_number')) {
                $updateFields['po_spk_number'] = $request->input('po_spk_number');
            }
            if ($request->filled('po_spk_date')) {
                $updateFields['po_spk_date'] = $request->input('po_spk_date');
            }
            if ($request->filled('contract_value')) {
                $updateFields['contract_value'] = $request->input('contract_value');
            }
            if ($request->filled('billing_terms')) {
                $updateFields['billing_terms'] = $request->input('billing_terms');
            }
            if ($request->filled('commercial_terms')) {
                $updateFields['commercial_terms'] = $request->input('commercial_terms');
            }
            if ($request->filled('sla_commitment')) {
                $updateFields['sla_commitment'] = $request->input('sla_commitment');
            }
            if ($request->filled('special_commitment')) {
                $updateFields['special_commitment'] = $request->input('special_commitment');
            }
            if ($request->filled('exclusions')) {
                $updateFields['exclusions'] = $request->input('exclusions');
            }

            if ($request->hasFile('po_spk_file')) {
                $uploadedPo = $request->file('po_spk_file');
                $poFileName = $uploadedPo->getClientOriginalName();
                $poFileSize = $uploadedPo->getSize();
                $poFileMime = $uploadedPo->getClientMimeType();
                $poFilePath = FileUploadHelper::storePublicly($uploadedPo, 'commercial_contracts');
                $updateFields['po_spk_file'] = $poFilePath;

                if (\Illuminate\Support\Facades\Schema::hasTable('project_documents')) {
                    \App\Models\ProjectDocument::updateOrCreate(
                        [
                            'project_id'   => $project->id,
                            'stage_number' => 1,
                            'document_key' => 'contract_po_so',
                        ],
                        [
                            'stage_name'     => 'Commercial',
                            'document_title' => 'Contract / PO / SO (Signed)',
                            'file_path'      => $poFilePath,
                            'file_name'      => $poFileName,
                            'file_size'      => $poFileSize ?: 0,
                            'file_mime'      => $poFileMime ?: 'application/octet-stream',
                            'status'         => 'Uploaded',
                            'uploaded_by'    => auth()->id(),
                            'version'        => 1,
                            'is_mandatory'   => true,
                            'notes'          => 'PO/SPK No: ' . ($request->input('po_spk_number') ?: $project->po_spk_number),
                        ]
                    );
                }
            }

            if ($target === 'managed_service') {
                $updateFields['stage'] = 'Operate';
                if ($request->filled('sla_tier')) {
                    $updateFields['sla_tier'] = $request->input('sla_tier');
                }
                if ($request->filled('sla_coverage_hours')) {
                    $updateFields['sla_coverage_hours'] = $request->input('sla_coverage_hours');
                }
                if ($request->filled('maintenance_frequency')) {
                    $updateFields['maintenance_frequency'] = $request->input('maintenance_frequency');
                }
                if ($request->filled('service_start_date')) {
                    $updateFields['service_start_date'] = $request->input('service_start_date');
                }
                if ($request->filled('service_end_date')) {
                    $updateFields['service_end_date'] = $request->input('service_end_date');
                }
            } else {
                $updateFields['stage'] = 'Deliver';
            }

            if (in_array($project->status, ['Draft', 'Planning', 'Opportunity'])) {
                $updateFields['status'] = 'In Progress';
            }

            $updateFields['commercial_handover_status'] = 'Approved';
            $updateFields['commercial_handover_at'] = now();
            $updateFields['commercial_handover_by'] = auth()->id();

            $project->update($updateFields);

            // Send notification
            if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'title'   => $target === 'managed_service' ? "Penugasan Lead Managed Service: {$project->name}" : "Penugasan Project Manager: {$project->name}",
                    'message' => "Anda telah ditugaskan untuk memimpin serah terima proyek '{$project->name}' (Klien: " . ($project->client ?: '-') . ").",
                    'url'     => route('projects.show', $project->id),
                    'is_read' => false,
                ]);
            }

            $msg = $target === 'managed_service' 
                ? "Serah terima berhasil & User '{$user->name}' ditugaskan sebagai Lead Managed Service!" 
                : "Serah terima berhasil & User '{$user->name}' ditugaskan sebagai Project Manager (PMO)!";
        } else {
            $taskTitle = $validated['task_title'] ?: ('Implementasi Teknis: ' . $project->name);
            $task = Task::create([
                'project_id'  => $project->id,
                'engineer_id' => $user->id,
                'title'       => $taskTitle,
                'status'      => 'Pending',
                'priority'    => 'Medium',
                'deadline'    => $validated['deadline'] ?: ($project->deadline ?: now()->addDays(7)),
                'created_by'  => auth()->id(),
            ]);

            if (\Illuminate\Support\Facades\Schema::hasTable('task_user')) {
                $task->engineers()->sync([$user->id]);
            }

            if (in_array($project->status, ['Draft', 'Planning', 'Opportunity'])) {
                $project->update(['status' => 'In Progress', 'stage' => 'Deliver']);
            }

            $msg = "Engineer '{$user->name}' berhasil ditugaskan pada proyek!";
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg, 'project' => $project->fresh(['pm', 'tasks.engineer'])]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Penugasan Review Draft ke Pimpinan (Head Divisi / Direktur) oleh Sales
     */
    public function assignApprover(Request $request, Project $project)
    {
        $validated = $request->validate([
            'role'             => 'required|in:head,director,both',
            'head_user_id'     => 'nullable|exists:users,id',
            'director_user_id' => 'nullable|exists:users,id',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $approvals = $handoverData['draft_approvals'] ?? [
            'head'     => ['assigned' => false, 'approved' => false, 'by' => null, 'date' => null, 'notes' => null],
            'director' => ['assigned' => false, 'approved' => false, 'by' => null, 'date' => null, 'notes' => null],
        ];

        $now = now()->format('d M Y H:i');
        $creatorName = auth()->user() ? auth()->user()->name : ($project->sales_name ?: 'Sales');

        if (in_array($validated['role'], ['head', 'both'])) {
            $headUser = !empty($validated['head_user_id']) 
                ? \App\Models\User::find($validated['head_user_id']) 
                : \App\Models\User::where('name', 'like', '%Susanto%')->first();
            $headName = $headUser ? $headUser->name : 'Pak Susanto Djaya';

            $approvals['head'] = array_merge($approvals['head'] ?? [], [
                'assigned'         => true,
                'assigned_to'      => $headName,
                'assigned_user_id' => $headUser ? $headUser->id : null,
                'assigned_by'      => $creatorName,
                'assigned_at'      => $now,
                'sales_notes'      => $validated['notes'] ?? null,
            ]);

            // Kirim notifikasi sistem ke Pak Susanto
            if ($headUser && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                \App\Models\Notification::create([
                    'user_id' => $headUser->id,
                    'title'   => 'Permohonan Review Draft: ' . $project->name,
                    'message' => "Sales ({$creatorName}) menugaskan Anda untuk meninjau kelayakan teknis draft proyek '{$project->name}' (Klien: " . ($project->client ?: '-') . ")." . (!empty($validated['notes']) ? " Catatan: {$validated['notes']}" : ''),
                    'url'     => route('projects.show', $project->id),
                    'is_read' => false,
                ]);
            }
        }

        if (in_array($validated['role'], ['director', 'both'])) {
            $directorUser = !empty($validated['director_user_id']) 
                ? \App\Models\User::find($validated['director_user_id']) 
                : \App\Models\User::where('name', 'like', '%Hariyadi%')->first();
            $directorName = $directorUser ? $directorUser->name : 'Pak Hariyadi';

            $approvals['director'] = array_merge($approvals['director'] ?? [], [
                'assigned'         => true,
                'assigned_to'      => $directorName,
                'assigned_user_id' => $directorUser ? $directorUser->id : null,
                'assigned_by'      => $creatorName,
                'assigned_at'      => $now,
                'sales_notes'      => $validated['notes'] ?? null,
            ]);

            // Kirim notifikasi sistem ke Pak Hariyadi
            if ($directorUser && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                \App\Models\Notification::create([
                    'user_id' => $directorUser->id,
                    'title'   => 'Permohonan Otorisasi Draft: ' . $project->name,
                    'message' => "Sales ({$creatorName}) menugaskan Anda untuk mengotorisasi draft proyek '{$project->name}' (Klien: " . ($project->client ?: '-') . ")." . (!empty($validated['notes']) ? " Catatan: {$validated['notes']}" : ''),
                    'url'     => route('projects.show', $project->id),
                    'is_read' => false,
                ]);
            }
        }

        $handoverData['draft_approvals'] = $approvals;
        $project->handover_data = $handoverData;
        $project->save();

        $msg = "Review draft berhasil ditugaskan ke pimpinan dan notifikasi telah dikirimkan!";
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg, 'approvals' => $approvals]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Persetujuan Draft Berjenjang (Pak Susanto - Head & Pak Hariyadi - Direktur)
     */
    public function approveDraft(Request $request, Project $project)
    {
        $validated = $request->validate([
            'approval_role' => 'required|in:head,director',
            'notes'         => 'nullable|string|max:1000',
            'auto_advance'  => 'nullable|boolean',
        ]);

        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $approvals = $handoverData['draft_approvals'] ?? [
            'head' => ['approved' => false, 'by' => null, 'date' => null, 'notes' => null],
            'director' => ['approved' => false, 'by' => null, 'date' => null, 'notes' => null],
        ];

        $now = now()->format('d M Y H:i');
        $approverUser = auth()->user();
        $approverName = $approverUser ? $approverUser->name : 'Pimpinan';

        if ($validated['approval_role'] === 'head') {
            $approvals['head'] = array_merge($approvals['head'] ?? [], [
                'assigned' => true,
                'approved' => true,
                'by'       => $approverName . ' (Head Divisi)',
                'date'     => $now,
                'notes'    => $validated['notes'] ?: 'Kelayakan teknis & alokasi resource disetujui.',
            ]);
            $msg = "Persetujuan Head Divisi ({$approverName}) berhasil dicatat!";

            // Kirim notifikasi ke pembuat proyek (Sales)
            $targetUserId = $project->creator_id ?? $project->created_by;
            if ($targetUserId && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                \App\Models\Notification::create([
                    'user_id' => $targetUserId,
                    'title'   => 'Draft Proyek Disetujui Head: ' . $project->name,
                    'message' => "Head Divisi ({$approverName}) telah menyetujui kelayakan teknis draft proyek '{$project->name}'.",
                    'url'     => route('projects.show', $project->id),
                    'is_read' => false,
                ]);
            }
        } else {
            $approvals['director'] = array_merge($approvals['director'] ?? [], [
                'assigned' => true,
                'approved' => true,
                'by'       => $approverName . ' (Direktur)',
                'date'     => $now,
                'notes'    => $validated['notes'] ?: 'Otorisasi anggaran dan persetujuan eksekusi kontrak disahkan.',
            ]);
            $msg = "Otorisasi Direktur ({$approverName}) berhasil dicatat!";

            // Kirim notifikasi ke pembuat proyek (Sales)
            $targetUserId = $project->creator_id ?? $project->created_by;
            if ($targetUserId && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                \App\Models\Notification::create([
                    'user_id' => $targetUserId,
                    'title'   => 'Draft Proyek Diotorisasi Direktur: ' . $project->name,
                    'message' => "Direktur ({$approverName}) telah mengotorisasi draft proyek '{$project->name}'.",
                    'url'     => route('projects.show', $project->id),
                    'is_read' => false,
                ]);
            }
        }

        $handoverData['draft_approvals'] = $approvals;
        $project->handover_data = $handoverData;

        if (!empty($approvals['head']['approved']) && !empty($approvals['director']['approved'])) {
            if ($project->status === 'Draft') {
                $project->status = 'Opportunity';
            }

            // Notifikasi ke seluruh PMO bahwa dual sign-off telah selesai
            if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                $pmoUsers = \App\Models\User::whereHas('roles', function($q) {
                    $q->whereIn('name', ['PMO', 'Project Manager', 'Lead Divisi', 'Group Leader']);
                })->orWhere('name', 'like', '%Rizki%')->get();

                foreach ($pmoUsers as $pmo) {
                    \App\Models\Notification::create([
                        'user_id' => $pmo->id,
                        'title'   => 'Draft Proyek Siap Handover: ' . $project->name,
                        'message' => "Proyek '{$project->name}' telah selesai ditinjau & diotorisasi (Dual Sign-Off lengkap) dan siap diproses lebih lanjut.",
                        'url'     => route('projects.show', $project->id),
                        'is_read' => false,
                    ]);
                }
            }
        }

        $project->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg, 'approvals' => $approvals]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Penugasan Tim Solusi Teknis (Business Development, Presales Specialist & Solution Architect)
     */
    public function assignTechnical(Request $request, Project $project)
    {
        $validated = $request->validate([
            'role'              => 'required|in:presales,architect,bdm,both,all',
            'bdm_user_id'       => 'nullable|exists:users,id',
            'presales_user_id'  => 'nullable|exists:users,id',
            'architect_user_id' => 'nullable|exists:users,id',
            'notes'             => 'nullable|string|max:1000',
        ]);

        $handoverData = is_array($project->handover_data) ? $project->handover_data : (json_decode($project->handover_data ?? '', true) ?: []);
        $technical = $handoverData['technical_assignments'] ?? [
            'bdm'             => ['assigned' => false, 'assigned_to' => null, 'assigned_user_id' => null, 'status' => 'Pending'],
            'presales'        => ['assigned' => false, 'assigned_to' => null, 'assigned_user_id' => null, 'sales_notes' => null, 'status' => 'Pending'],
            'architect'       => ['assigned' => false, 'assigned_to' => null, 'assigned_user_id' => null, 'sales_notes' => null, 'status' => 'Pending'],
            'bd_verification' => ['status' => 'Pending Assignment', 'verified_by' => null, 'verified_at' => null, 'notes' => null],
        ];

        $now = now()->format('d M Y H:i');
        $creatorName = auth()->user() ? auth()->user()->name : ($project->sales_name ?: 'Sales');

        // 1. Assign PIC BD (Product Manager / Verifikator)
        if (in_array($validated['role'], ['bdm', 'all']) || !empty($validated['bdm_user_id'])) {
            $bdmUser = !empty($validated['bdm_user_id'])
                ? \App\Models\User::find($validated['bdm_user_id'])
                : ($project->bdm ?: \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['BDM', 'BusDev', 'Business Development']))->first());
            
            if ($bdmUser) {
                $project->bdm_id = $bdmUser->id;
                $technical['bdm'] = array_merge($technical['bdm'] ?? [], [
                    'assigned'         => true,
                    'assigned_to'      => $bdmUser->name,
                    'assigned_user_id' => $bdmUser->id,
                    'assigned_by'      => $creatorName,
                    'assigned_at'      => $now,
                    'status'           => 'Assigned',
                ]);

                // Kirim notifikasi sistem ke BD
                if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                    $notifMsg = "Sales ({$creatorName}) menunjuk Anda sebagai PIC BD (Product Manager & Verifikator) untuk proyek '{$project->name}' (Klien: " . ($project->client ?: '-') . ").";
                    \App\Models\Notification::create([
                        'user_id' => $bdmUser->id,
                        'title'   => \Illuminate\Support\Str::limit('Penunjukan PIC BD: ' . $project->name, 240),
                        'message' => \Illuminate\Support\Str::limit($notifMsg, 240),
                        'url'     => route('projects.show', $project->id),
                        'is_read' => false,
                    ]);
                }
            }
        }

        // 2. Assign Pre-Sales Specialist
        if (in_array($validated['role'], ['presales', 'both', 'all'])) {
            $presalesUser = !empty($validated['presales_user_id'])
                ? \App\Models\User::find($validated['presales_user_id'])
                : \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['Presales', 'Pre-Sales', 'Sales', 'BDM', 'BusDev']))->first();
            $presalesName = $presalesUser ? $presalesUser->name : 'Pre-Sales Specialist';

            $technical['presales'] = array_merge($technical['presales'] ?? [], [
                'assigned'         => true,
                'assigned_to'      => $presalesName,
                'assigned_user_id' => $presalesUser ? $presalesUser->id : null,
                'assigned_by'      => $creatorName,
                'assigned_at'      => $now,
                'sales_notes'      => $validated['notes'] ?? ($technical['presales']['sales_notes'] ?? null),
                'status'           => ($technical['presales']['status'] ?? '') === 'Completed' ? 'Completed' : 'Assigned',
            ]);

            // Kirim notifikasi sistem ke Presales
            if ($presalesUser && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                $notifMsg = "Sales ({$creatorName}) menugaskan Anda untuk menyusun Proposal Teknis & BoQ proyek '{$project->name}' (Klien: " . ($project->client ?: '-') . ")." . (!empty($validated['notes']) ? " Catatan: {$validated['notes']}" : '');
                \App\Models\Notification::create([
                    'user_id' => $presalesUser->id,
                    'title'   => \Illuminate\Support\Str::limit('Penugasan Proposal Teknis & BoQ: ' . $project->name, 240),
                    'message' => \Illuminate\Support\Str::limit($notifMsg, 240),
                    'url'     => route('projects.show', $project->id),
                    'is_read' => false,
                ]);
            }
        }

        // 3. Assign Solution Architect
        if (in_array($validated['role'], ['architect', 'both', 'all'])) {
            $architectUser = !empty($validated['architect_user_id'])
                ? \App\Models\User::find($validated['architect_user_id'])
                : \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['Solution Architect', 'Solutions Architect', 'SA']))->first()
                   ?? \App\Models\User::where('name', 'like', '%Aris%')->first();
            $architectName = $architectUser ? $architectUser->name : 'Aris Sadewo (Solution Architect)';

            $technical['architect'] = array_merge($technical['architect'] ?? [], [
                'assigned'         => true,
                'assigned_to'      => $architectName,
                'assigned_user_id' => $architectUser ? $architectUser->id : null,
                'assigned_by'      => $creatorName,
                'assigned_at'      => $now,
                'sales_notes'      => $validated['notes'] ?? ($technical['architect']['sales_notes'] ?? null),
                'status'           => ($technical['architect']['status'] ?? '') === 'Completed' ? 'Completed' : 'Assigned',
            ]);

            // Kirim notifikasi sistem ke Solution Architect
            if ($architectUser && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                $notifMsg = "Sales ({$creatorName}) menugaskan Anda merancang Diagram Topologi & arsitektur proyek '{$project->name}' (Klien: " . ($project->client ?: '-') . ")." . (!empty($validated['notes']) ? " Catatan: {$validated['notes']}" : '');
                \App\Models\Notification::create([
                    'user_id' => $architectUser->id,
                    'title'   => \Illuminate\Support\Str::limit('Penugasan Desain Topologi & Sizing: ' . $project->name, 240),
                    'message' => \Illuminate\Support\Str::limit($notifMsg, 240),
                    'url'     => route('projects.show', $project->id),
                    'is_read' => false,
                ]);
            }
        }

        // Update default verification state if assigned
        if (!isset($technical['bd_verification']['status']) || $technical['bd_verification']['status'] === 'Pending Assignment') {
            $technical['bd_verification'] = [
                'status'       => 'Waiting Uploads',
                'verified_by'  => null,
                'verified_at'  => null,
                'notes'        => null,
            ];
        }

        $handoverData['technical_assignments'] = $technical;
        $project->handover_data = $handoverData;
        $project->save();

        $msg = "Penugasan tim kolaborasi solusi (PIC BD, Presales & Solution Architect) berhasil diperbarui!";
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg, 'technical' => $technical, 'project' => $project->fresh(['bdm'])]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Upload Dokumen Hasil Kerja Teknis oleh Presales Specialist atau Solution Architect
     */
    public function uploadTechnicalDoc(Request $request, Project $project)
    {
        $allowedExts = \App\Helpers\FileUploadHelper::allowedDocumentExtensions();

        $validated = $request->validate([
            'role_type'        => 'required|in:presales,architect',
            'document_title'   => 'nullable|string|max:255',
            'document_file'    => [
                'nullable',
                'file',
                'max:51200',
                function ($attribute, $value, $fail) use ($allowedExts) {
                    if ($value && $value->isValid()) {
                        $ext = strtolower($value->getClientOriginalExtension());
                        if (!$ext || !in_array($ext, $allowedExts)) {
                            $fail('Format file tidak didukung. Format yang diizinkan: PDF, Word (DOC/DOCX), Excel (XLS/XLSX), PPT, Visio (VSDX), Gambar, dan Arsip ZIP/RAR.');
                        }
                    }
                }
            ],
            'document_files'   => 'nullable|array',
            'document_files.*' => [
                'file',
                'max:51200',
                function ($attribute, $value, $fail) use ($allowedExts) {
                    if ($value && $value->isValid()) {
                        $ext = strtolower($value->getClientOriginalExtension());
                        if (!$ext || !in_array($ext, $allowedExts)) {
                            $fail('Format file tidak didukung. Format yang diizinkan: PDF, Word (DOC/DOCX), Excel (XLS/XLSX), PPT, Visio (VSDX), Gambar, dan Arsip ZIP/RAR.');
                        }
                    }
                }
            ],
            'notes'            => 'nullable|string|max:1000',
        ]);

        $files = [];
        if ($request->hasFile('document_files')) {
            $files = $request->file('document_files');
        } elseif ($request->hasFile('document_file')) {
            $files = [$request->file('document_file')];
        }

        if (empty($files)) {
            return back()->with('error', 'Silakan pilih berkas dokumen yang akan diunggah.');
        }

        $user = auth()->user();
        $uploaderName = $user ? $user->name : 'Tim Teknis Solusi';
        $now = now()->format('d M Y H:i');

        $handoverData = is_array($project->handover_data) ? $project->handover_data : (json_decode($project->handover_data ?? '', true) ?: []);
        $technical = $handoverData['technical_assignments'] ?? [];

        $roleKey = $validated['role_type'];
        $defaultDocKey = ($roleKey === 'presales') ? 'technical_proposal' : 'solution_architecture';
        $defaultDocTitle = ($roleKey === 'presales') ? 'Proposal Teknis & BoQ' : 'Desain Arsitektur & Topologi';
        $docTitle = $validated['document_title'] ?: $defaultDocTitle;

        $lastStoredPath = null;
        $lastStoredName = null;

        foreach ($files as $file) {
            $origName = $file->getClientOriginalName();
            $ext      = $file->getClientOriginalExtension();
            $size     = $file->getSize();
            $path     = \App\Helpers\FileUploadHelper::storePublicly($file, 'project_documents/' . $project->id);

            $lastStoredPath = $path;
            $lastStoredName = $origName;

            // Catat di project_documents (Stage 2: Solution / Presales)
            if (\Illuminate\Support\Facades\Schema::hasTable('project_documents')) {
                $docPayload = [
                    'project_id'     => $project->id,
                    'stage_number'   => 2,
                    'stage_name'     => 'Solution',
                    'document_key'   => $defaultDocKey,
                    'document_title' => $docTitle,
                    'file_name'      => $origName,
                    'file_path'      => $path,
                    'file_size'      => $size,
                    'file_extension' => $ext,
                    'status'         => 'Uploaded',
                    'notes'          => $validated['notes'] ?? null,
                    'uploaded_by'    => $user?->id,
                    'uploaded_at'    => now(),
                ];
                if (\Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'name')) {
                    $docPayload['name'] = $origName;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'document_type')) {
                    $docPayload['document_type'] = ($roleKey === 'presales' ? 'Technical Proposal' : 'Topology & Sizing');
                }
                \App\Models\ProjectDocument::create($docPayload);
            }
        }

        // Update technical assignment status
        $technical[$roleKey] = array_merge($technical[$roleKey] ?? [], [
            'status'         => 'Completed',
            'document_path'  => $lastStoredPath,
            'document_name'  => $lastStoredName,
            'document_title' => $docTitle,
            'completed_at'   => $now,
            'notes'          => $validated['notes'] ?? ($technical[$roleKey]['notes'] ?? null),
        ]);

        // Status Verifikasi BD menjadi 'Menunggu Verifikasi BD' (Pending Verification)
        $technical['bd_verification'] = [
            'status'         => 'Pending Verification',
            'submitted_at'   => $now,
            'submitted_by'   => $uploaderName,
            'verified_by'    => null,
            'verified_at'    => null,
            'notes'          => null, // Reset previous revision catatan
        ];

        $handoverData['technical_assignments'] = $technical;
        $project->handover_data = $handoverData;

        // Jika yang diunggah presales, sinkronkan juga kolom proposal_file di projects agar sinkron di dashboard presales
        if ($roleKey === 'presales') {
            $project->proposal_file = $lastStoredPath;
            if (!empty($validated['notes'])) {
                $project->proposal_notes = $validated['notes'];
            }
            $project->presales_status = 'Submitted';
        }

        $project->save();

        $roleLabel = ($roleKey === 'presales') ? 'Pre-Sales Specialist' : 'Solution Architect';

        // 1. Kirim notifikasi ke PIC BD untuk segera melakukan verifikasi
        $bdUserId = $project->bdm_id;
        if (!$bdUserId) {
            $bdUser = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['BDM', 'BusDev', 'Business Development']))->first();
            $bdUserId = $bdUser?->id;
        }

        if ($bdUserId && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
            \App\Models\Notification::create([
                'user_id' => $bdUserId,
                'title'   => \Illuminate\Support\Str::limit("Verifikasi Berkas {$roleLabel}: " . $project->name, 240),
                'message' => \Illuminate\Support\Str::limit("{$uploaderName} ({$roleLabel}) telah mengunggah '{$docTitle}' untuk proyek '{$project->name}'. Silakan verifikasi kelayakan dokumen.", 240),
                'url'     => route('projects.show', $project->id),
                'is_read' => false,
            ]);
        }

        // 2. Kirim notifikasi tembusan ke Sales pembuat proyek
        $salesUserId = $project->creator_id ?? $project->created_by;
        if ($salesUserId && $salesUserId != $bdUserId && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
            \App\Models\Notification::create([
                'user_id' => $salesUserId,
                'title'   => \Illuminate\Support\Str::limit("Berkas {$roleLabel} Diunggah: " . $project->name, 240),
                'message' => \Illuminate\Support\Str::limit("{$uploaderName} ({$roleLabel}) telah mengunggah berkas '{$docTitle}' untuk proyek '{$project->name}'. Berkas sedang diverifikasi oleh PIC BD.", 240),
                'url'     => route('projects.show', $project->id),
                'is_read' => false,
            ]);
        }

        $msg = "Berkas dokumen ({$docTitle}) berhasil diunggah dan dikirimkan ke PIC BD untuk verifikasi!";
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg, 'technical' => $technical]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Verifikasi Dokumen Solusi oleh PIC Business Development (BD)
     */
    public function verifyTechnicalSolution(Request $request, Project $project)
    {
        $validated = $request->validate([
            'decision' => 'required|in:approved,revision',
            'notes'    => 'nullable|string|max:1000',
        ]);

        $user = auth()->user();
        $verifierName = $user ? $user->name : 'PIC Business Development';
        $now = now()->format('d M Y H:i');

        $handoverData = is_array($project->handover_data) ? $project->handover_data : (json_decode($project->handover_data ?? '', true) ?: []);
        $technical = $handoverData['technical_assignments'] ?? [];

        $isApproved = ($validated['decision'] === 'approved');

        $technical['bd_verification'] = [
            'status'      => $isApproved ? 'Approved' : 'Revision Needed',
            'verified_by' => $verifierName,
            'verified_at' => $now,
            'notes'       => $validated['notes'] ?? null,
        ];

        // Jika disetujui BD, sinkronkan status Presales & Solution Architect menjadi selesai / disetujui
        if ($isApproved) {
            $sharedDocPath = $technical['presales']['document_path'] ?? ($technical['architect']['document_path'] ?? $project->proposal_file);
            $sharedDocName = $technical['presales']['document_name'] ?? ($technical['architect']['document_name'] ?? 'Proposal & Desain Teknis');

            if (!empty($technical['presales']['assigned']) && (empty($technical['presales']['document_path']) || ($technical['presales']['status'] ?? '') !== 'Completed')) {
                $technical['presales'] = array_merge($technical['presales'], [
                    'status'         => 'Completed',
                    'document_path'  => $technical['presales']['document_path'] ?? $sharedDocPath,
                    'document_name'  => $technical['presales']['document_name'] ?? $sharedDocName,
                    'document_title' => $technical['presales']['document_title'] ?? 'Proposal Teknis & Ruang Lingkup (SOW)',
                    'completed_at'   => $technical['presales']['completed_at'] ?? $now,
                    'notes'          => $technical['presales']['notes'] ?? 'Proposal disetujui oleh PIC BD.',
                ]);
            }

            if (!empty($technical['architect']['assigned']) && (empty($technical['architect']['document_path']) || ($technical['architect']['status'] ?? '') !== 'Completed')) {
                $technical['architect'] = array_merge($technical['architect'], [
                    'status'         => 'Completed',
                    'document_path'  => $technical['architect']['document_path'] ?? $sharedDocPath,
                    'document_name'  => $technical['architect']['document_name'] ?? $sharedDocName,
                    'document_title' => $technical['architect']['document_title'] ?? 'Desain Arsitektur & SOW (Terverifikasi BD)',
                    'completed_at'   => $technical['architect']['completed_at'] ?? $now,
                    'notes'          => $technical['architect']['notes'] ?? 'Desain arsitektur disetujui bersama paket proposal oleh PIC BD.',
                ]);
            }
        }

        // Jika disetujui, update sales_stage ke Proposal Submission jika masih Qualification
        if ($isApproved && in_array($project->sales_stage, ['Qualification', 'Discovery', null])) {
            $project->sales_stage = 'Proposal / Quoting';
            if ($project->win_probability < 30) {
                $project->win_probability = 50;
            }
        }

        $handoverData['technical_assignments'] = $technical;
        $project->handover_data = $handoverData;
        $project->save();

        // 1. Notifikasi ke Sales
        $salesUserId = $project->creator_id ?? $project->created_by;
        if ($salesUserId && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
            $salesTitle = $isApproved 
                ? "Proposal Teknis Disetujui BD: {$project->name}" 
                : "Dokumen Solusi Perlu Revisi: {$project->name}";
            $salesMsg = $isApproved
                ? "PIC BD ({$verifierName}) telah MENYETUJUI dokumen solusi teknis untuk proyek '{$project->name}'. Proposal resmi siap diajukan ke klien." . (!empty($validated['notes']) ? " Catatan BD: {$validated['notes']}" : '')
                : "PIC BD ({$verifierName}) meminta REVISI berkas solusi proyek '{$project->name}'. Catatan: " . ($validated['notes'] ?? 'Mohon perbaiki proposal/desain.');
            
            \App\Models\Notification::create([
                'user_id' => $salesUserId,
                'title'   => \Illuminate\Support\Str::limit($salesTitle, 240),
                'message' => \Illuminate\Support\Str::limit($salesMsg, 240),
                'url'     => route('projects.show', $project->id),
                'is_read' => false,
            ]);
        }

        // 2. Notifikasi ke Presales & SA jika minta revisi
        $techUserIds = array_filter([
            $technical['presales']['assigned_user_id'] ?? null,
            $technical['architect']['assigned_user_id'] ?? null,
        ]);

        foreach (array_unique($techUserIds) as $tUserId) {
            if ($tUserId && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                $tTitle = $isApproved 
                    ? "Berkas Solusi Disetujui BD: {$project->name}" 
                    : "Permintaan Revisi dari BD: {$project->name}";
                $tMsg = $isApproved
                    ? "Kerja bagus! PIC BD ({$verifierName}) telah menyetujui proposal & desain solusi proyek '{$project->name}'."
                    : "PIC BD ({$verifierName}) meminta revisi dokumen solusi proyek '{$project->name}'. Catatan: " . ($validated['notes'] ?? 'Silakan lakukan revisi berkas.');

                \App\Models\Notification::create([
                    'user_id' => $tUserId,
                    'title'   => \Illuminate\Support\Str::limit($tTitle, 240),
                    'message' => \Illuminate\Support\Str::limit($tMsg, 240),
                    'url'     => route('projects.show', $project->id),
                    'is_read' => false,
                ]);
            }
        }

        $msg = $isApproved 
            ? "Verifikasi berhasil! Dokumen solusi disetujui dan diteruskan ke Sales untuk proses penawaran ke klien." 
            : "Permintaan revisi berhasil dikirimkan ke tim Presales & Solution Architect beserta catatan revisi.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg, 'technical' => $technical, 'project' => $project->fresh(['bdm'])]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Update Pipeline Sales & Opportunity (Stage, Probability, Target Closing)
     */
    public function updatePipeline(Request $request, Project $project)
    {
        $validated = $request->validate([
            'sales_stage'          => 'required|string|max:100',
            'win_probability'      => 'required|numeric|min:0|max:100',
            'expected_closing_date'=> 'nullable|date',
            'contract_value'       => 'nullable|numeric|min:0',
        ]);

        $project->sales_stage = $validated['sales_stage'];
        $project->win_probability = $validated['win_probability'];
        $project->expected_closing_date = $validated['expected_closing_date'] ?? $project->expected_closing_date;
        if ($request->filled('contract_value')) {
            $project->contract_value = $validated['contract_value'];
        }

        $stageLower = strtolower($validated['sales_stage']);

        // Sinkronisasi otomatis jika stage Closed Won (Menang / Deal) atau probabilitas 100%
        if (str_contains($stageLower, 'won') || $validated['sales_stage'] === 'Closed Won' || (int)$validated['win_probability'] === 100) {
            $project->status = 'In Progress';
            $project->stage = 'Deliver';
            $project->sales_stage = 'Closed Won';
            $project->win_probability = 100;
            if ($project->progress < 10) {
                $project->progress = 15;
            }

            // Notifikasi ke tim PMO & Manajemen
            if (\Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                $pmoUsers = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['PMO', 'Project Manager', 'Lead Engineer']))->get();
                foreach ($pmoUsers as $pmo) {
                    \App\Models\Notification::create([
                        'user_id' => $pmo->id,
                        'title'   => \Illuminate\Support\Str::limit("Proyek Closed Won & In Progress: " . $project->name, 240),
                        'message' => \Illuminate\Support\Str::limit("Proyek '{$project->name}' (Klien: " . ($project->client ?: '-') . ") telah Closed Won dan otomatis beralih ke fase In Progress (Delivery PMO).", 240),
                        'url'     => route('projects.show', $project->id),
                        'is_read' => false,
                    ]);
                }
            }
        } elseif (str_contains($stageLower, 'lost') || $validated['sales_stage'] === 'Closed Lost' || (int)$validated['win_probability'] === 0) {
            $project->status = 'Cancelled';
            $project->sales_stage = 'Closed Lost';
            $project->win_probability = 0;
        }

        $project->save();

        $msg = (str_contains($stageLower, 'won') || $validated['sales_stage'] === 'Closed Won')
            ? "Status proyek berhasil diubah ke Closed Won dan otomatis dipindahkan ke status In Progress!"
            : "Pipeline Sales & Opportunity berhasil diperbarui!";
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg, 'project' => $project->fresh(['bdm'])]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Update Status / Lifecycle Stage Langsung dari Tampilan Detail
     */
    public function updateStageDirect(Request $request, Project $project)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Draft,Opportunity,In Progress,Pending,Completed',
        ]);

        $newStatus = $validated['status'];
        $updateData = ['status' => $newStatus];

        if ($newStatus === 'Draft') {
            $updateData['stage'] = 'Acquire';
        } elseif ($newStatus === 'Opportunity') {
            $updateData['stage'] = 'Acquire';
            $updateData['sales_stage'] = $project->sales_stage ?: 'Proposal Submission';
        } elseif ($newStatus === 'In Progress') {
            $updateData['stage'] = 'Deliver';
            if ($project->progress < 10) {
                $updateData['progress'] = 15;
            }
        } elseif ($newStatus === 'Pending') {
            // keep stage
        } elseif ($newStatus === 'Completed') {
            $updateData['stage'] = 'Deliver';
            $updateData['progress'] = 100;
            $updateData['sales_stage'] = 'Closed Won';
        }

        $project->update($updateData);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => "Status proyek berhasil diubah ke '{$newStatus}'.", 'project' => $project->fresh()]);
        }

        return back()->with('success', "Status proyek berhasil diubah menjadi '{$newStatus}'.");
    }

    /**
     * Update Estimasi & Data Proyek (Meta Banner)
     */
    public function updateMeta(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'contract_value' => 'nullable|numeric|min:0',
            'start_date'     => 'nullable|date',
            'deadline'       => 'nullable|date',
            'description'    => 'nullable|string|max:1000',
        ]);

        $updateData = ['name' => $validated['name']];
        if ($request->has('contract_value')) {
            $updateData['contract_value'] = $validated['contract_value'];
        }
        if ($request->has('start_date')) {
            $updateData['start_date'] = $validated['start_date'];
        }
        if ($request->has('deadline')) {
            $updateData['deadline'] = $validated['deadline'];
        }
        if ($request->has('description')) {
            $updateData['description'] = $validated['description'];
        }

        $project->update($updateData);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Data dan estimasi proyek berhasil diperbarui.', 'project' => $project->fresh()]);
        }

        return back()->with('success', 'Data dan estimasi proyek berhasil diperbarui.');
    }

    public function getData()
    {
        $projects = Project::with(['tasks:id,project_id,progress,status'])
            ->where(function($q) {
                $q->whereNull('project_type')->orWhere('project_type', '!=', 'Meeting / Internal');
            })
            ->get()->map(function($project) {
            return [
                'id'                 => $project->id,
                'name'               => $project->name,
                'client'             => $project->client,
                'sales_name'         => $project->sales_name,
                'location'           => $project->location,
                'project_type'       => $project->project_type,
                'visit_schedule'     => $project->visit_schedule,
                'start_date'         => $project->start_date ? $project->start_date->format('Y-m-d') : null,
                'deadline'           => $project->deadline ? $project->deadline->format('Y-m-d') : null,
                'duration_days'      => $project->duration_days,
                'duration_formatted' => $project->duration_formatted,
                'status'             => $project->status,
                'progress'           => $project->progress,
            ];
        });

        return response()->json($projects);
    }

    /**
     * Hitung ulang status project secara otomatis berdasarkan tasks.
     * - Tidak ada task / semua 0%  → Planning
     * - Ada task yang berjalan     → On Progress
     * - Semua task Completed       → Completed
     */
    private function recalculateStatus(Project $project): void
    {
        $tasks = $project->tasks;

        if ($tasks->isEmpty()) {
            $status = 'Planning';
        } elseif ($tasks->every(fn($t) => $t->status === 'Completed')) {
            $status = 'Completed';
        } else {
            $status = 'On Progress';
        }

        $project->updateQuietly(['status' => $status]);
    }
}