<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\Schedule;
use App\Models\User;
use App\Helpers\FileUploadHelper;

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

        // Filter projects sesuai scope role yang login (kecualikan dummy/internal Day Off & Draft yang masih dicoba-coba)
        $projectsQuery = Project::with(['tasks', 'creator'])
            ->whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti', 'CUTI', 'Cuti'])
            ->where('client', '!=', 'Internal / Umum')
            ->whereNotIn('status', ['Draft', 'draft'])
            ->where('stage', '!=', 'Draft');
        if ($scopeIds !== null) {
            $projectIds = $tasks->pluck('project_id')->filter()->unique();
            $divisionId = $user->division_id;
            $projectsQuery->where(function($q) use ($projectIds, $user, $divisionId) {
                if ($projectIds->isNotEmpty()) {
                    $q->whereIn('id', $projectIds);
                } else {
                    $q->whereRaw('0 = 1');
                }
                $q->orWhere('created_by', $user->id);
                if ($divisionId) {
                    $q->orWhere('division_id', $divisionId);
                }
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
        // ============================================================
        // DATA CHART LOAD PEKERJAAN ENGINEER (Bulan Ini & Minggu Ini)
        // Menghitung Task Aktif vs Selesai (Eksklusif: Meeting & Day Off tidak masuk beban penugasan)
        // ============================================================
        $startOfWeek  = now()->startOfWeek(\Carbon\Carbon::MONDAY)->startOfDay();
        $endOfWeek    = now()->endOfWeek(\Carbon\Carbon::SUNDAY)->endOfDay();
        $startOfMonth = now()->startOfMonth()->startOfDay();
        $endOfMonth   = now()->endOfMonth()->endOfDay();

        $buildEngineerLoad = function($taskList) use ($engineers, $hasTaskUser) {
            return $engineers->map(function($engineer) use ($taskList, $hasTaskUser) {
                $engineerTasks = $taskList->filter(function($t) use ($engineer, $hasTaskUser) {
                    if ($t->engineer_id == $engineer->id) return true;
                    if ($hasTaskUser && $t->relationLoaded('engineers') && $t->engineers->contains('id', $engineer->id)) {
                        return true;
                    }
                    return false;
                });

                // Task aktif & Task selesai (Status Completed = Selesai, Status selain Completed = Aktif)
                $activeTasks = $engineerTasks->where('status', '!=', 'Completed')->count();
                $completedTasks = $engineerTasks->where('status', 'Completed')->count();

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
                    'active'    => $activeTasks,
                    'tasks'     => $activeTasks,
                    'schedules' => 0,
                    'dayOff'    => 0,
                    'completed' => $completedTasks,
                    'total'     => $activeTasks + $completedTasks,
                ];
            })->values();
        };

        // Data Minggu Ini: Task pada rentang pekan ini (Senin - Minggu)
        $weekTasks = $tasks->filter(function($t) use ($startOfWeek, $endOfWeek) {
            $taskDate = $t->deadline ?? $t->created_at;
            return $taskDate && $taskDate >= $startOfWeek && $taskDate <= $endOfWeek;
        });
        $engineerLoadWeekData = $buildEngineerLoad($weekTasks);

        // Data Bulan Ini: Task pada rentang bulan ini (Tanggal 1 - 30/31)
        $monthTasks = $tasks->filter(function($t) use ($startOfMonth, $endOfMonth) {
            $taskDate = $t->deadline ?? $t->created_at;
            return $taskDate && $taskDate >= $startOfMonth && $taskDate <= $endOfMonth;
        });
        $engineerLoadMonthData = $buildEngineerLoad($monthTasks);

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

        // Permohonan persetujuan draft untuk pimpinan (Susanto & Hariyadi)
        $isSusanto = str_contains(strtolower($user->name), 'susanto') || $user->hasAnyRole(['Division Head', 'Head Divisi', 'Group Leader', 'HD / Direktur', 'Group Leader Delivery & Operation', 'Group Leader Commercial & Solution']);
        $isHariyadi = str_contains(strtolower($user->name), 'hariyadi') || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur']);
        $pendingDraftApprovals = collect();
        if (($isSusanto || $isHariyadi) && \Illuminate\Support\Facades\Schema::hasTable('projects')) {
            $pendingDraftApprovals = Project::where(function($q) {
                    $q->whereNotNull('handover_data')
                      ->orWhere('status', 'Draft')
                      ->orWhere('stage', 'Draft');
                })
                ->whereNull('deleted_at')
                ->latest()
                ->get()
                ->filter(function($p) use ($isSusanto, $isHariyadi) {
                    $hd = is_array($p->handover_data) ? $p->handover_data : (json_decode($p->handover_data ?? '', true) ?: []);
                    $approvals = $hd['draft_approvals'] ?? [];
                    $needHead = !empty($approvals['head']['assigned']) && empty($approvals['head']['approved']);
                    $needDirector = !empty($approvals['director']['assigned']) && empty($approvals['director']['approved']);

                    if ($isSusanto && $needHead) return true;
                    if ($isHariyadi && $needDirector) return true;

                    // Jika status proyek masih Draft dan belum disetujui
                    $isDraftState = in_array(strtolower($p->status ?? ''), ['draft']) || in_array(strtolower($p->stage ?? ''), ['draft']);
                    if ($isDraftState) {
                        if ($isSusanto && empty($approvals['head']['approved'])) return true;
                        if ($isHariyadi && empty($approvals['director']['approved'])) return true;
                    }

                    return false;
                })->values();
        }
        $data['pendingDraftApprovals'] = $pendingDraftApprovals;

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
        $isManagerial = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader Commercial & Solution', 'PMO', 'Project Manager']) || str_contains(strtolower($user->name), 'susanto') || str_contains(strtolower($user->name), 'hariyadi');

        $validSalesNames = ['Raiza', 'Nabylla Berlianita', 'Nabylla', 'raiza', 'nabylla'];

        // Standalone Sales: HANYA proyek peluang / komersial / sales pipeline (Raiza & Nabylla Berlianita)
        $allProjectsQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti', 'CUTI', 'Cuti'])
            ->where('client', '!=', 'Internal / Umum')
            ->where('name', 'not like', '%Preventive Maintenance%')
            ->where('name', 'not like', '%Corrective Maintenance%')
            ->where('name', 'not like', '%SLA%')
            ->where('name', 'not like', '%Training%')
            ->where('name', 'not like', '%Meeting%')
            ->where('name', 'not like', '%On Going Project%')
            ->where('name', 'not like', '%Closed Project%')
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
            ->where(function ($ex) {
                $ex->whereNull('sales_name')
                   ->orWhere(function ($sn) {
                       $sn->where('sales_name', 'not like', '%Sales Team%')
                          ->where('sales_name', 'not like', '%Via%')
                          ->where('sales_name', 'not like', '%Widodo%')
                          ->where('sales_name', 'not like', '%Donny%')
                          ->where('sales_name', 'not like', '%Erie%')
                          ->where('sales_name', 'not like', '%Hendry%')
                          ->where('sales_name', 'not like', '%Nelvia%')
                          ->where('sales_name', 'not like', '%Ribka%')
                          ->where('sales_name', 'not like', '%Sabar%');
                   });
            })
            ->whereDoesntHave('creator', function($c) {
                $c->whereHas('roles', function($r) {
                    $r->whereIn('name', ['Engineer', 'Field Engineer', 'Lead Maintenance', 'Maintenance', 'Lead Engineer', 'Network Engineer', 'Security Engineer', 'Managed Service', 'Team Leader Engineering', 'Team Leader', 'Lead Divisi']);
                });
            })
            ->with(['bdm', 'creator', 'salesActivities']);

        if (!$isManagerial) {
            $allProjectsQuery->where(function($q) use ($user) {
                $q->where('sales_name', $user->name)
                  ->orWhereRaw('LOWER(sales_name) = ?', [strtolower($user->name)])
                  ->orWhere('sales_name', 'like', '%' . $user->name . '%')
                  ->orWhere('created_by', $user->id)
                  ->orWhere('bdm_id', $user->id);
            });
        }

        $projects = (clone $allProjectsQuery)->where(function($q) use ($selectedYear) {
            $q->whereYear('created_at', $selectedYear)
              ->orWhereYear('start_date', $selectedYear)
              ->orWhereYear('po_spk_date', $selectedYear);
        })->get();

        if ($projects->isEmpty()) {
            $projects = (clone $allProjectsQuery)->get();
        }

        // Helper to extract numeric project value
        $valOf = fn($p) => (float) ($p->contract_value ?: ($p->quotation_amount ?: 0));

        // 1. Total Pipeline Value (Active Non Won/Lost)
        $activePipelineProjects = $projects->filter(function($p) {
            $salesStage = $p->sales_stage ?? '';
            $status = strtolower($p->status ?? '');
            return !in_array($salesStage, ['Closed Won', 'Closed Lost']) && !in_array($status, ['completed', 'cancelled', 'selesai']);
        });
        $totalPipelineCount = $activePipelineProjects->count();
        $totalPipelineValue = $activePipelineProjects->sum($valOf);

        // 2. Weighted Forecast Value (Nilai Tertimbang Probabilitas)
        $totalWeightedForecast = $activePipelineProjects->sum(function($p) use ($valOf) {
            $prob = $p->win_probability ?? 25;
            return $valOf($p) * ($prob / 100);
        });

        // 3. Deals in Negotiation / Approval (Closing Horizon)
        $negotiationProjects = $projects->filter(function($p) {
            $salesStage = $p->sales_stage ?? '';
            $status = strtolower($p->status ?? '');
            return in_array($salesStage, ['Negotiation', 'Approval', 'Contract / PO / SPK']) || in_array($status, ['in progress', 'on progress']);
        });
        $totalNegotiationCount = $negotiationProjects->count();
        $totalNegotiationValue = $negotiationProjects->sum($valOf);

        // 4. Closed Won YTD
        $wonProjects = $projects->filter(function($p) {
            $salesStage = $p->sales_stage ?? '';
            $status = strtolower($p->status ?? '');
            return $salesStage === 'Closed Won' || in_array($status, ['completed', 'finished', 'delivered', 'done', 'selesai']);
        });
        $totalWonCount = $wonProjects->count();
        $totalWonValue = $wonProjects->sum($valOf);

        // 5. Total Closed Lost & Win Rate Calculation
        $lostProjects = $projects->where('sales_stage', 'Closed Lost');
        $totalLostCount = $lostProjects->count();
        $totalClosedDeals = $totalWonCount + $totalLostCount;
        $winRate = $totalClosedDeals > 0 ? round(($totalWonCount / $totalClosedDeals) * 100, 1) : ($totalWonCount > 0 ? 100 : 0);

        // 6. CRM Activities Count this Month
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        $crmActivitiesQuery = \App\Models\SalesActivity::whereBetween('activity_date', [$startOfMonth, $endOfMonth]);
        $recentActivitiesQuery = \App\Models\SalesActivity::with(['project', 'sales'])->latest('activity_date');
        
        if (!$isManagerial) {
            $crmActivitiesQuery->where(function($q) use ($user) {
                $q->where('sales_id', $user->id)
                  ->orWhereHas('project', fn($pq) => $pq->where('sales_name', $user->name)->orWhere('created_by', $user->id));
            });
            $recentActivitiesQuery->where(function($q) use ($user) {
                $q->where('sales_id', $user->id)
                  ->orWhereHas('project', fn($pq) => $pq->where('sales_name', $user->name)->orWhere('created_by', $user->id));
            });
        }

        $crmActivitiesCount = $crmActivitiesQuery->count();
        $recentActivities = $recentActivitiesQuery->take(6)->get();

        // 7. 8-Stage Funnel Breakdown
        $stages = \App\Http\Controllers\SalesCrmController::$stages;
        $stageFunnel = [];
        foreach ($stages as $stageKey => $meta) {
            $stageProjects = $projects->filter(function($p) use ($stageKey) {
                if ($p->sales_stage === $stageKey) return true;
                if (empty($p->sales_stage)) {
                    $st = strtolower($p->status ?? '');
                    if ($stageKey === 'Closed Won' && in_array($st, ['completed', 'finished', 'delivered', 'done', 'selesai'])) return true;
                    if ($stageKey === 'Contract / PO / SPK' && in_array($st, ['in progress', 'on progress'])) return true;
                    if ($stageKey === 'Qualification' && in_array($st, ['opportunity', 'prospect', 'draft', 'planning'])) return true;
                }
                return false;
            });
            $stageFunnel[$stageKey] = [
                'label'          => $meta['label'],
                'color'          => $meta['color'],
                'bg'             => $meta['bg'],
                'default_prob'   => $meta['default_prob'],
                'count'          => $stageProjects->count(),
                'value'          => $stageProjects->sum($valOf),
                'weighted_value' => $stageProjects->sum(fn($p) => $valOf($p) * (($p->win_probability ?? $meta['default_prob']) / 100)),
            ];
        }

        // 8. Monthly Forecast vs Actual Trend (Jan - Dec)
        $monthlyForecast = array_fill(1, 12, 0);
        $monthlyActual = array_fill(1, 12, 0);

        foreach ($projects as $p) {
            $date = $p->created_at ?: ($p->start_date ?: $p->po_spk_date);
            if ($date) {
                $m = (int) \Carbon\Carbon::parse($date)->format('n');
                $val = $valOf($p);
                $prob = ($p->win_probability ?? 25) / 100;
                $monthlyForecast[$m] += ($val * $prob);
                if ($p->sales_stage === 'Closed Won' || in_array(strtolower($p->status ?? ''), ['completed', 'finished', 'delivered', 'done', 'selesai'])) {
                    $monthlyActual[$m] += $val;
                }
            }
        }

        $monthlyForecastInMillions = array_map(function($val) {
            return round($val / 1000000, 2);
        }, array_values($monthlyForecast));

        $monthlyActualInMillions = array_map(function($val) {
            return round($val / 1000000, 2);
        }, array_values($monthlyActual));

        // 9. Priority Deals (High Value & Active in Pipeline)
        $priorityDeals = (clone $allProjectsQuery)
            ->whereNotIn('sales_stage', ['Closed Lost'])
            ->whereNotIn('status', ['Cancelled'])
            ->orderByDesc('contract_value')
            ->take(6)
            ->get();

        // 10. Commercial Handover Pending Count
        $pendingHandoverCount = (clone $allProjectsQuery)
            ->where(function($q) {
                $q->whereIn('sales_stage', ['Contract / PO / SPK', 'Closed Won'])
                  ->orWhereIn('status', ['On Progress', 'In Progress']);
            })
            ->where(function($q) {
                $q->whereNull('commercial_handover_status')
                  ->orWhere('commercial_handover_status', 'Draft')
                  ->orWhere('commercial_handover_status', 'Pending');
            })
            ->count();

        // 5 Summary Metric Cards (Distinct & Non-overlapping)
        $totalProjectCount = $projects->count();
        $totalProjectValue = $projects->sum($valOf);

        // 4. Complete: benar-benar sudah selesai dikerjakan (Completed / Finished / Done / Selesai)
        $completeProjects = $projects->filter(function($p) {
            $st = strtolower(trim($p->status ?? ''));
            return in_array($st, ['completed', 'finished', 'delivered', 'done', 'selesai', 'closed']);
        });
        $totalCompleteCount = $completeProjects->count();
        $totalCompleteValue = $completeProjects->sum($valOf);

        // 3. Pending: review / tertunda / on hold
        $pendingProjects = $projects->filter(function($p) use ($completeProjects) {
            if ($completeProjects->contains('id', $p->id)) return false;
            $st = strtolower(trim($p->status ?? ''));
            return in_array($st, ['pending', 'on hold', 'review', 'clarification', 'waiting review', 'hold']);
        });
        $totalPendingCount = $pendingProjects->count();
        $totalPendingValue = $pendingProjects->sum($valOf);

        // 2. In Progress: project yang sedang aktif berjalan / dikerjakan / delivery
        $inProgressProjects = $projects->filter(function($p) use ($completeProjects, $pendingProjects) {
            if ($completeProjects->contains('id', $p->id) || $pendingProjects->contains('id', $p->id)) return false;
            $st = strtolower(trim($p->status ?? ''));
            $sst = $p->sales_stage ?? '';
            return in_array($st, ['in progress', 'on progress', 'active', 'development', 'testing', 'progress', 'ongoing'])
                || in_array($sst, ['Contract / PO / SPK', 'Closed Won', 'Negotiation', 'Approval']);
        });
        $totalInProgressCount = $inProgressProjects->count();
        $totalInProgressValue = $inProgressProjects->sum($valOf);

        // 1. Opportunity: prospek / kualifikasi awal (belum masuk pengerjaan)
        $oppProjects = $projects->filter(function($p) use ($completeProjects, $pendingProjects, $inProgressProjects) {
            if ($completeProjects->contains('id', $p->id) || $pendingProjects->contains('id', $p->id) || $inProgressProjects->contains('id', $p->id)) return false;
            return true;
        });
        $totalOppCount = $oppProjects->count();
        $totalOppValue = $oppProjects->sum($valOf);

        // Monthly chart data (in Billion IDR & raw amounts)
        $monthlyChartData = [];
        $monthlyChartRaw  = [];
        for ($m = 1; $m <= 12; $m++) {
            $val = $projects->filter(function($p) use ($m, $selectedYear, $valOf) {
                $date = $p->created_at ?: ($p->start_date ?: $p->po_spk_date);
                if (!$date) return false;
                $cDate = \Carbon\Carbon::parse($date);
                return $cDate->year == $selectedYear && $cDate->month == $m;
            })->sum($valOf);
            $monthlyChartData[] = round($val / 1000000000, 2);
            $monthlyChartRaw[]  = (float) $val;
        }

        // Project List Table (5 project terbaru)
        $projectList = (clone $allProjectsQuery)->with(['division', 'creator'])->latest('updated_at')->paginate(5)->withQueryString();

        $data = [
            'selectedYear'               => $selectedYear,
            'totalProjectCount'          => $totalProjectCount,
            'totalProjectValue'          => $totalProjectValue,
            'totalOppCount'              => $totalOppCount,
            'totalOppValue'              => $totalOppValue,
            'totalInProgressCount'       => $totalInProgressCount,
            'totalInProgressValue'       => $totalInProgressValue,
            'totalPendingCount'          => $totalPendingCount,
            'totalPendingValue'          => $totalPendingValue,
            'totalCompleteCount'         => $totalCompleteCount,
            'totalCompleteValue'         => $totalCompleteValue,
            'monthlyChartData'           => $monthlyChartData,
            'monthlyChartRaw'            => $monthlyChartRaw,
            'projectList'                => $projectList,
            'totalPipelineCount'         => $totalPipelineCount,
            'totalPipelineValue'         => $totalPipelineValue,
            'totalWeightedForecast'      => $totalWeightedForecast,
            'totalNegotiationCount'      => $totalNegotiationCount,
            'totalNegotiationValue'      => $totalNegotiationValue,
            'totalWonCount'              => $totalWonCount,
            'totalWonValue'              => $totalWonValue,
            'totalLostCount'             => $totalLostCount,
            'winRate'                    => $winRate,
            'crmActivitiesCount'         => $crmActivitiesCount,
            'recentActivities'           => $recentActivities,
            'stageFunnel'                => $stageFunnel,
            'monthlyForecastChart'       => $monthlyForecastInMillions,
            'monthlyActualChart'         => $monthlyActualInMillions,
            'priorityDeals'              => $priorityDeals,
            'pendingHandoverCount'       => $pendingHandoverCount,
            'salesTeam'                  => \App\Http\Controllers\BdmController::$salesTeam,
            'stages'                     => $stages,
            'isManagerial'               => $isManagerial,
        ];

        return view('sales.dashboard', $data);
    }

    public function presales(Request $request)
    {
        $user = auth()->user();
        $selectedYear = (int) $request->input('year', date('Y'));

        $validSalesNames = ['Raiza', 'Nabylla Berlianita', 'Nabylla', 'raiza', 'nabylla'];

        // 1. Ambil seluruh data proyek/tender pre-sales resmi (Sales Raiza & Nabylla Berlianita)
        $allTendersQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti', 'CUTI', 'Cuti'])
            ->where('client', '!=', 'Internal / Umum')
            ->where('name', 'not like', '%Preventive Maintenance%')
            ->where('name', 'not like', '%Corrective Maintenance%')
            ->where('name', 'not like', '%SLA%')
            ->where('name', 'not like', '%Training%')
            ->where('name', 'not like', '%Meeting%')
            ->where('name', 'not like', '%On Going Project%')
            ->where('name', 'not like', '%Closed Project%')
            ->where(function($q) use ($validSalesNames) {
                $q->whereIn('sales_name', $validSalesNames)
                  ->orWhere('sales_name', 'like', '%Raiza%')
                  ->orWhere('sales_name', 'like', '%Nabylla%')
                  ->orWhere('sales_name', 'like', '%raiza%')
                  ->orWhere('sales_name', 'like', '%nabylla%');
            })
            ->where(function ($ex) {
                $ex->where('sales_name', 'not like', '%Widodo%')
                   ->where('sales_name', 'not like', '%widodo%')
                   ->where('sales_name', 'not like', '%Via%')
                   ->where('sales_name', 'not like', '%Sales Team%')
                   ->where('sales_name', 'not like', '%Donny%')
                   ->where('sales_name', 'not like', '%Erie%')
                   ->where('sales_name', 'not like', '%Hendry%')
                   ->where('sales_name', 'not like', '%Nelvia%')
                   ->where('sales_name', 'not like', '%Ribka%')
                   ->where('sales_name', 'not like', '%Sabar%');
            })
            ->whereDoesntHave('creator', function($c) {
                $c->whereHas('roles', function($r) {
                    $r->whereIn('name', ['Engineer', 'Field Engineer', 'Lead Maintenance', 'Maintenance', 'Lead Engineer', 'Network Engineer', 'Security Engineer', 'Managed Service']);
                });
            })
            ->with(['division', 'creator']);

        $tendersYear = (clone $allTendersQuery)->whereYear('created_at', $selectedYear)->get();
        if ($tendersYear->isEmpty()) {
            $tendersYear = (clone $allTendersQuery)->get();
        }

        $isExecutive = \App\Helpers\ScopeHelper::isGlobal($user) 
            || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Head Divisi', 'Group Leader', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager']) 
            || str_contains(strtolower($user->name), 'susanto') 
            || str_contains(strtolower($user->name), 'hariyadi');

        // Pre-Sales hanya menghitung & menampilkan tender yang ditugaskan kepada Pre-Sales
        if (!$isExecutive && $user->hasAnyRole(['Presales', 'Pre-Sales'])) {
            $tendersYear = $tendersYear->filter(function($p) use ($user) {
                $hd = is_array($p->handover_data) ? $p->handover_data : (json_decode($p->handover_data ?? '', true) ?: []);
                $ps = $hd['technical_assignments']['presales'] ?? [];
                if (!empty($ps['assigned_user_id']) && $ps['assigned_user_id'] == $user->id) {
                    return true;
                }
                if (!empty($ps['assigned_to']) && strtolower(trim($ps['assigned_to'])) === strtolower(trim($user->name))) {
                    return true;
                }
                return false;
            })->values();
        }

        // 2. Kategori Status Pre-Sales & Tender
        $pendingProposalTenders = $tendersYear->whereNotIn('sales_stage', ['Closed Won', 'Closed Lost'])
            ->whereNull('proposal_file')
            ->values();

        $inReviewTenders        = $tendersYear->whereIn('status', ['Pending', 'Waiting Approval', 'In Review'])->values();

        $wonTenders             = $tendersYear->filter(function($p) {
            return $p->sales_stage === 'Closed Won' 
                || $p->status === 'Closed Won'
                || in_array($p->acquire_status, ['Deal / PO Terbit', 'Closed']);
        })->values();

        $totalTenderCount        = $tendersYear->count();
        $totalProposalNeeded     = $pendingProposalTenders->count();
        $proposalsReadyCount     = $tendersYear->whereNotNull('proposal_file')->count();
        $totalPipelineValue      = $pendingProposalTenders->sum('contract_value');
        $totalReviewValue        = $inReviewTenders->sum('contract_value');
        $totalWonValue           = $wonTenders->sum('contract_value');
        $technicalWinRate        = $totalTenderCount > 0 ? round(($wonTenders->count() / $totalTenderCount) * 100, 1) : 0;

        // Lifecycle Stages Breakdown
        $lifecycleStats = [
            'requirement' => $tendersYear->whereIn('sales_stage', ['Prospecting', 'Qualification', 'Requirement Analysis'])->count(),
            'sizing'      => $tendersYear->whereNull('proposal_file')->count(),
            'proposal'    => $proposalsReadyCount,
            'handover'    => $wonTenders->count(),
        ];

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

        // 3. Ringkasan Request Proposal Terbaru (6 items)
        $recentRequests = $tendersYear->sortByDesc('updated_at')->take(6)->values();

        // 4. Jadwal Demo / POC Terdekat (Hanya jadwal riil yang terhubung ke tender Pre-Sales aktif)
        $preSalesProjectIds = $tendersYear->pluck('id')->filter()->unique();
        $pocSchedules = Schedule::with(['project', 'engineer'])
            ->where('date', '>=', now()->toDateString())
            ->where(function($q) use ($preSalesProjectIds) {
                if ($preSalesProjectIds->isNotEmpty()) {
                    $q->whereIn('project_id', $preSalesProjectIds);
                } else {
                    $q->whereRaw('0 = 1');
                }
            })
            ->where(function($q) {
                $q->where('category', 'like', '%POC%')
                  ->orWhere('category', 'like', '%Demo%')
                  ->orWhere('category', 'like', '%Presales%')
                  ->orWhere('title', 'like', '%POC%')
                  ->orWhere('title', 'like', '%Demo%');
            })
            ->orderBy('date', 'asc')
            ->take(4)
            ->get();

        $data = [
            'user'                   => $user,
            'selectedYear'           => $selectedYear,
            'totalTenderCount'       => $totalTenderCount,
            'totalProposalNeeded'    => $totalProposalNeeded,
            'proposalsReadyCount'    => $proposalsReadyCount,
            'totalPipelineValue'     => $totalPipelineValue,
            'totalReviewValue'       => $totalReviewValue,
            'totalWonValue'          => $totalWonValue,
            'technicalWinRate'       => $technicalWinRate,
            'lifecycleStats'         => $lifecycleStats,
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
            'proposal_file'  => 'nullable|file|max:20480',
        ]);

        if ($request->hasFile('proposal_file')) {
            $path = FileUploadHelper::storePublicly($request->file('proposal_file'), 'proposals');
            $validated['proposal_file'] = $path;
        }

        $validated['presales_status'] = 'Submitted';
        $project->update($validated);

        return back()->with('success', 'Proposal teknis & estimasi mandays untuk ' . $project->name . ' berhasil disimpan dan dikirim ke tim Sales!');
    }

    public function bdm(Request $request)
    {
        $user = auth()->user();
        $selectedYear = (int) $request->input('year', date('Y'));

        $isExecutive = \App\Helpers\ScopeHelper::isGlobal($user) 
            || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Head Divisi', 'Group Leader', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager']) 
            || str_contains(strtolower($user->name), 'susanto') 
            || str_contains(strtolower($user->name), 'hariyadi');

        $allProjectsQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti']);
        
        $projects = (clone $allProjectsQuery)->whereYear('created_at', $selectedYear)->get();
        if ($projects->isEmpty()) {
            $projects = (clone $allProjectsQuery)->get();
        }

        // BD hanya menghitung & menampilkan tender yang ditugaskan kepada BD
        if (!$isExecutive && $user->hasAnyRole(['BDM', 'BusDev', 'Business Development'])) {
            $projects = $projects->filter(function($p) use ($user) {
                $hd = is_array($p->handover_data) ? $p->handover_data : (json_decode($p->handover_data ?? '', true) ?: []);
                $bdm = $hd['technical_assignments']['bdm'] ?? [];
                if (!empty($bdm['assigned_user_id']) && $bdm['assigned_user_id'] == $user->id) {
                    return true;
                }
                if (!empty($bdm['assigned_to']) && strtolower(trim($bdm['assigned_to'])) === strtolower(trim($user->name))) {
                    return true;
                }
                if (!empty($p->bdm_id) && $p->bdm_id == $user->id) {
                    return true;
                }
                return false;
            })->values();
        }

        // Summary Pipeline BDM
        $totalProjectCount = $projects->count();
        $totalNilaiProject = $projects->sum('contract_value');

        // 1. Peluang Tender Baru (Opportunity)
        $opportunityProjects = $projects->whereIn('status', ['Opportunity', 'Draft', 'Planning']);
        $totalOpportunityCount = $opportunityProjects->count();
        $totalNilaiOpportunity = $opportunityProjects->sum('contract_value');

        // 2. Proposal SOW Siap / Tender Siap
        $proposalsReadyCount = $projects->whereNotNull('proposal_file')->count();
        $proposalsPendingCount = $projects->whereNull('proposal_file')->whereIn('status', ['Opportunity', 'Draft', 'Planning'])->count();

        // 3. Tender Menang (Won) & Proyek Berjalan
        $inProgressProjects = $projects->whereIn('status', ['On Progress', 'In Progress']);
        $totalInProgressCount = $inProgressProjects->count();
        $totalNilaiInProgress = $inProgressProjects->sum('contract_value');

        $wonProjects = $projects->whereIn('status', ['Completed', 'Closed Won', 'On Progress']);
        $totalWonCount = $wonProjects->count();
        $totalWonValue = $wonProjects->sum('contract_value');

        // Monthly Trend
        $monthlyValues = array_fill(1, 12, 0);
        foreach ($projects as $p) {
            $date = $p->created_at ?? $p->start_date;
            if ($date) {
                $m = (int) \Carbon\Carbon::parse($date)->format('n');
                $monthlyValues[$m] += (float) ($p->contract_value ?? 0);
            }
        }
        $monthlyDataInMillions = array_map(function($val) {
            return round($val / 1000000, 2);
        }, array_values($monthlyValues));

        // Sektor Klien / Market Breakdown
        $sectorCounts = [
            'Government / Kementerian' => 0,
            'Banking & Financial'       => 0,
            'BUMN & Enterprise'         => 0,
            'Healthcare & Lainnya'      => 0,
        ];
        foreach ($projects as $p) {
            $clientLower = strtolower($p->client ?? '');
            if (str_contains($clientLower, 'kemenkeu') || str_contains($clientLower, 'kementerian') || str_contains($clientLower, 'dinas') || str_contains($clientLower, 'cukai') || str_contains($clientLower, 'pemerintah')) {
                $sectorCounts['Government / Kementerian'] += ($p->contract_value ?: 1);
            } elseif (str_contains($clientLower, 'bank') || str_contains($clientLower, 'btpn') || str_contains($clientLower, 'bca') || str_contains($clientLower, 'mandiri') || str_contains($clientLower, 'adira') || str_contains($clientLower, 'finance')) {
                $sectorCounts['Banking & Financial'] += ($p->contract_value ?: 1);
            } elseif (str_contains($clientLower, 'shopee') || str_contains($clientLower, 'lazada') || str_contains($clientLower, 'angkasa pura') || str_contains($clientLower, 'pln') || str_contains($clientLower, 'telkom') || str_contains($clientLower, 'indosat')) {
                $sectorCounts['BUMN & Enterprise'] += ($p->contract_value ?: 1);
            } else {
                $sectorCounts['Healthcare & Lainnya'] += ($p->contract_value ?: 1);
            }
        }
        $sectorDataInMillions = array_map(function($val) {
            return round($val / 1000000, 2);
        }, array_values($sectorCounts));

        // Lists
        $recentProjects        = $projects->sortByDesc('updated_at')->take(6)->values();
        $recentClients         = \App\Models\Client::latest()->take(5)->get();
        $recentVendors         = \App\Models\Vendor::latest()->take(5)->get();
        $inventoryHighlights   = \App\Models\InventoryItem::orderBy('stock', 'asc')->take(5)->get();
        $recentProposals       = $projects->whereNotNull('proposal_file')->sortByDesc('updated_at')->take(5)->values();

        $data = [
            'selectedYear'          => $selectedYear,
            'totalProjectCount'     => $totalProjectCount,
            'totalNilaiProject'     => $totalNilaiProject,
            'totalOpportunityCount' => $totalOpportunityCount,
            'totalNilaiOpportunity' => $totalNilaiOpportunity,
            'proposalsReadyCount'   => $proposalsReadyCount,
            'proposalsPendingCount' => $proposalsPendingCount,
            'totalInProgressCount'  => $totalInProgressCount,
            'totalNilaiInProgress'  => $totalNilaiInProgress,
            'totalWonCount'         => $totalWonCount,
            'totalWonValue'         => $totalWonValue,
            'monthlyChartData'      => $monthlyDataInMillions,
            'sectorChartData'       => $sectorDataInMillions,
            'sectorLabels'          => array_keys($sectorCounts),
            'recentProjects'        => $recentProjects,
            'recentClients'         => $recentClients,
            'recentVendors'         => $recentVendors,
            'inventoryHighlights'   => $inventoryHighlights,
            'recentProposals'       => $recentProposals,
        ];

        return view('bdm.dashboard', $data);
    }

    public function solutionArchitect(Request $request)
    {
        $user = auth()->user();
        $selectedYear = (int) $request->input('year', date('Y'));

        $validSalesNames = ['Raiza', 'Nabylla Berlianita', 'Nabylla', 'raiza', 'nabylla'];

        // 1. Ambil seluruh data proyek/tender arsitektur resmi (sinkron dengan Sales CRM & Presales)
        $allTendersQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti', 'CUTI', 'Cuti'])
            ->where('client', '!=', 'Internal / Umum')
            ->where('name', 'not like', '%Preventive Maintenance%')
            ->where('name', 'not like', '%Corrective Maintenance%')
            ->where('name', 'not like', '%SLA%')
            ->where('name', 'not like', '%Training%')
            ->where('name', 'not like', '%Meeting%')
            ->where('name', 'not like', '%On Going Project%')
            ->where('name', 'not like', '%Closed Project%')
            ->where(function($q) use ($validSalesNames) {
                $q->whereIn('sales_name', $validSalesNames)
                  ->orWhere('sales_name', 'like', '%Raiza%')
                  ->orWhere('sales_name', 'like', '%Nabylla%')
                  ->orWhere('sales_name', 'like', '%raiza%')
                  ->orWhere('sales_name', 'like', '%nabylla%');
            })
            ->where(function ($ex) {
                $ex->where('sales_name', 'not like', '%Widodo%')
                   ->where('sales_name', 'not like', '%widodo%')
                   ->where('sales_name', 'not like', '%Via%')
                   ->where('sales_name', 'not like', '%Sales Team%')
                   ->where('sales_name', 'not like', '%Donny%')
                   ->where('sales_name', 'not like', '%Erie%')
                   ->where('sales_name', 'not like', '%Hendry%')
                   ->where('sales_name', 'not like', '%Nelvia%')
                   ->where('sales_name', 'not like', '%Ribka%')
                   ->where('sales_name', 'not like', '%Sabar%');
            })
            ->whereDoesntHave('creator', function($c) {
                $c->whereHas('roles', function($r) {
                    $r->whereIn('name', ['Engineer', 'Field Engineer', 'Lead Maintenance', 'Maintenance', 'Lead Engineer', 'Network Engineer', 'Security Engineer', 'Managed Service']);
                });
            })
            ->with(['division', 'creator']);

        $tendersYear = (clone $allTendersQuery)->whereYear('created_at', $selectedYear)->get();
        if ($tendersYear->isEmpty()) {
            $tendersYear = (clone $allTendersQuery)->get();
        }

        $isExecutive = \App\Helpers\ScopeHelper::isGlobal($user) 
            || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Head Divisi', 'Group Leader', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager']) 
            || str_contains(strtolower($user->name), 'susanto') 
            || str_contains(strtolower($user->name), 'hariyadi');

        // Solution Architect hanya menghitung & menampilkan tender yang ditugaskan kepada SA
        if (!$isExecutive && $user->hasAnyRole(['Solution Architect', 'Solutions Architect', 'SA', 'Tech Develop'])) {
            $tendersYear = $tendersYear->filter(function($p) use ($user) {
                $hd = is_array($p->handover_data) ? $p->handover_data : (json_decode($p->handover_data ?? '', true) ?: []);
                $sa = $hd['technical_assignments']['architect'] ?? [];
                if (!empty($sa['assigned_user_id']) && $sa['assigned_user_id'] == $user->id) {
                    return true;
                }
                if (!empty($sa['assigned_to']) && strtolower(trim($sa['assigned_to'])) === strtolower(trim($user->name))) {
                    return true;
                }
                return false;
            })->values();
        }

        // Helper cek kelengkapan dokumen desain/topologi SA
        $hasSaDoc = function($p) {
            $hd = is_array($p->handover_data) ? $p->handover_data : (json_decode($p->handover_data ?? '', true) ?: []);
            return !empty($hd['technical_assignments']['architect']['document_path']);
        };

        // 2. Kategori Status Solution Architect & Tender
        $pendingDesignTenders = $tendersYear->filter(function($p) use ($hasSaDoc) {
            return !$hasSaDoc($p) && !in_array($p->sales_stage, ['Closed Won', 'Closed Lost']);
        })->values();

        $readyDesignTenders = $tendersYear->filter(function($p) use ($hasSaDoc) {
            return $hasSaDoc($p);
        })->values();

        $inReviewTenders = $tendersYear->whereIn('status', ['Pending', 'Waiting Approval', 'In Review'])->values();

        $wonTenders = $tendersYear->filter(function($p) {
            return $p->sales_stage === 'Closed Won' 
                || $p->status === 'Closed Won'
                || in_array($p->acquire_status, ['Deal / PO Terbit', 'Closed']);
        })->values();

        $totalTenderCount        = $tendersYear->count();
        $totalDesignNeeded       = $pendingDesignTenders->count();
        $designsReadyCount       = $readyDesignTenders->count();
        $totalPipelineValue      = $pendingDesignTenders->sum('contract_value');
        $totalReviewValue        = $inReviewTenders->sum('contract_value');
        $totalWonValue           = $wonTenders->sum('contract_value');
        $technicalWinRate        = $totalTenderCount > 0 ? round(($wonTenders->count() / $totalTenderCount) * 100, 1) : 0;

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

        // 3. Ringkasan Request Desain & Topologi Terbaru (6 items)
        $recentRequests = $tendersYear->sortByDesc('updated_at')->take(6)->values();

        // 4. Jadwal Demo / POC Terdekat (Hanya jadwal riil yang terhubung ke tender arsitektur)
        $architectProjectIds = $tendersYear->pluck('id')->filter()->unique();
        $pocSchedules = Schedule::with(['project', 'engineer', 'engineers'])
            ->where('date', '>=', now()->toDateString())
            ->where(function($q) use ($architectProjectIds, $user) {
                $q->where(function($sq) use ($architectProjectIds) {
                    if ($architectProjectIds->isNotEmpty()) {
                        $sq->whereIn('project_id', $architectProjectIds);
                    } else {
                        $sq->whereRaw('0 = 1');
                    }
                })->orWhere('engineer_id', $user->id)
                  ->orWhere('created_by', $user->id)
                  ->orWhereHas('engineers', fn($sq2) => $sq2->where('users.id', $user->id))
                  ->orWhere('category', 'like', '%POC%')
                  ->orWhere('category', 'like', '%Demo%')
                  ->orWhere('category', 'like', '%Presales%')
                  ->orWhere('category', 'like', '%Desain%')
                  ->orWhere('title', 'like', '%POC%')
                  ->orWhere('title', 'like', '%Demo%');
            })
            ->orderBy('date', 'asc')
            ->take(4)
            ->get();

        $data = [
            'user'                   => $user,
            'selectedYear'           => $selectedYear,
            'totalTenderCount'       => $totalTenderCount,
            'totalDesignNeeded'      => $totalDesignNeeded,
            'totalProposalNeeded'    => $totalDesignNeeded,
            'designsReadyCount'      => $designsReadyCount,
            'proposalsReadyCount'    => $designsReadyCount,
            'totalPipelineValue'     => $totalPipelineValue,
            'totalReviewValue'       => $totalReviewValue,
            'totalWonValue'          => $totalWonValue,
            'technicalWinRate'       => $technicalWinRate,
            'monthlyChartData'       => $monthlyDataInMillions,
            'distributionData'       => $distributionData,
            'recentRequests'         => $recentRequests,
            'pocSchedules'           => $pocSchedules,
            'inReviewCount'          => $inReviewTenders->count(),
            'wonCount'               => $wonTenders->count(),
        ];

        return view('architect.dashboard', $data);
    }
}