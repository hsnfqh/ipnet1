<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SalesActivity;
use App\Models\User;
use App\Models\Client;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileUploadHelper;
use Carbon\Carbon;

class SalesCrmController extends Controller
{
    /**
     * 8 Tahapan Siklus Penjualan Standar SOP (Sales Lifecycle)
     */
    public static $stages = [
        'Qualification'         => ['label' => 'Qualification',          'default_prob' => 10,  'color' => '#6B7280', 'bg' => '#F3F4F6'],
        'Qualified Opportunity' => ['label' => 'Qualified Opportunity',  'default_prob' => 25,  'color' => '#3B82F6', 'bg' => '#EFF6FF'],
        'Proposal Request'      => ['label' => 'Proposal Request',       'default_prob' => 50,  'color' => '#8B5CF6', 'bg' => '#F5F3FF'],
        'Quotation'             => ['label' => 'Quotation Submitted',    'default_prob' => 70,  'color' => '#EAB308', 'bg' => '#FEFCE8'],
        'Negotiation'           => ['label' => 'Negotiation',            'default_prob' => 85,  'color' => '#F97316', 'bg' => '#FFF7ED'],
        'Approval'              => ['label' => 'Internal Approval',      'default_prob' => 95,  'color' => '#06B6D4', 'bg' => '#ECFEFF'],
        'Contract / PO / SPK'   => ['label' => 'Contract / PO / SPK',    'default_prob' => 98,  'color' => '#10B981', 'bg' => '#ECFDF5'],
        'Closed Won'            => ['label' => 'Closed Won (Handover)',  'default_prob' => 100, 'color' => '#16A34A', 'bg' => '#F0FDF4'],
    ];

    /**
     * Tipe Aktivitas CRM
     */
    public static $activityTypes = [
        'Meeting'              => 'Meeting / Client Visit',
        'Phone Call'           => 'Telepon / WhatsApp Call',
        'Email'                => 'Email / Correspondence',
        'Demo / Presentation'  => 'Product Demo / Presentation',
        'Quotation Submission' => 'Pengiriman Penawaran / Quotation',
        'Negotiation'          => 'Klarifikasi & Negosiasi Harga',
        'Follow Up'            => 'Follow-up Prospek',
        'Contract Signing'     => 'Tanda Tangan Kontrak / PO',
    ];

