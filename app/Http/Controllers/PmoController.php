<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;
use App\Models\Division;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Helpers\ScopeHelper;

class PmoController extends Controller
{
    /**
     * Tampilan Dashboard Utama PMO (Executive Overview & Ringkasan Portofolio)
     */
    public function dashboard(Request $request)
    {
        // Pastikan proyek resmi PPATK selalu tersedia di portofolio PMO
        $hasPpatk = Project::withTrashed()
            ->where(function ($q) {
                $q->where('name', 'like', '%PPAT%')
                    ->orWhere('name', 'like', '%Peremajaan Router%')
                    ->orWhere('client', 'like', '%PPAT%');
            })
            ->exists();
        if (!$hasPpatk) {
            try {
                $ppatkSeeder = new \Database\Seeders\PpatkProjectSeeder();
                $ppatkSeeder->run();
            } catch (\Throwable $e) {}
        }
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
    /**
     * Pastikan Data Proyek PPATK Tersinkronisasi Persis Sesuai Excel
     */
    protected function ensurePpatkProjectData(Project $project): void
    {
        // Jika proyek sudah memiliki task WBS atau sudah pernah diinisialisasi, JANGAN PERNAH timpa perubahan user/PM!
        if ($project->tasks()->count() > 0) {
            return;
        }

        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $projectUpdated = false;

        if (empty($project->client)) {
            $project->client = 'PPAT - Pusat Pelaporan dan Analisa Transaksi Keuangan';
            $projectUpdated = true;
        }
        if (empty($project->po_spk_number)) {
            $project->po_spk_number = 'EP-01KYREAFEJH89MWPVDQ6W20NXV';
            $projectUpdated = true;
        }
        if (empty($project->customer_pic_technical)) {
            $project->customer_pic_technical = 'Richi Michael (PPATK)';
            $project->customer_pic_business  = 'Richi Michael (PPATK)';
            $projectUpdated = true;
        }

        if (empty($handoverData['estimate_project_duration'])) {
            $handoverData['estimate_project_duration'] = 30;
            $projectUpdated = true;
        }
        if (empty($handoverData['execution_weeks'])) {
            $handoverData['execution_weeks'] = 5;
            $projectUpdated = true;
        }
        if (empty($handoverData['customer_pic'])) {
            $handoverData['customer_pic'] = $project->customer_pic_technical ?: 'Richi Michael';
            $projectUpdated = true;
        }
        if (empty($handoverData['contract_spk_number'])) {
            $handoverData['contract_spk_number'] = $project->po_spk_number ?: 'EP-01KYREAFEJH89MWPVDQ6W20NXV';
            $projectUpdated = true;
        }

        // S-Curve Weekly Breakdown: Hanya isi baseline jika masih kosong
        if (empty($handoverData['s_curve_weekly'])) {
            $handoverData['s_curve_weekly'] = [
                0 => ['plan_weekly' => 0.0,  'plan' => 0.0,   'actual_weekly' => 0.0,  'actual' => 0.0,  'variance' => 0.0,  'notes' => 'Baseline Awal Proyek & Kick-off SPK'],
                1 => ['plan_weekly' => 23.3, 'plan' => 23.3,  'actual_weekly' => 20.0, 'actual' => 20.0, 'variance' => -3.3, 'notes' => 'Survey and Assessment selesai 100%, Design Review Meeting 50%, HLD/LLD Documentation 50%'],
                2 => ['plan_weekly' => 23.3, 'plan' => 46.7,  'actual_weekly' => null, 'actual' => null, 'variance' => null, 'notes' => ''],
                3 => ['plan_weekly' => 23.3, 'plan' => 70.0,  'actual_weekly' => null, 'actual' => null, 'variance' => null, 'notes' => ''],
                4 => ['plan_weekly' => 23.3, 'plan' => 93.3,  'actual_weekly' => null, 'actual' => null, 'variance' => null, 'notes' => ''],
                5 => ['plan_weekly' => 6.7,  'plan' => 100.0, 'actual_weekly' => null, 'actual' => null, 'variance' => null, 'notes' => ''],
            ];
            $projectUpdated = true;
        }

        // Matriks Input Aktual Sesuai Gambar 3: Hanya isi jika kosong
        if (empty($handoverData['actual_progress_inputs'])) {
            $handoverData['actual_progress_inputs'] = [
                'Survey and Assessment'     => [1 => 100],
                'Design Review Meeting'     => [1 => 50],
                'HLD and LLD Documentation' => [1 => 50],
            ];
            $projectUpdated = true;
        }

        if ($project->progress === null) {
            $project->progress = 20.0;
            $projectUpdated = true;
        }

        // Pastikan Personil Tim Lapangan dari Lead Engineer telah terdaftar
        if (empty($handoverData['assigned_engineers'])) {
            $engSyaiful = User::where('name', 'like', '%Syaiful%')->first();
            $engRaihan  = User::where('name', 'like', '%Raihan%')->first();
            $leadNugraha = User::where('name', 'like', '%Nugraha%')->first();

            $handoverData['assigned_engineers'] = [
                [
                    'id'          => $engSyaiful?->id,
                    'name'        => $engSyaiful?->name ?? 'Syaiful Amin',
                    'email'       => $engSyaiful?->email ?? 'syaiful@ipnetsolusindo.com',
                    'position'    => 'L2 Network Engineer',
                    'role'        => 'PIC Lapangan / Implementasi & Peremajaan Router',
                    'division'    => 'Divisi Network',
                    'assigned_at' => '2026-07-30 08:30',
                    'assigned_by' => $leadNugraha?->name ?? 'Nugraha Pratama',
                ],
                [
                    'id'          => $engRaihan?->id,
                    'name'        => $engRaihan?->name ?? 'Raihan Ghiffary',
                    'email'       => $engRaihan?->email ?? 'raihan@ipnetsolusindo.com',
                    'position'    => 'Field Network Engineer',
                    'role'        => 'Teknisi Lapangan / Staging, Integrasi & Cabling',
                    'division'    => 'Divisi Network',
                    'assigned_at' => '2026-07-30 08:30',
                    'assigned_by' => $leadNugraha?->name ?? 'Nugraha Pratama',
                ],
            ];
            $handoverData['team_delegated_at']      = '2026-07-30 08:30:00';
            $handoverData['team_delegated_by_name'] = $leadNugraha?->name ?? 'Nugraha Pratama';
            $handoverData['team_delegation_notes']  = 'Personel Network telah dialokasikan untuk pelaksanaan WBS 30 hari di Gedung PPATK Jakarta.';
            $handoverData['assigned_teams']         = ['Tim Network'];
            $projectUpdated = true;
        }

        if ($projectUpdated) {
            $project->handover_data = $handoverData;
            $project->saveQuietly();
        }

        // Master 11 WBS Deliverables PPATK Persis Sesuai Excel Template
        $masterWbs = [
            [
                'title'        => 'Survey and Assessment',
                'duration'     => 2,
                'starting_day' => 0,
                'finish_day'   => 2,
                'week'         => 1,
                'bobot'        => 6.7,
                'daily_weight' => 3.3,
                'default_prog' => 100,
                'default_stat' => 'Completed',
                'description'  => 'Survey and Assessment topologi & infrastruktur jaringan eksisting router PPATK.',
            ],
            [
                'title'        => 'Design Review Meeting',
                'duration'     => 4,
                'starting_day' => 2,
                'finish_day'   => 6,
                'week'         => 1,
                'bobot'        => 13.3,
                'daily_weight' => 3.3,
                'default_prog' => 100,
                'default_stat' => 'Completed',
                'description'  => 'Design Review Meeting arsitektur peremajaan router bersama PIC PPATK (Bpk. Richi Michael).',
            ],
            [
                'title'        => 'HLD and LLD Documentation',
                'duration'     => 4,
                'starting_day' => 6,
                'finish_day'   => 10,
                'week'         => 2,
                'bobot'        => 13.3,
                'daily_weight' => 3.3,
                'default_prog' => 100,
                'default_stat' => 'Completed',
                'description'  => 'Penyusunan dokumen teknis High Level Design (HLD) dan Low Level Design (LLD).',
            ],
            [
                'title'        => 'Stagging',
                'duration'     => 1,
                'starting_day' => 10,
                'finish_day'   => 11,
                'week'         => 2,
                'bobot'        => 3.3,
                'daily_weight' => 3.3,
                'default_prog' => 0,
                'default_stat' => 'Assigned',
                'description'  => 'Staging dan pengujian modul hardware serta firmware pada unit router baru.',
            ],
            [
                'title'        => 'Installation and Configuration',
                'duration'     => 5,
                'starting_day' => 11,
                'finish_day'   => 16,
                'week'         => 3,
                'bobot'        => 16.7,
                'daily_weight' => 3.3,
                'default_prog' => 0,
                'default_stat' => 'Assigned',
                'description'  => 'Pemasangan fisik rackmount, perkabelan, dan konfigurasi routing peremajaan router.',
            ],
            [
                'title'        => 'Integration',
                'duration'     => 3,
                'starting_day' => 16,
                'finish_day'   => 19,
                'week'         => 3,
                'bobot'        => 10.0,
                'daily_weight' => 3.3,
                'default_prog' => 0,
                'default_stat' => 'Assigned',
                'description'  => 'Integrasi router ke backbone switch, firewall gateway, dan segmen server PPATK.',
            ],
            [
                'title'        => 'Migration',
                'duration'     => 3,
                'starting_day' => 19,
                'finish_day'   => 22,
                'week'         => 4,
                'bobot'        => 10.0,
                'daily_weight' => 3.3,
                'default_prog' => 0,
                'default_stat' => 'Assigned',
                'description'  => 'Migrasi routing tabel dan pengalihan traffic data live (cutover window).',
            ],
            [
                'title'        => 'Testing and Commisioning',
                'duration'     => 2,
                'starting_day' => 22,
                'finish_day'   => 24,
                'week'         => 4,
                'bobot'        => 6.7,
                'daily_weight' => 3.3,
                'default_prog' => 0,
                'default_stat' => 'Assigned',
                'description'  => 'Uji komisioning performa, redundansi High Availability (HA), dan failover link.',
            ],
            [
                'title'        => 'Monitoring',
                'duration'     => 1,
                'starting_day' => 24,
                'finish_day'   => 25,
                'week'         => 4,
                'bobot'        => 3.3,
                'daily_weight' => 3.3,
                'default_prog' => 0,
                'default_stat' => 'Assigned',
                'description'  => 'Monitoring stabilitas traffic 24 jam paska migrasi router.',
            ],
            [
                'title'        => 'Documentation',
                'duration'     => 3,
                'starting_day' => 25,
                'finish_day'   => 28,
                'week'         => 4,
                'bobot'        => 10.0,
                'daily_weight' => 3.3,
                'default_prog' => 0,
                'default_stat' => 'Assigned',
                'description'  => 'Penyusunan As-Built Documentation, konfigurasi backup, dan handover manual.',
            ],
            [
                'title'        => 'BAST',
                'duration'     => 2,
                'starting_day' => 28,
                'finish_day'   => 30,
                'week'         => 5,
                'bobot'        => 6.7,
                'daily_weight' => 3.3,
                'default_prog' => 0,
                'default_stat' => 'Assigned',
                'description'  => 'Penandatanganan Berita Acara Serah Terima (BAST) pekerjaan bersama PPATK.',
            ],
        ];

        $startDate = $project->start_date ? \Carbon\Carbon::parse($project->start_date) : \Carbon\Carbon::create(2026, 7, 30);
        $engSyaiful = User::where('name', 'like', '%Syaiful%')->first();
        $engRaihan  = User::where('name', 'like', '%Raihan%')->first();
        $pmRizki    = $project->pm_id ?: User::where('name', 'like', '%Rizki%')->first()?->id;

        $valid11Titles = [
            'Survey and Assessment',
            'Design Review Meeting',
            'HLD and LLD Documentation',
            'Stagging',
            'Installation and Configuration',
            'Integration',
            'Migration',
            'Testing and Commisioning',
            'Monitoring',
            'Documentation',
            'BAST',
        ];

        // Hapus task lama / duplikat yang namanya tidak baku (seperti DRM, Integrasi, Migrasi)
        $project->tasks()->whereNotIn('title', $valid11Titles)->delete();

        $existingTasks = $project->tasks()->get();

        foreach ($masterWbs as $mw) {
            $matchedTask = $existingTasks->first(function($t) use ($mw) {
                return strtolower(trim($t->title)) === strtolower(trim($mw['title']));
            });

            $expectedMeta = "[Bobot: {$mw['bobot']}% | StartingDay: {$mw['starting_day']} | FinishDay: {$mw['finish_day']} | Week: {$mw['week']} | DailyWeight: {$mw['daily_weight']}%]";
            $tStart = $startDate->copy()->addDays($mw['starting_day'])->format('Y-m-d');
            $tEnd   = $startDate->copy()->addDays($mw['finish_day'])->subSecond()->format('Y-m-d H:i:s');
            $engId  = in_array($mw['title'], ['Stagging', 'Installation and Configuration', 'Monitoring']) ? ($engRaihan?->id ?: $engSyaiful?->id) : ($engSyaiful?->id ?: $engRaihan?->id);

            if ($matchedTask) {
                $needsUpdate = false;
                if (!str_contains($matchedTask->description ?? '', 'StartingDay:')) {
                    $cleanDesc = trim(preg_replace('/\[[^\]]+\]/i', '', $matchedTask->description ?? $mw['description']));
                    $matchedTask->description = ($cleanDesc ? $cleanDesc . ' ' : '') . $expectedMeta;
                    $needsUpdate = true;
                }
                if ($matchedTask->title !== $mw['title']) {
                    $matchedTask->title = $mw['title'];
                    $needsUpdate = true;
                }
                if (empty($matchedTask->start_date) || !str_contains($matchedTask->description ?? '', "StartingDay: {$mw['starting_day']}")) {
                    $matchedTask->start_date = $tStart;
                    $matchedTask->deadline = $tEnd;
                    $cleanDesc = trim(preg_replace('/\[[^\]]+\]/i', '', $matchedTask->description ?? $mw['description']));
                    $matchedTask->description = ($cleanDesc ? $cleanDesc . ' ' : '') . $expectedMeta;
                    $needsUpdate = true;
                }
                if ($mw['title'] === 'Design Review Meeting' && ($matchedTask->progress < 100 || $matchedTask->status !== 'Completed')) {
                    $matchedTask->progress = 100;
                    $matchedTask->status = 'Completed';
                    $needsUpdate = true;
                }
                if ($mw['title'] === 'Survey and Assessment' && ($matchedTask->progress < 100 || $matchedTask->status !== 'Completed')) {
                    $matchedTask->progress = 100;
                    $matchedTask->status = 'Completed';
                    $needsUpdate = true;
                }
                if ($mw['title'] === 'HLD and LLD Documentation' && ($matchedTask->progress < 100 || $matchedTask->status !== 'Completed')) {
                    $matchedTask->progress = 100;
                    $matchedTask->status = 'Completed';
                    $needsUpdate = true;
                }
                if ($matchedTask->progress === null) {
                    $matchedTask->progress = $mw['default_prog'];
                    $matchedTask->status = $mw['default_stat'];
                    $needsUpdate = true;
                }
                if ($needsUpdate) {
                    $matchedTask->saveQuietly();
                }
            } else {
                Task::create([
                    'project_id'  => $project->id,
                    'title'       => $mw['title'],
                    'engineer_id' => $engId,
                    'priority'    => 'High',
                    'status'      => $mw['default_stat'],
                    'progress'    => $mw['default_prog'],
                    'start_date'  => $tStart,
                    'deadline'    => $tEnd,
                    'description' => $mw['description'] . ' ' . $expectedMeta,
                    'created_by'  => $pmRizki ?: 1,
                ]);
            }
        }

        // Sinkronisasi akumulasi progres proyek dari task yang ada
        $this->syncProjectProgressFromTasks($project);
    }
    public function projectDashboard(Request $request, Project $project)
    {
        $authUser = auth()->user();
        $isLeadUser = $authUser && ScopeHelper::isTeamLeader($authUser);
        $isPmoUser = $authUser && (ScopeHelper::isPmo($authUser) || ($project->pm_id && $authUser->id == $project->pm_id) || ScopeHelper::isExecutive($authUser));

        $project->load([
            'pm:id,name,email',
            'division:id,name',
            'tasks.engineer:id,name',
            'creator:id,name',
        ]);

        $isPpatkProject = str_contains(strtolower($project->name), 'ppat')
            || str_contains(strtolower($project->client ?? ''), 'ppat')
            || str_contains(strtolower($project->name), 'peremajaan router');

        // Pastikan proyek resmi PPATK selalu ada di database
        $hasPpatk = Project::withTrashed()
            ->where(function ($q) {
                $q->where('name', 'like', '%PPAT%')
                    ->orWhere('name', 'like', '%Peremajaan Router%')
                    ->orWhere('client', 'like', '%PPAT%');
            })
            ->exists();
        if (!$hasPpatk) {
            try {
                $ppatkSeeder = new \Database\Seeders\PpatkProjectSeeder();
                $ppatkSeeder->run();
            } catch (\Throwable $e) {}
        }

        $allPmoProjects = Project::whereNotIn('name', [
            'DAY OFF', 'Day Off', 'Day Off / Cuti', 'CUTI', 'Cuti', 'SLA aa', 'SLA a'
        ])
        ->where('client', '!=', 'Internal / Umum')
        ->where('name', 'not like', '%Training%')
        ->where('name', 'not like', '%Meeting%')
        ->orderBy('id')
        ->get(['id', 'name', 'client', 'status']);

        if ($isPpatkProject && $project->tasks()->count() === 0) {
            $this->ensurePpatkProjectData($project);
            $project->load([
                'pm:id,name,email',
                'division:id,name',
                'tasks.engineer:id,name',
                'creator:id,name',
            ]);
        }

        $contractValue = (float) ($project->contract_value ?: ($project->quotation_amount ?: 0));
        $expenses = $project->expenses_list;
        $totalSpend = (float) $expenses->sum('amount');
        $remainingBudget = max(0, $contractValue - $totalSpend);
        $spendPercentage = $contractValue > 0 ? min(100, round(($totalSpend / $contractValue) * 100, 1)) : 0;

        $totalTasks = $project->tasks->count();
        $tasksCompletedCount = $project->tasks->whereIn('status', ['Done', 'Completed'])->count();
        $tasksInProgressCount = $project->tasks->whereIn('status', ['In Progress', 'Ongoing', 'in_progress'])->count();
        $tasksNotStartedCount = max(0, $totalTasks - $tasksInProgressCount - $tasksCompletedCount);
        $completedTasks = $tasksCompletedCount;
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

            $startStr = $t->start_date ? \Carbon\Carbon::parse($t->start_date)->format('d M') : null;
            $endStr = $t->deadline ? \Carbon\Carbon::parse($t->deadline)->format('d M') : null;
            $periodStr = ($startStr && $endStr) ? ($startStr === $endStr ? $endStr : "$startStr - $endStr") : ($endStr ?: $startStr);

            $assignedMap[$engName]['tasks'][] = [
                'id'          => $t->id,
                'title'       => $t->title,
                'status'      => $t->status ?: 'Assigned',
                'priority'    => $t->priority ?: 'Medium',
                'progress'    => (int) ($t->progress ?: 0),
                'start_date'  => $startStr,
                'deadline'    => $endStr,
                'period'      => $periodStr,
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

        // 1. Rentang Tanggal & Durasi Lengkap Proyek (Kontrak SPK & Tanggal Mulai)
        $startDate = $project->start_date ? \Carbon\Carbon::parse($project->start_date)->startOfDay() : ($project->created_at ? \Carbon\Carbon::parse($project->created_at)->startOfDay() : now()->startOfDay());
        $deadlineDate = $project->deadline ? \Carbon\Carbon::parse($project->deadline)->endOfDay() : $startDate->copy()->addWeeks(8)->endOfDay();
        
        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $scheduleType = request()->query('schedule_type', $handoverData['schedule_type'] ?? 'waterfall');
        if (!in_array($scheduleType, ['waterfall', 'hybrid'])) {
            $scheduleType = 'waterfall';
        }

        $estimateDuration = $handoverData['estimate_project_duration'] ?? null;
        $executionWeeks = $handoverData['execution_weeks'] ?? null;

        // Parse Activity List Tasks (WBS Breakdown) & Hubungkan ke Kurva S
        $tasksList = $project->tasks()->orderBy('id')->get();
        $totalTaskCount = $tasksList->count();

        $parsedTasks = [];
        $totalTaskBobotSum = 0.0;
        $maxTaskFinishDay = 0;
        $minTaskStartDay = null;

        // Pointer untuk sekuensial Waterfall
        $waterfallPointer = 0;

        foreach ($tasksList as $t) {
            $bobotVal = 0.0;
            if (preg_match('/Bobot:\s*([\d\.]+)%/i', $t->description ?? '', $bm)) {
                $bobotVal = (float) $bm[1];
            }
            $totalTaskBobotSum += $bobotVal;

            $duration = 1;
            if (preg_match('/Duration:\s*(\d+)/i', $t->description ?? '', $durm)) {
                $duration = max(1, (int) $durm[1]);
            } elseif ($t->start_date && $t->deadline) {
                $duration = max(1, (int) round(\Carbon\Carbon::parse($t->start_date)->diffInDays(\Carbon\Carbon::parse($t->deadline))) + 1);
            } elseif (preg_match('/FinishDay:\s*(\d+)/i', $t->description ?? '', $fdm) && preg_match('/StartingDay:\s*(\d+)/i', $t->description ?? '', $sdm)) {
                $duration = max(1, (int)$fdm[1] - (int)$sdm[1]);
            }

            $plannedStart = null;
            if (preg_match('/PlannedStart:\s*(\d+)/i', $t->description ?? '', $psm)) {
                $plannedStart = (int) $psm[1];
            } elseif (preg_match('/StartingDay:\s*(\d+)/i', $t->description ?? '', $sdm)) {
                $plannedStart = (int) $sdm[1];
            }

            if ($scheduleType === 'hybrid') {
                // Mode Hybrid: Task dapat berjalan paralel / tumpang-tindih (concurrent) berdasarkan Planned Start
                $startingDay = $plannedStart !== null ? $plannedStart : 0;
                $finishDay = $startingDay + $duration;
            } else {
                // Mode Waterfall: Strictly sequential (Finish-to-Start)
                $startingDay = $waterfallPointer;
                $finishDay = $startingDay + $duration;
                $waterfallPointer = $finishDay;
            }

            if ($minTaskStartDay === null || $startingDay < $minTaskStartDay) {
                $minTaskStartDay = $startingDay;
            }
            if ($finishDay > $maxTaskFinishDay) {
                $maxTaskFinishDay = $finishDay;
            }

            $wbsWeek = max(1, (int) ceil(($finishDay > 0 ? $finishDay : ($startingDay + 1)) / 7));

            $dailyWeight = $duration > 0 ? round($bobotVal / $duration, 2) : 0.0;
            $prog = (float) ($t->progress ?: (in_array($t->status, ['Done', 'Completed']) ? 100 : 0));

            $parsedTasks[] = [
                'id'            => $t->id,
                'title'         => $t->title,
                'duration'      => $duration,
                'planned_start' => $plannedStart ?? $startingDay,
                'starting_day'  => $startingDay,
                'finish_day'    => $finishDay,
                'week'          => $wbsWeek,
                'daily_weight'  => $dailyWeight,
                'bobot'         => $bobotVal,
                'progress'      => $prog,
                'status'        => $t->status,
                'start_date'    => $t->start_date ? \Carbon\Carbon::parse($t->start_date)->startOfDay() : null,
                'deadline'      => $t->deadline ? \Carbon\Carbon::parse($t->deadline)->endOfDay() : null,
            ];
        }

        // Urutkan parsedTasks berdasarkan urutan sekuensial WBS (starting_day & finish_day)
        usort($parsedTasks, function($a, $b) {
            $sdA = $a['starting_day'] ?? 999;
            $sdB = $b['starting_day'] ?? 999;
            if ($sdA === $sdB) {
                return ($a['finish_day'] ?? 999) <=> ($b['finish_day'] ?? 999);
            }
            return $sdA <=> $sdB;
        });

        // Hitung batas tanggal WBS berdasarkan task jika ada
        $earliestTaskDate = null;
        $latestTaskDate = null;
        foreach ($parsedTasks as $pt) {
            if ($pt['start_date']) {
                if (!$earliestTaskDate || $pt['start_date']->lt($earliestTaskDate)) {
                    $earliestTaskDate = $pt['start_date']->copy();
                }
            }
            if ($pt['deadline']) {
                if (!$latestTaskDate || $pt['deadline']->gt($latestTaskDate)) {
                    $latestTaskDate = $pt['deadline']->copy();
                }
            }
        }

        // Tentukan Rentang Pelaksanaan WBS (Berdasarkan PM Execution Timeline dari Task WBS)
        $wbsStartDate = $startDate->copy()->startOfDay();
        if ($estimateDuration) {
            $totalProjectDays = (int) $estimateDuration;
            $totalProjectWeeks = $executionWeeks ? (int) $executionWeeks : max(1, (int) ceil($totalProjectDays / 7));
            $wbsEndDate = $wbsStartDate->copy()->addDays(max(0, $totalProjectDays - 1))->endOfDay();
        } elseif ($maxTaskFinishDay > 0) {
            // Berdasarkan finish day terbesar dari WBS task (Waterfall = sum durasi, Hybrid = max finish offset)
            $totalProjectDays = $maxTaskFinishDay;
            $totalProjectWeeks = max(1, (int) ceil($totalProjectDays / 7));
            $wbsEndDate = $wbsStartDate->copy()->addDays(max(0, $totalProjectDays - 1))->endOfDay();
        } elseif ($isPpatkProject) {
            $totalProjectDays = 20;
            $totalProjectWeeks = 3;
            $wbsEndDate = $wbsStartDate->copy()->addDays(19)->endOfDay();
        } elseif ($earliestTaskDate && $latestTaskDate) {
            $totalProjectDays = max(1, (int) round($earliestTaskDate->diffInDays($latestTaskDate)) + 1);
            $totalProjectWeeks = max(1, (int) ceil($totalProjectDays / 7));
            $wbsStartDate = $earliestTaskDate->copy()->startOfDay();
            $wbsEndDate = $latestTaskDate->copy()->endOfDay();
        } else {
            // Fallback jika belum ada task WBS
            $totalProjectDays = max(1, (int) round($startDate->diffInDays($deadlineDate)));
            $totalProjectWeeks = max(1, (int) ceil($startDate->diffInWeeks($deadlineDate)));
            $wbsEndDate = $deadlineDate->copy()->endOfDay();
        }

        // Pastikan totalWeeks dan teks timeline selaras dengan WBS
        $totalWeeks = max(1, $totalProjectWeeks);
        $projectTimelineText = $wbsStartDate->format('d M Y') . ' s/d ' . $wbsEndDate->format('d M Y') . ' (' . $totalProjectDays . ' Hari Pelaksanaan, Kontrak SPK s/d ' . $deadlineDate->format('d M Y') . ')';

        // Hitung total durasi WBS dari seluruh task
        $totalTaskDuration = array_sum(array_column($parsedTasks, 'duration'));

        // Jika total bobot task tidak sama dengan 100% (misal seeder lama/parsial 66.7% atau 70.1% atau 0%),
        // hitung Activity Weight secara proporsional berdasarkan durasi sesuai rumus resmi Excel:
        // Activity Weight (%) = (Duration / Total Duration) * 100%
        // Daily Weight (%) = Activity Weight (%) / Duration
        if ($totalTaskCount > 0 && abs($totalTaskBobotSum - 100.0) > 0.5) {
            $runningBobotSum = 0.0;
            $countPt = count($parsedTasks);
            foreach ($parsedTasks as $idx => &$pt) {
                if ($totalTaskDuration > 0) {
                    $calcedBobot = round(($pt['duration'] / $totalTaskDuration) * 100.0, 2);
                } else {
                    $calcedBobot = round(100.0 / $countPt, 2);
                }

                // Pada item terakhir, pastikan akumulasi tepat 100.0%
                if ($idx === $countPt - 1) {
                    $calcedBobot = round(100.0 - $runningBobotSum, 2);
                }
                $runningBobotSum += $calcedBobot;

                $pt['bobot'] = $calcedBobot;
                $pt['daily_weight'] = $pt['duration'] > 0 ? round($pt['bobot'] / $pt['duration'], 2) : 0.0;
            }
            unset($pt);
            $totalTaskBobotSum = 100.0;
        } else {
            // Pastikan daily_weight terisi sesuai rumus = Bobot / Durasi
            foreach ($parsedTasks as &$pt) {
                if ($pt['duration'] > 0 && empty($pt['daily_weight'])) {
                    $pt['daily_weight'] = round($pt['bobot'] / $pt['duration'], 2);
                }
            }
            unset($pt);
        }

        // Pastikan setiap task memiliki start_date dan deadline yang valid secara sekuensial
        $taskPointer = $wbsStartDate->copy()->startOfDay();
        foreach ($parsedTasks as &$pt) {
            if (!$pt['start_date']) {
                if ($pt['starting_day'] !== null) {
                    $pt['start_date'] = $wbsStartDate->copy()->addDays($pt['starting_day'])->startOfDay();
                    $ptFinish = $pt['finish_day'] !== null ? $pt['finish_day'] : ($pt['starting_day'] + $pt['duration']);
                    $pt['deadline'] = $wbsStartDate->copy()->addDays(max(0, $ptFinish - 1))->endOfDay();
                } else {
                    $pt['start_date'] = $taskPointer->copy();
                    $pt['deadline'] = $taskPointer->copy()->addDays(max(0, $pt['duration'] - 1))->endOfDay();
                    $taskPointer = $pt['deadline']->copy()->startOfDay()->addDay();
                }
            }
        }
        unset($pt);

        // Array rentang tanggal per minggu (untuk header tabel Sheet 2 & Kurva S Sheet 4)
        $weekDateRanges = [];
        for ($w = 1; $w <= $totalWeeks; $w++) {
            $wStart = $wbsStartDate->copy()->addWeeks($w - 1)->startOfDay();
            if ($w == $totalWeeks) {
                $wEnd = $wbsEndDate->copy()->endOfDay();
            } else {
                $wEnd = $wbsStartDate->copy()->addWeeks($w)->subDay()->endOfDay();
            }
            if ($wStart->format('Y') === $wEnd->format('Y')) {
                if ($wStart->format('M') === $wEnd->format('M')) {
                    $weekDateRanges[$w] = $wStart->format('d') . ' - ' . $wEnd->format('d M');
                } else {
                    $weekDateRanges[$w] = $wStart->format('d M') . ' - ' . $wEnd->format('d M');
                }
            } else {
                $weekDateRanges[$w] = $wStart->format('d M y') . ' - ' . $wEnd->format('d M y');
            }
        }

        // 1. Hitung total bobot dan earned EV aktual dari parsedTasks secara dinamis
        // Rumus Sesuai Gambar 4: Actual Cumulative = sum of (task completion % x activity weight)
        $totalEarnedEV = 0.0;
        foreach ($parsedTasks as $pt) {
            $totalEarnedEV += ($pt['bobot'] * ($pt['progress'] / 100.0));
        }
        $actualEV = round($totalEarnedEV, 2);

        // Update progress project di DB jika berubah
        if (abs((float) $project->progress - $actualEV) > 0.01 && $totalTaskCount > 0) {
            $project->progress = $actualEV;
            $project->saveQuietly();
        }

        $currentProgressPercent = $actualEV > 0 ? $actualEV : (float) ($project->progress ?: 0.0);

        // Weekly Weight distribution per task untuk PPATK sesuai Gambar Excel Resmi (3 Minggu / 20 Hari)
        $ppatkWeeklyDistMap = [
            'MOS - Material On Site'         => [1 => 5.0, 2 => 0.0, 3 => 0.0],
            'Stagging'                       => [1 => 25.0, 2 => 0.0, 3 => 0.0],
            'Installation and Configuration' => [1 => 0.0, 2 => 10.0, 3 => 0.0],
            'Integrasi'                      => [1 => 0.0, 2 => 10.0, 3 => 0.0],
            'Migrasi'                        => [1 => 0.0, 2 => 10.0, 3 => 0.0],
            'User Acceptance'                => [1 => 0.0, 2 => 10.0, 3 => 0.0],
            'Monitoring'                     => [1 => 0.0, 2 => 0.0, 3 => 10.0],
            'Project Documentation'          => [1 => 0.0, 2 => 0.0, 3 => 10.0],
            'Documentation'                  => [1 => 0.0, 2 => 0.0, 3 => 10.0],
            'BAST'                           => [1 => 0.0, 2 => 0.0, 3 => 10.0],
        ];

        // 1. Matriks Weekly Weight (plan) per task (Waterfall & Hybrid Distribution)
        $weeklyWeightMatrix = [];
        $totalWeeklyWeightPlan = array_fill(1, $totalWeeks, 0.0);

        foreach ($parsedTasks as &$pt) {
            $tTitle = $pt['title'];
            $wDist = [];
            $duration = $pt['duration'];
            $bobot = $pt['bobot'];

            // Khusus PPATK bawaan seeder jika Waterfall dan ada mapping static
            if ($isPpatkProject && $scheduleType === 'waterfall' && isset($ppatkWeeklyDistMap[$tTitle])) {
                $wDist = $ppatkWeeklyDistMap[$tTitle];
                for ($w = 1; $w <= $totalWeeks; $w++) {
                    $wVal = (float) ($wDist[$w] ?? 0.0);
                    $totalWeeklyWeightPlan[$w] += $wVal;
                }
            } else {
                $tStartDay = $pt['starting_day'];
                $tFinishDay = $pt['finish_day'];
                $taskAllocatedWeight = 0.0;
                $lastActiveWeek = null;

                for ($w = 1; $w <= $totalWeeks; $w++) {
                    $wStartDay = ($w - 1) * 7;
                    $wEndDay = $w * 7;
                    $overlapStart = max($tStartDay, $wStartDay);
                    $overlapEnd = min($tFinishDay, $wEndDay);
                    $overlapDays = max(0, $overlapEnd - $overlapStart);

                    $wVal = 0.0;
                    if ($duration > 0 && $overlapDays > 0) {
                        $wVal = round(($overlapDays / $duration) * $bobot, 2);
                        $lastActiveWeek = $w;
                    }
                    $wDist[$w] = $wVal;
                    $taskAllocatedWeight += $wVal;
                }

                // Normalisasi pembulatan per baris task agar check tepat = bobot task
                if ($lastActiveWeek !== null && abs($taskAllocatedWeight - $bobot) > 0.001) {
                    $diff = round($bobot - $taskAllocatedWeight, 2);
                    $wDist[$lastActiveWeek] = round($wDist[$lastActiveWeek] + $diff, 2);
                }

                for ($w = 1; $w <= $totalWeeks; $w++) {
                    $totalWeeklyWeightPlan[$w] += $wDist[$w];
                }
            }

            $weeklyWeightMatrix[] = [
                'id'            => $pt['id'],
                'title'         => $pt['title'],
                'duration'      => $pt['duration'],
                'planned_start' => $pt['planned_start'],
                'starting_day'  => $pt['starting_day'],
                'finish_day'    => $pt['finish_day'],
                'weight'        => $pt['bobot'],
                'weeks'         => $wDist,
                'check'         => round(array_sum($wDist), 2),
            ];
        }
        unset($pt);

        for ($w = 1; $w <= $totalWeeks; $w++) {
            $totalWeeklyWeightPlan[$w] = round($totalWeeklyWeightPlan[$w], 2);
        }

        // Plan Cumulative % per week (Strictly bounded and normalized to 100.0%)
        $totalWeeklyWeightCum = [];
        $cumPlanAccum = 0.0;
        for ($w = 1; $w <= $totalWeeks; $w++) {
            $cumPlanAccum += $totalWeeklyWeightPlan[$w];
            $totalWeeklyWeightCum[$w] = min(100.0, round($cumPlanAccum, 2));
        }

        // Kunci minggu terakhir tepat 100.0%
        if ($totalWeeks > 0) {
            $totalWeeklyWeightCum[$totalWeeks] = 100.0;
            if ($totalWeeks > 1) {
                $totalWeeklyWeightPlan[$totalWeeks] = round(100.0 - $totalWeeklyWeightCum[$totalWeeks - 1], 2);
            } else {
                $totalWeeklyWeightPlan[1] = 100.0;
            }
        }

        // 2. Matriks Actual Progress per Task (Sheet 3)
        $actualProgressRows = [];
        $totalActualProgressSum = 0.0;
        foreach ($parsedTasks as $pt) {
            $actContribution = round(($pt['bobot'] * ($pt['progress'] / 100.0)), 1);
            $totalActualProgressSum += $actContribution;
            $actualProgressRows[] = [
                'id'       => $pt['id'],
                'title'    => $pt['title'],
                'weight'   => $pt['bobot'],
                'progress' => (float) $pt['progress'],
                'actual'   => $actContribution,
                'task'     => $project->tasks->firstWhere('id', $pt['id']),
            ];
        }
        $totalActualProgressSum = round($totalActualProgressSum, 1);
        $actualEV = $totalActualProgressSum;

        // 3. Hitung Progres Aktual Mingguan (Actual Weekly) dari pembobotan task WBS yang selesai
        // Mengikuti distribusi bobot mingguan task (Weekly Weight) x % progres task aktual
        $actualWeeklyCalculated = array_fill(1, $totalWeeks, 0.0);
        foreach ($weeklyWeightMatrix as $row) {
            $tId = $row['id'];
            $ptMatch = collect($parsedTasks)->firstWhere('id', $tId);
            $tProg = $ptMatch ? ($ptMatch['progress'] / 100.0) : 0.0;
            
            for ($w = 1; $w <= $totalWeeks; $w++) {
                $wPlan = $row['weeks'][$w] ?? 0.0;
                if ($wPlan > 0 && $tProg > 0) {
                    $actualWeeklyCalculated[$w] += ($wPlan * $tProg);
                }
            }
        }
        for ($w = 1; $w <= $totalWeeks; $w++) {
            $actualWeeklyCalculated[$w] = round($actualWeeklyCalculated[$w], 1);
        }

        // Tentukan minggu aktif tertinggi yang memiliki capaian progres riil
        $maxActiveWeek = 0;
        for ($w = 1; $w <= $totalWeeks; $w++) {
            if ($actualWeeklyCalculated[$w] > 0) {
                $maxActiveWeek = $w;
            }
        }

        // Distribusi Kurva S Mingguan (Actual Cumulative per Week)
        $actualCumulativePerWeek = [];
        $weekReportedFlags = array_fill(1, $totalWeeks, 0);
        $savedWeekly = $handoverData['s_curve_weekly'] ?? [];
        $accumActual = 0.0;

        for ($w = 1; $w <= $totalWeeks; $w++) {
            $accumActual = round($accumActual + $actualWeeklyCalculated[$w], 1);
            
            // Cek apakah ada manual override dari PM untuk minggu ini
            if (isset($savedWeekly[$w]['actual']) && $savedWeekly[$w]['actual'] !== null && $savedWeekly[$w]['actual'] !== '') {
                $actualCumulativePerWeek[$w] = (float) $savedWeekly[$w]['actual'];
                $weekReportedFlags[$w] = 1;
            } elseif ($w <= $maxActiveWeek || ($w == 1 && $actualEV > 0)) {
                // Minggu aktif berjalan yang sudah memiliki progres
                $actualCumulativePerWeek[$w] = min(100.0, $accumActual);
                $weekReportedFlags[$w] = 1;
            } else {
                // Minggu masa depan yang belum dilaporkan / belum berjalan
                $actualCumulativePerWeek[$w] = null;
                $weekReportedFlags[$w] = 0;
            }
        }

        $actualProgressInputMatrix = [];
        $actualProgressCalcMatrix = [];

        // Tentukan minggu aktif tertinggi yang dilaporkan
        $maxReportedWeek = 0;
        for ($w = $totalWeeks; $w >= 1; $w--) {
            if ($weekReportedFlags[$w] == 1) {
                $maxReportedWeek = $w;
                break;
            }
        }
        if ($maxReportedWeek === 0 && $totalEarnedEV > 0) {
            $maxReportedWeek = 1;
            $weekReportedFlags[1] = 1;
        }

        $currentWeek = max(1, min($totalWeeks, $maxReportedWeek ?: 1));
        $currentProgressPercent = $maxReportedWeek > 0 ? $actualCumulativePerWeek[$maxReportedWeek] : ($actualEV > 0 ? $actualEV : (float) ($project->progress ?: 0.0));

        if (abs((float) $project->progress - $currentProgressPercent) > 0.01) {
            $project->progress = $currentProgressPercent;
            $project->saveQuietly();
        }

        // Data Progres Mingguan yang tersimpan (Catatan / override)
        $savedWeekly = $handoverData['s_curve_weekly'] ?? [];

        // Generate Data Mingguan Lengkap (untuk Tabel & Grafik Kurva S)
        $weeklyRecords = [];
        $sCurveLabels = [];
        $sCurvePlan = [];
        $sCurveActual = [];

        // Minggu 0 (Baseline Start Sesuai Gambar Excel)
        $sCurveLabels[] = 0;
        $sCurvePlan[] = 0.0;
        $sCurveActual[] = 0.0;

        $weeklyRecords[] = [
            'week'           => 0,
            'date_range'     => 'Kick-off Baseline (' . $startDate->format('d M Y') . ')',
            'plan_weekly'    => 0.0,
            'plan_percent'   => 0.0,
            'actual_weekly'  => 0.0,
            'actual_percent' => 0.0,
            'variance'       => 0.0,
            'chart_helper'   => 0.0,
            'is_current'     => false,
            'is_passed'      => true,
            'is_future'      => false,
            'is_recorded'    => true,
            'notes'          => 'Baseline Awal SPK (0.0%)',
            'tasks_in_week'  => ['Kick-off & Baseline SPK'],
            'tasks_count'    => 1,
        ];

        // Mapping WBS tasks per week
        $ppatkWeeklyTaskMap = [
            1 => ['MOS - Material On Site', 'Stagging'],
            2 => ['Installation and Configuration', 'Integrasi', 'Migrasi', 'User Acceptance'],
            3 => ['Monitoring', 'Project Documentation', 'Documentation', 'BAST'],
        ];

        for ($w = 1; $w <= $totalWeeks; $w++) {
            $wStart = $wbsStartDate->copy()->addWeeks($w - 1)->startOfDay();
            if ($w == $totalWeeks) {
                $wEnd = $wbsEndDate->copy()->endOfDay();
            } else {
                $wEnd = $wbsStartDate->copy()->addWeeks($w)->subDay()->endOfDay();
            }
            $dateRange = $weekDateRanges[$w] ?? ($wStart->format('d M') . ' - ' . $wEnd->format('d M Y'));

            $tasksInThisWeek = [];
            if ($isPpatkProject && isset($ppatkWeeklyTaskMap[$w])) {
                $tasksInThisWeek = $ppatkWeeklyTaskMap[$w];
            }
            foreach ($parsedTasks as $pt) {
                if (!in_array($pt['title'], $tasksInThisWeek)) {
                    $tStart = $pt['start_date'];
                    $tEnd = $pt['deadline'];
                    $tWeek = $pt['week'];
                    if (($tWeek && $tWeek == $w) || ($tStart && $tEnd && $tStart->lte($wEnd) && $tEnd->gte($wStart))) {
                        $tasksInThisWeek[] = $pt['title'];
                    }
                }
            }

            $planVal = $totalWeeklyWeightCum[$w] ?? 100.0;
            $planWeeklyVal = $totalWeeklyWeightPlan[$w] ?? 0.0;

            $isRecorded = ($weekReportedFlags[$w] == 1);
            $actualVal = null;
            $actualWeeklyVal = null;
            $variance = null;
            $chartHelper = '#N/A';

            if ($isRecorded) {
                $actualVal = $actualCumulativePerWeek[$w];
                $prevAct = ($w == 1) ? 0.0 : ($actualCumulativePerWeek[$w - 1] ?? 0.0);
                $actualWeeklyVal = round($actualVal - $prevAct, 1);
                $variance = round($actualVal - $planVal, 1);
                $chartHelper = $actualVal;
            }

            $notes = $savedWeekly[$w]['notes'] ?? null;

            $weeklyRecords[] = [
                'week'           => $w,
                'date_range'     => $dateRange,
                'plan_weekly'    => $planWeeklyVal,
                'plan_percent'   => $planVal,
                'actual_weekly'  => $actualWeeklyVal,
                'actual_percent' => $actualVal,
                'variance'       => $variance,
                'chart_helper'   => $chartHelper,
                'is_current'     => ($w === $currentWeek),
                'is_passed'      => ($w < $currentWeek),
                'is_future'      => ($w > $currentWeek),
                'is_recorded'    => $isRecorded,
                'notes'          => $notes,
                'tasks_in_week'  => $tasksInThisWeek,
                'tasks_count'    => count($tasksInThisWeek),
            ];

            $sCurveLabels[] = $w;
            $sCurvePlan[] = $planVal;
            $sCurveActual[] = $actualVal;
        }

        // Catatan PM Terbaru untuk Ringkasan Status Kurva (Dinamis dari WBS Tasks)
        $latestPmNote = null;
        if (!empty($savedWeekly[$currentWeek]['notes'])) {
            $latestPmNote = $savedWeekly[$currentWeek]['notes'];
        } else {
            for ($w = $currentWeek; $w >= 1; $w--) {
                if (!empty($savedWeekly[$w]['notes'])) {
                    $latestPmNote = $savedWeekly[$w]['notes'];
                    break;
                }
            }
        }
        if (!$latestPmNote) {
            $activeTaskNotes = [];
            foreach ($parsedTasks as $pt) {
                if ($pt['progress'] > 0) {
                    if ($pt['progress'] >= 100) {
                        $activeTaskNotes[] = $pt['title'] . ' selesai 100%';
                    } else {
                        $activeTaskNotes[] = $pt['title'] . ' ' . round($pt['progress']) . '%';
                    }
                }
            }
            if (!empty($activeTaskNotes)) {
                $latestPmNote = implode(', ', $activeTaskNotes);
            } elseif (!empty($project->special_notes)) {
                $latestPmNote = $project->special_notes;
            }
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

            // Plan Harian: Diisi manual oleh PM via Update Progres (default null jika belum diisi)
            $planDVal = null;
            if (isset($savedDaily[$d]['plan']) && $savedDaily[$d]['plan'] !== null && $savedDaily[$d]['plan'] !== '') {
                $planDVal = (float) $savedDaily[$d]['plan'];
            }

            $actualDVal = null;
            $isRecordedD = false;
            $notesD = null;

            if (isset($savedDaily[$d])) {
                $actualDVal = (isset($savedDaily[$d]['actual']) && $savedDaily[$d]['actual'] !== null && $savedDaily[$d]['actual'] !== '') ? (float) $savedDaily[$d]['actual'] : null;
                $notesD = $savedDaily[$d]['notes'] ?? null;
                $isRecordedD = ($actualDVal !== null || $planDVal !== null);
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
                'variance'       => ($actualDVal !== null && $planDVal !== null) ? round($actualDVal - $planDVal, 2) : null,
            ];

            $sCurveDailyLabels[] = $d;
            $sCurveDailyPlan[] = $planDVal;
            $sCurveDailyActual[] = $actualDVal;
        }

        // Varians Kurva S saat ini (Deviasi Realisasi vs Target Rencana)
        $curRec = collect($weeklyRecords)->firstWhere('week', $currentWeek);
        $planValAtCurrentWeek = $curRec['plan_percent'] ?? null;
        $currentVariance = $planValAtCurrentWeek !== null ? round($currentProgressPercent - $planValAtCurrentWeek, 2) : 0.0;
        
        if ($isPpatkProject) {
            $healthStatus = 'Healthy';
            $healthStatusText = 'Active S-Curves (Toleransi Normal)';
        }

        // Summary Progress Indicator (Hijau / Kuning / Merah)
        // Berdasarkan kesehatan task mingguan dan deviasi Kurva S
        $overdueTasks = $project->tasks->whereNotIn('status', ['Completed', 'Done'])
            ->filter(fn($t) => $t->deadline && \Carbon\Carbon::parse($t->deadline)->isPast());
        $overdueTasksCount = $overdueTasks->count();
        $tasksOnTrackCount = max(0, $totalTasks - $overdueTasksCount);

        $isBrandNewProject = ($currentProgressPercent == 0 && empty($savedWeekly) && $totalTasks == 0);
        if ($isBrandNewProject) {
            $currentVariance = 0.0;
            $summaryProgress = [
                'status'         => 'neutral',
                'color_name'     => 'Netral',
                'badge_class'    => 'bg-slate-100 text-slate-700 border-slate-200/80',
                'dot_class'      => 'bg-slate-400',
                'gradient_class' => 'from-slate-400 to-slate-500',
                'bg_card'        => 'bg-gradient-to-r from-slate-50/70 via-white to-slate-50 border-slate-200/80',
                'label'          => 'Proyek Baru (Siap Dimulai)',
                'headline'       => 'Proyek baru aktif, siap menyusun WBS dan timeline pelaksanaan',
                'description'    => 'Belum ada task atau progres mingguan yang diinput PM. Gunakan fitur Tambah Task atau Import Excel untuk memulai.',
                'variance'       => 0.0,
                'overdue_count'  => 0,
                'on_track_count' => 0,
            ];
        } elseif ($overdueTasksCount > 0 || $currentVariance < -10 || $healthStatus === 'Delayed') {
            $summaryProgress = [
                'status'         => 'red',
                'color_name'     => 'Merah',
                'badge_class'    => 'bg-rose-50 text-rose-700 border-rose-200/80',
                'dot_class'      => 'bg-rose-600',
                'gradient_class' => 'from-rose-500 to-red-600',
                'bg_card'        => 'bg-gradient-to-r from-rose-50/70 via-white to-slate-50 border-rose-200/80',
                'label'          => 'Melewati Tenggat Waktu (Kritis / At Risk)',
                'headline'       => 'Terdapat task mingguan melewati deadline atau deviasi Kurva S tertinggal',
                'description'    => "Terdapat {$overdueTasksCount} task melewati deadline dengan deviasi progres mingguan {$currentVariance}%. Diperlukan percepatan personil lapangan.",
                'variance'       => $currentVariance,
                'overdue_count'  => $overdueTasksCount,
                'on_track_count' => $tasksOnTrackCount,
            ];
        } elseif ($currentVariance < -2 || $healthStatus === 'At-Risk' || ($currentProgressPercent < 50 && $currentWeek > ($totalWeeks / 2))) {
            $summaryProgress = [
                'status'         => 'yellow',
                'color_name'     => 'Kuning',
                'badge_class'    => 'bg-amber-50 text-amber-800 border-amber-200/80',
                'dot_class'      => 'bg-amber-500',
                'gradient_class' => 'from-amber-400 to-amber-600',
                'bg_card'        => 'bg-gradient-to-r from-amber-50/70 via-white to-slate-50 border-amber-200/80',
                'label'          => 'Sedang Berjalan (Pertengahan / Toleransi Normal)',
                'headline'       => 'Pekerjaan berjalan dalam batas toleransi jadwal kerja mingguan',
                'description'    => "Deviasi mingguan tercatat {$currentVariance}% tanpa task kritis terlambat. Personil aktif mengejar milestone berjalan.",
                'variance'       => $currentVariance,
                'overdue_count'  => $overdueTasksCount,
                'on_track_count' => $tasksOnTrackCount,
            ];
        } else {
            $summaryProgress = [
                'status'         => 'green',
                'color_name'     => 'Hijau',
                'badge_class'    => 'bg-emerald-50 text-emerald-800 border-emerald-200/80',
                'dot_class'      => 'bg-emerald-500',
                'gradient_class' => 'from-emerald-500 to-teal-600',
                'bg_card'        => 'bg-gradient-to-r from-emerald-50/70 via-white to-slate-50 border-emerald-200/80',
                'label'          => 'Selesai Tepat Waktu (On Track)',
                'headline'       => 'Seluruh task mingguan tepat waktu sesuai target Kurva S',
                'description'    => "Progres aktual berjalan optimal (+{$currentVariance}%) dan 0 task terlambat. Distribusi tugas tim berada dalam jalur rencana.",
                'variance'       => $currentVariance,
                'overdue_count'  => 0,
                'on_track_count' => $totalTasks,
            ];
        }

        $sCurveUpdateDate = now()->format('d M Y');
        $divisions = \App\Models\Division::all();

        return view('pmo.project-dashboard', compact(
            'project',
            'authUser',
            'isLeadUser',
            'isPmoUser',
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
            'summaryProgress',
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
            'teamDelegatedByName',
            'startDate',
            'deadlineDate',
            'wbsStartDate',
            'wbsEndDate',
            'weekDateRanges',
            'tasksInProgressCount',
            'tasksCompletedCount',
            'tasksNotStartedCount',
            'latestPmNote',
            'weeklyWeightMatrix',
            'totalWeeklyWeightPlan',
            'totalWeeklyWeightCum',
            'actualProgressInputMatrix',
            'actualProgressCalcMatrix',
            'actualProgressRows',
            'totalActualProgressSum',
            'actualCumulativePerWeek',
            'weekReportedFlags',
            'allPmoProjects',
            'scheduleType'
        ));
    }

    /**
     * Update Progres Mingguan Kurva S oleh PM (Target Plan & Realisasi Aktual)
     */
    public function updateWeeklyProgress(Request $request, Project $project)
    {
        $validated = $request->validate([
            'week'   => 'required|integer|min:1|max:52',
            'plan'   => 'nullable|numeric|min:0|max:100',
            'actual' => 'nullable|numeric|min:0|max:100',
            'notes'  => 'nullable|string|max:255',
        ]);

        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $weekly = $handoverData['s_curve_weekly'] ?? [];
        $weekly[$validated['week']] = [
            'plan'       => (isset($validated['plan']) && $validated['plan'] !== '') ? (float) $validated['plan'] : null,
            'actual'     => (isset($validated['actual']) && $validated['actual'] !== '') ? (float) $validated['actual'] : null,
            'notes'      => $validated['notes'] ?? null,
            'updated_at' => now()->toDateTimeString(),
            'updated_by' => auth()->user()?->name ?? 'PM',
        ];
        $handoverData['s_curve_weekly'] = $weekly;
        $project->handover_data = $handoverData;

        // Sinkronkan progres utama proyek jika actual diisi
        if (isset($validated['actual']) && $validated['actual'] !== '') {
            $project->progress = (int) round($validated['actual']);
        }
        $project->save();

        $info = [];
        if (isset($validated['plan']) && $validated['plan'] !== '') $info[] = 'Plan: ' . $validated['plan'] . '%';
        if (isset($validated['actual']) && $validated['actual'] !== '') $info[] = 'Aktual: ' . $validated['actual'] . '%';
        $infoStr = !empty($info) ? implode(', ', $info) : 'data disimpan';

        return redirect()->back()->with('success', 'Progres Minggu ke-' . $validated['week'] . ' berhasil diperbarui (' . $infoStr . ')!');
    }
    /**
     * Update Matrix Actual Progress (Sheet 3) per task per minggu
     */
    public function updateActualProgressMatrix(Request $request, Project $project)
    {
        $tasksInput = $request->input('tasks', []);
        $matrix = $request->input('matrix', []);
        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];

        $tasks = $project->tasks()->get();

        if (!empty($tasksInput)) {
            $savedInputs = $handoverData['actual_progress_inputs'] ?? [];
            foreach ($tasks as $task) {
                if (isset($tasksInput[$task->id]['progress'])) {
                    $prog = (float) $tasksInput[$task->id]['progress'];
                    $prog = max(0, min(100, $prog));
                    $task->progress = $prog;
                    if ($prog >= 100) {
                        $task->status = 'Completed';
                    } elseif ($prog > 0) {
                        $task->status = 'In Progress';
                    } else {
                        $task->status = 'Pending';
                    }
                    $task->saveQuietly();

                    // Sync into week 1 inputs as baseline
                    $savedInputs[$task->id][1] = $prog;
                    $savedInputs[$task->title][1] = $prog;
                }
            }
            $handoverData['actual_progress_inputs'] = $savedInputs;
            $project->handover_data = $handoverData;
            $project->save();

            $this->syncProjectProgressFromTasks($project);

            if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Progres aktual berhasil tersimpan otomatis!',
                    'progress' => (float) $project->progress,
                ]);
            }

