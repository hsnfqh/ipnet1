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
use App\Models\ActivityDocumentSignature;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
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

        // Auto-cleanup: Pastikan task & schedule auto-generate "Implementasi Teknis" serta task Day Off / Meeting dibersihkan dari penugasan
        try {
            if ($hasTaskUser) {
                \Illuminate\Support\Facades\DB::statement("DELETE FROM task_user WHERE task_id IN (SELECT id FROM tasks WHERE title LIKE '%Implementasi Teknis%')");
            }
            \Illuminate\Support\Facades\DB::statement("DELETE FROM tasks WHERE title LIKE '%Implementasi Teknis%'");
            \Illuminate\Support\Facades\DB::statement("DELETE FROM schedules WHERE title LIKE '%Implementasi Teknis%'");

            // Hapus task yang merupakan Day Off, Cuti, Libur, Meeting, atau Rapat
            $unwantedTaskIds = Task::where(function($q) {
                $q->where('title', 'like', '%Day Off%')
                  ->orWhere('title', 'like', '%day off%')
                  ->orWhere('title', 'like', '%dayoff%')
                  ->orWhere('title', 'like', '%Cuti%')
                  ->orWhere('title', 'like', '%cuti%')
                  ->orWhere('title', 'like', '%Libur%')
                  ->orWhere('title', 'like', '%libur%')
                  ->orWhere('title', 'like', '%Meeting%')
                  ->orWhere('title', 'like', '%meeting%')
                  ->orWhere('title', 'like', '%Rapat%')
                  ->orWhere('title', 'like', '%rapat%')
                  ->orWhereHas('project', function($pq) {
                      $pq->whereIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti', 'CUTI', 'Cuti'])
                        ->orWhere('name', 'like', '%Day Off%')
                        ->orWhere('name', 'like', '%day off%')
                        ->orWhere('name', 'like', '%Cuti%')
                        ->orWhere('name', 'like', '%cuti%')
                        ->orWhere('name', 'like', '%Meeting%')
                        ->orWhere('name', 'like', '%meeting%');
                  });
            })->pluck('id');

            // Tambahkan juga task yang judulnya persis judul agenda schedule bertipe Day Off / Meeting
            $meetingScheduleTitles = Schedule::where(function($sq) {
                $sq->whereIn('category', ['Meeting', 'Day Off', 'Meeting Klien / Principal', 'Sesi PoC & Lab', 'PoC & Demo', 'Cuti'])
                   ->orWhere('category', 'like', '%Meeting%')
                   ->orWhere('category', 'like', '%meeting%')
                   ->orWhere('category', 'like', '%Day Off%')
                   ->orWhere('category', 'like', '%day off%')
                   ->orWhere('category', 'like', '%dayoff%')
                   ->orWhere('category', 'like', '%cuti%')
                   ->orWhere('category', 'like', '%libur%');
            })->pluck('title')->filter()->unique()->toArray();

            if (!empty($meetingScheduleTitles)) {
                $scheduleTaskIds = Task::whereIn('title', $meetingScheduleTitles)->pluck('id');
                $unwantedTaskIds = $unwantedTaskIds->concat($scheduleTaskIds)->unique();
            }

            if ($unwantedTaskIds->isNotEmpty()) {
                if ($hasTaskUser) {
                    \Illuminate\Support\Facades\DB::table('task_user')->whereIn('task_id', $unwantedTaskIds)->delete();
                }
                Task::whereIn('id', $unwantedTaskIds)->delete();
            }
        } catch (\Exception $e) {}

        // Filter tasks sesuai scope role yang login (secara tegas KECUALIKAN Day Off & Meeting)
        $withRelations = ['project', 'engineer'];
        if ($hasTaskUser) {
            $withRelations[] = 'engineers';
        }
        $tasksQuery = Task::with($withRelations)
            ->where('title', 'not like', '%Implementasi Teknis%')
            ->where('title', 'not like', '%Day Off%')
            ->where('title', 'not like', '%day off%')
            ->where('title', 'not like', '%dayoff%')
            ->where('title', 'not like', '%cuti%')
            ->where('title', 'not like', '%libur%')
            ->where('title', 'not like', '%meeting%')
            ->where('title', 'not like', '%rapat%')
            ->whereDoesntHave('project', function($pq) {
                $pq->whereIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti', 'CUTI', 'Cuti'])
                  ->orWhere('name', 'like', '%Day Off%')
                  ->orWhere('name', 'like', '%day off%')
                  ->orWhere('name', 'like', '%Cuti%')
                  ->orWhere('name', 'like', '%cuti%')
                  ->orWhere('name', 'like', '%Meeting%')
                  ->orWhere('name', 'like', '%meeting%');
            });
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
            $title = strtolower($t->title ?? '');
            $projectName = strtolower($t->project->name ?? '');
            if (str_contains($title, 'implementasi teknis')) return false;
            if (str_contains($title, 'day off') || str_contains($title, 'dayoff') || str_contains($title, 'cuti') || str_contains($title, 'libur')) return false;
            if (str_contains($title, 'meeting') || str_contains($title, 'rapat')) return false;
            if (str_contains($projectName, 'day off') || str_contains($projectName, 'cuti') || str_contains($projectName, 'meeting')) return false;
            return true;
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
                    $title = strtolower($t->title ?? '');
                    $projectName = strtolower($t->project->name ?? '');
                    if (str_contains($title, 'day off') || str_contains($title, 'dayoff') || str_contains($title, 'cuti') || str_contains($title, 'libur')) return false;
                    if (str_contains($title, 'meeting') || str_contains($title, 'rapat')) return false;
                    if (str_contains($projectName, 'day off') || str_contains($projectName, 'cuti') || str_contains($projectName, 'meeting')) return false;

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
            ->where('title', 'not like', '%Implementasi Teknis%')
            ->where('title', 'not like', '%Day Off%')
            ->where('title', 'not like', '%day off%')
            ->where('title', 'not like', '%dayoff%')
            ->where('title', 'not like', '%cuti%')
            ->where('title', 'not like', '%libur%')
            ->where('title', 'not like', '%meeting%')
            ->where('title', 'not like', '%rapat%')
            ->whereDoesntHave('project', function($pq) {
                $pq->whereIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti', 'CUTI', 'Cuti'])
                  ->orWhere('name', 'like', '%Day Off%')
                  ->orWhere('name', 'like', '%day off%')
                  ->orWhere('name', 'like', '%Cuti%')
                  ->orWhere('name', 'like', '%cuti%')
                  ->orWhere('name', 'like', '%Meeting%')
                  ->orWhere('name', 'like', '%meeting%');
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

        // Pastikan kolom category tersedia & data tersinkron
        EngineerActivityLog::ensureCategoryColumnExists();

        $authUser = auth()->user();
        $isLead   = \App\Helpers\ScopeHelper::isManagerial($authUser);
        $linkedProjectIds = $this->getLinkedProjectIds($authUser);
        $allowedCategories = $this->resolveAllowedActivityCategories($authUser);

        $query = EngineerActivityLog::with([
            'engineer',
            'project',
        ]);

        // Pembatasan hak akses kategori:
        // - PMO/PM: hanya 'project'
        // - Managed Service: 'managed_service' & 'help_desk'
        // - Lead Engineer: sesuai divisinya masing-masing (Network/Security -> project, Maintenance/MS -> ms & hd)
        // - Head: bisa melihat seluruh kategori (keduanya/semuanya)
        if ($request->filled('category') && in_array($request->category, $allowedCategories)) {
            $query->where('category', $request->category);
        } else {
            $query->where(function ($q) use ($allowedCategories) {
                $q->whereIn('category', $allowedCategories);
                if (in_array('project', $allowedCategories)) {
                    $q->orWhereNull('category');
                }
            });
        }

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

        // Hitung ringkasan metrics sesuai filter & hak akses
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

        $projectsQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti']);

        // Filter daftar project dropdown sesuai wewenang divisi
        if (in_array('project', $allowedCategories) && !in_array('managed_service', $allowedCategories)) {
            // Khusus Project / PMO
            $projectsQuery->where(function ($pq) {
                $pq->whereNull('handover_target')
                   ->orWhere('handover_target', '!=', 'managed_service')
                   ->orWhere('handover_target', 'both');
            });
        } elseif (in_array('managed_service', $allowedCategories) && !in_array('project', $allowedCategories)) {
            // Khusus Managed Service & Helpdesk
            $projectsQuery->where(function ($pq) {
                $pq->where('handover_target', 'managed_service')
                   ->orWhere('handover_target', 'both')
                   ->orWhereHas('managedServiceAssets');
            });
        }

        $projectFields = [
            'id', 'name', 'client', 'location', 'po_number',
            'customer_pic_technical', 'description', 'progress',
            'pm_id', 'created_by', 'division_id'
        ];

        $projectsQuery->with([
            'division:id,name',
            'pm:id,name,position,division_id',
            'pm.division:id,name',
            'tasks' => function ($tq) {
                $tq->select('id', 'project_id', 'engineer_id')
                   ->with(['engineer:id,name,position,division_id', 'engineer.division:id,name', 'engineers:id,name,position,division_id', 'engineers.division:id,name']);
            },
            'schedules' => function ($sq) {
                $sq->select('id', 'project_id', 'engineer_id')
                   ->with(['engineer:id,name,position,division_id', 'engineer.division:id,name', 'engineers:id,name,position,division_id', 'engineers.division:id,name']);
            }
        ]);

        if ($isLead) {
            $projects = $projectsQuery->orderBy('name')->get($projectFields);
        } else {
            $projects = $projectsQuery->where(function ($q) use ($authUser, $linkedProjectIds, $hasTaskUser, $hasScheduleUser) {
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
                ->get($projectFields);
        }

        $allEngineers = User::orderBy('name')
            ->with('division:id,name')
            ->get(['id', 'name', 'email', 'position', 'division_id']);

        $engineers = collect();
        if ($isLead) {
            $engineers = \App\Helpers\ScopeHelper::getAssignableEngineers($authUser);
            if ($engineers->isEmpty()) {
                $engineers = $allEngineers;
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
            'allEngineers',
            'activityTypes',
            'linkedProjectIds',
            'allowedCategories'
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

        $hasReportData = $request->filled('report_data');
        $hasActivities = $request->has('activities') && is_array($request->activities);

        // Mode 1: Form Laporan Aktivitas Resmi / Bulk Entry
        if ($hasReportData || $hasActivities) {
            $projectId = $request->project_id ?: null;

            // Validasi hak akses proyek untuk engineer non-lead
            if (!$isLead && $projectId) {
                $hasTaskUser = \Illuminate\Support\Facades\Schema::hasTable('task_user');
                $hasScheduleUser = \Illuminate\Support\Facades\Schema::hasTable('schedule_user');
                $linkedProjectIds = $this->getLinkedProjectIds($authUser);

                $hasAccess = $linkedProjectIds->contains($projectId)
                    || Project::where('id', $projectId)
                        ->where(function ($q) use ($authUser, $hasTaskUser, $hasScheduleUser) {
                            $q->where('created_by', $authUser->id)
                              ->orWhereHas('tasks', function ($tq) use ($authUser, $hasTaskUser) {
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
                              });
                        })->exists();

                if (!$hasAccess) {
                    return redirect()->back()->with('error', 'Anda tidak memiliki hak akses penugasan untuk mencatat aktivitas pada proyek ini.');
                }
            }

            $activityTitle = trim($request->activity_title ?? '');
            $itemsCreated  = 0;

            // Ekstrak data report_data jika ada
            $rawReport = null;
            if ($request->filled('report_data')) {
                $rawReport = is_string($request->report_data) ? json_decode($request->report_data, true) : $request->report_data;
            }

            // Kumpulkan list activities dari $request->activities atau dari $rawReport rincian aktivitas
            $activitiesList = $hasActivities ? $request->activities : [];
            $reportCat = is_array($rawReport) ? ($rawReport['category'] ?? ($rawReport['report_type'] ?? 'project')) : ($request->input('category') ?: 'project');

            // Validasi wewenang kategori user
            $allowedCategories = $this->resolveAllowedActivityCategories($authUser);
            if (!in_array($reportCat, $allowedCategories)) {
                return redirect()->back()->with('error', 'Akses ditolak: Anda tidak memiliki hak akses untuk mencatat aktivitas kategori ' . strtoupper(str_replace('_', ' ', $reportCat)) . '.');
            }

            if (empty($activitiesList) && is_array($rawReport)) {
                if ($reportCat === 'managed_service' && !empty($rawReport['ms_aktivitas'])) {
                    foreach ($rawReport['ms_aktivitas'] as $ra) {
                        if (empty(trim($ra['aktivitas'] ?? ''))) continue;
                        $activitiesList[] = [
                            'subject'       => $ra['aktivitas'],
                            'activity_date' => $rawReport['ms_identitas']['tanggal'] ?? date('Y-m-d'),
                            'time_str'      => $ra['waktu'] ?? '',
                            'client_pic'    => $rawReport['ms_identitas']['customer'] ?? '',
                            'ipnet_pic'     => $rawReport['ms_identitas']['engineer_pic'] ?? '',
                            'notes'         => (!empty($ra['ticket_alarm']) ? "Ticket/Alarm: {$ra['ticket_alarm']} | " : '') . (!empty($ra['follow_up']) ? "Follow-up: {$ra['follow_up']} | " : '') . ($ra['hasil'] ?? ''),
                            'activity_type' => 'Maintenance',
                            'status'        => !empty($ra['status']) ? $ra['status'] : 'Selesai',
                        ];
                    }
                } elseif ($reportCat === 'help_desk' && !empty($rawReport['hd_aktivitas'])) {
                    foreach ($rawReport['hd_aktivitas'] as $ra) {
                        if (empty(trim($ra['aktivitas'] ?? ''))) continue;
                        $activitiesList[] = [
                            'subject'       => $ra['aktivitas'],
                            'activity_date' => $rawReport['hd_identitas']['tanggal'] ?? date('Y-m-d'),
                            'time_str'      => $ra['waktu'] ?? '',
                            'client_pic'    => $rawReport['hd_identitas']['customer_service'] ?? '',
                            'ipnet_pic'     => $rawReport['hd_identitas']['nama_engineer'] ?? '',
                            'notes'         => (!empty($ra['ticket_wo']) ? "Ticket/WO: {$ra['ticket_wo']} | " : '') . ($ra['hasil'] ?? ''),
                            'activity_type' => 'Field Support',
                            'status'        => !empty($ra['status']) ? $ra['status'] : 'Selesai',
                        ];
                    }
                } elseif (!empty($rawReport['rincian_aktivitas'])) {
                    foreach ($rawReport['rincian_aktivitas'] as $ra) {
                        if (empty(trim($ra['aktivitas'] ?? ''))) continue;
                        $activitiesList[] = [
                            'subject'       => $ra['aktivitas'],
                            'activity_date' => $rawReport['identitas']['hari_tanggal_raw'] ?? ($rawReport['identitas']['hari_tanggal'] ?? date('Y-m-d')),
                            'time_str'      => $ra['waktu'] ?? '',
                            'client_pic'    => $rawReport['identitas']['pic_customer'] ?? '',
                            'ipnet_pic'     => $rawReport['identitas']['nama_engineer'] ?? '',
                            'notes'         => !empty($ra['tindak_lanjut']) && $ra['tindak_lanjut'] !== '-' ? $ra['tindak_lanjut'] : ($ra['hasil'] ?? ''),
                            'activity_type' => !empty($rawReport['identitas']['kategori_managed_service']) ? 'Maintenance' : 'Troubleshooting',
                            'status'        => !empty($ra['status']) ? $ra['status'] : 'Selesai',
                        ];
                    }
                }
            }

            foreach ($activitiesList as $row) {
                $subject = trim($row['subject'] ?? '');
                if (empty($subject)) {
                    continue;
                }

                $rawDate = !empty($row['activity_date']) ? $row['activity_date'] : date('Y-m-d');
                try {
                    $date = \Carbon\Carbon::parse($rawDate)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $date = date('Y-m-d');
                }

                $time      = !empty($row['time_str']) ? trim($row['time_str']) : null;
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
                $actType  = !empty($row['activity_type']) ? $row['activity_type'] : (!empty($request->activity_type) ? $request->activity_type : 'Troubleshooting');

                $description = $subject;
                if ($activityTitle !== '') {
                    $description = "[{$activityTitle}] " . $description;
                }

                EngineerActivityLog::create([
                    'user_id'       => auth()->id(),
                    'project_id'    => $projectId,
                    'category'      => $reportCat,
                    'activity_type' => $actType,
                    'description'   => $description,
                    'location'      => $location,
                    'activity_date' => $date,
                    'start_time'    => $time,
                    'end_time'      => null,
                    'status'        => !empty($row['status']) ? $row['status'] : 'Selesai',
                    'notes'         => $finalNotes ?: null,
                ]);

                $itemsCreated++;
            }

            // Jika belum ada teks di baris rincian aktivitas, buatkan 1 baris otomatis agar dokumen tersimpan & tidak mental
            if ($itemsCreated === 0 && !empty($rawReport)) {
                if ($reportCat === 'managed_service') {
                    $defaultSubject = $activityTitle ?: (!empty($rawReport['ms_identitas']['service_device']) ? 'Managed Service: ' . $rawReport['ms_identitas']['service_device'] : 'Laporan Aktivitas Managed Service');
                    $rawDate        = $rawReport['ms_identitas']['tanggal'] ?? date('Y-m-d');
                    $time           = $rawReport['ms_identitas']['jam_mulai'] ?? null;
                    $clientPic      = $rawReport['ms_identitas']['customer'] ?? '';
                    $ipnetPic       = $rawReport['ms_identitas']['engineer_pic'] ?? '';
                    $actType        = 'Maintenance';
                } elseif ($reportCat === 'help_desk') {
                    $defaultSubject = $activityTitle ?: (!empty($rawReport['hd_identitas']['customer_service']) ? 'Operasional Help Desk: ' . $rawReport['hd_identitas']['customer_service'] : 'Laporan Operasional Help Desk');
                    $rawDate        = $rawReport['hd_identitas']['tanggal'] ?? date('Y-m-d');
                    $time           = $rawReport['hd_identitas']['jam_shift'] ?? null;
                    $clientPic      = $rawReport['hd_identitas']['customer_service'] ?? '';
                    $ipnetPic       = $rawReport['hd_identitas']['nama_engineer'] ?? '';
                    $actType        = 'Field Support';
                } else {
                    $defaultSubject = $activityTitle ?: (!empty($rawReport['ruang_lingkup']['target_hari_ini']) ? $rawReport['ruang_lingkup']['target_hari_ini'] : 'Laporan Aktivitas Harian Project');
                    $rawDate        = $rawReport['identitas']['hari_tanggal_raw'] ?? ($rawReport['identitas']['hari_tanggal'] ?? date('Y-m-d'));
                    $time           = $rawReport['identitas']['jam_mulai'] ?? null;
                    $clientPic      = $rawReport['identitas']['pic_customer'] ?? '';
                    $ipnetPic       = $rawReport['identitas']['nama_engineer'] ?? '';
                    $actType        = !empty($rawReport['identitas']['kategori_managed_service']) ? 'Maintenance' : 'Troubleshooting';
                }

                try {
                    $date = \Carbon\Carbon::parse($rawDate)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $date = date('Y-m-d');
                }

                EngineerActivityLog::create([
                    'user_id'       => auth()->id(),
                    'project_id'    => $projectId,
                    'category'      => $reportCat,
                    'activity_type' => $actType,
                    'description'   => $defaultSubject,
                    'location'      => $clientPic ? "Client Site ({$clientPic})" : null,
                    'activity_date' => $date,
                    'start_time'    => $time,
                    'end_time'      => null,
                    'status'        => 'Selesai',
                    'notes'         => $clientPic ? "PIC Klien: {$clientPic}" : null,
                ]);

                $itemsCreated = 1;
            }

            if ($itemsCreated === 0) {
                return redirect()->back()->with('error', 'Silakan isi setidaknya satu baris aktivitas pekerjaan.');
            }

            // Simpan atau perbarui data formulir resmi (report_data) jika dikirim dari form
            if ($projectId && !empty($rawReport) && is_array($rawReport)) {
                \App\Models\ActivityDocumentSignature::ensureSchemaReady();
                try {
                    $scopeKey = 'proj_' . $projectId;
                    $sig = \App\Models\ActivityDocumentSignature::where('scope_key', $scopeKey)
                        ->orWhere('project_id', $projectId)
                        ->first();

                    if (!$sig) {
                        $sig = new \App\Models\ActivityDocumentSignature();
                        $sig->document_number = \App\Models\ActivityDocumentSignature::generateDocumentNumber();
                        $sig->scope_key       = $scopeKey;
                        $sig->project_id      = $projectId;
                        $sig->project_name    = \App\Models\Project::find($projectId)?->name ?? 'Project';
                        $sig->status          = 'draft';
                    } else {
                        // Pastikan scope_key konsisten
                        $sig->scope_key = $scopeKey;
                    }

                    $sig->report_data = $rawReport;
                    $sig->save();

                    // Otomatis ikat personel di tabel manpower ke project jika belum terikat
                    if (!empty($rawReport['manpower']) && is_array($rawReport['manpower'])) {
                        foreach ($rawReport['manpower'] as $mpRow) {
                            $mpName = trim($mpRow['nama'] ?? '');
                            if ($mpName !== '') {
                                $targetEng = User::where('name', 'like', "%{$mpName}%")->first();
                                if ($targetEng) {
                                    $hasTask = Task::where('project_id', $projectId)
                                        ->where('engineer_id', $targetEng->id)
                                        ->exists();
                                    if (!$hasTask) {
                                        Task::create([
                                            'title'       => 'Pelaksanaan Teknis: ' . ($sig->project_name ?? 'Proyek'),
                                            'project_id'  => $projectId,
                                            'engineer_id' => $targetEng->id,
                                            'priority'    => 'Medium',
                                            'status'      => 'In Progress',
                                            'start_date'  => date('Y-m-d'),
                                            'created_by'  => $authUser->id,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Save signature report error: ' . $e->getMessage());
                }
            }

            return redirect()->back()->with('success', "Berhasil menyimpan {$itemsCreated} aktivitas dan formulir laporan resmi!");
        }

        // Mode 2: Single Activity Form
        $request->validate([
            'description'   => 'required|string|max:1000',
            'activity_date' => 'required|date',
            'activity_type' => 'required|string',
            'status'        => 'required|string',
        ]);

        $allowedCategories = $this->resolveAllowedActivityCategories($authUser);
        $singleCat = $request->input('category');
        if (!$singleCat || !in_array($singleCat, $allowedCategories)) {
            $singleCat = $allowedCategories[0] ?? 'project';
        }

        EngineerActivityLog::create([
            'user_id'       => auth()->id(),
            'project_id'    => $request->project_id ?: null,
            'category'      => $singleCat,
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
        $user = auth()->user();

        // Hapus seluruh aktivitas pada grup proyek ini jika parameter delete_group dikirim
        if (request()->has('delete_group') && $log->project_id) {
            $isGroupContributor = EngineerActivityLog::where('project_id', $log->project_id)
                ->where('user_id', $user->id)
                ->exists();

            if (!$isGroupContributor) {
                if (request()->wantsJson() || request()->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk menghapus log aktivitas ini karena bukan pembuat atau kontributor kegiatan.'], 403);
                }
                abort(403, 'Anda tidak memiliki hak akses untuk menghapus log aktivitas ini karena bukan pembuat atau kontributor kegiatan.');
            }

            $groupQuery = EngineerActivityLog::where('project_id', $log->project_id);
            $count = $groupQuery->count();
            $groupQuery->delete();

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => true, 'message' => "{$count} catatan aktivitas berhasil dihapus."]);
            }
            return back()->with('success', "{$count} catatan aktivitas berhasil dihapus.");
        }

        // Hapus single row: hanya pembuat atau kontributor proyek
        $isContributor = ($log->user_id === $user->id)
            || ($log->project_id && EngineerActivityLog::where('project_id', $log->project_id)->where('user_id', $user->id)->exists());

        if (!$isContributor) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk menghapus agenda ini. Hanya pembuat yang berhak menghapusnya.'], 403);
            }
            abort(403, 'Anda tidak memiliki hak akses untuk menghapus agenda ini. Hanya pembuat yang berhak menghapusnya.');
        }

        $log->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Catatan aktivitas berhasil dihapus.']);
        }

        return back()->with('success', 'Catatan aktivitas berhasil dihapus.');
    }

    // ─── Engineer Activity Log: Update (Edit) ──────────────────────────────────
    public function updateActivityLog(Request $request, EngineerActivityLog $log)
    {
        $user = auth()->user();

        // Hak akses edit hanya untuk pembuat log atau kontributor kegiatan pada proyek ini
        $isContributor = ($log->user_id === $user->id)
            || ($log->project_id && EngineerActivityLog::where('project_id', $log->project_id)->where('user_id', $user->id)->exists());

        if (!$isContributor) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengubah log aktivitas ini. Hanya pembuat yang berhak mengubahnya.'], 403);
            }
            abort(403, 'Anda tidak memiliki hak akses untuk mengubah log aktivitas ini. Hanya pembuat yang berhak mengubahnya.');
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
        @ini_set('memory_limit', '512M');
        @set_time_limit(120);

        $authUser = auth()->user();
        $isLead   = \App\Helpers\ScopeHelper::isManagerial($authUser);
        $linkedProjectIds = $this->getLinkedProjectIds($authUser);

        $query = EngineerActivityLog::with(['engineer', 'project']);

        // 1. Jika log_ids spesifik dikirim dari modal detail
        if ($request->filled('log_ids')) {
            $logIds = array_filter(array_map('trim', explode(',', $request->log_ids)));
            if (!empty($logIds)) {
                $query->whereIn('id', $logIds);
            }
        } elseif ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        } elseif ($request->filled('scope_key') && str_starts_with($request->scope_key, 'proj_')) {
            $pId = (int) substr($request->scope_key, 5);
            $query->where('project_id', $pId);
        } else {
            // Filter kepemilikan global jika export dari tabel utama
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

            if ($request->filled('date')) {
                $query->whereDate('activity_date', $request->date);
            }
        }

        $activities = $query->orderBy('activity_date', 'asc')->orderBy('start_time', 'asc')->orderBy('created_at', 'asc')->get();

        // Fallback resolve Project & Scope Key
        $resolvedProject = null;
        if ($request->filled('project_id')) {
            $resolvedProject = \App\Models\Project::find($request->project_id);
        } elseif ($request->filled('scope_key') && str_starts_with($request->scope_key, 'proj_')) {
            $resolvedProject = \App\Models\Project::find((int) substr($request->scope_key, 5));
        } elseif ($activities->isNotEmpty() && $activities->first()->project) {
            $resolvedProject = $activities->first()->project;
        }

        $scopeKey = $request->scope_key ?: ($resolvedProject ? ('proj_' . $resolvedProject->id) : ($activities->first()?->project_id ? ('proj_' . $activities->first()->project_id) : ('no_proj_' . ($activities->first()?->user_id ?? $authUser->id))));

        // Ambil tanda tangan & report_data resmi
        $documentSignature = null;
        if ($scopeKey) {
            try {
                $documentSignature = ActivityDocumentSignature::where('scope_key', $scopeKey)->first();
            } catch (\Throwable $e) {}
        }

        // Jika report_data dikirim via request (POST/GET), prioritaskan
        if ($request->filled('report_data')) {
            $rawReport = is_string($request->report_data) ? json_decode($request->report_data, true) : $request->report_data;
            if (is_array($rawReport)) {
                if (!$documentSignature) {
                    $documentSignature = new ActivityDocumentSignature([
                        'scope_key'       => $scopeKey,
                        'project_id'      => $resolvedProject?->id,
                        'project_name'    => $resolvedProject?->name ?? 'Project',
                        'document_number' => ActivityDocumentSignature::generateDocumentNumber(),
                        'status'          => 'draft'
                    ]);
                }
                $documentSignature->report_data = $rawReport;
            }
        }

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
                $ipnetPic = '-';
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
        $projectName  = $request->project_name ?: ($documentSignature?->project_name ?: ($resolvedProject?->name ?: ($activities->first()?->project?->name ?? 'Aktivitas Engineer')));
        $engineerName = $activities->first()?->engineer?->name ?? ($documentSignature?->pic_name ?? $authUser->name);
        $printedBy    = $authUser->name;

        // Base64 Logo untuk stabilitas render DomPDF
        $logoPath   = public_path('images/ipnet1.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('images/ipnet.png');
        }
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

        $verifyDocNumber = $documentSignature?->document_number ?? ('IPNET-ACT-' . date('Ym') . '-DRAFT');
        $verifyUrl       = url('/verify-document/' . $verifyDocNumber);
        $qrData          = $this->generateQrData($verifyUrl);

        $reportCategory = $request->get('category') ?? ($documentSignature?->report_data['category'] ?? ($documentSignature?->report_data['report_type'] ?? ($activities->first()?->category ?? 'project')));

        if (!$this->checkCanAccessCategory($authUser, $reportCategory)) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk melihat atau mengunduh laporan kategori ' . strtoupper(str_replace('_', ' ', $reportCategory)) . '.');
        }

        $pdf = Pdf::loadView('exports.engineer-activity-report-pdf', [
            'reportCategory'    => $reportCategory,
            'parsedActivities'  => $parsedActivities->values()->toArray(),
            'activities'        => $activities,
            'projectName'       => $projectName,
            'engineerName'      => $engineerName,
            'printedBy'         => $printedBy,
            'logoBase64'        => $logoBase64,
            'documentSignature' => $documentSignature,
            'verifyDocNumber'   => $verifyDocNumber,
            'verifyUrl'         => $verifyUrl,
            'qrPngBase64'       => $qrData['qrPngBase64'],
            'qrRawSvg'          => $qrData['qrRawSvg'],
            'qrApiUrl'          => $qrData['qrApiUrl'],
            'qrCodeBase64'      => $qrData['qrPngBase64'] ?: $qrData['qrApiUrl'],
            'qrSvgBase64'       => $qrData['qrPngBase64'] ?: $qrData['qrApiUrl'],
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);

        $safeName = \Illuminate\Support\Str::slug($projectName, '_') ?: 'Dokumen';
        $filename = "Laporan_Aktivitas_{$safeName}_" . now()->format('Ymd_His') . ".pdf";

        if ($request->get('action') === 'stream' || $request->has('stream')) {
            return response($pdf->output(), 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]);
        }

        return response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'private, no-transform, no-store, must-revalidate',
        ]);
    }

    /**
     * Tampilkan Langsung PDF Hasil Scan QR Code di Browser / HP (Tanpa Halaman Antara / Embel-embel Unduh)
     */
    public function streamVerifiedPdf(string $documentNumber)
    {
        $documentSignature = ActivityDocumentSignature::where('document_number', $documentNumber)->first();

        $query = \App\Models\EngineerActivityLog::with(['engineer', 'project']);

        if ($documentSignature) {
            if (!empty($documentSignature->log_ids)) {
                $query->whereIn('id', $documentSignature->log_ids);
            } elseif ($documentSignature->project_id) {
                $query->where('project_id', $documentSignature->project_id);
            } elseif ($documentSignature->scope_key && str_starts_with($documentSignature->scope_key, 'proj_')) {
                $pId = (int) substr($documentSignature->scope_key, 5);
                $query->where('project_id', $pId);
            } elseif ($documentSignature->pic_user_id) {
                $query->where('user_id', $documentSignature->pic_user_id);
            }
        }

        $activities = $query->orderBy('activity_date', 'asc')->orderBy('start_time', 'asc')->orderBy('created_at', 'asc')->get();

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
                $ipnetPic = '-';
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

        $projectName  = $documentSignature?->project_name ?: ($activities->first()?->project?->name ?? 'Aktivitas Lapangan');
        $engineerName = $documentSignature?->pic_name ?: ($activities->first()?->engineer?->name ?? 'PIC Engineer');
        $printedBy    = $documentSignature?->head_name ?: 'System Verification';

        $logoPath   = public_path('images/ipnet1.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('images/ipnet.png');
        }
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

        $verifyUrl = url('/verify-document/' . $documentNumber);
        $qrData    = $this->generateQrData($verifyUrl);

        $pdf = Pdf::loadView('exports.engineer-activity-report-pdf', [
            'reportCategory'    => $documentSignature?->report_data['category'] ?? ($documentSignature?->report_data['report_type'] ?? null),
            'parsedActivities'  => $parsedActivities,
            'activities'        => $activities,
            'projectName'       => $projectName,
            'engineerName'      => $engineerName,
            'printedBy'         => $printedBy,
            'logoBase64'        => $logoBase64,
            'documentSignature' => $documentSignature,
            'verifyDocNumber'   => $documentNumber,
            'verifyUrl'         => $verifyUrl,
            'qrPngBase64'       => $qrData['qrPngBase64'],
            'qrRawSvg'          => $qrData['qrRawSvg'],
            'qrApiUrl'          => $qrData['qrApiUrl'],
            'qrCodeBase64'      => $qrData['qrPngBase64'] ?: $qrData['qrApiUrl'],
            'qrSvgBase64'       => $qrData['qrPngBase64'] ?: $qrData['qrApiUrl'],
        ]);

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);

        return response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Laporan_Aktivitas_' . $documentNumber . '.pdf"',
        ]);
    }

    // ─── Engineer Activity Log: Export Excel ─────────────────────────────────────
    public function exportActivityLogExcel(\Illuminate\Http\Request $request)
    {
        $authUser = auth()->user();
        $isLead   = \App\Helpers\ScopeHelper::isManagerial($authUser);
        $linkedProjectIds = $this->getLinkedProjectIds($authUser);

        $allowedCategories = $this->resolveAllowedActivityCategories($authUser);

        $query = EngineerActivityLog::with(['engineer', 'project']);

        if ($request->filled('category') && in_array($request->category, $allowedCategories)) {
            $query->where('category', $request->category);
        } else {
            $query->where(function ($q) use ($allowedCategories) {
                $q->whereIn('category', $allowedCategories);
                if (in_array('project', $allowedCategories)) {
                    $q->orWhereNull('category');
                }
            });
        }

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
        $filename     = "Laporan_Kronologi_{$safeName}_" . now()->format('Ymd_His') . ".xlsx";

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kronologis Aktivitas');
        $sheet->setShowGridLines(true);

        // Page setup: A4 Landscape, Fit to 1 page wide
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        // Tentukan Nama Client untuk Sub-header PIC
        $firstProject = $activities->first()?->project;
        $clientName   = trim($firstProject?->client ?? '');

        $clientHeader = 'Klien';
        if (!empty($clientName) && !in_array(strtolower($clientName), ['internal', 'internal / umum', 'umum', '-'])) {
            if (str_contains(strtolower($clientName), 'angkasa pura')) {
                $clientHeader = 'APS';
            } elseif (str_contains(strtolower($clientName), 'bri')) {
                $clientHeader = 'BRI';
            } elseif (strlen($clientName) <= 12) {
                $clientHeader = strtoupper($clientName);
            } else {
                $words = explode(' ', $clientName);
                $clientHeader = (count($words) <= 2) ? strtoupper($clientName) : strtoupper($words[0] . ' ' . ($words[1] ?? ''));
            }
        } else {
            if (str_contains(strtolower($projectName), 'bri')) {
                $clientHeader = 'BRI';
            } elseif (str_contains(strtolower($projectName), 'bca')) {
                $clientHeader = 'BCA';
            } elseif (str_contains(strtolower($projectName), 'angkasa pura')) {
                $clientHeader = 'APS';
            }
        }

        // 1. Spacing Atas
        $sheet->getRowDimension(1)->setRowHeight(12);

        // 2. Judul Laporan Terpusat (Sesuai Referensi Gambar)
        $titleText = 'Laporan Kronologi ' . $projectName;
        if (str_starts_with(strtolower($projectName), 'laporan kronologi')) {
            $titleText = $projectName;
        }

        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', $titleText);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(26);

        // 3. Spacing Sebelum Header Tabel
        $sheet->getRowDimension(3)->setRowHeight(8);

        // 4. Struktur Dua Baris Header Kolom (Two-Tier Header persis referensi)
        $sheet->mergeCells('A4:A5');
        $sheet->setCellValue('A4', 'No');

        $sheet->mergeCells('B4:B5');
        $sheet->setCellValue('B4', 'Kronologis Agenda / Aktivitas');

        $sheet->mergeCells('C4:D4');
        $sheet->setCellValue('C4', 'PIC');

        $sheet->setCellValue('C5', $clientHeader);
        $sheet->setCellValue('D5', 'IPNET');

        $sheet->mergeCells('E4:E5');
        $sheet->setCellValue('E4', 'Tanggal Laporan');

        $sheet->mergeCells('F4:F5');
        $sheet->setCellValue('F4', 'Tanggal Eksekusi');

        $sheet->mergeCells('G4:G5');
        $sheet->setCellValue('G4', 'Waktu');

        $sheet->mergeCells('H4:H5');
        $sheet->setCellValue('H4', 'Catatan Aksi');

        // Setting Lebar Kolom
        $columnWidths = [
            'A' => 6,   // No
            'B' => 45,  // Kronologis Agenda / Aktivitas
            'C' => 16,  // PIC Klien
            'D' => 18,  // PIC IPNET
            'E' => 16,  // Tanggal Laporan
            'F' => 16,  // Tanggal Eksekusi
            'G' => 14,  // Waktu
            'H' => 42,  // Catatan Aksi
        ];

        foreach ($columnWidths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // Styling Header Tabel (Deep Corporate Blue #004C87 / #0B5394)
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '004C87'],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
            ],
        ];
        $sheet->getStyle('A4:H5')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(22);
        $sheet->getRowDimension(5)->setRowHeight(20);

        // Freeze Panes di bawah header tabel
        $sheet->freezePane('A6');

        // 5. Pengisian Baris Data
        $rowNum = 6;

        if ($activities->isEmpty()) {
            $sheet->mergeCells("A6:H6");
            $sheet->setCellValue("A6", "Belum ada catatan agenda atau aktivitas kronologis pada proyek ini.");
            $sheet->getStyle("A6:H6")->applyFromArray([
                'font' => ['italic' => true, 'color' => ['rgb' => '64748B'], 'size' => 10],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D0D7DE']]],
            ]);
            $sheet->getRowDimension(6)->setRowHeight(32);
            $rowNum = 7;
        } else {
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

                $description = $act->description ?? '-';
                if (preg_match('/^\[(.+?)\]\s*(.*)$/s', $description, $m)) {
                    $description = $m[2] ?: $m[1];
                }

                // Format Tanggal (j-M-y => e.g. 8-Sep-26 persis contoh gambar)
                $rawDate = $act->activity_date;
                $actDateStr = $rawDate ? $rawDate->format('j-M-y') : '-';

                $hasClient = ($clientPic !== '' && $clientPic !== '-');
                $hasIpnet  = ($ipnetPic !== '' && $ipnetPic !== '-');

                // Pemilahan Tanggal Laporan vs Tanggal Eksekusi sesuai konteks
                if ($hasClient && !$hasIpnet) {
                    $tglLaporan  = $actDateStr;
                    $tglEksekusi = '-';
                } elseif ($hasIpnet && !$hasClient) {
                    $tglLaporan  = '-';
                    $tglEksekusi = $actDateStr;
                } else {
                    $descLower = strtolower($description);
                    if (str_contains($descLower, 'menerima laporan') || str_contains($descLower, 'laporan dari') || str_contains($descLower, 'koordinasi')) {
                        $tglLaporan  = $actDateStr;
                        $tglEksekusi = '-';
                    } else {
                        $tglLaporan  = '-';
                        $tglEksekusi = $actDateStr;
                    }
                }

                // Format Waktu: 22.41 WIB
                if ($act->start_time) {
                    $timeStr = \Carbon\Carbon::parse($act->start_time)->format('H.i') . ' WIB';
                } else {
                    $timeStr = '-';
                }

                $catatanAksi = $notedOnly !== '' ? $notedOnly : '-';

                $sheet->setCellValue('A' . $rowNum, $idx + 1);
                $sheet->setCellValue('B' . $rowNum, $description);
                $sheet->setCellValue('C' . $rowNum, $clientPic ?: '-');
                $sheet->setCellValue('D' . $rowNum, $ipnetPic ?: '-');
                $sheet->setCellValue('E' . $rowNum, $tglLaporan);
                $sheet->setCellValue('F' . $rowNum, $tglEksekusi);
                $sheet->setCellValue('G' . $rowNum, $timeStr);
                $sheet->setCellValue('H' . $rowNum, $catatanAksi);

                // Alignments
                $sheet->getStyle('A' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);
                $sheet->getStyle('C' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('G' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H' . $rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setWrapText(true);

                $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                // Latar putih bersih dengan border sel abu-abu tipis
                $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFFFFF');

                $sheet->getStyle('A' . $rowNum . ':H' . $rowNum)->getBorders()->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setRGB('D0D7DE');

                $sheet->getRowDimension($rowNum)->setRowHeight(25);

                $rowNum++;
            }
        }

        // Garis batas luar tabel (Medium Border Biru Tua)
        $lastRow = max(6, $rowNum - 1);
        $sheet->getStyle('A4:H' . $lastRow)->getBorders()->getOutline()
            ->setBorderStyle(Border::BORDER_MEDIUM)
            ->getColor()->setRGB('004C87');

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

    /**
     * Generate multi-tier QR Code formats secara 100% lokal tanpa dependensi request eksternal
     * Menggunakan SimpleSoftwareIO\QrCode SVG format (native PHP, zero network latency)
     */
    protected function generateQrData(string $text): array
    {
        $qrSvgBase64 = '';
        $qrRawSvg    = '';

        try {
            $svg = (string) \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(100)
                ->margin(1)
                ->errorCorrection('M')
                ->generate($text);

            $qrRawSvg    = preg_replace('/<\?xml.*?\?>/i', '', $svg);
            $qrSvgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svg);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('QR generation error: ' . $e->getMessage());
        }

        return [
            'qrPngBase64'  => $qrSvgBase64,
            'qrRawSvg'     => $qrRawSvg,
            'qrApiUrl'     => '',
            'qrCodeBase64' => $qrSvgBase64,
            'qrSvgBase64'  => $qrSvgBase64,
        ];
    }

    /**
     * Resolusi kategori aktivitas engineer yang diizinkan untuk user secara aman (resilient terhadap OPcache lag).
     */
    protected function resolveAllowedActivityCategories($user): array
    {
        if (!$user) return [];
        if (method_exists(\App\Helpers\ScopeHelper::class, 'getAllowedActivityCategories')) {
            return \App\Helpers\ScopeHelper::getAllowedActivityCategories($user);
        }

        // Fallback aman jika class ScopeHelper di OPcache hosting belum mereload method baru
        if (\App\Helpers\ScopeHelper::isExecutive($user) || \App\Helpers\ScopeHelper::isGroupLeader($user) || \App\Helpers\ScopeHelper::isTeamLeader($user) || $user->hasAnyRole(['Super Admin', 'Superadmin', 'Admin', 'Lead Engineer', 'Team Leader Engineering', 'Team Leader', 'Lead Divisi', 'Lead Maintenance'])) {
            return ['project', 'managed_service', 'help_desk'];
        }

        $cats = [];
        if (\App\Helpers\ScopeHelper::isPmo($user)) {
            $cats[] = 'project';
        }
        if (\App\Helpers\ScopeHelper::isMaintenance($user)) {
            $cats[] = 'managed_service';
            $cats[] = 'help_desk';
        }
        if (empty($cats)) {
            $cats[] = 'project';
        }
        return array_values(array_unique($cats));
    }

    /**
     * Cek apakah user boleh mengakses kategori tertentu secara aman.
     */
    protected function checkCanAccessCategory($user, ?string $category): bool
    {
        if (!$category) return true;
        if (method_exists(\App\Helpers\ScopeHelper::class, 'canAccessActivityCategory')) {
            return \App\Helpers\ScopeHelper::canAccessActivityCategory($user, $category);
        }
        $allowed = $this->resolveAllowedActivityCategories($user);
        return in_array($category, $allowed);
    }
}