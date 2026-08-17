<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Http\Requests\ScheduleRequest;
use App\Services\ScheduleExportService;
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
        $user = auth()->user();
        
        $schedules = Schedule::with(['project', 'engineer', 'creator'])
            ->when(!$user->hasRole('Lead Engineer'), function($query) use ($user) {
                return $query->where('engineer_id', $user->id);
            })
            ->get()
            ->map(function($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->title,
                    'project_id' => $schedule->project_id,
                    'engineer_id' => $schedule->engineer_id,
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
                    'creator' => $schedule->creator ? [
                        'id' => $schedule->creator->id,
                        'name' => $schedule->creator->name,
                    ] : null,
                ];
            });
            
        $projects = Project::all();
        $engineers = User::engineers()->get();

        // Tasks dengan deadline untuk ditampilkan di kalender
        $tasks = Task::with(['project', 'engineer'])
            ->when(!$user->hasRole('Lead Engineer'), function($query) use ($user) {
                return $query->where('engineer_id', $user->id);
            })
            ->whereNotNull('deadline')
            ->whereNot('status', 'Completed')
            ->get()
            ->map(function($task) {
                return [
                    'id'          => $task->id,
                    'title'       => $task->title,
                    'deadline'    => $task->deadline ? $task->deadline->format('Y-m-d') : null,
                    'priority'    => $task->priority,
                    'status'      => $task->status,
                    'engineer_id' => $task->engineer_id,
                    'project_id'  => $task->project_id,
                    'project'     => $task->project ? ['id' => $task->project->id, 'name' => $task->project->name] : null,
                    'engineer'    => $task->engineer ? ['id' => $task->engineer->id, 'name' => $task->engineer->name] : null,
                ];
            });

        // Projects dengan deadline untuk ditampilkan di kalender
        $calendarProjects = Project::with('creator')
            ->when(!$user->hasRole('Lead Engineer'), function($query) use ($user) {
                // Engineer hanya lihat project yang ada task miliknya
                $projectIds = Task::where('engineer_id', $user->id)->pluck('project_id')->unique();
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
        
        return view('schedules.index', compact('schedules', 'projects', 'engineers', 'tasks', 'calendarProjects'));
    }

    /**
     * Store a newly created schedule.
     */
    public function store(ScheduleRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();
            
            $schedule = Schedule::create($data);
            $schedule->load(['project', 'engineer', 'creator']);

            $response = [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'project_id' => $schedule->project_id,
                'engineer_id' => $schedule->engineer_id,
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
            ];

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json($response, 201);
            }

            return redirect()->route('schedules.index')
                ->with('success', 'Jadwal berhasil ditambahkan!');
                
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
            $schedule->update($request->validated());
            $schedule->load(['project', 'engineer', 'creator']);

            $response = [
                'id' => $schedule->id,
                'title' => $schedule->title,
                'project_id' => $schedule->project_id,
                'engineer_id' => $schedule->engineer_id,
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
            
            $schedules = Schedule::with(['project', 'engineer'])
                ->when(!$user->hasRole('Lead Engineer'), function($query) use ($user) {
                    return $query->where('engineer_id', $user->id);
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
            
            $schedules = Schedule::with(['project', 'engineer'])
                ->when(!$user->hasRole('Lead Engineer'), function($query) use ($user) {
                    return $query->where('engineer_id', $user->id);
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
            $engineerId = $request->get('engineer_id');

            $schedules = Schedule::with(['project', 'engineer', 'creator'])
                ->when(!$user->hasRole('Lead Engineer'), function ($query) use ($user) {
                    return $query->where('engineer_id', $user->id);
                })
                ->when($user->hasRole('Lead Engineer') && $engineerId, function ($query) use ($engineerId) {
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
}