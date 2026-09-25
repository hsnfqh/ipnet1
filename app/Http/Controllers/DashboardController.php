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

        $salesTeam = \App\Http\Controllers\BdmController::$salesTeam;

        // Standalone Sales: HANYA proyek peluang / komersial / sales pipeline (terpisah dari eksekusi teknikal / engineer)
        $allProjectsQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti', 'CUTI', 'Cuti'])
            ->where('client', '!=', 'Internal / Umum')
            ->where(function($q) use ($user) {
                $q->where('stage', 'Acquire')
                  ->orWhere('opportunity_source', 'Direct Sales Prospecting')
                  ->orWhereNotNull('opportunity_source')
                  ->orWhereNotNull('bdm_id')
                  ->orWhere('bdm_handover_status', 'Self-Sourced Sales')
                  ->orWhere('created_by', $user->id)
                  ->orWhereHas('creator', function($c) {
                      $c->whereHas('roles', function($r) {
                          $r->whereIn('name', ['Sales', 'Account Manager', 'BDM', 'BusDev', 'Business Development']);
                      });
                  });
            })
            // JANGAN menyangkut teknikal / engineer / maintenance
            ->where('name', 'not like', '%Preventive Maintenance%')
            ->where('name', 'not like', '%Corrective Maintenance%')
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

        // 1. Opportunity: prospek/kualifikasi awal
        $oppProjects = $projects->filter(function($p) {
            $st = strtolower($p->status ?? '');
            $sst = $p->sales_stage ?? '';
            return in_array($st, ['opportunity', 'prospect', 'inisiasi', 'draft', 'planning']) 
                || in_array($sst, ['Qualification', 'Qualified Opportunity', 'Proposal Request', 'Quotation']);
        });
        $totalOppCount = $oppProjects->count();
        $totalOppValue = $oppProjects->sum($valOf);

        // 2. In Progress: negosiasi & kontrak aktif
        $inProgressProjects = $projects->filter(function($p) {
            $st = strtolower($p->status ?? '');
            $sst = $p->sales_stage ?? '';
            $isComplete = in_array($st, ['completed', 'finished', 'delivered', 'done', 'selesai']) || $sst === 'Closed Won';
            if ($isComplete) return false;
            return in_array($st, ['in progress', 'on progress', 'active', 'development', 'testing']) 
                || in_array($sst, ['Negotiation', 'Approval', 'Contract / PO / SPK']);
        });
        $totalInProgressCount = $inProgressProjects->count();
        $totalInProgressValue = $inProgressProjects->sum($valOf);

        // 3. Pending: review / tertunda
        $pendingProjects = $projects->filter(function($p) {
            $st = strtolower($p->status ?? '');
            return in_array($st, ['pending', 'on hold', 'review', 'clarification', 'waiting review']);
        });
        $totalPendingCount = $pendingProjects->count();
        $totalPendingValue = $pendingProjects->sum($valOf);

        // 4. Complete: Closed Won / selesai
        $completeProjects = $projects->filter(function($p) {
            $st = strtolower($p->status ?? '');
            $sst = $p->sales_stage ?? '';
            return in_array($st, ['completed', 'finished', 'delivered', 'done', 'selesai', 'closed']) 
                || strtolower($sst) === 'closed won';
        });
        $totalCompleteCount = $completeProjects->count();
        $totalCompleteValue = $completeProjects->sum($valOf);

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

        // Project List Table
        $projectList = (clone $allProjectsQuery)->with(['division', 'creator'])->latest('updated_at')->paginate(10)->withQueryString();

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

        // 1. Ambil seluruh data proyek/tender pre-sales
        $allTendersQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
            ->with(['division', 'creator']);
        $tendersYear = (clone $allTendersQuery)->whereYear('created_at', $selectedYear)->get();
        if ($tendersYear->isEmpty()) {
            $tendersYear = (clone $allTendersQuery)->get();
        }

        // 2. Kategori Status Pre-Sales & Tender
        $pendingProposalTenders = $tendersYear->whereIn('status', ['Opportunity', 'Draft', 'Planning'])
            ->where('stage', '!=', 'Deliver')
            ->whereNotIn('sales_stage', ['Closed Won', 'Closed Lost'])
            ->filter(fn($p) => !str_contains($p->name, 'On Going Project') && !str_contains($p->name, 'Closed Project') && !str_contains($p->name, 'Preventive Maintenance'))
            ->values();
        $inReviewTenders        = $tendersYear->whereIn('status', ['Pending', 'Waiting Approval'])->values();
        $wonTenders             = $tendersYear->filter(function($p) {
            return $p->stage === 'Deliver' 
                || in_array($p->status, ['On Progress', 'Completed', 'Closed Won']) 
                || $p->sales_stage === 'Closed Won'
                || str_contains($p->name, 'On Going Project')
                || str_contains($p->name, 'Closed Project')
                || str_contains($p->name, 'Preventive Maintenance');
        })->values();

        $totalTenderCount        = $tendersYear->count();
        $totalProposalNeeded     = $pendingProposalTenders->whereNull('proposal_file')->count();
        $proposalsReadyCount     = $tendersYear->whereNotNull('proposal_file')->count();
        $totalPipelineValue      = $pendingProposalTenders->sum('contract_value');
        $totalReviewValue        = $inReviewTenders->sum('contract_value');
        $totalWonValue           = $wonTenders->sum('contract_value');
        $technicalWinRate        = $totalTenderCount > 0 ? round(($wonTenders->count() / $totalTenderCount) * 100, 1) : 0;

        // Lifecycle Stages Breakdown
        $lifecycleStats = [
            'requirement' => $tendersYear->whereIn('status', ['Opportunity', 'Draft'])->count(),
            'sizing'      => $tendersYear->whereNull('proposal_file')->whereIn('status', ['Opportunity', 'Draft', 'Planning'])->count(),
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

        $allProjectsQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti']);
        
        $projects = (clone $allProjectsQuery)->whereYear('created_at', $selectedYear)->get();
        if ($projects->isEmpty()) {
            $projects = (clone $allProjectsQuery)->get();
        }

        // Summary Pipeline BDM
        $totalProjectCount = $projects->count();
        $totalNilaiProject = $projects->sum('contract_value');

        // 1. Peluang Tender Baru (Opportunity)
        $opportunityProjects = $projects->whereIn('status', ['Opportunity', 'Draft', 'Planning']);
        $totalOpportunityCount = $opportunityProjects->count();
        $totalNilaiOpportunity = $opportunityProjects->sum('contract_value');

        // 2. Proposal SOW Siap / Tender Siap
        $proposalsReadyCount = (clone $allProjectsQuery)->whereNotNull('proposal_file')->count();
        $proposalsPendingCount = (clone $allProjectsQuery)->whereNull('proposal_file')->whereIn('status', ['Opportunity', 'Draft', 'Planning'])->count();

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
        $recentProjects        = (clone $allProjectsQuery)->latest()->take(6)->get();
        $recentClients         = \App\Models\Client::latest()->take(5)->get();
        $recentVendors         = \App\Models\Vendor::latest()->take(5)->get();
        $inventoryHighlights   = \App\Models\InventoryItem::orderBy('stock', 'asc')->take(5)->get();
        $recentProposals       = (clone $allProjectsQuery)->whereNotNull('proposal_file')->latest('updated_at')->take(5)->get();

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

        $allProjectsQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
            ->with(['division', 'creator']);
        
        $projects = (clone $allProjectsQuery)->whereYear('created_at', $selectedYear)->get();
        if ($projects->isEmpty()) {
            $projects = (clone $allProjectsQuery)->get();
        }

        // Summary Solution Architect
        $totalProjectsCount = $projects->count();
        $totalPipelineValue = $projects->sum('contract_value');

        // 1. Dokumen Desain & Proposal Siap (HLD / LLD / SOW)
        $proposalsReady = (clone $allProjectsQuery)->whereNotNull('proposal_file')->get();
        $proposalsReadyCount = $proposalsReady->count();
        $totalMandays = (clone $allProjectsQuery)->sum('mandays') ?: 185;

        // 2. Desain & BoQ Dalam Proses (In Progress / Review)
        $designPending = (clone $allProjectsQuery)->whereNull('proposal_file')->whereIn('status', ['Opportunity', 'Draft', 'Planning'])->get();
        $designPendingCount = $designPending->count();

        // 3. Proyek Implementasi Aktif (Design Handed Over)
        $activeProjects = $projects->whereIn('status', ['On Progress', 'In Progress']);
        $activeProjectsCount = $activeProjects->count();

        // 4. Proyek Selesai / Deal Won
        $wonProjects = $projects->whereIn('status', ['Completed', 'Closed Won']);
        $wonProjectsCount = $wonProjects->count();

        // 5. Technical Handover 6 Pillars Validation
        $handoverPillars = [
            'approved_solution' => $proposalsReadyCount,
            'hld_lld_design'    => $proposalsReadyCount,
            'boq_bom_specs'     => $projects->where('contract_value', '>', 0)->count(),
            'feasibility_risk'  => $totalProjectsCount,
            'assumptions'       => $proposalsReadyCount,
            'exclusions'        => $proposalsReadyCount,
        ];

        // Monthly Trend Desain Arsitektur
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

        // Domain Arsitektur Breakdown
        $domainCounts = [
            'Enterprise Campus Network'    => 0,
            'Next-Gen Security & SOC'      => 0,
            'Data Center & Server Storage' => 0,
            'SD-WAN & Cloud Infra'         => 0,
        ];
        foreach ($projects as $p) {
            $pNameLower = strtolower($p->name . ' ' . ($p->description ?? ''));
            if (str_contains($pNameLower, 'firewall') || str_contains($pNameLower, 'fortinet') || str_contains($pNameLower, 'security') || str_contains($pNameLower, 'soc')) {
                $domainCounts['Next-Gen Security & SOC'] += 1;
            } elseif (str_contains($pNameLower, 'server') || str_contains($pNameLower, 'storage') || str_contains($pNameLower, 'data center') || str_contains($pNameLower, 'dell') || str_contains($pNameLower, 'nutanix')) {
                $domainCounts['Data Center & Server Storage'] += 1;
            } elseif (str_contains($pNameLower, 'sd-wan') || str_contains($pNameLower, 'cloud') || str_contains($pNameLower, 'wan') || str_contains($pNameLower, 'cisco')) {
                $domainCounts['SD-WAN & Cloud Infra'] += 1;
            } else {
                $domainCounts['Enterprise Campus Network'] += 1;
            }
        }
        $domainData = array_values($domainCounts);

        // Lists
        $recentDesignProjects  = (clone $allProjectsQuery)->latest()->take(6)->get();
        $pendingSowProjects    = (clone $allProjectsQuery)->whereNull('proposal_file')->whereIn('status', ['Opportunity', 'Draft', 'Planning'])->latest()->take(6)->get();
        $inventoryHighlights   = \Illuminate\Support\Facades\Schema::hasTable('inventory_items') ? \App\Models\InventoryItem::orderBy('stock', 'asc')->take(5)->get() : collect();
        $partnerVendors        = \Illuminate\Support\Facades\Schema::hasTable('partnerships') ? \App\Models\Partnership::latest()->take(5)->get() : collect();
        $user = auth()->user();
        $pocSchedules = Schedule::with(['project', 'engineer', 'engineers'])
            ->where(function($q) use ($user) {
                $q->where('engineer_id', $user->id)
                  ->orWhere('created_by', $user->id)
                  ->orWhereHas('engineers', fn($sq) => $sq->where('users.id', $user->id));
            })
            ->where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->take(5)
            ->get();

        if ($pocSchedules->isEmpty()) {
            $pocSchedules = Schedule::with(['project', 'engineer', 'engineers'])
                ->where(function($q) {
                    $q->where('category', 'like', '%PoC%')
                      ->orWhere('category', 'like', '%Lab%')
                      ->orWhere('category', 'like', '%Desain%')
                      ->orWhere('category', 'like', '%SOW%')
                      ->orWhere('category', 'like', '%Review%');
                })
                ->where('date', '>=', now()->toDateString())
                ->orderBy('date', 'asc')
                ->take(5)
                ->get();
        }

        $data = [
            'selectedYear'          => $selectedYear,
            'totalProjectsCount'    => $totalProjectsCount,
            'totalPipelineValue'    => $totalPipelineValue,
            'proposalsReadyCount'   => $proposalsReadyCount,
            'designPendingCount'    => $designPendingCount,
            'totalMandays'          => $totalMandays,
            'activeProjectsCount'   => $activeProjectsCount,
            'wonProjectsCount'      => $wonProjectsCount,
            'handoverPillars'       => $handoverPillars,
            'monthlyChartData'      => $monthlyDataInMillions,
            'domainChartData'       => $domainData,
            'domainLabels'          => array_keys($domainCounts),
            'recentDesignProjects'  => $recentDesignProjects,
            'pendingSowProjects'    => $pendingSowProjects,
            'inventoryHighlights'   => $inventoryHighlights,
            'partnerVendors'        => $partnerVendors,
            'pocSchedules'          => $pocSchedules,
        ];

        return view('architect.dashboard', $data);
    }
}