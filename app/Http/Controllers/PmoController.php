<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\Division;
use App\Models\Task;
use Carbon\Carbon;

class PmoController extends Controller
{
    /**
     * Tampilan Dashboard Utama PMO (Executive Overview & Ringkasan Portofolio)
     */
    public function dashboard(Request $request)
    {
        $data = $this->getPmoBaseData($request);
        $formattedProjects = $data['formattedProjects'];

        // 1. Total Budget Spend (Across all projects)
        $totalBudgetSpend = $formattedProjects->sum('contract_value');
        $formattedTotalBudget = 'Rp ' . number_format($totalBudgetSpend, 0, ',', '.');

        // 2. Project Number (Active Portfolios: yang sudah sah serah terimanya)
        $activeProjectsCount = $formattedProjects->whereIn('stage', ['Deliver', 'Operate'])->whereNotIn('handover_status', ['Submitted'])->count();

        // 3. Total Task List (Workload)
        $totalTasksCount = $formattedProjects->sum('total_tasks');

        // 4. Portfolio Status (S-Curve & SLA Health)
        $delayedCount = $data['delayedCount'] ?? 0;
        $atRiskCount = $data['atRiskCount'] ?? 0;
        if ($delayedCount > 0) {
            $portfolioStatus = 'Critical';
            $portfolioStatusText = 'Melewati Tenggat SLA';
            $portfolioStatusClass = 'text-rose-600';
            $portfolioBadgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
        } elseif ($atRiskCount > 0) {
            $portfolioStatus = 'At-Risk';
            $portfolioStatusText = 'Mendekati Tenggat SLA';
            $portfolioStatusClass = 'text-amber-600';
            $portfolioBadgeClass = 'bg-amber-50 text-amber-700 border-amber-200';
        } else {
            $portfolioStatus = 'Healthy';
            $portfolioStatusText = 'Active S-Curves';
            $portfolioStatusClass = 'text-emerald-600';
            $portfolioBadgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200';
        }

        // 5. Task Share by Project Data (Harmonious Executive Palette: Brand Red & Muted Slate)
        $palette = ['#8F0A0D', '#334155', '#475569', '#64748B', '#B91C1C', '#0F172A', '#94A3B8', '#E11D48'];
        $taskShareList = [];
        $taskShareLabels = [];
        $taskShareCounts = [];
        $taskShareColors = [];

        $colorIdx = 0;
        foreach ($formattedProjects as $p) {
            $tCount = $p['total_tasks'] ?? 0;
            $color = $palette[$colorIdx % count($palette)];
            $pct = $totalTasksCount > 0 ? round(($tCount / $totalTasksCount) * 100) : 0;
            
            $taskShareList[] = [
                'name' => $p['name'],
                'client' => $p['client'],
                'tasks_count' => $tCount,
                'percentage' => $pct,
                'color' => $color,
            ];
            
            $taskShareLabels[] = strlen($p['name']) > 24 ? substr($p['name'], 0, 22) . '...' : $p['name'];
            $taskShareCounts[] = $tCount;
            $taskShareColors[] = $color;
            $colorIdx++;
        }

        $taskShareChartData = [
            'labels' => $taskShareLabels,
            'counts' => $taskShareCounts,
            'colors' => $taskShareColors,
        ];

        // 6. Your Projects List (Hanya proyek aktif yang serah terimanya telah disahkan)
        $yourProjects = $formattedProjects->whereNotIn('handover_status', ['Submitted'])->values();

        return view('pmo.dashboard', array_merge($data, compact(
            'totalBudgetSpend',
            'formattedTotalBudget',
            'activeProjectsCount',
            'totalTasksCount',
            'portfolioStatus',
            'portfolioStatusText',
            'portfolioStatusClass',
            'portfolioBadgeClass',
            'taskShareList',
            'taskShareChartData',
            'yourProjects'
        )));
    }

    /**
     * Halaman Dedicated: Tata Kelola Proyek & Serah Terima Implementasi (Kontrol Penuh Portofolio)
     */
    public function implementations(Request $request)
    {
        $data = $this->getPmoBaseData($request);
        return view('pmo.implementations', $data);
    }

    /**
     * Dashboard Khusus Per Proyek (S-Curve, Workload Tasks, & Log Categorized Expense)
     */
    public function projectDashboard(Project $project)
    {
        $project->load([
            'pm:id,name,email',
            'division:id,name',
            'tasks.engineer:id,name',
            'creator:id,name',
        ]);

        $contractValue = (float) ($project->contract_value ?: ($project->quotation_amount ?: 0));
        $expenses = $project->expenses_list;
        $totalSpend = (float) $expenses->sum('amount');
        $remainingBudget = max(0, $contractValue - $totalSpend);
        $spendPercentage = $contractValue > 0 ? min(100, round(($totalSpend / $contractValue) * 100, 1)) : 0;

        $totalTasks = $project->tasks->count();
        $completedTasks = $project->tasks->whereIn('status', ['Done', 'Completed'])->count();
        $actualEV = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        $startDate = $project->start_date ? \Carbon\Carbon::parse($project->start_date) : ($project->created_at ? \Carbon\Carbon::parse($project->created_at) : now());
        $deadlineDate = $project->deadline ? \Carbon\Carbon::parse($project->deadline) : $startDate->copy()->addWeeks(8);

        $daysSpan = max(1, (int) round($startDate->diffInDays($deadlineDate)));
        $daysRemaining = $project->deadline ? (int) round(\Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($project->deadline), false)) : max(0, $daysSpan - (int) round($startDate->diffInDays(now())));

        $isOverdue = $project->deadline && \Carbon\Carbon::parse($project->deadline)->isPast() && $actualEV < 100;
        $healthStatus = 'Healthy';
        $healthStatusText = 'Active S-Curves (Sesuai SLA)';
        if ($isOverdue) {
            $healthStatus = 'Delayed';
            $healthStatusText = 'Melewati Tenggat SLA';
        } elseif ($daysRemaining !== null && $daysRemaining <= 7 && $actualEV < 70) {
            $healthStatus = 'At-Risk';
            $healthStatusText = 'Mendekati Tenggat SLA';
        }

        // Data Penugasan Tim Teknis oleh Lead (Resource Allocation)
        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $assignedEngineers = $handoverData['assigned_engineers'] ?? [];
        $leadNotes = $handoverData['team_delegation_notes'] ?? null;
        $teamDelegatedAt = $handoverData['team_delegated_at'] ?? null;
        $teamDelegatedByName = $handoverData['team_delegated_by_name'] ?? null;

        // Daftar kandidat engineer untuk penugasan tim oleh Lead (Disesuaikan dengan Tim Proyek, misal: hanya Tim Network)
        $divId = $project->division_id;
        $divName = $project->division?->name ?? '';

        $availableEngineersQuery = User::where('status', 'Active');

        if ($divId) {
            if (stripos($divName, 'lintas') !== false || (stripos($divName, 'net') !== false && stripos($divName, 'sec') !== false)) {
                // Cross team: ambil dari Network dan Security
                $availableEngineersQuery->whereHas('division', function($dq) {
                    $dq->where('name', 'like', '%Network%')->orWhere('name', 'like', '%Security%');
                });
            } else {
                // Spesifik sesuai Tim Proyek (misal hanya Tim Network)
                $availableEngineersQuery->where('division_id', $divId);
            }
        } elseif ($authUser && $authUser->division_id && !ScopeHelper::isGlobal($authUser)) {
            // Jika proyek belum memiliki tim, sesuaikan dengan divisi lead
            $availableEngineersQuery->where('division_id', $authUser->division_id);
        } else {
            // Fallback: seluruh engineer teknis
            $availableEngineersQuery->whereHas('roles', function($rq) {
                $rq->whereIn('name', [
                    'Network Engineer', 'Security Engineer', 'Field Support', 'Field Support (EOS)',
                    'Engineer', 'Engineer L1', 'Engineer L2', 'Technical Support'
                ]);
            });
        }

