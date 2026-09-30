<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Task;
use App\Models\Schedule;
use App\Models\User;
use App\Models\EngineerActivityLog;
use App\Helpers\FileUploadHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function lead(Request $request)
    {
        $user = auth()->user();
        $isSusanto = str_contains(strtolower($user->name ?? ''), 'susanto') || $user->hasAnyRole(['Division Head', 'Head Divisi', 'Group Leader', 'HD / Direktur', 'Group Leader Delivery & Operation', 'Group Leader Commercial & Solution']);
        $isHariyadi = str_contains(strtolower($user->name ?? ''), 'hariyadi') || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur']);
        $isExecutive = $isSusanto || $isHariyadi || \App\Helpers\ScopeHelper::isExecutive($user) || \App\Helpers\ScopeHelper::isGroupLeader($user);

        // Permohonan persetujuan draft untuk pimpinan (Susanto & Hariyadi)
        $pendingDraftApprovals = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('projects')) {
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

        // JIKA USER ADALAH DIREKTUR / DIVISION HEAD (HARIYADI & SUSANTO): HANYA KONEKSIKAN KE SALES & DRAFT
        if ($isExecutive) {
            $selectedYear = (int) $request->input('year', date('Y'));
            $validSalesNames = ['Raiza', 'Nabylla Berlianita', 'Nabylla', 'raiza', 'nabylla'];

            $salesProjectsQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti', 'CUTI', 'Cuti'])
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
                })
                ->with(['bdm', 'creator', 'salesActivities']);

            $salesProjects = (clone $salesProjectsQuery)->where(function($q) use ($selectedYear) {
                $q->whereYear('created_at', $selectedYear)
                  ->orWhereYear('start_date', $selectedYear)
                  ->orWhereYear('po_spk_date', $selectedYear);
            })->get();

            if ($salesProjects->isEmpty()) {
                $salesProjects = (clone $salesProjectsQuery)->get();
            }

            // Strict PHP Memory Filter
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

            $salesProjects = $salesProjects->filter($isValidSalesProject)->values();

            $valOf = fn($p) => (float) ($p->contract_value ?: ($p->quotation_amount ?: 0));

            // Summary metrics
            $totalProjectCount = $salesProjects->count();
            $totalProjectValue = $salesProjects->sum($valOf);

            // Complete / Closed Won
            $completeProjects = $salesProjects->filter(function($p) {
                $st = strtolower(trim($p->status ?? ''));
                $sst = $p->sales_stage ?? '';
                return $sst === 'Closed Won' || in_array($st, ['completed', 'finished', 'delivered', 'done', 'selesai', 'closed']);
            });
            $totalCompleteCount = $completeProjects->count();
            $totalCompleteValue = $completeProjects->sum($valOf);

            // Pending (Review / Draft)
            $pendingProjects = $salesProjects->filter(function($p) use ($completeProjects) {
                if ($completeProjects->contains('id', $p->id)) return false;
                $st = strtolower(trim($p->status ?? ''));
                return in_array($st, ['pending', 'on hold', 'review', 'clarification', 'waiting review', 'hold', 'draft']);
            });
            $totalPendingCount = $pendingProjects->count();
            $totalPendingValue = $pendingProjects->sum($valOf);

            // In Progress
            $inProgressProjects = $salesProjects->filter(function($p) use ($completeProjects, $pendingProjects) {
                if ($completeProjects->contains('id', $p->id) || $pendingProjects->contains('id', $p->id)) return false;
                $st = strtolower(trim($p->status ?? ''));
                $sst = $p->sales_stage ?? '';
                return in_array($st, ['in progress', 'on progress', 'active', 'development', 'testing', 'progress', 'ongoing'])
                    || in_array($sst, ['Contract / PO / SPK', 'Negotiation', 'Approval']);
            });
            $totalInProgressCount = $inProgressProjects->count();
            $totalInProgressValue = $inProgressProjects->sum($valOf);

            // Opportunity / Active Pipeline
            $oppProjects = $salesProjects->filter(function($p) use ($completeProjects, $pendingProjects, $inProgressProjects) {
                if ($completeProjects->contains('id', $p->id) || $pendingProjects->contains('id', $p->id) || $inProgressProjects->contains('id', $p->id)) return false;
                return true;
            });
            $totalOppCount = $oppProjects->count();
            $totalOppValue = $oppProjects->sum($valOf);

            // Active Pipeline sum
            $activePipelineProjects = $salesProjects->filter(function($p) {
                $salesStage = $p->sales_stage ?? '';
                $status = strtolower($p->status ?? '');
                return !in_array($salesStage, ['Closed Won', 'Closed Lost']) && !in_array($status, ['completed', 'cancelled', 'selesai']);
            });
            $totalPipelineCount = $activePipelineProjects->count();
            $totalPipelineValue = $activePipelineProjects->sum($valOf);

            // Win Rate
            $lostProjects = $salesProjects->where('sales_stage', 'Closed Lost');
            $totalLostCount = $lostProjects->count();
            $totalClosedDeals = $totalCompleteCount + $totalLostCount;
            $winRate = $totalClosedDeals > 0 ? round(($totalCompleteCount / $totalClosedDeals) * 100, 1) : ($totalCompleteCount > 0 ? 100 : 0);

            // Recent CRM Activities
            $recentActivities = \App\Models\SalesActivity::with(['project', 'sales'])->latest('activity_date')->take(5)->get();

            // 8-Stage Funnel
            $stages = class_exists(\App\Http\Controllers\SalesCrmController::class) && property_exists(\App\Http\Controllers\SalesCrmController::class, 'stages') ? \App\Http\Controllers\SalesCrmController::$stages : [];
            $stageFunnel = [];
            foreach ($stages as $stageKey => $meta) {
                $stageProjects = $salesProjects->filter(function($p) use ($stageKey) {
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
                ];
            }

            // Priority Deals & Projects list
            $priorityDeals = (clone $salesProjectsQuery)
                ->latest('updated_at')
                ->take(10)
                ->get()
                ->filter($isValidSalesProject)
                ->take(6)
                ->values();

            return view('dashboard.lead', [
                'isExecutive'           => true,
                'isSusanto'             => $isSusanto,
                'isHariyadi'            => $isHariyadi,
                'selectedYear'          => $selectedYear,
                'pendingDraftApprovals' => $pendingDraftApprovals,
                'totalProjectCount'     => $totalProjectCount,
                'totalProjectValue'     => $totalProjectValue,
                'totalOppCount'         => $totalOppCount,
                'totalOppValue'         => $totalOppValue,
                'totalInProgressCount'  => $totalInProgressCount,
                'totalInProgressValue'  => $totalInProgressValue,
                'totalPendingCount'     => $totalPendingCount,
                'totalPendingValue'     => $totalPendingValue,
                'totalCompleteCount'    => $totalCompleteCount,
                'totalCompleteValue'    => $totalCompleteValue,
                'totalPipelineCount'    => $totalPipelineCount,
                'totalPipelineValue'    => $totalPipelineValue,
                'winRate'               => $winRate,
                'recentActivities'      => $recentActivities,
                'stageFunnel'           => $stageFunnel,
                'priorityDeals'         => $priorityDeals,
            ]);
        }

        // UNTUK TEAM LEADER NON-EKSEKUTIF (LEAD ENGINEER TEKNIKAL)
        $scopeIds  = \App\Helpers\ScopeHelper::getScopeUserIds($user);
        $engineers = \App\Helpers\ScopeHelper::getAssignableEngineers($user);
        $hasTaskUser = \Illuminate\Support\Facades\Schema::hasTable('task_user');

        // Auto-cleanup: Pastikan task & schedule auto-generate "Implementasi Teknis" dibersihkan
        try {
            if ($hasTaskUser) {
                \Illuminate\Support\Facades\DB::statement("DELETE FROM task_user WHERE task_id IN (SELECT id FROM tasks WHERE title LIKE '%Implementasi Teknis%')");
            }
            \Illuminate\Support\Facades\DB::statement("DELETE FROM tasks WHERE title LIKE '%Implementasi Teknis%'");
            \Illuminate\Support\Facades\DB::statement("DELETE FROM schedules WHERE title LIKE '%Implementasi Teknis%'");
        } catch (\Exception $e) {}

        // Filter tasks sesuai scope role yang login
        $withRelations = ['project', 'engineer'];
        if ($hasTaskUser) {
            $withRelations[] = 'engineers';
        }
        $tasksQuery = Task::with($withRelations)->where('title', 'not like', '%Implementasi Teknis%');
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
        $tasks = $tasksQuery->get()->filter(function($t) {
            return !str_contains(strtolower($t->title ?? ''), 'implementasi teknis');
        })->values();

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

        // Ambil 5 project terbaru yang sedang aktif
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

        $weekTasks = $tasks->filter(function($t) use ($startOfWeek, $endOfWeek) {
            $taskDate = $t->deadline ?? $t->created_at;
            return $taskDate && $taskDate >= $startOfWeek && $taskDate <= $endOfWeek;
        });
        $engineerLoadWeekData = $buildEngineerLoad($weekTasks);

        $monthTasks = $tasks->filter(function($t) use ($startOfMonth, $endOfMonth) {
            $taskDate = $t->deadline ?? $t->created_at;
            return $taskDate && $taskDate >= $startOfMonth && $taskDate <= $endOfMonth;
        });
        $engineerLoadMonthData = $buildEngineerLoad($monthTasks);

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

        $incompleteTasksWithDeadline = $tasks->where('status', '!=', 'Completed')
            ->whereNotNull('deadline');

        $upcomingDeadline = $incompleteTasksWithDeadline
            ->filter(fn($t) => $t->deadline->startOfDay() >= now()->startOfDay())
            ->sortBy('deadline')
            ->first();

        $overdueTasksCount = $incompleteTasksWithDeadline
            ->filter(fn($t) => $t->deadline->startOfDay() < now()->startOfDay())
            ->count();

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

        // Activity Logs semua engineer untuk Lead Engineer
        $allEngineerActivityLogs = collect([]);
        if (\Illuminate\Support\Facades\Schema::hasTable('engineer_activity_logs')) {
            $logsQuery = EngineerActivityLog::with(['engineer', 'project']);
            // Filter berdasarkan scope tim jika bukan executive
            if ($scopeIds !== null) {
                $logsQuery->whereIn('user_id', $scopeIds);
            }
            $allEngineerActivityLogs = $logsQuery
                ->orderByDesc('activity_date')
                ->orderByDesc('created_at')
                ->take(50)
                ->get();
        }

        $data = [
            'isExecutive'            => false,
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
            'pendingDraftApprovals'  => $pendingDraftApprovals,
            'allEngineerActivityLogs'=> $allEngineerActivityLogs,
            'projects'               => $projects,
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

        // Activity Logs milik engineer ini
        $myActivityLogs = collect([]);
        if (\Illuminate\Support\Facades\Schema::hasTable('engineer_activity_logs')) {
            $myActivityLogs = EngineerActivityLog::with('project')
                ->where('user_id', $user->id)
                ->orderByDesc('activity_date')
                ->orderByDesc('created_at')
                ->take(30)
                ->get();
        }

        // Projects untuk dropdown activity log
        $hasTaskUser = \Illuminate\Support\Facades\Schema::hasTable('task_user');
        $hasScheduleUser = \Illuminate\Support\Facades\Schema::hasTable('schedule_user');

        $myProjects = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
            ->where(function($q) use ($user, $hasTaskUser, $hasScheduleUser) {
                $q->whereHas('tasks', function($tq) use ($user, $hasTaskUser) {
                    $tq->where('engineer_id', $user->id);
                    if ($hasTaskUser) {
                        $tq->orWhereHas('engineers', fn($sq) => $sq->where('users.id', $user->id));
                    }
                })
                ->orWhereHas('schedules', function($sq) use ($user, $hasScheduleUser) {
                    $sq->where('engineer_id', $user->id);
                    if ($hasScheduleUser) {
                        $sq->orWhereHas('engineers', fn($esq) => $esq->where('users.id', $user->id));
                    }
                })
                ->orWhere('created_by', $user->id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'client']);

        $data = [
            'myTasksCount'        => $myTasks->count(),
            'todaySchedulesCount' => $todaySchedules->count(),
            'myTasks'             => $myTasks->sortByDesc('created_at')->values(),
            'todaySchedules'      => $todaySchedules,
            'avgProgress'         => $myTasks->count() ? round($myTasks->avg('progress')) : 0,
            'nearestDeadline'     => $nearestDeadline,
            'overdueCount'        => $overdueMyCount,
            'myActivityLogs'      => $myActivityLogs,
            'myProjects'          => $myProjects,
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

    // ─── Dedicated Engineer Activity Log: Index (Lead Monitoring & Engineer Log) ───
    public function engineerActivityLogs(Request $request)
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('engineer_activity_logs')) {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $authUser = auth()->user();
        $isLead   = \App\Helpers\ScopeHelper::isManagerial($authUser);
        $linkedProjectIds = $this->getLinkedProjectIds($authUser);

        $query = EngineerActivityLog::with([
            'engineer',
            'project.schedules.engineer',
            'project.schedules.engineers',
            'project.tasks.engineer',
        ]);

        // Jika bukan managerial/lead, batasi log milik sendiri dan log pada proyek yang terhubung (tim/shared)
        if (!$isLead) {
            $query->where(function ($q) use ($authUser, $linkedProjectIds) {
                $q->where('user_id', $authUser->id)
                  ->orWhere('notes', 'like', "%{$authUser->name}%");

                if ($linkedProjectIds->isNotEmpty()) {
                    $q->orWhereIn('project_id', $linkedProjectIds);
                }
            });
        } else {
            // Managerial/Lead bisa filter per engineer tertentu
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        }

        // Filter Pencarian (Keyword)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('engineer', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('project', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                        ->orWhere('client', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Tipe Aktivitas
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Proyek
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter Tanggal
        if ($request->filled('date')) {
            $query->whereDate('activity_date', $request->date);
        }

        // Hitung ringkasan metrics
        $metricsQuery = clone $query;
        $totalActivities = $metricsQuery->count();
        $totalCompleted  = (clone $metricsQuery)->where('status', 'Selesai')->count();
        $totalInProgress = (clone $metricsQuery)->where('status', 'Sedang Berjalan')->count();
        $totalDelayed    = (clone $metricsQuery)->where('status', 'Ditunda')->count();
        $activeEngineersCount = (clone $metricsQuery)->distinct('user_id')->count('user_id');

        // Ambil data aktivitas dengan pagination
        $activities = $query->orderBy('activity_date', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(12)
                            ->withQueryString();

        // Data pendukung dropdown filter & form input
        $hasTaskUser = \Illuminate\Support\Facades\Schema::hasTable('task_user');
        $hasScheduleUser = \Illuminate\Support\Facades\Schema::hasTable('schedule_user');

        if ($isLead) {
            $projects = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
                ->orderBy('name')
                ->get(['id', 'name', 'client']);
        } else {
            $projects = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
                ->where(function ($q) use ($authUser, $linkedProjectIds, $hasTaskUser, $hasScheduleUser) {
                    if ($linkedProjectIds->isNotEmpty()) {
                        $q->whereIn('id', $linkedProjectIds);
                    }
                    $q->orWhereHas('tasks', function ($tq) use ($authUser, $hasTaskUser) {
                        $tq->where('engineer_id', $authUser->id);
                        if ($hasTaskUser) {
                            $tq->orWhereHas('engineers', fn($sq) => $sq->where('users.id', $authUser->id));
                        }
                    })
                    ->orWhereHas('schedules', function ($sq) use ($authUser, $hasScheduleUser) {
                        $sq->where('engineer_id', $authUser->id);
                        if ($hasScheduleUser) {
                            $sq->orWhereHas('engineers', fn($esq) => $esq->where('users.id', $authUser->id));
                        }
                    })
                    ->orWhere('created_by', $authUser->id);
                })
                ->orderBy('name')
                ->get(['id', 'name', 'client']);
        }

        $engineers = collect();
        if ($isLead) {
            $engineers = User::whereHas('roles', function ($q) {
                $q->whereIn('name', [
                    'Network Engineer', 'Security Engineer', 'Field Support (EOS)', 'Field Support',
                    'Managed Service', 'Engineer', 'Engineer L1', 'Engineer L2', 'Maintenance',
                    'Lead Engineer', 'Team Leader Engineering', 'Team Leader', 'Lead Maintenance'
                ]);
            })->orderBy('name')->get(['id', 'name', 'email']);

            if ($engineers->isEmpty()) {
                $engineers = User::orderBy('name')->get(['id', 'name', 'email']);
            }
        }

        $activityTypes = [
            'Instalasi / Penarikan Kabel',
            'Konfigurasi Router/Switch/Firewall',
            'Troubleshooting Jaringan',
            'Maintenance Rutin',
            'Survey Lokasi',
            'Dokumentasi & BA',
            'Testing & Commissioning',
            'Koordinasi & Meeting Teknis',
            'Lainnya',
        ];

        return view('engineer.activity_log.index', compact(
            'activities',
            'isLead',
            'totalActivities',
            'totalCompleted',
            'totalInProgress',
            'totalDelayed',
            'activeEngineersCount',
            'projects',
            'engineers',
            'activityTypes',
            'linkedProjectIds'
        ));
    }

    // ─── Engineer Activity Log: Store (Bulk Spreadsheet & Single) ───────────────
    public function storeActivityLog(Request $request)
    {
        if (!\Illuminate\Support\Facades\Schema::hasTable('engineer_activity_logs')) {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $authUser = auth()->user();
        $isLead   = \App\Helpers\ScopeHelper::isManagerial($authUser);

        // Mode 1: Spreadsheet Multi-Row Bulk Entry
        if ($request->has('activities') && is_array($request->activities)) {
            $projectId = $request->project_id ?: null;

            // Validasi hak akses proyek untuk engineer non-lead
            if (!$isLead && $projectId) {
                $linkedProjectIds = $this->getLinkedProjectIds($authUser);
                $hasAccess = $linkedProjectIds->contains($projectId)
                    || Project::where('id', $projectId)
                        ->where(function ($q) use ($authUser) {
                            $q->where('created_by', $authUser->id)
                              ->orWhereHas('tasks', fn($tq) => $tq->where('engineer_id', $authUser->id))
                              ->orWhereHas('schedules', fn($sq) => $sq->where('engineer_id', $authUser->id));
                        })->exists();

                if (!$hasAccess) {
                    return redirect()->back()->with('error', 'Anda tidak memiliki hak akses penugasan untuk mencatat aktivitas pada proyek ini.');
                }
            }
            $activityTitle = trim($request->activity_title ?? '');
            $itemsCreated = 0;

            foreach ($request->activities as $row) {
                $subject = trim($row['subject'] ?? '');
                if (empty($subject)) {
                    continue;
                }

                $date = !empty($row['activity_date']) ? $row['activity_date'] : date('Y-m-d');
                $time = !empty($row['time_str']) ? trim($row['time_str']) : null;
                $clientPic = trim($row['client_pic'] ?? '');
                $ipnetPic  = trim($row['ipnet_pic'] ?? '');
                $notedText = trim($row['notes'] ?? '');

                // Susun catatan & PIC
                $notesParts = [];
                if ($notedText !== '') {
                    $notesParts[] = $notedText;
                }
                if ($clientPic !== '') {
                    $notesParts[] = "PIC Klien: {$clientPic}";
                }
                if ($ipnetPic !== '') {
                    $notesParts[] = "PIC IPNET: {$ipnetPic}";
                }
                $finalNotes = implode(' | ', $notesParts);

                $location = $clientPic ? "Client Site ({$clientPic})" : null;
                $actType = !empty($row['activity_type']) ? $row['activity_type'] : (!empty($request->activity_type) ? $request->activity_type : 'Troubleshooting');

                $description = $subject;
                if ($activityTitle !== '') {
                    $description = "[{$activityTitle}] " . $description;
                }

                EngineerActivityLog::create([
                    'user_id'       => auth()->id(),
                    'project_id'    => $projectId,
                    'activity_type' => $actType,
                    'description'   => $description,
                    'location'      => $location,
                    'activity_date' => $date,
                    'start_time'    => $time,
                    'end_time'      => null,
                    'status'        => 'Selesai',
                    'notes'         => $finalNotes ?: null,
                ]);

                $itemsCreated++;
            }

            if ($itemsCreated === 0) {
                return redirect()->back()->with('error', 'Silakan isi setidaknya satu baris aktivitas.');
            }

            return redirect()->back()->with('success', "Berhasil menyimpan {$itemsCreated} aktivitas!");
        }

        // Mode 2: Single Activity Form
        $request->validate([
            'description'   => 'required|string|max:1000',
            'activity_date' => 'required|date',
            'activity_type' => 'required|string',
            'status'        => 'required|string',
        ]);

        EngineerActivityLog::create([
            'user_id'       => auth()->id(),
            'project_id'    => $request->project_id ?: null,
            'activity_type' => $request->activity_type,
            'description'   => $request->description,
            'location'      => $request->location,
            'activity_date' => $request->activity_date,
            'start_time'    => $request->start_time ?: null,
            'end_time'      => $request->end_time ?: null,
            'status'        => $request->status,
            'notes'         => $request->notes,
        ]);

        return redirect()->back()
            ->with('success', 'Catatan aktivitas berhasil disimpan!');
    }

    // ─── Engineer Activity Log: Delete ──────────────────────────────────────────
    public function deleteActivityLog(EngineerActivityLog $log)
    {
        // Hanya engineer yang membuat yang bisa hapus (atau lead)
        $user = auth()->user();
        $isLead = \App\Helpers\ScopeHelper::isManagerial($user);
        if ($log->user_id !== $user->id && !$isLead) {
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus log aktivitas ini.');
        }
        $log->delete();
        return back()->with('success', 'Catatan aktivitas berhasil dihapus.');
    }

    // ─── Engineer Activity Log: Update (Edit) ──────────────────────────────────
    public function updateActivityLog(Request $request, EngineerActivityLog $log)
    {
        $user   = auth()->user();
        $isLead = \App\Helpers\ScopeHelper::isManagerial($user);
        $linkedProjectIds = $this->getLinkedProjectIds($user);
        $isProjectMember  = $log->project_id && $linkedProjectIds->contains($log->project_id);

        if ($log->user_id !== $user->id && !$isLead && !$isProjectMember) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah log aktivitas ini.');
        }

        $subject     = trim($request->input('subject', ''));
        $date        = $request->input('activity_date') ?: $log->activity_date?->format('Y-m-d');
        $clientPic   = trim($request->input('client_pic', ''));
        $ipnetPic    = trim($request->input('ipnet_pic', ''));
        $notedText   = trim($request->input('notes', ''));
        $projectId   = $request->input('project_id') ?: $log->project_id;
        $actTitle    = trim($request->input('activity_title', ''));

        // Rebuild description (preserve title prefix if any)
        if ($actTitle !== '') {
            $description = "[{$actTitle}] " . $subject;
        } else {
            // Preserve existing title prefix
            if (preg_match('/^\[(.+?)\]\s*(.*)$/s', $log->description, $m)) {
                $description = "[{$m[1]}] " . $subject;
            } else {
                $description = $subject;
            }
        }

        // Rebuild notes
        $notesParts = [];
        if ($notedText !== '') {
            $notesParts[] = $notedText;
        }
        if ($clientPic !== '') {
            $notesParts[] = "PIC Klien: {$clientPic}";
        }
        if ($ipnetPic !== '') {
            $notesParts[] = "PIC IPNET: {$ipnetPic}";
        }
        $finalNotes = implode(' | ', $notesParts);

        $log->update([
            'project_id'    => $projectId,
            'description'   => $description,
            'activity_date' => $date,
            'notes'         => $finalNotes ?: null,
        ]);

        return back()->with('success', 'Aktivitas berhasil diperbarui!');
    }

    // ─── Engineer Activity Log: Export PDF ──────────────────────────────────────
    public function exportActivityLogPdf(\Illuminate\Http\Request $request)
    {
        $authUser = auth()->user();
        $isLead   = \App\Helpers\ScopeHelper::isManagerial($authUser);
        $linkedProjectIds = $this->getLinkedProjectIds($authUser);

        $query = EngineerActivityLog::with(['engineer', 'project']);

        if (!$isLead) {
            $query->where(function ($q) use ($authUser, $linkedProjectIds) {
                $q->where('user_id', $authUser->id)
                  ->orWhere('notes', 'like', "%{$authUser->name}%");
                if ($linkedProjectIds->isNotEmpty()) {
                    $q->orWhereIn('project_id', $linkedProjectIds);
                }
            });
        } else {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        }

        // Filter log_ids spesifik dari modal popup
        if ($request->filled('log_ids')) {
            $logIds = array_filter(array_map('trim', explode(',', $request->log_ids)));
            if (!empty($logIds)) {
                $query->whereIn('id', $logIds);
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('engineer', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('project', fn($sq) => $sq->where('name', 'like', "%{$search}%")->orWhere('client', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('activity_date', $request->date);
        }

        $activities = $query->orderBy('activity_date', 'asc')->orderBy('start_time', 'asc')->orderBy('created_at', 'asc')->get();

        // Urai data persis seperti format tabel form: NO, AKTIVITAS, TANGGAL, WAKTU (JAM), PIC KLIEN, PIC IPNET, NOTED
        $parsedActivities = $activities->map(function ($a, $idx) {
            $rawNotes  = $a->notes ?? '';
            $clientPic = '';
            $ipnetPic  = '';
            $notedOnly = $rawNotes;

            if ($rawNotes) {
                $parts = array_map('trim', explode('|', $rawNotes));
                $notedParts = [];
                foreach ($parts as $part) {
                    if (str_starts_with($part, 'PIC Klien:')) {
                        $clientPic = trim(substr($part, strlen('PIC Klien:')));
                    } elseif (str_starts_with($part, 'PIC IPNET:')) {
                        $ipnetPic = trim(substr($part, strlen('PIC IPNET:')));
                    } else {
                        $notedParts[] = $part;
                    }
                }
                $notedOnly = implode(' | ', array_filter($notedParts));
            }

            if (!$ipnetPic) {
                $ipnetPic = $a->engineer->name ?? '-';
            }

            $description = $a->description ?? '-';
            if (preg_match('/^\[(.+?)\]\s*(.*)$/s', $description, $m)) {
                $description = $m[2] ?: $m[1];
            }

            return [
                'no'         => $idx + 1,
                'activity'   => $description,
                'date'       => $a->activity_date ? $a->activity_date->format('d/m/Y') : '-',
                'time'       => $a->start_time ? \Carbon\Carbon::parse($a->start_time)->format('H:i') : '-',
                'client_pic' => $clientPic ?: '-',
                'ipnet_pic'  => $ipnetPic,
                'notes'      => $notedOnly ?: '-',
            ];
        });

        // Nama Proyek & Engineer untuk Kop Laporan
        $projectName  = $request->project_name ?: ($activities->first()?->project?->name ?? 'Aktivitas Engineer');
        $engineerName = $activities->first()?->engineer?->name ?? $authUser->name;
        $printedBy    = $authUser->name;

        // Base64 Logo untuk stabilitas render DomPDF
        $logoPath   = public_path('images/ipnet1.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('images/ipnet.png');
        }
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

        $pdf = Pdf::loadView('exports.engineer-activity-report-pdf', [
            'parsedActivities' => $parsedActivities,
            'activities'       => $activities,
            'projectName'      => $projectName,
            'engineerName'     => $engineerName,
            'printedBy'        => $printedBy,
            'logoBase64'       => $logoBase64,
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        $safeName = \Illuminate\Support\Str::slug($projectName, '_');
        $filename = "Laporan_Aktivitas_{$safeName}_" . now()->format('Ymd_His') . ".pdf";

        return $pdf->download($filename);
    }

    // ─── Engineer Activity Log: Export Excel ─────────────────────────────────────
    public function exportActivityLogExcel(\Illuminate\Http\Request $request)
    {
        $authUser = auth()->user();
        $isLead   = \App\Helpers\ScopeHelper::isManagerial($authUser);
        $linkedProjectIds = $this->getLinkedProjectIds($authUser);

        $query = EngineerActivityLog::with(['engineer', 'project']);

        if (!$isLead) {
            $query->where(function ($q) use ($authUser, $linkedProjectIds) {
                $q->where('user_id', $authUser->id)
                  ->orWhere('notes', 'like', "%{$authUser->name}%");
                if ($linkedProjectIds->isNotEmpty()) {
                    $q->orWhereIn('project_id', $linkedProjectIds);
                }
            });
        } else {
            if ($request->filled('user_id')) {
                $query->where('user_id', $request->user_id);
            }
        }

        // Filter log_ids spesifik dari modal popup
        if ($request->filled('log_ids')) {
            $logIds = array_filter(array_map('trim', explode(',', $request->log_ids)));
            if (!empty($logIds)) {
                $query->whereIn('id', $logIds);
            }
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('engineer', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('project', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('activity_date', $request->date);
        }

        $activities = $query->orderBy('activity_date', 'asc')->orderBy('start_time', 'asc')->orderBy('created_at', 'asc')->get();

        $projectName  = $request->project_name ?: ($activities->first()?->project?->name ?? 'Aktivitas Engineer');
        $engineerName = $activities->first()?->engineer?->name ?? $authUser->name;
        $safeName     = \Illuminate\Support\Str::slug($projectName, '_');
        $filename     = "Laporan_Aktivitas_{$safeName}_" . now()->format('Ymd_His') . ".xlsx";

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Aktivitas');

        // Header Dokumen
        $sheet->setCellValue('A1', 'PT. IP NETWORK SOLUSINDO');
        $sheet->setCellValue('A2', 'Golden Centrum Complex, Jl. Majapahit 26P Jakarta 10160');
        $sheet->setCellValue('A3', 'LAPORAN AKTIVITAS KRONOLOGIS ENGINEER');
        $sheet->setCellValue('A4', 'Proyek: ' . $projectName . ' | Dicatat Oleh: ' . $engineerName . ' | Dicetak: ' . now()->format('d/m/Y H:i') . ' WIB');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('8F0A0D');
        $sheet->getStyle('A2')->getFont()->setSize(9)->getColor()->setRGB('555555');
        $sheet->getStyle('A3')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A4')->getFont()->setItalic(true)->setSize(8.5)->getColor()->setRGB('666666');

        // Header Kolom (Baris 6) persis form: NO, AKTIVITAS, TANGGAL, WAKTU (JAM), PIC KLIEN, PIC IPNET, NOTED
        $headers = ['No', 'Aktivitas', 'Tanggal', 'Waktu (Jam)', 'PIC Klien', 'PIC IPNET', 'Noted'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        
        foreach ($headers as $k => $h) {
            $sheet->setCellValue($cols[$k] . '6', $h);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '8F0A0D']],
            'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER, 'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '73080A']]],
        ];
        $sheet->getStyle('A6:G6')->applyFromArray($headerStyle);
        $sheet->getRowDimension(6)->setRowHeight(24);

        $rowNum = 7;
        foreach ($activities as $idx => $act) {
            $rawNotes  = $act->notes ?? '';
            $clientPic = '';
            $ipnetPic  = '';
            $notedOnly = $rawNotes;

            if ($rawNotes) {
                $parts = array_map('trim', explode('|', $rawNotes));
                $notedParts = [];
                foreach ($parts as $part) {
                    if (str_starts_with($part, 'PIC Klien:')) {
                        $clientPic = trim(substr($part, strlen('PIC Klien:')));
                    } elseif (str_starts_with($part, 'PIC IPNET:')) {
                        $ipnetPic = trim(substr($part, strlen('PIC IPNET:')));
                    } else {
                        $notedParts[] = $part;
                    }
                }
                $notedOnly = implode(' | ', array_filter($notedParts));
            }

            if (!$ipnetPic) {
                $ipnetPic = $act->engineer->name ?? '-';
            }

            $description = $act->description ?? '-';
            if (preg_match('/^\[(.+?)\]\s*(.*)$/s', $description, $m)) {
                $description = $m[2] ?: $m[1];
            }

            $timeStr = $act->start_time ? \Carbon\Carbon::parse($act->start_time)->format('H:i') : '-';

            $sheet->setCellValue('A' . $rowNum, $idx + 1);
            $sheet->setCellValue('B' . $rowNum, $description);
            $sheet->setCellValue('C' . $rowNum, $act->activity_date ? $act->activity_date->format('d/m/Y') : '-');
            $sheet->setCellValue('D' . $rowNum, $timeStr);
            $sheet->setCellValue('E' . $rowNum, $clientPic ?: '-');
            $sheet->setCellValue('F' . $rowNum, $ipnetPic ?: '-');
            $sheet->setCellValue('G' . $rowNum, $notedOnly ?: '-');

            // Alignment
            $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Alternating fill
            if ($rowNum % 2 == 1) {
                $sheet->getStyle('A' . $rowNum . ':G' . $rowNum)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8FAFC');
            }

            $rowNum++;
        }

        // Border data rows
        if ($rowNum > 7) {
            $lastRow = $rowNum - 1;
            $sheet->getStyle('A7:G' . $lastRow)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                ->getColor()->setRGB('CBD5E1');
        }

        // Auto width for columns
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Mendapatkan daftar ID project yang terhubung dengan seorang engineer.
     * Terhubung via:
     * 1. Log aktivitas yang dibuat oleh engineer tersebut.
     * 2. Log aktivitas yang menyebut nama engineer di kolom notes (PIC IPNET / Nama).
     * 3. Jadwal Kerja (Schedule) di mana engineer ditugaskan (engineer_id atau pivot schedule_user).
     * 4. Task di mana engineer ditugaskan (engineer_id).
     */
    private function getLinkedProjectIds($user): \Illuminate\Support\Collection
    {
        $linkedProjectIds = collect();

        try {
            // 1. Log yang dibuat user
            $myLogProjectIds = EngineerActivityLog::where('user_id', $user->id)
                ->whereNotNull('project_id')
                ->pluck('project_id');
            $linkedProjectIds = $linkedProjectIds->merge($myLogProjectIds);

            // 2. Log yang menyebut nama user sebagai PIC (di kolom notes)
            $name = trim($user->name);
            $firstName = explode(' ', $name)[0] ?? '';
            $picLogQuery = EngineerActivityLog::whereNotNull('project_id')
                ->where(function ($q) use ($name, $firstName) {
                    $q->where('notes', 'like', "%{$name}%");
                    if (strlen($firstName) >= 4) {
                        $q->orWhere('notes', 'like', "%{$firstName}%");
                    }
                });
            $linkedProjectIds = $linkedProjectIds->merge($picLogQuery->pluck('project_id'));
        } catch (\Throwable $e) {}

        // 3. Schedule penugasan kerja
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('schedules')) {
                $schedProjectIds = \App\Models\Schedule::whereNotNull('project_id')
                    ->where(function ($sq) use ($user) {
                        $sq->where('engineer_id', $user->id);
                        if (\Illuminate\Support\Facades\Schema::hasTable('schedule_user')) {
                            $sq->orWhereHas('engineers', function ($engQ) use ($user) {
                                $engQ->where('users.id', $user->id);
                            });
                        }
                    })
                    ->pluck('project_id');
                $linkedProjectIds = $linkedProjectIds->merge($schedProjectIds);
            }
        } catch (\Throwable $e) {}

        // 4. Task penugasan
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('tasks')) {
                $taskProjectIds = \App\Models\Task::whereNotNull('project_id')
                    ->where(function ($tq) use ($user) {
                        $tq->where('engineer_id', $user->id);
                        if (\Illuminate\Support\Facades\Schema::hasTable('task_user')) {
                            $tq->orWhereHas('engineers', function ($engQ) use ($user) {
                                $engQ->where('users.id', $user->id);
                            });
                        }
                    })
                    ->pluck('project_id');
                $linkedProjectIds = $linkedProjectIds->merge($taskProjectIds);
            }
        } catch (\Throwable $e) {}

        // 5. Proyek yang dibuat oleh user
        try {
            $createdProjectIds = \App\Models\Project::where('created_by', $user->id)->pluck('id');
            $linkedProjectIds = $linkedProjectIds->merge($createdProjectIds);
        } catch (\Throwable $e) {}

        return $linkedProjectIds->unique()->filter()->values();
    }
}