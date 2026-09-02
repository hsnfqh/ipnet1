<?php
// app/Http/Controllers/ProjectController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Requests\ProjectRequest;

class ProjectController extends Controller
{
    public function index()
    {
        $user         = auth()->user();
        $isLead       = \App\Helpers\ScopeHelper::isManagerial($user);
        $isDirektur   = $user->hasAnyRole(['Direktur', 'HD / Direktur']);
        $isSupervisor = \App\Helpers\ScopeHelper::isGlobal($user);
        $canManage    = \App\Helpers\ScopeHelper::canManageTasks($user);
        $canCreate    = \App\Helpers\ScopeHelper::canCreateProjects($user);
        $scopeIds     = \App\Helpers\ScopeHelper::getScopeUserIds($user);

        if ($isDirektur || $isSupervisor) {
            // Direktur & Group Leader: Global memantau semua proyek lintas divisi
            $projects = Project::with(['tasks.engineer:id,name', 'creator:id,name'])->get();
        } elseif ($user->hasRole('Team Leader') && $user->division_id) {
            // Team Leader: Hanya proyek yang berada di divisinya sendiri
            $projects = Project::with(['tasks.engineer:id,name', 'creator:id,name'])
                ->where('division_id', $user->division_id)
                ->get();
        } elseif ($isLead) {
            $projects = Project::with(['tasks.engineer:id,name', 'creator:id,name'])->get();
        } else {
            // Engineer non-lead: Hanya project yang ada task untuk dirinya
            $projectIds = \App\Models\Task::whereIn('engineer_id', $scopeIds)
                ->pluck('project_id')
                ->unique();
            $projects = Project::with(['tasks.engineer:id,name', 'creator:id,name'])
                ->where(function($q) use ($projectIds, $user) {
                    $q->whereIn('id', $projectIds)
                      ->orWhere('created_by', $user->id);
                })
                ->get();
        }

        return view('projects.index', compact('projects', 'isLead', 'canManage', 'canCreate', 'isDirektur', 'isSupervisor'));
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
        $project->update($request->validated());

        // Recalculate status otomatis berdasarkan tasks
        $this->recalculateStatus($project);

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
        return response()->json($project->load(['tasks', 'creator']));
    }

    public function getData()
    {
        $projects = Project::with(['tasks:id,project_id,progress,status'])->get()->map(function($project) {
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