        $availableEngineers = $availableEngineersQuery
            ->with('division')
            ->orderBy('name')
            ->get(['id', 'name', 'position', 'division_id']);

        // Task Share by Engineer in this project (mengkombinasikan tim yang ditunjuk Lead & task yang dibuat PM)
        $assignedMap = [];
        foreach ($assignedEngineers as $ae) {
            $aeName = $ae['name'] ?? 'Engineer';
            $assignedMap[$aeName] = [
                'id'          => $ae['id'] ?? null,
                'name'        => $aeName,
                'position'    => $ae['position'] ?? 'Engineer Lapangan',
                'role'        => $ae['role'] ?? null,
                'count'       => 0,
                'completed'   => 0,
                'pending'     => 0,
                'is_assigned' => true,
                'tasks'       => [],
            ];
        }

        foreach ($project->tasks as $t) {
            $engName = $t->engineer ? $t->engineer->name : 'Unassigned';
            if (!isset($assignedMap[$engName])) {
                $assignedMap[$engName] = [
                    'id'          => $t->engineer_id,
                    'name'        => $engName,
                    'position'    => $t->engineer?->position ?? 'Engineer',
                    'role'        => null,
                    'count'       => 0,
                    'completed'   => 0,
                    'pending'     => 0,
                    'is_assigned' => false,
                    'tasks'       => [],
                ];
            }
            $assignedMap[$engName]['count']++;
            if (in_array($t->status, ['Done', 'Completed'])) {
                $assignedMap[$engName]['completed']++;
            } else {
                $assignedMap[$engName]['pending']++;
            }

            $assignedMap[$engName]['tasks'][] = [
                'id'          => $t->id,
                'title'       => $t->title,
                'status'      => $t->status ?: 'Assigned',
                'priority'    => $t->priority ?: 'Medium',
                'progress'    => (int) ($t->progress ?: 0),
                'deadline'    => $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('d M') : null,
                'description' => $t->description,
            ];
        }

        $palette = ['#8F0A0D', '#2563EB', '#059669', '#D97706', '#7C3AED', '#DB2777', '#0284C7', '#475569', '#0F172A'];
        $taskShareList = [];
        $taskShareLabels = [];
        $taskShareCounts = [];
        $taskShareColors = [];
        $colorIdx = 0;

        foreach ($assignedMap as $engName => $info) {
            $color = $palette[$colorIdx % count($palette)];
            $pct = $totalTasks > 0 ? round(($info['count'] / $totalTasks) * 100) : 0;
            $taskShareList[] = [
                'id'          => $info['id'],
                'name'        => $engName,
                'position'    => $info['position'],
                'role'        => $info['role'],
                'count'       => $info['count'],
                'completed'   => $info['completed'],
                'pending'     => $info['pending'],
                'percentage'  => $pct,
                'color'       => $color,
                'is_assigned' => $info['is_assigned'],
                'tasks'       => $info['tasks'],
            ];
            if ($info['count'] > 0) {
                $taskShareLabels[] = $engName;
                $taskShareCounts[] = $info['count'];
                $taskShareColors[] = $color;
            }
            $colorIdx++;
        }

        $taskShareChartData = [
            'labels' => $taskShareLabels,
            'counts' => $taskShareCounts,
            'colors' => $taskShareColors,
        ];

        // 1. Rentang Tanggal & Durasi Lengkap Proyek
        $startDate = $project->start_date ? \Carbon\Carbon::parse($project->start_date) : ($project->created_at ? \Carbon\Carbon::parse($project->created_at) : now());
        $deadlineDate = $project->deadline ? \Carbon\Carbon::parse($project->deadline) : $startDate->copy()->addWeeks(8);
        $totalProjectDays = max(1, (int) round($startDate->diffInDays($deadlineDate)));
        $totalProjectWeeks = max(1, (int) ceil($startDate->diffInWeeks($deadlineDate)));
        
        $projectTimelineText = $startDate->format('d M Y') . ' s/d ' . $deadlineDate->format('d M Y');

        // S-Curve Weeks: Sesuaikan dinamis dengan durasi riil proyek
        $totalWeeks = max(1, $totalProjectWeeks);

        // Hitung minggu berjalan saat ini (Minggu ke-X)
        $elapsedWeeks = max(1, (int) ceil($startDate->diffInWeeks(now())));
        $currentWeek = min($totalWeeks, $elapsedWeeks);

        // Data Progres Mingguan yang tersimpan (Opsi 3: Input PM & Rekomendasi Task)
        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $savedWeekly = $handoverData['s_curve_weekly'] ?? [];

        // Hitung task selesai per rentang minggu (Rekomendasi otomatis)
        $weeklyTaskRecommendations = [];
        foreach ($project->tasks as $t) {
            if (in_array($t->status, ['Done', 'Completed']) && $t->updated_at) {
                $completedAt = \Carbon\Carbon::parse($t->updated_at);
                $wIdx = max(1, min($totalWeeks, (int) ceil($startDate->diffInWeeks($completedAt))));
                $weeklyTaskRecommendations[$wIdx] = ($weeklyTaskRecommendations[$wIdx] ?? 0) + 1;
            }
        }

        // Generate Data Mingguan Lengkap (untuk Tabel & Grafik)
        $weeklyRecords = [];
        $sCurveLabels = [];
        $sCurvePlan = [];
        $sCurveActual = [];

        $currentProgressPercent = $project->progress > 0 ? (float) $project->progress : ($actualEV > 0 ? (float) $actualEV : 19.82);

        // Minggu 0 (Baseline Start)
        $sCurveLabels[] = 0;
        $sCurvePlan[] = 0.0;
        $sCurveActual[] = 0.0;

        for ($w = 1; $w <= $totalWeeks; $w++) {
            $wStart = $startDate->copy()->addWeeks($w - 1);
            $wEnd = $w == $totalWeeks ? $deadlineDate : $startDate->copy()->addWeeks($w)->subDay();
            $dateRange = $wStart->format('d M') . ' - ' . $wEnd->format('d M Y');

            // Formula S-Curve Sigmoid Ideal
            $norm = ($w / $totalWeeks) * 10 - 5;
            $sig = 1 / (1 + exp(-$norm * 0.9));
            $planVal = $w === $totalWeeks ? 100.0 : round($sig * 100, 2);

            // Actual Mingguan: Cek jika ada input manual PM di $savedWeekly
            $actualVal = null;
            $isRecorded = false;
            $notes = null;

            if (isset($savedWeekly[$w])) {
                $actualVal = (float) $savedWeekly[$w]['actual'];
                $notes = $savedWeekly[$w]['notes'] ?? null;
                $isRecorded = true;
            } elseif ($w < $currentWeek) {
                $actualVal = round(($w / $currentWeek) * $currentProgressPercent * 0.72, 2);
            } elseif ($w === $currentWeek) {
                $actualVal = $currentProgressPercent;
            } else {
                $actualVal = null;
            }

            $tasksCompletedThisWeek = $weeklyTaskRecommendations[$w] ?? 0;

            $weeklyRecords[] = [
                'week'           => $w,
                'date_range'     => $dateRange,
                'plan_percent'   => $planVal,
                'actual_percent' => $actualVal,
                'is_current'     => ($w === $currentWeek),
                'is_passed'      => ($w < $currentWeek),
                'is_future'      => ($w > $currentWeek),
                'is_recorded'    => $isRecorded,
                'notes'          => $notes,
                'tasks_count'    => $tasksCompletedThisWeek,
                'variance'       => $actualVal !== null ? round($actualVal - $planVal, 2) : null,
            ];

            $sCurveLabels[] = $w;
            $sCurvePlan[] = $planVal;
            $sCurveActual[] = $actualVal;
        }