    /**
     * 1. Halaman Dedicated: Sales Pipeline & Project Kanban Board
     */
    public function pipeline(Request $request)
    {
        $user = auth()->user();
        $isManagerial = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Head Divisi', 'Group Leader', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation', 'PMO', 'Project Manager']) || str_contains(strtolower($user->name), 'susanto') || str_contains(strtolower($user->name), 'hariyadi');

        $search = $request->input('search');
        $filterDivision = $request->input('division_id');
        $filterApproval = $request->input('approval_status');
        $filterSales = $request->input('sales');

        $salesTeam = BdmController::$salesTeam;

        $allProjectsQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
            // Standalone Sales: HANYA proyek peluang/pipeline sales (belum terhubung ke teknikal/engineer)
            ->where(function($q) use ($salesTeam, $user) {
                $q->where('stage', 'Acquire')
                  ->orWhere('opportunity_source', 'Direct Sales Prospecting')
                  ->orWhereNotNull('opportunity_source')
                  ->orWhereNotNull('bdm_id')
                  ->orWhere('bdm_handover_status', 'Self-Sourced Sales')
                  ->orWhereIn('sales_name', $salesTeam)
                  ->orWhere('created_by', $user->id)
                  ->orWhereHas('creator', function($c) {
                      $c->whereHas('roles', function($r) {
                          $r->whereIn('name', ['Sales', 'Account Manager', 'BDM', 'BusDev', 'Business Development']);
                      });
                  });
            })
            // JANGAN menyangkut teknikal / engineer / maintenance
            ->where('name', 'not like', '%Preventive Maintenance%')
            ->where('name', 'not like', '%Corrective Maintenance%')
            ->whereDoesntHave('creator', function($c) {
                $c->whereHas('roles', function($r) {
                    $r->whereIn('name', ['Engineer', 'Field Engineer', 'Lead Maintenance', 'Maintenance']);
                });
            })
            ->with(['bdm', 'creator', 'salesActivities']);

        if (!$isManagerial) {
            $allProjectsQuery->where(function($q) use ($user) {
                $q->where('sales_name', $user->name)
                  ->orWhere('created_by', $user->id);
            });
        }

        if ($search) {
            $allProjectsQuery->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%")
                  ->orWhere('sales_name', 'like', "%{$search}%")
                  ->orWhere('quotation_number', 'like', "%{$search}%");
            });
        }

        if ($filterDivision) {
            $allProjectsQuery->where('division_id', $filterDivision);
        }

        if ($filterApproval) {
            $allProjectsQuery->where(function($q) use ($filterApproval) {
                $q->where('handover_status', $filterApproval)
                  ->orWhere('status', $filterApproval)
                  ->orWhere('sales_stage', $filterApproval);
            });
        }

        if ($filterSales && $isManagerial) {
            $allProjectsQuery->where('sales_name', $filterSales);
        }

        $allProjects = $allProjectsQuery->latest('updated_at')->get();

        // 5 Kanban Columns
        $kanban = [
            'draft' => $allProjects->filter(function($p) {
                $st = strtolower($p->status ?? '');
                return $st === 'draft' || $st === 'planning' || $st === 'qualification';
            }),
            'opportunity' => $allProjects->filter(function($p) {
                $st = strtolower($p->status ?? '');
                $stage = strtolower($p->stage ?? '');
                $salesStage = strtolower($p->sales_stage ?? '');
                if (in_array($st, ['in progress', 'on progress', 'active', 'development', 'testing', 'completed', 'finished', 'delivered', 'done', 'cancelled']) || $salesStage === 'closed won' || $salesStage === 'closed lost') {
                    return false;
                }
                return $st === 'opportunity' || $st === 'prospect' || ($stage === 'acquire' && !in_array($st, ['draft', 'planning']));
            }),
            'in_progress' => $allProjects->filter(function($p) {
                $st = strtolower($p->status ?? '');
                $salesStage = strtolower($p->sales_stage ?? '');
                if (in_array($st, ['completed', 'finished', 'delivered', 'done', 'cancelled']) || $salesStage === 'closed lost') {
                    return false;
                }
                return in_array($st, ['in progress', 'on progress', 'active', 'development', 'testing']) || $salesStage === 'closed won';
            }),
            'pending' => $allProjects->filter(function($p) {
                $st = strtolower($p->status ?? '');
                return in_array($st, ['pending', 'on hold', 'review', 'clarification']);
            }),
            'completed' => $allProjects->filter(function($p) {
                $st = strtolower($p->status ?? '');
                return in_array($st, ['completed', 'finished', 'delivered', 'done']);
            }),
        ];

        $divisions = \App\Models\Division::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $salesTeam = BdmController::$salesTeam;
        $stages = self::$stages;

        $totalPipelineValue = $allProjects->whereNotIn('sales_stage', ['Closed Won', 'Closed Lost'])->sum('contract_value');
        $totalWeightedForecast = $allProjects->whereNotIn('sales_stage', ['Closed Won', 'Closed Lost'])->sum(fn($p) => ($p->contract_value ?? 0) * (($p->win_probability ?? 10) / 100));
        $totalWonValue = $allProjects->where('sales_stage', 'Closed Won')->sum('contract_value');

        return view('sales.pipeline.index', compact(
            'allProjects',
            'kanban',
            'divisions',
            'clients',
            'search',
            'filterDivision',
            'filterApproval',
            'filterSales',
            'salesTeam',
            'stages',
            'isManagerial',
            'totalPipelineValue',
            'totalWeightedForecast',
            'totalWonValue'
        ));
    }

    /**
     * Halaman Full: Form Tambah Proyek / Opportunity Baru
     */
    public function createOpportunity(Request $request)
    {
        $divisions = \App\Models\Division::orderBy('name')->get();
        $clients   = Client::orderBy('name')->get();
        $stages    = self::$stages;
        $isCompleted = $request->query('type') === 'completed';

        return view('sales.pipeline.create', compact('divisions', 'clients', 'stages', 'isCompleted'));
    }

    /**
     * Store New Sales Opportunity / Project
     */
    public function storeOpportunity(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'client'                => 'required|string|max:255',
            'contract_value'        => 'nullable|numeric|min:0',
            'division_id'           => 'nullable|exists:divisions,id',
            'status'                => 'nullable|string',
            'sales_stage'           => 'nullable|string',
            'win_probability'       => 'nullable|integer|min:0|max:100',
            'expected_closing_date' => 'nullable|date',
            'opportunity_source'    => 'nullable|string|max:255',
            'sales_notes'           => 'nullable|string',
            'is_completed'          => 'nullable|boolean',
        ]);

        $user = auth()->user();
        $isCompleted = $request->boolean('is_completed') || ($request->input('status') === 'Completed');

        if ($isCompleted) {
            $status = 'Completed';
            $stage = 'Acquire';          // Tetap di Acquire — PMO hanya lihat Deliver & Operate
            $salesStage = 'Closed Won';
            $prob = 100;
            $progress = 0;              // Progress teknis belum dimulai, baru commercial closed
        } else {
            $status = $validated['status'] ?? 'Draft';
            $stage = in_array($status, ['In Progress', 'Active']) ? 'Deliver' : 'Draft';
            $salesStage = $validated['sales_stage'] ?? ($status === 'Draft' ? 'Qualification' : 'Qualified Opportunity');
            $defaultProb = self::$stages[$salesStage]['default_prob'] ?? 25;
            $prob = isset($validated['win_probability']) && $validated['win_probability'] !== '' ? (int) $validated['win_probability'] : $defaultProb;
            $progress = 0;
        }

        $closingDate = !empty($validated['expected_closing_date']) ? $validated['expected_closing_date'] : now()->addMonths(1)->toDateString();
        $contractValue = $validated['contract_value'] ?? 0;
        $salesName = $request->input('sales_name') ?: $user->name;

        // Check matching Client in DB
        $clientRecord = Client::where('name', $validated['client'])
            ->orWhere('department', $validated['client'])
            ->orWhere('id', $validated['client'])
            ->first();

        $clientName = $clientRecord ? $clientRecord->name : $validated['client'];
        $clientDept = $request->input('client_department') ?: ($clientRecord ? $clientRecord->department : null);
        $customerPicName = $request->input('customer_pic_name') ?: ($clientRecord ? $clientRecord->pic_name : null);
        $customerPicPhone = $request->input('customer_pic_phone') ?: ($clientRecord ? $clientRecord->phone : null);
        $customerPicEmail = $request->input('customer_pic_email') ?: ($clientRecord ? $clientRecord->email : null);

        $project = Project::create([
            'name'                   => $validated['name'],
            'client'                 => $clientName,
            'contract_value'         => $contractValue,
            'division_id'            => $validated['division_id'] ?? null,
            'sales_stage'            => $salesStage,
            'win_probability'        => $prob,
            'expected_closing_date'  => $closingDate,
            'opportunity_source'     => $validated['opportunity_source'] ?? 'Direct Sales Prospecting',
            'sales_name'             => $salesName,
            'created_by'             => $user->id,
            'sales_notes'            => $validated['sales_notes'] ?? null,
            'status'                 => $status,
            'stage'                  => $stage,
            'progress'               => $progress,
            'acquire_status'         => $status === 'Completed' ? 'Closed' : ($status === 'Draft' ? 'Draft' : 'Prospecting'),
            'bdm_handover_status'    => 'Self-Sourced Sales',
            'start_date'             => now(),
            'deadline'               => $closingDate,
            'customer_pic_technical' => $customerPicEmail,
            'customer_pic_business'  => $customerPicPhone,
            'customer_pic_finance'   => $customerPicEmail,
        ]);

        // Auto add Client to database if not exists
        if (!$clientRecord && !empty($validated['client'])) {
            Client::create([
                'name'        => $validated['client'],
                'department'  => $clientDept,
                'pic_name'    => $customerPicName,
                'phone'       => $customerPicPhone,
                'email'       => $customerPicEmail,
                'created_by'  => $user->id,
            ]);
        }

        // Auto record initial Sales Activity log
        SalesActivity::create([
            'project_id'    => $project->id,
            'sales_id'      => $user->id,
            'activity_type' => 'Follow Up',
            'subject'       => 'Inisiasi Proyek: ' . $project->name,
            'activity_date' => now(),
            'notes'         => 'Proyek tersimpan dengan status ' . $status,
            'next_action'   => 'Monitoring progres dan koordinasi tim teknis',
            'status'        => 'Completed',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Proyek '{$project->name}' berhasil disimpan.",
                'project' => $project
            ]);
        }

        return redirect()->route('sales.pipeline.index')
            ->with('success', "Proyek '{$project->name}' berhasil ditambahkan ke board.");
    }

    /**
     * Update Sales Lifecycle Stage & Forecast Information
     */
    public function updateStage(Request $request, Project $project)
    {
        $validated = $request->validate([
            'sales_stage'           => 'required|string',
            'win_probability'       => 'required|integer|min:0|max:100',
            'expected_closing_date' => 'nullable|date',
            'contract_value'        => 'nullable|numeric|min:0',
            'quotation_number'      => 'nullable|string|max:255',
            'quotation_amount'      => 'nullable|numeric|min:0',
            'quotation_file'        => FileUploadHelper::fileValidationRule(20480),
            'sales_notes'           => 'nullable|string',
            'lost_reason'           => 'nullable|string',
            'lost_competitor'       => 'nullable|string',
        ]);

        $oldStage = $project->sales_stage;
        $quotationFilePath = $project->quotation_file;

        if ($request->hasFile('quotation_file')) {
            $quotationFilePath = FileUploadHelper::storePublicly($request->file('quotation_file'), 'sales_quotations');
        }

        $updateData = [
            'sales_stage'           => $validated['sales_stage'],
            'win_probability'       => $validated['win_probability'],
            'expected_closing_date' => $validated['expected_closing_date'] ?? $project->expected_closing_date,
            'quotation_number'      => $validated['quotation_number'] ?? $project->quotation_number,
            'quotation_amount'      => $validated['quotation_amount'] ?? $project->quotation_amount,
            'quotation_file'        => $quotationFilePath,
            'lost_reason'           => $validated['lost_reason'] ?? $project->lost_reason,
            'lost_competitor'       => $validated['lost_competitor'] ?? $project->lost_competitor,
        ];

        if (!empty($validated['contract_value'])) {
            $updateData['contract_value'] = $validated['contract_value'];
        }

        // Automatic Status Adjustment
        if ($validated['sales_stage'] === 'Closed Won') {
            $updateData['status'] = 'In Progress';
            $updateData['stage'] = 'Deliver';
            $updateData['win_probability'] = 100;
        } elseif ($validated['sales_stage'] === 'Closed Lost') {
            $updateData['status'] = 'Cancelled';
            $updateData['win_probability'] = 0;
        } else {
            $updateData['status'] = 'Opportunity';
        }

        $project->update($updateData);

        // Record CRM Activity Log
        $activityNotes = "Stage diubah dari '{$oldStage}' menjadi '{$validated['sales_stage']}' dengan probabilitas closing {$validated['win_probability']}%.";
        if (!empty($validated['sales_notes'])) {
            $activityNotes .= " Catatan: " . $validated['sales_notes'];
        }

        SalesActivity::create([
            'project_id'    => $project->id,
            'sales_id'      => auth()->id(),
            'activity_type' => $validated['sales_stage'] === 'Quotation' ? 'Quotation Submission' : ($validated['sales_stage'] === 'Negotiation' ? 'Negotiation' : 'Follow Up'),
            'subject'       => "Update Lifecycle: {$validated['sales_stage']}",
            'activity_date' => now(),
            'notes'         => $activityNotes,
            'status'        => 'Completed',
        ]);

        return redirect()->back()
            ->with('success', "Status peluang '{$project->name}' berhasil diperbarui ke tahap {$validated['sales_stage']}.");
    }

    /**
     * 2. Halaman Dedicated: Sales CRM Activity Log
     */
    public function activities(Request $request)
    {
        $user = auth()->user();
        $isManagerial = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader Commercial & Solution', 'PMO', 'Project Manager']);

        $search = $request->input('search');
        $filterType = $request->input('type');
        $filterProject = $request->input('project_id');

        $query = SalesActivity::with(['project', 'sales'])->latest('activity_date');

        if (!$isManagerial) {
            $query->where(function($q) use ($user) {
                $q->where('sales_id', $user->id)
                  ->orWhereHas('project', fn($pq) => $pq->where('sales_name', $user->name)->orWhere('created_by', $user->id));
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('project', fn($pq) => $pq->where('name', 'like', "%{$search}%")->orWhere('client', 'like', "%{$search}%"));
            });
        }

        if ($filterType) {
            $query->where('activity_type', $filterType);
        }

        if ($filterProject) {
            $query->where('project_id', $filterProject);
        }

        $activities = $query->paginate(12)->withQueryString();

        $activeProjectsQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti']);
        if (!$isManagerial) {
            $activeProjectsQuery->where(function($q) use ($user) {
                $q->where('sales_name', $user->name)
                  ->orWhere('created_by', $user->id);
            });
        }
        $activeProjects = $activeProjectsQuery->orderBy('name')->get(['id', 'name', 'client', 'sales_name']);

        $activityTypes = self::$activityTypes;

        return view('sales.activities.index', compact(
            'activities',
            'activeProjects',
            'activityTypes',
            'search',
            'filterType',
            'filterProject',
            'isManagerial'
        ));
    }

    /**
     * Store New CRM Activity
     */
    public function storeActivity(Request $request)
    {
        $validated = $request->validate([
            'project_id'       => 'required|exists:projects,id',
            'activity_type'    => 'required|string',
            'subject'          => 'required|string|max:255',
            'activity_date'    => 'required|date',
            'notes'            => 'nullable|string',
            'next_action'      => 'nullable|string',
            'next_action_date' => 'nullable|date',
            'status'           => 'nullable|string',
        ]);

        $activity = SalesActivity::create([
            'project_id'       => $validated['project_id'],
            'sales_id'         => auth()->id(),
            'activity_type'    => $validated['activity_type'],
            'subject'          => $validated['subject'],
            'activity_date'    => $validated['activity_date'],
            'notes'            => $validated['notes'] ?? null,
            'next_action'      => $validated['next_action'] ?? null,
            'next_action_date' => $validated['next_action_date'] ?? null,
            'status'           => $validated['status'] ?? 'Completed',
        ]);

        return redirect()->back()
            ->with('success', "Aktivitas CRM '{$activity->subject}' berhasil dicatat.");
    }

    /**
     * Store Multiple / Bulk CRM Activities (Spreadsheet / Kronologi Table Mode)
     */
    public function storeBulkActivities(Request $request)
    {
        $validated = $request->validate([
            'project_id'                 => 'required|exists:projects,id',
            'activities'                 => 'required|array|min:1',
            'activities.*.subject'       => 'required|string|max:500',
            'activities.*.activity_type' => 'nullable|string|max:100',
            'activities.*.activity_date' => 'nullable|string',
            'activities.*.time_str'      => 'nullable|string|max:50',
            'activities.*.client_pic'    => 'nullable|string|max:255',
            'activities.*.ipnet_pic'     => 'nullable|string|max:255',
            'activities.*.notes'         => 'nullable|string',
            'activities.*.next_action'   => 'nullable|string|max:255',
        ]);

        $projectId = $validated['project_id'];
        $createdCount = 0;
        $now = now();

        foreach ($validated['activities'] as $row) {
            $subj = trim($row['subject'] ?? '');
            if (empty($subj)) {
                continue;
            }

            // Parse datetime
            $dateStr = !empty($row['activity_date']) ? $row['activity_date'] : $now->toDateString();
            $timeInput = !empty($row['time_str']) ? trim($row['time_str']) : $now->format('H:i');
            $cleanTime = str_replace('.', ':', preg_replace('/[^\d\.\:]/', '', $timeInput));
            if (empty($cleanTime)) {
                $cleanTime = '00:00';
            } elseif (!str_contains($cleanTime, ':')) {
                $cleanTime = $cleanTime . ':00';
            }

            try {
                $fullDateTime = \Carbon\Carbon::parse("{$dateStr} {$cleanTime}");
            } catch (\Exception $e) {
                $fullDateTime = $now;
            }

            // Gabungkan PIC Klien, PIC Internal, & Catatan Aksi ke field notes
            $noteParts = [];
            if (!empty($row['client_pic'])) {
                $noteParts[] = "PIC Klien: " . trim($row['client_pic']);
            }
            if (!empty($row['ipnet_pic'])) {
                $noteParts[] = "PIC IPNET: " . trim($row['ipnet_pic']);
            }
            if (!empty($row['time_str'])) {
                $noteParts[] = "Waktu: " . trim($row['time_str']);
            }
            if (!empty($row['notes'])) {
                $noteParts[] = trim($row['notes']);
            }
            $finalNotes = implode(" | ", $noteParts);

            SalesActivity::create([
                'project_id'       => $projectId,
                'sales_id'         => auth()->id(),
                'activity_type'    => !empty($row['activity_type']) ? $row['activity_type'] : 'Troubleshooting',
                'subject'          => $subj,
                'activity_date'    => $fullDateTime,
                'notes'            => $finalNotes ?: null,
                'next_action'      => !empty($row['next_action']) ? trim($row['next_action']) : null,
                'status'           => 'Completed',
            ]);
            $createdCount++;
        }

        return redirect()->back()
            ->with('success', "Luar biasa! Berhasil mencatat {$createdCount} log kronologi aktivitas sekaligus!");
    }

    /**
     * Update Existing CRM Activity
     */
    public function updateActivity(Request $request, SalesActivity $activity)
    {
        $validated = $request->validate([
            'project_id'       => 'required|exists:projects,id',
            'activity_type'    => 'required|string',
            'subject'          => 'required|string|max:255',
            'activity_date'    => 'required|date',
            'notes'            => 'nullable|string',
            'next_action'      => 'nullable|string',
            'next_action_date' => 'nullable|date',
            'status'           => 'nullable|string',
        ]);

        $activity->update([
            'project_id'       => $validated['project_id'],
            'activity_type'    => $validated['activity_type'],
            'subject'          => $validated['subject'],
            'activity_date'    => $validated['activity_date'],
            'notes'            => $validated['notes'] ?? null,
            'next_action'      => $validated['next_action'] ?? null,
            'next_action_date' => $validated['next_action_date'] ?? null,
            'status'           => $validated['status'] ?? $activity->status,
        ]);

        return redirect()->back()
            ->with('success', "Aktivitas CRM '{$activity->subject}' berhasil diperbarui.");
    }

    /**
     * Delete CRM Activity
     */
    public function destroyActivity(SalesActivity $activity)
    {
        $subject = $activity->subject;
        $activity->delete();

        return redirect()->back()
            ->with('success', "Aktivitas CRM '{$subject}' berhasil dihapus.");
    }

    /**
     * 3. Halaman Dedicated: Commercial Handover to Project (Delivery/PMO or Managed Service)
     */
    public function commercialHandoverIndex(Request $request)
    {
        $user = auth()->user();
        $isManagerial = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole(['Director', 'Direktur', 'HD / Direktur', 'Division Head', 'Group Leader Commercial & Solution', 'PMO', 'Project Manager', 'Lead Maintenance', 'Managed Service']);

        $search = $request->input('search');
        $filterStatus = $request->input('status');
        $filterTarget = $request->input('target');

        $query = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti'])
            ->where(function($q) {
                // Hanya proyek yang sudah DEAL / Closed Won / Terbit Kontrak PO yang masuk ke Handover
                $q->whereIn('sales_stage', ['Closed Won', 'Contract / PO / SPK'])
                  ->orWhereNotNull('po_spk_number')
                  ->orWhere(function($sub) {
                      $sub->whereNotNull('commercial_handover_status')
                          ->where('commercial_handover_status', '!=', 'Draft');
                  });
            })
            ->with(['creator', 'pm', 'commercialHandoverBy', 'msAcceptedBy']);

        if (!$isManagerial) {
            $query->where(function($q) use ($user) {
                $q->where('sales_name', $user->name)
                  ->orWhere('commercial_handover_by', $user->id)
                  ->orWhere('created_by', $user->id);
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%")
                  ->orWhere('po_spk_number', 'like', "%{$search}%");
            });
        }

        if ($filterStatus) {
            $query->where('commercial_handover_status', $filterStatus);
        }

        if ($filterTarget) {
            $query->where('handover_target', $filterTarget);
        }

        $handoverProjects = $query->latest('updated_at')->paginate(10)->withQueryString();

        return view('sales.handover.index', compact('handoverProjects', 'search', 'filterStatus', 'filterTarget', 'isManagerial'));
    }

    /**
     * Process Official Commercial Handover to Delivery/PMO or Managed Service
     */
    public function submitCommercialHandover(Request $request, Project $project)
    {
        $validated = $request->validate([
            'handover_target'          => 'required|string|in:pmo,managed_service',
            'po_spk_number'            => 'required|string|max:255',
            'po_spk_date'              => 'required|date',
            'contract_value'           => 'required|numeric|min:0',
            'po_spk_file'              => FileUploadHelper::fileValidationRule(25600),
            'doc_customer_requirement' => FileUploadHelper::fileValidationRule(25600),
            'doc_commercial_proposal'  => FileUploadHelper::fileValidationRule(25600),
            'doc_negotiation_record'   => FileUploadHelper::fileValidationRule(25600),
            'doc_rfi_rfp'              => FileUploadHelper::fileValidationRule(25600),
            'doc_commercial_package'   => FileUploadHelper::fileValidationRule(25600),
            'billing_terms'            => 'required|string',
            'commercial_terms'         => 'nullable|string',
            'sla_commitment'           => 'nullable|string',
            'special_commitment'       => 'nullable|string',
            'exclusions'               => 'nullable|string',
            'description'              => 'nullable|string',
            // Managed Service Specific Fields
            'sla_tier'                 => 'nullable|string|in:Platinum,Gold,Silver,Bronze',
            'sla_coverage_hours'       => 'nullable|string|max:100',
            'maintenance_frequency'    => 'nullable|string|max:100',
            'service_start_date'       => 'nullable|date',
            'service_end_date'         => 'nullable|date',
        ]);

        $poFilePath = $project->po_spk_file;
        if ($request->hasFile('po_spk_file')) {
            $uploadedPo = $request->file('po_spk_file');
            $poFileName = $uploadedPo->getClientOriginalName();
            $poFileSize = $uploadedPo->getSize();
            $poFileMime = $uploadedPo->getClientMimeType();
            $poFilePath = FileUploadHelper::storePublicly($uploadedPo, 'commercial_contracts');
            
            // Record to project_documents table (Stage 1: Signed Contract / PO)
            \App\Models\ProjectDocument::updateOrCreate(
                [
                    'project_id'   => $project->id,
                    'stage_number' => 1,
                    'document_key' => 'contract_po_so',
                ],
                [
                    'stage_name'     => 'Commercial',
                    'document_title' => 'Contract / PO / SO (Signed)',
                    'file_path'      => $poFilePath,
                    'file_name'      => $poFileName,
                    'file_size'      => $poFileSize ?: 0,
                    'file_mime'      => $poFileMime ?: 'application/octet-stream',
                    'status'         => 'Uploaded',
                    'uploaded_by'    => auth()->id(),
                    'version'        => 1,
                    'is_mandatory'   => true,
                    'notes'          => 'PO/SPK No: ' . $validated['po_spk_number'],
                ]
            );
        }

        // Store other Stage 1 Handover documents if provided
        $stage1DocInputs = [
            'doc_customer_requirement' => [
                'key'       => 'customer_requirement',
                'title'     => 'Customer Requirement',
                'folder'    => 'customer_requirements',
                'mandatory' => true,
            ],
            'doc_commercial_proposal' => [
                'key'       => 'commercial_proposal',
                'title'     => 'Commercial Proposal',
                'folder'    => 'commercial_proposals',
                'mandatory' => true,
            ],
            'doc_negotiation_record' => [
                'key'       => 'negotiation_record',
                'title'     => 'Negotiation Record & MoM',
                'folder'    => 'negotiation_records',
                'mandatory' => false,
            ],
            'doc_rfi_rfp' => [
                'key'       => 'rfi_rfp_rfq',
                'title'     => 'RFI / RFP / RFQ',
                'folder'    => 'rfp_documents',
                'mandatory' => false,
            ],
            'doc_commercial_package' => [
                'key'       => 'commercial_handover_package',
                'title'     => 'Commercial Handover Package',
                'folder'    => 'commercial_handover_packages',
                'mandatory' => true,
            ],
        ];

        foreach ($stage1DocInputs as $inputName => $docMeta) {
            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                $filePath = FileUploadHelper::storePublicly($file, 'project_documents/' . $docMeta['folder']);
                
                \App\Models\ProjectDocument::updateOrCreate(
                    [
                        'project_id'   => $project->id,
                        'stage_number' => 1,
                        'document_key' => $docMeta['key'],
                    ],
                    [
                        'stage_name'     => 'Commercial',
                        'document_title' => $docMeta['title'],
                        'file_path'      => $filePath,
                        'file_name'      => $file->getClientOriginalName(),
                        'file_size'      => $file->getSize(),
                        'file_mime'      => $file->getClientMimeType(),
                        'status'         => 'Uploaded',
                        'uploaded_by'    => auth()->id(),
                        'version'        => 1,
                        'is_mandatory'   => $docMeta['mandatory'],
                        'notes'          => 'Diunggah saat Commercial Handover oleh Sales (' . auth()->user()->name . ')',
                    ]
                );
            }
        }

        $target = $validated['handover_target'];
        $isManagedService = ($target === 'managed_service');

        $updateData = [
            'handover_target'            => $target,
            'po_spk_number'              => $validated['po_spk_number'],
            'po_spk_date'                => $validated['po_spk_date'],
            'contract_value'             => $validated['contract_value'],
            'po_spk_file'                => $poFilePath,
            'billing_terms'              => $validated['billing_terms'],
            'commercial_terms'           => $validated['commercial_terms'] ?? 'Standar Garansi Resmi & Franco Jakarta',
            'sla_commitment'             => $validated['sla_commitment'] ?? ($isManagedService ? 'SLA Response Time 15-30 Menit & Resolusi 4 Jam' : 'Standar SLA Jam Kerja (8x5) Response Time 4 Jam'),
            'special_commitment'         => $validated['special_commitment'] ?? 'Tidak ada komitmen khusus di luar TOR',
            'exclusions'                 => $validated['exclusions'] ?? 'Pengadaan di luar BOM terlampir dikenakan PO terpisah',
            'description'                => $validated['description'] ?? $project->description,
            'sales_stage'                => 'Closed Won',
            'win_probability'            => 100,
            'commercial_handover_status' => 'Submitted',
            'commercial_handover_at'     => now(),
            'commercial_handover_by'     => auth()->id(),
        ];

        if ($isManagedService) {
            $updateData['stage']                 = 'Operate';
            $updateData['status']                = 'Active';
            $updateData['sla_tier']              = $validated['sla_tier'] ?? 'Gold';
            $updateData['sla_coverage_hours']    = $validated['sla_coverage_hours'] ?? '24x7';
            $updateData['maintenance_frequency'] = $validated['maintenance_frequency'] ?? 'Monthly';
            $updateData['service_start_date']    = $validated['service_start_date'] ?? $validated['po_spk_date'];
            $updateData['service_end_date']      = $validated['service_end_date'] ?? null;
            $updateData['ms_handover_status']    = 'Submitted';
        } else {
            $updateData['stage']                 = 'Deliver';
            $updateData['status']                = 'In Progress';
            $updateData['handover_status']       = 'Submitted';
            $updateData['handover_submitted_at'] = now();
        }

        $project->update($updateData);

        // Send Notifications based on handover target
        if ($isManagedService) {
            $msUsers = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Managed Service', 'Lead Maintenance', 'Lead Engineer', 'Director', 'Direktur']);
            })->get();

            foreach ($msUsers as $msUser) {
                Notification::create([
                    'user_id' => $msUser->id,
                    'title'   => 'Serah Terima Kontrak Managed Service Baru',
                    'message' => "Kontrak Managed Service untuk '{$project->name}' ({$project->client}) telah Closed Won dan diserahkan ke Tim Managed Service (SLA Tier: " . ($project->sla_tier ?: 'Gold') . ").",
                    'type'    => 'commercial_handover',
                    'is_read' => false,
                ]);
            }

            return redirect()->back()
                ->with('success', "Berkas Serah Terima Managed Service untuk proyek '{$project->name}' berhasil dikirim ke Tim Managed Service.");
        } else {
            $pmoUsers = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['PMO', 'Project Manager', 'Lead Engineer', 'Director', 'Direktur']);
            })->get();

            foreach ($pmoUsers as $pmo) {
                Notification::create([
                    'user_id' => $pmo->id,
                    'title'   => 'Commercial Handover Baru dari Sales',
                    'message' => "Proyek Implementasi '{$project->name}' ({$project->client}) telah Closed Won dan diserahkan ke Tim Delivery & PMO.",
                    'type'    => 'commercial_handover',
                    'is_read' => false,
                ]);
            }

            return redirect()->back()
                ->with('success', "Berkas Commercial Handover untuk proyek '{$project->name}' berhasil dikirim ke Tim Delivery & PMO.");
        }
    }
}