            return redirect()->route('pmo.projects.show', ['project' => $project->id, 'tab' => 'actual_progress'])
                ->with('success', 'Progres aktual per task berhasil disimpan!');
        }

        $handoverData['actual_progress_inputs'] = $matrix;
        $project->handover_data = $handoverData;

        // Sinkronkan progress task di DB dengan nilai terakhir yang dilaporkan
        foreach ($tasks as $task) {
            $key = (string) $task->id;
            $taskInputs = $matrix[$key] ?? ($matrix[$task->title] ?? []);
            
            $latestVal = null;
            for ($w = 5; $w >= 1; $w--) {
                if (isset($taskInputs[$w]) && $taskInputs[$w] !== '' && $taskInputs[$w] !== null) {
                    $latestVal = (float) $taskInputs[$w];
                    break;
                }
            }

            if ($latestVal !== null) {
                $task->progress = $latestVal;
                if ($latestVal >= 100) {
                    $task->status = 'Completed';
                } elseif ($latestVal > 0) {
                    $task->status = 'In Progress';
                }
                $task->saveQuietly();
            }
        }

        $project->save();
        $this->syncProjectProgressFromTasks($project);

        if ($request->wantsJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Progres aktual berhasil tersimpan otomatis!',
                'progress' => (float) $project->progress,
            ]);
        }

        return redirect()->route('pmo.projects.show', ['project' => $project->id, 'tab' => 'actual_progress'])
            ->with('success', 'Actual Progress matrix berhasil diperbarui dan disinkronkan ke Kurva S!');
    }

    /**
     * Update Progres Harian Kurva S oleh PM (Target Plan & Realisasi Aktual)
     */
    public function updateDailyProgress(Request $request, Project $project)
    {
        $validated = $request->validate([
            'day'    => 'required|integer|min:1|max:365',
            'plan'   => 'nullable|numeric|min:0|max:100',
            'actual' => 'nullable|numeric|min:0|max:100',
            'notes'  => 'nullable|string|max:255',
        ]);

        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $daily = $handoverData['s_curve_daily'] ?? [];
        $daily[$validated['day']] = [
            'plan'       => (isset($validated['plan']) && $validated['plan'] !== '') ? (float) $validated['plan'] : null,
            'actual'     => (isset($validated['actual']) && $validated['actual'] !== '') ? (float) $validated['actual'] : null,
            'notes'      => $validated['notes'] ?? null,
            'updated_at' => now()->toDateTimeString(),
            'updated_by' => auth()->user()?->name ?? 'PM',
        ];
        $handoverData['s_curve_daily'] = $daily;
        $project->handover_data = $handoverData;

        if (isset($validated['actual']) && $validated['actual'] !== '') {
            $project->progress = (int) round($validated['actual']);
        }
        $project->save();

        $info = [];
        if (isset($validated['plan']) && $validated['plan'] !== '') $info[] = 'Plan: ' . $validated['plan'] . '%';
        if (isset($validated['actual']) && $validated['actual'] !== '') $info[] = 'Aktual: ' . $validated['actual'] . '%';
        $infoStr = !empty($info) ? implode(', ', $info) : 'data disimpan';

        return redirect()->back()->with('success', 'Progres Hari ke-' . $validated['day'] . ' berhasil diperbarui (' . $infoStr . ')!');
    }

    /**
     * PM Memperbarui Periode Tanggal Proyek (Start Date & Deadline)
     */
    public function updateTimeline(Request $request, Project $project)
    {
        $validated = $request->validate([
            'start_date'                => 'required|date',
            'deadline'                  => 'required|date|after_or_equal:start_date',
            'estimate_project_duration' => 'nullable|integer|min:1|max:365',
            'execution_weeks'           => 'nullable|integer|min:1|max:52',
            'schedule_type'             => 'nullable|in:waterfall,hybrid',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $deadline = Carbon::parse($validated['deadline']);

        $project->start_date = $startDate->format('Y-m-d');
        $project->deadline = $deadline->format('Y-m-d');

        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        if ($request->filled('estimate_project_duration')) {
            $handoverData['estimate_project_duration'] = (int) $request->input('estimate_project_duration');
        }
        if ($request->filled('execution_weeks')) {
            $handoverData['execution_weeks'] = (int) $request->input('execution_weeks');
        }
        if ($request->filled('schedule_type')) {
            $handoverData['schedule_type'] = $request->input('schedule_type');
        }
        $project->handover_data = $handoverData;
        $project->save();

        $durDays = $request->filled('estimate_project_duration') ? (int) $request->input('estimate_project_duration') : max(1, (int) round($startDate->diffInDays($deadline)) + 1);

        return back()->with('success', "Durasi dan periode tanggal proyek berhasil diperbarui menjadi {$startDate->format('d M Y')} s/d {$deadline->format('d M Y')} ({$durDays} Hari)!");
    }

    /**
     * PM Memperbarui Metodologi Penjadwalan Proyek (Waterfall vs Hybrid)
     */
    public function updateScheduleType(Request $request, Project $project)
    {
        $validated = $request->validate([
            'schedule_type' => 'required|in:waterfall,hybrid',
        ]);

        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $handoverData['schedule_type'] = $validated['schedule_type'];
        $project->handover_data = $handoverData;
        $project->save();

        $typeName = $validated['schedule_type'] === 'hybrid' ? 'Hybrid (Concurrent / Overlapping)' : 'Waterfall (Sequential)';

        return back()->with('success', "Metodologi proyek berhasil diubah ke {$typeName}!");
    }

    /**
     * PM Memperbarui Nama Klien & PIC Klien Proyek
     */
    public function updateClient(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client'       => 'required|string|max:255',
            'customer_pic' => 'nullable|string|max:255',
        ]);

        $project->client = trim($validated['client']);
        $pic = !empty($validated['customer_pic']) ? trim($validated['customer_pic']) : null;
        $project->customer_pic_technical = $pic;
        $project->customer_pic_business  = $pic;

        $hd = is_array($project->handover_data) ? $project->handover_data : [];
        if ($pic !== null) {
            $hd['customer_pic'] = $pic;
        } else {
            unset($hd['customer_pic']);
        }
        $project->handover_data = $hd;
        $project->save();

        return back()->with('success', 'Informasi Klien & PIC berhasil diperbarui!');
    }

    /**
     * Penetapan / Perubahan Divisi Pelaksana Proyek oleh PM
     */
    public function assignDivision(Request $request, Project $project)
    {
        $teams = (array) $request->input('teams', []);
        if ($request->filled('custom_team_name')) {
            $teams[] = $request->input('custom_team_name');
        } elseif (empty($teams) && $request->filled('division_id') && $request->input('division_id') !== '__custom__') {
            $divObj = Division::find($request->input('division_id'));
            if ($divObj) {
                $teams[] = preg_replace('/^Divisi\s+/i', 'Tim ', $divObj->name);
            }
        }

        $cleanTeams = [];
        foreach ($teams as $t) {
            $t = trim((string) $t);
            if (!empty($t)) {
                $formatted = preg_match('/^tim\s+/i', $t) ? $t : ('Tim ' . $t);
                if (!in_array($formatted, $cleanTeams)) {
                    $cleanTeams[] = $formatted;
                }
            }
        }

        if (empty($cleanTeams)) {
            return back()->with('error', 'Silakan pilih minimal satu tim teknis pelaksana.');
        }

        $hasNetwork = false;
        $hasSecurity = false;
        $customTeams = [];

        foreach ($cleanTeams as $teamName) {
            $lower = strtolower($teamName);
            if (str_contains($lower, 'net') && !str_contains($lower, 'sec')) {
                $hasNetwork = true;
            } elseif (str_contains($lower, 'sec') && !str_contains($lower, 'net')) {
                $hasSecurity = true;
            } elseif (str_contains($lower, 'net') && str_contains($lower, 'sec')) {
                $hasNetwork = true;
                $hasSecurity = true;
            } else {
                $customTeams[] = $teamName;
            }
        }

        $divisionId = null;
        if ($hasNetwork && $hasSecurity) {
            $netSecDiv = Division::where('name', 'like', '%Network & Security%')->orWhere('code', 'NET-SEC')->first();
            $divisionId = $netSecDiv ? $netSecDiv->id : 4;
        } elseif ($hasNetwork) {
            $netDiv = Division::where('code', 'NET')->orWhere('name', 'like', '%Network%')->first();
            $divisionId = $netDiv ? $netDiv->id : 1;
        } elseif ($hasSecurity) {
            $secDiv = Division::where('code', 'SEC')->orWhere('name', 'like', '%Security%')->first();
            $divisionId = $secDiv ? $secDiv->id : 2;
        } elseif (!empty($customTeams)) {
            $cName = $customTeams[0];
            $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $cName), 0, 4)) ?: 'TIM';
            $cDiv = Division::firstOrCreate(
                ['name' => $cName],
                ['code' => $code]
            );
            $divisionId = $cDiv->id;
        } else {
            $divisionId = 1;
        }

        foreach ($customTeams as $cName) {
            $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $cName), 0, 4)) ?: 'TIM';
            Division::firstOrCreate(
                ['name' => $cName],
                ['code' => $code]
            );
        }

        $project->division_id = $divisionId;
        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $handoverData['assigned_teams'] = $cleanTeams;
        $project->handover_data = $handoverData;
        $project->save();

        $teamsDisplay = implode(' & ', $cleanTeams);
        return redirect()->back()->with('success', "Tim teknis pelaksana proyek berhasil ditetapkan: {$teamsDisplay}!");
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
        // Pastikan data proyek resmi PPATK selalu tersedia di portofolio PMO
        $hasPpatk = Project::withTrashed()
            ->where(function ($q) {
                $q->where('name', 'like', '%PPAT%')
                    ->orWhere('name', 'like', '%Peremajaan Router%')
                    ->orWhere('client', 'like', '%PPAT%');
            })
            ->exists();
        if (!$hasPpatk) {
            try {
                $ppatkSeeder = new \Database\Seeders\PpatkProjectSeeder();
                $ppatkSeeder->run();
            } catch (\Throwable $e) {}
        }

        // 1. Ambil data proyek aktif PMO (Hanya proyek komersial resmi tahap Deliver atau berkas Handover Implementasi dari Sales)
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
        // Kecualikan proyek yang murni Managed Service / Pemeliharaan SLA (karena itu ranah modul Managed Service)
        ->where('name', 'not like', 'Managed Service%')
        ->where(function($q) {
            $q->whereNull('handover_target')
              ->orWhere('handover_target', '!=', 'managed_service')
              ->orWhere('handover_target', 'both')
              ->orWhere('name', 'like', '%PPAT%')
              ->orWhere('client', 'like', '%PPAT%')
              ->orWhere('name', 'like', '%Peremajaan Router%');
        })
        ->where(function($q) {
            $q->whereNull('project_type')
              ->orWhere('project_type', 'not like', '%Managed Service%')
              ->orWhere('handover_target', 'both')
              ->orWhere('name', 'like', '%PPAT%')
              ->orWhere('client', 'like', '%PPAT%')
              ->orWhere('name', 'like', '%Peremajaan Router%');
        })
        ->where(function($q) {
            $q->where('sales_name', 'like', '%Nabylla%')
              ->orWhere('sales_name', 'like', '%Raiza%')
              ->orWhereNotNull('sales_name')
              ->orWhere('sales_stage', 'Closed Won')
              ->orWhere('status', 'In Progress')
              ->orWhereNotNull('ms_handover_status');
        })
        ->where(function($q) {
            $q->whereIn('stage', ['Deliver', 'Operate', 'Completed'])
              ->orWhereIn('handover_status', ['Submitted', 'Conditional', 'Approved'])
              ->orWhereNotNull('ms_handover_status')
              ->orWhere('sales_stage', 'Closed Won')
              ->orWhereIn('status', ['In Progress', 'On Progress', 'Active']);
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
            $assignedTeams = is_array($p->handover_data) ? ($p->handover_data['assigned_teams'] ?? null) : null;
            if (!empty($assignedTeams) && is_array($assignedTeams)) {
                $divisionName = implode(' & ', $assignedTeams);
            } elseif ($p->division && $p->division_id) {
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
                if (!empty($p->handover_approved_at) || in_array($stLower, ['in progress', 'on progress', 'active'])) {
                    // Jika proyek sudah berjalan aktif di lapangan, maka serah terima dianggap sudah disahkan
                    $handoverStatus = 'Approved';
                } else {
                    $handoverStatus = $handoverStatus ?: 'Draft';
                }
            }

            // Deteksi apakah proyek memiliki lingkup Managed Service / Pemeliharaan / SLA
            $rawType = strtolower($p->project_type ?? '');
            $rawName = strtolower($p->name ?? '');
            $hasMs = (
                str_contains($rawType, 'managed service') ||
                str_contains($rawType, 'pemeliharaan') ||
                str_contains($rawType, 'sla') ||
                str_contains($rawName, 'managed service') ||
                str_contains($rawName, 'pemeliharaan') ||
                str_contains($rawName, 'sla') ||
                $p->handover_target === 'both' ||
                $p->handover_target === 'managed_service'
            );

            // Proyek PPATK murni implementasi PMO, tidak masuk ke alur maintenance/MS
            if (str_contains(strtolower($p->name), 'ppat') || str_contains(strtolower($p->name), 'peremajaan router') || str_contains(strtolower($p->client ?? ''), 'ppat')) {
                $hasMs = false;
            }

            // PIC Klien pendamping diskusi ke PM
            $customerPic = $p->customer_pic_technical;
            if (empty($customerPic) || $customerPic === '-') {
                $customerPic = $p->customer_pic_business;
            }
            if (empty($customerPic) || $customerPic === '-') {
                $customerPic = is_array($p->handover_data) ? ($p->handover_data['customer_contacts']['note'] ?? null) : null;
            }
            if (empty($customerPic)) {
                $customerPic = '-';
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
                'start_date_raw'                => $p->start_date ? $p->start_date->format('Y-m-d') : '',
                'deadline'                      => $p->deadline ? $p->deadline->format('d M Y') : '-',
                'deadline_raw'                  => $p->deadline ? $p->deadline->format('Y-m-d') : '',
                'duration'                      => $p->duration_formatted ?: '-',
                'description'                   => $p->description ?: '',
                'po_spk_number'                 => $p->po_spk_number ?: ($p->po_number ?: ''),
                'status'                        => $p->status ?: 'In Progress',
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
                'handover_data'                 => (function() use ($p) {
                    $hd = is_array($p->handover_data) ? $p->handover_data : [];
                    $sSig = $hd['sales_signature'] ?? [];
                    if (!empty($sSig['user_id'])) {
                        $salesUser = \App\Models\User::find($sSig['user_id']);
                        if ($salesUser) {
                            $sSig['qr_svg'] = $salesUser->signature_qr_svg;
                        }
                    } elseif (!empty($sSig['qr_token'])) {
                        try {
                            $sSig['qr_svg'] = (string) \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                                ->size(80)->margin(0)->errorCorrection('M')
                                ->generate(route('public.signature.verify', $sSig['qr_token']));
                        } catch (\Throwable $e) {}
                    }
                    $hd['sales_signature'] = $sSig;
                    return $hd;
                })(),
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
                'project_type'                  => $p->project_type ?: 'One-Time Project',
                'has_ms'                        => $hasMs,
                'customer_pic'                  => $customerPic,
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
        $readyToOperateCount = $formattedProjects->where('has_ms', true)->where('stage', 'Deliver')->where('progress', '>=', 100)->count();

        // 5. Persebaran 4-Tahap Siklus Ver 2.0 (Projek Aktif Deliver/Operate hanya menghitung yang sudah sah serah terimanya)
        $stageCounts = [
            'Acquire' => $allProjects->where('stage', 'Acquire')->count(),
            'Design'  => $allProjects->where('stage', 'Design')->count(),
            'Deliver' => $formattedProjects->where('stage', 'Deliver')->whereNotIn('handover_status', ['Submitted'])->count(),
            'Operate' => $formattedProjects->where('stage', 'Operate')->whereNotIn('handover_status', ['Submitted'])->count(),
        ];

        // 6. Data Master Tim Teknis untuk Filter & Form (Semua Tim Teknis)
        $divisions = Division::orderBy('name')->get()->map(function($d) {
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
        
        $assignedPm = \App\Models\User::find($validated['pm_id']);
        if ($assignedPm && method_exists($assignedPm, 'ensureSignatureQrToken')) {
            $assignedPm->ensureSignatureQrToken();
        }

        $pmUser = auth()->user();
        if ($pmUser && method_exists($pmUser, 'ensureSignatureQrToken')) {
            $pmUser->ensureSignatureQrToken();
        }

        $chosenSigType = $request->input('signature_mode', $request->input('signature_type', ($assignedPm?->signature_type ?: ($pmUser?->signature_type ?: 'signature_qr'))));
        $existingHandoverData = is_array($project->handover_data) ? $project->handover_data : [];
        
        // Gunakan nama dan identitas PM yang dipilih/ditugaskan pada proyek
        $existingHandoverData['pm_signature'] = [
            'signed'     => true,
            'user_id'    => $assignedPm ? $assignedPm->id : $pmUser?->id,
            'user_name'  => $assignedPm ? $assignedPm->name : ($pmUser?->name ?: 'Project Manager'),
            'user_role'  => 'Project Manager',
            'signed_at'  => now()->format('Y-m-d H:i:s'),
            'qr_token'   => $assignedPm?->signature_qr_token ?: $pmUser?->signature_qr_token,
            'signature'  => $assignedPm?->signature_data ?: $pmUser?->signature_data,
            'type'       => $chosenSigType,
        ];

        $updateData = [
            'handover_status'      => 'Approved',
            'handover_approved_at' => now(),
            'handover_approved_by' => auth()->id(),
            'stage'                => 'Deliver',
            'status'               => 'On Progress',
            'process_status'       => 'In Progress',
            'pm_id'                => $validated['pm_id'],
            'division_id'          => $validated['division_id'],
            'handover_data'        => $existingHandoverData,
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
            'sla_tier'              => 'nullable|string|in:Platinum,Gold,Silver,Bronze',
            'maintenance_frequency' => 'nullable|string|max:100',
            'sla_coverage_hours'    => 'nullable|string|max:100',
            'service_start_date'    => 'nullable|date',
            'service_end_date'      => 'nullable|date',
            'special_notes'         => 'nullable|string',
            'signature_type'        => 'nullable|string|max:50',
        ]);

        $slaTier   = $validated['sla_tier'] ?? ($project->sla_tier ?: 'Gold');
        $maintFreq = $validated['maintenance_frequency'] ?? ($project->maintenance_frequency ?: 'Bulanan (Monthly)');
        $coverage  = $validated['sla_coverage_hours'] ?? ($project->sla_coverage_hours ?: '24x7 Non-Stop');
        $startDate = $validated['service_start_date'] ?? ($project->service_start_date ?: now()->toDateString());
        $endDate   = $validated['service_end_date'] ?? $project->service_end_date;
        $notes     = $validated['special_notes'] ?? ($project->special_notes ?: 'Pekerjaan implementasi telah selesai dan diserahkan ke Managed Service.');

        $existingHandoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $existingHandoverData['ms_handover'] = [
            'checklist_status'         => $request->input('checklist_status', []),
            'checklist_na'             => $request->input('checklist_na', []),
            'checklist_notes'          => $request->input('checklist_notes', []),
            'handover_status_selected' => $request->input('handover_status_selected', 'approved'),
            'service_acceptance'       => $request->input('service_acceptance', 'Layanan implementasi telah rampung dan siap dialihkan ke tim pemeliharaan berkala.'),
            'risks_and_pic'            => $request->input('risks_and_pic', ''),
            'follow_up_date'           => $request->input('follow_up_date', ''),
            'confirmation_notes'       => $request->input('confirmation_notes', 'Penyerahan tanggung jawab operasional dari Project Manager kepada Managed Service.'),
            'special_notes'            => $notes,
            'submitted_at'             => now()->toDateTimeString(),
            'submitted_by'             => auth()->id(),
            'pm_signature'             => [
                'user_id'              => auth()->id(),
                'user_name'            => auth()->user() ? auth()->user()->name : 'Project Manager',
                'user_role'            => 'Project Manager',
                'signed_at'            => now()->toDateTimeString(),
                'signature_type'       => $request->input('signature_type', 'signature_qr'),
                'qr_token'             => auth()->user() ? auth()->user()->signature_qr_token : null,
                'signature'            => auth()->user() ? auth()->user()->signature_data : null,
            ],
        ];

        $project->update([
            'stage'                 => 'Operate',
            'status'                => 'Active',
            'handover_target'       => 'both',
            'sla_tier'              => $slaTier,
            'maintenance_frequency' => $maintFreq,
            'sla_coverage_hours'    => $coverage,
            'service_start_date'    => $startDate,
            'service_end_date'      => $endDate,
            'ms_handover_status'    => 'Submitted',
            'handover_data'         => $existingHandoverData,
            'special_notes'         => $notes,
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

        // Otomatis sinkronkan personil yang ditugaskan Lead ke siklus Preventive Maintenance & Schedules
        $primaryEngineerId = !empty($validated['engineer_ids']) ? (int) $validated['engineer_ids'][0] : null;
        if ($primaryEngineerId) {
            // Update personil di seluruh siklus PM yang belum selesai
            if (\Illuminate\Support\Facades\Schema::hasTable('managed_service_pm_cycles')) {
                \App\Models\ManagedServicePmCycle::where('project_id', $project->id)
                    ->where('activity_status', '!=', 'Completed')
                    ->whereNull('assigned_engineer_id')
                    ->update(['assigned_engineer_id' => $primaryEngineerId]);
            }

            // Update personil di jadwal kunjungan Preventive Maintenance pada tabel schedules
            if (\Illuminate\Support\Facades\Schema::hasTable('schedules')) {
                $pmSchedules = \App\Models\Schedule::where('project_id', $project->id)
                    ->where('category', 'Preventive Maintenance')
                    ->whereNull('engineer_id')
                    ->get();

                foreach ($pmSchedules as $sched) {
                    $sched->update(['engineer_id' => $primaryEngineerId]);
                    if (\Illuminate\Support\Facades\Schema::hasTable('schedule_user')) {
                        $sched->engineers()->sync([$primaryEngineerId]);
                    }
                }
            }
        }

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
    /**
     * PM Membuat Task Teknis WBS Baru (Bukan dari Sales, PM buat & kelola sendiri)
     */
    public function storeTask(Request $request, Project $project)
    {
        $rawTitle = trim((string) $request->input('title', ''));
        if ($rawTitle === '__custom__' || empty($rawTitle)) {
            $rawTitle = trim((string) $request->input('title_custom', ''));
        }
        if (!empty($rawTitle)) {
            $request->merge(['title' => $rawTitle]);
        }

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'duration_days' => 'nullable|integer|min:1|max:365',
            'starting_day'  => 'nullable|integer|min:0|max:365',
            'finish_day'    => 'nullable|integer|min:0|max:365',
            'planned_start' => 'nullable|integer|min:0|max:365',
            'start_date'    => 'nullable|date',
            'deadline'      => 'nullable|date',
            'bobot'         => 'nullable|numeric|min:0|max:100',
            'progress'      => 'nullable|numeric|min:0|max:100',
            'status'        => 'nullable|string|max:50',
            'priority'      => 'nullable|in:Low,Medium,High',
            'engineer_id'   => 'nullable|exists:users,id',
            'description'   => 'nullable|string|max:2000',
        ]);

        $duration = (int) ($validated['duration_days'] ?? 1);
        $plannedStart = $request->filled('planned_start') ? (int) $request->input('planned_start') : ($request->filled('starting_day') ? (int) $request->input('starting_day') : 0);
        $startingDay = $request->filled('starting_day') ? (int) $request->input('starting_day') : $plannedStart;
        $finishDay = $request->filled('finish_day') ? (int) $request->input('finish_day') : ($startingDay + $duration);
        $duration = max(1, $finishDay - $startingDay);
        $week = max(1, (int) ceil(($finishDay > 0 ? $finishDay : ($startingDay + 1)) / 7));

        $projStart = $project->start_date ? Carbon::parse($project->start_date)->startOfDay() : now()->startOfDay();
        $startDate = !empty($validated['start_date']) ? Carbon::parse($validated['start_date']) : $projStart->copy()->addDays($startingDay);
        if (!empty($validated['deadline'])) {
            $deadline = Carbon::parse($validated['deadline']);
        } else {
            $deadline = $startDate->copy()->addDays(max(0, $duration - 1))->endOfDay();
        }

        $progress = isset($validated['progress']) ? (int) $validated['progress'] : 0;
        $status = $validated['status'] ?? ($progress >= 100 ? 'Completed' : ($progress > 0 ? 'In Progress' : 'Not Started'));

        $bobotVal = isset($validated['bobot']) && is_numeric($validated['bobot']) ? (float) $validated['bobot'] : 0.0;
        $dailyWeight = $duration > 0 ? round($bobotVal / $duration, 2) : 0.0;

        $desc = $validated['description'] ?? '';
        $metaParts = [
            "Bobot: " . number_format($bobotVal, 1) . "%",
            "StartingDay: {$startingDay}",
            "FinishDay: {$finishDay}",
            "PlannedStart: {$plannedStart}",
            "Week: {$week}",
            "DailyWeight: {$dailyWeight}%"
        ];
        $cleanDesc = trim(preg_replace('/\[[^\]]+\]/i', '', $desc));
        $desc = ($cleanDesc ? $cleanDesc . ' ' : '') . '[' . implode(' | ', $metaParts) . ']';

        $engineerId = $validated['engineer_id'] ?? null;
        if (!$engineerId) {
            $assignedEngs = $project->handover_data['assigned_engineers'] ?? [];
            if (!empty($assignedEngs)) {
                $engineerId = $assignedEngs[0]['id'] ?? null;
            }
        }
        if (!$engineerId) {
            $engineerId = auth()->id();
        }

        $task = Task::create([
            'title'       => $validated['title'],
            'project_id'  => $project->id,
            'engineer_id' => $engineerId,
            'priority'    => $validated['priority'] ?? 'Medium',
            'status'      => $status,
            'start_date'  => $startDate->format('Y-m-d'),
            'deadline'    => $deadline->format('Y-m-d 18:00:00'),
            'description' => $desc ?: null,
            'progress'    => $progress,
            'created_by'  => auth()->id() ?: ($defaultEngineerId ?: 1),
        ]);

        if (\Illuminate\Support\Facades\Schema::hasTable('task_user') && $engineerId) {
            $task->engineers()->sync([$engineerId]);
        }

        $this->syncProjectProgressFromTasks($project);

        return back()->with('success', "Task WBS '{$task->title}' berhasil ditambahkan ke proyek oleh PM!");
    }

    /**
     * PM Mengubah Task Teknis WBS (Edit Task)
     */
    public function updateTask(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'duration_days' => 'nullable|integer|min:1|max:365',
            'starting_day'  => 'nullable|integer|min:0|max:365',
            'finish_day'    => 'nullable|integer|min:0|max:365',
            'planned_start' => 'nullable|integer|min:0|max:365',
            'start_date'    => 'nullable|date',
            'deadline'      => 'nullable|date',
            'bobot'         => 'nullable|numeric|min:0|max:100',
            'progress'      => 'nullable|numeric|min:0|max:100',
            'status'        => 'nullable|string|max:50',
            'priority'      => 'nullable|in:Low,Medium,High',
            'engineer_id'   => 'nullable|exists:users,id',
            'description'   => 'nullable|string|max:2000',
        ]);

        $oldDesc = $task->description ?? '';
        $defaultStartDay = preg_match('/StartingDay:\s*(\d+)/i', $oldDesc, $sdm) ? (int)$sdm[1] : 0;
        $defaultPlannedStart = preg_match('/PlannedStart:\s*(\d+)/i', $oldDesc, $psm) ? (int)$psm[1] : $defaultStartDay;
        $plannedStart = $request->filled('planned_start') ? (int) $request->input('planned_start') : ($request->filled('starting_day') ? (int) $request->input('starting_day') : $defaultPlannedStart);
        $startingDay = $request->filled('starting_day') ? (int) $request->input('starting_day') : $plannedStart;
        
        $duration = (int) ($validated['duration_days'] ?? 1);
        $finishDay = $request->filled('finish_day') ? (int) $request->input('finish_day') : ($startingDay + $duration);
        $duration = max(1, $finishDay - $startingDay);
        $week = max(1, (int) ceil(($finishDay > 0 ? $finishDay : ($startingDay + 1)) / 7));

        $startDate = !empty($validated['start_date']) ? Carbon::parse($validated['start_date']) : ($task->start_date ? Carbon::parse($task->start_date) : now());
        if (!empty($validated['deadline'])) {
            $deadline = Carbon::parse($validated['deadline']);
        } else {
            $deadline = $startDate->copy()->addDays(max(0, $duration - 1))->endOfDay();
        }

        $progress = isset($validated['progress']) ? (int) $validated['progress'] : (int) $task->progress;
        $status = $validated['status'] ?? ($progress >= 100 ? 'Completed' : ($progress > 0 ? 'In Progress' : 'Not Started'));

        $bobotVal = (isset($validated['bobot']) && is_numeric($validated['bobot'])) ? (float) $validated['bobot'] : (preg_match('/Bobot:\s*([\d\.]+)%/i', $oldDesc, $bm) ? (float)$bm[1] : 0.0);
        $dailyWeight = $duration > 0 ? round($bobotVal / $duration, 2) : 0.0;

        $desc = $validated['description'] ?? ($task->description ?? '');
        $metaParts = [
            "Bobot: " . number_format($bobotVal, 1) . "%",
            "StartingDay: {$startingDay}",
            "FinishDay: {$finishDay}",
            "PlannedStart: {$plannedStart}",
            "Week: {$week}",
            "DailyWeight: {$dailyWeight}%"
        ];
        $cleanText = trim(preg_replace('/\[[^\]]+\]/i', '', $desc));
        $desc = ($cleanText ? $cleanText . ' ' : '') . '[' . implode(' | ', $metaParts) . ']';

        $updateData = [
            'title'       => $validated['title'],
            'priority'    => $validated['priority'] ?? ($task->priority ?: 'Medium'),
            'status'      => $status,
            'start_date'  => $startDate->format('Y-m-d'),
            'deadline'    => $deadline->format('Y-m-d 18:00:00'),
            'description' => $desc ?: null,
            'progress'    => $progress,
        ];

        if (!empty($validated['engineer_id'])) {
            $updateData['engineer_id'] = $validated['engineer_id'];
            if (\Illuminate\Support\Facades\Schema::hasTable('task_user')) {
                $task->engineers()->sync([$validated['engineer_id']]);
            }
        }

        $task->update($updateData);

        if ($task->project) {
            $this->syncProjectProgressFromTasks($task->project);
        }

        return back()->with('success', "Task WBS '{$task->title}' berhasil diperbarui oleh PM!");
    }

    /**
     * PM Menghapus Task Teknis WBS
     */
    public function destroyTask(Request $request, Task $task)
    {
        $project = $task->project;
        $title = $task->title;
        if (\Illuminate\Support\Facades\Schema::hasTable('task_user')) {
            $task->engineers()->detach();
        }
        $task->forceDelete();

        if ($project) {
            $this->syncProjectProgressFromTasks($project);
        }

        return back()->with('success', "Task WBS '{$title}' berhasil dihapus dari proyek.");
    }

    private function syncProjectProgressFromTasks(Project $project)
    {
        $tasks = $project->tasks()->get();
        if ($tasks->isEmpty()) {
            return;
        }

        $totalCumulative = 0;
        $totalBobotSum = 0;
        $tasksData = [];

        foreach ($tasks as $t) {
            $bobot = null;
            if (preg_match('/Bobot:\s*([\d\.]+)%/i', $t->description ?? '', $m)) {
                $bobot = (float) $m[1];
            }
            $dur = (int) ($t->duration ?? 1);
            $prog = (float) ($t->progress ?? 0);
            $tasksData[] = [
                'task' => $t,
                'bobot' => $bobot,
                'duration' => $dur,
                'progress' => $prog,
            ];
            if ($bobot !== null) {
                $totalBobotSum += $bobot;
            }
        }

        if (abs($totalBobotSum - 100.0) <= 1.0) {
            foreach ($tasksData as $td) {
                $totalCumulative += (($td['bobot'] ?? 0) * ($td['progress'] / 100.0));
            }
        } else {
            $totalDur = array_sum(array_column($tasksData, 'duration'));
            if ($totalDur > 0) {
                foreach ($tasksData as $td) {
                    $weight = ($td['duration'] / $totalDur) * 100.0;
                    $totalCumulative += ($weight * ($td['progress'] / 100.0));
                }
            } else {
                $avgProg = array_sum(array_column($tasksData, 'progress')) / count($tasksData);
                $totalCumulative = $avgProg;
            }
        }

        $project->progress = min(100.0, max(0.0, round($totalCumulative, 2)));
        $project->saveQuietly();
    }

    public function quickStoreTask(Request $request, Project $project)
    {
        $rawTitle = trim((string) $request->input('title', ''));
        if ($rawTitle === '__custom__' || empty($rawTitle)) {
            $rawTitle = trim((string) $request->input('title_custom', ''));
        }
        if (!empty($rawTitle)) {
            $request->merge(['title' => $rawTitle]);
        }

        // Mendukung penugasan ke satu atau beberapa engineer pelaksana
        $engineerIds = $request->input('engineer_ids', []);
        if (empty($engineerIds) && $request->filled('engineer_id')) {
            $engineerIds = [$request->input('engineer_id')];
        }
        $engineerIds = array_values(array_filter(array_unique(array_map('intval', (array) $engineerIds))));

        if (empty($engineerIds)) {
            return back()->with('error', 'Silakan pilih minimal satu engineer pelaksana.')->withInput();
        }

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'priority'     => 'required|in:Low,Medium,High',
            'start_date'   => 'nullable|date',
            'deadline'     => 'nullable|date',
            'description'  => 'nullable|string|max:2000',
        ]);

        $primaryEngineerId = $engineerIds[0];

        $startDate = $validated['start_date'] ?? now()->toDateString();
        $deadlineStr = !empty($validated['deadline']) 
            ? substr($validated['deadline'], 0, 10) . ' 18:00:00' 
            : ($project->deadline ? $project->deadline->format('Y-m-d') . ' 18:00:00' : now()->addWeeks(2)->format('Y-m-d') . ' 18:00:00');

        $task = Task::create([
            'title'       => $validated['title'],
            'project_id'  => $project->id,
            'engineer_id' => $primaryEngineerId,
            'priority'    => $validated['priority'],
            'status'      => 'Assigned',
            'start_date'  => $startDate,
            'deadline'    => $deadlineStr,
            'description' => $validated['description'] ?? null,
            'progress'    => 0,
            'attachments' => 0,
            'created_by'  => auth()->id() ?: ($defaultEngineerId ?: 1),
        ]);

        if (\Illuminate\Support\Facades\Schema::hasTable('task_user')) {
            $task->engineers()->sync($engineerIds);
        }

        // Notifikasi ke seluruh engineer penerima tugas dari PM
        foreach ($engineerIds as $engId) {
            try {
                \App\Models\Notification::create([
                    'user_id' => $engId,
                    'title'   => 'Tugas Teknis Baru (PM): ' . $task->title,
                    'message' => "Project Manager telah menugaskan tugas '{$task->title}' pada proyek '{$project->name}'.",
                    'type'    => 'task_assigned',
                    'url'     => route('tasks.index'),
                    'is_read' => false,
                ]);
            } catch (\Throwable $e) {}
        }

        $assignedCount = count($engineerIds);
        $firstEng = User::find($primaryEngineerId);
        $engText = $assignedCount > 1 
            ? ($firstEng ? $firstEng->name : 'Engineer') . " (+" . ($assignedCount - 1) . " engineer lainnya)"
            : ($firstEng ? $firstEng->name : 'engineer');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Task "' . $task->title . '" berhasil dibuat dan didelegasikan ke ' . $engText . '!',
                'task'    => $task,
            ]);
        }

        return back()->with('success', 'Task "' . $task->title . '" berhasil dibuat dan didelegasikan ke ' . $engText . '!');
    }

    /**
     * Unduh Template Excel Resmi untuk Import Task WBS Proyek
     */
    public function downloadTaskTemplateExcel(Project $project)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template WBS');

        // Headers (A-F, tanpa kolom Tgl Mulai dan Tgl Selesai)
        $headers = [
            'A1' => 'No',
            'B1' => 'Nama Tugas / Deliverable (WBS)',
            'C1' => 'Deskripsi Scope',
            'D1' => 'Durasi (Hari)',
            'E1' => 'Bobot (%)',
            'F1' => 'Progress (%)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Header Styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
                'name' => 'Calibri',
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '8F0A0D'], // Brand Red
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Sample Data Rows (Realistic networking project tasks)
        $sampleRows = [
            [1, 'Staging & Inventarisasi Perangkat', 'Pengecekan fisik perangkat dan staging konfigurasi dasar', 5, 15.00, 0],
            [2, 'Instalasi Rak & Penarikan Kabel (Cabling)', 'Penarikan kabel UTP Cat6 dan patching rack IDF', 7, 25.00, 0],
            [3, 'Konfigurasi VLAN & Routing Protokol', 'Setup VLAN Trunking, OSPF/BGP, dan access list keamanan', 5, 20.00, 0],
            [4, 'Pengujian Sistem & UAT Bersama Klien', 'User Acceptance Testing fungsionalitas failover & speed test', 4, 20.00, 0],
            [5, 'Dokumentasi As-Built & BAST 1', 'Penyusunan laporan akhir, topologi as-built, dan serah terima', 5, 20.00, 0],
        ];

        $r = 2;
        foreach ($sampleRows as $row) {
            $sheet->setCellValue('A' . $r, $row[0]);
            $sheet->setCellValue('B' . $r, $row[1]);
            $sheet->setCellValue('C' . $r, $row[2]);
            $sheet->setCellValue('D' . $r, $row[3]);
            $sheet->setCellValue('E' . $r, $row[4]);
            $sheet->setCellValue('F' . $r, $row[5]);

            $sheet->getStyle("A{$r}:F{$r}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');
            $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$r}:F{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getRowDimension($r)->setRowHeight(22);
            $r++;
        }

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(50);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);

        $safeName = Str::slug($project->name ?: 'Proyek');
        $filename = 'Template_WBS_' . $safeName . '.xlsx';

        $response = new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    /**
     * Import File Excel Spreadsheet Menjadi Task WBS Proyek
     */
    public function importTasksExcel(Request $request, Project $project)
    {
        $request->validate([
            'excel_file'           => 'required|file|max:10240', // Maks 10MB
            'mode'                 => 'nullable|in:append,replace',
            'execution_start_date' => 'nullable|date',
            'execution_end_date'   => 'nullable|date',
        ]);

        $file = $request->file('excel_file');
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
            return back()->with('error', 'Format file tidak didukung. Harap unggah file .xlsx, .xls, atau .csv');
        }

        // Validasi Periode Pelaksanaan Pekerjaan vs Kontrak
        $contractDeadline = $project->deadline ? Carbon::parse($project->deadline)->endOfDay() : null;
        $contractStart = $project->start_date ? Carbon::parse($project->start_date)->startOfDay() : null;

        $executionStartDate = $request->input('execution_start_date');
        $executionEndDate = $request->input('execution_end_date');

        $execStart = $executionStartDate ? Carbon::parse($executionStartDate)->startOfDay() : ($contractStart ?: now()->startOfDay());
        $execEnd = $executionEndDate ? Carbon::parse($executionEndDate)->endOfDay() : ($contractDeadline ?: $execStart->copy()->addMonths(1)->endOfDay());

        // Aturan: Tanggal selesai pelaksanaan TIDAK BOLEH melebihi batas akhir kontrak proyek
        if ($contractDeadline && $execEnd->gt($contractDeadline)) {
            return back()->with('error', 'Target Selesai Pelaksanaan (' . $execEnd->format('d M Y') . ') tidak boleh melebihi tanggal batas akhir kontrak proyek (' . $contractDeadline->format('d M Y') . ')!');
        }

        if ($execEnd->lt($execStart)) {
            return back()->with('error', 'Target Selesai Pelaksanaan tidak boleh lebih awal dari Tanggal Mulai Pelaksanaan.');
        }

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            if (empty($rows) || count($rows) < 2) {
                return back()->with('error', 'File Excel kosong atau tidak memiliki data baris.');
            }

            // Cari baris header (biasanya baris 1)
            $headerRowIdx = 1;
            foreach ($rows as $idx => $rData) {
                $joined = strtolower(implode(' ', array_filter($rData)));
                if (str_contains($joined, 'nama') || str_contains($joined, 'wbs') || str_contains($joined, 'tugas') || str_contains($joined, 'task')) {
                    $headerRowIdx = $idx;
                    break;
                }
            }

            // Mode replace: Hapus task lama jika PM memilih opsi reset / timpa
            if ($request->input('mode') === 'replace') {
                $project->tasks()->delete();
            }

            $importedCount = 0;
            $defaultEngineerId = null;
            $assignedEngs = $project->handover_data['assigned_engineers'] ?? [];
            if (!empty($assignedEngs)) {
                $defaultEngineerId = $assignedEngs[0]['id'] ?? null;
            }
            if (!$defaultEngineerId) {
                $defaultEngineerId = auth()->id();
            }

            $currentTaskStart = $execStart->copy();

            // Iterasi baris data (setelah header)
            foreach ($rows as $idx => $row) {
                if ($idx <= $headerRowIdx) {
                    continue;
                }

                // Kolom B atau Kolom A: Nama Tugas / WBS
                $title = trim((string) ($row['B'] ?? ''));
                if (empty($title) && !empty($row['A']) && !is_numeric($row['A'])) {
                    $title = trim((string) $row['A']);
                }

                if (empty($title)) {
                    continue; // Lewati baris kosong
                }

                // Deskripsi Scope
                $rawDesc = trim((string) ($row['C'] ?? ''));

                // Durasi (Hari)
                $duration = 1;
                if (!empty($row['D']) && is_numeric($row['D'])) {
                    $duration = max(1, (int) $row['D']);
                }

                // Cek format kolom: jika file lama 8 kolom (ada tanggal di E/F atau bobot di G/H)
                $isLegacyFormat = (!empty($row['G']) || !empty($row['H'])) || (
                    isset($row['E']) && (str_contains((string)$row['E'], '-') || (is_numeric($row['E']) && (float)$row['E'] > 30000))
                );

                if ($isLegacyFormat) {
                    $rawBobot = $row['G'] ?? null;
                    $rawProgress = $row['H'] ?? null;
                } else {
                    $rawBobot = $row['E'] ?? null;
                    $rawProgress = $row['F'] ?? null;
                }

                // Jadwal otomatis task dalam periode pelaksanaan
                $taskStart = $currentTaskStart->copy();
                $taskEnd = $taskStart->copy()->addDays(max(0, $duration - 1))->setTime(18, 0, 0);

                if ($taskEnd->gt($execEnd)) {
                    $taskEnd = $execEnd->copy();
                }
                if ($contractDeadline && $taskEnd->gt($contractDeadline)) {
                    $taskEnd = $contractDeadline->copy();
                }

                // Geser tanggal task berikutnya
                $currentTaskStart = $taskEnd->copy()->startOfDay()->addDay();
                if ($currentTaskStart->gt($execEnd)) {
                    $currentTaskStart = $execEnd->copy()->startOfDay();
                }

                // Bobot (%)
                $bobotVal = null;
                if (!empty($rawBobot)) {
                    $cleanBobot = str_replace(['%', ' '], '', (string) $rawBobot);
                    $cleanBobot = str_replace(',', '.', $cleanBobot);
                    if (is_numeric($cleanBobot)) {
                        $bobotVal = (float) $cleanBobot;
                    }
                }

                // Sisipkan tag [Bobot: XX%] ke deskripsi
                if ($bobotVal !== null) {
                    $formattedBobot = number_format($bobotVal, 2, '.', '');
                    $descWithBobot = trim("[Bobot: {$formattedBobot}%] " . $rawDesc);
                } else {
                    $descWithBobot = $rawDesc ?: null;
                }

                // Progress (%)
                $progress = 0;
                if (!empty($rawProgress)) {
                    $cleanProg = str_replace(['%', ' '], '', (string) $rawProgress);
                    $cleanProg = str_replace(',', '.', $cleanProg);
                    if (is_numeric($cleanProg)) {
                        $progress = max(0, min(100, (int) round((float) $cleanProg)));
                    }
                }

                // Status
                $status = 'Not Started';
                if ($progress >= 100) {
                    $status = 'Completed';
                } elseif ($progress > 0) {
                    $status = 'In Progress';
                }

                // Buat Task WBS di database
                Task::create([
                    'title'       => $title,
                    'project_id'  => $project->id,
                    'engineer_id' => $defaultEngineerId,
                    'priority'    => 'Medium',
                    'status'      => $status,
                    'start_date'  => $startDate->format('Y-m-d'),
                    'deadline'    => $deadline->format('Y-m-d 18:00:00'),
                    'description' => $descWithBobot,
                    'progress'    => $progress,
                    'attachments' => 0,
                    'created_by'  => auth()->id() ?: ($defaultEngineerId ?: 1),
                ]);

                $importedCount++;
            }

            if ($importedCount === 0) {
                return back()->with('error', 'Tidak ada data task yang valid berhasil diimpor. Pastikan kolom Nama Tugas terisi.');
            }

            $project->syncStatusWithTasks();

            return back()->with('success', "Berhasil mengimpor {$importedCount} task WBS dari file Excel ke proyek!");

        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal memproses file Excel: ' . $e->getMessage());
        }
    }

    /**
     * Buat Proyek Baru Secara Manual oleh PMO (Direct Delivery / Canvas Kosong)
     */
    public function storeManualProject(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'client'            => 'required|string|max:255',
            'customer_pic'      => 'nullable|string|max:255',
            'division_id'       => 'nullable|string',
            'custom_team_name'  => 'nullable|string|max:100',
            'teams'             => 'nullable|array',
            'pm_id'             => 'required|exists:users,id',
            'contract_value'    => 'nullable|string',
            'po_spk_number'     => 'nullable|string|max:100',
            'project_type'      => 'nullable|string|max:100',
            'start_date'        => 'nullable|date',
            'deadline'          => 'nullable|date',
            'description'       => 'nullable|string|max:2000',
        ]);

        // 1. Tentukan Tim Teknis Pelaksana (Dapat memilih lebih dari satu atau ketik manual)
        $teams = (array) $request->input('teams', []);
        if ($request->filled('custom_team_name')) {
            $teams[] = $request->input('custom_team_name');
        } elseif (empty($teams) && $request->filled('division_id') && $request->input('division_id') !== '__custom__') {
            $divObj = Division::find($request->input('division_id'));
            if ($divObj) {
                $teams[] = preg_replace('/^Divisi\s+/i', 'Tim ', $divObj->name);
            }
        }

        $cleanTeams = [];
        foreach ($teams as $t) {
            $t = trim((string) $t);
            if (!empty($t)) {
                $formatted = preg_match('/^tim\s+/i', $t) ? $t : ('Tim ' . $t);
                if (!in_array($formatted, $cleanTeams)) {
                    $cleanTeams[] = $formatted;
                }
            }
        }

        if (empty($cleanTeams)) {
            $cleanTeams = ['Tim Network'];
        }

        $hasNetwork = false;
        $hasSecurity = false;
        $customTeams = [];

        foreach ($cleanTeams as $teamName) {
            $lower = strtolower($teamName);
            if (str_contains($lower, 'net') && !str_contains($lower, 'sec')) {
                $hasNetwork = true;
            } elseif (str_contains($lower, 'sec') && !str_contains($lower, 'net')) {
                $hasSecurity = true;
            } elseif (str_contains($lower, 'net') && str_contains($lower, 'sec')) {
                $hasNetwork = true;
                $hasSecurity = true;
            } else {
                $customTeams[] = $teamName;
            }
        }

        $divisionId = null;
        if ($hasNetwork && $hasSecurity) {
            $netSecDiv = Division::where('name', 'like', '%Network & Security%')->orWhere('code', 'NET-SEC')->first();
            $divisionId = $netSecDiv ? $netSecDiv->id : 4;
        } elseif ($hasNetwork) {
            $netDiv = Division::where('code', 'NET')->orWhere('name', 'like', '%Network%')->first();
            $divisionId = $netDiv ? $netDiv->id : 1;
        } elseif ($hasSecurity) {
            $secDiv = Division::where('code', 'SEC')->orWhere('name', 'like', '%Security%')->first();
            $divisionId = $secDiv ? $secDiv->id : 2;
        } elseif (!empty($customTeams)) {
            $cName = $customTeams[0];
            $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $cName), 0, 4)) ?: 'TIM';
            $cDiv = Division::firstOrCreate(
                ['name' => $cName],
                ['code' => $code]
            );
            $divisionId = $cDiv->id;
        } else {
            $firstDiv = Division::first();
            $divisionId = $firstDiv ? $firstDiv->id : 1;
        }

        // Catat custom teams ke tabel divisions jika belum ada
        foreach ($customTeams as $cName) {
            $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $cName), 0, 4)) ?: 'TIM';
            Division::firstOrCreate(
                ['name' => $cName],
                ['code' => $code]
            );
        }

        // 2. Format Nilai Kontrak (bersihkan format Rupiah)
        $contractValue = null;
        if (!empty($validated['contract_value'])) {
            $cleanVal = preg_replace('/[^\d]/', '', (string) $validated['contract_value']);
            if (is_numeric($cleanVal)) {
                $contractValue = (float) $cleanVal;
            }
        }

        // 3. Periode Proyek
        $startDate = !empty($validated['start_date']) 
            ? Carbon::parse($validated['start_date'])->toDateString() 
            : now()->toDateString();

        $deadline = !empty($validated['deadline']) 
            ? Carbon::parse($validated['deadline'])->format('Y-m-d 18:00:00') 
            : Carbon::parse($startDate)->addMonth()->format('Y-m-d 18:00:00');

        $customerPic = !empty($validated['customer_pic']) ? trim($validated['customer_pic']) : null;

        // 4. Buat Proyek (Projek Aktif Bersih / Canvas Kosong)
        $handoverData = [
            'assigned_teams'    => $cleanTeams,
            'checklist'         => [],
            'customer_contacts' => [
                'name' => $customerPic,
                'note' => $customerPic,
            ],
        ];

        $project = Project::create([
            'name'                        => $validated['name'],
            'client'                      => $validated['client'],
            'customer_pic_technical'      => $customerPic,
            'customer_pic_business'       => $customerPic,
            'division_id'                 => $divisionId,
            'pm_id'                       => $validated['pm_id'],
            'contract_value'              => $contractValue,
            'po_spk_number'               => $validated['po_spk_number'] ?? null,
            'project_type'                => $validated['project_type'] ?: 'One-Time Project',
            'start_date'                  => $startDate,
            'deadline'                    => $deadline,
            'description'                 => $validated['description'] ?? null,
            'stage'                       => 'Deliver',
            'status'                      => 'In Progress',
            'progress'                    => 0,
            'sales_name'                  => 'Direct PMO',
            'sales_stage'                 => 'Closed Won',
            'handover_target'             => 'pmo',
            'handover_status'             => 'Approved',
            'commercial_handover_status'  => 'Approved',
            'handover_data'               => $handoverData,
            'created_by'                  => auth()->id() ?: $validated['pm_id'],
        ]);

        return redirect()->route('pmo.projects.show', $project->id)
            ->with('success', "Proyek '{$project->name}' berhasil dibuat! Canvas proyek siap diisi tugas WBS & jadwal kerja.");
    }

    /**
     * Hapus Proyek PMO secara aman beserta seluruh task WBS & data turunannya
     */
    public function destroyProject(Request $request, Project $project)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        $canDelete = $user->hasAnyRole([
            'Super Admin', 'Admin', 'PMO', 'Project Manager',
            'Director', 'Direktur', 'HD / Direktur',
            'Division Head', 'Group Leader', 'Group Leader Delivery & Operation'
        ])
        || $project->created_by == $user->id
        || ($project->pm_id && $project->pm_id == $user->id)
        || ($project->sales_name && str_contains(strtolower($project->sales_name), strtolower($user->name)))
        || \App\Helpers\ScopeHelper::isExecutive($user);

        if (!$canDelete) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki hak akses untuk menghapus proyek ini.'
                ], 403);
            }
            return back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus proyek ini.');
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $projectName = $project->name;

            // Cascade delete child relations
            $project->tasks()->delete();
            $project->schedules()->delete();

            if (\Illuminate\Support\Facades\Schema::hasTable('project_expenses')) {
                \Illuminate\Support\Facades\DB::table('project_expenses')->where('project_id', $project->id)->delete();
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('project_documents')) {
                \App\Models\ProjectDocument::where('project_id', $project->id)->delete();
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('project_milestones')) {
                \Illuminate\Support\Facades\DB::table('project_milestones')->where('project_id', $project->id)->delete();
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('project_team_members')) {
                \Illuminate\Support\Facades\DB::table('project_team_members')->where('project_id', $project->id)->delete();
            }

            $project->delete();

            \Illuminate\Support\Facades\DB::commit();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Proyek '{$projectName}' berhasil dihapus!"
                ], 200);
            }

            return redirect()->route('pmo.implementations.index')
                ->with('success', "Proyek '{$projectName}' berhasil dihapus!");

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('PMO Destroy Project Error: ' . $e->getMessage());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus proyek: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Gagal menghapus proyek: ' . $e->getMessage());
        }
    }

    /**
     * Update Data Proyek PMO
     */
    public function updateProject(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'client'         => 'required|string|max:255',
            'customer_pic'   => 'nullable|string|max:255',
            'pm_id'          => 'nullable|exists:users,id',
            'po_spk_number'  => 'nullable|string|max:100',
            'contract_value' => 'nullable',
            'project_type'   => 'nullable|string|max:100',
            'start_date'     => 'nullable|date',
            'deadline'       => 'nullable|date',
            'status'         => 'nullable|string|max:50',
            'description'    => 'nullable|string',
            'assigned_teams' => 'nullable|array',
        ]);

        // Format Nilai Kontrak
        $contractValue = $project->contract_value;
        if (isset($validated['contract_value'])) {
            $cleanVal = preg_replace('/[^\d]/', '', (string) $validated['contract_value']);
            $contractValue = is_numeric($cleanVal) ? (float) $cleanVal : null;
        }

        $customerPic = !empty($validated['customer_pic']) ? trim($validated['customer_pic']) : $project->customer_pic_technical;

        $project->name = $validated['name'];
        $project->client = $validated['client'];
        $project->customer_pic_technical = $customerPic;
        $project->customer_pic_business = $customerPic;
        if (!empty($validated['pm_id'])) {
            $project->pm_id = $validated['pm_id'];
        }
        if (isset($validated['po_spk_number'])) {
            $project->po_spk_number = $validated['po_spk_number'];
        }
        $project->contract_value = $contractValue;
        if (!empty($validated['project_type'])) {
            $project->project_type = $validated['project_type'];
        }
        if (!empty($validated['start_date'])) {
            $project->start_date = Carbon::parse($validated['start_date'])->toDateString();
        }
        if (!empty($validated['deadline'])) {
            $project->deadline = Carbon::parse($validated['deadline'])->format('Y-m-d 18:00:00');
        }
        if (!empty($validated['status'])) {
            $project->status = $validated['status'];
        }
        if (isset($validated['description'])) {
            $project->description = $validated['description'];
        }

        if (!empty($validated['assigned_teams'])) {
            $cleanTeams = array_values(array_filter($validated['assigned_teams']));
            $hd = is_array($project->handover_data) ? $project->handover_data : [];
            $hd['assigned_teams'] = $cleanTeams;
            $project->handover_data = $hd;
        }

        $project->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Proyek '{$project->name}' berhasil diperbarui!"
            ]);
        }

        return redirect()->route('pmo.implementations.index')
            ->with('success', "Proyek '{$project->name}' berhasil diperbarui!");
    }
}
