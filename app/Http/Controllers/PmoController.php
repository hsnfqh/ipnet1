<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\Division;
use App\Models\Task;
use Carbon\Carbon;

class PmoController extends Controller
{
    /**
     * Tampilan Dashboard Utama PMO (Project Control Tower)
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        // 1. Ambil seluruh data proyek dengan relasi lengkap
        $query = Project::with([
            'pm:id,name,email',
            'division:id,name,code',
            'tasks.engineer:id,name',
            'creator:id,name',
        ]);

        // Filter divisi jika dipilih
        if ($request->filled('division_id') && $request->division_id !== 'all') {
            $query->where('division_id', $request->division_id);
        }

        // Filter stage jika dipilih
        if ($request->filled('stage') && $request->stage !== 'all') {
            $query->where('stage', $request->stage);
        }

        // Filter PIC PM jika dipilih
        if ($request->filled('pm_id') && $request->pm_id !== 'all') {
            $query->where('pm_id', $request->pm_id);
        }

        $allProjects = $query->orderBy('created_at', 'desc')->get();

        // 2. Default dokumen deliverables (8 Dokumen Wajib Tahap Deliver Ver 2.0)
        $documentKeys = [
            'pmp'            => 'Project Management Plan (PMP)',
            'wbs'            => 'Work Breakdown Structure (WBS)',
            'time_plan'      => 'Schedule / Time Plan',
            'eng_design'     => 'Engineering Design',
            'impl_plan'      => 'Implementation Plan',
            'uat_report'     => 'Test Plan & UAT Report',
            'handover_doc'   => 'Handover Document (BAST)',
            'as_built_doc'   => 'As Built Documentation',
        ];

        // Format data project untuk dashboard
        $formattedProjects = $allProjects->map(function ($p) use ($documentKeys) {
            $totalTasks = $p->tasks->count();
            $completedTasks = $p->tasks->whereIn('status', ['Done', 'Completed'])->count();
            $calculatedProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

            // Pastikan checklist dokumen ada
            $checklist = is_array($p->documents_checklist) ? $p->documents_checklist : [];
            $completedDocsCount = 0;
            foreach (array_keys($documentKeys) as $k) {
                if (!empty($checklist[$k]) && $checklist[$k] === true) {
                    $completedDocsCount++;
                }
            }

            // Penentuan Kesehatan Proyek (On-Track vs At-Risk vs Delayed)
            $isOverdue = $p->deadline && Carbon::parse($p->deadline)->isPast() && $calculatedProgress < 100;
            $healthStatus = 'On-Track';
            if ($isOverdue) {
                $healthStatus = 'Delayed';
            } elseif ($p->deadline) {
                $daysRemaining = Carbon::now()->diffInDays(Carbon::parse($p->deadline), false);
                if ($daysRemaining <= 7 && $calculatedProgress < 70) {
                    $healthStatus = 'At-Risk';
                }
            }

            return [
                'id'                  => $p->id,
                'name'                => $p->name,
                'client'              => $p->client,
                'sales_name'          => $p->sales_name ?: '-',
                'location'            => $p->location ?: '-',
                'stage'               => $p->stage ?: 'Deliver',
                'process_status'      => $p->process_status ?: 'In Progress',
                'health_status'       => $healthStatus,
                'progress'            => $calculatedProgress,
                'start_date'          => $p->start_date ? $p->start_date->format('d M Y') : '-',
                'deadline'            => $p->deadline ? $p->deadline->format('d M Y') : '-',
                'deadline_raw'        => $p->deadline ? $p->deadline->format('Y-m-d') : '',
                'duration'            => $p->duration_formatted ?: '-',
                'pm'                  => $p->pm ? $p->pm->name : 'Belum Ditugaskan',
                'pm_id'               => $p->pm_id,
                'division'            => $p->division ? $p->division->name : 'Umum / Cross-Divisi',
                'division_id'         => $p->division_id,
                'total_tasks'         => $totalTasks,
                'completed_tasks'     => $completedTasks,
                'documents_checklist' => $checklist,
                'docs_completed_count'=> $completedDocsCount,
                'total_docs_count'    => count($documentKeys),
            ];
        });

        // 3. Ringkasan Metrik Eksekutif
        $totalDeliver = $formattedProjects->where('stage', 'Deliver')->count();
        $onTrackCount = $formattedProjects->where('health_status', 'On-Track')->count();
        $delayedCount = $formattedProjects->where('health_status', 'Delayed')->count();
        $atRiskCount  = $formattedProjects->where('health_status', 'At-Risk')->count();

        // Gateway Handover Counts
        $ho2Count = $formattedProjects->where('stage', 'Design')->count();
        $ho3Count = $formattedProjects->where('stage', 'Deliver')->where('docs_completed_count', '>=', 6)->count();

        // 4. Hitung Utilisasi Engineer Lintas Divisi
        $allEngineers = User::role(['Engineer', 'Engineer L1', 'Engineer L2', 'Maintenance'])->get();
        $assignedEngineerIds = Task::whereHas('project', function($q) {
            $q->whereNull('deleted_at');
        })->whereIn('status', ['In Progress', 'Testing', 'Review'])
          ->pluck('engineer_id')
          ->filter()
          ->unique()
          ->toArray();

        $activeEngineersCount = count($assignedEngineerIds);
        $totalEngineersCount  = $allEngineers->count();
        $standbyEngineersCount= max(0, $totalEngineersCount - $activeEngineersCount);

        // 5. Persebaran 4-Tahap Siklus Ver 2.0
        $stageCounts = [
            'Acquire' => $allProjects->where('stage', 'Acquire')->count(),
            'Design'  => $allProjects->where('stage', 'Design')->count(),
            'Deliver' => $allProjects->where('stage', 'Deliver')->count(),
            'Operate' => $allProjects->where('stage', 'Operate')->count(),
        ];

        // 6. Data Master untuk Filter & Form
        $divisions = Division::all();
        $pmList = User::role(['PMO', 'Project Manager'])->get(['id', 'name', 'email']);

        return view('pmo.dashboard', compact(
            'formattedProjects',
            'documentKeys',
            'totalDeliver',
            'onTrackCount',
            'delayedCount',
            'atRiskCount',
            'ho2Count',
            'ho3Count',
            'activeEngineersCount',
            'standbyEngineersCount',
            'totalEngineersCount',
            'stageCounts',
            'divisions',
            'pmList'
        ));
    }

    /**
     * Update Tahap Siklus & Status Proses Proyek (Handover Gateway Approval)
     */
    public function updateStage(Request $request, Project $project)
    {
        $validated = $request->validate([
            'stage'          => 'required|string|in:Acquire,Design,Deliver,Operate',
            'process_status' => 'required|string|in:Belum Mulai,In Progress,Menunggu Handover,Selesai,Dibatalkan',
            'pm_id'          => 'nullable|exists:users,id',
        ]);

        $project->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status siklus proyek ' . $project->name . ' berhasil diperbarui!',
                'project' => $project->fresh(['pm', 'division']),
            ]);
        }

        return back()->with('success', 'Status siklus proyek berhasil diperbarui!');
    }

    /**
     * Update Checklist Dokumen Deliverable (8 Dokumen Ver 2.0)
     */
    public function updateDocuments(Request $request, Project $project)
    {
        $validated = $request->validate([
            'documents_checklist' => 'required|array',
        ]);

        $project->update([
            'documents_checklist' => $validated['documents_checklist'],
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Checklist dokumen proyek ' . $project->name . ' berhasil disimpan!',
                'project' => $project->fresh(['pm', 'division']),
            ]);
        }

        return back()->with('success', 'Checklist dokumen berhasil diperbarui!');
    }
}
