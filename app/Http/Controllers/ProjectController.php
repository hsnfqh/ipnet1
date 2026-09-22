<?php
// app/Http/Controllers/ProjectController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use App\Http\Requests\ProjectRequest;

class ProjectController extends Controller
{
    public function index()
    {
        $user         = auth()->user();
        $isLead       = \App\Helpers\ScopeHelper::isManagerial($user);
        $isDirektur   = $user->hasAnyRole(['Direktur', 'HD / Direktur']);
        $isSupervisor = \App\Helpers\ScopeHelper::isGroupLeader($user);
        $isSales      = $user->hasAnyRole(['Sales', 'BusDev']);
        $isPmo        = $user->hasAnyRole(['PMO', 'Project Manager']);

        $canManage    = \App\Helpers\ScopeHelper::isManagerial($user) || $isSales;
        $canCreate    = \App\Helpers\ScopeHelper::canCreateProjects($user) || $isSales;
        $canEditProgress = \App\Helpers\ScopeHelper::isTeamLeader($user) || \App\Helpers\ScopeHelper::isManagerial($user);
        $scopeIds     = \App\Helpers\ScopeHelper::getScopeUserIds($user);

        $baseQuery = Project::with(['tasks.engineer:id,name', 'creator:id,name'])
            ->where(function($q) {
                $q->whereNull('project_type')->orWhere('project_type', '!=', 'Meeting / Internal');
            });

        if ($isDirektur || $isSupervisor || $isSales || $isPmo) {
            // Direktur, Group Leader, Sales, PMO: Memantau seluruh portofolio proyek
            $projects = $baseQuery->get();
        } elseif ($user->hasRole('Team Leader') && $user->division_id) {
            // Team Leader: Proyek divisi, proyek umum/unassigned, proyek yang dibuatnya, atau yang ada task anggotanya
            $teamUserIds = \App\Helpers\ScopeHelper::getScopeUserIds($user) ?? [];
            $projectIdsWithTeamTasks = \App\Models\Task::whereIn('engineer_id', $teamUserIds)->pluck('project_id')->filter()->unique();

            $projects = $baseQuery
                ->where(function($q) use ($user, $projectIdsWithTeamTasks) {
                    $q->where('division_id', $user->division_id)
                      ->orWhereNull('division_id')
                      ->orWhere('created_by', $user->id);
                    if ($projectIdsWithTeamTasks->isNotEmpty()) {
                        $q->orWhereIn('id', $projectIdsWithTeamTasks);
                    }
                })
                ->get();
        } elseif ($isLead) {
            $projects = $baseQuery->get();
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
                ->get();
        }

        return view('projects.index', compact('projects', 'isLead', 'canManage', 'canCreate', 'isDirektur', 'isSupervisor', 'canEditProgress'));
    }

    public function store(ProjectRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        $data['status'] = 'Planning';
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
            $relations[] = 'projectDocuments.uploader';
            $relations[] = 'projectDocuments.verifier';
        }
        $project->load($relations);

        $documentFlow = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('project_documents')) {
            $documentFlow = \App\Services\ProjectDocumentFlowService::getProjectDocumentProgress($project);
        }

        $allUsers = User::with('roles')->orderBy('name')->get();

        if (request()->wantsJson() || request()->isJson() || request()->ajax()) {
            return response()->json([
                'project' => $project,
                'document_flow' => $documentFlow,
                'all_users' => $allUsers,
            ]);
        }

        return view('projects.show', compact('project', 'documentFlow', 'allUsers'));
    }

    /**
     * Penugasan Tim (Project Manager atau Engineer)
     */
    public function assignTeam(Request $request, Project $project)
    {
        $validated = $request->validate([
            'role_type'  => 'required|in:pm,engineer',
            'user_id'    => 'required|exists:users,id',
            'task_title' => 'nullable|string|max:255',
            'deadline'   => 'nullable|date',
        ]);

        $user = User::findOrFail($validated['user_id']);

        if ($validated['role_type'] === 'pm') {
            $project->update(['pm_id' => $user->id]);
            $msg = "User '{$user->name}' berhasil ditugaskan sebagai Project Manager!";
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
        if ($validated['approval_role'] === 'head') {
            $approvals['head'] = [
                'approved' => true,
                'by' => 'Susanto Djaya (Head Divisi)',
                'date' => $now,
                'notes' => $validated['notes'] ?: 'Kelayakan teknis & alokasi resource disetujui.',
            ];
            $msg = "Persetujuan Head Divisi (Pak Susanto) berhasil dicatat!";
        } else {
            $approvals['director'] = [
                'approved' => true,
                'by' => 'Hariyadi (Direktur)',
                'date' => $now,
                'notes' => $validated['notes'] ?: 'Otorisasi anggaran dan persetujuan eksekusi kontrak disahkan.',
            ];
            $msg = "Otorisasi Direktur (Pak Hariyadi) berhasil dicatat!";
        }

        $handoverData['draft_approvals'] = $approvals;
        $project->handover_data = $handoverData;

        if (!empty($approvals['head']['approved']) && !empty($approvals['director']['approved'])) {
            if ($project->status === 'Draft') {
                $project->status = 'Opportunity';
            }
        }

        $project->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg, 'approvals' => $approvals]);
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
     * Update Estimasi Proyek (Meta Banner)
     */
    public function updateMeta(Request $request, Project $project)
    {
        $validated = $request->validate([
            'contract_value' => 'nullable|numeric|min:0',
            'start_date'     => 'nullable|date',
            'deadline'       => 'nullable|date',
            'description'    => 'nullable|string|max:1000',
        ]);

        $project->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Estimasi dan timeline proyek berhasil diperbarui.', 'project' => $project->fresh()]);
        }

        return back()->with('success', 'Estimasi dan timeline proyek berhasil diperbarui.');
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