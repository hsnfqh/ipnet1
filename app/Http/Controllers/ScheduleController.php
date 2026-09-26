<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Schedule;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Http\Requests\ScheduleRequest;
use App\Services\ScheduleExportService;
use App\Helpers\ScopeHelper;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the schedules.
     */
    public function index()
    {
        $user         = auth()->user();
        $isSusanto    = str_contains(strtolower($user->name ?? ''), 'susanto') || $user->hasAnyRole(['Division Head', 'Head Divisi', 'Group Leader', 'HD / Direktur', 'Group Leader Delivery & Operation', 'Group Leader Commercial & Solution']);
        $isHariyadi   = str_contains(strtolower($user->name ?? ''), 'hariyadi') || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur']);
        $isExecutive  = $isSusanto || $isHariyadi || ScopeHelper::isExecutive($user) || ScopeHelper::isGroupLeader($user);
        $isArchitect  = $user->hasAnyRole(['Solution Architect', 'Solutions Architect', 'SA']);
        $isCommercial = $isExecutive || $user->hasAnyRole(['Sales', 'Account Manager', 'BusDev', 'BDM', 'Business Development', 'CRO', 'Customer Relation Officer']);
        $isLead       = (ScopeHelper::isManagerial($user) || $isCommercial) && !$isArchitect;
        $canManageSchedule = !$isExecutive && (ScopeHelper::canManageSchedules($user) || $isArchitect);
        $scopeIds     = $isExecutive ? null : ScopeHelper::getScopeUserIds($user);
        $hasScheduleUser = Schema::hasTable('schedule_user');

        $commercialRoles = [
            'Presales', 'Pre-Sales',
            'Sales', 'Account Manager',
            'BDM', 'BusDev', 'Business Development',
            'PMO', 'Project Manager',
            'Solution Architect', 'Solutions Architect', 'SA', 'Tech Develop',
            'CRO', 'Customer Relation Officer'
        ];

        $validSalesNames = [
            'Raiza', 'Nabylla Berlianita', 'Akbar', 'Aris', 'Kurnijanto Edy',
            'Novan Pudjirachmanto', 'M. Kipsriyanto', 'Armen Yuldi', 'Hasan'
        ];

        $executiveTeamUsers = User::where(function($q) use ($commercialRoles) {
            $q->whereHas('roles', fn($r) => $r->whereIn('name', $commercialRoles))
              ->orWhereIn('name', ['Raiza', 'Nabylla Berlianita', 'Akbar', 'Aris', 'Kurnijanto Edy', 'Novan Pudjirachmanto', 'M. Kipsriyanto', 'Armen Yuldi', 'Hasan'])
              ->orWhere('position', 'like', '%Sales%')
              ->orWhere('position', 'like', '%Presales%')
              ->orWhere('position', 'like', '%Business Development%')
              ->orWhere('position', 'like', '%BDM%')
              ->orWhere('position', 'like', '%PMO%')
              ->orWhere('position', 'like', '%Project Manager%')
              ->orWhere('position', 'like', '%Solution Architect%');
        })
        ->whereDoesntHave('roles', function($r) {
            $r->whereIn('name', ['Engineer', 'Field Engineer', 'Lead Maintenance', 'Maintenance', 'Lead Engineer', 'Network Engineer', 'Security Engineer', 'Managed Service', 'Field Support']);
        })
        ->whereNotIn('name', [
            'Eka Kurnia', 'Ardiansyah', 'Dafa Rizqullah', 'Rorik', 'Helmi Shiamsyah', 'Doris', 'Mario', 'Eris',
            'Ignatius Rizky', 'Syaiful Amin', 'Raihan Ghiffary', 'Panca Pangga Ramadhan', 'Dedy Suryana', 'Nugraha Pratama',
            'Shiamsyah Azis', 'Agus Prasetyo'
        ])
        ->orderBy('name')
        ->get();
        $executiveTeamUserIds = $executiveTeamUsers->pluck('id')->toArray();

        // Auto-heal / Sinkronkan Task ke Jadwal Kerja sesuai scope divisi user
        if (!$isArchitect && !$isExecutive) {
            // 1. Bersihkan Jadwal kategori Task/Kegiatan yang task induknya sudah dihapus / sudah di-rename, serta bersihkan task jika jadwal berkategori Meeting / Day Off
            try {
                $meetingScheduleTitles = Schedule::where(function($q) {
                    $q->whereIn('category', ['Meeting', 'Day Off', 'Meeting Klien / Principal', 'Sesi PoC & Lab', 'PoC & Demo'])
                      ->orWhere('category', 'like', '%Meeting%')
                      ->orWhere('category', 'like', '%meeting%');
                })->pluck('title')->filter()->unique()->toArray();

                if (!empty($meetingScheduleTitles)) {
                    Task::whereIn('title', $meetingScheduleTitles)->delete();
                }

                $existingTaskTitles = Task::pluck('title')->toArray();
                Schedule::where(function($q) {
                    $q->whereIn('category', ['Task', 'Kegiatan'])
                      ->orWhere(function($sq) {
                          $sq->where('category', 'Preventive Maintenance')
                             ->where('title', 'regexp', '^\\[(PM|CM|INC|REQ|CR|TCK)-');
                      });
                })
                ->whereNotIn('title', $existingTaskTitles)
                ->delete();
            } catch (\Exception $e) {
                // Abaikan jika regex tidak disupport driver DB tertentu
                try {
                    $existingTaskTitles = Task::pluck('title')->toArray();
                    Schedule::whereIn('category', ['Task', 'Kegiatan'])
                        ->whereNotIn('title', $existingTaskTitles)
                        ->delete();
                } catch (\Exception $ex) {}
            }

            $taskQuery = Task::with('engineers')->whereNotNull('deadline');
            if (!empty($meetingScheduleTitles)) {
                $taskQuery->whereNotIn('title', $meetingScheduleTitles);
            }
            if ($scopeIds !== null) {
                $taskQuery->where(function($q) use ($scopeIds) {
                    $q->whereIn('engineer_id', $scopeIds)
                      ->orWhereHas('engineers', fn($sq) => $sq->whereIn('users.id', $scopeIds));
                });
            }
            $allTasks = $taskQuery->get();
            foreach ($allTasks as $matchingTask) {
                $taskStartDate = $matchingTask->start_date ? $matchingTask->start_date->format('Y-m-d') : $matchingTask->deadline->format('Y-m-d');
                $taskEndDate   = $matchingTask->deadline->format('Y-m-d');
                $taskTime = $matchingTask->deadline_time 
                    ? substr($matchingTask->deadline_time, 0, 5) 
                    : ($matchingTask->deadline->format('H:i') !== '00:00' ? $matchingTask->deadline->format('H:i') : '09:00');

                $curDate = \Carbon\Carbon::parse($taskStartDate);
                $endDate = \Carbon\Carbon::parse($taskEndDate);
                if ($curDate->gt($endDate)) {
                    $temp = $curDate;
                    $curDate = $endDate;
                    $endDate = $temp;
                }

                $schedCategory = preg_match('/^\[(PM|CM|INC|REQ|CR|TCK)-[0-9\-]+\]/', $matchingTask->title) 
                    ? 'Preventive Maintenance' 
                    : 'Task';

                $engIds = [];
                if ($hasScheduleUser) {
                    $engIds = $matchingTask->engineers->pluck('id')->toArray();
                    if (empty($engIds) && $matchingTask->engineer_id) {
                        $engIds = [$matchingTask->engineer_id];
                    }
                }

                while ($curDate->lte($endDate)) {
                    $curStr = $curDate->toDateString();
                    // Lewati hari Minggu jika tugas memiliki rentang multi-hari
                    if ($taskStartDate === $taskEndDate || !$curDate->isSunday()) {
                        $existingSched = Schedule::where('title', $matchingTask->title)
                            ->whereDate('date', $curStr)
                            ->first();

                        if ($existingSched) {
                            if ($hasScheduleUser && !empty($engIds) && $existingSched->engineers()->count() === 0) {
                                $existingSched->engineers()->sync($engIds);
                            }
                        } else {
                            $sched = Schedule::create([
                                'title'       => $matchingTask->title,
                                'project_id'  => $matchingTask->project_id,
                                'date'        => $curStr,
                                'category'    => $schedCategory,
                                'engineer_id' => $matchingTask->engineer_id,
                                'start_time'  => $taskTime . ':00',
                                'end_time'    => date('H:i:s', strtotime($taskTime . ' +3 hours')),
                                'location'    => $matchingTask->project ? ($matchingTask->project->location ?? 'On-Site Client') : 'On-Site Client',
                                'description' => $matchingTask->description ?? ('Pengerjaan task: ' . $matchingTask->title),
                                'created_by'  => $matchingTask->created_by ?? $user->id,
                            ]);

                            if ($hasScheduleUser && !empty($engIds)) {
                                $sched->engineers()->sync($engIds);
                            }
                        }
                    }
                    $curDate->addDay();
                }
            }
        }

        // Auto-deduplikasi data ganda di database berdasarkan judul dan tanggal yang sama persis
        try {
            $duplicates = Schedule::select('title', 'date')
                ->whereNotNull('title')
                ->whereNotNull('date')
                ->groupBy('title', 'date')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($duplicates as $dup) {
                $dupDateStr = $dup->date instanceof \Carbon\Carbon ? $dup->date->format('Y-m-d') : substr($dup->date, 0, 10);
                $dupRecords = Schedule::where('title', $dup->title)
                    ->whereDate('date', $dupDateStr)
                    ->orderBy('id', 'asc')
                    ->get();

                if ($dupRecords->count() > 1) {
                    // Jika ada jadwal tiket berlabel Preventive Maintenance / Meeting, prioritaskan simpan jadwal tersebut
                    $pmMatch = $dupRecords->firstWhere('category', 'Preventive Maintenance') ?? $dupRecords->firstWhere('category', 'Meeting');
                    $keepId = $pmMatch ? $pmMatch->id : $dupRecords->first()->id;
                    $deleteIds = $dupRecords->where('id', '!=', $keepId)->pluck('id')->all();
                    Schedule::whereIn('id', $deleteIds)->delete();
                }
            }
        } catch (\Exception $e) {
            // Abaikan jika query grup gagal
        }

        $withRelations = ['project', 'engineer', 'creator'];
        if ($hasScheduleUser) {
            $withRelations[] = 'engineers';
        }

        $schedulesQuery = Schedule::with($withRelations);
        if ($isExecutive) {
            // Executive (Hariyadi & Susanto): Hanya terhubung ke jadwal Presales, Sales, BD/BDM, dan PMO
            // Putus total dari jadwal maintenance/troubleshooting lapangan yang melibatkan tim engineer delivery
            $deliveryEngineerIds = User::where(function($q) {
                $q->whereHas('roles', fn($r) => $r->whereIn('name', [
                    'Engineer', 'Field Engineer', 'Lead Maintenance', 'Maintenance', 'Lead Engineer', 
                    'Network Engineer', 'Security Engineer', 'Managed Service', 'Field Support', 'Field Support (EOS)'
                ]))
                ->orWhereIn('name', [
                    'Eka Kurnia', 'Ardiansyah', 'Dafa Rizqullah', 'Rorik', 'Helmi Shiamsyah', 'Doris', 'Mario', 'Eris',
                    'Ignatius Rizky', 'Syaiful Amin', 'Raihan Ghiffary', 'Panca Pangga Ramadhan', 'Dedy Suryana', 'Nugraha Pratama',
                    'Shiamsyah Azis', 'Agus Prasetyo'
                ]);
            })->pluck('id')->toArray();

            $schedulesQuery->where(function($q) use ($executiveTeamUserIds, $hasScheduleUser) {
                $q->whereIn('engineer_id', $executiveTeamUserIds);
                if ($hasScheduleUser) {
                    $q->orWhereHas('engineers', fn($eq) => $eq->whereIn('users.id', $executiveTeamUserIds));
                }
            })
            ->whereNotIn('engineer_id', $deliveryEngineerIds);

            if ($hasScheduleUser) {
                $schedulesQuery->whereDoesntHave('engineers', fn($eq) => $eq->whereIn('users.id', $deliveryEngineerIds));
            }

            $schedulesQuery->whereNotIn('category', ['Task', 'Preventive Maintenance', 'Corrective Maintenance', 'Troubleshoot', 'Ticket', 'Kegiatan']);
        } elseif ($isArchitect) {
            // Solution Architect mengelola jadwal/agenda kerja mandiri (diary SA)
            $schedulesQuery->where(function($q) use ($user, $hasScheduleUser) {
                $q->where('created_by', $user->id)
                  ->orWhere('engineer_id', $user->id);
                if ($hasScheduleUser) {
                    $q->orWhereHas('engineers', fn($sq) => $sq->where('users.id', $user->id));
                }
            });
        } elseif ($isCommercial) {
            // Sales / Commercial: dapat melihat jadwal Presales, Solution Architect, tim Commercial, serta jadwal yang dibuat oleh user atau melibatkan mereka
            $presalesUserIds = \App\Models\User::role([
                'Presales', 'Pre-Sales', 'Solution Architect', 'Solutions Architect',
                'Sales', 'Account Manager', 'BDM', 'BusDev', 'Business Development', 'CRO'
            ])->pluck('id')->toArray();

            $schedulesQuery->where(function($q) use ($presalesUserIds, $user, $hasScheduleUser) {
                $q->whereIn('engineer_id', $presalesUserIds)
                  ->orWhere('created_by', $user->id)
                  ->orWhereHas('creator', fn($cq) => $cq->role(['Sales', 'Account Manager', 'BDM', 'BusDev', 'Business Development', 'Presales', 'Solution Architect']));
                if ($hasScheduleUser) {
                    $q->orWhereHas('engineers', fn($sq) => $sq->whereIn('users.id', $presalesUserIds));
                }
            });
        } elseif ($scopeIds !== null) {
            $schedulesQuery->where(function($q) use ($scopeIds, $user, $hasScheduleUser) {
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

        $schedules = $schedulesQuery->get()
            ->map(function($schedule) use ($hasScheduleUser) {
                $engineerIds = $hasScheduleUser && $schedule->relationLoaded('engineers')
                    ? $schedule->engineers->pluck('id')->toArray()
                    : ($schedule->engineer_id ? [$schedule->engineer_id] : []);
                $engineersList = $hasScheduleUser && $schedule->relationLoaded('engineers')
                    ? $schedule->engineers->map(fn($e) => ['id' => $e->id, 'name' => $e->name])->toArray()
                    : ($schedule->engineer ? [['id' => $schedule->engineer->id, 'name' => $schedule->engineer->name]] : []);

                $taskStatus = null;
                if (in_array($schedule->category, ['Task', 'Kegiatan'])) {
                    $matchingTask = Task::where('title', $schedule->title)
                        ->where('project_id', $schedule->project_id)
                        ->first();
                    if ($matchingTask) {
                        $taskStatus = $matchingTask->status;
                    }
                }

                return [
                    'id'          => $schedule->id,
                    'title'       => $schedule->title,
                    'category'    => $schedule->category ?? 'Meeting',
                    'project_id'  => $schedule->project_id,
                    'engineer_id' => $schedule->engineer_id,
                    'engineer_ids'=> $engineerIds,
                    'date'        => $schedule->date ? $schedule->date->format('Y-m-d') : '',
                    'start_time'  => $schedule->start_time ? substr($schedule->start_time, 0, 5) : '',
                    'end_time'    => $schedule->end_time ? substr($schedule->end_time, 0, 5) : '',
                    'location'    => $schedule->location,
                    'description' => $schedule->description,
                    'status'      => $taskStatus,
                    'task_status' => $taskStatus,
                    'project'     => $schedule->project ? [
                        'id'   => $schedule->project->id,
                        'name' => $schedule->project->name,
                    ] : null,
                    'engineer'    => $schedule->engineer ? [
                        'id'   => $schedule->engineer->id,
                        'name' => $schedule->engineer->name,
                    ] : null,
                    'engineers'   => $engineersList,
                    'creator'     => $schedule->creator ? [
                        'id'   => $schedule->creator->id,
                        'name' => $schedule->creator->name,
                    ] : null,
                ];
            });

        $divisionId = $user->division_id;
        $isGlobal = ScopeHelper::isGlobal($user);
        if ($isExecutive) {
            $projects = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti', 'CUTI', 'Cuti'])
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
                      ->orWhere('sales_name', 'like', '%Nabylla%');
                })
                ->orderBy('name')
                ->get();
            $rawEngineers = $executiveTeamUsers;
        } else {
            $projectsQuery = Project::query();
            if ($divisionId && !$isGlobal) {
                $projectsQuery->where(function($q) use ($divisionId, $user, $scopeIds) {
                    $q->where('division_id', $divisionId)
                      ->orWhereNull('division_id')
                      ->orWhere('created_by', $user->id);
                    if (!empty($scopeIds)) {
                        $teamTaskProjectIds = Task::whereIn('engineer_id', $scopeIds)->pluck('project_id')->filter()->unique();
                        if ($teamTaskProjectIds->isNotEmpty()) {
                            $q->orWhereIn('id', $teamTaskProjectIds);
                        }
                    }
                });
            }
            $projects = $projectsQuery
                ->whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
                ->orderBy('name')
                ->get();
            $rawEngineers = $isArchitect ? collect([$user]) : ScopeHelper::getAssignableEngineers($user);
            if ($isCommercial) {
                // Urutkan Presales & Solution Architect di urutan teratas agar Sales langsung menemukan partner untuk POC / meeting
                $rawEngineers = $rawEngineers->sortByDesc(function($e) {
                    $isPresalesOrSA = method_exists($e, 'hasAnyRole') && $e->hasAnyRole(['Presales', 'Pre-Sales', 'Solution Architect', 'Solutions Architect', 'SA']);
                    return $isPresalesOrSA ? 2 : (method_exists($e, 'hasAnyRole') && $e->hasAnyRole(['Sales', 'Account Manager', 'BDM']) ? 1 : 0);
                })->values();
            }
        }
        $engineers = $rawEngineers->map(function($e) {
            $isMaint = ($e->division_id == 3) || (method_exists($e, 'hasAnyRole') && $e->hasAnyRole(['Lead Maintenance', 'Maintenance', 'Managed Service', 'Field Support', 'Field Support (EOS)']));
            $roles = method_exists($e, 'getRoleNames') ? $e->getRoleNames()->toArray() : [];
            return [
                'id' => $e->id,
                'name' => $e->name,
                'position' => $e->position ?? '',
                'role' => implode(', ', $roles),
                'division_id' => $e->division_id,
                'is_maintenance' => (bool)$isMaint,
            ];
        });

        $hasTaskUser = Schema::hasTable('task_user');
        $withTaskRelations = ['project', 'engineer'];
        if ($hasTaskUser) {
            $withTaskRelations[] = 'engineers';
        }

        // Tasks dengan deadline untuk ditampilkan di kalender
        if ($isArchitect || $isCommercial) {
            // Solution Architect & Commercial tidak terbebani task lapangan engineer lain
            $tasks = collect([]);
        } else {
            $existingScheduleTitles = Schedule::pluck('title')->map(fn($t) => strtolower(trim($t)))->toArray();
            $tasks = Task::with($withTaskRelations)
                ->when($scopeIds !== null, function($query) use ($scopeIds, $hasTaskUser) {
                    return $query->where(function($q) use ($scopeIds, $hasTaskUser) {
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
                })
                ->whereNotNull('deadline')
                ->get()
                ->filter(function($task) use ($existingScheduleTitles) {
                    return !in_array(strtolower(trim($task->title)), $existingScheduleTitles);
                })
                ->values()
                ->map(function($task) use ($hasTaskUser) {
                    $engineerIds = $hasTaskUser && $task->relationLoaded('engineers') && $task->engineers->isNotEmpty()
                        ? $task->engineers->pluck('id')->toArray()
                        : ($task->engineer_id ? [$task->engineer_id] : []);
                    $engineersList = $hasTaskUser && $task->relationLoaded('engineers') && $task->engineers->isNotEmpty()
                        ? $task->engineers->map(fn($e) => ['id' => $e->id, 'name' => $e->name])->toArray()
                        : ($task->engineer ? [['id' => $task->engineer->id, 'name' => $task->engineer->name]] : []);

                    return [
                        'id'            => $task->id,
                        'title'         => $task->title,
                        'deadline'      => $task->deadline ? $task->deadline->format('Y-m-d') : null,
                        'deadline_time' => $task->deadline_time ? substr($task->deadline_time, 0, 5) : '',
                        'priority'      => $task->priority,
                        'status'        => $task->status,
                        'engineer_id'   => $task->engineer_id,
                        'engineer_ids'  => $engineerIds,
                        'engineers'     => $engineersList,
                        'project_id'    => $task->project_id,
                        'project'       => $task->project ? ['id' => $task->project->id, 'name' => $task->project->name] : null,
                        'engineer'      => $task->engineer ? ['id' => $task->engineer->id, 'name' => $task->engineer->name] : null,
                    ];
                });
        }

        // Projects dengan deadline untuk ditampilkan di kalender
        if ($isExecutive) {
            $calendarProjects = collect([]);
        } else {
            $calendarProjects = Project::with('creator')
                ->when($divisionId && !$isGlobal, function($query) use ($divisionId, $user, $scopeIds) {
                    return $query->where(function($q) use ($divisionId, $user, $scopeIds) {
                        $q->where('division_id', $divisionId)
                          ->orWhereNull('division_id')
                          ->orWhere('created_by', $user->id);
                        if (!empty($scopeIds)) {
                            $teamTaskProjectIds = Task::whereIn('engineer_id', $scopeIds)->pluck('project_id')->filter()->unique();
                            if ($teamTaskProjectIds->isNotEmpty()) {
                                $q->orWhereIn('id', $teamTaskProjectIds);
                            }
                        }
                    });
                })
                ->whereNotNull('deadline')
                ->get()
                ->map(function($project) {
                    return [
                        'id'       => $project->id,
                        'name'     => $project->name,
                        'deadline' => $project->deadline ? $project->deadline->format('Y-m-d') : null,
                        'status'   => $project->status,
                        'client'   => $project->client,
                    ];
                });
        }

        $isMaintenance = ScopeHelper::isMaintenance($user);
        $msTickets = collect([]);
        if (!$isExecutive && ($isMaintenance || ScopeHelper::isGlobal($user))) {
            $msTickets = \App\Models\ManagedServiceTicket::with(['assignedEngineer', 'asset', 'project'])
                ->whereIn('status', ['Open', 'In Progress', 'Pending Vendor'])
                ->latest()
                ->get();
        }

        return view('schedules.index', compact('schedules', 'projects', 'engineers', 'tasks', 'calendarProjects', 'isLead', 'canManageSchedule', 'isArchitect', 'isMaintenance', 'msTickets', 'isCommercial', 'isExecutive'));
    }

    /**
     * Store a newly created schedule.
     */
    public function store(ScheduleRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();
            $hasScheduleUser = Schema::hasTable('schedule_user');

            // Kelola category & project 'other' / Day Off / Meeting (Meeting internal tidak dimasukkan ke tabel projects)
            $data['category'] = $request->input('category', 'Meeting');
            if ($data['category'] === 'Day Off' || $data['category'] === 'Meeting' || in_array(strtoupper(trim($request->input('new_project_name', ''))), ['DAY OFF', 'DAY OFF / CUTI', 'CUTI'])) {
                $data['project_id'] = null;
                if (!empty($request->input('new_project_name')) && empty($data['title'])) {
                    $data['title'] = trim($request->input('new_project_name'));
                }
            } elseif ($request->input('project_id') === 'other' || !empty($request->input('new_project_name'))) {
                $projectName = trim($request->input('new_project_name'));
                if (!empty($projectName)) {
                    $project = Project::firstOrCreate(
                        ['name' => $projectName],
                        [
                            'client'       => 'Internal / Lainnya',
                            'location'     => $data['location'] ?? 'On-Site / Lapangan',
                            'start_date'   => now()->toDateString(),
                            'deadline'     => now()->addMonth()->toDateString(),
                            'status'       => 'Planning',
                            'project_type' => 'One-Time Project',
                            'created_by'   => auth()->id(),
                        ]
                    );
                    $data['project_id'] = $project->id;
                    if (empty($data['title'])) {
                        $data['title'] = $projectName;
                    }
                } else {
                    $data['project_id'] = null;
                }
            }
            $createTask = ($request->boolean('create_task') || $request->input('create_task') === '1' || $request->input('create_task') === 1 || $request->input('create_task') === true || in_array($data['category'] ?? '', ['Task', 'Kegiatan'])) && (($data['category'] ?? '') !== 'Day Off');
            $taskPriority = $request->input('task_priority', 'High');
            if (!in_array($taskPriority, ['Low', 'Medium', 'High', 'Urgent'])) {
                $taskPriority = 'High';
            }
            unset($data['create_task'], $data['task_priority']);

            $engineerIds = $request->input('engineer_ids', []);
            if (empty($engineerIds) && !empty($data['engineer_id'])) {
                $engineerIds = [(int) $data['engineer_id']];
            }
            if (!empty($engineerIds)) {
                $data['engineer_id'] = $engineerIds[0];
            }
            unset($data['engineer_ids']);

            // Parse multiple sessions atau Date Range (Rentang Tanggal Proyek Panjang)
            $dateMode = $request->input('date_mode', 'sessions');
            $startDateStr = $request->input('start_date');
            $endDateStr = $request->input('end_date');
            $excludeSundays = $request->boolean('exclude_sundays', true);
            $excludeSaturdays = $request->boolean('exclude_saturdays', false);
            $includeSundays = $request->boolean('include_sundays', false);
            if ($includeSundays) {
                $excludeSundays = false;
            }

            $rawSessions = $request->input('sessions', []);
            $sessions = [];

            if ($dateMode === 'range' && !empty($startDateStr) && !empty($endDateStr)) {
                $startTime = !empty($data['start_time']) ? $data['start_time'] : null;
                $endTime   = !empty($data['end_time']) ? $data['end_time'] : $startTime;
                $location  = $data['location'] ?? null;

                $currentDate = \Carbon\Carbon::parse($startDateStr);
                $endDate = \Carbon\Carbon::parse($endDateStr);

                if ($currentDate->gt($endDate)) {
                    $temp = $currentDate;
                    $currentDate = $endDate;
                    $endDate = $temp;
                }

                while ($currentDate->lte($endDate)) {
                    $isSunday = $currentDate->isSunday();
                    $isSaturday = $currentDate->isSaturday();

                    $skip = false;
                    if ($isSunday && $excludeSundays) {
                        $skip = true;
                    }
                    if ($isSaturday && $excludeSaturdays) {
                        $skip = true;
                    }

                    if (!$skip) {
                        $sessions[] = [
                            'date'       => $currentDate->toDateString(),
                            'start_time' => $startTime,
                            'end_time'   => $endTime,
                            'location'   => $location,
                        ];
                    }

                    $currentDate->addDay();
                }
            } elseif (!empty($rawSessions) && is_array($rawSessions)) {
                foreach ($rawSessions as $s) {
                    if (!empty($s['date'])) {
                        $startTime = !empty($s['start_time']) ? $s['start_time'] : null;
                        $endTime   = !empty($s['end_time']) ? $s['end_time'] : $startTime;
                        $sessions[] = [
                            'date'       => $s['date'],
                            'start_time' => $startTime,
                            'end_time'   => $endTime,
                            'location'   => isset($s['location']) && trim($s['location']) !== '' ? trim($s['location']) : ($data['location'] ?? null),
                        ];
                    }
                }
            }

            // Fallback single session
            if (empty($sessions)) {
                $startTime = !empty($data['start_time']) ? $data['start_time'] : null;
                $endTime   = !empty($data['end_time']) ? $data['end_time'] : $startTime;
                $sessions[] = [
                    'date'       => $data['date'] ?? now()->toDateString(),
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'location'   => $data['location'] ?? null,
                ];
            }

            $createdSchedules = [];
            $creator = auth()->user();
            $creatorName = $creator ? $creator->name : 'Team Leader';

            $allDates = array_column($sessions, 'date');
            sort($allDates);
            $firstDate = !empty($allDates) ? reset($allDates) : ($data['date'] ?? now()->toDateString());
            $lastDate  = !empty($allDates) ? end($allDates) : $firstDate;
            $isMultiDate = count($allDates) > 1;

            foreach ($sessions as $session) {
                $scheduleData = $data;
                $scheduleData['date']       = $session['date'];
                $scheduleData['start_time'] = $session['start_time'];
                $scheduleData['end_time']   = $session['end_time'];
                $scheduleData['location']   = $session['location'];
                unset($scheduleData['sessions']);

                $schedule = Schedule::create($scheduleData);
                if ($hasScheduleUser && !empty($engineerIds)) {
                    $schedule->engineers()->sync($engineerIds);
                }
                $withRelations = ['project', 'engineer', 'creator'];
                if ($hasScheduleUser) {
                    $withRelations[] = 'engineers';
                }
                $schedule->load($withRelations);

                $engineerIdsList = $hasScheduleUser && $schedule->relationLoaded('engineers')
                    ? $schedule->engineers->pluck('id')->toArray()
                    : ($schedule->engineer_id ? [$schedule->engineer_id] : []);
                $engineersList = $hasScheduleUser && $schedule->relationLoaded('engineers')
                    ? $schedule->engineers->map(fn($e) => ['id' => $e->id, 'name' => $e->name])->toArray()
                    : ($schedule->engineer ? [['id' => $schedule->engineer->id, 'name' => $schedule->engineer->name]] : []);

                $createdSchedules[] = [
                    'id'          => $schedule->id,
                    'title'       => $schedule->title,
                    'category'    => $schedule->category ?? 'Meeting',
                    'project_id'  => $schedule->project_id,
                    'engineer_id' => $schedule->engineer_id,
                    'engineer_ids'=> $engineerIdsList,
                    'date'        => $schedule->date ? $schedule->date->format('Y-m-d') : '',
                    'start_time'  => $schedule->start_time ? substr($schedule->start_time, 0, 5) : '',
                    'end_time'    => $schedule->end_time ? substr($schedule->end_time, 0, 5) : '',
                    'location'    => $schedule->location,
                    'description' => $schedule->description,
                    'project'     => $schedule->project ? [
                        'id'   => $schedule->project->id,
                        'name' => $schedule->project->name,
                    ] : null,
                    'engineer'    => $schedule->engineer ? [
                        'id'   => $schedule->engineer->id,
                        'name' => $schedule->engineer->name,
                    ] : null,
                    'engineers'   => $engineersList,
                ];
            }

            // Dapatkan daftar unik seluruh engineer yang ditugaskan
            $allAssignedEngineerIds = $engineerIds;
            if (empty($allAssignedEngineerIds) && !empty($data['engineer_id'])) {
                $allAssignedEngineerIds = [(int) $data['engineer_id']];
            }

            $firstSessionTime = !empty($sessions[0]['start_time']) ? $sessions[0]['start_time'] : (!empty($data['start_time']) ? $data['start_time'] : null);
            $deadlineTime = $firstSessionTime ? substr($firstSessionTime, 0, 5) . ':00' : '23:59:00';

            // Format label rentang tanggal untuk keterangan task & notifikasi
            $dateRangeLabel = $isMultiDate
                ? (\Carbon\Carbon::parse($firstDate)->translatedFormat('d M Y') . ' s/d ' . \Carbon\Carbon::parse($lastDate)->translatedFormat('d M Y') . ' (' . count($sessions) . ' hari kerja)')
                : (\Carbon\Carbon::parse($firstDate)->translatedFormat('d/m/Y') . ($firstSessionTime ? ' Pukul ' . substr($firstSessionTime, 0, 5) . ' WIB' : ''));

            // 1. Buat HANYA 1 TASK di Penugasan Tim jika opsi dicentang (mencegah spam tiket per hari)
            if ($createTask && !empty($data['project_id'])) {
                $taskDesc = ($data['description'] ? $data['description'] . "\n\n" : '')
                    . "[Dibuat otomatis dari Jadwal: " . $data['title'] 
                    . " | Periode: " . $dateRangeLabel
                    . (!empty($data['location']) ? " | Lokasi: " . $data['location'] : "") . "]";

                $task = Task::create([
                    'title'         => $data['title'],
                    'project_id'    => $data['project_id'],
                    'engineer_id'   => !empty($allAssignedEngineerIds) ? $allAssignedEngineerIds[0] : null,
                    'priority'      => $taskPriority,
                    'status'        => 'Assigned',
                    'progress'      => 0,
                    'attachments'   => 0,
                    'start_date'    => $firstDate,
                    'deadline'      => $lastDate . ' ' . $deadlineTime,
                    'deadline_time' => $firstSessionTime ? substr($firstSessionTime, 0, 5) . ':00' : null,
                    'description'   => $taskDesc,
                    'created_by'    => auth()->id(),
                ]);

                if (Schema::hasTable('task_user') && !empty($allAssignedEngineerIds)) {
                    $task->engineers()->sync($allAssignedEngineerIds);
                }

                // Kirim 1 notifikasi penugasan task per engineer
                foreach ($allAssignedEngineerIds as $engId) {
                    \App\Models\Notification::create([
                        'user_id' => (int) $engId,
                        'title'   => 'Task Baru dari Jadwal: ' . $data['title'],
                        'message' => 'Tiket pekerjaan baru telah dibuat dari jadwal oleh ' . $creatorName . ' (Periode: ' . $dateRangeLabel . ', Prioritas: ' . $taskPriority . '). Silakan proses di menu Penugasan Tim.',
                        'url'     => route('tasks.index'),
                        'is_read' => false,
                    ]);
                }
            }

            // 2. Kirim 1 notifikasi agenda jadwal per engineer
            $notifTitle = ($data['category'] ?? '') === 'Day Off' 
                ? 'Jadwal Day Off / Cuti: ' . $data['title'] 
                : 'Agenda Jadwal Baru: ' . $data['title'];
            
            $notifMsg = 'Anda dijadwalkan oleh ' . $creatorName . ' pada: "' . $data['title'] . '" (' . $dateRangeLabel . ').';

            foreach ($allAssignedEngineerIds as $engId) {
                \App\Models\Notification::create([
                    'user_id' => (int) $engId,
                    'title'   => $notifTitle,
                    'message' => $notifMsg,
                    'url'     => route('schedules.index'),
                    'is_read' => false,
                ]);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success'   => true,
                    'message'   => count($createdSchedules) . ' jadwal berhasil dibuat!',
                    'schedule'  => $createdSchedules[0] ?? null,
                    'schedules' => $createdSchedules,
                ], 201);
            }

            return redirect()->route('schedules.index')
                ->with('success', 'Jadwal tim berhasil ditambahkan!');
                
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Gagal menyimpan jadwal: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('schedules.index')
                ->with('error', 'Gagal menyimpan jadwal!');
        }
    }

    /**
     * Update the specified schedule.
     */
    public function update(ScheduleRequest $request, Schedule $schedule)
    {
        try {
            $data = $request->validated();
            $hasScheduleUser = Schema::hasTable('schedule_user');

            // Kelola category & project 'other' / Day Off jika diedit
            // Kelola category & project 'other' / Day Off / Meeting (Meeting internal tidak dimasukkan ke tabel projects)
            $data['category'] = $request->input('category', $schedule->category ?? 'Meeting');
            if ($data['category'] === 'Day Off' || $data['category'] === 'Meeting' || in_array(strtoupper(trim($request->input('new_project_name', ''))), ['DAY OFF', 'DAY OFF / CUTI', 'CUTI'])) {
                $data['project_id'] = null;
                if (!empty($request->input('new_project_name')) && empty($data['title'])) {
                    $data['title'] = trim($request->input('new_project_name'));
                }
            } elseif ($request->input('project_id') === 'other' || !empty($request->input('new_project_name'))) {
                $data['project_id'] = null;
                if (!empty($request->input('new_project_name')) && empty($data['title'])) {
                    $data['title'] = trim($request->input('new_project_name'));
                }
            }
            unset($data['new_project_name']);

            if (empty($data['end_time']) && !empty($data['start_time'])) {
                $data['end_time'] = $data['start_time'];
            }

            $engineerIds = $request->input('engineer_ids', []);
            if (empty($engineerIds) && !empty($data['engineer_id'])) {
                $engineerIds = [(int) $data['engineer_id']];
            }
            if (!empty($engineerIds)) {
                $data['engineer_id'] = $engineerIds[0];
                if ($hasScheduleUser) {
                    $schedule->engineers()->sync($engineerIds);
                }
            }
            unset($data['engineer_ids']);

            $oldTitle = $schedule->getOriginal('title');
            $oldProjectId = $schedule->getOriginal('project_id');

            $schedule->update($data);
            $withRelations = ['project', 'engineer', 'creator'];
            if ($hasScheduleUser) {
                $withRelations[] = 'engineers';
            }
            $schedule->load($withRelations);

            $engineerIdsList = $hasScheduleUser && $schedule->relationLoaded('engineers')
                ? $schedule->engineers->pluck('id')->toArray()
                : ($schedule->engineer_id ? [$schedule->engineer_id] : []);
            $engineersList = $hasScheduleUser && $schedule->relationLoaded('engineers')
                ? $schedule->engineers->map(fn($e) => ['id' => $e->id, 'name' => $e->name])->toArray()
                : ($schedule->engineer ? [['id' => $schedule->engineer->id, 'name' => $schedule->engineer->name]] : []);

            // Sync / Update Task jika agenda ini memiliki task terkait atau kategori Task/Kegiatan
            $deadlineTime = $schedule->start_time ? substr($schedule->start_time, 0, 5) . ':00' : '23:59:00';
            $dateStr = $schedule->date ? $schedule->date->format('Y-m-d') : now()->toDateString();
            
            // Cari task lama berdasarkan judul lama atau judul baru
            $task = null;
            if ($oldTitle) {
                $task = Task::where('title', $oldTitle)
                    ->where('project_id', $oldProjectId)
                    ->first();

                if (!$task) {
                    $task = Task::where('title', $oldTitle)->first();
                }
            }

            if (!$task) {
                $task = Task::where('title', $schedule->title)
                    ->where('project_id', $schedule->project_id)
                    ->first();
            }

            $isMeetingOrDayOff = in_array($schedule->category, ['Meeting', 'Day Off', 'Meeting Klien / Principal', 'Sesi PoC & Lab', 'PoC & Demo']) || str_contains(strtolower($schedule->category ?? ''), 'meeting');

            if ($isMeetingOrDayOff) {
                // Jika jadwal diubah menjadi Meeting atau Day Off, hapus task penugasan tim terkait agar tidak muncul lagi di menu Penugasan Tim
                if ($task) {
                    $task->delete();
                }
                Task::where('title', $schedule->title)
                    ->when(!empty($oldTitle), fn($q) => $q->orWhere('title', $oldTitle))
                    ->delete();
            } else {
                // Kategori adalah Task / Kegiatan / Preventive Maintenance
                if ($task) {
                    // Update task yang sudah ada (termasuk rename judul baru)
                    $taskUpdateData = [
                        'title'         => $schedule->title,
                        'deadline'      => $dateStr . ' ' . $deadlineTime,
                        'deadline_time' => $schedule->start_time ? substr($schedule->start_time, 0, 5) . ':00' : null,
                        'engineer_id'   => $schedule->engineer_id,
                        'description'   => $schedule->description ?: $task->description,
                    ];

                    if (!empty($schedule->project_id)) {
                        $taskUpdateData['project_id'] = $schedule->project_id;
                    }

                    $task->update($taskUpdateData);

                    if (Schema::hasTable('task_user') && !empty($engineerIdsList)) {
                        $task->engineers()->sync($engineerIdsList);
                    }
                } elseif ((in_array($schedule->category, ['Task', 'Kegiatan', 'Preventive Maintenance']) || $request->boolean('create_task')) && !empty($schedule->project_id)) {
                    // Buat task baru hanya jika memang belum pernah ada, kategori adalah Task, dan project_id tidak null
                    $task = Task::create([
                        'title'         => $schedule->title,
                        'project_id'    => $schedule->project_id,
                        'engineer_id'   => $schedule->engineer_id,
                        'priority'      => $request->input('task_priority', 'High'),
                        'status'        => 'Assigned',
                        'progress'      => 0,
                        'attachments'   => 0,
                        'deadline'      => $dateStr . ' ' . $deadlineTime,
                        'deadline_time' => $schedule->start_time ? substr($schedule->start_time, 0, 5) . ':00' : null,
                        'description'   => $schedule->description ?: ('Task dibuat dari jadwal: ' . $schedule->title),
                        'created_by'    => auth()->id(),
                    ]);

                    if (Schema::hasTable('task_user') && !empty($engineerIdsList)) {
                        $task->engineers()->sync($engineerIdsList);
                    }
                }
            }

            // Kirim notifikasi ke seluruh engineer jika agenda diperbarui
            $creator = auth()->user();
            $creatorName = $creator ? $creator->name : 'Team Leader';
            $notifTitle = $schedule->category === 'Day Off' ? 'Pembaruan Jadwal Day Off: ' . $schedule->title : 'Pembaruan Agenda: ' . $schedule->title;
            foreach ($engineerIdsList as $engId) {
                \App\Models\Notification::create([
                    'user_id' => (int) $engId,
                    'title'   => $notifTitle,
                    'message' => 'Agenda "' . $schedule->title . '" telah diperbarui oleh ' . $creatorName . ' (' . ($schedule->date ? $schedule->date->format('d/m/Y') : '-') . ($schedule->start_time ? ' pukul ' . substr($schedule->start_time, 0, 5) . ' WIB' : '') . ').',
                    'url'     => route('schedules.index'),
                    'is_read' => false,
                ]);
            }

            $response = [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'category' => $schedule->category ?? 'Meeting',
                'project_id' => $schedule->project_id,
                'engineer_id' => $schedule->engineer_id,
                'engineer_ids' => $engineerIdsList,
                'date' => $schedule->date ? $schedule->date->format('Y-m-d') : '',
                'start_time' => $schedule->start_time ? substr($schedule->start_time, 0, 5) : '',
                'end_time' => $schedule->end_time ? substr($schedule->end_time, 0, 5) : '',
                'location' => $schedule->location,
                'description' => $schedule->description,
                'project' => $schedule->project ? [
                    'id' => $schedule->project->id,
                    'name' => $schedule->project->name,
                ] : null,
                'engineer' => $schedule->engineer ? [
                    'id' => $schedule->engineer->id,
                    'name' => $schedule->engineer->name,
                ] : null,
                'engineers' => $engineersList,
            ];

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($response);
            }

            return redirect()->route('schedules.index')
                ->with('success', 'Jadwal berhasil diperbarui!');
                
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Gagal memperbarui jadwal: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('schedules.index')
                ->with('error', 'Gagal memperbarui jadwal!');
        }
    }

    /**
     * Remove the specified schedule.
     */
    public function destroy(Schedule $schedule)
    {
        try {
            if (in_array($schedule->category, ['Task', 'Kegiatan', 'Preventive Maintenance'])) {
                $taskQuery = Task::where('title', $schedule->title);
                if (!empty($schedule->project_id)) {
                    $taskQuery->where('project_id', $schedule->project_id);
                }
                $taskQuery->delete();
            }

            $schedule->delete();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Jadwal berhasil dihapus!'
                ]);
            }

            return redirect()->route('schedules.index')
                ->with('success', 'Jadwal berhasil dihapus!');
                
        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus jadwal: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('schedules.index')
                ->with('error', 'Gagal menghapus jadwal!');
        }
    }

    /**
     * Get calendar data for FullCalendar.
     */
    public function getCalendarData(Request $request)
    {
        try {
            $user = auth()->user();
            $start = $request->get('start', now()->startOfWeek()->toDateString());
            $end = $request->get('end', now()->endOfWeek()->toDateString());
            
            $scopeIds = ScopeHelper::getScopeUserIds($user);
            
            $schedules = Schedule::with(['project', 'engineer'])
                ->when($scopeIds !== null, function($query) use ($scopeIds) {
                    return count($scopeIds) === 1
                        ? $query->where('engineer_id', $scopeIds[0])
                        : $query->whereIn('engineer_id', $scopeIds);
                })
                ->whereBetween('date', [$start, $end])
                ->get()
                ->map(function($schedule) {
                    return [
                        'id' => $schedule->id,
                        'title' => $schedule->title,
                        'start' => $schedule->date . 'T' . $schedule->start_time,
                        'end' => $schedule->date . 'T' . $schedule->end_time,
                        'project' => $schedule->project->name ?? '',
                        'engineer' => $schedule->engineer->name ?? '',
                        'location' => $schedule->location,
                        'backgroundColor' => '#C81E2C',
                        'borderColor' => '#C81E2C',
                    ];
                });

            return response()->json($schedules);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data kalender: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get schedules for a specific week.
     */
    public function getSchedulesForWeek(Request $request)
    {
        try {
            $user = auth()->user();
            $weekStart = $request->get('week_start', now()->startOfWeek()->toDateString());
            $weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));
            $scopeIds = ScopeHelper::getScopeUserIds($user);
            
            $schedules = Schedule::with(['project', 'engineer'])
                ->when($scopeIds !== null, function($query) use ($scopeIds) {
                    return count($scopeIds) === 1
                        ? $query->where('engineer_id', $scopeIds[0])
                        : $query->whereIn('engineer_id', $scopeIds);
                })
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->get()
                ->map(function($schedule) {
                    return [
                        'id' => $schedule->id,
                        'title' => $schedule->title,
                        'date' => $schedule->date->format('Y-m-d'),
                        'start_time' => $schedule->start_time ? substr($schedule->start_time, 0, 5) : '',
                        'end_time' => $schedule->end_time ? substr($schedule->end_time, 0, 5) : '',
                        'location' => $schedule->location,
                        'project_id' => $schedule->project_id,
                        'engineer_id' => $schedule->engineer_id,
                        'project' => $schedule->project ? [
                            'id' => $schedule->project->id,
                            'name' => $schedule->project->name,
                        ] : null,
                        'engineer' => $schedule->engineer ? [
                            'id' => $schedule->engineer->id,
                            'name' => $schedule->engineer->name,
                        ] : null,
                    ];
                });

            return response()->json($schedules);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data mingguan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export schedules to Excel with Daily, Weekly, and Monthly sheets.
     */
    public function exportExcel(Request $request, ScheduleExportService $exportService)
    {
        try {
            $user = auth()->user();
            $isCommercial = $user->hasAnyRole(['Sales', 'Account Manager', 'BusDev', 'BDM', 'Business Development', 'CRO', 'Customer Relation Officer']);
            $isLead = ScopeHelper::isManagerial($user) || $isCommercial;
            $scopeIds = ScopeHelper::getScopeUserIds($user);
            $engineerId = $request->get('engineer_id');

            $schedulesQuery = Schedule::with(['project', 'engineer', 'creator']);
            if ($isCommercial) {
                $presalesUserIds = \App\Models\User::role([
                    'Presales', 'Pre-Sales', 'Solution Architect', 'Solutions Architect',
                    'Sales', 'Account Manager', 'BDM', 'BusDev', 'Business Development', 'CRO'
                ])->pluck('id')->toArray();

                $schedulesQuery->where(function($q) use ($presalesUserIds, $user) {
                    $q->whereIn('engineer_id', $presalesUserIds)
                      ->orWhere('created_by', $user->id)
                      ->orWhereHas('creator', fn($cq) => $cq->role(['Sales', 'Account Manager', 'BDM', 'BusDev', 'Business Development', 'Presales', 'Solution Architect']));
                    if (Schema::hasTable('schedule_user')) {
                        $q->orWhereHas('engineers', fn($sq) => $sq->whereIn('users.id', $presalesUserIds));
                    }
                });
            } elseif ($scopeIds !== null) {
                $schedulesQuery->when(count($scopeIds) === 1, fn($q) => $q->where('engineer_id', $scopeIds[0]), fn($q) => $q->whereIn('engineer_id', $scopeIds));
            }

            if ($isLead && $engineerId) {
                $schedulesQuery->where(function($q) use ($engineerId) {
                    $q->where('engineer_id', $engineerId);
                    if (Schema::hasTable('schedule_user')) {
                        $q->orWhereHas('engineers', fn($sq) => $sq->where('users.id', $engineerId));
                    }
                });
            }
            $schedules = $schedulesQuery->get();

            $engineerFilterName = null;
            if ($engineerId) {
                $engUser = User::find($engineerId);
                if ($engUser) {
                    $engineerFilterName = $engUser->name;
                }
            }

            $spreadsheet = $exportService->generate($schedules, $engineerFilterName);

            $filename = 'Jadwal_Kerja_' . date('Y-m-d_H-i-s') . '.xlsx';

            $response = new StreamedResponse(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            });

            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengeksport data jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Export schedules to PDF Document.
     */
    public function exportPdf(Request $request, ScheduleExportService $exportService)
    {
        try {
            $user = auth()->user();
            $isCommercial = $user->hasAnyRole(['Sales', 'Account Manager', 'BusDev', 'BDM', 'Business Development', 'CRO', 'Customer Relation Officer']);
            $isLead = ScopeHelper::isManagerial($user) || $isCommercial;
            $scopeIds = ScopeHelper::getScopeUserIds($user);
            $engineerId = $request->get('engineer_id');

            $schedulesQuery = Schedule::with(['project', 'engineer', 'creator']);
            if ($isCommercial) {
                $presalesUserIds = \App\Models\User::role([
                    'Presales', 'Pre-Sales', 'Solution Architect', 'Solutions Architect',
                    'Sales', 'Account Manager', 'BDM', 'BusDev', 'Business Development', 'CRO'
                ])->pluck('id')->toArray();

                $schedulesQuery->where(function($q) use ($presalesUserIds, $user) {
                    $q->whereIn('engineer_id', $presalesUserIds)
                      ->orWhere('created_by', $user->id)
                      ->orWhereHas('creator', fn($cq) => $cq->role(['Sales', 'Account Manager', 'BDM', 'BusDev', 'Business Development', 'Presales', 'Solution Architect']));
                    if (Schema::hasTable('schedule_user')) {
                        $q->orWhereHas('engineers', fn($sq) => $sq->whereIn('users.id', $presalesUserIds));
                    }
                });
            } elseif ($scopeIds !== null) {
                $schedulesQuery->when(count($scopeIds) === 1, fn($q) => $q->where('engineer_id', $scopeIds[0]), fn($q) => $q->whereIn('engineer_id', $scopeIds));
            }

            if ($isLead && $engineerId) {
                $schedulesQuery->where(function($q) use ($engineerId) {
                    $q->where('engineer_id', $engineerId);
                    if (Schema::hasTable('schedule_user')) {
                        $q->orWhereHas('engineers', fn($sq) => $sq->where('users.id', $engineerId));
                    }
                });
            }
            $schedules = $schedulesQuery->get();

            $engineerFilterName = null;
            if ($engineerId) {
                $engUser = User::find($engineerId);
                if ($engUser) {
                    $engineerFilterName = $engUser->name;
                }
            }

            $pdf = $exportService->generatePdf($schedules, $engineerFilterName);
            $filename = 'Laporan_Jadwal_Kerja_' . date('Y-m-d_H-i-s') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengeksport PDF jadwal: ' . $e->getMessage());
        }
    }
}