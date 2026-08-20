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
        $user = auth()->user();
        $isLead = $user->hasRole('Lead Engineer');

        if ($isLead) {
            // Hanya load field tasks yang dibutuhkan Alpine.js (progress & status)
            $projects = Project::with(['tasks:id,project_id,progress,status'])->get();
        } else {
            // Engineer hanya bisa lihat project yang dia punya task di dalamnya
            $projectIds = \App\Models\Task::where('engineer_id', $user->id)
                ->pluck('project_id')
                ->unique();
            $projects = Project::with(['tasks:id,project_id,progress,status'])
                ->whereIn('id', $projectIds)
                ->get();
        }

        return view('projects.index', compact('projects', 'isLead'));
    }

    public function store(ProjectRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();
        // Status awal selalu Planning saat project baru dibuat
        $data['status'] = 'Planning';

        $project = Project::create($data);

        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json($project->load(['tasks:id,project_id,progress,status']), 201);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Project berhasil ditambahkan!');
    }

    public function update(ProjectRequest $request, Project $project)
    {
        $data = $request->validated();
        // Status TIDAK diubah dari form — auto-dihitung dari tasks
        // Hanya update metadata project saja
        $project->update($data);

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
        try {
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
                'id'       => $project->id,
                'name'     => $project->name,
                'client'   => $project->client,
                'location' => $project->location,
                'deadline' => $project->deadline ? $project->deadline->format('Y-m-d') : null,
                'status'   => $project->status,
                'progress' => $project->progress,
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