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
        $user         = auth()->user();
        $isSusanto    = str_contains(strtolower($user->name ?? ''), 'susanto') || $user->hasAnyRole(['Division Head', 'Head Divisi', 'Group Leader', 'HD / Direktur', 'Group Leader Delivery & Operation', 'Group Leader Commercial & Solution']);
        $isHariyadi   = str_contains(strtolower($user->name ?? ''), 'hariyadi') || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur']);
        $isExecutive  = $isSusanto || $isHariyadi || \App\Helpers\ScopeHelper::isExecutive($user) || \App\Helpers\ScopeHelper::isGroupLeader($user);
        $isLead       = \App\Helpers\ScopeHelper::isManagerial($user);
        $scopeIds     = \App\Helpers\ScopeHelper::getScopeUserIds($user);

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

        if ($isExecutive) {
            $scopeIds = $executiveTeamUserIds;
        }

        // Filter Inputs
        $engineerId = $request->get('engineer_id');
        $projectId  = $request->get('project_id');
        $category   = $request->get('category');
        $dateStart  = $request->get('date_start');
        $dateEnd    = $request->get('date_end');
        $search     = $request->get('search');

        // Base Query
        $query = Timesheet::with(['user', 'project', 'task'])
            ->when($scopeIds !== null, function ($q) use ($scopeIds) {
                return $q->whereIn('user_id', $scopeIds);
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

        $timesheets = $query->orderBy('date', 'desc')->orderBy('start_time', 'desc')->paginate(10)->withQueryString();

        // Summary Calculations (Based on current user context)
        $statQuery = Timesheet::query()
            ->when($scopeIds !== null, fn($q) => $q->whereIn('user_id', $scopeIds));

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
            $engineers = $executiveTeamUsers;
            $myTasks = collect([]);
        } else {
            $projects = Project::orderBy('name')->get();
            $engineers = $isLead ? \App\Helpers\ScopeHelper::getAssignableEngineers($user) : collect();
            $myTasks = Task::when(!$isLead, fn($q) => $q->where('engineer_id', $user->id))->orderBy('title')->get();
        }

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
        $isLead = \App\Helpers\ScopeHelper::isManagerial($user);

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

        // Kirim notifikasi Timesheet
        if ($isLead && $assignedUserId != $user->id) {
            // Notifikasi ke engineer jika dibuatkan oleh lead
            \App\Models\Notification::create([
                'user_id' => (int) $assignedUserId,
                'title'   => 'Log Timesheet Dicatat oleh ' . $user->name,
                'message' => 'Aktivitas "' . $validated['activity'] . '" telah dicatat untuk Anda pada tanggal ' . $validated['date'] . ' (Kategori: ' . $validated['category'] . ').',
                'url'     => route('timesheets.index'),
                'is_read' => false,
            ]);
        } elseif (!$isLead) {
            // Notifikasi ke Lead Engineer jika dicatat oleh engineer biasa
            $leads = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['Direktur', 'Lead Engineer', 'Lead Divisi', 'Team Leader', 'Group Leader']))->get();
            foreach ($leads as $lead) {
                if ($lead->id !== $user->id) {
                    \App\Models\Notification::create([
                        'user_id' => $lead->id,
                        'title'   => 'Log Timesheet Baru: ' . $user->name,
                        'message' => 'Engineer ' . $user->name . ' telah mencatat aktivitas "' . $validated['activity'] . '" (' . round($durationMinutes / 60, 1) . ' jam) pada tanggal ' . $validated['date'] . '.',
                        'url'     => route('timesheets.index', ['engineer_id' => $user->id]),
                        'is_read' => false,
                    ]);
                }
            }
        }

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
        $isLead = \App\Helpers\ScopeHelper::isManagerial($user);

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
        $isLead = \App\Helpers\ScopeHelper::isManagerial($user);

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
        abort_unless(\App\Helpers\ScopeHelper::isManagerial(auth()->user()), 403, 'Hanya Team Leader dan Manajemen yang berhak mengekspor laporan timesheet.');

        try {
            $user = auth()->user();
            $isLead = \App\Helpers\ScopeHelper::isManagerial($user);

            $engineerId = $request->get('engineer_id');
            $projectId  = $request->get('project_id');
            $category   = $request->get('category');
            $dateStart  = $request->get('date_start');
            $dateEnd    = $request->get('date_end');

            $scopeIds = \App\Helpers\ScopeHelper::getScopeUserIds($user);

            $timesheets = Timesheet::with(['user', 'project', 'task'])
                ->when($scopeIds !== null, function ($q) use ($scopeIds) {
                    return $q->whereIn('user_id', $scopeIds);
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
        abort_unless(\App\Helpers\ScopeHelper::isManagerial(auth()->user()), 403, 'Hanya Team Leader dan Manajemen yang berhak mengekspor laporan timesheet.');

        try {
            $user = auth()->user();
            $isLead = \App\Helpers\ScopeHelper::isManagerial($user);

            $engineerId = $request->get('engineer_id');
            $projectId  = $request->get('project_id');
            $category   = $request->get('category');
            $dateStart  = $request->get('date_start');
            $dateEnd    = $request->get('date_end');

            $scopeIds = \App\Helpers\ScopeHelper::getScopeUserIds($user);

            $timesheets = Timesheet::with(['user', 'project', 'task'])
                ->when($scopeIds !== null, function ($q) use ($scopeIds) {
                    return $q->whereIn('user_id', $scopeIds);
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
