<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\Schedule;
use App\Models\User;

class DashboardController extends Controller
{
    public function lead()
    {
        $user      = auth()->user();
        $scopeIds  = \App\Helpers\ScopeHelper::getScopeUserIds($user);
        $engineers = \App\Helpers\ScopeHelper::getAssignableEngineers($user);

        // Filter tasks sesuai scope role yang login
        $tasksQuery = Task::with(['project', 'engineer']);
        if ($scopeIds !== null) {
            $tasksQuery->where(function($q) use ($scopeIds) {
                count($scopeIds) === 1
                    ? $q->where('engineer_id', $scopeIds[0])
                    : $q->whereIn('engineer_id', $scopeIds);
            });
        }
        $tasks = $tasksQuery->get();

        // Filter projects sesuai scope role yang login
        $projectsQuery = Project::with(['tasks', 'creator']);
        if ($scopeIds !== null) {
            $projectIds = $tasks->pluck('project_id')->unique();
            $projectsQuery->where(function($q) use ($projectIds, $user) {
                $q->whereIn('id', $projectIds)
                  ->orWhere('created_by', $user->id);
            });
        }
        $projects = $projectsQuery->get();

        $schedulesQuery = Schedule::with(['project', 'engineer']);
        if ($scopeIds !== null) {
            $schedulesQuery->whereIn('engineer_id', $scopeIds);
        }
        $schedules = $schedulesQuery->get();

        // Hanya tampilkan max 5 project yang sedang On Progress untuk bar chart
        $projectProgressData = $projects
            ->where('status', 'On Progress')
            ->sortByDesc('created_at')
            ->take(5)
            ->map(function($project) {
                return [
                    'name'     => substr($project->name, 0, 18),
                    'progress' => $project->progress,
                ];
            })->values();

        $statusData = [
            ['name' => 'Assigned', 'value' => $tasks->where('status', 'Assigned')->count(), 'color' => '#3B82F6'],
            ['name' => 'In Progress', 'value' => $tasks->where('status', 'In Progress')->count(), 'color' => '#F59E0B'],
            ['name' => 'Waiting Review', 'value' => $tasks->where('status', 'Waiting Review')->count(), 'color' => '#8B5CF6'],
            ['name' => 'Completed', 'value' => $tasks->where('status', 'Completed')->count(), 'color' => '#10B981'],
        ];

        // ============================================================
        // DATA CHART LOAD PEKERJAAN ENGINEER (Bulan Ini & Minggu Ini)
        // Disesuaikan sesuai scope tim masing-masing Team Leader
        // ============================================================
        $startOfWeek  = now()->startOfWeek();
        $endOfWeek    = now()->endOfWeek();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth   = now()->endOfMonth();

        $buildEngineerLoad = function($taskList) use ($engineers) {
            return $engineers->map(function($engineer) use ($taskList) {
                $engineerTasks = $taskList->where('engineer_id', $engineer->id);
                $active = $engineerTasks->where('status', '!=', 'Completed')->count();
                $completed = $engineerTasks->where('status', 'Completed')->count();
                return [
                    'name' => $engineer->name,
                    'active' => $active,
                    'completed' => $completed,
                    'total' => $active,
                ];
            })->sortByDesc('active')->values();
        };

        // Data Bulan Ini (default): Seluruh task aktif pada bulan berjalan ini
        $engineerLoadMonthData = $buildEngineerLoad(
            $tasks->filter(function($t) use ($startOfMonth, $endOfMonth) {
                if ($t->status === 'Completed') {
                    return false;
                }
                if ($t->deadline) {
                    return $t->deadline <= $endOfMonth->copy()->endOfDay();
                }
                return true;
            })
        );

        // Data Minggu Ini: Hanya task aktif yang deadlinenya jatuh pada pekan ini
        $engineerLoadWeekData = $buildEngineerLoad(
            $tasks->filter(function($t) use ($startOfWeek, $endOfWeek) {
                if ($t->status === 'Completed') {
                    return false;
                }
                if ($t->deadline) {
                    return $t->deadline >= $startOfWeek->copy()->startOfDay() 
                        && $t->deadline <= $endOfWeek->copy()->endOfDay();
                }
                // Jika tidak ada deadline, cek apakah dibuat minggu ini
                return $t->created_at >= $startOfWeek->copy()->startOfDay() 
                    && $t->created_at <= $endOfWeek->copy()->endOfDay();
            })
        );

        // Filter task yang belum selesai dan memiliki deadline
        $incompleteTasksWithDeadline = $tasks->where('status', '!=', 'Completed')
            ->whereNotNull('deadline');

        // Deadline terdekat: cari yang hari ini atau di masa depan (>= today)
        $upcomingDeadline = $incompleteTasksWithDeadline
            ->filter(fn($t) => $t->deadline->startOfDay() >= now()->startOfDay())
            ->sortBy('deadline')
            ->first();

        // Hitung total task overdue (belum selesai dan tanggal sudah terlewat)
        $overdueTasksCount = $incompleteTasksWithDeadline
            ->filter(fn($t) => $t->deadline->startOfDay() < now()->startOfDay())
            ->count();

        // Jika tidak ada deadline mendatang, ambil task aktif dengan tanggal paling mutakhir
        if (!$upcomingDeadline) {
            $upcomingDeadline = $incompleteTasksWithDeadline->sortByDesc('deadline')->first();
        }

        $data = [
            'projectsCount'          => $projects->count(),
            'tasksCount'             => $tasks->count(),
            'tasksAssigned'          => $tasks->where('status', 'Assigned')->count(),
            'tasksInProgress'        => $tasks->where('status', 'In Progress')->count(),
            'tasksCompleted'         => $tasks->where('status', 'Completed')->count(),
            'upcomingDeadline'       => $upcomingDeadline,
            'overdueTasksCount'      => $overdueTasksCount,
            'recentProjects'         => $projects->sortByDesc('created_at')->take(4)->values(),
            'recentTasks'            => $tasks->sortByDesc('created_at')->take(4)->values(),
            'projectProgressData'    => $projectProgressData,
            'statusData'             => $statusData,
            'engineerLoadData'       => $engineerLoadMonthData,
            'engineerLoadMonthData'  => $engineerLoadMonthData,
            'engineerLoadWeekData'   => $engineerLoadWeekData,
        ];

        return view('dashboard.lead', $data);
    }

