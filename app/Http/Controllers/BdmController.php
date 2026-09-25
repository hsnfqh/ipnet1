<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\MarketIntelligence;
use App\Models\Partnership;
use App\Models\Client;
use App\Models\Vendor;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileUploadHelper;
use Carbon\Carbon;

class BdmController extends Controller
{
    /**
     * Personil Sales Resmi IP Network (Raiza & Nabylla Berlianita)
     */
    public static $salesTeam = [
        'Raiza',
        'Nabylla Berlianita',
    ];

    /**
     * 5 Personil BDM Resmi IP Network
     */
    public static $bdmTeam = [
        'Kurnijanto Edy',
        'Novan Pudjirachmanto',
        'M. Kipsriyanto',
        'Armen Yuldi',
        'Antonius Dony',
    ];

    /**
     * 1. Overview Dashboard (Clean, Executive Summary)
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $isManagerial = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager']);

        $selectedYear = (int) $request->input('year', date('Y'));

        $allProjectsQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti']);

        if (!$isManagerial) {
            $allProjectsQuery->where(function($q) use ($user) {
                $q->where('bdm_id', $user->id)
                  ->orWhere('created_by', $user->id);
            });
        }
        
        $projects = (clone $allProjectsQuery)->whereYear('created_at', $selectedYear)->get();
        if ($projects->isEmpty()) {
            $projects = (clone $allProjectsQuery)->get();
        }

        // 5 Summary Metrics
        $totalProjectCount = $projects->count();
        $totalNilaiProject = $projects->sum('contract_value');

        $opportunityProjects = $projects->whereIn('status', ['Opportunity', 'Draft', 'Planning']);
        $totalOpportunityCount = $opportunityProjects->count();
        $totalNilaiOpportunity = $opportunityProjects->sum('contract_value');

        $handedOverCount = (clone $allProjectsQuery)->where('bdm_handover_status', 'Handed Over to Sales')->count();
        $acceptedBySalesCount = (clone $allProjectsQuery)->where('bdm_handover_status', 'Accepted by Sales')->count();
        $totalHandoverCount = $handedOverCount + $acceptedBySalesCount;
        $conversionRate = $totalOpportunityCount > 0 ? round(($totalHandoverCount / $totalOpportunityCount) * 100, 1) : 0;

        $intelQuery = MarketIntelligence::query();
        $partnerQuery = Partnership::query();
        if (!$isManagerial) {
            $intelQuery->where('author_id', $user->id);
            $partnerQuery->where('created_by', $user->id);
        }

        $intelCount = $intelQuery->count();
        $partnerCount = (clone $partnerQuery)->where('status', 'Active')->count();

        // Monthly Trend
        $monthlyValues = array_fill(1, 12, 0);
        foreach ($projects as $p) {
            $date = $p->created_at ?? $p->start_date;
            if ($date) {
                $m = (int) Carbon::parse($date)->format('n');
                $monthlyValues[$m] += (float) ($p->contract_value ?? 0);
            }
        }
        $monthlyDataInMillions = array_map(function($val) {
            return round($val / 1000000, 2);
        }, array_values($monthlyValues));

        // Sektor Klien / Market Breakdown
        $sectorCounts = [
            'Government / Kementerian' => 0,
            'Banking & Financial'       => 0,
            'BUMN & Enterprise'         => 0,
            'Healthcare & Lainnya'      => 0,
        ];
        foreach ($projects as $p) {
            $clientLower = strtolower($p->client ?? '');
            if (str_contains($clientLower, 'kemenkeu') || str_contains($clientLower, 'kementerian') || str_contains($clientLower, 'dinas') || str_contains($clientLower, 'cukai') || str_contains($clientLower, 'pemerintah')) {
                $sectorCounts['Government / Kementerian'] += ($p->contract_value ?: 1);
            } elseif (str_contains($clientLower, 'bank') || str_contains($clientLower, 'btpn') || str_contains($clientLower, 'bca') || str_contains($clientLower, 'mandiri') || str_contains($clientLower, 'adira') || str_contains($clientLower, 'finance')) {
                $sectorCounts['Banking & Financial'] += ($p->contract_value ?: 1);
            } elseif (str_contains($clientLower, 'shopee') || str_contains($clientLower, 'lazada') || str_contains($clientLower, 'angkasa pura') || str_contains($clientLower, 'pln') || str_contains($clientLower, 'telkom') || str_contains($clientLower, 'indosat')) {
                $sectorCounts['BUMN & Enterprise'] += ($p->contract_value ?: 1);
            } else {
                $sectorCounts['Healthcare & Lainnya'] += ($p->contract_value ?: 1);
            }
        }
        $sectorDataInMillions = array_map(function($val) {
            return round($val / 1000000, 2);
        }, array_values($sectorCounts));

        // Recent Highlights (Personalized for BDM)
        $recentOpportunities = (clone $allProjectsQuery)->latest()->take(5)->get();
        $recentIntels = (clone $intelQuery)->latest()->take(3)->get();
        $recentPartners = (clone $partnerQuery)->latest()->take(3)->get();

        return view('bdm.dashboard', compact(
            'selectedYear',
            'totalProjectCount',
            'totalNilaiProject',
            'totalOpportunityCount',
            'totalNilaiOpportunity',
            'handedOverCount',
            'acceptedBySalesCount',
            'totalHandoverCount',
            'conversionRate',
            'intelCount',
            'partnerCount',
            'monthlyDataInMillions',
            'sectorDataInMillions',
            'sectorCounts',
            'recentOpportunities',
            'recentIntels',
            'recentPartners'
        ));
    }

    /**
     * 2. Dedicated Menu: Inisiasi Peluang & Handover ke Sales
     */
    public function opportunities(Request $request)
    {
        $user = auth()->user();
        $isManagerial = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager']);

        $search = $request->input('search');
        $filterStatus = $request->input('status');

        $query = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
            ->with(['bdm', 'creator']);

        if (!$isManagerial) {
            $query->where(function($q) use ($user) {
                $q->where('bdm_id', $user->id)
                  ->orWhere('created_by', $user->id);
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%")
                  ->orWhere('opportunity_source', 'like', "%{$search}%");
            });
        }

        if ($filterStatus) {
            if ($filterStatus === 'draft') {
                $query->where(function($q) {
                    $q->whereNull('bdm_handover_status')->orWhere('bdm_handover_status', 'Draft');
                });
            } elseif ($filterStatus === 'handed_over') {
                $query->where('bdm_handover_status', 'Handed Over to Sales');
            } elseif ($filterStatus === 'accepted') {
                $query->where('bdm_handover_status', 'Accepted by Sales');
            }
        }

        $opportunities = $query->latest()->paginate(8)->withQueryString();

        $salesUsers = User::with('division')
            ->where(function($q) {
                $q->whereHas('roles', function($r) {
                    $r->whereIn('name', ['Sales', 'Account Manager']);
                })->orWhereIn('name', self::$salesTeam);
            })
            ->whereNotIn('name', self::$bdmTeam)
            ->whereDoesntHave('roles', function($r) {
                $r->whereIn('name', ['BDM', 'BusDev', 'Business Development']);
            })
            ->orderBy('name')
            ->get();

        if ($salesUsers->isEmpty()) {
            $salesUsers = collect(self::$salesTeam)->map(function($name, $idx) {
                return (object)[
                    'id' => $idx + 1,
                    'name' => $name,
                    'position' => 'Sales (Account Manager)',
                    'division' => (object)['name' => 'Commercial Division']
                ];
            });
        }

        $salesTeamNames = self::$salesTeam;

        return view('bdm.opportunities.index', compact('opportunities', 'search', 'filterStatus', 'salesUsers', 'salesTeamNames'));
    }

