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
        $isManagerialOrPresales = \App\Helpers\ScopeHelper::isGlobal($user) || $user->hasAnyRole([
            'Director', 'Direktur', 'HD / Direktur', 'Division Head', 
            'Presales', 'Solution Architect', 'Solutions Architect', 
            'Lead Presales', 'Group Leader Commercial & Solution', 'PMO', 'Project Manager'
        ]);

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

        // Tab Filtering
        if ($tab === 'pending') {
            $query->whereNull('proposal_file')
                  ->whereIn('status', ['Opportunity', 'Draft', 'Planning'])
                  ->where('stage', '!=', 'Deliver')
                  ->whereNotIn('sales_stage', ['Closed Won', 'Closed Lost'])
                  ->where('name', 'not like', '%On Going Project%')
                  ->where('name', 'not like', '%Closed Project%')
                  ->where('name', 'not like', '%Preventive Maintenance%');
        } elseif ($tab === 'submitted') {
            $query->whereNotNull('proposal_file')
                  ->where('sales_stage', '!=', 'Closed Lost');
        } elseif ($tab === 'won') {
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

        // Counter stats
        $baseQuery = Project::whereNotIn('name', ['DAY OFF', 'Day Off', 'Day Off / Cuti']);
        if (!$isManagerialOrPresales) {
            $baseQuery->where(function ($q) use ($user) {
                $q->where('sales_name', $user->name)
                  ->orWhere('created_by', $user->id);
            });
        }

        $counts = [
            'all'       => (clone $baseQuery)->count(),
            'pending'   => (clone $baseQuery)->whereNull('proposal_file')
                                             ->whereIn('status', ['Opportunity', 'Draft', 'Planning'])
                                             ->where('stage', '!=', 'Deliver')
                                             ->whereNotIn('sales_stage', ['Closed Won', 'Closed Lost'])
                                             ->where('name', 'not like', '%On Going Project%')
                                             ->where('name', 'not like', '%Closed Project%')
                                             ->where('name', 'not like', '%Preventive Maintenance%')
                                             ->count(),
            'submitted' => (clone $baseQuery)->whereNotNull('proposal_file')->where('sales_stage', '!=', 'Closed Lost')->count(),
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
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $user = auth()->user();
        if ($user && $user->hasAnyRole(['Sales', 'BDM']) && !$user->hasAnyRole(['Presales', 'Solution Architect', 'Solutions Architect', 'PMO', 'Project Manager', 'Direktur', 'HD / Direktur', 'Group Leader', 'Lead Engineer'])) {
            return back()->with('error', 'Akses Dibatasi: Dokumen teknis dan SOW hanya dapat disusun dan diunggah oleh tim Presales & Solution Architect.');
        }

        $validated = $request->validate([
            'proposal_notes' => 'nullable|string',
            'mandays'        => 'nullable|integer|min:1',
            'proposal_file'  => FileUploadHelper::fileValidationRule(51200),
        ], [
            'proposal_file.max'   => 'Ukuran file maksimal adalah 50MB.',
            'mandays.min'         => 'Estimasi mandays minimal adalah 1 hari.',
        ]);

        if (empty($validated['proposal_notes'])) {
            $validated['proposal_notes'] = $project->proposal_notes ?: ($project->description ?: 'Proposal teknis & SOW telah disusun.');
        }

        if (empty($validated['mandays'])) {
            $validated['mandays'] = $project->mandays ?: 10;
        }

        $now = now()->format('d M Y H:i');
        $uploaderName = $user ? $user->name : 'Pre-Sales Specialist';

        if ($request->hasFile('proposal_file')) {
            $uploadedFile = $request->file('proposal_file');
            $origName     = $uploadedFile->getClientOriginalName();
            $ext          = $uploadedFile->getClientOriginalExtension();
            $size         = $uploadedFile->getSize();

            // Hapus file lama jika ada
            if ($project->proposal_file) {
                FileUploadHelper::delete($project->proposal_file);
            }
            $path = FileUploadHelper::storePublicly($uploadedFile, 'proposals');
            $validated['proposal_file'] = $path;

            // Catat di project_documents (Stage 2: Solution)
            if (\Illuminate\Support\Facades\Schema::hasTable('project_documents')) {
                $docPayload = [
                    'project_id'     => $project->id,
                    'stage_number'   => 2,
                    'stage_name'     => 'Solution',
                    'document_key'   => 'technical_proposal',
                    'document_title' => 'Proposal Teknis & Ruang Lingkup (SOW)',
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
                    $docPayload['document_type'] = 'Technical Proposal';
                }
                \App\Models\ProjectDocument::create($docPayload);
            }

            // Sinkronkan ke penugasan tim solusi (handover_data) agar SA/Presales/Sales tidak perlu kerja 2x
            $handoverData = is_array($project->handover_data) ? $project->handover_data : (json_decode($project->handover_data ?? '', true) ?: []);
            $technical = $handoverData['technical_assignments'] ?? [];
            $technical['presales'] = array_merge($technical['presales'] ?? [], [
                'status'         => 'Completed',
                'document_path'  => $path,
                'document_name'  => $origName,
                'document_title' => 'Proposal Teknis & Ruang Lingkup (SOW)',
                'completed_at'   => $now,
                'notes'          => $validated['proposal_notes'],
            ]);

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

            // Kirim notifikasi ke PIC BD
            $bdUserId = $project->bdm_id;
            if (!$bdUserId) {
                $bdUser = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('name', ['BDM', 'BusDev', 'Business Development']))->first();
                $bdUserId = $bdUser?->id;
            }
            if ($bdUserId && \Illuminate\Support\Facades\Schema::hasTable('notifications')) {
                \App\Models\Notification::create([
                    'user_id' => $bdUserId,
                    'title'   => \Illuminate\Support\Str::limit("Verifikasi Proposal Teknis: " . $project->name, 240),
                    'message' => \Illuminate\Support\Str::limit("{$uploaderName} telah mengunggah berkas proposal & SOW untuk proyek '{$project->name}'. Silakan verifikasi kelayakan dokumen.", 240),
                    'url'     => route('projects.show', $project->id),
                    'is_read' => false,
                ]);
            }
        }

        $validated['presales_status'] = 'Submitted';
        $project->update($validated);

        return back()->with('success', 'Proposal teknis & SOW untuk ' . $project->name . ' berhasil disimpan dan diteruskan ke PIC BD & Sales!');
    }

    public function download(Project $project)
    {
        if (!$project->proposal_file) {
            return back()->with('error', 'Berkas proposal teknis belum tersedia.');
        }

        $filePath = storage_path('app/public/' . $project->proposal_file);
        if (!file_exists($filePath)) {
            $filePath = public_path('storage/' . $project->proposal_file);
        }

        if (!file_exists($filePath)) {
            return back()->with('error', 'Berkas proposal teknis tidak ditemukan di server.');
        }

        return response()->download($filePath);
    }

    public function destroyFile(Project $project)
    {
        $user = auth()->user();
        if ($user && $user->hasAnyRole(['Sales', 'BusDev']) && !$user->hasAnyRole(['Presales', 'PMO', 'Project Manager', 'Direktur', 'HD / Direktur', 'Group Leader', 'Lead Engineer'])) {
            return back()->with('error', 'Akses Dibatasi: Hanya tim Presales Engineering yang dapat menghapus berkas proposal teknis.');
        }

        if ($project->proposal_file) {
            FileUploadHelper::delete($project->proposal_file);
        }

        $project->update([
            'proposal_file' => null,
            'presales_status' => 'Pending',
        ]);

        return back()->with('success', 'Berkas proposal teknis untuk ' . $project->name . ' berhasil dihapus.');
    }
}
