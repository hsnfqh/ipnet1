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
     * Upload / Perbarui Dokumen untuk slot tertentu pada tahap tertentu
     */
    public function upload(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'document_id'   => 'nullable|exists:project_documents,id',
            'stage_number'  => 'required|integer|min:1|max:6',
            'document_key'  => 'required|string|max:100',
            'document_file' => 'required|file|mimes:pdf,docx,doc,xlsx,xls,zip,rar,png,jpg,jpeg,txt,csv|max:51200', // max 50MB
            'notes'         => 'nullable|string|max:1000',
        ]);

        // Cari atau inisialisasi dokumen
        ProjectDocumentFlowService::ensureProjectDocumentsInitialized($project);

        $doc = ProjectDocument::where('project_id', $project->id)
            ->where('stage_number', $validated['stage_number'])
            ->where('document_key', $validated['document_key'])
            ->first();

        if (!$doc) {
            $stages = ProjectDocumentFlowService::getStagesDefinition();
            $stageInfo = $stages[$validated['stage_number']] ?? ['stage_name' => 'General'];

            $doc = new ProjectDocument([
                'project_id'     => $project->id,
                'stage_number'   => $validated['stage_number'],
                'stage_name'     => $stageInfo['stage_name'],
                'document_key'   => $validated['document_key'],
                'document_title' => ucwords(str_replace('_', ' ', $validated['document_key'])),
                'is_mandatory'   => true,
            ]);
        }

        // Hapus file lama jika ada
        if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $file = $request->file('document_file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $size = $file->getSize();

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

        return response()->json([
            'success'  => true,
            'message'  => "Dokumen '{$doc->document_title}' berhasil diunggah!",
            'document' => $doc->fresh(['uploader', 'verifier']),
        ]);
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
    public function delete(Project $project, ProjectDocument $document): JsonResponse
    {
        if ($document->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Dokumen tidak valid.'], 403);
        }

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

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

        return response()->json([
            'success'  => true,
            'message'  => "Berkas dokumen '{$document->document_title}' berhasil dihapus.",
            'document' => $document->fresh(),
        ]);
    }
}