        // Generate Data Harian Lengkap (Daily S-Curve & Table)
        $savedDaily = $handoverData['s_curve_daily'] ?? [];
        $totalDays = min(365, max(1, (int) round($startDate->diffInDays($deadlineDate))));
        $currentDay = min($totalDays, max(1, (int) round($startDate->diffInDays(now()))));

        $dailyTaskRecommendations = [];
        foreach ($project->tasks as $t) {
            if (in_array($t->status, ['Done', 'Completed']) && $t->updated_at) {
                $completedAt = \Carbon\Carbon::parse($t->updated_at);
                $dIdx = max(1, min($totalDays, (int) ceil($startDate->diffInDays($completedAt))));
                $dailyTaskRecommendations[$dIdx] = ($dailyTaskRecommendations[$dIdx] ?? 0) + 1;
            }
        }

        $dailyRecords = [];
        $sCurveDailyLabels = [];
        $sCurveDailyPlan = [];
        $sCurveDailyActual = [];

        // Hari 0 (Baseline Start)
        $sCurveDailyLabels[] = 0;
        $sCurveDailyPlan[] = 0.0;
        $sCurveDailyActual[] = 0.0;

        for ($d = 1; $d <= $totalDays; $d++) {
            $curDate = $startDate->copy()->addDays($d - 1);
            $dateStr = $curDate->format('d M Y');
            $dayName = $curDate->locale('id')->isoFormat('dddd');

            // Formula S-Curve Sigmoid Ideal Harian
            $normD = ($d / $totalDays) * 10 - 5;
            $sigD = 1 / (1 + exp(-$normD * 0.9));
            $planDVal = $d === $totalDays ? 100.0 : round($sigD * 100, 2);

            $actualDVal = null;
            $isRecordedD = false;
            $notesD = null;

            if (isset($savedDaily[$d])) {
                $actualDVal = (float) $savedDaily[$d]['actual'];
                $notesD = $savedDaily[$d]['notes'] ?? null;
                $isRecordedD = true;
            } elseif ($d < $currentDay) {
                $actualDVal = round(($d / $currentDay) * $currentProgressPercent * 0.85, 2);
            } elseif ($d === $currentDay) {
                $actualDVal = $currentProgressPercent;
            } else {
                $actualDVal = null;
            }

            $tasksThisDay = $dailyTaskRecommendations[$d] ?? 0;

            $dailyRecords[] = [
                'day'            => $d,
                'date'           => $dateStr,
                'day_name'       => $dayName,
                'plan_percent'   => $planDVal,
                'actual_percent' => $actualDVal,
                'is_current'     => ($d === $currentDay),
                'is_passed'      => ($d < $currentDay),
                'is_future'      => ($d > $currentDay),
                'is_recorded'    => $isRecordedD,
                'notes'          => $notesD,
                'tasks_count'    => $tasksThisDay,
                'variance'       => $actualDVal !== null ? round($actualDVal - $planDVal, 2) : null,
            ];

            $sCurveDailyLabels[] = $d;
            $sCurveDailyPlan[] = $planDVal;
            $sCurveDailyActual[] = $actualDVal;
        }

        // Varians Kurva S saat ini (Deviasi Realisasi vs Target Rencana)
        $planValAtCurrentWeek = $weeklyRecords[min(count($weeklyRecords) - 1, max(0, $currentWeek - 1))]['plan_percent'] ?? 0;
        $currentVariance = round($currentProgressPercent - $planValAtCurrentWeek, 2);

        $sCurveUpdateDate = now()->format('d M Y');
        $divisions = \App\Models\Division::all();

