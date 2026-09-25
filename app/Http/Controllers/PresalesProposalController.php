<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Division;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileUploadHelper;

class PresalesProposalController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isArchitect = $user && $user->hasAnyRole(['Solution Architect', 'Solutions Architect', 'SA', 'Tech Develop']);
        $isPresales = $user && $user->hasAnyRole(['Presales', 'Pre-Sales']);
        $isManagerial = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole([
            'Director', 'Direktur', 'HD / Direktur', 'Division Head', 
            'Lead Presales', 'Group Leader Commercial & Solution', 'Super Admin', 'Admin'
        ]);
        $isManagerialOrPresales = $isManagerial || $isPresales || $isArchitect;

        $search     = $request->input('search');
        $tab        = $request->input('tab', 'pending'); // pending, submitted, won, lost
        $divisionId = $request->input('division_id');

        $query = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti']);

        if (!$isManagerialOrPresales) {
            $query->where(function ($q) use ($user) {
                $q->where('sales_name', $user->name)
                  ->orWhere('created_by', $user->id);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%")
                  ->orWhere('sales_name', 'like', "%{$search}%");
            });
        }

        if ($divisionId) {
            $query->where('division_id', $divisionId);
        }

        // Tab Filtering (Role-Aware)
        if ($isArchitect && !$isPresales && !$isManagerial) {
            // Solution Architect specific filtering
            if ($tab === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('handover_data->technical_assignments->architect->document_path')
                      ->orWhere('handover_data->technical_assignments->architect->document_path', '');
                })->whereIn('status', ['Opportunity', 'Draft', 'Planning', 'Deliver'])
                  ->whereNotIn('sales_stage', ['Closed Won', 'Closed Lost'])
                  ->where('name', 'not like', '%On Going Project%')
                  ->where('name', 'not like', '%Closed Project%')
                  ->where('name', 'not like', '%Preventive Maintenance%');
            } elseif ($tab === 'submitted') {
                $query->whereNotNull('handover_data->technical_assignments->architect->document_path')
                      ->where('handover_data->technical_assignments->architect->document_path', '!=', '')
                      ->where('sales_stage', '!=', 'Closed Lost');
            }
        } elseif ($isPresales && !$isArchitect && !$isManagerial) {
            // Pre-Sales specific filtering
            if ($tab === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('proposal_file')
                      ->where(function ($sq) {
                          $sq->whereNull('handover_data->technical_assignments->presales->document_path')
                             ->orWhere('handover_data->technical_assignments->presales->document_path', '');
                      });
                })->whereIn('status', ['Opportunity', 'Draft', 'Planning'])
                  ->where('stage', '!=', 'Deliver')
                  ->whereNotIn('sales_stage', ['Closed Won', 'Closed Lost'])
                  ->where('name', 'not like', '%On Going Project%')
                  ->where('name', 'not like', '%Closed Project%')
                  ->where('name', 'not like', '%Preventive Maintenance%');
            } elseif ($tab === 'submitted') {
                $query->where(function ($q) {
                    $q->whereNotNull('proposal_file')
                      ->orWhere(function ($sq) {
                          $sq->whereNotNull('handover_data->technical_assignments->presales->document_path')
                             ->where('handover_data->technical_assignments->presales->document_path', '!=', '');
                      });
                })->where('sales_stage', '!=', 'Closed Lost');
            }
        } else {
            // Global / Managerial / Admin
            if ($tab === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('proposal_file')
                      ->orWhereNull('handover_data->technical_assignments->architect->document_path');
                })->whereIn('status', ['Opportunity', 'Draft', 'Planning', 'Deliver'])
                  ->whereNotIn('sales_stage', ['Closed Won', 'Closed Lost'])
                  ->where('name', 'not like', '%On Going Project%')
                  ->where('name', 'not like', '%Closed Project%')
                  ->where('name', 'not like', '%Preventive Maintenance%');
            } elseif ($tab === 'submitted') {
                $query->where(function ($q) {
                    $q->whereNotNull('proposal_file')
                      ->orWhereNotNull('handover_data->technical_assignments->architect->document_path')
                      ->orWhereNotNull('handover_data->technical_assignments->presales->document_path');
                })->where('sales_stage', '!=', 'Closed Lost');
            }
        }

        if ($tab === 'won') {
            $query->where(function ($q) {
                $q->where('stage', 'Deliver')
                  ->orWhereIn('status', ['On Progress', 'Completed', 'Closed Won'])
                  ->orWhere('sales_stage', 'Closed Won')
                  ->orWhere('name', 'like', '%On Going Project%')
                  ->orWhere('name', 'like', '%Closed Project%')
                  ->orWhere('name', 'like', '%Preventive Maintenance%');
            })->where('sales_stage', '!=', 'Closed Lost');
        } elseif ($tab === 'lost') {
            $query->where(function ($q) {
                $q->where('sales_stage', 'Closed Lost')
                  ->orWhereIn('status', ['Cancelled', 'Rejected', 'Closed Lost', 'Lost', 'Drop']);
            });
        }

        $perPage = (int) $request->input('per_page', 10);
        if ($perPage <= 0) {
            $perPage = 10;
        }

        $projects = $query->latest()->paginate($perPage)->withQueryString();

        // Counter stats (Role-Aware)
        $baseQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti']);
        if (!$isManagerialOrPresales) {
            $baseQuery->where(function ($q) use ($user) {
                $q->where('sales_name', $user->name)
                  ->orWhere('created_by', $user->id);
            });
        }

        $pendingCount = 0;
        $submittedCount = 0;

        if ($isArchitect && !$isPresales && !$isManagerial) {
            $pendingCount = (clone $baseQuery)->where(function ($q) {
                $q->whereNull('handover_data->technical_assignments->architect->document_path')
                  ->orWhere('handover_data->technical_assignments->architect->document_path', '');
            })->whereIn('status', ['Opportunity', 'Draft', 'Planning', 'Deliver'])
              ->whereNotIn('sales_stage', ['Closed Won', 'Closed Lost'])
              ->where('name', 'not like', '%On Going Project%')
              ->where('name', 'not like', '%Closed Project%')
              ->where('name', 'not like', '%Preventive Maintenance%')
              ->count();

            $submittedCount = (clone $baseQuery)->whereNotNull('handover_data->technical_assignments->architect->document_path')
                                                ->where('handover_data->technical_assignments->architect->document_path', '!=', '')
                                                ->where('sales_stage', '!=', 'Closed Lost')
                                                ->count();
        } elseif ($isPresales && !$isArchitect && !$isManagerial) {
            $pendingCount = (clone $baseQuery)->where(function ($q) {
                $q->whereNull('proposal_file')
                  ->where(function ($sq) {
                      $sq->whereNull('handover_data->technical_assignments->presales->document_path')
                         ->orWhere('handover_data->technical_assignments->presales->document_path', '');
                  });
            })->whereIn('status', ['Opportunity', 'Draft', 'Planning'])
              ->where('stage', '!=', 'Deliver')
              ->whereNotIn('sales_stage', ['Closed Won', 'Closed Lost'])
              ->where('name', 'not like', '%On Going Project%')
              ->where('name', 'not like', '%Closed Project%')
              ->where('name', 'not like', '%Preventive Maintenance%')
              ->count();

            $submittedCount = (clone $baseQuery)->where(function ($q) {
                $q->whereNotNull('proposal_file')
                  ->orWhere(function ($sq) {
                      $sq->whereNotNull('handover_data->technical_assignments->presales->document_path')
                         ->where('handover_data->technical_assignments->presales->document_path', '!=', '');
                  });
            })->where('sales_stage', '!=', 'Closed Lost')->count();
        } else {
            $pendingCount = (clone $baseQuery)->where(function ($q) {
                $q->whereNull('proposal_file')
                  ->orWhereNull('handover_data->technical_assignments->architect->document_path');
            })->whereIn('status', ['Opportunity', 'Draft', 'Planning', 'Deliver'])
              ->whereNotIn('sales_stage', ['Closed Won', 'Closed Lost'])
              ->where('name', 'not like', '%On Going Project%')
              ->where('name', 'not like', '%Closed Project%')
              ->where('name', 'not like', '%Preventive Maintenance%')
              ->count();

            $submittedCount = (clone $baseQuery)->where(function ($q) {
                $q->whereNotNull('proposal_file')
                  ->orWhereNotNull('handover_data->technical_assignments->architect->document_path')
                  ->orWhereNotNull('handover_data->technical_assignments->presales->document_path');
            })->where('sales_stage', '!=', 'Closed Lost')->count();
        }

        $counts = [
            'all'       => (clone $baseQuery)->count(),
            'pending'   => $pendingCount,
            'submitted' => $submittedCount,
            'won'       => (clone $baseQuery)->where(function ($q) {
                                $q->where('stage', 'Deliver')
                                  ->orWhereIn('status', ['On Progress', 'Completed', 'Closed Won'])
                                  ->orWhere('sales_stage', 'Closed Won')
                                  ->orWhere('name', 'like', '%On Going Project%')
                                  ->orWhere('name', 'like', '%Closed Project%')
                                  ->orWhere('name', 'like', '%Preventive Maintenance%');
                            })->where('sales_stage', '!=', 'Closed Lost')->count(),
            'lost'      => (clone $baseQuery)->where(function ($q) {
                $q->where('sales_stage', 'Closed Lost')
                  ->orWhereIn('status', ['Cancelled', 'Rejected', 'Closed Lost', 'Lost', 'Drop']);
            })->count(),
        ];

        $divisions = Division::all();

        return view('presales.proposals.index', [
            'projects'               => $projects,
            'counts'                 => $counts,
            'tab'                    => $tab,
            'divisions'              => $divisions,
            'isManagerialOrPresales' => $isManagerialOrPresales,
            'isArchitect'            => $isArchitect,
            'isPresales'             => $isPresales,
            'isManagerial'           => $isManagerial,
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $user = auth()->user();
        if ($user && $user->hasAnyRole(['Sales', 'BDM']) && !$user->hasAnyRole(['Presales', 'Solution Architect', 'Solutions Architect', 'PMO', 'Project Manager', 'Direktur', 'HD / Direktur', 'Group Leader', 'Lead Engineer'])) {
            return back()->with('error', 'Akses Dibatasi: Dokumen teknis dan SOW hanya dapat disusun dan diunggah oleh tim Presales & Solution Architect.');
        }

        $validated = $request->validate([
            'role_type'      => 'nullable|in:presales,architect',
            'proposal_notes' => 'nullable|string',
            'mandays'        => 'nullable|integer|min:1',
            'proposal_file'  => FileUploadHelper::fileValidationRule(51200),
        ], [
            'proposal_file.max'   => 'Ukuran file maksimal adalah 50MB.',
            'mandays.min'         => 'Estimasi mandays minimal adalah 1 hari.',
        ]);

        if (empty($validated['proposal_notes'])) {
            $validated['proposal_notes'] = $project->proposal_notes ?: ($project->description ?: 'Dokumen teknis & SOW telah disusun.');
        }

        if (empty($validated['mandays'])) {
            $validated['mandays'] = $project->mandays ?: 10;
        }

        $now = now()->format('d M Y H:i');
        $uploaderName = $user ? $user->name : 'Tim Solusi Teknis';

        $handoverData = is_array($project->handover_data) ? $project->handover_data : (json_decode($project->handover_data ?? '', true) ?: []);
        $technical = $handoverData['technical_assignments'] ?? [];

        $isUploaderArchitect = $user && (
            $user->hasAnyRole(['Solution Architect', 'Solutions Architect', 'SA'])
            || str_contains(strtolower($user->name), 'aris')
            || (!empty($technical['architect']['assigned_user_id']) && $user->id == $technical['architect']['assigned_user_id'])
        );

        $roleType = $validated['role_type'] ?? ($isUploaderArchitect ? 'architect' : 'presales');

        if ($request->hasFile('proposal_file')) {
            $uploadedFile = $request->file('proposal_file');
            $origName     = $uploadedFile->getClientOriginalName();
            $ext          = $uploadedFile->getClientOriginalExtension();
            $size         = $uploadedFile->getSize();

            $path = FileUploadHelper::storePublicly($uploadedFile, 'proposals');

            // Catat di project_documents (Stage 2: Solution)
            if (\Illuminate\Support\Facades\Schema::hasTable('project_documents')) {
                $docPayload = [
                    'project_id'     => $project->id,
                    'stage_number'   => 2,
                    'stage_name'     => 'Solution',
                    'document_key'   => ($roleType === 'architect') ? 'solution_architecture' : 'technical_proposal',
                    'document_title' => ($roleType === 'architect') ? 'Desain Arsitektur & Topologi' : 'Proposal Teknis & Ruang Lingkup (SOW)',
                    'file_name'      => $origName,
                    'file_path'      => $path,
                    'file_size'      => $size,
                    'file_extension' => $ext,
                    'status'         => 'Uploaded',
                    'notes'          => $validated['proposal_notes'],
                    'uploaded_by'    => $user?->id,
                    'uploaded_at'    => now(),
                ];
                if (\Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'name')) {
                    $docPayload['name'] = $origName;
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'document_type')) {
                    $docPayload['document_type'] = ($roleType === 'architect') ? 'Topology & Sizing' : 'Technical Proposal';
                }
                \App\Models\ProjectDocument::create($docPayload);
            }

            // Simpan sesuai role
            if ($roleType === 'architect') {
                if (!empty($technical['architect']['document_path'])) {
                    FileUploadHelper::delete($technical['architect']['document_path']);
                }
                $technical['architect'] = array_merge($technical['architect'] ?? [], [
                    'status'         => 'Completed',
                    'document_path'  => $path,
                    'document_name'  => $origName,
                    'document_title' => 'Desain Arsitektur & Topologi',
                    'completed_at'   => $now,
                    'notes'          => $validated['proposal_notes'],
                ]);
            } else {
                if ($project->proposal_file) {
                    FileUploadHelper::delete($project->proposal_file);
                }
                $technical['presales'] = array_merge($technical['presales'] ?? [], [
                    'status'         => 'Completed',
                    'document_path'  => $path,
                    'document_name'  => $origName,
                    'document_title' => 'Proposal Teknis & Ruang Lingkup (SOW)',
                    'completed_at'   => $now,
                    'notes'          => $validated['proposal_notes'],
                ]);
                $validated['proposal_file'] = $path;
            }

            // Status verifikasi BD otomatis beralih ke 'Pending Verification'
            $technical['bd_verification'] = [
                'status'         => 'Pending Verification',
                'submitted_at'   => $now,
                'submitted_by'   => $uploaderName,
                'verified_by'    => null,
                'verified_at'    => null,
                'notes'          => null,
            ];

            $handoverData['technical_assignments'] = $technical;
            $validated['handover_data'] = $handoverData;

            // 1. Kirim notifikasi ke PIC BD
            $bdUserId = $project->bdm_id;
            if (!$bdUserId) {
                $bdUser = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['BDM', 'BusDev', 'Business Development']))->first();
                $bdUserId = $bdUser?->id;
            }
            if ($bdUserId && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                \App\Models\Notification::create([
                    'user_id' => $bdUserId,
                    'title'   => \Illuminate\Support\Str::limit("Verifikasi Dokumen Teknis: " . $project->name, 240),
                    'message' => \Illuminate\Support\Str::limit("{$uploaderName} telah mengunggah berkas untuk proyek '{$project->name}'. Silakan verifikasi kelayakan dokumen.", 240),
                    'url'     => route('projects.show', $project->id),
                    'is_read' => false,
                ]);
            }

            // 2. Kirim notifikasi ke rekan tim solusi
            $targetUserId = ($roleType === 'architect') 
                ? ($technical['presales']['assigned_user_id'] ?? null) 
                : ($technical['architect']['assigned_user_id'] ?? null);

            if ($targetUserId && $targetUserId != ($user?->id) && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                \App\Models\Notification::create([
                    'user_id' => $targetUserId,
                    'title'   => \Illuminate\Support\Str::limit("Dokumen Teknis Diunggah: " . $project->name, 240),
                    'message' => \Illuminate\Support\Str::limit("{$uploaderName} telah mengunggah berkas untuk proyek '{$project->name}'.", 240),
                    'url'     => route('projects.show', $project->id),
                    'is_read' => false,
                ]);
            }

            // 3. Kirim notifikasi ke Sales pembuat proyek
            $salesUserId = $project->creator_id ?? $project->created_by;
            if ($salesUserId && $salesUserId != ($user?->id) && $salesUserId != $bdUserId && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                \App\Models\Notification::create([
                    'user_id' => $salesUserId,
                    'title'   => \Illuminate\Support\Str::limit("Dokumen Solusi Diunggah: " . $project->name, 240),
                    'message' => \Illuminate\Support\Str::limit("{$uploaderName} telah mengunggah berkas untuk proyek '{$project->name}'. Berkas sedang diverifikasi oleh PIC BD.", 240),
                    'url'     => route('projects.show', $project->id),
                    'is_read' => false,
                ]);
            }
        }

        if ($roleType === 'presales') {
            $validated['presales_status'] = 'Submitted';
        }
        $project->update($validated);

        $docName = ($roleType === 'architect') ? 'Desain Arsitektur' : 'Proposal Teknis & SOW';
        return back()->with('success', "{$docName} untuk {$project->name} berhasil disimpan dan diteruskan ke PIC BD & Sales!");
    }

    public function download(Request $request, Project $project)
    {
        $type = $request->input('type');
        $handoverData = is_array($project->handover_data) ? $project->handover_data : (json_decode($project->handover_data ?? '', true) ?: []);
        $technical = $handoverData['technical_assignments'] ?? [];
        
        $user = auth()->user();
        $isArchitect = $user && ($user->hasAnyRole(['Solution Architect', 'Solutions Architect', 'SA']) || str_contains(strtolower($user->name), 'aris'));

        $targetFile = null;
        if ($type === 'architect' || (!$type && $isArchitect && !empty($technical['architect']['document_path']))) {
            $targetFile = $technical['architect']['document_path'] ?? null;
        } elseif ($type === 'presales' || (!$type && !$isArchitect && !empty($technical['presales']['document_path']))) {
            $targetFile = $technical['presales']['document_path'] ?? $project->proposal_file;
        }

        if (!$targetFile) {
            $targetFile = $project->proposal_file 
                ?: ($technical['architect']['document_path'] ?? ($technical['presales']['document_path'] ?? null));
        }

        if (!$targetFile) {
            return back()->with('error', 'Berkas dokumen teknis belum tersedia.');
        }

        $filePath = storage_path('app/public/' . $targetFile);
        if (!file_exists($filePath)) {
            $filePath = public_path('storage/' . $targetFile);
        }

        if (!file_exists($filePath)) {
            return back()->with('error', 'Berkas dokumen teknis tidak ditemukan di server.');
        }

        return response()->download($filePath);
    }

    public function destroyFile(Request $request, Project $project)
    {
        $user = auth()->user();
        if ($user && $user->hasAnyRole(['Sales', 'BusDev']) && !$user->hasAnyRole(['Presales', 'Solution Architect', 'Solutions Architect', 'PMO', 'Project Manager', 'Direktur', 'HD / Direktur', 'Group Leader', 'Lead Engineer'])) {
            return back()->with('error', 'Akses Dibatasi: Hanya tim Solusi Teknis / Presales yang dapat menghapus berkas.');
        }

        $isArchitect = $user && ($user->hasAnyRole(['Solution Architect', 'Solutions Architect', 'SA']) || str_contains(strtolower($user->name), 'aris'));
        $type = $request->input('type', $isArchitect ? 'architect' : 'presales');

        $handoverData = is_array($project->handover_data) ? $project->handover_data : (json_decode($project->handover_data ?? '', true) ?: []);
        $technical = $handoverData['technical_assignments'] ?? [];

        if ($type === 'architect') {
            if (!empty($technical['architect']['document_path'])) {
                FileUploadHelper::delete($technical['architect']['document_path']);
                $technical['architect']['document_path'] = null;
                $technical['architect']['document_name'] = null;
                $technical['architect']['status'] = 'Assigned';
            }
        } else {
            if ($project->proposal_file) {
                FileUploadHelper::delete($project->proposal_file);
            }
            if (!empty($technical['presales']['document_path'])) {
                FileUploadHelper::delete($technical['presales']['document_path']);
                $technical['presales']['document_path'] = null;
                $technical['presales']['document_name'] = null;
                $technical['presales']['status'] = 'Assigned';
            }
            $project->proposal_file = null;
            $project->presales_status = 'Pending';
        }

        $handoverData['technical_assignments'] = $technical;
        $project->handover_data = $handoverData;
        $project->save();

        return back()->with('success', 'Berkas dokumen teknis untuk ' . $project->name . ' berhasil dihapus.');
    }
}
