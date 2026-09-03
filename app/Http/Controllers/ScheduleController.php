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
        $user     = auth()->user();
        $scopeIds = ScopeHelper::getScopeUserIds($user);
        $hasScheduleUser = Schema::hasTable('schedule_user');

        $withRelations = ['project', 'engineer', 'creator'];
        if ($hasScheduleUser) {
            $withRelations[] = 'engineers';
        }

        $schedules = Schedule::with($withRelations)
            ->when($scopeIds !== null, function($query) use ($scopeIds, $user, $hasScheduleUser) {
                return $query->where(function($q) use ($scopeIds, $user, $hasScheduleUser) {
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
            })
            ->get()
            ->map(function($schedule) use ($hasScheduleUser) {
                $engineerIds = $hasScheduleUser && $schedule->relationLoaded('engineers')
                    ? $schedule->engineers->pluck('id')->toArray()
                    : ($schedule->engineer_id ? [$schedule->engineer_id] : []);
                $engineersList = $hasScheduleUser && $schedule->relationLoaded('engineers')
                    ? $schedule->engineers->map(fn($e) => ['id' => $e->id, 'name' => $e->name])->toArray()
                    : ($schedule->engineer ? [['id' => $schedule->engineer->id, 'name' => $schedule->engineer->name]] : []);

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
        $projectsQuery = Project::where('status', '!=', 'Completed');
        if ($divisionId && !$isGlobal) {
            $projectsQuery->where(function($q) use ($divisionId) {
                $q->where('division_id', $divisionId)
                  ->orWhereNull('division_id');
            });
        }
        $projects  = $projectsQuery->orderBy('name')->get();
        $engineers = ScopeHelper::getAssignableEngineers($user);

        $hasTaskUser = Schema::hasTable('task_user');
        $withTaskRelations = ['project', 'engineer'];
        if ($hasTaskUser) {
            $withTaskRelations[] = 'engineers';
        }

        // Tasks dengan deadline untuk ditampilkan di kalender
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
            ->whereNot('status', 'Completed')
            ->get()
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

        // Projects dengan deadline untuk ditampilkan di kalender
        $calendarProjects = Project::with('creator')
            ->when($scopeIds !== null, function($query) use ($scopeIds, $hasTaskUser) {
                // Non-global hanya lihat project yang ada task untuk timnya
                $projectIdsQuery = Task::query();
                if (count($scopeIds) === 1) {
                    $projectIdsQuery->where('engineer_id', $scopeIds[0]);
                    if ($hasTaskUser) {
                        $projectIdsQuery->orWhereHas('engineers', fn($sq) => $sq->where('users.id', $scopeIds[0]));
                    }
                } else {
                    $projectIdsQuery->whereIn('engineer_id', $scopeIds);
                    if ($hasTaskUser) {
                        $projectIdsQuery->orWhereHas('engineers', fn($sq) => $sq->whereIn('users.id', $scopeIds));
                    }
                }
                $projectIds = $projectIdsQuery->pluck('project_id')->unique();
                return $query->whereIn('id', $projectIds);
            })
            ->whereNotNull('deadline')
            ->whereNot('status', 'Completed')
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
        
        $isLead = ScopeHelper::isManagerial($user);
        return view('schedules.index', compact('schedules', 'projects', 'engineers', 'tasks', 'calendarProjects', 'isLead'));
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

            // Kelola category & project 'other' / Day Off
            $data['category'] = $request->input('category', 'Meeting');
            if ($data['category'] === 'Day Off' || in_array(strtoupper(trim($request->input('new_project_name', ''))), ['DAY OFF', 'DAY OFF / CUTI', 'CUTI'])) {
                $data['project_id'] = null;
            } elseif ($request->input('project_id') === 'other' || !empty($request->input('new_project_name'))) {
                $projectName = trim($request->input('new_project_name'));
                if (!empty($projectName) && !in_array(strtoupper($projectName), ['DAY OFF', 'DAY OFF / CUTI', 'CUTI'])) {
                    $project = Project::firstOrCreate(
                        ['name' => $projectName],
                        [
                            'client'       => 'Internal / Umum',
                            'location'     => $request->input('location') ?: 'Kantor / Ruang Meeting',
                            'start_date'   => $request->input('date') ?: now()->toDateString(),
                            'deadline'     => $request->input('date') ?: now()->toDateString(),
                            'status'       => 'On Progress',
                            'project_type' => 'Meeting / Internal',
                            'created_by'   => auth()->id(),
                        ]
                    );
                    $data['project_id'] = $project->id;
                }
            }
            unset($data['new_project_name']);

            $engineerIds = $request->input('engineer_ids', []);
            if (empty($engineerIds) && !empty($data['engineer_id'])) {
                $engineerIds = [(int) $data['engineer_id']];
            }
            if (!empty($engineerIds)) {
                $data['engineer_id'] = $engineerIds[0];
            }
            unset($data['engineer_ids']);

            // Parse multiple sessions jika ada
            $rawSessions = $request->input('sessions', []);
            $sessions = [];
            if (!empty($rawSessions) && is_array($rawSessions)) {
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

                // Notifikasi ke engineer
                $notifTitle = $schedule->category === 'Day Off' ? 'Jadwal Day Off / Cuti: ' . $schedule->title : 'Agenda Jadwal Baru: ' . $schedule->title;
                foreach ($engineerIdsList as $engId) {
                    \App\Models\Notification::create([
                        'user_id' => (int) $engId,
                        'title'   => $notifTitle,
                        'message' => 'Anda dijadwalkan oleh ' . $creatorName . ' pada: "' . $schedule->title . '" (' . ($schedule->date ? $schedule->date->format('d/m/Y') : '-') . ($schedule->start_time ? ' pukul ' . substr($schedule->start_time, 0, 5) . ' WIB' : '') . ').',
                        'url'     => route('schedules.index'),
                        'is_read' => false,
                    ]);
                }

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
            $data['category'] = $request->input('category', $schedule->category ?? 'Meeting');
            if ($data['category'] === 'Day Off' || in_array(strtoupper(trim($request->input('new_project_name', ''))), ['DAY OFF', 'DAY OFF / CUTI', 'CUTI'])) {
                $data['project_id'] = null;
            } elseif ($request->input('project_id') === 'other' || !empty($request->input('new_project_name'))) {
                $projectName = trim($request->input('new_project_name'));
                if (!empty($projectName) && !in_array(strtoupper($projectName), ['DAY OFF', 'DAY OFF / CUTI', 'CUTI'])) {
                    $project = Project::firstOrCreate(
                        ['name' => $projectName],
                        [
                            'client'       => 'Internal / Umum',
                            'location'     => $request->input('location') ?: 'Kantor / Ruang Meeting',
                            'start_date'   => $request->input('date') ?: now()->toDateString(),
                            'deadline'     => $request->input('date') ?: now()->toDateString(),
                            'status'       => 'On Progress',
                            'project_type' => 'Meeting / Internal',
                            'created_by'   => auth()->id(),
                        ]
                    );
                    $data['project_id'] = $project->id;
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
            $isLead = ScopeHelper::isManagerial($user);
            $scopeIds = ScopeHelper::getScopeUserIds($user);
            $engineerId = $request->get('engineer_id');

            $schedules = Schedule::with(['project', 'engineer', 'creator'])
                ->when($scopeIds !== null, function ($query) use ($scopeIds) {
                    return count($scopeIds) === 1
                        ? $query->where('engineer_id', $scopeIds[0])
                        : $query->whereIn('engineer_id', $scopeIds);
                })
                ->when($isLead && $engineerId, function ($query) use ($engineerId) {
                    return $query->where('engineer_id', $engineerId);
                })
                ->get();

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
            $isLead = ScopeHelper::isManagerial($user);
            $scopeIds = ScopeHelper::getScopeUserIds($user);
            $engineerId = $request->get('engineer_id');

            $schedules = Schedule::with(['project', 'engineer', 'creator'])
                ->when($scopeIds !== null, function ($query) use ($scopeIds) {
                    return count($scopeIds) === 1
                        ? $query->where('engineer_id', $scopeIds[0])
                        : $query->whereIn('engineer_id', $scopeIds);
                })
                ->when($isLead && $engineerId, function ($query) use ($engineerId) {
                    return $query->where('engineer_id', $engineerId);
                })
                ->get();

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