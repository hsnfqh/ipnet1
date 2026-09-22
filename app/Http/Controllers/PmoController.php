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
     * Tampilan Dashboard Utama PMO (Project Control Tower)
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        // 1. Ambil data proyek aktif PMO (Hanya Tahap Deliver & Operate)
        $query = Project::with([
            'pm:id,name,email',
            'division:id,name,code',
            'tasks.engineer:id,name',
            'creator:id,name',
        ])->whereIn('stage', ['Deliver', 'Operate']);

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

        // Format data project untuk dashboard
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

            return [
                'id'                            => $p->id,
                'name'                          => $p->name,
                'so_code'                       => $p->so_number ?: ($p->po_number ?: 'SO-IPNET-' . str_pad($p->id, 4, '0', STR_PAD_LEFT)),
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
                'division'                      => $p->division ? $p->division->name : 'Lintas Divisi',
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

        // 4. Hitung Utilisasi Engineer Lintas Divisi
        $allEngineers = User::role(['Engineer', 'Engineer L1', 'Engineer L2', 'Maintenance'])->get();
        $assignedEngineerIds = Task::whereHas('project', function($q) {
            $q->whereNull('deleted_at');
        })->whereIn('status', ['In Progress', 'Testing', 'Review'])
          ->pluck('engineer_id')
          ->filter()
          ->unique()
          ->toArray();

        $activeEngineersCount = count($assignedEngineerIds);
        $totalEngineersCount  = $allEngineers->count();
        $standbyEngineersCount= max(0, $totalEngineersCount - $activeEngineersCount);

        // 5. Persebaran 4-Tahap Siklus Ver 2.0
        $stageCounts = [
            'Acquire' => $allProjects->where('stage', 'Acquire')->count(),
            'Design'  => $allProjects->where('stage', 'Design')->count(),
            'Deliver' => $allProjects->where('stage', 'Deliver')->count(),
            'Operate' => $allProjects->where('stage', 'Operate')->count(),
        ];

        // 6. Data Master untuk Filter & Form
        $divisions = Division::all();
        $pmList = User::role(['PMO', 'Project Manager'])->get(['id', 'name', 'email']);

        return view('pmo.dashboard', compact(
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
            'activeEngineersCount',
            'standbyEngineersCount',
            'totalEngineersCount',
            'stageCounts',
            'divisions',
            'pmList'
        ));
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
        ]);

        $project->update([
            'handover_status'      => 'Approved',
            'handover_approved_at' => now(),
            'handover_approved_by' => auth()->id(),
            'stage'                => 'Deliver',
            'status'               => 'On Progress',
            'process_status'       => 'In Progress',
            'pm_id'                => $validated['pm_id'],
            'division_id'          => $validated['division_id'],
        ]);

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
                'message' => 'Handover berhasil disahkan! Proyek resmi memasuki tahap Deliver.',
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
        $validated = $request->validate([
            'documents_checklist' => 'required|array',
            'special_notes'       => 'nullable|string',
        ]);

        $project->update([
            'handover_data'       => $validated['documents_checklist'],
            'documents_checklist' => $validated['documents_checklist'],
            'special_notes'       => $validated['special_notes'] ?? $project->special_notes,
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