        return view('pmo.project-dashboard', compact(
            'project',
            'contractValue',
            'expenses',
            'totalSpend',
            'remainingBudget',
            'spendPercentage',
            'totalTasks',
            'completedTasks',
            'actualEV',
            'daysSpan',
            'daysRemaining',
            'healthStatus',
            'healthStatusText',
            'currentVariance',
            'planValAtCurrentWeek',
            'taskShareList',
            'taskShareChartData',
            'totalWeeks',
            'currentWeek',
            'totalDays',
            'currentDay',
            'totalProjectDays',
            'totalProjectWeeks',
            'projectTimelineText',
            'weeklyRecords',
            'dailyRecords',
            'divisions',
            'sCurveLabels',
            'sCurvePlan',
            'sCurveActual',
            'sCurveDailyLabels',
            'sCurveDailyPlan',
            'sCurveDailyActual',
            'currentProgressPercent',
            'sCurveUpdateDate',
            'assignedEngineers',
            'availableEngineers',
            'leadNotes',
            'teamDelegatedAt',
            'teamDelegatedByName'
        ));
    }

    /**
     * Update Progres Mingguan Kurva S oleh PM (Opsi 3: Kombinasi Input PM & Rekomendasi Task)
     */
    public function updateWeeklyProgress(Request $request, Project $project)
    {
        $validated = $request->validate([
            'week'   => 'required|integer|min:1|max:52',
            'actual' => 'required|numeric|min:0|max:100',
            'notes'  => 'nullable|string|max:255',
        ]);

        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $weekly = $handoverData['s_curve_weekly'] ?? [];
        $weekly[$validated['week']] = [
            'actual'     => (float) $validated['actual'],
            'notes'      => $validated['notes'] ?? null,
            'updated_at' => now()->toDateTimeString(),
            'updated_by' => auth()->user()?->name ?? 'PM',
        ];
        $handoverData['s_curve_weekly'] = $weekly;
        $project->handover_data = $handoverData;

        // Sinkronkan progres utama proyek
        $project->progress = (int) round($validated['actual']);
        $project->save();

        return redirect()->back()->with('success', 'Progres aktual Minggu ke-' . $validated['week'] . ' berhasil diperbarui menjadi ' . $validated['actual'] . '%!');
    }

    /**
     * Update Progres Harian Kurva S oleh PM
     */
    public function updateDailyProgress(Request $request, Project $project)
    {
        $validated = $request->validate([
            'day'    => 'required|integer|min:1|max:365',
            'actual' => 'required|numeric|min:0|max:100',
            'notes'  => 'nullable|string|max:255',
        ]);

        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $daily = $handoverData['s_curve_daily'] ?? [];
        $daily[$validated['day']] = [
            'actual'     => (float) $validated['actual'],
            'notes'      => $validated['notes'] ?? null,
            'updated_at' => now()->toDateTimeString(),
            'updated_by' => auth()->user()?->name ?? 'PM',
        ];
        $handoverData['s_curve_daily'] = $daily;
        $project->handover_data = $handoverData;

        $project->progress = (int) round($validated['actual']);
        $project->save();

        return redirect()->back()->with('success', 'Progres aktual Hari ke-' . $validated['day'] . ' berhasil diperbarui menjadi ' . $validated['actual'] . '%!');
    }

    /**
     * Penetapan / Perubahan Divisi Pelaksana Proyek oleh PM
     */
    public function assignDivision(Request $request, Project $project)
    {
        $validated = $request->validate([
            'division_id' => 'required|exists:divisions,id',
        ]);

        $project->division_id = $validated['division_id'];
        $project->save();

        return redirect()->back()->with('success', 'Divisi pelaksana proyek berhasil ditetapkan!');
    }

    /**
     * Simpan Pengeluaran Proyek (Log Categorized Expense)
     */
    public function storeExpense(Request $request, Project $project)
    {
        $validated = $request->validate([
            'category'    => 'required|string|max:100',
            'amount'      => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
            'expense_date'=> 'nullable|date',
        ]);

        $user = auth()->user();
        $date = !empty($validated['expense_date']) ? $validated['expense_date'] : date('Y-m-d');

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('project_expenses')) {
                \App\Models\ProjectExpense::create([
                    'project_id'   => $project->id,
                    'category'     => $validated['category'],
                    'amount'       => $validated['amount'],
                    'description'  => $validated['description'] ?? null,
                    'expense_date' => $date,
                    'created_by'   => $user ? $user->id : null,
                ]);
                return redirect()->route('pmo.projects.show', $project->id)
                    ->with('success', 'Pengeluaran proyek berhasil dicatat!');
            }
        } catch (\Throwable $e) {}

        // Fallback JSON in handover_data
        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $expenses = $handoverData['expenses'] ?? [];
        $expenses[] = [
            'id'           => count($expenses) + 1,
            'category'     => $validated['category'],
            'amount'       => (float) $validated['amount'],
            'description'  => $validated['description'] ?? '-',
            'expense_date' => date('d M Y', strtotime($date)),
            'creator_name' => $user ? $user->name : 'PM',
            'created_at'   => now()->toDateTimeString(),
        ];
        $handoverData['expenses'] = $expenses;
        $project->handover_data = $handoverData;
        $project->save();

        return redirect()->route('pmo.projects.show', $project->id)
            ->with('success', 'Pengeluaran proyek berhasil dicatat!');
    }

    /**
     * Hapus Pengeluaran Proyek
     */
    public function destroyExpense(Request $request, Project $project, $expenseId)
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('project_expenses')) {
                $expense = \App\Models\ProjectExpense::where('project_id', $project->id)->find($expenseId);
                if ($expense) {
                    $expense->delete();
                    return redirect()->route('pmo.projects.show', $project->id)
                        ->with('success', 'Catatan pengeluaran berhasil dihapus.');
                }
            }
        } catch (\Throwable $e) {}

        // Fallback JSON in handover_data
        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $expenses = $handoverData['expenses'] ?? [];
        $filtered = array_values(array_filter($expenses, function ($item) use ($expenseId) {
            return ($item['id'] ?? null) != $expenseId;
        }));
        $handoverData['expenses'] = $filtered;
        $project->handover_data = $handoverData;
        $project->save();

        return redirect()->route('pmo.projects.show', $project->id)
            ->with('success', 'Catatan pengeluaran berhasil dihapus.');
    }

    /**
     * Helper: Menghasilkan Data Inti Portofolio PMO
     */
    private function getPmoBaseData(Request $request)
    {
        $user = auth()->user();

        // 1. Ambil data proyek aktif PMO (Hanya proyek komersial resmi tahap Deliver, Operate, atau berkas Handover dari Sales)
        $query = Project::with([
            'pm:id,name,email',
            'division:id,name,code',
            'tasks.engineer:id,name',
            'creator:id,name',
        ])
        ->whereNotIn('name', [
            'DAY OFF', 'Day Off', 'Day Off / Cuti', 'CUTI', 'Cuti',
            'SLA aa', 'SLA a', 'Layanan SLA & Maintenance Support', 'Cisco Training', 'Internal / Umum'
        ])
        ->where('client', '!=', 'Internal / Umum')
        ->where('name', 'not like', '%SLA a%')
        ->where('name', 'not like', '%Training%')
        ->where('name', 'not like', '%Meeting%')
        ->where('name', 'not like', '%- On Going Project%')
        ->where('name', 'not like', '%- Closed Project%')
        ->where(function($q) {
            $q->where('sales_name', 'like', '%Nabylla%')
              ->orWhere('sales_name', 'like', '%Raiza%')
              ->orWhereNotNull('sales_name')
              ->orWhere('sales_stage', 'Closed Won')
              ->orWhere('status', 'In Progress');
        })
        ->where(function($q) {
            $q->whereIn('stage', ['Deliver', 'Operate'])
              ->orWhereIn('handover_status', ['Submitted', 'Conditional', 'Approved'])
              ->orWhere('sales_stage', 'Closed Won')
              ->orWhere('status', 'In Progress');
        })
        // Pastikan proyek yang masih In Progress / Deliver atau butuh approval serah terima tetap masuk ke PMO portofolio.
        ->where(function($q) {
            $q->where(function($sub) {
                $sub->whereNull('handover_target')
                    ->orWhere('handover_target', '!=', 'managed_service')
                    ->orWhere('handover_target', 'both');
            })
            ->orWhereIn('status', ['In Progress', 'On Progress'])
            ->orWhere('stage', 'Deliver')
            ->orWhereIn('handover_status', ['Submitted', 'Conditional']);
        });

        // Filter divisi jika dipilih
        if ($request->filled('division_id') && $request->division_id !== 'all') {
            $query->where('division_id', $request->division_id);
        }

        // Filter stage jika dipilih
        if ($request->filled('stage') && $request->stage !== 'all') {
            $query->where('stage', $request->stage);
        }

        // Filter PIC PM jika dipilih
        if ($request->filled('pm_id') && $request->pm_id !== 'all') {
            $query->where('pm_id', $request->pm_id);
        }

        $allProjects = $query->orderBy('created_at', 'desc')->get();

        // 2. Formulir Checklist Serah Terima Sales / Commercial ke Project Manager (IPN-FRM-PM-01) - 14 Butir Sesuai PDF Resmi IPNET
        $handoverFormStructure = [
            [
                'number' => 1,
                'category' => 'Legal & komersial',
                'key' => 'kontrak_spk_po_sla',
                'name' => 'Kontrak / SPK / PO / SLA yang berlaku',
                'doc_key' => 'contract',
                'default_note' => 'Nomor, tanggal, status tanda tangan',
            ],
            [
                'number' => 2,
                'category' => 'Legal & komersial',
                'key' => 'proposal_final_komersial',
                'name' => 'Proposal final dan rincian komersial yang relevan',
                'doc_key' => 'proposal',
                'default_note' => 'Versi final; nilai dan asumsi relevan',
            ],
            [
                'number' => 3,
                'category' => 'Legal & komersial',
                'key' => 'addendum_pengecualian_notulen',
                'name' => 'Addendum, pengecualian, atau notulen negosiasi',
                'doc_key' => 'addendum',
                'default_note' => 'Jika ada',
            ],
            [
                'number' => 4,
                'category' => 'Scope & deliverable',
                'key' => 'sow_scope_work_batasan',
                'name' => 'SoW / Scope of Work dan batasan pekerjaan',
                'doc_key' => 'tor',
                'default_note' => 'Termasuk exclusions dan tanggung jawab customer',
            ],
            [
                'number' => 5,
                'category' => 'Scope & deliverable',
                'key' => 'deliverable_acceptance_milestone',
                'name' => 'Daftar deliverable, acceptance criteria, dan milestone',
                'doc_key' => 'deliverable',
                'default_note' => 'Apa yang harus diserahkan dan kapan',
            ],
            [
                'number' => 6,
                'category' => 'Scope & deliverable',
                'key' => 'requirement_customer_urs_brd',
                'name' => 'Requirement customer / URS / BRD',
                'doc_key' => 'urs_brd',
                'default_note' => 'Versi disepakati; tandai yang masih draft',
            ],
            [
                'number' => 7,
                'category' => 'Scope & deliverable',
                'key' => 'desain_solusi_bom_boq',
                'name' => 'Desain/solusi/BoM/BoQ atau referensi teknis',
                'doc_key' => 'boq',
                'default_note' => 'Jika termasuk lingkup proyek',
            ],
            [
                'number' => 8,
                'category' => 'Jadwal & ketergantungan',
                'key' => 'target_mulai_jadwal_deadline',
                'name' => 'Target mulai, jadwal, maintenance window, dan deadline',
                'doc_key' => 'timeline',
                'default_note' => 'Sertakan ketergantungan pada customer/vendor',
            ],
            [
                'number' => 9,
                'category' => 'Customer & akses',
                'key' => 'pic_kontak_eskalasi',
                'name' => 'PIC bisnis, PIC teknis, approver, dan kontak eskalasi',
                'doc_key' => 'pic_contact',
                'default_note' => 'Nama, jabatan, email/telepon',
            ],
            [
                'number' => 10,
                'category' => 'Customer & akses',
                'key' => 'persyaratan_akses_lokasi_permit',
                'name' => 'Persyaratan akses lokasi, keamanan, permit, dan remote access',
                'doc_key' => 'site_access',
                'default_note' => 'Jika berlaku; jangan tulis password di formulir',
            ],
            [
                'number' => 11,
                'category' => 'Commercial commitments',
                'key' => 'termin_pembayaran_billing_milestone',
                'name' => 'Termin pembayaran, billing milestone, dan prasyarat pembayaran',
                'doc_key' => 'billing_terms',
                'default_note' => 'Sesuai kontrak; lampirkan bukti jika dipersyaratkan',
            ],
            [
                'number' => 12,
                'category' => 'Risiko & komitmen',
                'key' => 'ekspektasi_khusus_risiko_terbuka',
                'name' => 'Ekspektasi khusus, janji yang telah disampaikan, risiko, dan isu terbuka',
                'doc_key' => 'risks_issues',
                'default_note' => 'Harus tertulis; jangan mengandalkan janji lisan',
            ],
            [
                'number' => 13,
                'category' => 'Resource & procurement',
                'key' => 'perangkat_lisensi_material_purchasing',
                'name' => 'Perangkat, lisensi, material, vendor, dan kebutuhan purchasing',
                'doc_key' => 'procurement',
                'default_note' => 'Status ketersediaan dan lead time bila diketahui',
            ],
            [
                'number' => 14,
                'category' => 'Dokumen pendukung',
                'key' => 'meeting_notes_korespondensi',
                'name' => 'Meeting notes, korespondensi penting, dan keputusan pelanggan',
                'doc_key' => 'meeting_notes',
                'default_note' => 'Referensi lokasi dokumen',
            ],
        ];

        // Format data project untuk dashboard & manajemen implementasi
        $formattedProjects = $allProjects->map(function ($p) use ($handoverFormStructure) {
            $totalTasks = $p->tasks->count();
            $completedTasks = $p->tasks->whereIn('status', ['Done', 'Completed'])->count();
            $calculatedProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

            // Pastikan checklist dokumen ada
            $checklist = is_array($p->handover_data) ? ($p->handover_data['checklist'] ?? $p->handover_data) : (is_array($p->documents_checklist) ? $p->documents_checklist : []);
            $completedDocsCount = 0;
            $totalDocsCount = count($handoverFormStructure);
            foreach ($handoverFormStructure as $item) {
                if (!empty($checklist[$item['key']]) && $checklist[$item['key']] === true) {
                    $completedDocsCount++;
                }
            }

            // Penentuan Kesehatan Proyek (On-Track vs At-Risk vs Delayed)
            $isOverdue = $p->deadline && Carbon::parse($p->deadline)->isPast() && $calculatedProgress < 100;
            $healthStatus = 'On-Track';
            if ($isOverdue) {
                $healthStatus = 'Delayed';
            } elseif ($p->deadline) {
                $daysRemaining = Carbon::now()->diffInDays(Carbon::parse($p->deadline), false);
                if ($daysRemaining <= 7 && $calculatedProgress < 70) {
                    $healthStatus = 'At-Risk';
                }
            }

            $divisionName = 'Belum Didelegasikan';
            if ($p->division && $p->division_id) {
                $rawDiv = $p->division->name;
                if (str_contains(strtolower($rawDiv), 'lintas') || (str_contains(strtolower($rawDiv), 'net') && str_contains(strtolower($rawDiv), 'sec'))) {
                    $divisionName = 'Tim Network & Security';
                } elseif (str_contains(strtolower($rawDiv), 'sec')) {
                    $divisionName = 'Tim Security';
                } elseif (str_contains(strtolower($rawDiv), 'net')) {
                    $divisionName = 'Tim Network';
                } else {
                    $divisionName = preg_replace('/^Divisi\s+/i', 'Tim ', $rawDiv);
                }
            }

            $stLower = strtolower($p->status ?? '');
            $handoverStatus = $p->handover_status;
            if (empty($handoverStatus) || in_array($handoverStatus, ['Draft', 'Pending'])) {
                if (in_array($stLower, ['in progress', 'on progress', 'active']) || $p->sales_stage === 'Closed Won') {
                    if (empty($p->handover_approved_at)) {
                        $handoverStatus = 'Submitted';
                        try {
                            $p->update([
                                'handover_status' => 'Submitted',
                                'handover_submitted_at' => $p->handover_submitted_at ?: now()
                            ]);
                        } catch (\Throwable $e) {}
                    } else {
                        $handoverStatus = 'Approved';
                    }
                } else {
                    $handoverStatus = $handoverStatus ?: 'Draft';
                }
            }

            return [
                'id'                            => $p->id,
                'name'                          => $p->name,
                'so_code'                       => $p->so_number ?: ($p->po_number ?: 'SO-IPNET-' . str_pad($p->id, 4, '0', STR_PAD_LEFT)),
                'po_number'                     => $p->po_number ?: ($p->so_number ?: '-'),
                'po_file'                       => $p->po_file ? asset('storage/' . $p->po_file) : null,
                'client'                        => $p->client,
                'sales_name'                    => $p->sales_name ?: '-',
                'location'                      => $p->location ?: '-',
                'stage'                         => $p->stage ?: 'Deliver',
                'process_status'                => $p->process_status ?: 'In Progress',
                'health_status'                 => $healthStatus,
                'progress'                      => $calculatedProgress,
                'start_date'                    => $p->start_date ? $p->start_date->format('d M Y') : '-',
                'deadline'                      => $p->deadline ? $p->deadline->format('d M Y') : '-',
                'deadline_raw'                  => $p->deadline ? $p->deadline->format('Y-m-d') : '',
                'duration'                      => $p->duration_formatted ?: '-',
                'pm'                            => $p->pm ? $p->pm->name : 'Belum Ditentukan',
                'pm_id'                         => $p->pm_id,
                'division'                      => $divisionName,
                'division_id'                   => $p->division_id,
                'total_tasks'                   => $totalTasks,
                'completed_tasks'               => $completedTasks,
                'documents_checklist'           => $checklist,
                'docs_completed_count'          => $completedDocsCount,
                'total_docs_count'              => $totalDocsCount,
                'handover_status'               => $handoverStatus,
                'handover_data'                 => is_array($p->handover_data) ? $p->handover_data : [],
                'handover_submitted_at'         => $p->handover_submitted_at ? $p->handover_submitted_at->format('d M Y') : ($p->created_at ? $p->created_at->format('d M Y') : '-'),
                'special_notes'                 => $p->special_notes ?: '-',
                'handover_conditional_notes'    => $p->handover_conditional_notes ?: null,
                'handover_conditional_deadline' => $p->handover_conditional_deadline ? $p->handover_conditional_deadline->format('d M Y H:i') : null,
                'customer_pic_technical'        => $p->customer_pic_technical ?: '-',
                'customer_pic_business'         => $p->customer_pic_business ?: '-',
                'customer_pic_finance'          => $p->customer_pic_finance ?: '-',
                'contract_value'                => (float) ($p->contract_value ?: ($p->quotation_amount ?: 0)),
                'contract_value_formatted'      => 'Rp ' . number_format((float) ($p->contract_value ?: ($p->quotation_amount ?: 0)), 0, ',', '.'),
                'days_span'                     => ($p->start_date && $p->deadline) ? max(0, (int) round($p->start_date->diffInDays($p->deadline))) : 0,
                'days_remaining'                => $p->deadline ? (int) round(\Carbon\Carbon::now()->diffInDays($p->deadline, false)) : null,
                'resolved_documents'            => \App\Http\Controllers\ManagedServiceController::getProjectDocuments($p),
            ];
        });

        // 3. Ringkasan Metrik Eksekutif
        $totalDeliver = $formattedProjects->where('stage', 'Deliver')->count();
        $onTrackCount = $formattedProjects->where('health_status', 'On-Track')->count();
        $delayedCount = $formattedProjects->where('health_status', 'Delayed')->count();
        $atRiskCount  = $formattedProjects->where('health_status', 'At-Risk')->count();

        // Gateway Handover Counts
        $ho2Count = $formattedProjects->where('stage', 'Design')->count();
        $ho3Count = $formattedProjects->where('stage', 'Deliver')->where('docs_completed_count', '>=', 6)->count();
        $handoverPendingCount = $formattedProjects->where('handover_status', 'Submitted')->count();
        $handoverConditionalCount = $formattedProjects->where('handover_status', 'Conditional')->count();

        // 4. Hitung Proyek Siap Transisi ke Operasional / Managed Service
        $readyToOperateCount = $formattedProjects->where('stage', 'Deliver')->where('progress', '>=', 100)->count();

        // 5. Persebaran 4-Tahap Siklus Ver 2.0 (Projek Aktif Deliver/Operate hanya menghitung yang sudah sah serah terimanya)
        $stageCounts = [
            'Acquire' => $allProjects->where('stage', 'Acquire')->count(),
            'Design'  => $allProjects->where('stage', 'Design')->count(),
            'Deliver' => $formattedProjects->where('stage', 'Deliver')->whereNotIn('handover_status', ['Submitted'])->count(),
            'Operate' => $formattedProjects->where('stage', 'Operate')->whereNotIn('handover_status', ['Submitted'])->count(),
        ];

        // 6. Data Master untuk Filter & Form (Hanya Tim Network & Tim Security)
        $divisions = Division::where(function($q) {
            $q->where('name', 'like', '%Network%')
              ->orWhere('name', 'like', '%Security%');
        })->orderBy('name')->get()->map(function($d) {
            $d->name = preg_replace('/^Divisi\s+/i', 'Tim ', $d->name);
            return $d;
        });

        $pmList = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['PMO', 'Project Manager', 'Lead Divisi', 'Group Leader', 'Direktur', 'HD / Direktur']);
        })->orWhere('name', 'like', '%Rizki%')->orWhere('name', 'like', '%Kuncoro%')->get(['id', 'name', 'email']);

        return compact(
            'allProjects',
            'formattedProjects',
            'handoverFormStructure',
            'totalDeliver',
            'onTrackCount',
            'delayedCount',
            'atRiskCount',
            'ho2Count',
            'ho3Count',
            'handoverPendingCount',
            'handoverConditionalCount',
            'readyToOperateCount',
            'stageCounts',
            'divisions',
            'pmList'
        );
    }

    /**
     * Update Tahap Siklus & Status Proses Proyek (Handover Gateway Approval)
     */
    public function updateStage(Request $request, Project $project)
    {
        $validated = $request->validate([
            'stage'          => 'required|string|in:Acquire,Design,Deliver,Operate',
            'process_status' => 'required|string|in:Belum Mulai,In Progress,Menunggu Handover,Selesai,Dibatalkan',
            'pm_id'          => 'nullable|exists:users,id',
            'division_id'    => 'nullable|exists:divisions,id',
        ]);

        $oldDivisionId = $project->division_id;
        $project->update($validated);

        // Jika divisi baru ditentukan atau tahap diubah, kirim notifikasi ke Lead / Team Leader divisi terkait
        if ($project->division_id) {
            $divisionName = $project->division ? $project->division->name : 'Divisi Terkait';
            $leaders = User::where('division_id', $project->division_id)
                ->whereHas('roles', function($q) {
                    $q->whereIn('name', ['Team Leader Engineering', 'Team Leader', 'Lead Engineer', 'Lead Divisi', 'Managed Service']);
                })->get();

            foreach ($leaders as $leader) {
                \App\Models\Notification::create([
                    'user_id' => $leader->id,
                    'title'   => 'Penyerahan Proyek: ' . $project->name,
                    'message' => 'PMO telah menyerahkan proyek "' . $project->name . '" (Klien: ' . $project->client . ') ke ' . $divisionName . ' (Tahap: ' . $project->stage . '). Silakan atur dan delegasikan tugas tim Anda.',
                    'url'     => route('tasks.index'),
                    'is_read' => false,
                ]);
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status siklus & divisi proyek ' . $project->name . ' berhasil diperbarui!',
                'project' => $project->fresh(['pm', 'division']),
            ]);
        }

        return back()->with('success', 'Status siklus proyek berhasil diperbarui!');
    }

    /**
     * Pengesahan Serah Terima Internal (Handover Approved)
     * Titik Transisi: Tanggung jawab proyek penuh beralih ke PM & Tim Delivery
     */
    public function approveHandover(Request $request, Project $project)
    {
        $validated = $request->validate([
            'pm_id'       => 'required|exists:users,id',
            'division_id' => 'required|exists:divisions,id',
            'checklist'   => 'nullable',
        ]);

        $updateData = [
            'handover_status'      => 'Approved',
            'handover_approved_at' => now(),
            'handover_approved_by' => auth()->id(),
            'stage'                => 'Deliver',
            'status'               => 'On Progress',
            'process_status'       => 'In Progress',
            'pm_id'                => $validated['pm_id'],
            'division_id'          => $validated['division_id'],
        ];

        if ($request->has('checklist')) {
            $cl = $request->input('checklist');
            if (is_string($cl)) $cl = json_decode($cl, true);
            if (is_array($cl)) {
                $existingHandoverData = is_array($project->handover_data) ? $project->handover_data : [];
                $updateData['handover_data'] = array_merge($existingHandoverData, [
                    'checklist'                => $cl,
                    'checklist_status'         => $request->input('checklist_status', []),
                    'checklist_na'             => $request->input('checklist_na', []),
                    'checklist_notes'          => $request->input('checklist_notes', []),
                    'handover_status_selected' => $request->input('handover_status_selected', 'approved'),
                    'project_type_category'    => $request->input('project_type_category', 'Implementasi'),
                    'project_type_other'       => $request->input('project_type_other', ''),
                    'doc_storage_location'     => $request->input('doc_storage_location', ''),
                    'handover_clarification_issues' => $request->input('handover_clarification_issues', ''),
                    'handover_assumptions'     => $request->input('handover_assumptions', ''),
                    'handover_tech_validation' => $request->input('handover_tech_validation', ''),
                    'handover_pending_decisions'=> $request->input('handover_pending_decisions', ''),
                    'handover_related_docs'    => $request->input('handover_related_docs', ''),
                    'handover_risks'           => $request->input('handover_risks', ''),
                    'handover_missing_info'    => $request->input('handover_missing_info', ''),
                    'handover_pic_target'      => $request->input('handover_pic_target', ''),
                    'handover_work_limitations'=> $request->input('handover_work_limitations', ''),
                    'special_notes'            => $request->input('special_notes', $project->special_notes),
                ]);
                $updateData['documents_checklist'] = $cl;
            }
        }

        if ($request->filled('special_notes')) {
            $updateData['special_notes'] = $request->input('special_notes');
        }

        $project->update($updateData);

        // Notifikasi & Penugasan Awal ke Lead Engineer divisi terkait (Lead Network / Lead Security)
        $divisionName = $project->division ? $project->division->name : 'Lintas Divisi';
        $leadersQuery = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Team Leader Engineering', 'Team Leader', 'Lead Engineer', 'Lead Divisi', 'Lead Maintenance', 'Managed Service']);
        });

        if ($project->division_id) {
            $leadersQuery->where('division_id', $project->division_id);
        }

        $leaders = $leadersQuery->get();
        $targetLeadId = $leaders->isNotEmpty() ? $leaders->first()->id : (auth()->id() ?: 1);

        // Penyerahan proyek ke divisi tanpa membuat task otomatis (penugasan jadwal sepenuhnya ranah Lead Engineer)
        foreach ($leaders as $leader) {
            \App\Models\Notification::create([
                'user_id' => $leader->id,
                'title'   => 'Proyek Masuk dari PMO: ' . $project->name,
                'message' => 'PMO telah menyerahkan proyek "' . $project->name . '" (Klien: ' . $project->client . ') ke ' . $divisionName . '. Silakan tentukan Field Engineer dan delegasikan tugas tim.',
                'url'     => route('tasks.index'),
                'is_read' => false,
            ]);
        }

        // Notifikasi ke Sales & PIC PM
        $salesUser = User::where('name', $project->sales_name)->first();
        if ($salesUser) {
            \App\Models\Notification::create([
                'user_id' => $salesUser->id,
                'title'   => 'Handover Disahkan: ' . $project->name,
                'message' => 'Formulir Serah Terima Proyek "' . $project->name . '" telah disahkan oleh PMO. Tanggung jawab teknis penuh kini dipegang oleh Tim Delivery.',
                'url'     => route('acquire.index'),
                'is_read' => false,
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Handover berhasil disahkan! Proyek resmi masuk ke tahap Deliver dan siap didelegasikan oleh Lead Engineer.',
                'project' => $project->fresh(['pm', 'division']),
            ]);
        }

        return back()->with('success', 'Handover berhasil disahkan! Proyek resmi memasuki tahap Deliver.');
    }

    /**
     * Penolakan / Status Bersyarat Handover (Handover Conditional - 2x24 Jam)
     */
    public function conditionalHandover(Request $request, Project $project)
    {
        $validated = $request->validate([
            'notes'  => 'required|string|max:1000',
            'status' => 'nullable|string|in:Conditional,Rejected',
        ]);

        $newStatus = $validated['status'] ?? 'Conditional';
        $existingHandoverData = is_array($project->handover_data) ? $project->handover_data : [];

        $project->update([
            'handover_status'               => $newStatus,
            'handover_conditional_notes'    => $validated['notes'],
            'handover_conditional_deadline' => $newStatus === 'Conditional' ? now()->addHours(48) : null,
            'process_status'                => $newStatus === 'Rejected' ? 'Handover Ditolak' : 'Menunggu Handover',
            'handover_data'                 => array_merge($existingHandoverData, [
                'checklist'                => $request->input('checklist', $project->documents_checklist),
                'checklist_status'         => $request->input('checklist_status', []),
                'checklist_na'             => $request->input('checklist_na', []),
                'checklist_notes'          => $request->input('checklist_notes', []),
                'handover_status_selected' => $request->input('handover_status_selected', $newStatus === 'Rejected' ? 'rejected' : 'conditional'),
                'project_type_category'    => $request->input('project_type_category', 'Implementasi'),
                'project_type_other'       => $request->input('project_type_other', ''),
                'doc_storage_location'     => $request->input('doc_storage_location', ''),
                'handover_clarification_issues' => $request->input('handover_clarification_issues', ''),
                'handover_assumptions'     => $request->input('handover_assumptions', ''),
                'handover_tech_validation' => $request->input('handover_tech_validation', ''),
                'handover_pending_decisions'=> $request->input('handover_pending_decisions', ''),
                'handover_related_docs'    => $request->input('handover_related_docs', ''),
                'handover_risks'           => $request->input('handover_risks', ''),
                'handover_missing_info'    => $request->input('handover_missing_info', $validated['notes']),
                'handover_pic_target'      => $request->input('handover_pic_target', ''),
                'handover_work_limitations'=> $request->input('handover_work_limitations', ''),
                'special_notes'            => $request->input('special_notes', $project->special_notes),
            ]),
        ]);

        // Notifikasi ke Sales
        $salesUser = User::where('name', $project->sales_name)->first();
        if ($salesUser) {
            $notifTitle = $newStatus === 'Rejected' 
                ? 'Handover Ditolak / Dikembalikan: ' . $project->name 
                : 'Handover Conditional (Revisi 2x24 Jam): ' . $project->name;
            $notifMsg = $newStatus === 'Rejected'
                ? 'PMO menolak dan mengembalikan berkas handover "' . $project->name . '". Alasan: ' . $validated['notes']
                : 'PMO menandai berkas handover "' . $project->name . '" sebagai bersyarat. Catatan: ' . $validated['notes'] . '. Harap lengkapi dalam 2x24 jam.';

            \App\Models\Notification::create([
                'user_id' => $salesUser->id,
                'title'   => $notifTitle,
                'message' => $notifMsg,
                'url'     => route('acquire.index'),
                'is_read' => false,
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status Handover ' . $newStatus . ' berhasil dicatat.',
                'project' => $project->fresh(['pm', 'division']),
            ]);
        }

        return back()->with('success', 'Status Handover ' . $newStatus . ' berhasil dicatat.');
    }

    /**
     * Update Checklist Formulir Serah Terima Proyek (Internal Handover)
     */
    public function updateDocuments(Request $request, Project $project)
    {
        $checklist = $request->input('documents_checklist', $request->input('checklist', []));
        if (is_string($checklist)) {
            $checklist = json_decode($checklist, true) ?: [];
        }

        if (!is_array($checklist)) {
            $checklist = [];
        }

        $specialNotes = $request->input('special_notes', $project->special_notes);

        $project->update([
            'handover_data'       => $checklist,
            'documents_checklist' => $checklist,
            'special_notes'       => $specialNotes,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Formulir Serah Terima Proyek ' . $project->name . ' berhasil diperbarui!',
                'project' => $project->fresh(['pm', 'division']),
            ]);
        }

        return back()->with('success', 'Formulir Serah Terima Proyek berhasil diperbarui!');
    }

    /**
     * Handover Proyek Pasca-Implementasi (Gate 4: PMO -> Managed Service)
     */
    public function handoverToManagedService(Request $request, Project $project)
    {
        $validated = $request->validate([
            'sla_tier'              => 'required|string|in:Platinum,Gold,Silver,Bronze',
            'maintenance_frequency' => 'required|string|max:100',
            'sla_coverage_hours'    => 'required|string|max:100',
            'service_start_date'    => 'required|date',
            'service_end_date'      => 'nullable|date|after_or_equal:service_start_date',
            'special_notes'         => 'nullable|string',
        ]);

        $existingHandoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $existingHandoverData['ms_handover'] = [
            'checklist_status'         => $request->input('checklist_status', []),
            'checklist_na'             => $request->input('checklist_na', []),
            'checklist_notes'          => $request->input('checklist_notes', []),
            'handover_status_selected' => $request->input('handover_status_selected', 'approved'),
            'service_acceptance'       => $request->input('service_acceptance', ''),
            'risks_and_pic'            => $request->input('risks_and_pic', ''),
            'follow_up_date'           => $request->input('follow_up_date', ''),
            'confirmation_notes'       => $request->input('confirmation_notes', ''),
            'special_notes'            => $validated['special_notes'] ?? $project->special_notes,
            'submitted_at'             => now()->toDateTimeString(),
            'submitted_by'             => auth()->id(),
        ];

        $project->update([
            'stage'                 => 'Operate',
            'status'                => 'Active',
            'handover_target'       => 'managed_service',
            'sla_tier'              => $validated['sla_tier'],
            'maintenance_frequency' => $validated['maintenance_frequency'],
            'sla_coverage_hours'    => $validated['sla_coverage_hours'],
            'service_start_date'    => $validated['service_start_date'],
            'service_end_date'      => $validated['service_end_date'],
            'ms_handover_status'    => 'Submitted',
            'handover_data'         => $existingHandoverData,
            'special_notes'         => $validated['special_notes'] ?? $project->special_notes,
        ]);

        // Kirim notifikasi ke Tim Managed Service
        $msUsers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Managed Service', 'Lead Maintenance', 'Lead Engineer', 'Director', 'Direktur']);
        })->get();

        foreach ($msUsers as $ms) {
            \App\Models\Notification::create([
                'user_id' => $ms->id,
                'title'   => 'Serah Terima Pasca-Implementasi (PMO -> MS)',
                'message' => "Proyek '{$project->name}' telah selesai tahap implementasi (BAST) dan diserahkan oleh PMO ke Tim Managed Service untuk operasional & maintenance.",
                'type'    => 'service_handover',
                'url'     => route('ms.dashboard'),
                'is_read' => false,
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Proyek ' . $project->name . ' berhasil diserahterimakan ke Tim Managed Service (Tahap Operate)!',
                'project' => $project->fresh(['pm', 'division']),
            ]);
        }

        return back()->with('success', 'Proyek ' . $project->name . ' berhasil diserahterimakan ke Tim Managed Service (Tahap Operate)!');
    }

    /**
     * Delegasi Personil Tim Lapangan oleh Lead / PMO (Resource Allocation)
     */
    public function assignTeam(Request $request, Project $project)
    {
        $validated = $request->validate([
            'engineer_ids'   => 'required|array|min:1',
            'engineer_ids.*' => 'exists:users,id',
            'notes'          => 'nullable|string|max:1000',
            'roles'          => 'nullable|array',
        ]);

        $engineers = User::whereIn('id', $validated['engineer_ids'])->with('division')->get();
        $assignedList = [];
        $leadName = auth()->user() ? auth()->user()->name : 'Lead Engineer';
        $rolesInput = $request->input('roles', []);

        foreach ($engineers as $eng) {
            $engRole = !empty($rolesInput[$eng->id]) ? trim($rolesInput[$eng->id]) : null;
            $assignedList[] = [
                'id'          => $eng->id,
                'name'        => $eng->name,
                'email'       => $eng->email,
                'position'    => $eng->position ?: 'Field Engineer',
                'role'        => $engRole,
                'division'    => $eng->division ? preg_replace('/^Divisi\s+/i', 'Tim ', $eng->division->name) : 'Tim Teknis',
                'assigned_at' => now()->format('d M Y H:i'),
                'assigned_by' => $leadName,
            ];

            // Kirim notifikasi ke masing-masing engineer yang ditugaskan
            try {
                \App\Models\Notification::create([
                    'user_id' => $eng->id,
                    'title'   => 'Penugasan Tim Proyek: ' . $project->name,
                    'message' => "Anda telah ditugaskan oleh {$leadName} sebagai personil pelaksana lapangan untuk proyek '{$project->name}'.",
                    'type'    => 'project_assignment',
                    'url'     => route('tasks.index'),
                    'is_read' => false,
                ]);
            } catch (\Throwable $e) {}
        }

        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $handoverData['assigned_engineers'] = $assignedList;
        $handoverData['team_delegation_notes'] = $validated['notes'] ?? null;
        $handoverData['team_delegated_at'] = now()->format('d M Y H:i');
        $handoverData['team_delegated_by_id'] = auth()->id();
        $handoverData['team_delegated_by_name'] = $leadName;

        $project->handover_data = $handoverData;
        $project->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tim teknis (' . count($assignedList) . ' personil) berhasil ditetapkan oleh Lead untuk proyek ini!',
                'assigned_engineers' => $assignedList,
            ]);
        }

        return back()->with('success', 'Tim teknis lapangan (' . count($assignedList) . ' personil) berhasil ditetapkan oleh Lead untuk proyek ini!');
    }

    /**
     * PM Membuat & Mendelegasikan Task Teknis (WBS) Cepat ke Personil Proyek
     */
    public function quickStoreTask(Request $request, Project $project)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'engineer_id'  => 'required|exists:users,id',
            'priority'     => 'required|in:Low,Medium,High',
            'deadline'     => 'nullable|date',
            'description'  => 'nullable|string|max:2000',
        ]);

        $deadlineStr = $validated['deadline'] 
            ? substr($validated['deadline'], 0, 10) . ' 18:00:00' 
            : ($project->deadline ? $project->deadline->format('Y-m-d') . ' 18:00:00' : now()->addWeeks(2)->format('Y-m-d') . ' 18:00:00');

        $task = Task::create([
            'title'       => $validated['title'],
            'project_id'  => $project->id,
            'engineer_id' => $validated['engineer_id'],
            'priority'    => $validated['priority'],
            'status'      => 'Assigned',
            'start_date'  => now()->toDateString(),
            'deadline'    => $deadlineStr,
            'description' => $validated['description'] ?? null,
            'progress'    => 0,
            'attachments' => 0,
            'created_by'  => auth()->id(),
        ]);

        if (\Illuminate\Support\Facades\Schema::hasTable('task_user')) {
            $task->engineers()->sync([$validated['engineer_id']]);
        }

        $eng = User::find($validated['engineer_id']);

        // Notifikasi ke engineer penerima tugas dari PM
        try {
            \App\Models\Notification::create([
                'user_id' => $validated['engineer_id'],
                'title'   => 'Tugas Teknis Baru (PM): ' . $task->title,
                'message' => "Project Manager telah menugaskan tugas '{$task->title}' pada proyek '{$project->name}'.",
                'type'    => 'task_assigned',
                'url'     => route('tasks.index'),
                'is_read' => false,
            ]);
        } catch (\Throwable $e) {}

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task "' . $task->title . '" berhasil dibuat dan didelegasikan ke ' . ($eng ? $eng->name : 'engineer') . '!',
                'task'    => $task,
            ]);
        }

        return back()->with('success', 'Task "' . $task->title . '" berhasil dibuat dan didelegasikan ke ' . ($eng ? $eng->name : 'engineer') . '!');
    }
}

