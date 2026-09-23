<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectDocument;
use App\Services\ProjectDocumentFlowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProjectDocumentController extends Controller
{
    /**
     * Dapatkan daftar seluruh dokumen 6 tahap untuk sebuah proyek (JSON API)
     */
    public function getProjectFlow(Project $project): JsonResponse
    {
        $flowData = ProjectDocumentFlowService::getProjectDocumentProgress($project);

        return response()->json([
            'success' => true,
            'project' => [
                'id'     => $project->id,
                'name'   => $project->name,
                'client' => $project->client,
                'stage'  => $project->stage,
            ],
            'stages' => $flowData,
        ]);
    }

    /**
     * Upload / Perbarui Dokumen untuk slot tertentu atau upload multiple attachment
     */
    public function upload(Request $request, Project $project)
    {
        $validated = $request->validate([
            'document_id'      => 'nullable',
            'stage_number'     => 'nullable|integer|min:1|max:6',
            'document_key'     => 'nullable|string|max:100',
            'document_file'    => 'nullable|file|max:51200', // max 50MB
            'document_files'   => 'nullable|array',
            'document_files.*' => 'file|max:51200',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $files = [];
        if ($request->hasFile('document_files')) {
            $files = $request->file('document_files');
        } elseif ($request->hasFile('document_file')) {
            $files = [$request->file('document_file')];
        }

        if (empty($files)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Silakan pilih berkas untuk diunggah.'], 422);
            }
            return redirect()->back()->with('error', 'Silakan pilih berkas untuk diunggah.');
        }

        $stageNumber = !empty($validated['stage_number']) ? (int)$validated['stage_number'] : 1;
        $documentKey = !empty($validated['document_key']) ? $validated['document_key'] : 'lampiran_pendukung';
        $stages = ProjectDocumentFlowService::getStagesDefinition();
        $stageInfo = $stages[$stageNumber] ?? ['stage_name' => 'Commercial'];

        $hasNameCol        = \Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'name');
        $hasDocTitleCol    = \Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'document_title');
        $hasDocKeyCol      = \Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'document_key');
        $hasStageNumCol    = \Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'stage_number');
        $hasStageNameCol   = \Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'stage_name');
        $hasIsMandatoryCol = \Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'is_mandatory');
        $hasDocTypeCol     = \Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'document_type');
        $hasFileNameCol    = \Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'file_name');
        $hasFileSizeCol    = \Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'file_size');
        $hasFileExtCol     = \Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'file_extension');
        $hasStatusCol      = \Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'status');
        $hasUploadedByCol  = \Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'uploaded_by');
        $hasUploadedAtCol  = \Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'uploaded_at');
        $hasNotesCol       = \Illuminate\Support\Facades\Schema::hasColumn('project_documents', 'notes');

        $uploadedDocs = [];

        foreach ($files as $index => $file) {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();
            $cleanTitle = pathinfo($originalName, PATHINFO_FILENAME);

            // Jika upload tunggal dengan slot spesifik
            $doc = null;
            if (count($files) === 1 && !empty($validated['document_id'])) {
                $doc = ProjectDocument::find($validated['document_id']);
            } elseif (count($files) === 1 && $documentKey !== 'lampiran_pendukung' && $hasDocKeyCol && $hasStageNumCol) {
                $doc = ProjectDocument::where('project_id', $project->id)
                    ->where('stage_number', $stageNumber)
                    ->where('document_key', $documentKey)
                    ->first();
            }

            if (!$doc) {
                $doc = new ProjectDocument();
                $doc->project_id = $project->id;
                if ($hasDocKeyCol) {
                    $doc->document_key = 'attachment_' . \Illuminate\Support\Str::slug($cleanTitle) . '_' . uniqid();
                }
                if ($hasStageNumCol) {
                    $doc->stage_number = $stageNumber;
                }
                if ($hasStageNameCol) {
                    $doc->stage_name = $stageInfo['stage_name'] ?? 'Commercial';
                }
                if ($hasIsMandatoryCol) {
                    $doc->is_mandatory = false;
                }
            } else {
                // Hapus file lama jika ada
                if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }
            }

            $path = $file->store("project_documents/{$project->id}/stage_{$stageNumber}", 'public');

            $doc->file_path = $path;
            if ($hasNameCol) {
                $doc->name = $originalName;
            }
            if ($hasDocTitleCol) {
                $doc->document_title = $cleanTitle ?: ('Attachment ' . ($index + 1));
            }
            if ($hasDocTypeCol) {
                $doc->document_type = 'Attachment';
            }
            if ($hasFileNameCol) {
                $doc->file_name = $originalName;
            }
            if ($hasFileSizeCol) {
                $doc->file_size = $size;
            }
            if ($hasFileExtCol) {
                $doc->file_extension = strtolower($extension);
            }
            if ($hasStatusCol) {
                $doc->status = 'Uploaded';
            }
            if ($hasUploadedByCol) {
                $doc->uploaded_by = auth()->id();
            }
            if ($hasUploadedAtCol) {
                $doc->uploaded_at = now();
            }
            if ($hasNotesCol && !empty($validated['notes'])) {
                $doc->notes = $validated['notes'];
            }
            $doc->save();
            $uploadedDocs[] = $doc;
        }

        $count = count($uploadedDocs);
        $displayTitle = $uploadedDocs[0]->document_title ?? ($uploadedDocs[0]->name ?? 'Berkas');
        $msg = $count > 1 
            ? "{$count} berkas lampiran berhasil diunggah!" 
            : "Dokumen '{$displayTitle}' berhasil diunggah!";

        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'message'   => $msg,
                'count'     => $count,
                'documents' => $uploadedDocs,
                'document'  => $uploadedDocs[0],
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Verifikasi Dokumen oleh Role Berwenang (Gatekeeper)
     */
    public function verify(Request $request, Project $project, ProjectDocument $document): JsonResponse
    {
        if ($document->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Dokumen tidak valid.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:Verified,Rejected,Pending',
            'notes'  => 'nullable|string|max:1000',
        ]);

        $document->status = $validated['status'];
        if ($validated['status'] === 'Verified') {
            $document->verified_by = auth()->id();
            $document->verified_at = now();
        } else {
            $document->verified_by = null;
            $document->verified_at = null;
        }

        if (isset($validated['notes'])) {
            $document->notes = $validated['notes'];
        }

        $document->save();

        $actionText = $document->status === 'Verified' ? 'telah diverifikasi & disahkan' : ($document->status === 'Rejected' ? 'diminta perbaikan' : 'status direset');

        return response()->json([
            'success'  => true,
            'message'  => "Dokumen '{$document->document_title}' {$actionText}!",
            'document' => $document->fresh(['uploader', 'verifier']),
        ]);
    }

    /**
     * Unduh Berkas Dokumen dengan Aman
     */
    public function download(Project $project, ProjectDocument $document)
    {
        if ($document->project_id !== $project->id || !$document->file_path) {
            abort(404, 'Berkas dokumen tidak ditemukan.');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Berkas fisik tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Hapus Dokumen yang Diunggah
     */
    public function delete(Request $request, Project $project, ProjectDocument $document)
    {
        if ($document->project_id !== $project->id) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Dokumen tidak valid.'], 403);
            }
            return redirect()->back()->with('error', 'Dokumen tidak valid.');
        }

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $title = $document->document_title;
        if (!$document->is_mandatory) {
            $document->delete();
        } else {
            $document->file_path = null;
            $document->file_name = null;
            $document->file_size = null;
            $document->file_extension = null;
            $document->status = 'Pending';
            $document->uploaded_by = null;
            $document->uploaded_at = null;
            $document->verified_by = null;
            $document->verified_at = null;
            $document->save();
        }

        $msg = "Berkas dokumen '{$title}' berhasil dihapus.";
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }
}
