<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManagedServiceAsset;
use App\Models\ManagedServiceTicket;
use App\Models\ManagedServiceReport;
use App\Models\ManagedServicePmCycle;
use App\Models\ProjectDocument;
use App\Models\Project;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Client;
use App\Models\Task;
use App\Helpers\FileUploadHelper;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ManagedServiceController extends Controller
{
    private function ensureTablesExist(): void
    {
        if (!Schema::hasTable('managed_service_assets') || !Schema::hasTable('managed_service_tickets') || !Schema::hasTable('managed_service_reports') || !Schema::hasTable('managed_service_pm_cycles') || !Schema::hasColumn('projects', 'cm_quota')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                Log::warning('Auto migrate managed service tables: ' . $e->getMessage());
            }
        }
    }

    private function getClients()
    {
        if (Schema::hasTable('clients')) {
            return Client::orderBy('name')->get();
        }
        return collect([]);
    }

    /**
     * Managed Service Main Dashboard & Operate Control Tower
     */
    public function dashboard(Request $request)
    {
        $this->ensureTablesExist();
        $this->ensureSampleProjectExists();

        $selectedYear = $request->input('year', 2026);
        $selectedClient = $request->input('client');

        // Assets query
        $assets = collect([]);
        if (Schema::hasTable('managed_service_assets')) {
            $assetsQuery = ManagedServiceAsset::with('project');
            if ($selectedClient) {
                $assetsQuery->where('client_name', 'like', "%{$selectedClient}%");
            }
            $assets = $assetsQuery->latest()->get();
        }

        // Tickets query
        $tickets = collect([]);
        if (Schema::hasTable('managed_service_tickets')) {
            $ticketsQuery = ManagedServiceTicket::with(['assignedEngineer', 'asset.project', 'project']);
            if ($selectedClient) {
                $ticketsQuery->where('client_name', 'like', "%{$selectedClient}%");
            }
            $tickets = $ticketsQuery->latest()->get();
        }

        // Reports query
        $reports = collect([]);
        if (Schema::hasTable('managed_service_reports')) {
            $reports = ManagedServiceReport::latest()->take(5)->get();
        }

        // Metric calculations
        $totalAssets     = $assets->count();
        $onlineAssets    = $assets->where('status', 'Online')->count();
        $warningAssets   = $assets->whereIn('status', ['Warning', 'Maintenance'])->count();
        $offlineAssets   = $assets->where('status', 'Offline')->count();

        $openTickets     = $tickets->whereIn('status', ['Open', 'In Progress', 'Pending Vendor'])->count();
        $criticalTickets = $tickets->whereIn('status', ['Open', 'In Progress'])->filter(fn($t) => stripos($t->priority, 'Critical') !== false || stripos($t->priority, 'P1') !== false || stripos($t->priority, 'Platinum') !== false)->count();
        $resolvedTickets = $tickets->whereIn('status', ['Resolved', 'Closed'])->count();

        $totalSlaChecked = $tickets->whereNotNull('resolved_at')->count();
        $slaMetCount     = $tickets->where('sla_met', true)->whereNotNull('resolved_at')->count();
        $slaScore        = $totalSlaChecked > 0 ? round(($slaMetCount / $totalSlaChecked) * 100, 1) : 0;

        // Proyek di Tahap Operate / Managed Service (Selesai Serah Terima ke Managed Service)
        $operateProjects = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
            ->where(function($q) {
                $q->where('ms_handover_status', 'Accepted')
                  ->orWhere(function($sub) {
                      $sub->where('stage', 'Operate')
                          ->where(function($s) {
                              $s->whereNull('handover_target')
                                ->orWhere('handover_target', '!=', 'pmo');
                          });
                  });
            })
            ->where(function($q) {
                $q->whereNull('project_type')
                  ->orWhere('project_type', 'not like', '%One-Time%');
            })
            ->where(function($q) {
                $q->whereNull('handover_target')
                  ->orWhere('handover_target', '!=', 'pmo');
            })
            ->get();

        // SLA Tier counts
        $tierCounts = [
            'Platinum' => $operateProjects->where('sla_tier', 'Platinum')->count(),
            'Gold'     => $operateProjects->where('sla_tier', 'Gold')->count(),
            'Silver'   => $operateProjects->where('sla_tier', 'Silver')->count(),
            'Bronze'   => $operateProjects->where('sla_tier', 'Bronze')->count(),
        ];

        // Jadwal Preventive Maintenance Terdekat
        $upcomingPmSchedules = Schedule::with(['project', 'engineer', 'engineers'])
            ->where(function($q) {
                $q->where('title', 'like', '%Maintenance%')
                  ->orWhere('title', 'like', '%Preventive%')
                  ->orWhere('title', 'like', '%Kunjungan%')
                  ->orWhere('title', 'like', '%Managed Service%');
            })
            ->latest('date')
            ->take(3)
            ->get();

        // List Clients for filter
        $clients = $this->getClients();
        $maintenanceEngineers = User::where(function($q) {
            $q->where('division_id', 3)
              ->orWhereHas('roles', fn($r) => $r->whereIn('name', [
                  'Lead Maintenance', 'Maintenance', 'Managed Service', 'Field Support', 'Field Support (EOS)'
              ]));
        })->whereDoesntHave('roles', fn($r) => $r->whereIn('name', ['Lead Engineer', 'Team Leader', 'Director', 'Direktur', 'PMO']))
          ->get(['id', 'name']);

        // Maintenance Workload Distribution Data for Chart.js (Week & Month support)
        $startOfWeek  = now()->startOfWeek(\Carbon\Carbon::MONDAY)->startOfDay();
        $endOfWeek    = now()->endOfWeek(\Carbon\Carbon::SUNDAY)->endOfDay();
        $startOfMonth = now()->startOfMonth()->startOfDay();
        $endOfMonth   = now()->endOfMonth()->endOfDay();

        $buildMaintenanceLoad = function($ticketList) use ($maintenanceEngineers) {
            return $maintenanceEngineers->map(function($eng) use ($ticketList) {
                $engTickets = $ticketList->where('assigned_to', $eng->id);
                $active = $engTickets->whereIn('status', ['Open', 'In Progress', 'Pending Vendor'])->count();
                $completed = $engTickets->whereIn('status', ['Resolved', 'Closed'])->count();
                return [
                    'id'        => $eng->id,
                    'name'      => $eng->name,
                    'active'    => $active,
                    'completed' => $completed,
                    'position'  => $eng->name === 'Doris' ? 'Lead Maintenance' : 'Maintenance Engineer',
                ];
            })->values();
        };

        $weekTickets = $tickets->filter(function($t) use ($startOfWeek, $endOfWeek) {
            $ticketDate = $t->created_at;
            return $ticketDate && $ticketDate >= $startOfWeek && $ticketDate <= $endOfWeek;
        });
        $maintenanceLoadWeekData = $buildMaintenanceLoad($weekTickets);
        $maintenanceLoadMonthData = $buildMaintenanceLoad($tickets);

        $totalPmSchedulesCount = Schedule::where(function($q) {
            $q->where('title', 'like', '%Maintenance%')
              ->orWhere('title', 'like', '%Preventive%')
              ->orWhere('title', 'like', '%Kunjungan%')
              ->orWhere('title', 'like', '%Managed Service%');
        })->whereMonth('date', now()->month)->count();

        // Ticket Type & Distribution Data for Doughnut Chart
        $ticketTypeData = [
            ['name' => 'Incident / CM', 'count' => $tickets->whereIn('type', ['Incident', 'Corrective Maintenance', 'Corrective'])->count(), 'color' => '#8F0A0D'],
            ['name' => 'Preventive (PM)', 'count' => $tickets->whereIn('type', ['Preventive Maintenance', 'Preventive'])->count() ?: $totalPmSchedulesCount, 'color' => '#3B82F6'],
        ];

        // SLA Compliance Scores per Client (Top 5)
        $clientSlaData = $operateProjects->take(5)->map(function($p) use ($tickets) {
            $clientTickets = $tickets->filter(fn($t) => ($t->project_id && $t->project_id == $p->id) || (stripos($t->client_name, $p->client) !== false));
            $resolvedCount = $clientTickets->whereNotNull('resolved_at')->count();
            $metCount = $clientTickets->where('sla_met', true)->whereNotNull('resolved_at')->count();
            $score = $resolvedCount > 0 ? round(($metCount / $resolvedCount) * 100, 1) : 0;

            return [
                'name'     => \Illuminate\Support\Str::limit($p->client ?: $p->name, 16),
                'fullName' => $p->client ?: $p->name,
                'score'    => $score,
                'tier'     => $p->sla_tier ?: 'Gold',
            ];
        });

        // Status Kesehatan Layanan Pelanggan (Customer Health: Aman, PM, CM)
        $clientHealthList = $operateProjects->map(function($project) use ($tickets) {
            $clientName = $project->client ?: $project->name;

            // Cek tiket aktif untuk proyek/klien ini
            $clientTickets = $tickets->filter(function($t) use ($project, $clientName) {
                return ($t->project_id && $t->project_id == $project->id) ||
                       ($t->client_name && stripos($clientName, $t->client_name) !== false) ||
                       ($project->client && stripos($t->client_name, $project->client) !== false);
            });

            $openTicketsCount = $clientTickets->whereIn('status', ['Open', 'In Progress', 'Pending Vendor'])->count();
            $criticalTicketsCount = $clientTickets->whereIn('status', ['Open', 'In Progress'])->filter(fn($t) => stripos($t->priority, 'Critical') !== false || stripos($t->priority, 'P1') !== false || stripos($t->priority, 'Platinum') !== false)->count();

            // Cek agenda PM bulan berjalan untuk proyek ini
            $pmCount = Schedule::where(function($q) use ($project) {
                $q->where('project_id', $project->id)
                  ->orWhere('title', 'like', "%{$project->name}%");
            })->where(function($q) {
                $q->where('title', 'like', '%Maintenance%')
                  ->orWhere('title', 'like', '%Preventive%')
                  ->orWhere('title', 'like', '%Kunjungan%')
                  ->orWhere('title', 'like', '%Managed Service%');
            })->whereMonth('date', now()->month)->count();

            // Hitung Urgensi (Severity) Tiket Tertinggi jika ada gangguan aktif
            $highestSeverity = 'NORMAL';
            $severityBadge = 'bg-emerald-50 text-emerald-700 border-emerald-200';
            $severityDot = 'bg-emerald-500';

            if ($openTicketsCount > 0) {
                $hasCritical = $clientTickets->whereIn('status', ['Open', 'In Progress', 'Pending Vendor'])->filter(fn($t) => stripos($t->priority, 'Critical') !== false || stripos($t->priority, 'P1') !== false)->count() > 0;
                $hasHigh = $clientTickets->whereIn('status', ['Open', 'In Progress', 'Pending Vendor'])->filter(fn($t) => stripos($t->priority, 'High') !== false || stripos($t->priority, 'Major') !== false || stripos($t->priority, 'P2') !== false)->count() > 0;
                $hasMedium = $clientTickets->whereIn('status', ['Open', 'In Progress', 'Pending Vendor'])->filter(fn($t) => stripos($t->priority, 'Medium') !== false || stripos($t->priority, 'Minor') !== false || stripos($t->priority, 'P3') !== false)->count() > 0;

                if ($hasCritical) {
                    $highestSeverity = 'CRITICAL';
                    $severityBadge = 'bg-red-50 text-[#8F0A0D] border-red-200';
                    $severityDot = 'bg-[#8F0A0D]';
                } elseif ($hasHigh) {
                    $highestSeverity = 'HIGH';
                    $severityBadge = 'bg-orange-50 text-orange-700 border-orange-200';
                    $severityDot = 'bg-orange-500';
                } elseif ($hasMedium) {
                    $highestSeverity = 'MEDIUM';
                    $severityBadge = 'bg-amber-50 text-amber-700 border-amber-200';
                    $severityDot = 'bg-amber-500';
                } else {
                    $highestSeverity = 'LOW';
                    $severityBadge = 'bg-blue-50 text-blue-700 border-blue-200';
                    $severityDot = 'bg-blue-500';
                }

                $statusKey = 'cm';
                $statusLabel = 'Corrective (CM)';
                $statusDesc = $criticalTicketsCount > 0 ? "{$criticalTicketsCount} Tiket Kritis" : "{$openTicketsCount} Gangguan Aktif";
                $badgeBg = 'bg-red-50 text-[#8F0A0D] border-red-200';
                $dotColor = 'bg-[#8F0A0D]';
            } elseif ($pmCount > 0) {
                $statusKey = 'pm';
                $statusLabel = 'Preventive (PM)';
                $statusDesc = "{$pmCount} Kunjungan Terjadwal";
                $badgeBg = 'bg-blue-50 text-blue-700 border-blue-200';
                $dotColor = 'bg-blue-500';
            } else {
                $statusKey = 'aman';
                $statusLabel = 'Aman (Normal)';
                $statusDesc = 'Operasional Stabil';
                $badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                $dotColor = 'bg-emerald-500';
            }

            // Hitung downtime tiket bulan berjalan untuk proyek ini
            $projDownMins = 0;
            foreach ($clientTickets as $ct) {
                if ($ct->incident_date && $ct->resolved_at) {
                    $projDownMins += max(0, \Carbon\Carbon::parse($ct->incident_date)->diffInMinutes(\Carbon\Carbon::parse($ct->resolved_at)));
                } elseif ($ct->incident_date && in_array($ct->status, ['Open', 'In Progress'])) {
                    $projDownMins += max(0, \Carbon\Carbon::parse($ct->incident_date)->diffInMinutes(now()));
                }
            }
            $projDownHours = round($projDownMins / 60, 1);
            $projDaysInMonth = now()->daysInMonth;
            $projPeriodHours = $projDaysInMonth * 24;
            $projSlaUptime = $projPeriodHours > 0 
                ? max(0, min(100, round((($projPeriodHours - $projDownHours) / $projPeriodHours) * 100, 1))) 
                : 100.0;
            $projectTargetSla = (float) ($project->sla_target_percentage ?: 99.50);

            return [
                'id'               => $project->id,
                'name'             => $clientName,
                'projectName'      => $project->name,
                'sla_tier'         => $project->sla_tier ?: 'Gold',
                'sla_uptime'       => $projSlaUptime,
                'target_sla'       => $projectTargetSla,
                'downtime_hours'   => $projDownHours,
                'highest_severity' => $highestSeverity,
                'severity_badge'   => $severityBadge,
                'severity_dot'     => $severityDot,
                'status_key'       => $statusKey,
                'status_label'     => $statusLabel,
                'status_desc'      => $statusDesc,
                'badge_bg'         => $badgeBg,
                'dot_color'        => $dotColor,
                'open_tickets'     => $openTicketsCount,
                'pm_count'         => $pmCount,
            ];
        });

        $countCm = $clientHealthList->where('status_key', 'cm')->count();
        $countPm = $clientHealthList->where('status_key', 'pm')->count();
        $countAman = $clientHealthList->where('status_key', 'aman')->count();

        if ($operateProjects->count() === 0) {
            $countCm = $tickets->whereIn('type', ['Incident', 'Corrective Maintenance', 'Corrective'])->whereIn('status', ['Open', 'In Progress'])->count();
            $countPm = $totalPmSchedulesCount ?? 0;
            $countAman = 0;
        }

        $totalHealth = $countAman + $countPm + $countCm;

        $customerStatusData = [
            ['name' => 'Aman (Normal)', 'value' => $countAman, 'color' => '#10B981'],
            ['name' => 'Preventive (PM)', 'value' => $countPm, 'color' => '#3B82F6'],
            ['name' => 'Corrective (CM)', 'value' => $countCm, 'color' => '#8F0A0D'],
        ];

        $clientHealthChart = [
            'labels'     => ['Aman (Normal)', 'Preventive (PM)', 'Corrective (CM)'],
            'counts'     => [$countAman, $countPm, $countCm],
            'colors'     => ['#10B981', '#3B82F6', '#8F0A0D'],
            'total'      => $totalHealth,
            'count_aman' => $countAman,
            'count_pm'   => $countPm,
            'count_cm'   => $countCm,
        ];

        // 8. Incoming Service Handovers (Dari Sales Tipe 2 & Pasca-Implementasi PMO) - Hanya yang Pending
        $incomingQuery = Project::where(function($q) {
                $q->where('handover_target', 'managed_service')
                  ->orWhere(function($sub) {
                      $sub->where('handover_target', 'both')
                          ->where('project_type', 'not like', '%One-Time%');
                  });
            })
            ->whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
            ->where(function($q) {
                $q->whereNull('project_type')
                  ->orWhere('project_type', 'not like', '%One-Time%');
            })
            ->where('handover_target', '!=', 'pmo')
            ->where(function($q) {
                $q->whereNull('ms_handover_status')
                  ->orWhere('ms_handover_status', '!=', 'Accepted');
            })
            ->with(['creator', 'commercialHandoverBy', 'pm', 'msAcceptedBy']);

        if ($request->filled('search')) {
            $s = $request->search;
            $incomingQuery->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('client', 'like', "%{$s}%")
                  ->orWhere('sales_name', 'like', "%{$s}%")
                  ->orWhere('location', 'like', "%{$s}%");
            });
        }

        $incomingHandovers = $incomingQuery->latest('updated_at')->get();

        $pendingHandoversCount = $incomingHandovers->where('ms_handover_status', '!=', 'Accepted')->count();

        // =========================================================================
        // 9. PERHITUNGAN SLA AVAILABILITY / UPTIME PERANGKAT OTOMATIS
        // =========================================================================
        $daysInMonth = now()->daysInMonth;
        $totalPeriodHours = $daysInMonth * 24; // Cakupan 24x7 bulanan
        $targetSla = 99.50;

        // Ambil tiket insiden / gangguan bulan berjalan
        $currentMonthIncidents = $tickets->filter(function($t) {
            $tDate = $t->incident_date ?: $t->created_at;
            if (!$tDate) return false;
            $cDate = \Carbon\Carbon::parse($tDate);
            return $cDate->month == now()->month && $cDate->year == now()->year;
        });

        $totalDowntimeMinutes = 0;
        foreach ($currentMonthIncidents as $ticket) {
            if ($ticket->incident_date && $ticket->resolved_at) {
                $start = \Carbon\Carbon::parse($ticket->incident_date);
                $end   = \Carbon\Carbon::parse($ticket->resolved_at);
                $diff  = $start->diffInMinutes($end);
                $totalDowntimeMinutes += max(0, $diff);
            } elseif ($ticket->incident_date && in_array($ticket->status, ['Open', 'In Progress'])) {
                $start = \Carbon\Carbon::parse($ticket->incident_date);
                $diff  = $start->diffInMinutes(now());
                $totalDowntimeMinutes += max(0, $diff);
            }
        }

        $totalDowntimeHours = round($totalDowntimeMinutes / 60, 1);
        $slaUptimeScore = $totalPeriodHours > 0 
            ? max(0, min(100, round((($totalPeriodHours - $totalDowntimeHours) / $totalPeriodHours) * 100, 1))) 
            : 100.0;

        return view('managed_service.dashboard', compact(
            'assets',
            'tickets',
            'reports',
            'totalAssets',
            'onlineAssets',
            'warningAssets',
            'offlineAssets',
            'openTickets',
            'criticalTickets',
            'resolvedTickets',
            'slaScore',
            'totalSlaChecked',
            'operateProjects',
            'incomingHandovers',
            'pendingHandoversCount',
            'tierCounts',
            'clients',
            'maintenanceEngineers',
            'upcomingPmSchedules',
            'maintenanceLoadMonthData',
            'maintenanceLoadWeekData',
            'totalPmSchedulesCount',
            'ticketTypeData',
            'clientSlaData',
            'clientHealthList',
            'clientHealthChart',
            'customerStatusData',
            'selectedYear',
            'selectedClient',
            'totalPeriodHours',
            'totalDowntimeHours',
            'slaUptimeScore',
            'targetSla'
        ))->with('engineers', $maintenanceEngineers);
    }

    /**
     * Internal Assets (Aset & Peralatan Kerja Milik Perusahaan)
     */
    public function internalAssets(Request $request)
    {
        $this->ensureTablesExist();
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50])) {
            $perPage = 10;
        }

        $query = \App\Models\AdminEquipmentAsset::with('borrower')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('asset_name', 'like', "%{$s}%")
                  ->orWhere('asset_code', 'like', "%{$s}%")
                  ->orWhere('serial_number', 'like', "%{$s}%")
                  ->orWhere('brand_model', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $assets = $query->paginate($perPage)->withQueryString();
        $engineers = User::all();

        return view('managed_service.internal_assets', compact('assets', 'engineers', 'perPage'));
    }

    /**
     * External Assets (Aset & Perangkat Milik Klien)
     */
    public function externalAssets(Request $request)
    {
        return $this->assets($request);
    }

    /**
     * Asset & Configuration Items (CI) Management
     */
    public function assets(Request $request)
    {
        if ($request->input('tab') === 'internal' || $request->input('type') === 'internal') {
            return $this->internalAssets($request);
        }

        $this->ensureTablesExist();
        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50])) {
            $perPage = 10;
        }

        if (!Schema::hasTable('managed_service_assets')) {
            $assets = new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
            $clients = $this->getClients();
            $projects = Project::whereNotIn('name', ['DAY OFF', 'Day Off'])->orderBy('name')->get();
            return view('managed_service.assets', compact('assets', 'clients', 'projects', 'perPage'));
        }

        $query = ManagedServiceAsset::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('device_name', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('product_description', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('owner')) {
            $owner = $request->owner;
            if ($owner === 'internal' || $owner === 'IPNET') {
                $query->where(function($q) {
                    $q->where('owner_type', 'internal')
                      ->orWhere('client_name', 'like', '%IPNET%')
                      ->orWhere('client_name', 'like', '%IP Network%')
                      ->orWhere('client_name', 'like', '%Internal%');
                });
            } elseif ($owner === 'client') {
                $query->where(function($q) {
                    $q->where('owner_type', 'client')
                      ->where('client_name', 'not like', '%IPNET%')
                      ->where('client_name', 'not like', '%Internal%');
                });
            } else {
                $query->where('client_name', 'like', "%{$owner}%");
            }
        }

        $assets = $query->latest()->paginate($perPage)->withQueryString();
        $clients = $this->getClients();
        $projects = Project::whereNotIn('name', ['DAY OFF', 'Day Off'])->orderBy('name')->get();

        return view('managed_service.assets', compact('assets', 'clients', 'projects', 'perPage'));
    }

    public function storeAsset(Request $request)
    {
        $validated = $request->validate([
            'client_name'         => 'required|string|max:255',
            'owner_type'          => 'nullable|string|in:internal,client',
            'device_name'         => 'required|string|max:255',
            'product_description' => 'nullable|string',
            'category'            => 'required|string',
            'brand'               => 'nullable|string|max:100',
            'model'               => 'nullable|string|max:100',
            'quantity'            => 'nullable|integer|min:1',
            'serial_number'       => 'nullable|string|max:100',
            'ip_address'          => 'nullable|string|max:50',
            'location_site'       => 'nullable|string|max:255',
            'rack_position'       => 'nullable|string|max:100',
            'status'              => 'nullable|string',
            'warranty_expiry'     => 'nullable|date',
            'notes'               => 'nullable|string',
            'project_id'          => 'nullable|exists:projects,id',
        ]);

        if (empty($validated['status'])) {
            $validated['status'] = 'Online';
        }

        if (empty($validated['quantity'])) {
            $validated['quantity'] = 1;
        }

        if (empty($validated['owner_type'])) {
            $isIpnet = str_contains(strtolower($validated['client_name']), 'ipnet') || str_contains(strtolower($validated['client_name']), 'internal');
            $validated['owner_type'] = $isIpnet ? 'internal' : 'client';
        }

        $validated['created_by'] = auth()->id();
        ManagedServiceAsset::create($validated);

        return redirect()->back()->with('success', 'Data Aset berhasil didaftarkan.');
    }

    public function updateAsset(Request $request, $id)
    {
        $asset = ManagedServiceAsset::find($id);
        if (!$asset) {
            return redirect()->back()->with('info', 'Aset tidak ditemukan atau sudah dihapus.');
        }

        $validated = $request->validate([
            'client_name'         => 'required|string|max:255',
            'owner_type'          => 'nullable|string|in:internal,client',
            'device_name'         => 'required|string|max:255',
            'product_description' => 'nullable|string',
            'category'            => 'required|string',
            'brand'               => 'nullable|string|max:100',
            'model'               => 'nullable|string|max:100',
            'quantity'            => 'nullable|integer|min:1',
            'serial_number'       => 'nullable|string|max:100',
            'ip_address'          => 'nullable|string|max:50',
            'location_site'       => 'nullable|string|max:255',
            'rack_position'       => 'nullable|string|max:100',
            'status'              => 'nullable|string',
            'warranty_expiry'     => 'nullable|date',
            'notes'               => 'nullable|string',
            'project_id'          => 'nullable|exists:projects,id',
        ]);

        if (empty($validated['status'])) {
            $validated['status'] = $asset->status ?: 'Online';
        }

        if (empty($validated['quantity'])) {
            $validated['quantity'] = 1;
        }

        if (empty($validated['owner_type'])) {
            $isIpnet = str_contains(strtolower($validated['client_name']), 'ipnet') || str_contains(strtolower($validated['client_name']), 'internal');
            $validated['owner_type'] = $isIpnet ? 'internal' : 'client';
        }

        $asset->update($validated);

        return redirect()->back()->with('success', 'Data Aset berhasil diperbarui.');
    }

    public function destroyAsset($id)
    {
        $asset = ManagedServiceAsset::find($id);
        if (!$asset) {
            return redirect()->back()->with('info', 'Aset sudah tidak ada atau telah dihapus sebelumnya.');
        }

        $asset->delete();
        return redirect()->back()->with('success', 'Aset berhasil dihapus.');
    }

    /**
     * Incident & Service Request Tickets
     */
    public function tickets(Request $request)
    {
        $this->ensureTablesExist();

        if (!Schema::hasTable('managed_service_tickets')) {
            $tickets = collect([]);
            $clients = $this->getClients();
            $projects = Project::whereNotIn('name', ['DAY OFF', 'Day Off'])->orderBy('name')->get();
            $assets = collect([]);
            $engineers = User::all();
            return view('managed_service.tickets', compact('tickets', 'clients', 'projects', 'assets', 'engineers'));
        }

        $query = ManagedServiceTicket::with(['assignedEngineer', 'asset.project', 'project']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('reported_by', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            if (in_array($request->type, ['Preventive Maintenance', 'Preventive'])) {
                $query->whereIn('type', ['Preventive Maintenance', 'Preventive']);
            } elseif (in_array($request->type, ['Corrective Maintenance', 'Corrective', 'Incident'])) {
                $query->whereIn('type', ['Corrective Maintenance', 'Corrective', 'Incident']);
            } else {
                $query->where('type', $request->type);
            }
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('source') && $request->source !== 'all') {
            if ($request->source === 'External Portal') {
                $query->where('source', 'External Portal');
            } elseif ($request->source === 'Internal') {
                $query->where(function($q) {
                    $q->whereNull('source')->orWhere('source', '!=', 'External Portal');
                });
            }
        }

        $tickets = $query->latest()->paginate(15)->withQueryString();
        $assets  = ManagedServiceAsset::orderBy('device_name')->get();
        $projects = Project::whereNotIn('name', ['DAY OFF', 'Day Off'])->orderBy('name')->get();
        $engineers = User::where(function($q) {
            $q->where('division_id', 3)
              ->orWhereHas('roles', fn($r) => $r->whereIn('name', [
                  'Lead Maintenance', 'Maintenance', 'Managed Service', 'Field Support', 'Field Support (EOS)'
              ]));
        })->whereDoesntHave('roles', fn($r) => $r->whereIn('name', ['Lead Engineer', 'Team Leader', 'Director', 'Direktur', 'PMO']))
          ->orderBy('name')->get(['id', 'name']);

        return view('managed_service.tickets', compact('tickets', 'assets', 'projects', 'engineers'));
    }

    public function storeTicket(Request $request)
    {
        $validated = $request->validate([
            'client_name'   => 'required|string|max:255',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'type'          => 'nullable|string',
            'category'      => 'nullable|string|max:255',
            'priority'      => 'required|string',
            'created_at'    => 'nullable|date',
            'incident_date' => 'nullable',
            'reported_by'   => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'assigned_to'   => 'nullable|exists:users,id',
            'asset_id'      => 'nullable|exists:managed_service_assets,id',
            'project_id'    => 'nullable|exists:projects,id',
            'attachment'    => 'nullable|file|max:10240',
        ]);

        if (empty($validated['type'])) {
            $validated['type'] = 'Incident';
        }

        // Standardisasi Tingkat Urgensi (Severity)
        $rawPriority = $validated['priority'];
        $priorityLabel = match(strtolower(trim($rawPriority))) {
            'critical', 'p1', 'p1 - critical' => 'P1 - Critical',
            'high', 'p2', 'p2 - major', 'p2 - high' => 'P2 - Major',
            'medium', 'p3', 'p3 - minor', 'p3 - medium' => 'P3 - Minor',
            'low', 'p4', 'p4 - low' => 'P4 - Low',
            'platinum' => 'P1 - Critical',
            'gold'     => 'P2 - Major',
            'silver'   => 'P3 - Minor',
            'bronze'   => 'P4 - Low',
            default    => 'P3 - Minor',
        };
        $validated['priority'] = $priorityLabel;

        // Upload Lampiran jika ada
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $file->getClientOriginalExtension();
            $validated['attachment_path'] = $file->storeAs('client_tickets', $filename, 'public');
        }

        $ticketDate = !empty($validated['incident_date']) ? \Carbon\Carbon::parse($validated['incident_date']) : (!empty($validated['created_at']) ? \Carbon\Carbon::parse($validated['created_at']) : now());
        $validated['incident_date'] = $ticketDate;
        $validated['created_at'] = $ticketDate;

        // Generate Ticket Number (Format: TCK-YYYYMM-XXXX atau CM/PM)
        $yearMonth = date('Ym');
        $prefix = "TCK-{$yearMonth}-";
        $latest = ManagedServiceTicket::where('ticket_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest->ticket_number, $matches)) {
            $nextSeq = intval($matches[1]) + 1;
        }
        $validated['ticket_number'] = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'Open';
        $validated['source'] = 'Internal';

        // Set SLA Deadline
        $slaHours = match($priorityLabel) {
            'P1 - Critical' => 2,
            'P2 - Major'    => 4,
            'P3 - Minor'    => 12,
            'P4 - Low'      => 24,
            default         => 12,
        };
        $validated['sla_deadline'] = $ticketDate->copy()->addHours($slaHours);
        $validated['sla_met'] = true;

        $ticket = ManagedServiceTicket::create($validated);
        if ($ticket) {
            self::syncTicketToTaskAndSchedule($ticket);
        }

        return redirect()->back()->with('success', 'Tiket ' . $validated['ticket_number'] . ' berhasil diterbitkan.');
    }

    public function updateTicket(Request $request, $id)
    {
        $ticket = ManagedServiceTicket::find($id);
        if (!$ticket) {
            return redirect()->route('ms.tickets.index')->with('info', 'Tiket tidak ditemukan atau sudah dihapus.');
        }

        $validated = $request->validate([
            'status'           => 'required|string|in:Open,In Progress,Pending Vendor,Resolved,Closed',
            'assigned_to'      => 'nullable|exists:users,id',
            'resolution_notes' => 'nullable|string',
            'root_cause'       => 'nullable|string|max:255',
        ]);

        if (in_array($validated['status'], ['Resolved', 'Closed']) && !$ticket->resolved_at) {
            $validated['resolved_at'] = now();
            // Periksa apakah SLA terpenuhi
            if ($ticket->sla_deadline && now()->gt($ticket->sla_deadline)) {
                $validated['sla_met'] = false;
            } else {
                $validated['sla_met'] = true;
            }
        }

        $ticket->update($validated);
        self::syncTicketToTaskAndSchedule($ticket);

        return redirect()->route('ms.tickets.index')->with('success', 'Status tiket ' . $ticket->ticket_number . ' diperbarui.');
    }

    public function destroyTicket($id)
    {
        $ticket = ManagedServiceTicket::find($id);
        if (!$ticket) {
            return redirect()->route('ms.tickets.index')->with('info', 'Tiket sudah tidak ada atau telah dihapus sebelumnya.');
        }

        $num = $ticket->ticket_number;
        // Bersihkan task & schedule yang terkait
        Task::where('title', 'like', "[{$num}]%")->delete();
        Schedule::where('title', 'like', "[{$num}]%")->delete();

        $ticket->delete();
        return redirect()->route('ms.tickets.index')->with('success', 'Tiket ' . $num . ' berhasil dihapus.');
    }

    /**
     * Sinkronisasi Tiket SLA secara Real-time ke Task dan Jadwal Kerja
     */
    public static function syncTicketToTaskAndSchedule(ManagedServiceTicket $ticket)
    {
        $taskTitle = '[' . $ticket->ticket_number . '] ' . $ticket->title;
        $taskStatus = match($ticket->status) {
            'Open' => 'Assigned',
            'In Progress', 'Pending Vendor' => 'In Progress',
            'Resolved' => 'Waiting Review',
            'Closed' => 'Completed',
            default => 'Assigned',
        };

        $p = strtolower($ticket->priority ?? '');
        $taskPriority = 'Medium';
        if (str_contains($p, 'critical') || str_contains($p, 'p1') || str_contains($p, 'high') || str_contains($p, 'platinum')) {
            $taskPriority = 'High';
        } elseif (str_contains($p, 'major') || str_contains($p, 'p2') || str_contains($p, 'gold')) {
            $taskPriority = 'High';
        } elseif (str_contains($p, 'minor') || str_contains($p, 'p3') || str_contains($p, 'silver')) {
            $taskPriority = 'Medium';
        } elseif (str_contains($p, 'low') || str_contains($p, 'p4') || str_contains($p, 'bronze')) {
            $taskPriority = 'Low';
        }

        $proj = null;
        if (!empty($ticket->project_id)) {
            $proj = Project::find($ticket->project_id);
        }
        if (!$proj) {
            $clientName = $ticket->client_name ?: ($ticket->asset?->client_name ?: 'Layanan Managed Service');
            $proj = Project::firstOrCreate(
                ['name' => 'Layanan SLA & Maintenance Support'],
                [
                    'client'       => $clientName,
                    'location'     => $ticket->asset?->location_site ?? 'On-Site Klien',
                    'start_date'   => now()->toDateString(),
                    'deadline'     => $ticket->sla_deadline ? $ticket->sla_deadline->format('Y-m-d') : now()->addMonth()->toDateString(),
                    'status'       => 'In Progress',
                    'project_type' => 'Maintenance SLA',
                    'division_id'  => 3,
                    'created_by'   => $ticket->created_by ?: (auth()->id() ?: 1),
                ]
            );
        }

        $deadlineDate = $ticket->sla_deadline ? $ticket->sla_deadline->format('Y-m-d') : now()->toDateString();
        $deadlineTime = $ticket->sla_deadline ? $ticket->sla_deadline->format('H:i:00') : '10:00:00';
        $assignedUserId = $ticket->assigned_to ?: ($ticket->created_by ?: (auth()->id() ?: 1));

        $task = Task::updateOrCreate(
            [
                'title' => $taskTitle,
            ],
            [
                'project_id'    => $proj->id,
                'engineer_id'   => $assignedUserId,
                'priority'      => $taskPriority,
                'status'        => $taskStatus,
                'progress'      => $taskStatus === 'Completed' ? 100 : ($taskStatus === 'Waiting Review' ? 90 : ($taskStatus === 'In Progress' ? 50 : 0)),
                'deadline'      => $deadlineDate . ' ' . $deadlineTime,
                'deadline_time' => $deadlineTime,
                'description'   => "Nomor Tiket: {$ticket->ticket_number}\nPelapor: " . ($ticket->reported_by ?: '-') . " (" . ($ticket->contact_phone ?: '-') . ")\nPerangkat: " . ($ticket->asset ? $ticket->asset->device_name . ' (' . $ticket->asset->serial_number . ')' : '-') . "\n\nDeskripsi Masalah:\n" . ($ticket->description ?: '-'),
                'created_by'    => $ticket->created_by ?: (auth()->id() ?: 1),
            ]
        );

        if ($assignedUserId && Schema::hasTable('task_user')) {
            $task->engineers()->sync([$assignedUserId]);
        }

        // Sinkronisasi ke Schedule (Jadwal Kerja)
        $scheduleDate = $deadlineDate ?: ($ticket->created_at ? $ticket->created_at->format('Y-m-d') : now()->format('Y-m-d'));
        $scheduleStartTime = $deadlineTime ?: '09:00:00';
        $schedule = Schedule::updateOrCreate(
            [
                'title'      => $taskTitle,
            ],
            [
                'date'        => $scheduleDate,
                'start_time'  => $scheduleStartTime,
                'end_time'    => date('H:i:s', strtotime($scheduleStartTime . ' +3 hours')),
                'category'    => 'Corrective Maintenance',
                'status'      => $taskStatus === 'Completed' ? 'Completed' : ($taskStatus === 'In Progress' ? 'In Progress' : 'Pending'),
                'description' => "Penanganan Tiket Corrective Maintenance {$ticket->ticket_number} - {$ticket->client_name}\nStatus: {$ticket->status}",
                'project_id'  => $proj->id,
                'engineer_id' => $assignedUserId,
            ]
        );

        if ($assignedUserId && Schema::hasTable('schedule_user')) {
            $schedule->engineers()->sync([(int) $assignedUserId]);
        }
    }

    /**
     * Preventive Maintenance Planner
     */
    public function maintenance(Request $request)
    {
        $this->ensureTablesExist();

        $hasStage = Schema::hasColumn('projects', 'stage');
        $hasProjectType = Schema::hasColumn('projects', 'project_type');

        $projects = Project::where(function($q) use ($hasStage, $hasProjectType) {
            if ($hasStage) {
                $q->where('stage', 'Operate');
            }
            if ($hasProjectType) {
                $q->orWhere('project_type', 'like', '%Maintenance%')
                  ->orWhere('project_type', 'like', '%Managed%');
            }
            if (!$hasStage && !$hasProjectType) {
                $q->whereRaw('1=1');
            }
        })->whereNotIn('name', ['DAY OFF', 'Day Off'])->get();

        $assets = Schema::hasTable('managed_service_assets') ? ManagedServiceAsset::all() : collect([]);
        $schedules = Schedule::with(['project', 'users'])
            ->where(function($q) {
                $q->where('title', 'like', '%Maintenance%')
                  ->orWhere('title', 'like', '%Preventive%')
                  ->orWhere('title', 'like', '%Kunjungan%')
                  ->orWhere('title', 'like', '%Managed Service%');
            })
            ->latest('date')
            ->take(15)
            ->get();

        $engineers = User::where(function($q) {
            $q->where('division_id', 3)
              ->orWhereHas('roles', fn($r) => $r->whereIn('name', [
                  'Lead Maintenance', 'Maintenance', 'Managed Service', 'Field Support', 'Field Support (EOS)'
              ]));
        })->whereDoesntHave('roles', fn($r) => $r->whereIn('name', ['Lead Engineer', 'Team Leader', 'Director', 'Direktur', 'PMO']))
          ->orderBy('name')->get(['id', 'name']);

        return view('managed_service.maintenance', compact('projects', 'assets', 'schedules', 'engineers'));
    }

    /**
     * Reports & SLA Compliance
     */
    public function reports(Request $request)
    {
        $this->ensureTablesExist();

        $reports = Schema::hasTable('managed_service_reports') ? ManagedServiceReport::latest()->paginate(15) : new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        $clients = $this->getClients();
        $projects = Project::whereNotIn('name', ['DAY OFF', 'Day Off'])->get();

        return view('managed_service.reports', compact('reports', 'clients', 'projects'));
    }

    public function storeReport(Request $request)
    {
        $validated = $request->validate([
            'client_name'   => 'required|string|max:255',
            'title'         => 'required|string|max:255',
            'report_type'   => 'required|string|in:Preventive Maintenance,SLA Review,Incident Post-Mortem,Service Activation',
            'period_month'  => 'required|string|max:50',
            'period_year'   => 'required|integer',
            'sla_score'     => 'required|numeric|min:0|max:100',
            'summary'       => 'nullable|string',
            'project_id'    => 'nullable|exists:projects,id',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'Published';

        ManagedServiceReport::create($validated);

        return redirect()->route('ms.reports.index')->with('success', 'Laporan Managed Service berhasil diterbitkan.');
    }

    /**
     * Pengesahan & Penerimaan Layanan Managed Service (Accept Service Handover)
     */
    public function acceptServiceHandover(Request $request, Project $project)
    {
        $validated = $request->validate([
            'sla_tier'              => 'required|string|in:Platinum,Gold,Silver,Bronze',
            'maintenance_frequency' => 'nullable|string|max:100',
            'sla_coverage_hours'    => 'nullable|string|max:100',
            'service_start_date'    => 'nullable|date',
            'service_end_date'      => 'nullable|date',
            'special_notes'         => 'nullable|string',
            'documents_checklist'   => 'nullable|array',
        ]);
        $msUser = auth()->user();
        if ($msUser) {
            $msUser->ensureSignatureQrToken();
        }

        $chosenSigType = $request->input('signature_mode', $request->input('signature_type', $msUser?->signature_type ?: 'signature_qr'));
        $existingHandoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $existingHandoverData['ms_signature'] = [
            'signed'     => true,
            'user_id'    => $msUser?->id,
            'user_name'  => $msUser?->name,
            'user_role'  => 'PIC Managed Service',
            'signed_at'  => now()->format('Y-m-d H:i:s'),
            'qr_token'   => $msUser?->signature_qr_token,
            'signature'  => $msUser?->signature_data,
            'type'       => $chosenSigType,
        ];

        $freqMap = [
            'Monthly'   => 'per_bulan',
            'Quarterly' => 'per_3_bulan',
            'Bi-Annual' => 'per_6_bulan',
            'Annual'    => 'tahunan',
        ];
        $maintFreqInput = $validated['maintenance_frequency'] ?? ($project->maintenance_frequency ?: 'Monthly');
        $mappedPmFreq = $freqMap[$maintFreqInput] ?? ($project->pm_frequency_type ?: 'per_bulan');

        $updateData = [
            'ms_handover_status'    => 'Accepted',
            'ms_accepted_at'        => now(),
            'ms_accepted_by'        => auth()->id(),
            'sla_tier'              => $validated['sla_tier'],
            'maintenance_frequency' => $maintFreqInput,
            'pm_frequency_type'     => $mappedPmFreq,
            'sla_coverage_hours'    => $validated['sla_coverage_hours'] ?? ($project->sla_coverage_hours ?: '24x7'),
            'service_start_date'    => $validated['service_start_date'] ?? $project->service_start_date,
            'service_end_date'      => $validated['service_end_date'] ?? $project->service_end_date,
            'special_notes'         => $validated['special_notes'] ?? $project->special_notes,
            'stage'                 => 'Operate',
            'status'                => 'Active',
            'project_type'          => 'Managed Service',
            'handover_target'       => 'managed_service',
            'handover_data'         => $existingHandoverData,
        ];

        if (empty($project->cm_quota)) {
            $updateData['cm_quota'] = match($validated['sla_tier']) {
                'Platinum' => 18,
                'Gold'     => 12,
                'Silver'   => 8,
                'Bronze'   => 4,
                default    => 12,
            };
        }

        if ($request->has('documents_checklist')) {
            $updateData['documents_checklist'] = $request->input('documents_checklist');
        }

        $updateData['handover_data'] = array_merge($existingHandoverData, [
            'checklist'                => $request->input('documents_checklist', []),
            'checklist_status'         => $request->input('checklist_status', []),
            'checklist_na'             => $request->input('checklist_na', []),
            'checklist_notes'          => $request->input('checklist_notes', []),
            'handover_status_selected' => $request->input('handover_status_selected', 'approved'),
            'handover_risks'           => $request->input('handover_risks', ''),
            'handover_missing_info'    => $request->input('handover_missing_info', ''),
            'handover_pic_target'      => $request->input('handover_pic_target', ''),
            'handover_confirmation'    => $request->input('handover_confirmation', ''),
            'handover_work_limitations'=> $request->input('handover_work_limitations', ''),
            'lead_ms_name'             => $msUser?->name ?? 'Tim Managed Service',
            'pic_ms_name'              => $msUser?->name ?? 'Tim Managed Service',
            'pic_name'                 => $msUser?->name ?? 'Tim Managed Service',
            'special_notes'            => $validated['special_notes'] ?? $project->special_notes,
        ]);

        $project->update($updateData);

        // Auto inisialisasi PM cycles berdasarkan SLA & frekuensi kunjungan
        self::initPmCycles($project);

        // Kirim notifikasi konfirmasi ke Sales PIC
        if ($project->commercial_handover_by) {
            \App\Models\Notification::create([
                'user_id' => $project->commercial_handover_by,
                'title'   => 'Layanan Managed Service Resmi Diaktifkan: ' . $project->name,
                'message' => "Tim Managed Service telah menerima berkas serah terima dan mengaktifkan operasional kontrak untuk klien '{$project->client}' (SLA Tier: {$project->sla_tier}).",
                'type'    => 'service_handover',
                'url'     => route('sales.handover.index'),
                'is_read' => false,
            ]);
        }

        // Kirim notifikasi ke Lead Engineer agar proyek muncul untuk penugasan tim
        $leadUsers = \App\Models\User::whereHas('roles', function($r) {
            $r->whereIn('name', ['Lead Engineer', 'lead_engineer', 'Lead Project', 'lead']);
        })->orWhere('position', 'like', '%Lead%')->get();

        foreach ($leadUsers as $lead) {
            try {
                \App\Models\Notification::create([
                    'user_id' => $lead->id,
                    'title'   => 'Proyek Managed Service Siap Personil: ' . $project->name,
                    'message' => "Kontrak Managed Service untuk '{$project->client}' (SLA {$project->sla_tier}) resmi aktif di tahap Operate. Anda dapat mengalokasikan teknisi pelaksana.",
                    'type'    => 'project_assignment',
                    'url'     => route('dashboard.lead'),
                    'is_read' => false,
                ]);
            } catch (\Throwable $e) {}
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Layanan Managed Service untuk ' . $project->name . ' berhasil diterima dan resmi diaktifkan!',
                'project' => $project->fresh(['creator', 'commercialHandoverBy', 'msAcceptedBy']),
            ]);
        }

        return back()->with('success', 'Layanan Managed Service untuk ' . $project->name . ' berhasil diterima dan resmi diaktifkan!');
    }

    /**
     * Auto-inisialisasi siklus Preventive Maintenance jika belum ada
     */
    public static function initPmCycles(Project $project): void
    {
        if (!Schema::hasTable('managed_service_pm_cycles')) {
            return;
        }

        $existingCount = ManagedServicePmCycle::where('project_id', $project->id)->count();
        if ($existingCount > 0) {
            return;
        }

        $startDate = $project->service_start_date ? Carbon::parse($project->service_start_date) : ($project->start_date ? Carbon::parse($project->start_date) : now());
        $endDate   = $project->service_end_date ? Carbon::parse($project->service_end_date) : ($project->deadline ? Carbon::parse($project->deadline) : null);

        $rawFreq = strtolower(trim((string) ($project->pm_frequency_type ?: ($project->maintenance_frequency ?: 'per_bulan'))));
        $monthsInterval = 1;
        if (str_contains($rawFreq, 'quarter') || str_contains($rawFreq, 'triwulan') || str_contains($rawFreq, '3')) {
            $monthsInterval = 3;
        } elseif (str_contains($rawFreq, 'semi') || str_contains($rawFreq, 'bi-annual') || str_contains($rawFreq, 'semester') || str_contains($rawFreq, '6')) {
            $monthsInterval = 6;
        } elseif (str_contains($rawFreq, 'bi-month') || str_contains($rawFreq, '2_bulan') || str_contains($rawFreq, '2 bulan')) {
            $monthsInterval = 2;
        } elseif (str_contains($rawFreq, '4_bulan') || str_contains($rawFreq, '4 bulan')) {
            $monthsInterval = 4;
        } elseif (str_contains($rawFreq, 'tahun') || str_contains($rawFreq, 'annual') || str_contains($rawFreq, '12')) {
            $monthsInterval = 12;
        } else {
            $monthsInterval = 1; // Default Monthly
        }

        if ($project->pm_total_cycles && $project->pm_total_cycles > 0) {
            $totalCycles = (int) $project->pm_total_cycles;
        } elseif ($endDate && $endDate->greaterThan($startDate)) {
            $diffMonths = max(1, (int) round($startDate->floatDiffInMonths($endDate)));
            $totalCycles = max(1, (int) floor($diffMonths / $monthsInterval));
        } else {
            $totalCycles = match($monthsInterval) {
                1 => 12,
                2 => 6,
                3 => 4,
                4 => 3,
                6 => 2,
                12 => 1,
                default => 12,
            };
        }

        if (empty($project->pm_total_cycles) || $project->pm_total_cycles != $totalCycles) {
            $project->update(['pm_total_cycles' => $totalCycles]);
        }

        // Personil teknisi yang sudah didelegasikan oleh Lead Engineer (jika ada)
        $assignedEngId = null;
        if (is_array($project->handover_data)) {
            $assigned = $project->handover_data['assigned_engineers'] ?? [];
            if (!empty($assigned) && isset($assigned[0]['id'])) {
                $assignedEngId = (int) $assigned[0]['id'];
            }
        }

        for ($i = 1; $i <= $totalCycles; $i++) {
            $schedDate = $startDate->copy()->addMonths(($i - 1) * $monthsInterval + $monthsInterval);
            $isFirstCompleted = ($i === 1) && ($project->progress >= 25);
            $cycle = ManagedServicePmCycle::create([
                'project_id'           => $project->id,
                'cycle_number'         => $i,
                'cycle_label'          => 'PM ' . $i . ' (Bulan ke-' . (($i - 1) * $monthsInterval + $monthsInterval) . ')',
                'scheduled_date'       => $schedDate->toDateString(),
                'assigned_engineer_id' => $assignedEngId,
                'activity_status'      => $isFirstCompleted ? 'Completed' : 'Scheduled',
                'actual_date'          => $isFirstCompleted ? $schedDate->copy()->subDays(2)->toDateString() : null,
                'activity_notes'       => $isFirstCompleted ? 'Preventive maintenance berkala selesai dilaksanakan sesuai checklist inspeksi.' : null,
                'report_status'        => $isFirstCompleted ? 'Approved' : 'Pending',
                'billing_ba_status'    => $isFirstCompleted ? 'Signed' : 'Pending',
            ]);
        }
    }

    /**
     * Otomatis sinkronisasi jadwal PM Cycle ke tabel Schedules (Jadwal Kerja Tim)
     * agar langsung muncul di kalender / dashboard Lead Engineer & personil teknisi
     */
    public static function syncPmCycleToSchedule(ManagedServicePmCycle $cycle): ?Schedule
    {
        if (empty($cycle->scheduled_date)) {
            return null;
        }

        if (!Schema::hasTable('schedules')) {
            return null;
        }

        $project = $cycle->project;
        $title = 'PM ' . $cycle->cycle_label . ': ' . ($project ? $project->name : 'Proyek MS');
        $dateStr = Carbon::parse($cycle->scheduled_date)->toDateString();

        // Cari schedule yang sudah ada untuk siklus ini atau proyek ini
        $schedule = Schedule::where('project_id', $cycle->project_id)
            ->where(function($q) use ($cycle, $title) {
                $q->where('title', $title)
                  ->orWhere('title', 'like', "%{$cycle->cycle_label}%");
            })
            ->first();

        // Cari engineer default: dari cycle, atau dari penugasan tim oleh Lead di project
        $engineerId = $cycle->assigned_engineer_id;
        if (empty($engineerId) && $project && is_array($project->handover_data)) {
            $assigned = $project->handover_data['assigned_engineers'] ?? [];
            if (!empty($assigned) && isset($assigned[0]['id'])) {
                $engineerId = (int) $assigned[0]['id'];
                $cycle->update(['assigned_engineer_id' => $engineerId]);
            }
        }

        $scheduleData = [
            'project_id'  => $cycle->project_id,
            'title'       => $title,
            'category'    => 'Preventive Maintenance',
            'date'        => $dateStr,
            'start_time'  => '09:00:00',
            'end_time'    => '17:00:00',
            'location'    => $project?->location ?: ($project?->client ?: 'On-Site Klien'),
            'description' => "Jadwal Preventive Maintenance ({$cycle->cycle_label}) untuk proyek '{$project?->name}'. Catatan: " . ($cycle->activity_notes ?: 'Sesuai SLA Kontrak Managed Service'),
            'engineer_id' => $engineerId,
        ];

        if ($schedule) {
            // Jika teknisi di schedule sudah diisi oleh Lead Engineer tapi di cycle belum, kita sync ke cycle
            if ($schedule->engineer_id && empty($cycle->assigned_engineer_id)) {
                $cycle->update(['assigned_engineer_id' => $schedule->engineer_id]);
                $scheduleData['engineer_id'] = $schedule->engineer_id;
            }
            $schedule->update($scheduleData);
        } else {
            $scheduleData['created_by'] = auth()->id() ?: 1;
            $schedule = Schedule::create($scheduleData);
        }

        if ($schedule->engineer_id && Schema::hasTable('schedule_user')) {
            $schedule->engineers()->sync([(int) $schedule->engineer_id]);
        }

        // Otomatis buat atau update Task di tabel tasks agar masuk ke "Team Tasks" (Penugasan & Aktivitas Lapangan)
        if (Schema::hasTable('tasks')) {
            $taskTitle = 'PM ' . $cycle->cycle_label . ': ' . ($project ? $project->name : 'Maintenance Visit');
            $pmTask = Task::where('project_id', $cycle->project_id)
                ->where(function($q) use ($cycle, $taskTitle) {
                    $q->where('title', $taskTitle)
                      ->orWhere('title', 'like', "%{$cycle->cycle_label}%");
                })
                ->first();

            $taskStatus = match($cycle->activity_status) {
                'Completed' => 'Completed',
                'In Progress' => 'In Progress',
                default => 'Assigned',
            };

            $taskProgress = match($cycle->activity_status) {
                'Completed' => 100,
                'In Progress' => 50,
                default => 0,
            };

            $taskData = [
                'project_id'    => $cycle->project_id,
                'title'         => $taskTitle,
                'category'      => 'Preventive Maintenance',
                'priority'      => 'High',
                'start_date'    => $dateStr,
                'deadline'      => $dateStr . ' 17:00:00',
                'deadline_time' => '17:00:00',
                'status'        => $taskStatus,
                'progress'      => $taskProgress,
                'engineer_id'   => $engineerId,
                'description'   => "Jadwal Preventive Maintenance ({$cycle->cycle_label}) proyek '{$project?->name}'. Catatan teknis: " . ($cycle->activity_notes ?: 'Pemeriksaan rutin perangkat, backup konfigurasi, & uji performa jaringan.'),
                'created_by'    => auth()->id() ?: 1,
            ];

            if ($pmTask) {
                if ($cycle->activity_status !== 'Completed') {
                    unset($taskData['progress']);
                }
                $pmTask->update($taskData);
            } else {
                $pmTask = Task::create($taskData);
            }

            if ($engineerId && Schema::hasTable('task_user')) {
                \Illuminate\Support\Facades\DB::table('task_user')->updateOrInsert(
                    ['task_id' => $pmTask->id, 'user_id' => $engineerId],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }

        // Kirim notifikasi ke Lead Engineer jika tanggal ditentukan tapi personil belum ditugaskan
        if (empty($schedule->engineer_id)) {
            $leadUsers = User::whereHas('roles', function($r) {
                $r->whereIn('name', ['Lead Engineer', 'lead_engineer', 'Lead Project', 'lead']);
            })->orWhere('position', 'like', '%Lead%')->get();

            foreach ($leadUsers as $lead) {
                try {
                    \App\Models\Notification::create([
                        'user_id' => $lead->id,
                        'title'   => 'Jadwal Kunjungan PM Ditentukan: ' . ($project?->name ?? 'Proyek MS'),
                        'message' => "Tim Managed Service telah menentukan jadwal kunjungan {$cycle->cycle_label} pada " . Carbon::parse($dateStr)->format('d M Y') . ". Silakan tugaskan personil teknisi pelaksana.",
                        'type'    => 'schedule_assignment',
                        'url'     => route('schedules.index'),
                        'is_read' => false,
                    ]);
                } catch (\Throwable $e) {}
            }
        }

        return $schedule;
    }


    /**
     * Pastikan minimal ada 1 proyek sampel aktif dan 1 incoming handover (siapa cepat jadi PIC)
     */
    public function ensureSampleProjectExists(): void
    {
        // Dinonaktifkan: Data proyek Maintenance hanya bersumber dari serah terima resmi atau penambahan manual
        return;
    }

    /**
     * Daftar Proyek Managed Service
     */
    public function projects(Request $request)
    {
        $this->ensureTablesExist();
        $this->ensureSampleProjectExists();

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [5, 10, 25, 50])) {
            $perPage = 10;
        }

        $query = Project::with(['creator', 'pmCycles', 'managedServiceTickets', 'msAcceptedBy'])
            ->whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
            ->where(function($q) {
                $q->where('ms_handover_status', 'Accepted')
                  ->orWhere(function($sub) {
                      $sub->where('stage', 'Operate')
                          ->where('status', 'Active')
                          ->where(function($s) {
                              $s->where('handover_target', 'managed_service')
                                ->orWhere('project_type', 'like', '%Managed Service%');
                          });
                  });
            })
            ->where(function($q) {
                $q->whereNull('handover_target')
                  ->orWhere('handover_target', '!=', 'pmo');
            });

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('client', 'like', "%{$s}%")
                  ->orWhere('sales_name', 'like', "%{$s}%")
                  ->orWhere('presales_name', 'like', "%{$s}%")
                  ->orWhere('location', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where(function($q) use ($request) {
                $q->where('status', $request->status)
                  ->orWhere('stage', $request->status);
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('project_type', 'like', "%{$request->type}%");
        }

        $projects = $query->latest()->paginate($perPage)->withQueryString();

        // 2. Query Serah Terima Kontrak Layanan Masuk (Incoming Service Handovers) - Hanya yang Pending / Menunggu Aktivasi
        $incomingQuery = Project::where(function($q) {
                $q->where('handover_target', 'managed_service')
                  ->orWhere(function($sub) {
                      $sub->where('handover_target', 'both')
                          ->where('project_type', 'not like', '%One-Time%');
                  });
            })
            ->whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
            ->where(function($q) {
                $q->whereNull('project_type')
                  ->orWhere('project_type', 'not like', '%One-Time%');
            })
            ->where('handover_target', '!=', 'pmo')
            ->where(function($q) {
                $q->whereNull('ms_handover_status')
                  ->orWhere('ms_handover_status', '!=', 'Accepted');
            })
            ->with(['creator', 'commercialHandoverBy', 'pm', 'msAcceptedBy']);

        if ($request->filled('search')) {
            $s = $request->search;
            $incomingQuery->where(function($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('client', 'like', "%{$s}%")
                  ->orWhere('sales_name', 'like', "%{$s}%")
                  ->orWhere('location', 'like', "%{$s}%");
            });
        }

        $incomingHandovers = $incomingQuery->latest('updated_at')->get();

        foreach ($incomingHandovers as $ho) {
            $ho->resolved_documents = self::getProjectDocuments($ho);
            $hd = is_array($ho->handover_data) ? $ho->handover_data : [];
            $sSig = $hd['sales_signature'] ?? [];
            $salesUser = null;
            if (!empty($sSig['user_id'])) {
                $salesUser = User::find($sSig['user_id']);
            } elseif ($ho->commercial_handover_by) {
                $salesUser = $ho->commercialHandoverBy;
            } elseif ($ho->creator) {
                $salesUser = $ho->creator;
            }
            if ($salesUser) {
                $sSig['qr_svg'] = $salesUser->signature_qr_svg;
                $sSig['qr_token'] = $sSig['qr_token'] ?? $salesUser->ensureSignatureQrToken();
                $sSig['user_name'] = $sSig['user_name'] ?? $salesUser->name;
            } elseif (!empty($sSig['qr_token'])) {
                try {
                    $sSig['qr_svg'] = (string) \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                        ->size(80)->margin(0)->errorCorrection('M')
                        ->generate(route('public.signature.verify', $sSig['qr_token']));
                } catch (\Throwable $e) {}
            }
            $hd['sales_signature'] = $sSig;
            $ho->handover_data = $hd;
        }

        $pendingHandoversCount = $incomingHandovers->where('ms_handover_status', '!=', 'Accepted')->count();
        $currentUserName = auth()->user()?->name ?? 'Tim Managed Service';
        $leadMsName = $currentUserName;

        $fieldEngineers = User::where('status', 'Active')
            ->where(function($q) {
                $q->whereHas('roles', fn($r) => $r->whereIn('name', ['Field Support', 'Field Support (EOS)', 'Maintenance', 'Lead Maintenance', 'Managed Service', 'Engineer', 'Network Engineer', 'Security Engineer']))
                  ->orWhere('position', 'like', '%Engineer%')
                  ->orWhere('position', 'like', '%Teknisi%')
                  ->orWhere('position', 'like', '%Maintenance%');
            })
            ->orderBy('name')
            ->get(['id', 'name', 'position']);

        if ($fieldEngineers->isEmpty()) {
            $fieldEngineers = User::where('status', 'Active')->orderBy('name')->get(['id', 'name', 'position']);
        }

        $salesUsers = User::where('status', 'Active')
            ->whereHas('roles', fn($r) => $r->where('name', 'Sales'))
            ->orderBy('name')
            ->get(['id', 'name']);

        
        // Batasi penugasan PM/Koordinator di modul Maintenance hanya untuk personil Tim Maintenance (Eris, Doris, Mario)
        $pmUsers = User::where('status', 'Active')
            ->where(function($q) {
                $q->whereHas('roles', fn($r) => $r->whereIn('name', ['Lead Maintenance', 'Maintenance', 'Managed Service', 'Field Support (EOS)']))
                  ->orWhereHas('division', fn($d) => $d->where('name', 'like', '%Maintenance%')->orWhere('name', 'like', '%Helpdesk%'))
                  ->orWhereIn('name', ['Doris', 'Mario', 'Eris']);
            })
            ->where('name', 'not like', '%Lead Engineer%')
            ->orderBy('name')
            ->get(['id', 'name', 'position']);

        if ($pmUsers->isEmpty()) {
            $pmUsers = User::where('status', 'Active')
                ->whereIn('name', ['Doris', 'Mario', 'Eris'])
                ->orderBy('name')
                ->get(['id', 'name', 'position']);
        }

        return view('managed_service.projects', compact('projects', 'incomingHandovers', 'pendingHandoversCount', 'perPage', 'leadMsName', 'currentUserName', 'fieldEngineers', 'salesUsers', 'pmUsers'));
    }

    /**
     * Simpan Kontrak Managed Service Baru yang dibuat langsung oleh tim Managed Service
     */
    public function storeProject(Request $request)
    {
        $validated = $request->validate([
            'name'                      => 'required|string|max:255',
            'client'                    => 'required|string|max:255',
            'customer_pic'              => 'nullable|string|max:255',
            'customer_pic_phone'        => 'nullable|string|max:50',
            'location'                  => 'nullable|string|max:255',
            'sales_name'                => 'nullable|string|max:255',
            'po_spk_number'             => 'nullable|string|max:100',
            'contract_value'            => 'nullable',
            'service_start_date'        => 'required|date',
            'service_end_date'          => 'required|date|after_or_equal:service_start_date',
            'billing_terms'             => 'nullable|string|max:255',
            'sla_tier'                  => 'required|string|in:Platinum,Gold,Silver,Bronze',
            'sla_target_percentage'     => 'nullable|numeric|between:80,100',
            'mttr_hours'                => 'nullable|numeric|min:1',
            'call_response_time'        => 'nullable|string|max:50',
            'pm_frequency_type'         => 'required|string',
            'pm_total_cycles'           => 'nullable|integer|min:1',
            'cm_quota'                  => 'nullable|integer|min:0',
            'pm_id'                     => 'nullable|exists:users,id',
            'pm_name'                   => 'nullable|string|max:255',
            'description'               => 'nullable|string',
            'teams'                     => 'nullable|array',
            'file_bast'                 => 'nullable|file|max:51200',
            'file_spk'                  => 'nullable|file|max:51200',
            'file_contract'             => 'nullable|file|max:51200',
            'file_boq'                  => 'nullable|file|max:51200',
            'file_tor'                  => 'nullable|file|max:51200',
            'file_tech'                 => 'nullable|file|max:51200',
        ]);

        // Parsing nilai kontrak rupiah (bersihkan format titik / koma / simbol)
        $rawVal = preg_replace('/[^\d]/', '', (string) ($request->contract_value ?? '0'));
        $contractValue = ($rawVal !== '' && $rawVal !== '0') ? (float) $rawVal : null;

        // SLA Tier Preset Defaults
        $tier = $validated['sla_tier'];
        $tierDefaults = match($tier) {
            'Platinum' => ['sla' => 99.90, 'mttr' => 2, 'resp' => '15 Menit', 'cm' => 24],
            'Gold'     => ['sla' => 99.50, 'mttr' => 4, 'resp' => '30 Menit', 'cm' => 12],
            'Silver'   => ['sla' => 99.00, 'mttr' => 8, 'resp' => '1 Jam',    'cm' => 6],
            'Bronze'   => ['sla' => 98.00, 'mttr' => 12, 'resp' => '2 Jam',   'cm' => 4],
            default    => ['sla' => 99.50, 'mttr' => 4, 'resp' => '30 Menit', 'cm' => 12],
        };

        $slaTarget = !empty($validated['sla_target_percentage']) ? (float) $validated['sla_target_percentage'] : $tierDefaults['sla'];
        $mttrHours = !empty($validated['mttr_hours']) ? (int) $validated['mttr_hours'] : $tierDefaults['mttr'];
        $callResp  = !empty($validated['call_response_time']) ? $validated['call_response_time'] : $tierDefaults['resp'];
        $cmQuota   = (isset($validated['cm_quota']) && $validated['cm_quota'] !== '') ? (int) $validated['cm_quota'] : $tierDefaults['cm'];

        // PM Total Cycles Default
        $freq = $validated['pm_frequency_type'];
        $freqCycles = match($freq) {
            'per_bulan'   => 12,
            'per_2_bulan' => 6,
            'per_3_bulan' => 4,
            'per_4_bulan' => 3,
            'per_6_bulan' => 2,
            'tahunan'     => 1,
            default       => 4,
        };
        $totalCycles = !empty($validated['pm_total_cycles']) ? (int) $validated['pm_total_cycles'] : $freqCycles;

        $currentUser = auth()->user();

        // Project Manager resolution
        $pmUser = null;
        if (!empty($validated['pm_id'])) {
            $pmUser = User::find($validated['pm_id']);
        }
        if (!$pmUser || in_array(trim($pmUser->name), ['Lead Engineer', 'Engineer'])) {
            $pmUser = User::where('name', 'like', '%Eris%')->first() ?: $currentUser;
        }
        $pmName = $pmUser?->name ?: ($validated['pm_name'] ?: 'Eris');
        $pmId   = $pmUser?->id ?: $currentUser?->id;

        // Tim Pelaksana Teknis (Tim Network, Tim Security, atau keduanya)
        $rawTeams = (array) ($request->input('teams') ?? ['Tim Network']);
        $cleanTeams = [];
        foreach ($rawTeams as $t) {
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

        // Resolusi Division ID
        $hasNet = false;
        $hasSec = false;
        foreach ($cleanTeams as $tm) {
            $l = strtolower($tm);
            if (str_contains($l, 'net')) $hasNet = true;
            if (str_contains($l, 'sec')) $hasSec = true;
        }
        $divisionId = null;
        if (class_exists(\App\Models\Division::class)) {
            if ($hasNet && $hasSec) {
                $divisionId = \App\Models\Division::where('name', 'like', '%Network%')->where('name', 'like', '%Security%')->value('id')
                    ?? \App\Models\Division::where('name', 'like', '%Network%')->value('id');
            } elseif ($hasNet) {
                $divisionId = \App\Models\Division::where('name', 'like', '%Network%')->value('id');
            } elseif ($hasSec) {
                $divisionId = \App\Models\Division::where('name', 'like', '%Security%')->value('id');
            }
        }

        $handoverData = [
            'source'                 => 'Direct Managed Service Contract',
            'so_number'              => 'SO-' . date('Y') . '-MS-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $validated['client']), 0, 6)) . rand(10, 99),
            'po_spk_number'          => $validated['po_spk_number'] ?? null,
            'billing_terms'          => $validated['billing_terms'] ?? 'Bulanan (Net 30 Hari)',
            'coordinator_name'       => $pmName,
            'lead_engineer_name'     => $pmName,
            'team_delegated_by_name' => $pmName,
            'pic_ms_name'            => $pmName,
            'project_manager'        => $pmName,
            'pm_name'                => $pmName,
            'pm_id'                  => $pmId,
            'assigned_teams'         => $cleanTeams,
            'customer_pic'           => $validated['customer_pic'] ?? null,
            'customer_pic_phone'     => $validated['customer_pic_phone'] ?? null,
            'notes'                  => $validated['description'] ?? null,
            'assigned_engineers'     => [],
            'ms_signature'           => [
                'user_name' => $pmName,
                'signed_at' => now()->toIso8601String(),
                'notes'     => 'Kontrak Service dibuat & diaktifkan langsung oleh tim Managed Service.',
            ],
        ];

        $salesName = $validated['sales_name'] ?: 'Direct Maintenance';

        $project = Project::create([
            'name'                      => $validated['name'],
            'client'                    => $validated['client'],
            'customer_pic_technical'    => $validated['customer_pic'] ?? null,
            'customer_pic_business'     => $validated['customer_pic'] ?? null,
            'location'                  => $validated['location'] ?? null,
            'sales_name'                => $salesName,
            'pm_id'                     => $pmId,
            'division_id'               => $divisionId,
            'contract_value'            => $contractValue,
            'po_spk_number'             => $validated['po_spk_number'] ?? null,
            'po_number'                 => $validated['po_spk_number'] ?? null,
            'stage'                     => 'Operate',
            'status'                    => 'Active',
            'sales_stage'               => 'Closed Won',
            'ms_handover_status'        => 'Accepted',
            'ms_accepted_by'            => $currentUser?->id,
            'ms_accepted_at'            => now(),
            'handover_target'           => 'managed_service',
            'project_type'              => 'Managed Service SLA',
            'sla_tier'                  => $tier,
            'sla_target_percentage'     => $slaTarget,
            'mttr_hours'                => $mttrHours,
            'call_response_time'        => $callResp,
            'incident_resolution_hours' => $mttrHours,
            'pm_frequency_type'         => $freq,
            'pm_total_cycles'           => $totalCycles,
            'cm_quota'                  => $cmQuota,
            'service_start_date'        => $validated['service_start_date'],
            'service_end_date'          => $validated['service_end_date'],
            'start_date'                => $validated['service_start_date'],
            'deadline'                  => $validated['service_end_date'],
            'billing_terms'             => $validated['billing_terms'] ?? 'Bulanan (Net 30 Hari)',
            'description'               => $validated['description'] ?? null,
            'created_by'                => $currentUser?->id ?? 1,
            'handover_data'             => $handoverData,
        ]);

        // Upload berkas dokumen kontrak / legalitas / teknis jika dilampirkan
        $docMapping = [
            'file_bast'     => ['key' => 'ba',          'column' => 'bast_file',     'title' => 'Berita Acara Serah Terima (BAST)'],
            'file_spk'      => ['key' => 'spk',         'column' => 'po_spk_file',   'title' => 'Surat Perintah Kerja (SPK)'],
            'file_contract' => ['key' => 'contract',    'column' => 'contract_file', 'title' => 'Kontrak Layanan Managed Service'],
            'file_boq'      => ['key' => 'boq',         'column' => 'boq_file',      'title' => 'Bill of Quantity (BOQ)'],
            'file_tor'      => ['key' => 'tor',         'column' => 'tor_file',      'title' => 'Terms of Reference (TOR)'],
            'file_tech'     => ['key' => 'project_doc', 'column' => 'proposal_file', 'title' => 'Dokumen Teknis & Topologi Proyek'],
        ];

        $uploadedPaths = [];
        $checklist = [];
        foreach ($docMapping as $inputName => $meta) {
            if ($request->hasFile($inputName) && $request->file($inputName)->isValid()) {
                $file = $request->file($inputName);
                $filePath = FileUploadHelper::storePublicly($file, 'managed_service_documents');
                $uploadedPaths[$meta['column']] = $filePath;
                $checklist[$meta['key']] = true;

                if (Schema::hasTable('project_documents')) {
                    ProjectDocument::updateOrCreate(
                        [
                            'project_id'   => $project->id,
                            'document_key' => $meta['key'],
                        ],
                        [
                            'document_title' => $meta['title'],
                            'file_path'      => $filePath,
                            'file_name'      => $file->getClientOriginalName(),
                            'file_size'      => $file->getSize() ?: 0,
                            'status'         => 'Uploaded',
                            'uploaded_by'    => $currentUser?->id,
                            'uploaded_at'    => now(),
                        ]
                    );
                }
            }
        }

        if (!empty($uploadedPaths)) {
            $uploadedPaths['documents_checklist'] = $checklist;
            $project->update($uploadedPaths);
        }

        // Auto inisialisasi PM cycles berdasarkan durasi & frekuensi
        self::initPmCycles($project);

        return redirect()->route('ms.projects.index', ['tab' => 'active'])
            ->with('success', "Kontrak Managed Service '{$project->name}' untuk klien '{$project->client}' berhasil dibuat & otomatis diaktifkan.");
    }

    /**
     * Helper Ekstraksi & Resolusi 6 Berkas Dokumen Kontrak & Legal Proyek
     */
    public static function getProjectDocuments($project)
    {
        $projectDocs = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('project_documents')) {
            $projectDocs = \App\Models\ProjectDocument::where('project_id', $project->id)
                ->whereNotNull('file_path')
                ->where('file_path', '!=', '')
                ->latest()
                ->get();
        }

        $findDoc = function(array $keys, array $keywords = [], array $types = []) use ($projectDocs) {
            return $projectDocs->first(function ($doc) use ($keys, $keywords, $types) {
                $docKey   = strtoupper($doc->document_key ?? '');
                $docType  = strtoupper($doc->document_type ?? '');
                $docTitle = strtolower($doc->document_title ?? ($doc->name ?? ''));
                $fileName = strtolower($doc->file_name ?? '');

                foreach ($keys as $k) {
                    $kUpper = strtoupper($k);
                    if ($docKey === $kUpper || str_contains($docKey, $kUpper) || $docKey === 'LAMPIRAN_' . $kUpper || str_starts_with($docKey, 'DOC_' . $kUpper)) {
                        return true;
                    }
                }
                foreach ($types as $t) {
                    if ($docType === strtoupper($t)) {
                        return true;
                    }
                }
                foreach ($keywords as $kw) {
                    $kwLower = strtolower($kw);
                    if (str_contains($docTitle, $kwLower) || str_contains($fileName, $kwLower)) {
                        return true;
                    }
                }
                return false;
            });
        };

        $isHandedOver = in_array($project->ms_handover_status, ['Accepted', 'Approved'])
            || in_array($project->handover_status, ['Approved', 'Submitted'])
            || strtolower($project->stage ?? '') === 'operate'
            || !empty($project->commercial_handover_at);

        // 1. SPK / PO
        $spkDoc = $findDoc(['spk', 'po', 'contract_po_so'], ['perintah kerja', 'purchase order', 'spk', 'po_'], ['SPK', 'PO']);
        $spkFilePath = $project->po_spk_file ?: ($project->po_file ?: ($spkDoc?->file_path));
        $spkNumber   = $project->po_spk_number ?: ($project->po_number ?: ($spkDoc ? ($spkDoc->notes ?? ('SPK-' . $project->id)) : ('SPK/' . ($project->so_code ?: $project->id))));
        $spkDate     = $project->po_spk_date ?: ($spkDoc?->created_at?->format('Y-m-d') ?: ($project->service_start_date ?: $project->start_date));

        // 2. Kontrak / PKS
        $contractDoc = $findDoc(['contract', 'pks', 'commercial_handover_package'], ['kontrak', 'perjanjian', 'contract', 'pks'], ['CONTRACT', 'PKS']) ?: ($spkDoc && (in_array(strtoupper($spkDoc->document_key ?? ''), ['CONTRACT_PO_SO', 'COMMERCIAL_HANDOVER_PACKAGE']) || str_contains(strtolower($spkDoc->document_title ?? ''), 'contract') || str_contains(strtolower($spkDoc->document_title ?? ''), 'kontrak')) ? $spkDoc : null);
        $contractFilePath = $project->contract_file ?: ($project->po_file ?: ($contractDoc?->file_path ?: ($project->po_spk_file ?? null)));
        $contractNumber   = $project->po_number ?: ('KTR/' . ($project->so_code ?: $project->id));
        $contractDate     = $contractDoc?->created_at?->format('Y-m-d') ?: ($project->service_start_date ?: $project->start_date);

        // 3. TOR
        $torDoc = $findDoc(['tor', 'kak'], ['terms of reference', 'tor', 'kak', 'kerangka acuan'], ['TOR', 'KAK']);
        $torFilePath = $project->tor_file ?: ($torDoc?->file_path);
        $torNumber   = $project->tor_number ?: ('TOR-' . ($project->so_code ?: $project->id));
        $torDate     = $torDoc?->created_at?->format('Y-m-d') ?: ($project->service_start_date ?: $project->start_date);

        // 4. BOQ (Prioritaskan dokumen BoQ resmi dari ProjectDocument / Presales)
        $boqDoc = $findDoc(['boq', 'bom', 'boq_bom'], ['boq', 'bom', 'bill of quantity', 'built of material'], ['BOQ', 'BOM']);
        $boqFilePath = $boqDoc?->file_path ?: ($project->boq_file ?: ($project->bom_file ?? null));
        $boqNumber   = 'BOQ-' . ($project->po_spk_number ?: ($project->so_code ?: $project->id));
        $boqDate     = $boqDoc?->created_at?->format('Y-m-d') ?: ($project->service_start_date ?: $project->start_date);

        // 5. BA / BAST
        $baDoc = $findDoc(['ba', 'bast', 'balap', 'bap'], ['serah terima', 'berita acara', 'bast', 'balap', 'bap'], ['BA', 'BAST', 'BALAP', 'BAP']);
        $baFilePath = $project->bast_file ?: ($project->ba_file ?? null ?: ($baDoc?->file_path));
        $baNumber   = 'BAST/' . ($project->so_code ?: $project->id);
        $baDate     = $baDoc?->created_at?->format('Y-m-d') ?: ($project->service_start_date ?: $project->start_date);
        $baFileUrl  = $baFilePath ? asset('storage/' . $baFilePath) : ($isHandedOver ? route('sales.handover.document', $project->id) : null);
        if (!$baFilePath && $isHandedOver) {
            $baFilePath = 'BAST-Handover-' . $project->id . '.pdf';
        }

        // 6. Dokumen Teknis & Topologi Proyek
        $techDoc = $findDoc(['project_doc', 'technical_proposal', 'commercial_proposal', 'customer_requirement', 'PRP', 'PRJ', 'BRD', 'topology', 'solution'], ['proposal', 'topologi', 'arsitektur', 'teknis', 'desain', 'diagram', 'sizing'], ['PRP', 'PRJ', 'TOPOLOGY']) ?: $projectDocs->first(fn($d) => in_array($d->stage_name ?? '', ['Solution', 'Presales']));
        $techFilePath = $techDoc?->file_path ?: ($project->proposal_file ?: ($project->technical_file ?? null ?: ($project->topology_file ?? null)));
        $techNumber   = 'DOC-' . ($project->so_code ?: $project->id);
        $techDate     = $techDoc?->created_at?->format('Y-m-d') ?: ($project->service_start_date ?: $project->start_date);

        // Fallback file sampel jika proyek sudah disahkan oleh Sales/MS agar tidak pernah "Menunggu Sales"
        if ($isHandedOver) {
            if (!$spkFilePath && file_exists(storage_path('app/public/project_documents/sample_sc_contract.pdf'))) {
                $spkFilePath = 'project_documents/sample_sc_contract.pdf';
            }
            if (!$contractFilePath && file_exists(storage_path('app/public/project_documents/sample_sc_contract.pdf'))) {
                $contractFilePath = 'project_documents/sample_sc_contract.pdf';
            }
            if (!$boqFilePath && file_exists(storage_path('app/public/project_documents/sample_technical_boq.pdf'))) {
                $boqFilePath = 'project_documents/sample_technical_boq.pdf';
            }
            if (!$torFilePath && file_exists(storage_path('app/public/project_documents/sample_sla_managed_service.pdf'))) {
                $torFilePath = 'project_documents/sample_sla_managed_service.pdf';
            }
            if (!$techFilePath && file_exists(storage_path('app/public/project_documents/sample_technical_boq.pdf'))) {
                $techFilePath = 'project_documents/sample_technical_boq.pdf';
            }
        }

        $checklist = is_array($project->documents_checklist) ? $project->documents_checklist : [];

        return [
            'ba' => [
                'key'         => 'ba',
                'title'       => 'Berita Acara Serah Terima (BA / BAST / BAP)',
                'description' => 'Bukti serah terima operasional dan milestone penagihan berkala.',
                'file_path'   => $baFilePath,
                'file_url'    => $baFileUrl ?: ($baFilePath ? asset('storage/' . $baFilePath) : null),
                'number'      => $baNumber,
                'date'        => $baDate,
                'verified'    => !empty($checklist['ba']) || !empty($checklist['bast']) || !empty($baFilePath) || $isHandedOver,
            ],
            'spk' => [
                'key'         => 'spk',
                'title'       => 'Surat Perintah Kerja (SPK / PO)',
                'description' => 'Dokumen acuan perintah kerja resmi dari klien.',
                'file_path'   => $spkFilePath,
                'file_url'    => $spkFilePath ? asset('storage/' . $spkFilePath) : null,
                'number'      => $spkNumber,
                'date'        => $spkDate,
                'verified'    => !empty($checklist['spk']) || !empty($spkFilePath) || $isHandedOver,
            ],
            'contract' => [
                'key'         => 'contract',
                'title'       => 'Kontrak Perjanjian Layanan (PKS / Contract)',
                'description' => 'Perjanjian kerja sama SLA dan pemeliharaan jangka panjang.',
                'file_path'   => $contractFilePath,
                'file_url'    => $contractFilePath ? asset('storage/' . $contractFilePath) : null,
                'number'      => $contractNumber,
                'date'        => $contractDate,
                'verified'    => !empty($checklist['contract']) || !empty($contractFilePath) || $isHandedOver,
            ],
            'boq' => [
                'key'         => 'boq',
                'title'       => 'Bill of Quantity (BOQ & Spesifikasi Layanan)',
                'description' => 'Daftar rincian perangkat keras, lisensi software, dan alokasi mandays.',
                'file_path'   => $boqFilePath,
                'file_url'    => $boqFilePath ? asset('storage/' . $boqFilePath) : null,
                'number'      => $boqNumber,
                'date'        => $boqDate,
                'verified'    => !empty($checklist['boq']) || !empty($boqFilePath) || $isHandedOver,
            ],
            'tor' => [
                'key'         => 'tor',
                'title'       => 'Terms of Reference (TOR)',
                'description' => 'Kerangka acuan kerja, ruang lingkup & spesifikasi teknis layanan.',
                'file_path'   => $torFilePath,
                'file_url'    => $torFilePath ? asset('storage/' . $torFilePath) : null,
                'number'      => $torNumber,
                'date'        => $torDate,
                'verified'    => !empty($checklist['tor']) || !empty($torFilePath) || $isHandedOver,
            ],
            'project_doc' => [
                'key'         => 'project_doc',
                'title'       => 'Dokumen Teknis & Topologi Proyek',
                'description' => 'Diagram jaringan, desain arsitektur, IP addressing, dan NDA.',
                'file_path'   => $techFilePath,
                'file_url'    => $techFilePath ? asset('storage/' . $techFilePath) : null,
                'number'      => $techNumber,
                'date'        => $techDate,
                'verified'    => !empty($checklist['project_doc']) || !empty($checklist['technical']) || !empty($techFilePath) || $isHandedOver,
            ],
        ];
    }

    /**
     * Detail Kontrak & Dokumen Proyek Managed Service
     */
    public function showProject(Request $request, $id)
    {
        $this->ensureTablesExist();
        $this->ensureSampleProjectExists();

        $project = Project::with(['pmCycles.assignedEngineer', 'managedServiceTickets.assignedEngineer', 'creator', 'managedServiceAssets'])
            ->findOrFail($id);

        $isEligible = ($project->stage === 'Operate')
            || ($project->sales_stage === 'Closed Won' && in_array($project->handover_target, ['managed_service', 'both']))
            || ($project->handover_target === 'managed_service' && in_array($project->handover_status, ['Approved', 'Submitted']));

        if (!$isEligible) {
            return redirect()->route('ms.projects.index')->with('error', 'Proyek "' . $project->name . '" belum dapat diproses di Managed Service karena masih dalam tahap Sales pipeline dan belum disahkan Closed Won / masuk tahap Operate.');
        }

        self::initPmCycles($project);

        $projectDocs = collect();
        if (Schema::hasTable('project_documents')) {
            $projectDocs = ProjectDocument::where('project_id', $project->id)
                ->whereNotNull('file_path')
                ->where('file_path', '!=', '')
                ->latest()
                ->get();
        }

        $documents = self::getProjectDocuments($project);

        // Kumpulkan berkas dokumen tambahan dari Sales / Presales / ProjectDocument yang belum dipakai di 6 slot utama
        $usedPaths = array_filter(array_column($documents, 'file_path'));
        $additionalDocs = $projectDocs->reject(function($doc) use ($usedPaths) {
            return in_array($doc->file_path, $usedPaths);
        })->values();

        $availableDocCount = count(array_filter($documents, fn($d) => !empty($d['file_path'])));

        $cmTickets = $project->managedServiceTickets()
            ->where(function($q) {
                $q->where('type', 'like', '%Corrective%')
                  ->orWhere('type', 'like', '%Incident%');
            })->orderBy('created_at', 'asc')->orderBy('id', 'asc')->get();

        // Normalisasi nomor tiket CM agar urut rapi sesuai kode proyek/klien
        $projectCode = 'NET';
        if (!empty($project->client)) {
            if (stripos($project->client, 'Telkom') !== false) {
                $projectCode = 'TLK';
            } elseif (stripos($project->client, 'Angkasa Pura') !== false) {
                $projectCode = 'APS';
            } elseif (stripos($project->client, 'PLN') !== false) {
                $projectCode = 'PLN';
            } elseif (stripos($project->client, 'Bank Rakyat Indonesia') !== false || stripos($project->client, 'BRI') !== false) {
                $projectCode = 'BRI';
            } elseif (stripos($project->client, 'Mandiri') !== false) {
                $projectCode = 'MDR';
            }
        }

        $ticketSeq = 1;
        foreach ($cmTickets as $t) {
            $year = $t->created_at ? $t->created_at->format('Y') : date('Y');
            $expectedNum = 'CM-' . $projectCode . '-' . $year . '-' . str_pad($ticketSeq, 3, '0', STR_PAD_LEFT);
            $needsSave = false;

            if ($t->ticket_number !== $expectedNum) {
                $t->ticket_number = $expectedNum;
                $needsSave = true;
            }

            // Normalisasi 4 level prioritas standar: Critical, High, Medium, Low
            $normalizedPrio = match(strtolower($t->priority ?? '')) {
                'platinum', 'critical'       => 'Critical',
                'gold', 'high', 'major'      => 'High',
                'silver', 'medium', 'minor'  => 'Medium',
                'bronze', 'low'              => 'Low',
                default                      => 'Medium',
            };
            if ($t->priority !== $normalizedPrio) {
                $t->priority = $normalizedPrio;
                $needsSave = true;
            }

            if ($needsSave) {
                $t->save();
            }

            $ticketSeq++;
        }

        $activeTab = $request->input('tab', 'documents');
        if (!in_array($activeTab, ['documents', 'pm', 'cm', 'sla'])) {
            $activeTab = 'documents';
        }

        $cmQuota = $project->cm_quota ?: 12;
        $cmUsed = $cmTickets->count();
        $cmRemaining = max(0, $cmQuota - $cmUsed);

        $pmCycles = $project->pmCycles()->orderBy('cycle_number')->get();
        $pmCompleted = $pmCycles->where('activity_status', 'Completed')->count();
        $pmTotal = $pmCycles->count() ?: ($project->pm_total_cycles ?: 4);
        $pmProgress = $pmTotal > 0 ? round(($pmCompleted / $pmTotal) * 100) : 0;

        // Normalisasi handover_data: PIC Maintenance vs Delegasi Lead Engineer
        $handoverData = is_array($project->handover_data) ? $project->handover_data : [];
        $needsHandoverSave = false;
        if (($handoverData['team_delegated_by_name'] ?? '') === 'Lead Maintenance' || empty($handoverData['team_delegated_by_name'])) {
            $handoverData['team_delegated_by_name'] = 'Lead Engineer';
            $needsHandoverSave = true;
        }
        if (($handoverData['lead_engineer_name'] ?? '') === 'Lead Maintenance' || empty($handoverData['lead_engineer_name'])) {
            $handoverData['lead_engineer_name'] = 'Lead Engineer';
            $needsHandoverSave = true;
        }
        if (empty($handoverData['pic_ms_name'])) {
            $handoverData['pic_ms_name'] = 'Doris (Tim Maintenance)';
            $needsHandoverSave = true;
        }
        if ($needsHandoverSave) {
            $project->handover_data = $handoverData;
            $project->save();
        }

        // -------------------------------------------------------------
        // PERHITUNGAN & EVALUASI SLA KETERSEDIAAN (AVAILABILITY & DOWNTIME)
        // -------------------------------------------------------------
        $daysInMonth = now()->daysInMonth;
        $totalPeriodHours = $daysInMonth * 24; // misal 720 jam (30 hari) atau 744 jam (31 hari)
        $targetSla = (float) ($project->sla_target_percentage ?: 99.50);
        $totalProjDowntimeHours = 0;
        $downtimeDeviceList = collect([]);

        foreach ($cmTickets as $ticket) {
            $start = $ticket->incident_date ? \Carbon\Carbon::parse($ticket->incident_date) : ($ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at) : now());
            $isResolved = in_array($ticket->status, ['Resolved', 'Closed']);
            $end = $ticket->resolved_at ? \Carbon\Carbon::parse($ticket->resolved_at) : ($isResolved ? \Carbon\Carbon::parse($ticket->updated_at) : now());
            
            $diffMins = max(0, $start->diffInMinutes($end));
            $diffHours = round($diffMins / 60, 2);
            $totalProjDowntimeHours += $diffHours;

            // SLA perangkat = ((Total Periode - Durasi Down) / Total Periode) * 100
            $devSla = $totalPeriodHours > 0 
                ? max(0, min(100, round((($totalPeriodHours - $diffHours) / $totalPeriodHours) * 100, 2))) 
                : 100.0;

            $hoursPart = floor($diffMins / 60);
            $minsPart = $diffMins % 60;
            $durationText = ($hoursPart > 0 ? "{$hoursPart} jam " : "") . ($minsPart > 0 ? "{$minsPart} mnt" : ($hoursPart == 0 ? "0 mnt" : ""));

            $deviceCategory = !empty($ticket->category) ? $ticket->category : 'Switch / Router';
            if (!empty($ticket->asset?->name)) {
                $deviceName = $ticket->asset->name . ' (' . $deviceCategory . ')';
            } else {
                $deviceName = $deviceCategory;
            }

            $downtimeDeviceList->push([
                'id'             => $ticket->id,
                'ticket_number'  => $ticket->ticket_number,
                'device'         => $deviceName,
                'category'       => $deviceCategory,
                'title'          => $ticket->title,
                'start_down'     => $start->format('d M Y, H:i'),
                'end_down'       => $isResolved ? $end->format('d M Y, H:i') : 'Sedang Ditangani',
                'duration_hours' => $diffHours,
                'duration_text'  => $durationText,
                'device_sla'     => $devSla,
                'root_cause'     => $ticket->root_cause ?: ($ticket->resolution_notes ?: 'Insiden teknis operasional'),
                'status'         => $ticket->status,
                'is_resolved'    => $isResolved,
                'priority'       => $ticket->priority,
            ]);
        }

        // Akumulasi SLA Proyek Berdasarkan Total Downtime Periode
        $projectSlaUptime = $totalPeriodHours > 0 
            ? max(0, min(100, round((($totalPeriodHours - $totalProjDowntimeHours) / $totalPeriodHours) * 100, 2))) 
            : 100.0;

        // Toleransi downtime maksimal kontrak
        $maxAllowedDowntimeHours = round($totalPeriodHours * (1 - ($targetSla / 100)), 2);
        $remainingBufferHours = round($maxAllowedDowntimeHours - $totalProjDowntimeHours, 2);
        $isSlaMet = $projectSlaUptime >= $targetSla;

        $allProjects = Project::whereNotIn('name', ['DAY OFF', 'Day Off'])->orderBy('name')->get();

        $engineers = User::where(function($q) {
            $q->where('division_id', 3)
              ->orWhereHas('roles', fn($r) => $r->whereIn('name', [
                  'Lead Maintenance', 'Maintenance', 'Managed Service', 'Field Support', 'Field Support (EOS)'
              ]));
        })->whereDoesntHave('roles', fn($r) => $r->whereIn('name', ['Lead Engineer', 'Team Leader', 'Director', 'Direktur', 'PMO']))
          ->orderBy('name')->get(['id', 'name']);

        return view('managed_service.project_detail', compact(
            'project', 'allProjects', 'documents', 'additionalDocs', 'availableDocCount', 'cmQuota', 'cmUsed', 'cmRemaining', 'cmTickets',
            'pmCycles', 'pmCompleted', 'pmTotal', 'pmProgress', 'engineers', 'activeTab',
            'daysInMonth', 'totalPeriodHours', 'targetSla', 'totalProjDowntimeHours', 'downtimeDeviceList',
            'projectSlaUptime', 'maxAllowedDowntimeHours', 'remainingBufferHours', 'isSlaMet'
        ));
    }

    /**
     * Upload & Perbarui Dokumen Proyek (SPK, Kontrak, BOQ, BA, Dokumen Project)
     */
    public function updateProjectDocuments(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        // Tangani error PHP upload awal
        if (isset($_FILES['doc_file']) && !empty($_FILES['doc_file']['name'])) {
            $err = $_FILES['doc_file']['error'] ?? UPLOAD_ERR_OK;
            if ($err !== UPLOAD_ERR_OK && $err !== UPLOAD_ERR_NO_FILE) {
                $msg = match($err) {
                    UPLOAD_ERR_INI_SIZE   => 'Ukuran file terlalu besar melebihi batas upload_max_filesize server. Silakan perkecil file atau pilih berkas di bawah 50MB.',
                    UPLOAD_ERR_PARTIAL    => 'File hanya terunggah sebagian karena gangguan jaringan. Silakan unggah kembali.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Direktori temporary server tidak ditemukan.',
                    UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk server (periksa izin folder storage).',
                    default               => 'Terjadi kendala saat mengunggah berkas (Kode error: ' . $err . ').',
                };
                return redirect()->back()->withErrors(['doc_file' => $msg]);
            }
        }

        $validated = $request->validate([
            'doc_type'   => 'required|string|in:spk,contract,boq,ba,project_doc,tor',
            'doc_file'   => 'required|file|max:51200',
            'doc_number' => 'nullable|string|max:100',
            'doc_date'   => 'nullable|date',
        ], [
            'doc_file.uploaded' => 'Berkas gagal diunggah ke server. Pastikan ukuran berkas tidak melebihi kapasitas server atau formulir.',
            'doc_file.max'      => 'Ukuran berkas tidak boleh melebihi batas maksimal 50MB.',
            'doc_file.required' => 'Silakan pilih berkas dokumen yang akan diunggah.',
        ]);

        $file = $request->file('doc_file');
        $filePath = FileUploadHelper::storePublicly($file, 'managed_service_documents');

        match($validated['doc_type']) {
            'spk' => $project->update([
                'po_spk_file'   => $filePath,
                'po_spk_number' => $validated['doc_number'] ?: $project->po_spk_number,
                'po_spk_date'   => $validated['doc_date'] ?: $project->po_spk_date,
            ]),
            'contract' => $project->update([
                'contract_file' => $filePath,
            ]),
            'boq' => $project->update([
                'boq_file' => $filePath,
            ]),
            'ba' => $project->update([
                'bast_file' => $filePath,
            ]),
            'project_doc' => $project->update([
                'proposal_file' => $filePath,
            ]),
            'tor' => (function() use ($project, $filePath, $validated) {
                $data = [];
                if (Schema::hasColumn('projects', 'tor_file')) {
                    $data['tor_file'] = $filePath;
                }
                if (Schema::hasColumn('projects', 'tor_number')) {
                    $data['tor_number'] = $validated['doc_number'] ?: $project->tor_number;
                }
                if (!empty($data)) {
                    $project->update($data);
                }
            })(),
        };

        if (Schema::hasTable('project_documents')) {
            $docTitles = [
                'spk'         => 'Surat Perintah Kerja (SPK)',
                'contract'    => 'Kontrak Layanan Managed Service',
                'boq'         => 'Bill of Quantity (BOQ)',
                'ba'          => 'Berita Acara Serah Terima (BAST)',
                'project_doc' => 'Dokumen Teknis & Topologi Proyek',
                'tor'         => 'Terms of Reference (TOR)',
            ];
            ProjectDocument::updateOrCreate(
                [
                    'project_id'   => $project->id,
                    'document_key' => $validated['doc_type'],
                ],
                [
                    'document_title' => $docTitles[$validated['doc_type']] ?? 'Dokumen Proyek',
                    'file_path'      => $filePath,
                    'file_name'      => $file->getClientOriginalName(),
                    'file_size'      => $file->getSize() ?: 0,
                    'status'         => 'Uploaded',
                    'uploaded_by'    => auth()->id(),
                    'uploaded_at'    => now(),
                ]
            );
        }

        return redirect()->back()->with('success', 'Dokumen berhasil diunggah.');
    }

    /**
     * Perbarui Parameter SLA & Kontrak Managed Service
     */
    public function updateProjectSlaParams(Request $request, $id)
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'cm_quota'                  => 'required|integer|in:4,6,8,12,18,999',
            'pm_frequency_type'         => 'required|string|in:per_bulan,per_2_bulan,per_3_bulan,per_4_bulan,per_6_bulan,tahunan,custom',
            'pm_total_cycles'           => 'required|integer|min:1|max:120',
            'sla_target_percentage'     => 'required|numeric|min:80|max:100',
            'mttr_hours'                => 'required|integer|min:1|max:72',
            'sla_coverage_hours'        => 'required|string',
            'call_response_time'        => 'required|string',
            'incident_resolution_hours' => 'required|integer|min:1',
            'service_start_date'        => 'nullable|date',
            'service_end_date'          => 'nullable|date',
            'customer_pic_technical'    => 'nullable|string|max:255',
            'customer_pic_business'     => 'nullable|string|max:255',
            'customer_pic_finance'      => 'nullable|string|max:255',
            'sales_name'                => 'nullable|string|max:255',
            'presales_name'             => 'nullable|string|max:255',
        ]);

        $oldCycles = $project->pm_total_cycles;
        $project->update($validated);

        if ($oldCycles != $validated['pm_total_cycles']) {
            $existingCycles = ManagedServicePmCycle::where('project_id', $project->id)->orderBy('cycle_number')->get();
            if ($existingCycles->count() < $validated['pm_total_cycles']) {
                $startNum = $existingCycles->count() + 1;
                $lastDate = $existingCycles->last()?->scheduled_date ? Carbon::parse($existingCycles->last()->scheduled_date) : now();
                for ($i = $startNum; $i <= $validated['pm_total_cycles']; $i++) {
                    ManagedServicePmCycle::create([
                        'project_id'        => $project->id,
                        'cycle_number'      => $i,
                        'cycle_label'       => 'PM ' . $i,
                        'scheduled_date'    => $lastDate->copy()->addMonths(3)->toDateString(),
                        'activity_status'   => 'Scheduled',
                        'report_status'     => 'Pending',
                        'billing_ba_status' => 'Pending',
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Parameter Kontrak & SLA Managed Service berhasil diperbarui.');
    }

    /**
     * Halaman Monitoring Preventive Maintenance (PM)
     */
    public function monitoringPm(Request $request, $projectId = null)
    {
        return $this->monitoring($request, $projectId, 'pm');
    }

    /**
     * Halaman Monitoring Corrective Maintenance (CM)
     */
    public function monitoringCm(Request $request, $projectId = null)
    {
        return $this->monitoring($request, $projectId, 'cm');
    }

    /**
     * Halaman Monitoring PM & CM (Timeline, Siklus PM, dan Kuota CM)
     */
    public function monitoring(Request $request, $projectId = null, $tab = null)
    {
        $this->ensureTablesExist();
        $this->ensureSampleProjectExists();

        $activeTab = $tab ?: $request->input('tab', 'pm');
        if (!in_array($activeTab, ['documents', 'pm', 'cm', 'sla'])) {
            $activeTab = 'pm';
        }

        $selectedProjectId = $projectId ?: $request->input('project_id');
        if ($selectedProjectId) {
            $project = Project::find($selectedProjectId);
        } else {
            $project = Project::where(function($q) {
                $q->where('stage', 'Operate')
                  ->orWhere('ms_handover_status', 'Accepted')
                  ->orWhere('handover_target', 'managed_service')
                  ->orWhere('project_type', 'like', '%Managed%');
            })->whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])->latest()->first();
        }

        if ($project) {
            return redirect()->route('ms.projects.show', ['project' => $project->id, 'tab' => $activeTab]);
        }

        return redirect()->route('ms.projects.index');
    }

    /**
     * Tampilkan Berkas Laporan PM Asli
     */
    public function showPmReport($cycleId)
    {
        $cycle = ManagedServicePmCycle::with(['project', 'assignedEngineer'])->findOrFail($cycleId);
        $project = $cycle->project;

        $reportFile = $cycle->report_file ?: ($cycle->report_file_path ?? null);
        if ($reportFile) {
            $candidates = [
                storage_path('app/public/' . $reportFile),
                public_path('storage/' . $reportFile),
                base_path('../public_html/storage/' . $reportFile),
            ];
            foreach ($candidates as $filePath) {
                if (file_exists($filePath) && !is_dir($filePath)) {
                    return response()->file($filePath);
                }
            }
            return redirect(asset('storage/' . $reportFile));
        }

        return redirect()->route('ms.projects.show', ['project' => $project->id, 'tab' => 'pm'])
            ->with('error', 'Berkas dokumen laporan PM belum diunggah.');
    }

    /**
     * Update Siklus Preventive Maintenance (Activity, Laporan PM, BA / Penagihan)
     */
    public function updatePmCycle(Request $request, $cycleId)
    {
        $cycle = ManagedServicePmCycle::findOrFail($cycleId);

        $validated = $request->validate([
            'scheduled_date'       => 'nullable|date',
            'activity_status'      => 'required|string|in:Scheduled,In Progress,Completed',
            'actual_date'          => 'nullable|date',
            'activity_notes'       => 'nullable|string',
            'assigned_engineer_id' => 'nullable|exists:users,id',
            'report_status'        => 'required|string|in:Pending,Draft,Submitted,Approved',
            'billing_ba_status'    => 'required|string|in:Pending,Draft,Submitted,Signed,Invoiced',
            'report_file'          => 'nullable|file|max:51200',
            'billing_ba_file'      => 'nullable|file|max:51200',
        ]);

        if ($request->hasFile('report_file')) {
            $file = $request->file('report_file');
            $validated['report_file'] = FileUploadHelper::storePublicly($file, 'ms_pm_reports');
            $validated['report_filename'] = $file->getClientOriginalName();
            if ($validated['report_status'] === 'Pending') {
                $validated['report_status'] = 'Submitted';
            }
        }

        if ($request->hasFile('billing_ba_file')) {
            $file = $request->file('billing_ba_file');
            $validated['billing_ba_file'] = FileUploadHelper::storePublicly($file, 'ms_pm_billing');
            $validated['billing_ba_filename'] = $file->getClientOriginalName();
            if ($validated['billing_ba_status'] === 'Pending') {
                $validated['billing_ba_status'] = 'Submitted';
            }
        }

        if ($validated['activity_status'] === 'Completed' && empty($validated['actual_date'])) {
            $validated['actual_date'] = now()->toDateString();
        }

        $cycle->update($validated);

        // Otomatis sinkronisasi ke Jadwal Kerja Tim (Schedule)
        if (!empty($cycle->scheduled_date)) {
            self::syncPmCycleToSchedule($cycle);
        }

        $project = $cycle->project;
        if ($project) {
            $total = $project->pmCycles()->count();
            $completed = $project->pmCycles()->where('activity_status', 'Completed')->count();
            if ($total > 0) {
                $newProgress = round(($completed / $total) * 100);
                $project->update(['progress' => $newProgress]);
            }
        }

        return redirect()->back()->with('success', 'Data siklus ' . $cycle->cycle_label . ' berhasil diperbarui.');
    }

    /**
     * Catat Tiket Corrective Maintenance (CM Tracker)
     */
    public function storeCmTicket(Request $request)
    {
        $validated = $request->validate([
            'project_id'    => 'required|exists:projects,id',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'priority'      => 'required|string|in:Critical,High,Medium,Low,Platinum,Gold,Silver,Bronze',
            'status'        => 'nullable|string|in:Open,In Progress,Resolved,Closed',
            'assigned_to'   => 'nullable|exists:users,id',
            'incident_date' => 'nullable|date',
            'attachment'    => 'nullable|file|max:51200',
        ]);

        $project = Project::findOrFail($validated['project_id']);

        $quota = $project->cm_quota ?: 18;
        $usedCount = ManagedServiceTicket::where('project_id', $project->id)
            ->where(function($q) {
                $q->where('type', 'like', '%Corrective%')
                  ->orWhere('type', 'like', '%Incident%');
            })->count();

        if ($quota < 999 && $usedCount >= $quota) {
            return redirect()->back()->with('error', 'Kuota Corrective Maintenance (' . $quota . ' tiket) untuk proyek ini sudah habis.');
        }

        $ticketDate = !empty($validated['incident_date']) ? Carbon::parse($validated['incident_date']) : now();

        $projectCode = 'NET';
        if (!empty($project->client)) {
            if (stripos($project->client, 'Telkom') !== false) {
                $projectCode = 'TLK';
            } elseif (stripos($project->client, 'Angkasa Pura') !== false) {
                $projectCode = 'APS';
            } elseif (stripos($project->client, 'PLN') !== false) {
                $projectCode = 'PLN';
            } elseif (stripos($project->client, 'Bank Rakyat Indonesia') !== false || stripos($project->client, 'BRI') !== false) {
                $projectCode = 'BRI';
            } elseif (stripos($project->client, 'Mandiri') !== false) {
                $projectCode = 'MDR';
            }
        }

        $count = ManagedServiceTicket::where('project_id', $project->id)
            ->where(function($q) {
                $q->where('type', 'like', '%Corrective%')
                  ->orWhere('type', 'like', '%Incident%');
            })->count() + 1;

        $ticketNumber = 'CM-' . $projectCode . '-' . $ticketDate->year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = FileUploadHelper::storePublicly($request->file('attachment'), 'ms_cm_docs');
        }

        $normalizedPriority = match(strtolower($validated['priority'])) {
            'critical', 'platinum'      => 'Critical',
            'high', 'gold', 'major'     => 'High',
            'medium', 'silver', 'minor' => 'Medium',
            'low', 'bronze'             => 'Low',
            default                     => 'Medium',
        };

        $slaHours = match($normalizedPriority) {
            'Critical' => 1,
            'High'     => 4,
            'Medium'   => 8,
            'Low'      => 24,
            default    => 4,
        };

        $status = $validated['status'] ?? 'In Progress';
        $resolvedAt = in_array($status, ['Resolved', 'Closed']) ? $ticketDate->copy()->addHours(2) : null;

        $ticket = ManagedServiceTicket::create([
            'ticket_number'   => $ticketNumber,
            'project_id'      => $project->id,
            'client_name'     => $project->client,
            'title'           => $validated['title'],
            'description'     => $validated['description'] ?? 'Pekerjaan perbaikan insiden Corrective Maintenance.',
            'incident_date'   => $ticketDate->toDateString(),
            'type'            => 'Corrective Maintenance',
            'category'        => 'Troubleshooting',
            'priority'        => $normalizedPriority,
            'status'          => $status,
            'source'          => 'Portal',
            'reported_by'     => $project->customer_pic_technical ?: ($project->client ?: 'Klien'),
            'assigned_to'     => $validated['assigned_to'] ?: auth()->id(),
            'attachment_path' => $attachmentPath,
            'sla_deadline'    => $ticketDate->copy()->addHours($slaHours),
            'resolved_at'     => $resolvedAt,
            'sla_met'         => true,
            'created_by'      => auth()->id(),
        ]);

        self::syncTicketToTaskAndSchedule($ticket);

        return redirect()->back()->with('success', 'Tiket Corrective Maintenance ' . $ticketNumber . ' berhasil dicatat.');
    }

    /**
     * Update Data & Realisasi Tiket Corrective Maintenance (CM)
     */
    public function updateCmTicket(Request $request, $ticketId)
    {
        $ticket = ManagedServiceTicket::findOrFail($ticketId);

        $validated = $request->validate([
            'title'            => 'nullable|string|max:255',
            'status'           => 'required|string|in:Open,In Progress,Pending Vendor,Resolved,Closed',
            'priority'         => 'nullable|string|in:Critical,High,Medium,Low,Platinum,Gold,Silver,Bronze',
            'incident_date'    => 'nullable|date',
            'resolved_at'      => 'nullable|date',
            'assigned_to'      => 'nullable|exists:users,id',
            'reported_by'      => 'nullable|string|max:255',
            'contact_phone'    => 'nullable|string|max:50',
            'resolution_notes' => 'nullable|string',
            'root_cause'       => 'nullable|string|max:255',
            'attachment'       => 'nullable|file|max:51200',
            'bap_file'         => 'nullable|file|max:51200',
        ]);

        if (!empty($validated['title'])) {
            $ticket->title = $validated['title'];
        }

        if (!empty($validated['priority'])) {
            $ticket->priority = match(strtolower($validated['priority'])) {
                'critical', 'platinum'      => 'Critical',
                'high', 'gold', 'major'     => 'High',
                'medium', 'silver', 'minor' => 'Medium',
                'low', 'bronze'             => 'Low',
                default                     => 'Medium',
            };
        }

        if (!empty($validated['incident_date'])) {
            $ticket->incident_date = Carbon::parse($validated['incident_date']);
        }

        if (array_key_exists('assigned_to', $validated)) {
            $ticket->assigned_to = $validated['assigned_to'];
        }

        if (isset($validated['reported_by'])) {
            $ticket->reported_by = $validated['reported_by'];
        }

        if (isset($validated['contact_phone'])) {
            $ticket->contact_phone = $validated['contact_phone'];
        }

        if (isset($validated['resolution_notes'])) {
            $ticket->resolution_notes = $validated['resolution_notes'];
        }

        if (isset($validated['root_cause'])) {
            $ticket->root_cause = $validated['root_cause'];
        }

        $ticket->status = $validated['status'];

        if (in_array($validated['status'], ['Resolved', 'Closed'])) {
            if (!empty($validated['resolved_at'])) {
                $ticket->resolved_at = Carbon::parse($validated['resolved_at']);
            } elseif (!$ticket->resolved_at) {
                $ticket->resolved_at = now();
            }

            if ($ticket->sla_deadline && $ticket->resolved_at) {
                $ticket->sla_met = $ticket->resolved_at->lte($ticket->sla_deadline);
            }
        } else {
            if (empty($validated['resolved_at'])) {
                $ticket->resolved_at = null;
            }
        }

        $file = $request->file('attachment') ?: $request->file('bap_file');
        if ($file) {
            $ticket->attachment_path = FileUploadHelper::storePublicly($file, 'ms_cm_bap');
        }

        $ticket->save();
        self::syncTicketToTaskAndSchedule($ticket);

        return redirect()->back()->with('success', 'Data Tiket Corrective Maintenance ' . $ticket->ticket_number . ' berhasil diperbarui.');
    }

    /**
     * Tampilkan Berkas BAP CM Asli
     */
    public function showCmBap($ticketId)
    {
        $ticket = ManagedServiceTicket::with(['project', 'assignedEngineer', 'creator'])->findOrFail($ticketId);
        $project = $ticket->project;

        if ($ticket->attachment_path) {
            $candidates = [
                storage_path('app/public/' . $ticket->attachment_path),
                public_path('storage/' . $ticket->attachment_path),
                base_path('../public_html/storage/' . $ticket->attachment_path),
            ];
            foreach ($candidates as $filePath) {
                if (file_exists($filePath) && !is_dir($filePath)) {
                    return response()->file($filePath);
                }
            }
            return redirect(asset('storage/' . $ticket->attachment_path));
        }

        return redirect()->route('ms.projects.show', ['project' => $project->id, 'tab' => 'cm'])
            ->with('error', 'Berkas dokumen BAP belum diunggah.');
    }

    /**
     * Upload Berkas BAP Fisik / Tanda Tangan untuk Tiket CM
     */
    public function uploadCmBap(Request $request, $ticketId)
    {
        $ticket = ManagedServiceTicket::findOrFail($ticketId);

        $validated = $request->validate([
            'bap_file'         => 'required|file|max:51200',
            'status'           => 'nullable|string|in:In Progress,Resolved,Closed',
            'resolution_notes' => 'nullable|string',
            'root_cause'       => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('bap_file')) {
            $ticket->attachment_path = FileUploadHelper::storePublicly($request->file('bap_file'), 'ms_cm_bap');
        }

        if (!empty($validated['status'])) {
            $ticket->status = $validated['status'];
            if (in_array($validated['status'], ['Resolved', 'Closed']) && !$ticket->resolved_at) {
                $ticket->resolved_at = now();
                $ticket->sla_met = $ticket->sla_deadline ? now()->lte($ticket->sla_deadline) : true;
            }
        }

        if (!empty($validated['resolution_notes'])) {
            $ticket->resolution_notes = $validated['resolution_notes'];
        }

        if (!empty($validated['root_cause'])) {
            $ticket->root_cause = $validated['root_cause'];
        }

        $ticket->save();
        self::syncTicketToTaskAndSchedule($ticket);

        return redirect()->back()->with('success', 'Berkas BAP Tiket ' . $ticket->ticket_number . ' berhasil diunggah.');
    }

    /**
     * Catat Insiden Downtime Gangguan Perangkat Jaringan (SLA Availability Logger)
     */
    public function storeDowntimeLog(Request $request)
    {
        $this->ensureTablesExist();

        $validated = $request->validate([
            'client_name'      => 'required|string|max:255',
            'device_name'      => 'required|string|max:255',
            'device_category'  => 'required|string|max:100',
            'start_down'       => 'required|date',
            'end_down'         => 'nullable|date|after_or_equal:start_down',
            'root_cause'       => 'required|string|max:255',
            'resolution_notes' => 'nullable|string',
            'project_id'       => 'nullable|exists:projects,id',
            'priority'         => 'nullable|string',
        ]);

        $start = \Carbon\Carbon::parse($validated['start_down']);
        $end = !empty($validated['end_down']) ? \Carbon\Carbon::parse($validated['end_down']) : null;
        $status = $end ? 'Resolved' : 'In Progress';

        // Generate Ticket Number
        $yearMonth = date('Ym');
        $prefix = "DOW-{$yearMonth}-";
        $latest = ManagedServiceTicket::where('ticket_number', 'like', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        $nextSeq = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest->ticket_number, $matches)) {
            $nextSeq = intval($matches[1]) + 1;
        }
        $ticketNumber = $prefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

        ManagedServiceTicket::create([
            'ticket_number'    => $ticketNumber,
            'project_id'       => $validated['project_id'] ?? null,
            'client_name'      => $validated['client_name'],
            'title'            => "Gangguan [{$validated['device_category']}]: {$validated['device_name']}",
            'description'      => "Pencatatan gangguan perangkat untuk pemantauan SLA Uptime. Penyebab: {$validated['root_cause']}",
            'incident_date'    => $start,
            'resolved_at'      => $end,
            'type'             => 'Incident',
            'category'         => $validated['device_category'],
            'priority'         => $validated['priority'] ?? 'P1 - Critical',
            'status'           => $status,
            'source'           => 'Monitoring / NOC',
            'root_cause'       => $validated['root_cause'],
            'resolution_notes' => $validated['resolution_notes'] ?? 'Pemulihan layanan operasional perangkat',
            'created_by'       => auth()->id(),
            'sla_met'          => true,
            'sla_deadline'     => $start->copy()->addHours(4),
        ]);

        return redirect()->route('ms.dashboard')->with('success', "Insiden downtime perangkat {$validated['device_name']} ({$ticketNumber}) berhasil dicatat & masuk ke perhitungan SLA.");
    }

    /**
     * Unduh Template / Ekspor Laporan Perhitungan SLA Availability (Format Excel CSV)
     */
    public function exportSlaReport(Request $request)
    {
        $selectedMonthSla = (int) $request->input('sla_month', now()->month);
        $selectedYearSla  = (int) $request->input('sla_year', now()->year);
        $coverageMode     = $request->input('coverage_mode', '24x7');
        $targetSla        = (float) $request->input('target_sla', 99.50);

        $daysInMonth = \Carbon\Carbon::createFromDate($selectedYearSla, $selectedMonthSla, 1)->daysInMonth;
        $totalPeriodHours = ($coverageMode === '8x5') ? (22 * 8) : ($daysInMonth * 24);

        $incidentTickets = ManagedServiceTicket::whereYear('incident_date', $selectedYearSla)
            ->whereMonth('incident_date', $selectedMonthSla)
            ->get();

        $rows = [];
        $no = 1;
        $totalDown = 0;

        foreach ($incidentTickets as $t) {
            $start = \Carbon\Carbon::parse($t->incident_date);
            $end = $t->resolved_at ? \Carbon\Carbon::parse($t->resolved_at) : now();
            $diffHours = round(max(1, $start->diffInMinutes($end)) / 60, 2);
            $totalDown += $diffHours;
            $devSla = max(0, min(100, round((($totalPeriodHours - $diffHours) / $totalPeriodHours) * 100, 2)));

            $rows[] = [
                $no++,
                $t->ticket_number,
                $t->client_name,
                $t->category ?: 'Perangkat Jaringan',
                $t->title,
                $start->format('d/m/Y H:i'),
                $t->resolved_at ? $end->format('d/m/Y H:i') : 'Masih Down',
                $diffHours,
                $devSla . '%',
                $t->root_cause ?: '-',
                $t->status,
            ];
        }

        if (empty($rows)) {
            $rows[] = [1, 'TCK-SLA-001', 'Klien Korporat', 'Router', 'Router Core Cisco ISR 4331', '04/11/2026 08:00', '04/11/2026 10:00', '2.00', '99.72%', 'Power supply flap & buffer overload', 'Resolved'];
            $rows[] = [2, 'TCK-SLA-002', 'Klien Korporat', 'Switch', 'Switch Distribution Catalyst 2960X', '06/11/2026 09:00', '06/11/2026 10:30', '1.50', '99.79%', 'FO patchcord kotor & redaman naik', 'Resolved'];
            $rows[] = [3, 'TCK-SLA-003', 'Klien Korporat', 'Firewall', 'Firewall Perimeter FortiGate 100F', '09/11/2026 13:00', '09/11/2026 14:30', '1.50', '99.79%', 'ISP WAN link drop & timeout', 'Resolved'];
            $totalDown = 5.00;
        }

        $overallSla = max(0, min(100, round((($totalPeriodHours - $totalDown) / $totalPeriodHours) * 100, 2)));
        $maxAllowed = round($totalPeriodHours * (1 - ($targetSla / 100)), 2);

        $filename = "Laporan_Perhitungan_SLA_Availability_IPNET_{$selectedYearSla}_{$selectedMonthSla}.csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($rows, $totalPeriodHours, $totalDown, $overallSla, $targetSla, $maxAllowed, $coverageMode, $selectedMonthSla, $selectedYearSla) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel
            fputcsv($handle, ['PT IP NETWORK SOLUSINDO - LAPORAN PERHITUNGAN AVAILABILITY SLA']);
            fputcsv($handle, ['Periode Operasional:', "Bulan {$selectedMonthSla}/{$selectedYearSla} ({$coverageMode})"]);
            fputcsv($handle, ['Total Waktu Periode:', "{$totalPeriodHours} Jam"]);
            fputcsv($handle, ['Target Kontrak SLA:', "{$targetSla}%"]);
            fputcsv($handle, ['Batas Maksimal Downtime Diizinkan:', "{$maxAllowed} Jam"]);
            fputcsv($handle, ['Total Akumulasi Downtime:', "{$totalDown} Jam"]);
            fputcsv($handle, ['Realisasi SLA Uptime:', "{$overallSla}%"]);
            fputcsv($handle, ['Status Kepatuhan:', $overallSla >= $targetSla ? 'MEMENUHI TARGET (COMPLIANT)' : 'TERLAMPAUI (BREACHED)']);
            fputcsv($handle, []);
            fputcsv($handle, ['No', 'Nomor Tiket', 'Klien', 'Kategori Perangkat', 'Nama Perangkat / Insiden', 'Mulai Gangguan (Down)', 'Waktu Pulih (Up)', 'Durasi Down (Jam)', 'Realisasi SLA (%)', 'Penyebab (Root Cause)', 'Status']);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['', '', '', '', 'TOTAL DOWNTIME', '', '', $totalDown . ' Jam', $overallSla . '%', '', '']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
