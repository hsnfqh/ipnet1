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

        // 2. Project Number (Active Portfolios)
        $activeProjectsCount = $formattedProjects->whereIn('stage', ['Deliver', 'Operate'])->count();

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

        // 6. Your Projects List (Untuk preview list rapi di dashboard)
        $yourProjects = $formattedProjects;

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

        $daysSpan = ($project->start_date && $project->deadline) ? max(0, $project->start_date->diffInDays($project->deadline)) : 0;
        $daysRemaining = $project->deadline ? \Carbon\Carbon::now()->diffInDays($project->deadline, false) : null;

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

        // Task Share by Engineer in this project
        $engineerTaskCounts = [];
        foreach ($project->tasks as $t) {
            $engName = $t->engineer ? $t->engineer->name : 'Unassigned';
            $engineerTaskCounts[$engName] = ($engineerTaskCounts[$engName] ?? 0) + 1;
        }

        $palette = ['#8F0A0D', '#334155', '#475569', '#64748B', '#B91C1C', '#0F172A', '#94A3B8'];
        $taskShareList = [];
        $colorIdx = 0;
        foreach ($engineerTaskCounts as $engName => $count) {
            $taskShareList[] = [
                'name' => $engName,
                'count' => $count,
                'percentage' => $totalTasks > 0 ? round(($count / $totalTasks) * 100) : 0,
                'color' => $palette[$colorIdx % count($palette)],
            ];
            $colorIdx++;
        }

        $taskShareChartData = [
            'labels' => array_keys($engineerTaskCounts),
            'counts' => array_values($engineerTaskCounts),
            'colors' => array_column($taskShareList, 'color'),
        ];

        // 1. Rentang Tanggal & Durasi Lengkap Proyek
        $startDate = $project->start_date ? \Carbon\Carbon::parse($project->start_date) : ($project->created_at ? \Carbon\Carbon::parse($project->created_at) : now());
        $deadlineDate = $project->deadline ? \Carbon\Carbon::parse($project->deadline) : $startDate->copy()->addWeeks(8);
        $totalProjectDays = max(1, $startDate->diffInDays($deadlineDate));
        $totalProjectWeeks = max(1, (int) ceil($startDate->diffInWeeks($deadlineDate)));
        
        $projectTimelineText = $startDate->format('d M Y') . ' s/d ' . $deadlineDate->format('d M Y');

        // S-Curve Weeks: Batas tampilan grafik implementasi utama (default 8 minggu seperti di Excel atau durasi proyek)
        $totalWeeks = 8;
        if ($totalProjectWeeks >= 4 && $totalProjectWeeks <= 16) {
            $totalWeeks = $totalProjectWeeks;
        }

        // Hitung minggu berjalan saat ini (Minggu ke-X)
        $elapsedWeeks = (int) ceil($startDate->diffInWeeks(now()));
        $currentWeek = max(1, min($totalWeeks, $elapsedWeeks ?: 3));

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
            'taskShareList',
            'taskShareChartData',
            'totalWeeks',
            'currentWeek',
            'totalProjectDays',
            'totalProjectWeeks',
            'projectTimelineText',
            'weeklyRecords',
            'divisions',
            'sCurveLabels',
            'sCurvePlan',
            'sCurveActual',
            'currentProgressPercent',
            'sCurveUpdateDate'
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
        // PMO Implementasi hanya menangani proyek Delivery / Implementasi fisik (Kecualikan kontrak murni Managed Service & Pemeliharaan)
        ->where(function($q) {
            $q->where(function($sub) {
                $sub->whereNull('handover_target')
                    ->orWhere('handover_target', '!=', 'managed_service')
                    ->orWhere('handover_target', 'both');
            })
            ->where(function($sub) {
                $sub->whereNull('project_type')
                    ->orWhere('project_type', '!=', 'Managed Service');
            })
            ->where('name', 'not like', '%perpanjangan pemeliharaan%')
            ->where('name', 'not like', '%maintenance contract%');
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

        // 2. Formulir Check List Serah Terima (Internal Handover) - 4 Kategori Resmi
        $handoverFormStructure = [
            [
                'number' => 'I',
                'category_name' => 'Legal & Komersial',
                'items' => [
                    [
                        'key' => 'contract_signed',
                        'name' => 'Kontrak / SPK / SLA yang sudah di tandatangani',
                        'default_note' => 'No. Kontrak & Tanggal',
                    ],
                    [
                        'key' => 'bom_proposal',
                        'name' => 'Dokumen Built of Material (BoW) (Commercial Proposal)',
                        'default_note' => 'Final version',
                    ],
                    [
                        'key' => 'negotiation_addendum',
                        'name' => 'Berita Acara Negosiasi / Lampiran Perubahan (Addendum)',
                        'default_note' => 'Jika ada',
                    ],
                ]
            ],
            [
                'number' => 'II',
                'category_name' => 'Kebutuhan & Lingkup',
                'items' => [
                    [
                        'key' => 'sow_document',
                        'name' => 'Dokumen Statement of Work (SoW) / Scope of Work',
                        'default_note' => 'Batasan proyek',
                    ],
                    [
                        'key' => 'urs_requirement',
                        'name' => 'Dokumen Business Requirement / User Requirement (URS)',
                        'default_note' => 'Hasil dari customer',
                    ],
                    [
                        'key' => 'mockup_wireframe',
                        'name' => 'Mockup / Desain Awal / Wireframe (jika ada dari sales)',
                        'default_note' => 'Referensi UI/UX',
                    ],
                    [
                        'key' => 'maintenance_contract',
                        'name' => 'Kegiatan Kontrak Maintenance (Maintenance Contract)',
                        'default_note' => 'Cek dalam kontrak',
                    ],
                ]
            ],
            [
                'number' => 'III',
                'category_name' => 'Administrasi & Finansial',
                'items' => [
                    [
                        'key' => 'dp_payment_proof',
                        'name' => 'Bukti Pembayaran Termin 1 / Down Payment (DP)',
                        'default_note' => 'Lampirkan bukti kuitansi',
                    ],
                    [
                        'key' => 'billing_milestone',
                        'name' => 'Jadwal Penagihan Termin (Billing Milestone)',
                        'default_note' => 'Sesuai kontrak',
                    ],
                ]
            ],
            [
                'number' => 'IV',
                'category_name' => 'Kontak & Akses Customer',
                'items' => [
                    [
                        'key' => 'customer_contacts',
                        'name' => 'Daftar Kontak Utama Customer (PIC Teknis, PIC Bisnis, Finance)',
                        'default_note' => 'Nama, No. HP, Email',
                    ],
                    [
                        'key' => 'meeting_notes',
                        'name' => 'Hasil Catatan Pertemuan (Meeting Notes) / Ekspektasi Customer',
                        'default_note' => 'Catatan khusus align sales',
                    ],
                ]
            ],
        ];

        // Format data project untuk dashboard & manajemen implementasi
        $formattedProjects = $allProjects->map(function ($p) use ($handoverFormStructure) {
            $totalTasks = $p->tasks->count();
            $completedTasks = $p->tasks->whereIn('status', ['Done', 'Completed'])->count();
            $calculatedProgress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

            // Pastikan checklist dokumen ada
            $checklist = is_array($p->handover_data) ? $p->handover_data : (is_array($p->documents_checklist) ? $p->documents_checklist : []);
            $completedDocsCount = 0;
            $totalDocsCount = 0;
            foreach ($handoverFormStructure as $cat) {
                foreach ($cat['items'] as $item) {
                    $totalDocsCount++;
                    if (!empty($checklist[$item['key']]) && $checklist[$item['key']] === true) {
                        $completedDocsCount++;
                    }
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
                    $divisionName = 'Network & Security';
                } elseif (str_contains(strtolower($rawDiv), 'sec')) {
                    $divisionName = 'Divisi Security';
                } elseif (str_contains(strtolower($rawDiv), 'net')) {
                    $divisionName = 'Divisi Network';
                } else {
                    $divisionName = $rawDiv;
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
                'handover_status'               => $p->handover_status ?: 'Draft',
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
                'days_span'                     => ($p->start_date && $p->deadline) ? max(0, $p->start_date->diffInDays($p->deadline)) : 0,
                'days_remaining'                => $p->deadline ? \Carbon\Carbon::now()->diffInDays($p->deadline, false) : null,
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

        // 5. Persebaran 4-Tahap Siklus Ver 2.0
        $stageCounts = [
            'Acquire' => $allProjects->where('stage', 'Acquire')->count(),
            'Design'  => $allProjects->where('stage', 'Design')->count(),
            'Deliver' => $allProjects->where('stage', 'Deliver')->count(),
            'Operate' => $allProjects->where('stage', 'Operate')->count(),
        ];

        // 6. Data Master untuk Filter & Form (Hanya Divisi Network & Divisi Security)
        $divisions = Division::where(function($q) {
            $q->where('name', 'like', '%Network%')
              ->orWhere('name', 'like', '%Security%');
        })->orderBy('name')->get();

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
                $updateData['handover_data'] = $cl;
                $updateData['documents_checklist'] = $cl;
            }
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
            'notes' => 'required|string|max:1000',
        ]);

        $project->update([
            'handover_status'               => 'Conditional',
            'handover_conditional_notes'    => $validated['notes'],
            'handover_conditional_deadline' => now()->addHours(48), // 2x24 jam per SOP
            'process_status'                => 'Menunggu Handover',
        ]);

        // Notifikasi mendesak ke Sales
        $salesUser = User::where('name', $project->sales_name)->first();
        if ($salesUser) {
            \App\Models\Notification::create([
                'user_id' => $salesUser->id,
                'title'   => 'Handover Conditional (Revisi 2x24 Jam): ' . $project->name,
                'message' => 'PMO menandai berkas handover "' . $project->name . '" sebagai bersyarat. Catatan: ' . $validated['notes'] . '. Harap lengkapi dalam 2x24 jam.',
                'url'     => route('acquire.index'),
                'is_read' => false,
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status Handover Conditional berhasil dicatat (Tenggat 2x24 Jam).',
                'project' => $project->fresh(['pm', 'division']),
            ]);
        }

        return back()->with('success', 'Status Handover Conditional berhasil dicatat.');
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
}