    /**
     * 3. Dedicated Menu: Market Intelligence & Riset Pasar
     */
    public function intelligence(Request $request)
    {
        $user = auth()->user();
        $isManagerial = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager']);

        $search = $request->input('search');
        $category = $request->input('category');

        $query = MarketIntelligence::with('author');

        if (!$isManagerial) {
            $query->where('author_id', $user->id);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%")
                  ->orWhere('industry_sector', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        $intels = $query->latest()->paginate(9)->withQueryString();

        return view('bdm.intelligence.index', compact('intels', 'search', 'category'));
    }

    /**
     * 4. Dedicated Menu: Kemitraan Prinsipal & Channel Network
     */
    public function partnerships(Request $request)
    {
        $user = auth()->user();
        $isManagerial = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager']);

        $search = $request->input('search');
        $partnerType = $request->input('type');

        $query = Partnership::with('creator');

        if (!$isManagerial) {
            $query->where('created_by', $user->id);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('partner_name', 'like', "%{$search}%")
                  ->orWhere('pic_name', 'like', "%{$search}%")
                  ->orWhere('collaboration_scope', 'like', "%{$search}%");
            });
        }

        if ($partnerType) {
            $query->where('partner_type', $partnerType);
        }

        $partners = $query->latest()->paginate(9)->withQueryString();

        return view('bdm.partnerships.index', compact('partners', 'search', 'partnerType'));
    }

