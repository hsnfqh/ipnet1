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
        $criticalTickets = $tickets->where('priority', 'P1 - Critical')->whereIn('status', ['Open', 'In Progress'])->count();
        $resolvedTickets = $tickets->whereIn('status', ['Resolved', 'Closed'])->count();

        $totalSlaChecked = $tickets->whereNotNull('resolved_at')->count();
        $slaMetCount     = $tickets->where('sla_met', true)->whereNotNull('resolved_at')->count();
        $slaScore        = $totalSlaChecked > 0 ? round(($slaMetCount / $totalSlaChecked) * 100, 1) : 0;

        // Proyek di Tahap Operate / Managed Service (Selesai Serah Terima atau Closed Won & In Progress)
        $operateProjects = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
            ->where(function($q) {
                $q->where('stage', 'Operate')
                  ->orWhere('sales_stage', 'Closed Won')
                  ->orWhere('status', 'In Progress')
                  ->orWhereIn('handover_target', ['managed_service', 'both'])
                  ->orWhere('name', 'like', '%pemeliharaan%')
                  ->orWhere('name', 'like', '%maintenance%')
                  ->orWhere('name', 'like', '%managed service%');
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
            $criticalTicketsCount = $clientTickets->where('priority', 'P1 - Critical')->whereIn('status', ['Open', 'In Progress'])->count();

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

            return [
                'id'               => $project->id,
                'name'             => $clientName,
                'projectName'      => $project->name,
                'sla_tier'         => $project->sla_tier ?: 'Gold',
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

        // 8. Incoming Service Handovers (Dari Sales Tipe 2 & Pasca-Implementasi PMO)
        $incomingHandovers = Project::where(function($q) {
                $q->where('stage', 'Operate')
                  ->orWhere('handover_target', 'managed_service')
                  ->orWhere('project_type', 'like', '%Managed Service%')
                  ->orWhere('name', 'like', '%pemeliharaan%')
                  ->orWhere('name', 'like', '%maintenance%');
            })
            ->whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
            ->with(['creator', 'commercialHandoverBy', 'pm', 'msAcceptedBy'])
            ->latest('updated_at')
            ->get();

        $pendingHandoversCount = $incomingHandovers->where('ms_handover_status', '!=', 'Accepted')->count();

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
            'selectedClient'
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
                'category'    => 'Preventive Maintenance',
                'status'      => $taskStatus === 'Completed' ? 'Completed' : ($taskStatus === 'In Progress' ? 'In Progress' : 'Pending'),
                'description' => "Penanganan Tiket SLA {$ticket->ticket_number} - {$ticket->client_name}\nStatus: {$ticket->status}",
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
        ]);

        $project->update([
            'ms_handover_status'    => 'Accepted',
            'ms_accepted_at'        => now(),
            'ms_accepted_by'        => auth()->id(),
            'sla_tier'              => $validated['sla_tier'],
            'maintenance_frequency' => $validated['maintenance_frequency'] ?? ($project->maintenance_frequency ?: 'Monthly'),
            'sla_coverage_hours'    => $validated['sla_coverage_hours'] ?? ($project->sla_coverage_hours ?: '24x7'),
            'service_start_date'    => $validated['service_start_date'] ?? $project->service_start_date,
            'service_end_date'      => $validated['service_end_date'] ?? $project->service_end_date,
            'special_notes'         => $validated['special_notes'] ?? $project->special_notes,
            'stage'                 => 'Operate',
            'status'                => 'Active',
        ]);

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

        $totalCycles = $project->pm_total_cycles ?: 4;
        $startDate = $project->service_start_date ? Carbon::parse($project->service_start_date) : ($project->start_date ? Carbon::parse($project->start_date) : now());
        $monthsInterval = match($project->pm_frequency_type) {
            'per_bulan'   => 1,
            'per_2_bulan' => 2,
            'per_3_bulan' => 3,
            'per_4_bulan' => 4,
            default       => 3,
        };

        for ($i = 1; $i <= $totalCycles; $i++) {
            $schedDate = $startDate->copy()->addMonths(($i - 1) * $monthsInterval + $monthsInterval);
            $isFirstCompleted = ($i === 1) && ($project->progress >= 25);
            ManagedServicePmCycle::create([
                'project_id'        => $project->id,
                'cycle_number'      => $i,
                'cycle_label'       => 'PM ' . $i . ' (Bulan ke-' . (($i - 1) * $monthsInterval + $monthsInterval) . ')',
                'scheduled_date'    => $schedDate->toDateString(),
                'activity_status'   => $isFirstCompleted ? 'Completed' : 'Scheduled',
                'actual_date'       => $isFirstCompleted ? $schedDate->copy()->subDays(2)->toDateString() : null,
                'activity_notes'    => $isFirstCompleted ? 'Preventive maintenance berkala Q1 selesai dilaksanakan sesuai checklist inspeksi.' : null,
                'report_status'     => $isFirstCompleted ? 'Approved' : 'Pending',
                'billing_ba_status' => $isFirstCompleted ? 'Signed' : 'Pending',
            ]);
        }
    }

    /**
     * Pastikan minimal ada 1 proyek sampel (Bank Raya sesuai sketsa user) jika database kosong
     */
    private function ensureSampleProjectExists(): void
    {
        if (Project::count() === 0) {
            $creatorId = auth()->id() ?: User::first()?->id ?: 1;
            $sampleProject = Project::create([
                'name'                      => 'Layanan Managed Service & Pemeliharaan Jaringan Bank Raya',
                'client'                    => 'Bank Raya',
                'sales_name'                => 'Raiza',
                'presales_name'             => 'Akbar',
                'location'                  => 'Menara BRI Lt. 12, Jakarta Selatan',
                'project_type'              => 'Managed Service SLA',
                'sla_tier'                  => 'Platinum',
                'stage'                     => 'Operate',
                'status'                    => 'Active',
                'progress'                  => 25,
                'contract_value'            => 450000000,
                'start_date'                => now()->subMonths(3)->toDateString(),
                'deadline'                  => now()->addMonths(9)->toDateString(),
                'service_start_date'        => now()->subMonths(3)->toDateString(),
                'service_end_date'          => now()->addMonths(9)->toDateString(),
                'po_spk_number'             => 'SPK/IPNET/BRY/2026/014',
                'po_spk_date'               => now()->subMonths(3)->toDateString(),
                'sla_commitment'            => '99.5% Uptime, 24x7 Standby Support, MTTR < 4 Jam',
                'sla_coverage_hours'        => '24x7',
                'maintenance_frequency'     => 'Quarterly',
                'pm_frequency_type'         => 'per_3_bulan',
                'pm_total_cycles'           => 4,
                'cm_quota'                  => 18,
                'mttr_hours'                => 4,
                'call_response_time'        => '15 menit',
                'incident_resolution_hours' => 4,
                'sla_target_percentage'     => 99.50,
                'customer_pic_technical'    => 'Bpk. Budi Santoso (Head of IT Ops - 081234567890)',
                'customer_pic_business'     => 'Ibu Diana Putri (Procurement - 081298765432)',
                'customer_pic_finance'      => 'Bpk. Hendra Gunawan (Finance)',
                'created_by'                => $creatorId,
            ]);

            self::initPmCycles($sampleProject);

            ManagedServiceTicket::create([
                'ticket_number'    => 'CM-2026-001',
                'project_id'       => $sampleProject->id,
                'client_name'      => 'Bank Raya',
                'title'            => 'Kegiatan Upgrade OS & Patching Security Core Switch',
                'description'      => 'Upgrade firmware NOS dan verifikasi redundansi link failover.',
                'incident_date'    => now()->subWeeks(2)->toDateString(),
                'type'             => 'Corrective Maintenance',
                'category'         => 'Switch',
                'priority'         => 'Gold',
                'status'           => 'Resolved',
                'source'           => 'Portal',
                'reported_by'      => 'Budi Santoso',
                'contact_phone'    => '081234567890',
                'assigned_to'      => $creatorId,
                'resolved_at'      => now()->subWeeks(2)->addHours(2),
                'sla_met'          => true,
                'resolution_notes' => 'Upgrade OS selesai tanpa downtime transaksi nasabah.',
                'created_by'       => $creatorId,
            ]);
        }
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

        $query = Project::with(['creator', 'pmCycles', 'managedServiceTickets'])
            ->whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
            ->where(function($q) {
                $q->where('stage', 'Operate')
                  ->orWhere('sales_stage', 'Closed Won')
                  ->orWhere('status', 'In Progress')
                  ->orWhereIn('handover_target', ['managed_service', 'both'])
                  ->orWhere('name', 'like', '%pemeliharaan%')
                  ->orWhere('name', 'like', '%maintenance%')
                  ->orWhere('name', 'like', '%managed service%');
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

        return view('managed_service.projects', compact('projects', 'perPage'));
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

        // Helper untuk mencocokkan dokumen dari tabel project_documents
        $findDoc = function(array $keys, array $keywords = [], array $types = []) use ($projectDocs) {
            return $projectDocs->first(function ($doc) use ($keys, $keywords, $types) {
                $docKey   = strtoupper($doc->document_key ?? '');
                $docType  = strtoupper($doc->document_type ?? '');
                $docTitle = strtolower($doc->document_title ?? ($doc->name ?? ''));
                $fileName = strtolower($doc->file_name ?? '');

                foreach ($keys as $k) {
                    $kUpper = strtoupper($k);
                    if ($docKey === $kUpper || $docKey === 'LAMPIRAN_' . $kUpper || str_starts_with($docKey, 'DOC_' . $kUpper)) {
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

        // 1. SPK / PO
        $spkDoc = $findDoc(
            ['spk', 'po', 'contract_po_so'],
            ['perintah kerja', 'purchase order', 'spk', 'po_'],
            ['SPK', 'PO']
        );
        $spkFilePath = $project->po_spk_file ?: ($project->po_file ?: ($spkDoc?->file_path));
        $spkNumber   = $project->po_spk_number ?: ($project->po_number ?: ($spkDoc ? ($spkDoc->notes ?? ('SPK-' . $project->id)) : null));
        $spkDate     = $project->po_spk_date ?: ($spkDoc?->created_at?->format('Y-m-d') ?: $project->start_date);

        // 2. Kontrak / PKS
        $contractDoc = $findDoc(
            ['contract', 'pks', 'commercial_handover_package'],
            ['kontrak', 'perjanjian', 'contract', 'pks'],
            ['CONTRACT', 'PKS']
        ) ?: ($spkDoc && (in_array(strtoupper($spkDoc->document_key ?? ''), ['CONTRACT_PO_SO', 'COMMERCIAL_HANDOVER_PACKAGE']) || str_contains(strtolower($spkDoc->document_title ?? ''), 'contract') || str_contains(strtolower($spkDoc->document_title ?? ''), 'kontrak')) ? $spkDoc : null);

        $contractFilePath = $project->contract_file ?: ($project->po_file ?: ($contractDoc?->file_path ?: ($project->po_spk_file ?? null)));
        $contractNumber   = $project->po_number ?: ($contractDoc ? ('KTR/' . $project->id) : ('KTR/' . $project->id));
        $contractDate     = $contractDoc?->created_at?->format('Y-m-d') ?: ($project->service_start_date ?: $project->start_date);

        // 3. TOR
        $torDoc = $findDoc(
            ['tor', 'kak'],
            ['terms of reference', 'tor', 'kak', 'kerangka acuan'],
            ['TOR', 'KAK']
        );
        $torFilePath = $project->tor_file ?: ($torDoc?->file_path);
        $torNumber   = $project->tor_number ?: ($torDoc ? ('TOR-' . $project->id) : null);
        $torDate     = $torDoc?->created_at?->format('Y-m-d') ?: $project->start_date;

        // 4. BOQ / BOM
        $boqDoc = $findDoc(
            ['boq', 'bom'],
            ['boq', 'bom', 'bill of quantity', 'built of material'],
            ['BOQ', 'BOM']
        );
        $boqFilePath = $project->boq_file ?: ($project->bom_file ?? null ?: ($boqDoc?->file_path));
        $boqNumber   = 'BOQ-' . ($project->po_spk_number ?: $project->id);
        $boqDate     = $boqDoc?->created_at?->format('Y-m-d') ?: $project->start_date;

        // 5. BA / BAST / BALAP
        $baDoc = $findDoc(
            ['ba', 'bast', 'balap', 'bap'],
            ['serah terima', 'berita acara', 'bast', 'balap', 'bap'],
            ['BA', 'BAST', 'BALAP', 'BAP']
        );
        $baFilePath = $project->bast_file ?: ($project->ba_file ?? null ?: ($baDoc?->file_path));
        $baNumber   = 'BAST/' . $project->id;
        $baDate     = $baDoc?->created_at?->format('Y-m-d') ?: ($project->service_start_date ?: $project->start_date);

        // 6. Dokumen Teknis & Topologi / Proposal
        $techDoc = $findDoc(
            ['project_doc', 'technical_proposal', 'commercial_proposal', 'customer_requirement', 'PRP', 'PRJ', 'BRD'],
            ['proposal', 'topologi', 'arsitektur', 'teknis', 'desain', 'diagram', 'sizing'],
            ['PRP', 'PRJ', 'TOPOLOGY']
        ) ?: $projectDocs->first(fn($d) => in_array($d->stage_name ?? '', ['Solution', 'Presales']));

        $techFilePath = $project->proposal_file ?: ($project->technical_file ?? null ?: ($project->topology_file ?? null ?: ($techDoc?->file_path)));
        $techNumber   = 'DOC-' . $project->id;
        $techDate     = $techDoc?->created_at?->format('Y-m-d') ?: null;

        $documents = [
            'spk' => [
                'key'         => 'spk',
                'title'       => 'Surat Perintah Kerja (SPK / PO)',
                'description' => 'Dokumen acuan perintah kerja resmi dari klien.',
                'file_path'   => $spkFilePath,
                'number'      => $spkNumber,
                'date'        => $spkDate,
            ],
            'contract' => [
                'key'         => 'contract',
                'title'       => 'Kontrak Perjanjian Layanan (PKS / Contract)',
                'description' => 'Perjanjian kerja sama SLA dan pemeliharaan jangka panjang.',
                'file_path'   => $contractFilePath,
                'number'      => $contractNumber,
                'date'        => $contractDate,
            ],
            'tor' => [
                'key'         => 'tor',
                'title'       => 'Terms of Reference (TOR)',
                'description' => 'Kerangka acuan kerja, ruang lingkup & spesifikasi teknis layanan.',
                'file_path'   => $torFilePath,
                'number'      => $torNumber,
                'date'        => $torDate,
            ],
            'boq' => [
                'key'         => 'boq',
                'title'       => 'Bill of Quantity (BOQ & Spesifikasi Layanan)',
                'description' => 'Daftar rincian perangkat keras, lisensi software, dan alokasi mandays.',
                'file_path'   => $boqFilePath,
                'number'      => $boqNumber,
                'date'        => $boqDate,
            ],
            'ba' => [
                'key'         => 'ba',
                'title'       => 'Berita Acara Serah Terima (BA / BAST / BAP)',
                'description' => 'Bukti serah terima operasional dan milestone penagihan berkala.',
                'file_path'   => $baFilePath,
                'number'      => $baNumber,
                'date'        => $baDate,
            ],
            'project_doc' => [
                'key'         => 'project_doc',
                'title'       => 'Dokumen Teknis & Topologi Proyek',
                'description' => 'Diagram jaringan, desain arsitektur, IP addressing, dan NDA.',
                'file_path'   => $techFilePath,
                'number'      => $techNumber,
                'date'        => $techDate,
            ],
        ];

        // Kumpulkan berkas dokumen tambahan dari Sales / Presales / ProjectDocument yang belum dipakai di 6 slot utama
        $usedPaths = array_filter(array_column($documents, 'file_path'));
        $additionalDocs = $projectDocs->reject(function($doc) use ($usedPaths) {
            return in_array($doc->file_path, $usedPaths);
        })->values();

        $availableDocCount = count(array_filter($documents, fn($d) => !empty($d['file_path']))) + $additionalDocs->count();

        $cmTickets = $project->managedServiceTickets()
            ->where(function($q) {
                $q->where('type', 'like', '%Corrective%')
                  ->orWhere('type', 'like', '%Incident%');
            })->latest()->get();
        $cmQuota = $project->cm_quota ?: 18;
        $cmUsed = $cmTickets->count();
        $cmRemaining = max(0, $cmQuota - $cmUsed);

        $pmCycles = $project->pmCycles()->orderBy('cycle_number')->get();
        $pmCompleted = $pmCycles->where('activity_status', 'Completed')->count();
        $pmTotal = $pmCycles->count() ?: ($project->pm_total_cycles ?: 4);
        $pmProgress = $pmTotal > 0 ? round(($pmCompleted / $pmTotal) * 100) : 0;

        $activeTab = $request->input('tab', 'documents');
        if (!in_array($activeTab, ['documents', 'pm', 'cm'])) {
            $activeTab = 'documents';
        }

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
            'pmCycles', 'pmCompleted', 'pmTotal', 'pmProgress', 'engineers', 'activeTab'
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
            'pm_frequency_type'         => 'required|string|in:per_bulan,per_2_bulan,per_3_bulan,per_4_bulan,custom',
            'pm_total_cycles'           => 'required|integer|min:1|max:24',
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
            $project = Project::where('stage', 'Operate')
                ->orWhere('project_type', 'like', '%Managed%')
                ->latest()
                ->first() ?: Project::whereNotIn('name', ['DAY OFF', 'Day Off'])->first();
        }

        if ($project) {
            return redirect()->route('ms.projects.show', ['project' => $project->id, 'tab' => $activeTab]);
        }

        return redirect()->route('ms.projects.index');
    }

    /**
     * Update Siklus Preventive Maintenance (Activity, Laporan PM, BA / Penagihan)
     */
    public function updatePmCycle(Request $request, $cycleId)
    {
        $cycle = ManagedServicePmCycle::findOrFail($cycleId);

        $validated = $request->validate([
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
            'priority'      => 'required|string|in:Platinum,Gold,Silver,Bronze',
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
        $count = ManagedServiceTicket::whereYear('created_at', $ticketDate->year)->count() + 1;
        $ticketNumber = 'CM-' . $ticketDate->year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = FileUploadHelper::storePublicly($request->file('attachment'), 'ms_cm_docs');
        }

        $slaHours = match($validated['priority']) {
            'Platinum' => 1,
            'Gold'     => 4,
            'Silver'   => 8,
            'Bronze'   => 24,
            default    => 4,
        };

        $ticket = ManagedServiceTicket::create([
            'ticket_number'   => $ticketNumber,
            'project_id'      => $project->id,
            'client_name'     => $project->client,
            'title'           => $validated['title'],
            'description'     => $validated['description'] ?? 'Pekerjaan perbaikan insiden Corrective Maintenance.',
            'incident_date'   => $ticketDate->toDateString(),
            'type'            => 'Corrective Maintenance',
            'category'        => 'Troubleshooting',
            'priority'        => $validated['priority'],
            'status'          => 'In Progress',
            'source'          => 'Portal',
            'reported_by'     => $project->customer_pic_technical ?: ($project->client ?: 'Klien'),
            'assigned_to'     => $validated['assigned_to'] ?: auth()->id(),
            'attachment_path' => $attachmentPath,
            'sla_deadline'    => $ticketDate->copy()->addHours($slaHours),
            'created_by'      => auth()->id(),
        ]);

        self::syncTicketToTaskAndSchedule($ticket);

        return redirect()->back()->with('success', 'Tiket Corrective Maintenance ' . $ticketNumber . ' berhasil dicatat.');
    }
}