    public function engineer()
    {
        $user = auth()->user();
        $myTasks = Task::with(['project'])->where('engineer_id', $user->id)->get();
        $todaySchedules = Schedule::with(['project'])
            ->where('engineer_id', $user->id)
            ->where('date', now()->toDateString())
            ->get();

        $incompleteMyTasks = $myTasks->where('status', '!=', 'Completed')
            ->whereNotNull('deadline');

        $nearestDeadline = $incompleteMyTasks
            ->filter(fn($t) => $t->deadline->startOfDay() >= now()->startOfDay())
            ->sortBy('deadline')
            ->first();

        $overdueMyCount = $incompleteMyTasks
            ->filter(fn($t) => $t->deadline->startOfDay() < now()->startOfDay())
            ->count();

        if (!$nearestDeadline) {
            $nearestDeadline = $incompleteMyTasks->sortByDesc('deadline')->first();
        }

        $data = [
            'myTasksCount'        => $myTasks->count(),
            'todaySchedulesCount' => $todaySchedules->count(),
            'myTasks'             => $myTasks->sortByDesc('created_at')->values(),
            'todaySchedules'      => $todaySchedules,
            'avgProgress'         => $myTasks->count() ? round($myTasks->avg('progress')) : 0,
            'nearestDeadline'     => $nearestDeadline,
            'overdueCount'        => $overdueMyCount,
        ];

        return view('dashboard.engineer', $data);
    }
}