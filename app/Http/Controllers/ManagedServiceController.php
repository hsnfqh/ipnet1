<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ManagedServiceAsset;
use App\Models\ManagedServiceTicket;
use App\Models\ManagedServiceReport;
use App\Models\Project;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Client;
use App\Models\Task;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ManagedServiceController extends Controller
{
    private function ensureTablesExist(): void
    {
        if (!Schema::hasTable('managed_service_assets') || !Schema::hasTable('managed_service_tickets') || !Schema::hasTable('managed_service_reports')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                Log::warning('Auto migrate managed service tables: ' . $e->getMessage());
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
        $slaScore        = $totalSlaChecked > 0 ? round(($slaMetCount / $totalSlaChecked) * 100, 1) : 99.8;

        // Proyek di Tahap Operate / Managed Service
        $hasStage = Schema::hasColumn('projects', 'stage');
        $hasProjectType = Schema::hasColumn('projects', 'project_type');

        $operateProjects = Project::where(function($q) use ($hasStage, $hasProjectType) {
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
        })->whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])->get();

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
              ->orWhereHas('roles', fn($r) => $r->whereIn('name', ['Lead Maintenance', 'Maintenance']));
        })->whereDoesntHave('roles', fn($r) => $r->whereIn('name', ['Lead Engineer', 'Team Leader']))
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
            ['name' => 'Incident', 'count' => $tickets->where('type', 'Incident')->count() ?: 1, 'color' => '#EF4444'],
            ['name' => 'Service Request', 'count' => $tickets->where('type', 'Service Request')->count() ?: 1, 'color' => '#3B82F6'],
            ['name' => 'Change Request', 'count' => $tickets->where('type', 'Change Request')->count() ?: 1, 'color' => '#8B5CF6'],
            ['name' => 'Preventive Maint', 'count' => $totalPmSchedulesCount ?: 2, 'color' => '#10B981'],
        ];

        // SLA Compliance Scores per Client (Top 5)
        $clientSlaData = $operateProjects->take(5)->map(function($p) {
            return [
                'name'     => \Illuminate\Support\Str::limit($p->client ?: $p->name, 16),
                'fullName' => $p->client ?: $p->name,
                'score'    => 99.8,
                'tier'     => $p->sla_tier ?: 'Gold',
            ];
        });

        if ($clientSlaData->isEmpty()) {
            $clientSlaData = collect([
                ['name' => 'Bank Mandiri', 'fullName' => 'Bank Mandiri (Persero) Tbk', 'score' => 99.9, 'tier' => 'Platinum'],
                ['name' => 'PT Telkom', 'fullName' => 'PT Telkom Indonesia', 'score' => 99.5, 'tier' => 'Platinum'],
                ['name' => 'Kemenkeu RI', 'fullName' => 'Kementerian Keuangan RI', 'score' => 99.2, 'tier' => 'Gold'],
                ['name' => 'PT Astra Int', 'fullName' => 'PT Astra International Tbk', 'score' => 98.8, 'tier' => 'Gold'],
                ['name' => 'BCA Syariah', 'fullName' => 'PT Bank BCA Syariah', 'score' => 99.4, 'tier' => 'Silver'],
            ]);
        }

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
            'operateProjects',
            'tierCounts',
            'clients',
            'maintenanceEngineers',
            'upcomingPmSchedules',
            'maintenanceLoadMonthData',
            'maintenanceLoadWeekData',
            'totalPmSchedulesCount',
            'ticketTypeData',
            'clientSlaData',
            'selectedYear',
            'selectedClient'
        ));
    }

    /**
     * Asset & Configuration Items (CI) Management
     */
    public function assets(Request $request)
    {
        $this->ensureTablesExist();

        if (!Schema::hasTable('managed_service_assets')) {
            $assets = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $clients = $this->getClients();
            $projects = Project::whereNotIn('name', ['DAY OFF', 'Day Off'])->orderBy('name')->get();
            return view('managed_service.assets', compact('assets', 'clients', 'projects'));
        }

        $query = ManagedServiceAsset::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('device_name', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
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

        $assets = $query->latest()->paginate(15)->withQueryString();
        $clients = $this->getClients();
        $projects = Project::whereNotIn('name', ['DAY OFF', 'Day Off'])->orderBy('name')->get();

        return view('managed_service.assets', compact('assets', 'clients', 'projects'));
    }

    public function storeAsset(Request $request)
    {
        $validated = $request->validate([
            'client_name'   => 'required|string|max:255',
            'device_name'   => 'required|string|max:255',
            'category'      => 'required|string|in:Router,Switch,Firewall,Server,Access Point,UPS,Storage,Other',
            'brand'         => 'nullable|string|max:100',
            'model'         => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'ip_address'    => 'nullable|string|max:50',
            'location_site' => 'nullable|string|max:255',
            'rack_position' => 'nullable|string|max:100',
            'status'        => 'required|string|in:Online,Warning,Offline,Maintenance',
            'warranty_expiry'=> 'nullable|date',
            'notes'         => 'nullable|string',
            'project_id'    => 'nullable|exists:projects,id',
        ]);

        $validated['created_by'] = auth()->id();
        ManagedServiceAsset::create($validated);

        return redirect()->route('ms.assets.index')->with('success', 'Aset Configuration Item (CI) berhasil didaftarkan.');
    }

    public function updateAsset(Request $request, $id)
    {
        $asset = ManagedServiceAsset::find($id);
        if (!$asset) {
            return redirect()->route('ms.assets.index')->with('info', 'Aset tidak ditemukan atau sudah dihapus.');
        }

        $validated = $request->validate([
            'client_name'   => 'required|string|max:255',
            'device_name'   => 'required|string|max:255',
            'category'      => 'required|string|in:Router,Switch,Firewall,Server,Access Point,UPS,Storage,Other',
            'brand'         => 'nullable|string|max:100',
            'model'         => 'nullable|string|max:100',
            'serial_number' => 'nullable|string|max:100',
            'ip_address'    => 'nullable|string|max:50',
            'location_site' => 'nullable|string|max:255',
            'rack_position' => 'nullable|string|max:100',
            'status'        => 'required|string|in:Online,Warning,Offline,Maintenance',
            'warranty_expiry'=> 'nullable|date',
            'notes'         => 'nullable|string',
            'project_id'    => 'nullable|exists:projects,id',
        ]);

        $asset->update($validated);

        return redirect()->route('ms.assets.index')->with('success', 'Data Aset berhasil diperbarui.');
    }

    public function destroyAsset($id)
    {
        $asset = ManagedServiceAsset::find($id);
        if (!$asset) {
            return redirect()->route('ms.assets.index')->with('info', 'Aset sudah tidak ada atau telah dihapus sebelumnya.');
        }

        $asset->delete();
        return redirect()->route('ms.assets.index')->with('success', 'Aset berhasil dihapus.');
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
            $query->where('type', $request->type);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority') && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->latest()->paginate(15)->withQueryString();
        $assets  = ManagedServiceAsset::orderBy('device_name')->get();
        $projects = Project::whereNotIn('name', ['DAY OFF', 'Day Off'])->orderBy('name')->get();
        $engineers = User::where(function($q) {
            $q->where('division_id', 3)
              ->orWhereHas('roles', fn($r) => $r->whereIn('name', ['Lead Maintenance', 'Maintenance']));
        })->whereDoesntHave('roles', fn($r) => $r->whereIn('name', ['Lead Engineer', 'Team Leader']))
          ->get(['id', 'name']);

        return view('managed_service.tickets', compact('tickets', 'assets', 'projects', 'engineers'));
    }

    public function storeTicket(Request $request)
    {
        $validated = $request->validate([
            'client_name'   => 'required|string|max:255',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'type'          => 'required|string|in:Incident,Service Request,Change Request',
            'priority'      => 'nullable|string|in:Platinum,Gold,Silver,Bronze,P1 - Critical,P2 - Major,P3 - Minor,P4 - Low',
            'created_at'    => 'nullable|date',
            'reported_by'   => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'assigned_to'   => 'nullable|exists:users,id',
            'asset_id'      => 'nullable|exists:managed_service_assets,id',
        ]);

        if (empty($validated['priority'])) {
            $validated['priority'] = 'Gold';
        }

        $ticketDate = !empty($validated['created_at']) ? \Carbon\Carbon::parse($validated['created_at']) : now();
        $validated['created_at'] = $ticketDate;

        // Generate Ticket Number
        $prefix = match($validated['type']) {
            'Incident'        => 'INC',
            'Service Request' => 'REQ',
            'Change Request'  => 'CR',
            default           => 'TCK',
        };
        $count = ManagedServiceTicket::whereYear('created_at', $ticketDate->year)->count() + 1;
        $validated['ticket_number'] = $prefix . '-' . $ticketDate->year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'Open';

        // Set SLA Deadline
        $slaHours = match($validated['priority']) {
            'Platinum', 'P1 - Critical' => 1,
            'Gold', 'P2 - Major'       => 4,
            'Silver', 'P3 - Minor'     => 8,
            'Bronze', 'P4 - Low'       => 24,
            default                    => 4,
        };
        $validated['sla_deadline'] = $ticketDate->copy()->addHours($slaHours);

        ManagedServiceTicket::create($validated);
        $createdTicket = ManagedServiceTicket::where('ticket_number', $validated['ticket_number'])->first();
        if ($createdTicket) {
            self::syncTicketToTaskAndSchedule($createdTicket);
        }

        return redirect()->route('ms.tickets.index')->with('success', 'Tiket ' . $validated['ticket_number'] . ' berhasil dibuat.');
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

        // Sinkronisasi ke Schedule (Jadwal Kerja) - Kategori Maintenance (Bukan Proyek Delivery)
        $scheduleDate = $ticket->created_at ? $ticket->created_at->format('Y-m-d') : now()->format('Y-m-d');
        $schedule = Schedule::updateOrCreate(
            [
                'title'      => $taskTitle,
            ],
            [
                'date'        => $scheduleDate,
                'start_time'  => '09:00:00',
                'end_time'    => '17:00:00',
                'category'    => 'Preventive Maintenance',
                'status'      => $taskStatus === 'Completed' ? 'Completed' : ($taskStatus === 'In Progress' ? 'In Progress' : 'Pending'),
                'description' => "Penanganan Tiket SLA {$ticket->ticket_number} - {$ticket->client_name}\nStatus: {$ticket->status}",
                'project_id'  => $proj->id,
                'engineer_id' => $assignedUserId,
            ]
        );

        if ($assignedUserId && Schema::hasTable('schedule_user')) {
            $schedule->users()->sync([$assignedUserId]);
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
              ->orWhereHas('roles', fn($r) => $r->whereIn('name', ['Lead Maintenance', 'Maintenance']));
        })->whereDoesntHave('roles', fn($r) => $r->whereIn('name', ['Lead Engineer', 'Team Leader']))
          ->get(['id', 'name']);

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
}