    /**
     * Store New Business Opportunity
     */
    public function storeOpportunity(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'client'               => 'required|string|max:255',
            'contract_value'       => 'required|numeric|min:0|max:999999999999999999',
            'opportunity_source'   => 'required|string|max:255',
            'initial_requirement'  => 'nullable|string',
            'target_timeline_type' => 'nullable|string',
            'bd_assessment_score'  => 'nullable|integer|min:0|max:100',
        ]);

        $user = auth()->user();

        $project = Project::create([
            'name'                 => $validated['name'],
            'client'               => $validated['client'],
            'contract_value'       => $validated['contract_value'],
            'opportunity_source'   => $validated['opportunity_source'],
            'initial_requirement'  => $validated['initial_requirement'] ?? null,
            'target_timeline_type' => $validated['target_timeline_type'] ?? 'Q3 2026',
            'bd_assessment_score'  => $validated['bd_assessment_score'] ?? 80,
            'status'               => 'Opportunity',
            'stage'                => 'Acquire',
            'acquire_status'       => 'Prospecting',
            'bdm_handover_status'  => 'Draft',
            'bdm_id'               => $user->id,
            'created_by'           => $user->id,
            'start_date'           => now(),
            'deadline'             => now()->addMonths(2),
        ]);

        // Auto add client if not exists
        if (!Client::where('name', $validated['client'])->exists()) {
            Client::create([
                'name'       => $validated['client'],
                'created_by' => $user->id,
            ]);
        }

        return redirect()->back()
            ->with('success', "Peluang Bisnis '{$project->name}' berhasil didaftarkan ke pipeline.");
    }

    /**
     * Update Existing Business Opportunity
     */
    public function updateOpportunity(Request $request, Project $project)
    {
        $user = auth()->user();
        $isManagerial = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager']);

        if (!$isManagerial && $project->bdm_id !== $user->id && $project->created_by !== $user->id) {
            return redirect()->back()->with('error', 'Anda hanya dapat mengubah peluang bisnis yang Anda inisiasi sendiri.');
        }

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'client'               => 'required|string|max:255',
            'contract_value'       => 'required|numeric|min:0|max:999999999999999999',
            'opportunity_source'   => 'required|string|max:255',
            'initial_requirement'  => 'nullable|string',
            'target_timeline_type' => 'nullable|string',
            'bd_assessment_score'  => 'nullable|integer|min:0|max:100',
        ]);

        $project->update([
            'name'                 => $validated['name'],
            'client'               => $validated['client'],
            'contract_value'       => $validated['contract_value'],
            'opportunity_source'   => $validated['opportunity_source'],
            'initial_requirement'  => $validated['initial_requirement'] ?? $project->initial_requirement,
            'target_timeline_type' => $validated['target_timeline_type'] ?? $project->target_timeline_type,
            'bd_assessment_score'  => $validated['bd_assessment_score'] ?? $project->bd_assessment_score,
        ]);

        // Auto add client if not exists
        if (!Client::where('name', $validated['client'])->exists()) {
            Client::create([
                'name'       => $validated['client'],
                'created_by' => $user->id,
            ]);
        }

        return redirect()->back()->with('success', "Peluang Bisnis '{$project->name}' berhasil diperbarui.");
    }

    /**
     * Delete Business Opportunity
     */
    public function destroyOpportunity(Project $project)
    {
        $user = auth()->user();
        $isManagerial = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager']);

        if (!$isManagerial && $project->bdm_id !== $user->id && $project->created_by !== $user->id) {
            return redirect()->back()->with('error', 'Anda hanya dapat menghapus peluang bisnis yang Anda buat sendiri.');
        }

        $projectName = $project->name;
        $project->delete();

        return redirect()->back()->with('success', "Peluang Bisnis '{$projectName}' berhasil dihapus.");
    }

    /**
     * Official BDM -> Sales Handover Package (11 Elements)
     */
    /**
     * Official BDM -> Sales Handover Package (11 Elements)
     */
    public function handoverToSales(Request $request, Project $project)
    {
        $validated = $request->validate([
            'sales_name'             => 'required|string',
            'business_need_summary'  => 'required|string',
            'target_timeline_type'   => 'nullable|string',
            'competitor_analysis'    => 'nullable|string',
            'partner_alignment'      => 'nullable|string',
            'recommended_partner'    => 'nullable|string',
            'bd_assessment_score'    => 'nullable|integer|min:1|max:100',
            'initial_requirement'    => 'nullable|string',
            'pic_name'               => 'nullable|string',
            'pic_role'               => 'nullable|string',
            'pic_phone'              => 'nullable|string',
            'pic_email'              => 'nullable|string',
            'handover_document_file' => 'nullable|file|max:20480',
            'attachment_file'        => 'nullable|file|max:20480',
            'notes'                  => 'nullable|string',
        ]);

        $filePath = $project->handover_document_file;
        if ($request->hasFile('handover_document_file')) {
            $filePath = FileUploadHelper::storePublicly($request->file('handover_document_file'), 'bdm_handovers');
        } elseif ($request->hasFile('attachment_file')) {
            $filePath = FileUploadHelper::storePublicly($request->file('attachment_file'), 'bdm_handovers');
        }

        $existingStakeholders = is_array($project->stakeholders_data) ? $project->stakeholders_data : [];
        $stakeholders = [
            'pic_name'  => $request->input('pic_name') ?: ($existingStakeholders['pic_name'] ?? '-'),
            'pic_role'  => $request->input('pic_role') ?: ($existingStakeholders['pic_role'] ?? '-'),
            'pic_phone' => $request->input('pic_phone') ?: ($existingStakeholders['pic_phone'] ?? '-'),
            'pic_email' => $request->input('pic_email') ?: ($existingStakeholders['pic_email'] ?? '-'),
        ];

        $targetTimeline = $request->input('target_timeline_type') ?: ($project->target_timeline_type ?: 'Q3 2026');
        $partnerAlignment = $request->input('partner_alignment') ?: ($request->input('recommended_partner') ?: ($project->partner_alignment ?: 'Direct / Multi-vendor'));
        $bdScore = $request->filled('bd_assessment_score') ? (int)$request->input('bd_assessment_score') : ($project->bd_assessment_score ?: 80);

        $project->update([
            'sales_name'             => $validated['sales_name'],
            'business_need_summary'  => $validated['business_need_summary'],
            'target_timeline_type'   => $targetTimeline,
            'competitor_analysis'    => $request->input('competitor_analysis') ?: ($project->competitor_analysis ?: 'Belum teridentifikasi'),
            'partner_alignment'      => $partnerAlignment,
            'bd_assessment_score'    => $bdScore,
            'initial_requirement'    => $request->input('initial_requirement') ?: $project->initial_requirement,
            'notes'                  => $request->input('notes') ?: $project->notes,
            'stakeholders_data'      => $stakeholders,
            'handover_document_file' => $filePath,
            'bdm_handover_status'    => 'Handed Over to Sales',
            'bdm_handover_at'        => now(),
            'acquire_status'         => 'Proposal Preparation',
        ]);

        $assignedSales = User::where('name', $validated['sales_name'])->first();
        if ($assignedSales) {
            Notification::create([
                'user_id' => $assignedSales->id,
                'title'   => 'Peluang Baru Diserahkan dari BDM',
                'message' => "Peluang '{$project->name}' ({$project->client}) telah diserahterimakan kepada Anda.",
                'type'    => 'opportunity_handover',
                'is_read' => false,
            ]);
        }

        return redirect()->back()
            ->with('success', "Berkas Serah Terima Peluang '{$project->name}' berhasil diserahkan kepada {$validated['sales_name']}.");
    }

    /**
     * Store Market Intelligence
     */
    public function storeIntelligence(Request $request)
    {
        $validated = $request->validate([
            'title'           => 'required|string|max:255',
            'category'        => 'required|string',
            'industry_sector' => 'required|string',
            'summary'         => 'required|string',
            'potential_value' => 'nullable|numeric|min:0|max:999999999999999999',
            'impact_level'    => 'required|string',
            'source_url'      => 'nullable|string',
        ]);

        $intel = MarketIntelligence::create([
            'title'           => $validated['title'],
            'category'        => $validated['category'],
            'industry_sector' => $validated['industry_sector'],
            'summary'         => $validated['summary'],
            'potential_value' => $validated['potential_value'] ?? null,
            'impact_level'    => $validated['impact_level'],
            'source_url'      => $validated['source_url'] ?? null,
            'status'          => 'Active',
            'author_id'       => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', "Market Intelligence '{$intel->title}' berhasil ditambahkan.");
    }

    /**
     * Delete Market Intelligence
     */
    public function destroyIntelligence(MarketIntelligence $intelligence)
    {
        $user = auth()->user();
        $isManagerial = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager']);

        if (!$isManagerial && $intelligence->author_id !== $user->id) {
            return redirect()->back()->with('error', 'Anda hanya dapat menghapus Market Intelligence yang Anda buat sendiri.');
        }

        $intelligence->delete();
        return redirect()->back()
            ->with('success', 'Market intelligence berhasil dihapus.');
    }

    /**
     * Store Partnership & Channel Network
     */
    public function storePartnership(Request $request)
    {
        $validated = $request->validate([
            'partner_name'        => 'required|string|max:255',
            'partner_type'        => 'required|string',
            'tier_level'          => 'required|string',
            'pic_name'            => 'nullable|string|max:255',
            'pic_contact'         => 'nullable|string|max:255',
            'pic_email'           => 'nullable|email|max:255',
            'collaboration_scope' => 'nullable|string',
        ]);

        $partner = Partnership::create([
            'partner_name'        => $validated['partner_name'],
            'partner_type'        => $validated['partner_type'],
            'tier_level'          => $validated['tier_level'],
            'pic_name'            => $validated['pic_name'] ?? null,
            'pic_contact'         => $validated['pic_contact'] ?? null,
            'pic_email'           => $validated['pic_email'] ?? null,
            'collaboration_scope' => $validated['collaboration_scope'] ?? null,
            'status'              => 'Active',
            'created_by'          => auth()->id(),
        ]);

        return redirect()->back()
            ->with('success', "Kemitraan '{$partner->partner_name}' berhasil didaftarkan.");
    }

    /**
     * Delete Partnership
     */
    public function destroyPartnership(Partnership $partnership)
    {
        $user = auth()->user();
        $isManagerial = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager']);

        if (!$isManagerial && $partnership->created_by !== $user->id) {
            return redirect()->back()->with('error', 'Anda hanya dapat menghapus data kemitraan yang Anda buat sendiri.');
        }

        $partnership->delete();
        return redirect()->back()
            ->with('success', 'Data kemitraan berhasil dihapus.');
    }
}
