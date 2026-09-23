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
            'document_id'      => 'nullable|exists:project_documents,id',
            'stage_number'     => 'required|integer|min:1|max:6',
            'document_key'     => 'required|string|max:100',
            'document_file'    => 'nullable|file|mimes:pdf,docx,doc,xlsx,xls,zip,rar,png,jpg,jpeg,txt,csv|max:51200', // max 50MB
            'document_files'   => 'nullable|array',
            'document_files.*' => 'file|mimes:pdf,docx,doc,xlsx,xls,zip,rar,png,jpg,jpeg,txt,csv|max:51200',
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

        // Cari atau inisialisasi dokumen
        ProjectDocumentFlowService::ensureProjectDocumentsInitialized($project);
        $stages = ProjectDocumentFlowService::getStagesDefinition();
        $stageInfo = $stages[$validated['stage_number']] ?? ['stage_name' => 'General'];
        $uploadedDocs = [];

        foreach ($files as $index => $file) {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $size = $file->getSize();
            $cleanTitle = pathinfo($originalName, PATHINFO_FILENAME);

            // Jika upload tunggal dengan slot spesifik
            if (count($files) === 1 && !empty($validated['document_id'])) {
                $doc = ProjectDocument::find($validated['document_id']);
            } elseif (count($files) === 1 && $validated['document_key'] !== 'lampiran_pendukung') {
                $doc = ProjectDocument::where('project_id', $project->id)
                    ->where('stage_number', $validated['stage_number'])
                    ->where('document_key', $validated['document_key'])
                    ->first();
            } else {
                $doc = null;
            }

            if (!$doc) {
                $docKey = 'attachment_' . \Illuminate\Support\Str::slug($cleanTitle) . '_' . uniqid();
                $doc = new ProjectDocument([
                    'project_id'     => $project->id,
                    'stage_number'   => $validated['stage_number'],
                    'stage_name'     => $stageInfo['stage_name'],
                    'document_key'   => $docKey,
                    'document_title' => $cleanTitle ?: ('Attachment ' . ($index + 1)),
                    'is_mandatory'   => false,
                ]);
            } else {
                // Hapus file lama jika ada
                if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }
            }

            $path = $file->store("project_documents/{$project->id}/stage_{$validated['stage_number']}", 'public');

            $doc->file_path = $path;
            $doc->file_name = $originalName;
            $doc->file_size = $size;
            $doc->file_extension = strtolower($extension);
            $doc->status = 'Uploaded';
            $doc->uploaded_by = auth()->id();
            $doc->uploaded_at = now();
            if (!empty($validated['notes'])) {
                $doc->notes = $validated['notes'];
            }
            $doc->save();
            $uploadedDocs[] = $doc;
        }

        $count = count($uploadedDocs);
        $msg = $count > 1 
            ? "{$count} berkas lampiran berhasil diunggah!" 
            : "Dokumen '{$uploadedDocs[0]->document_title}' berhasil diunggah!";

        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'message'   => $msg,
                'count'     => $count,
                'documents' => $uploadedDocs,
                'document'  => $uploadedDocs[0]->fresh(['uploader', 'verifier']),
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
