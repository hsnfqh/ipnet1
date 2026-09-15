<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CroEngagement;
use App\Models\CroCsatSurvey;
use App\Models\CroConcern;
use App\Models\CroAccountHealth;
use App\Models\CroOpportunity;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CroController extends Controller
{
    /**
     * Dashboard Utama CRO - Control Tower Kepuasan, Retensi & Koordinasi Isu
     */
    public function dashboard(Request $request)
    {
        $selectedClient = $request->input('client');
        $selectedYear   = $request->input('year', date('Y'));

        // 1. CSAT Metrics
        $csatQuery = CroCsatSurvey::query();
        if ($selectedClient) {
            $csatQuery->where('client_name', 'like', "%{$selectedClient}%");
        }
        $csatList = $csatQuery->latest('survey_date')->get();
        $avgCsat  = $csatList->count() > 0 ? round($csatList->avg('csat_score'), 2) : 4.8;
        $totalSurveys = $csatList->count();

        // 2. Account Health Metrics
        $healthQuery = CroAccountHealth::query();
        if ($selectedClient) {
            $healthQuery->where('client_name', 'like', "%{$selectedClient}%");
        }
        $healthAccounts = $healthQuery->get();
        $totalAccounts  = $healthAccounts->count();
        $healthyCount   = $healthAccounts->where('health_status', 'Healthy')->count();
        $warningCount   = $healthAccounts->where('health_status', 'Warning')->count();
        $atRiskCount    = $healthAccounts->where('health_status', 'At-Risk')->count();
        $healthyPct     = $totalAccounts > 0 ? round(($healthyCount / $totalAccounts) * 100, 1) : 92.5;

        // At-Risk Watchlist
        $atRiskWatchlist = $healthAccounts->whereIn('health_status', ['At-Risk', 'Warning'])->sortBy('health_score');

        // 3. Concern & Escalation Orchestration Metrics
        $concernQuery = CroConcern::with(['assignedUser', 'client', 'project']);
        if ($selectedClient) {
            $concernQuery->where('client_name', 'like', "%{$selectedClient}%");
        }
        $concerns = $concernQuery->latest()->get();
        $openConcernsCount = $concerns->whereNotIn('status', ['Closed'])->count();
        $criticalConcernsCount = $concerns->whereIn('severity', ['P1 - Critical', 'Critical'])->whereNotIn('status', ['Closed'])->count();
        $pendingConfirmationCount = $concerns->where('status', 'Pending Confirmation')->count();

        // 4. Renewal Watch (Next 90 Days)
        $now = Carbon::now();
        $ninetyDaysLater = Carbon::now()->addDays(90);
        $renewalsAtStake = $healthAccounts->filter(function ($item) use ($now, $ninetyDaysLater) {
            return $item->contract_end_date && $item->contract_end_date->between($now, $ninetyDaysLater);
        });
        $totalRenewalValue = $renewalsAtStake->sum('estimated_annual_value');

        // 5. Opportunities Bridge
        $oppsQuery = CroOpportunity::query();
        if ($selectedClient) {
            $oppsQuery->where('client_name', 'like', "%{$selectedClient}%");
        }
        $opportunities = $oppsQuery->latest()->get();
        $totalOppValue = $opportunities->whereIn('status', ['Identified', 'Handed Over', 'In Sales Pipeline'])->sum('estimated_value');

        // 6. Recent Engagements
        $engagements = CroEngagement::latest('engagement_date')->take(5)->get();

        // Helpers for Form Modals
        $clients = Client::orderBy('name')->get();
        $projects = Project::orderBy('name')->get(['id', 'name', 'client']);
        $internalUsers = User::orderBy('name')->get(['id', 'name', 'email']);
        $salesUsers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Sales', 'Account Manager', 'BDM', 'BusDev', 'Business Development']);
        })->get(['id', 'name']);

        return view('cro.dashboard', compact(
            'avgCsat',
            'totalSurveys',
            'totalAccounts',
            'healthyCount',
            'warningCount',
            'atRiskCount',
            'healthyPct',
            'atRiskWatchlist',
            'concerns',
            'openConcernsCount',
            'criticalConcernsCount',
            'pendingConfirmationCount',
            'renewalsAtStake',
            'totalRenewalValue',
            'opportunities',
            'totalOppValue',
            'engagements',
            'clients',
            'projects',
            'internalUsers',
            'salesUsers',
            'selectedClient'
        ));
    }

    /**
     * Sub-Halaman 1: Relationship & Engagement Logs
     */
    public function engagements(Request $request)
    {
        $query = CroEngagement::with(['client', 'creator']);

        if ($request->filled('client')) {
            $query->where('client_name', 'like', "%{$request->client}%");
        }
        if ($request->filled('type')) {
            $query->where('engagement_type', $request->type);
        }
        if ($request->filled('sentiment')) {
            $query->where('sentiment', $request->sentiment);
        }

        $engagements = $query->latest('engagement_date')->paginate(15);
        $clients = Client::orderBy('name')->get();

        return view('cro.engagements.index', compact('engagements', 'clients'));
    }

    public function storeEngagement(Request $request)
    {
        $validated = $request->validate([
            'client_name'         => 'required|string|max:255',
            'client_id'           => 'nullable|exists:clients,id',
            'pic_name'            => 'nullable|string|max:255',
            'pic_contact'         => 'nullable|string|max:255',
            'engagement_type'     => 'required|string',
            'title'               => 'required|string|max:255',
            'discussion_summary'  => 'nullable|string',
            'action_items'        => 'nullable|string',
            'sentiment'           => 'required|string',
            'engagement_date'     => 'required|date',
            'location'            => 'nullable|string|max:255',
        ]);

        $validated['created_by'] = auth()->id();

        CroEngagement::create($validated);

        return redirect()->back()->with('success', 'Catatan aktivitas engagement / meeting berhasil disimpan.');
    }

    public function destroyEngagement(CroEngagement $engagement)
    {
        $engagement->delete();
        return redirect()->back()->with('success', 'Catatan engagement berhasil dihapus.');
    }

    /**
     * Sub-Halaman 2: Satisfaction & CSAT
     */
    public function satisfaction(Request $request)
    {
        $query = CroCsatSurvey::with(['client', 'project', 'creator']);

        if ($request->filled('client')) {
            $query->where('client_name', 'like', "%{$request->client}%");
        }
        if ($request->filled('category')) {
            $query->where('service_category', $request->category);
        }

        $surveys = $query->latest('survey_date')->paginate(15);
        $avgScore = CroCsatSurvey::avg('csat_score') ?: 4.8;
        $totalResponses = CroCsatSurvey::count();
        $fiveStarCount = CroCsatSurvey::where('csat_score', 5)->count();
        $fiveStarPct = $totalResponses > 0 ? round(($fiveStarCount / $totalResponses) * 100, 1) : 0;

        $clients = Client::orderBy('name')->get();
        $projects = Project::orderBy('name')->get(['id', 'name', 'client']);

        return view('cro.satisfaction.index', compact('surveys', 'avgScore', 'totalResponses', 'fiveStarPct', 'clients', 'projects'));
    }

    public function storeCsat(Request $request)
    {
        $validated = $request->validate([
            'client_name'            => 'required|string|max:255',
            'client_id'              => 'nullable|exists:clients,id',
            'project_id'             => 'nullable|exists:projects,id',
            'service_category'       => 'required|string',
            'respondent_name'        => 'required|string|max:255',
            'respondent_role'        => 'nullable|string|max:255',
            'csat_score'             => 'required|integer|min:1|max:5',
            'nps_score'              => 'nullable|integer|min:0|max:10',
            'sla_satisfaction_score' => 'nullable|integer|min:1|max:5',
            'support_speed_score'    => 'nullable|integer|min:1|max:5',
            'feedback_notes'         => 'nullable|string',
            'areas_of_improvement'   => 'nullable|string',
            'sentiment'              => 'required|string',
            'survey_date'            => 'required|date',
        ]);

        $validated['created_by'] = auth()->id();

        CroCsatSurvey::create($validated);

        return redirect()->back()->with('success', 'Data hasil survey kepuasan (CSAT) berhasil dicatat.');
    }

    public function destroyCsat(CroCsatSurvey $csat)
    {
        $csat->delete();
        return redirect()->back()->with('success', 'Data survey CSAT berhasil dihapus.');
    }

    /**
     * Sub-Halaman 3: Concern & Escalation Orchestration
     */
    public function concerns(Request $request)
    {
        $query = CroConcern::with(['assignedUser', 'client', 'project']);

        if ($request->filled('client')) {
            $query->where('client_name', 'like', "%{$request->client}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('dept')) {
            $query->where('assigned_dept', $request->dept);
        }

        $concerns = $query->latest()->paginate(15);
        $clients = Client::orderBy('name')->get();
        $projects = Project::orderBy('name')->get(['id', 'name', 'client']);
        $internalUsers = User::orderBy('name')->get(['id', 'name', 'email']);

        return view('cro.concerns.index', compact('concerns', 'clients', 'projects', 'internalUsers'));
    }

    public function storeConcern(Request $request)
    {
        $validated = $request->validate([
            'client_name'   => 'required|string|max:255',
            'client_id'     => 'nullable|exists:clients,id',
            'project_id'    => 'nullable|exists:projects,id',
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'source'        => 'required|string',
            'severity'      => 'required|string',
            'assigned_dept' => 'required|string',
            'assigned_to'   => 'nullable|exists:users,id',
            'sla_due_date'  => 'nullable|date',
        ]);

        // Generate Ticket Number: CR-YYYY-XXXX
        $count = CroConcern::whereYear('created_at', date('Y'))->count() + 1;
        $validated['ticket_number'] = sprintf('CR-%s-%04d', date('Y'), $count);
        $validated['created_by']    = auth()->id();
        $validated['status']        = $request->filled('assigned_to') ? 'Dispatched' : 'Open';

        CroConcern::create($validated);

        return redirect()->back()->with('success', "Concern #{$validated['ticket_number']} berhasil dicatat & didisposisikan.");
    }

    public function dispatchConcern(Request $request, CroConcern $concern)
    {
        $validated = $request->validate([
            'assigned_dept' => 'required|string',
            'assigned_to'   => 'required|exists:users,id',
            'sla_due_date'  => 'nullable|date',
        ]);

        $concern->update([
            'assigned_dept' => $validated['assigned_dept'],
            'assigned_to'   => $validated['assigned_to'],
            'sla_due_date'  => $validated['sla_due_date'] ?? $concern->sla_due_date,
            'status'        => 'In Progress',
        ]);

        return redirect()->back()->with('success', "Concern #{$concern->ticket_number} berhasil didisposisikan ke tim internal.");
    }

    public function resolveConcern(Request $request, CroConcern $concern)
    {
        $validated = $request->validate([
            'root_cause'   => 'required|string',
            'action_taken' => 'required|string',
        ]);

        $concern->update([
            'root_cause'   => $validated['root_cause'],
            'action_taken' => $validated['action_taken'],
            'resolved_at'  => now(),
            'status'       => 'Pending Confirmation',
        ]);

        return redirect()->back()->with('success', "Tindakan teknis dicatat. Status tiket beralih ke 'Menunggu Konfirmasi Klien'.");
    }

    public function confirmConcern(Request $request, CroConcern $concern)
    {
        $validated = $request->validate([
            'customer_confirmed_at'        => 'nullable|date',
            'customer_confirmation_notes'  => 'nullable|string',
            'customer_satisfaction_rating' => 'required|integer|min:1|max:5',
        ]);

        $concern->update([
            'customer_confirmed_at'        => $validated['customer_confirmed_at'] ?? now(),
            'customer_confirmation_notes'  => $validated['customer_confirmation_notes'],
            'customer_satisfaction_rating' => $validated['customer_satisfaction_rating'],
            'status'                       => 'Closed',
        ]);

        return redirect()->back()->with('success', "Konfirmasi klien berhasil divalidasi! Tiket #{$concern->ticket_number} resmi CLOSED.");
    }

    public function destroyConcern(CroConcern $concern)
    {
        $concern->delete();
        return redirect()->back()->with('success', 'Tiket concern berhasil dihapus.');
    }

    /**
     * Sub-Halaman 4: Retention & Account Health
     */
    public function retention(Request $request)
    {
        $query = CroAccountHealth::with(['client', 'accountManager', 'updater']);

        if ($request->filled('client')) {
            $query->where('client_name', 'like', "%{$request->client}%");
        }
        if ($request->filled('status')) {
            $query->where('health_status', $request->status);
        }

        $accounts = $query->orderBy('health_score', 'asc')->paginate(15);
        $clients = Client::orderBy('name')->get();
        $salesUsers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Sales', 'Account Manager', 'BDM', 'BusDev', 'Business Development', 'CRO', 'Customer Relation Officer']);
        })->get(['id', 'name']);

        return view('cro.retention.index', compact('accounts', 'clients', 'salesUsers'));
    }

    public function storeAccountHealth(Request $request)
    {
        $validated = $request->validate([
            'client_name'            => 'required|string|max:255',
            'client_id'              => 'nullable|exists:clients,id',
            'health_status'          => 'required|string',
            'health_score'           => 'required|integer|min:0|max:100',
            'contract_end_date'      => 'nullable|date',
            'estimated_annual_value' => 'nullable|numeric|min:0',
            'renewal_probability'    => 'required|integer|min:0|max:100',
            'risk_factors'           => 'nullable|string',
            'retention_strategy'     => 'nullable|string',
            'next_touchpoint_date'   => 'nullable|date',
            'account_manager_id'     => 'nullable|exists:users,id',
        ]);

        $validated['last_review_date'] = now();
        $validated['updated_by']       = auth()->id();

        CroAccountHealth::updateOrCreate(
            ['client_name' => $validated['client_name']],
            $validated
        );

        return redirect()->back()->with('success', 'Matriks kesehatan & retensi akun klien berhasil diperbarui.');
    }

    /**
     * Sub-Halaman 5: Account Development & Opportunity Bridge
     */
    public function opportunities(Request $request)
    {
        $query = CroOpportunity::with(['client', 'salesPic', 'creator']);

        if ($request->filled('client')) {
            $query->where('client_name', 'like', "%{$request->client}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('opportunity_type', $request->type);
        }

        $opportunities = $query->latest()->paginate(15);
        $clients = Client::orderBy('name')->get();
        $salesUsers = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Sales', 'Account Manager', 'BDM', 'BusDev', 'Business Development']);
        })->get(['id', 'name']);

        return view('cro.opportunities.index', compact('opportunities', 'clients', 'salesUsers'));
    }

    public function storeOpportunity(Request $request)
    {
        $validated = $request->validate([
            'client_name'       => 'required|string|max:255',
            'client_id'         => 'nullable|exists:clients,id',
            'opportunity_type'  => 'required|string',
            'title'             => 'required|string|max:255',
            'estimated_value'   => 'nullable|numeric|min:0',
            'requirement_notes' => 'nullable|string',
            'handed_over_to'    => 'nullable|exists:users,id',
        ]);

        $validated['created_by'] = auth()->id();
        if ($request->filled('handed_over_to')) {
            $validated['status'] = 'Handed Over';
            $validated['handed_over_at'] = now();
        } else {
            $validated['status'] = 'Identified';
        }

        CroOpportunity::create($validated);

        return redirect()->back()->with('success', 'Peluang ekspansi akun berhasil dicatat.');
    }

    public function handoverOpportunity(Request $request, CroOpportunity $opportunity)
    {
        $validated = $request->validate([
            'handed_over_to' => 'required|exists:users,id',
        ]);

        $opportunity->update([
            'handed_over_to' => $validated['handed_over_to'],
            'handed_over_at' => now(),
            'status'         => 'Handed Over',
        ]);

        return redirect()->back()->with('success', "Peluang '{$opportunity->title}' berhasil di-handover ke tim Sales/BDM.");
    }

    public function destroyOpportunity(CroOpportunity $opportunity)
    {
        $opportunity->delete();
        return redirect()->back()->with('success', 'Peluang ekspansi berhasil dihapus.');
    }
}
