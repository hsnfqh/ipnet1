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
        $hasTaskUser = \Illuminate\Support\Facades\Schema::hasTable('task_user');

        // Filter tasks sesuai scope role yang login
        $withRelations = ['project', 'engineer'];
        if ($hasTaskUser) {
            $withRelations[] = 'engineers';
        }
        $tasksQuery = Task::with($withRelations);
        if ($scopeIds !== null) {
            $tasksQuery->where(function($q) use ($scopeIds, $hasTaskUser) {
                if (count($scopeIds) === 1) {
                    $q->where('engineer_id', $scopeIds[0]);
                    if ($hasTaskUser) {
                        $q->orWhereHas('engineers', fn($sq) => $sq->where('users.id', $scopeIds[0]));
                    }
                } else {
                    $q->whereIn('engineer_id', $scopeIds);
                    if ($hasTaskUser) {
                        $q->orWhereHas('engineers', fn($sq) => $sq->whereIn('users.id', $scopeIds));
                    }
                }
            });
        }
        $tasks = $tasksQuery->get();

        // Filter projects sesuai scope role yang login (kecualikan dummy/internal Day Off)
        $projectsQuery = Project::with(['tasks', 'creator'])
            ->whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti', 'CUTI', 'Cuti'])
            ->where('client', '!=', 'Internal / Umum');
        if ($scopeIds !== null) {
            $projectIds = $tasks->pluck('project_id')->unique();
            $projectsQuery->where(function($q) use ($projectIds, $user) {
                $q->whereIn('id', $projectIds)
                  ->orWhere('created_by', $user->id);
            });
        }
        $projects = $projectsQuery->get();

        $hasScheduleUser = \Illuminate\Support\Facades\Schema::hasTable('schedule_user');
        $scheduleRelations = ['project', 'engineer', 'creator'];
        if ($hasScheduleUser) {
            $scheduleRelations[] = 'engineers';
        }
        $schedulesQuery = Schedule::with($scheduleRelations);
        if ($scopeIds !== null) {
            $schedulesQuery->where(function($q) use ($scopeIds, $hasScheduleUser, $user) {
                if (count($scopeIds) === 1) {
                    $q->where('engineer_id', $scopeIds[0]);
                    if ($hasScheduleUser) {
                        $q->orWhereHas('engineers', fn($sq) => $sq->where('users.id', $scopeIds[0]));
                    }
                } else {
                    $q->whereIn('engineer_id', $scopeIds);
                    if ($hasScheduleUser) {
                        $q->orWhereHas('engineers', fn($sq) => $sq->whereIn('users.id', $scopeIds));
                    }
                }
                $q->orWhere('created_by', $user->id);
            });
        }
        $schedules = $schedulesQuery->get();

        // Ambil 5 project terbaru yang sedang aktif (diurutkan berdasarkan yang paling baru dibuat)
        $selectedProjects = $projects->where('status', 'On Progress')
            ->sortByDesc('created_at')
            ->take(5);

        if ($selectedProjects->count() < 5) {
            $otherProjects = $projects->where('status', '!=', 'Completed')
                ->whereNotIn('id', $selectedProjects->pluck('id'))
                ->sortByDesc('created_at')
                ->take(5 - $selectedProjects->count());
            $selectedProjects = $selectedProjects->concat($otherProjects);
        }

        $projectProgressData = $selectedProjects->map(function($project) {
            return [
                'id'       => $project->id,
                'name'     => substr($project->name, 0, 20),
                'fullName' => $project->name,
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
        // Mendukung Filter Tim Lintas Divisi & Menggabungkan Task + Jadwal Kegiatan
        // ============================================================
        $startOfWeek  = now()->startOfWeek()->startOfDay();
        $endOfWeek    = now()->endOfWeek()->endOfDay();
        $startOfMonth = now()->startOfMonth()->startOfDay();
        $endOfMonth   = now()->endOfMonth()->endOfDay();

        $buildEngineerLoad = function($taskList, $scheduleList) use ($engineers, $hasTaskUser, $hasScheduleUser) {
            return $engineers->map(function($engineer) use ($taskList, $scheduleList, $hasTaskUser, $hasScheduleUser) {
                $engineerTasks = $taskList->filter(function($t) use ($engineer, $hasTaskUser) {
                    if ($t->engineer_id == $engineer->id) return true;
                    if ($hasTaskUser && $t->relationLoaded('engineers') && $t->engineers->contains('id', $engineer->id)) {
                        return true;
                    }
                    return false;
                });

                $engineerSchedules = $scheduleList->filter(function($s) use ($engineer, $hasScheduleUser) {
                    if ($s->engineer_id == $engineer->id) return true;
                    if ($hasScheduleUser && $s->relationLoaded('engineers') && $s->engineers->contains('id', $engineer->id)) {
                        return true;
                    }
                    return false;
                });

                $activeTasks = $engineerTasks->where('status', '!=', 'Completed')->count();
                $activeSchedules = $engineerSchedules->where('category', '!=', 'Day Off')->count();
                $totalActive = $activeTasks + $activeSchedules;

                $divName = 'Lainnya';
                if ($engineer->hasRole(['Lead Maintenance', 'Maintenance']) || ($engineer->division && str_contains(strtolower($engineer->division->name), 'maintenance'))) {
                    $divName = 'Maintenance';
                } elseif ($engineer->division && str_contains(strtolower($engineer->division->name), 'network')) {
                    $divName = 'Network';
                } elseif ($engineer->division && str_contains(strtolower($engineer->division->name), 'security')) {
                    $divName = 'Security';
                }

                return [
                    'id'        => $engineer->id,
                    'name'      => $engineer->name,
                    'division'  => $divName,
                    'position'  => $engineer->position ?? $engineer->role,
                    'active'    => $totalActive,
                    'tasks'     => $activeTasks,
                    'schedules' => $activeSchedules,
                    'completed' => $engineerTasks->where('status', 'Completed')->count(),
                    'total'     => $totalActive,
                ];
            })->values();
        };

        // Data Minggu Ini: Task & Jadwal yang jatuh pada rentang pekan ini (Senin - Minggu)
        $weekTasks = $tasks->filter(function($t) use ($startOfWeek, $endOfWeek) {
            if ($t->status === 'Completed') return false;
            if ($t->deadline) {
                return $t->deadline >= $startOfWeek && $t->deadline <= $endOfWeek;
            }
            return $t->created_at >= $startOfWeek && $t->created_at <= $endOfWeek;
        });
        $weekSchedules = $schedules->filter(function($s) use ($startOfWeek, $endOfWeek) {
            if ($s->category === 'Day Off') return false;
            if ($s->date) {
                return $s->date >= $startOfWeek && $s->date <= $endOfWeek;
            }
            return false;
        });
        $engineerLoadWeekData = $buildEngineerLoad($weekTasks, $weekSchedules);

        // Data Bulan Ini: Task & Jadwal yang jatuh pada bulan ini (Tanggal 1 - 30/31)
        $monthTasks = $tasks->filter(function($t) use ($startOfMonth, $endOfMonth) {
            if ($t->status === 'Completed') return false;
            if ($t->deadline) {
                return $t->deadline >= $startOfMonth && $t->deadline <= $endOfMonth;
            }
            return $t->created_at >= $startOfMonth && $t->created_at <= $endOfMonth;
        });
        $monthSchedules = $schedules->filter(function($s) use ($startOfMonth, $endOfMonth) {
            if ($s->category === 'Day Off') return false;
            if ($s->date) {
                return $s->date >= $startOfMonth && $s->date <= $endOfMonth;
            }
            return false;
        });
        $engineerLoadMonthData = $buildEngineerLoad($monthTasks, $monthSchedules);

        // Penentuan Filter Tim Default (Doris -> Maintenance, Leader lain -> divisinya, Global -> Semua)
        $canFilterTeams = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasRole('Lead Maintenance');
        $defaultTeamFilter = 'All';
        if ($user->hasRole('Lead Maintenance')) {
            $defaultTeamFilter = 'Maintenance';
        } elseif ($user->hasRole('Team Leader')) {
            if ($user->division && str_contains(strtolower($user->division->name), 'network')) {
                $defaultTeamFilter = 'Network';
            } elseif ($user->division && str_contains(strtolower($user->division->name), 'security')) {
                $defaultTeamFilter = 'Security';
            }
        }

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

        // Data Presensi Personil Hari Ini (Live Monitoring Lead & Direktur)
        $today = now()->toDateString();
        $todayAttendancesQuery = \App\Models\Attendance::with('user')
            ->where('attendance_date', $today);
        if ($scopeIds !== null) {
            $todayAttendancesQuery->whereIn('user_id', $scopeIds);
        }
        $allTodayAttendances = $todayAttendancesQuery->orderByDesc('created_at')->get();
        $todayAttendances = $allTodayAttendances->unique('user_id')->values();
        $clockInCount = $todayAttendances->where('type', 'clock_in')->count();
        $outOfRangeCount = $todayAttendances->where('is_within_range', false)->count();

        // Jadwal Terdekat / Hari Ini untuk Widget Dashboard Lead (Kecualikan Day Off)
        $hasScheduleUser = \Illuminate\Support\Facades\Schema::hasTable('schedule_user');
        $scheduleRelations = ['project', 'engineer', 'creator'];
        if ($hasScheduleUser) {
            $scheduleRelations[] = 'engineers';
        }

        $upcomingSchedulesQuery = Schedule::with($scheduleRelations)
            ->where('category', '!=', 'Day Off');
        if ($scopeIds !== null) {
            $upcomingSchedulesQuery->where(function($q) use ($scopeIds, $hasScheduleUser, $user) {
                if (count($scopeIds) === 1) {
                    $q->where('engineer_id', $scopeIds[0]);
                    if ($hasScheduleUser) {
                        $q->orWhereHas('engineers', fn($sq) => $sq->where('users.id', $scopeIds[0]));
                    }
                } else {
                    $q->whereIn('engineer_id', $scopeIds);
                    if ($hasScheduleUser) {
                        $q->orWhereHas('engineers', fn($sq) => $sq->whereIn('users.id', $scopeIds));
                    }
                }
                $q->orWhere('created_by', $user->id);
            });
        }

        // Ambil jadwal mulai hari ini ke depan (terdekat), jika kurang dari 4 ambil yang terbaru
        $upcomingSchedules = (clone $upcomingSchedulesQuery)
            ->where('date', '>=', $today)
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(4)
            ->get();

        if ($upcomingSchedules->count() < 4) {
            $otherRecentSchedules = (clone $upcomingSchedulesQuery)
                ->whereNotIn('id', $upcomingSchedules->pluck('id'))
                ->orderByDesc('date')
                ->orderByDesc('start_time')
                ->take(4 - $upcomingSchedules->count())
                ->get();
            $recentSchedules = $upcomingSchedules->concat($otherRecentSchedules);
        } else {
            $recentSchedules = $upcomingSchedules;
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
            'recentSchedules'        => $recentSchedules->values(),
            'projectProgressData'    => $projectProgressData,
            'statusData'             => $statusData,
            'engineerLoadData'       => $engineerLoadMonthData,
            'engineerLoadMonthData'  => $engineerLoadMonthData,
            'engineerLoadWeekData'   => $engineerLoadWeekData,
            'canFilterTeams'         => $canFilterTeams,
            'defaultTeamFilter'      => $defaultTeamFilter,
            'todayAttendances'       => $todayAttendances->take(5),
            'clockInCount'           => $clockInCount,
            'outOfRangeCount'        => $outOfRangeCount,
        ];

        return view('dashboard.lead', $data);
    }

    public function engineer()
    {
        $user = auth()->user();
        $hasTaskUser     = \Illuminate\Support\Facades\Schema::hasTable('task_user');
        $hasScheduleUser = \Illuminate\Support\Facades\Schema::hasTable('schedule_user');

        $taskRelations = ['project', 'engineer'];
        if ($hasTaskUser) {
            $taskRelations[] = 'engineers';
        }

        $myTasks = Task::with($taskRelations)
            ->where(function($q) use ($user, $hasTaskUser) {
                $q->where('engineer_id', $user->id);
                if ($hasTaskUser) {
                    $q->orWhereHas('engineers', fn($sq) => $sq->where('users.id', $user->id));
                }
            })
            ->get();

        $scheduleRelations = ['project', 'engineer'];
        if ($hasScheduleUser) {
            $scheduleRelations[] = 'engineers';
        }

        $todaySchedules = Schedule::with($scheduleRelations)
            ->where('date', now()->toDateString())
            ->where(function($q) use ($user, $hasScheduleUser) {
                $q->where('engineer_id', $user->id);
                if ($hasScheduleUser) {
                    $q->orWhereHas('engineers', fn($sq) => $sq->where('users.id', $user->id));
                }
            })
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

    public function sales(Request $request)
    {
        $user = auth()->user();
        $selectedYear = (int) $request->input('year', date('Y'));

        $allProjectsQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti']);
        
        $projects = (clone $allProjectsQuery)->whereYear('created_at', $selectedYear)->get();
        if ($projects->isEmpty()) {
            $projects = (clone $allProjectsQuery)->get();
        }

        // 1. Total Project
        $totalProjectCount = $projects->count();
        $totalNilaiProject = $projects->sum('contract_value');

        // 2. Opportunity (Opportunity, Draft, Planning)
        $opportunityProjects = $projects->whereIn('status', ['Opportunity', 'Draft', 'Planning']);
        $totalOpportunityCount = $opportunityProjects->count();
        $totalNilaiOpportunity = $opportunityProjects->sum('contract_value');

        // 3. In Progress (On Progress, In Progress)
        $inProgressProjects = $projects->whereIn('status', ['On Progress', 'In Progress']);
        $totalInProgressCount = $inProgressProjects->count();
        $totalNilaiInProgress = $inProgressProjects->sum('contract_value');

        // 4. Pending (Pending, Waiting Approval)
        $pendingProjects = $projects->whereIn('status', ['Pending', 'Waiting Approval']);
        $totalPendingCount = $pendingProjects->count();
        $totalNilaiPending = $pendingProjects->sum('contract_value');

        // 5. Complete (Completed, Closed Won)
        $completeProjects = $projects->whereIn('status', ['Completed', 'Closed Won']);
        $totalCompleteCount = $completeProjects->count();
        $totalNilaiComplete = $completeProjects->sum('contract_value');

        // Monthly data for chart (Jan - Dec)
        $monthlyValues = array_fill(1, 12, 0);
        foreach ($projects as $p) {
            $date = $p->created_at ?? $p->start_date;
            if ($date) {
                $m = (int) \Carbon\Carbon::parse($date)->format('n');
                $monthlyValues[$m] += (float) ($p->contract_value ?? 0);
            }
        }
        $monthlyDataInMillions = array_map(function($val) {
            return round($val / 1000000, 2); // in Millions (Juta)
        }, array_values($monthlyValues));

        // Additional informative widgets for rich dashboard
        $recentProjects        = (clone $allProjectsQuery)->latest()->take(6)->get();
        $recentClients         = \App\Models\Client::latest()->take(5)->get();
        $recentVendors         = \App\Models\Vendor::latest()->take(5)->get();
        $inventoryHighlights   = \App\Models\InventoryItem::orderBy('stock', 'asc')->take(5)->get();

        // Presales Proposals connection for Sales team
        $proposalsReadyCount   = (clone $allProjectsQuery)->whereNotNull('proposal_file')->count();
        $proposalsPendingCount = (clone $allProjectsQuery)->whereNull('proposal_file')->whereIn('status', ['Opportunity', 'Draft', 'Planning'])->count();
        $recentProposals       = (clone $allProjectsQuery)->whereNotNull('proposal_file')->latest('updated_at')->take(5)->get();

        $data = [
            'selectedYear'          => $selectedYear,
            'totalProjectCount'     => $totalProjectCount,
            'totalNilaiProject'     => $totalNilaiProject,
            'totalOpportunityCount' => $totalOpportunityCount,
            'totalNilaiOpportunity' => $totalNilaiOpportunity,
            'totalInProgressCount'  => $totalInProgressCount,
            'totalNilaiInProgress'  => $totalNilaiInProgress,
            'totalPendingCount'     => $totalPendingCount,
            'totalNilaiPending'     => $totalNilaiPending,
            'totalCompleteCount'    => $totalCompleteCount,
            'totalNilaiComplete'    => $totalNilaiComplete,
            'proposalsReadyCount'   => $proposalsReadyCount,
            'proposalsPendingCount' => $proposalsPendingCount,
            'recentProposals'       => $recentProposals,
            'monthlyChartData'      => $monthlyDataInMillions,
            'recentProjects'        => $recentProjects,
            'recentClients'         => $recentClients,
            'recentVendors'         => $recentVendors,
            'inventoryHighlights'   => $inventoryHighlights,
        ];

        return view('sales.dashboard', $data);
    }

    public function presales(Request $request)
    {
        $user = auth()->user();
        $selectedYear = (int) $request->input('year', date('Y'));

        // 1. Ambil seluruh data proyek/tender pre-sales
        $allTendersQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti']);
        $tendersYear = (clone $allTendersQuery)->whereYear('created_at', $selectedYear)->get();
        if ($tendersYear->isEmpty()) {
            $tendersYear = (clone $allTendersQuery)->get();
        }

        // 2. Kategori Status Pre-Sales & Tender
        $pendingProposalTenders = $tendersYear->whereIn('status', ['Opportunity', 'Draft', 'Planning'])->values();
        $inReviewTenders        = $tendersYear->whereIn('status', ['Pending', 'Waiting Approval'])->values();
        $wonTenders             = $tendersYear->whereIn('status', ['On Progress', 'Completed', 'Closed Won'])->values();

        $totalTenderCount        = $tendersYear->count();
        $totalProposalNeeded     = $pendingProposalTenders->count();
        $totalPipelineValue      = $pendingProposalTenders->sum('contract_value');
        $totalReviewValue        = $inReviewTenders->sum('contract_value');
        $totalWonValue           = $wonTenders->sum('contract_value');

        // Monthly data for chart (Jan - Dec)
        $monthlyValues = array_fill(1, 12, 0);
        foreach ($tendersYear as $p) {
            $date = $p->created_at ?? $p->start_date;
            if ($date) {
                $m = (int) \Carbon\Carbon::parse($date)->format('n');
                $monthlyValues[$m] += (float) ($p->contract_value ?? 0);
            }
        }
        $monthlyDataInMillions = array_map(function($val) {
            return round($val / 1000000, 2);
        }, array_values($monthlyValues));

        // Status Distribution data for Doughnut Chart (in Millions)
        $distributionData = [
            round($totalPipelineValue / 1000000, 2),
            round($totalReviewValue / 1000000, 2),
            round($totalWonValue / 1000000, 2),
        ];

        // 3. Ringkasan Request Proposal Terbaru (5 items)
        $recentRequests = $pendingProposalTenders->take(5);

        // 4. Jadwal Demo / POC Terdekat (4 items)
        $pocSchedules = Schedule::with(['project', 'engineer'])
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->take(4)
            ->get();

        $data = [
            'user'                   => $user,
            'selectedYear'           => $selectedYear,
            'totalTenderCount'       => $totalTenderCount,
            'totalProposalNeeded'    => $totalProposalNeeded,
            'totalPipelineValue'     => $totalPipelineValue,
            'totalReviewValue'       => $totalReviewValue,
            'totalWonValue'          => $totalWonValue,
            'monthlyChartData'       => $monthlyDataInMillions,
            'distributionData'       => $distributionData,
            'recentRequests'         => $recentRequests,
            'pocSchedules'           => $pocSchedules,
            'inReviewCount'          => $inReviewTenders->count(),
            'wonCount'               => $wonTenders->count(),
        ];

        return view('presales.dashboard', $data);
    }

    public function uploadProposal(Request $request, Project $project)
    {
        $validated = $request->validate([
            'proposal_notes' => 'nullable|string',
            'mandays'        => 'nullable|integer|min:1',
            'proposal_file'  => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,zip,rar|max:20480',
        ]);

        if ($request->hasFile('proposal_file')) {
            $path = $request->file('proposal_file')->store('proposals', 'public');
            $validated['proposal_file'] = $path;
        }

        $validated['presales_status'] = 'Submitted';
        $project->update($validated);

        return back()->with('success', 'Proposal teknis & estimasi mandays untuk ' . $project->name . ' berhasil disimpan dan dikirim ke tim Sales!');
    }

    public function downloadProposal(Project $project)
    {
        if (!$project->proposal_file || !\Illuminate\Support\Facades\Storage::disk('public')->exists($project->proposal_file)) {
            return back()->with('error', 'Berkas proposal teknis belum tersedia.');
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download($project->proposal_file);
    }
}