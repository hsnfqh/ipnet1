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
use Carbon\Carbon;

class ManagedServiceController extends Controller
{
    /**
     * Managed Service Main Dashboard & Operate Control Tower
     */
    public function dashboard(Request $request)
    {
        $selectedYear = $request->input('year', 2026);
        $selectedClient = $request->input('client');

        // Assets query
        $assetsQuery = ManagedServiceAsset::query();
        if ($selectedClient) {
            $assetsQuery->where('client_name', 'like', "%{$selectedClient}%");
        }
        $assets = $assetsQuery->latest()->get();

        // Tickets query
        $ticketsQuery = ManagedServiceTicket::with(['assignedEngineer', 'asset']);
        if ($selectedClient) {
            $ticketsQuery->where('client_name', 'like', "%{$selectedClient}%");
        }
        $tickets = $ticketsQuery->latest()->get();

        // Reports query
        $reports = ManagedServiceReport::latest()->take(5)->get();

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
        $operateProjects = Project::where(function($q) {
            $q->where('stage', 'Operate')
              ->orWhere('project_type', 'like', '%Maintenance%')
              ->orWhere('project_type', 'like', '%Managed%');
        })->whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])->get();

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
        $clients = Client::orderBy('name')->get();
        $maintenanceEngineers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Lead Maintenance', 'Maintenance', 'Lead Engineer', 'Engineer']);
        })->get(['id', 'name']);

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
            'clients',
            'maintenanceEngineers',
            'upcomingPmSchedules',
            'selectedYear',
            'selectedClient'
        ));
    }

    /**
     * Asset & Configuration Items (CI) Management
     */
    public function assets(Request $request)
    {
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
        $clients = Client::orderBy('name')->get();
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

    public function updateAsset(Request $request, ManagedServiceAsset $asset)
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

        $asset->update($validated);

        return redirect()->route('ms.assets.index')->with('success', 'Data Aset berhasil diperbarui.');
    }

    public function destroyAsset(ManagedServiceAsset $asset)
    {
        $asset->delete();
        return redirect()->route('ms.assets.index')->with('success', 'Aset berhasil dihapus.');
    }

    /**
     * Incident & Service Request Tickets
     */
    public function tickets(Request $request)
    {
        $query = ManagedServiceTicket::with(['assignedEngineer', 'asset']);

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
        $engineers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Lead Maintenance', 'Maintenance', 'Lead Engineer', 'Engineer']);
        })->get();

        return view('managed_service.tickets', compact('tickets', 'assets', 'engineers'));
    }

    public function storeTicket(Request $request)
    {
        $validated = $request->validate([
            'client_name'   => 'required|string|max:255',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'type'          => 'required|string|in:Incident,Service Request,Change Request',
            'priority'      => 'required|string|in:P1 - Critical,P2 - Major,P3 - Minor,P4 - Low',
            'reported_by'   => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'assigned_to'   => 'nullable|exists:users,id',
            'asset_id'      => 'nullable|exists:managed_service_assets,id',
        ]);

        // Generate Ticket Number
        $prefix = match($validated['type']) {
            'Incident'        => 'INC',
            'Service Request' => 'REQ',
            'Change Request'  => 'CR',
            default           => 'TCK',
        };
        $count = ManagedServiceTicket::whereYear('created_at', now()->year)->count() + 1;
        $validated['ticket_number'] = $prefix . '-' . now()->year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'Open';

        // Set SLA Deadline
        $slaHours = match($validated['priority']) {
            'P1 - Critical' => 1,
            'P2 - Major'    => 4,
            'P3 - Minor'    => 8,
            'P4 - Low'      => 24,
            default         => 4,
        };
        $validated['sla_deadline'] = now()->addHours($slaHours);

        ManagedServiceTicket::create($validated);

        return redirect()->route('ms.tickets.index')->with('success', 'Tiket ' . $validated['ticket_number'] . ' berhasil dibuat.');
    }

    public function updateTicket(Request $request, ManagedServiceTicket $ticket)
    {
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

        return redirect()->route('ms.tickets.index')->with('success', 'Status tiket ' . $ticket->ticket_number . ' diperbarui.');
    }

    /**
     * Preventive Maintenance Planner
     */
    public function maintenance(Request $request)
    {
        $projects = Project::where(function($q) {
            $q->where('stage', 'Operate')
              ->orWhere('project_type', 'like', '%Maintenance%')
              ->orWhere('project_type', 'like', '%Managed%');
        })->whereNotIn('name', ['DAY OFF', 'Day Off'])->get();

        $assets = ManagedServiceAsset::all();
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

        $engineers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Lead Maintenance', 'Maintenance', 'Lead Engineer', 'Engineer']);
        })->get();

        return view('managed_service.maintenance', compact('projects', 'assets', 'schedules', 'engineers'));
    }

    /**
     * Reports & SLA Compliance
     */
    public function reports(Request $request)
    {
        $reports = ManagedServiceReport::latest()->paginate(15);
        $clients = Client::orderBy('name')->get();
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
