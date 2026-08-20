<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Timesheet;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Services\TimesheetExportService;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TimesheetController extends Controller
{
    /**
     * Display timesheet list (Leader: Team view, Engineer: Personal view).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isLead = $user->hasRole('Lead Engineer');

        // Filter Inputs
        $engineerId = $request->get('engineer_id');
        $projectId  = $request->get('project_id');
        $category   = $request->get('category');
        $dateStart  = $request->get('date_start');
        $dateEnd    = $request->get('date_end');
        $search     = $request->get('search');

        // Base Query
        $query = Timesheet::with(['user', 'project', 'task'])
            ->when(!$isLead, function ($q) use ($user) {
                return $q->where('user_id', $user->id);
            })
            ->when($isLead && $engineerId, function ($q) use ($engineerId) {
                return $q->where('user_id', $engineerId);
            })
            ->when($projectId, function ($q) use ($projectId) {
                return $q->where('project_id', $projectId);
            })
            ->when($category, function ($q) use ($category) {
                return $q->where('category', $category);
            })
            ->when($dateStart, function ($q) use ($dateStart) {
                return $q->whereDate('date', '>=', $dateStart);
            })
            ->when($dateEnd, function ($q) use ($dateEnd) {
                return $q->whereDate('date', '<=', $dateEnd);
            })
            ->when($search, function ($q) use ($search) {
                return $q->where(function ($sq) use ($search) {
                    $sq->where('activity', 'like', "%{$search}%")
                       ->orWhere('notes', 'like', "%{$search}%")
                       ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                       ->orWhereHas('project', fn($pq) => $pq->where('name', 'like', "%{$search}%"))
                       ->orWhereHas('task', fn($tq) => $tq->where('title', 'like', "%{$search}%"));
                });
            });

        $timesheets = $query->orderBy('date', 'desc')->orderBy('start_time', 'desc')->paginate(15)->withQueryString();

        // Summary Calculations (Based on current user context)
        $statQuery = Timesheet::query()
            ->when(!$isLead, fn($q) => $q->where('user_id', $user->id));

        $startOfWeek  = now()->startOfWeek();
        $endOfWeek    = now()->endOfWeek();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth   = now()->endOfMonth();

        $allStatLogs = $statQuery->get();

        $weekLogs = $allStatLogs->filter(fn($t) => $t->date && $t->date >= $startOfWeek && $t->date <= $endOfWeek);
        $monthLogs = $allStatLogs->filter(fn($t) => $t->date && $t->date >= $startOfMonth && $t->date <= $endOfMonth);

        $totalWeekHours  = round($weekLogs->sum('duration_minutes') / 60, 1);
        $totalMonthHours = round($monthLogs->sum('duration_minutes') / 60, 1);
        $totalLogsCount  = $allStatLogs->count();
        $totalOvertimeHours = round($allStatLogs->where('category', 'Overtime')->sum('duration_minutes') / 60, 1);

        // Active projects and available engineers
        $projects = Project::orderBy('name')->get();
        $engineers = $isLead ? User::engineers()->active()->orderBy('name')->get() : collect();
        $myTasks = Task::when(!$isLead, fn($q) => $q->where('engineer_id', $user->id))->orderBy('title')->get();

        return view('timesheets.index', compact(
            'timesheets',
            'isLead',
            'projects',
            'engineers',
            'myTasks',
            'totalWeekHours',
            'totalMonthHours',
            'totalLogsCount',
            'totalOvertimeHours',
            'engineerId',
            'projectId',
            'category',
            'dateStart',
            'dateEnd',
            'search'
        ));
    }

    /**
     * Store a newly created timesheet.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $isLead = $user->hasRole('Lead Engineer');

        $validated = $request->validate([
            'engineer_id' => $isLead ? 'nullable|exists:users,id' : 'nullable',
            'project_id'  => 'nullable|exists:projects,id',
            'task_id'     => 'nullable|exists:tasks,id',
            'date'        => 'required|date',
            'start_time'  => 'required',
            'end_time'    => 'required',
            'category'    => 'required|in:On-Site,Remote,Overtime,Maintenance',
            'activity'    => 'required|string|max:1000',
            'notes'       => 'nullable|string|max:500',
        ]);

        $assignedUserId = ($isLead && !empty($validated['engineer_id'])) ? $validated['engineer_id'] : $user->id;

        // Calculate Duration in Minutes
        $start = Carbon::parse($validated['date'] . ' ' . $validated['start_time']);
        $end   = Carbon::parse($validated['date'] . ' ' . $validated['end_time']);
        
        if ($end->lt($start)) {
            $end->addDay();
        }
        $durationMinutes = max(1, $start->diffInMinutes($end));

        $timesheet = Timesheet::create([
            'user_id'          => $assignedUserId,
            'project_id'       => $validated['project_id'] ?? null,
            'task_id'          => $validated['task_id'] ?? null,
            'date'             => $validated['date'],
            'start_time'       => $validated['start_time'],
            'end_time'         => $validated['end_time'],
            'duration_minutes' => $durationMinutes,
            'category'         => $validated['category'],
            'activity'         => $validated['activity'],
            'notes'            => $validated['notes'] ?? null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Log aktivitas berhasil ditambahkan!',
                'data'    => $timesheet->load(['user', 'project', 'task']),
            ]);
        }

        return redirect()->route('timesheets.index')->with('success', 'Log aktivitas berhasil ditambahkan!');
    }

    /**
     * Update the specified timesheet.
     */
    public function update(Request $request, Timesheet $timesheet)
    {
        $user = auth()->user();
        $isLead = $user->hasRole('Lead Engineer');

        if (!$isLead && $timesheet->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data ini.');
        }

        $validated = $request->validate([
            'engineer_id' => $isLead ? 'nullable|exists:users,id' : 'nullable',
            'project_id'  => 'nullable|exists:projects,id',
            'task_id'     => 'nullable|exists:tasks,id',
            'date'        => 'required|date',
            'start_time'  => 'required',
            'end_time'    => 'required',
            'category'    => 'required|in:On-Site,Remote,Overtime,Maintenance',
            'activity'    => 'required|string|max:1000',
            'notes'       => 'nullable|string|max:500',
        ]);

        $start = Carbon::parse($validated['date'] . ' ' . $validated['start_time']);
        $end   = Carbon::parse($validated['date'] . ' ' . $validated['end_time']);
        
        if ($end->lt($start)) {
            $end->addDay();
        }
        $durationMinutes = max(1, $start->diffInMinutes($end));

        $updateData = [
            'project_id'       => $validated['project_id'] ?? null,
            'task_id'          => $validated['task_id'] ?? null,
            'date'             => $validated['date'],
            'start_time'       => $validated['start_time'],
            'end_time'         => $validated['end_time'],
            'duration_minutes' => $durationMinutes,
            'category'         => $validated['category'],
            'activity'         => $validated['activity'],
            'notes'            => $validated['notes'] ?? null,
        ];

        if ($isLead && !empty($validated['engineer_id'])) {
            $updateData['user_id'] = $validated['engineer_id'];
        }

        $timesheet->update($updateData);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Log aktivitas berhasil diperbarui!',
                'data'    => $timesheet->fresh(['user', 'project', 'task']),
            ]);
        }

        return redirect()->route('timesheets.index')->with('success', 'Log aktivitas berhasil diperbarui!');
    }

    /**
     * Remove the specified timesheet.
     */
    public function destroy(Request $request, Timesheet $timesheet)
    {
        $user = auth()->user();
        $isLead = $user->hasRole('Lead Engineer');

        if (!$isLead && $timesheet->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $timesheet->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Log aktivitas berhasil dihapus!',
            ]);
        }

        return redirect()->route('timesheets.index')->with('success', 'Log aktivitas berhasil dihapus!');
    }

    /**
     * Export timesheets to Excel.
     */
    public function exportExcel(Request $request, TimesheetExportService $exportService)
    {
        try {
            $user = auth()->user();
            $isLead = $user->hasRole('Lead Engineer');

            $engineerId = $request->get('engineer_id');
            $projectId  = $request->get('project_id');
            $category   = $request->get('category');
            $dateStart  = $request->get('date_start');
            $dateEnd    = $request->get('date_end');

            $timesheets = Timesheet::with(['user', 'project', 'task'])
                ->when(!$isLead, function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                })
                ->when($isLead && $engineerId, function ($q) use ($engineerId) {
                    return $q->where('user_id', $engineerId);
                })
                ->when($projectId, function ($q) use ($projectId) {
                    return $q->where('project_id', $projectId);
                })
                ->when($category, function ($q) use ($category) {
                    return $q->where('category', $category);
                })
                ->when($dateStart, function ($q) use ($dateStart) {
                    return $q->whereDate('date', '>=', $dateStart);
                })
                ->when($dateEnd, function ($q) use ($dateEnd) {
                    return $q->whereDate('date', '<=', $dateEnd);
                })
                ->orderBy('date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();

            $filters = $this->buildFilterLabels($engineerId, $projectId, $dateStart, $dateEnd, $user, $isLead);

            $spreadsheet = $exportService->generateExcel($timesheets, $filters);
            $filename = 'Timesheet_IPNet_' . date('Y-m-d_His') . '.xlsx';

            $response = new StreamedResponse(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            });

            $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor Excel: ' . $e->getMessage());
        }
    }

    /**
     * Export timesheets to PDF.
     */
    public function exportPdf(Request $request, TimesheetExportService $exportService)
    {
        try {
            $user = auth()->user();
            $isLead = $user->hasRole('Lead Engineer');

            $engineerId = $request->get('engineer_id');
            $projectId  = $request->get('project_id');
            $category   = $request->get('category');
            $dateStart  = $request->get('date_start');
            $dateEnd    = $request->get('date_end');

            $timesheets = Timesheet::with(['user', 'project', 'task'])
                ->when(!$isLead, function ($q) use ($user) {
                    return $q->where('user_id', $user->id);
                })
                ->when($isLead && $engineerId, function ($q) use ($engineerId) {
                    return $q->where('user_id', $engineerId);
                })
                ->when($projectId, function ($q) use ($projectId) {
                    return $q->where('project_id', $projectId);
                })
                ->when($category, function ($q) use ($category) {
                    return $q->where('category', $category);
                })
                ->when($dateStart, function ($q) use ($dateStart) {
                    return $q->whereDate('date', '>=', $dateStart);
                })
                ->when($dateEnd, function ($q) use ($dateEnd) {
                    return $q->whereDate('date', '<=', $dateEnd);
                })
                ->orderBy('date', 'asc')
                ->orderBy('start_time', 'asc')
                ->get();

            $filters = $this->buildFilterLabels($engineerId, $projectId, $dateStart, $dateEnd, $user, $isLead);

            $pdf = $exportService->generatePdf($timesheets, $filters);
            $filename = 'Timesheet_IPNet_' . date('Y-m-d_His') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }

    /**
     * Helper to build human-readable filter descriptions for export headers.
     */
    private function buildFilterLabels($engineerId, $projectId, $dateStart, $dateEnd, $user, $isLead): array
    {
        $engineerName = 'Semua Engineer';
        if (!$isLead) {
            $engineerName = $user->name;
        } elseif ($engineerId) {
            $eng = User::find($engineerId);
            if ($eng) $engineerName = $eng->name;
        }

        $projectName = 'Semua Project';
        if ($projectId) {
            $p = Project::find($projectId);
            if ($p) $projectName = $p->name;
        }

        $periodText = 'Semua Waktu';
        if ($dateStart && $dateEnd) {
            $periodText = Carbon::parse($dateStart)->format('d/m/Y') . ' s/d ' . Carbon::parse($dateEnd)->format('d/m/Y');
        } elseif ($dateStart) {
            $periodText = 'Mulai ' . Carbon::parse($dateStart)->format('d/m/Y');
        } elseif ($dateEnd) {
            $periodText = 'Hingga ' . Carbon::parse($dateEnd)->format('d/m/Y');
        }

        return [
            'engineer_name' => $engineerName,
            'project_name'  => $projectName,
            'period_text'   => $periodText,
        ];
    }
}
