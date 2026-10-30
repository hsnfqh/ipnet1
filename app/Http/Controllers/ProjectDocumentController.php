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
     * Cek otorisasi akses khusus Berkas Sales (Confidential)
     * Hanya dapat diakses oleh:
     * 1. Sales PIC / Pembuat Proyek
     * 2. Pak Santoso (Susanto Djaya) & Pak Hari (Hariyadi)
     * 3. Direktur / Management / Head Divisi / BDM / Super Admin
     */
    public static function canAccessSalesDocs(Project $project, $user = null): bool
    {
        return $project->canAccessSalesDocs($user);
    }

    /**
     * Upload / Perbarui Dokumen untuk slot tertentu atau upload multiple attachment
     */
    public function upload(Request $request, Project $project)
    {
        // 1. Tangani kesalahan upload PHP awal (seperti file melebihi upload_max_filesize server)
        if (isset($_FILES['document_file']) && !empty($_FILES['document_file']['name'])) {
            $err = $_FILES['document_file']['error'] ?? UPLOAD_ERR_OK;
            if ($err !== UPLOAD_ERR_OK && $err !== UPLOAD_ERR_NO_FILE) {
                $msg = match($err) {
                    UPLOAD_ERR_INI_SIZE   => 'Ukuran file melebihi batas konfigurasi server (upload_max_filesize). Silakan perkecil ukuran file atau gunakan file di bawah 50MB.',
                    UPLOAD_ERR_FORM_SIZE  => 'Ukuran file melebihi batas formulir upload.',
                    UPLOAD_ERR_PARTIAL    => 'File hanya terunggah sebagian karena gangguan jaringan. Silakan unggah kembali.',
                    UPLOAD_ERR_NO_TMP_DIR => 'Server tidak memiliki direktori temporary untuk menyimpan file upload.',
                    UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk server (periksa izin folder atau kapasitas disk).',
                    UPLOAD_ERR_EXTENSION  => 'Unggahan file dihentikan oleh ekstensi PHP server.',
                    default               => 'Terjadi kendala saat mengunggah berkas (Kode kesalahan server: ' . $err . ').',
                };
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => $msg], 422);
                }
                return redirect()->back()->withErrors(['document_file' => $msg]);
            }
        }

        // Cek juga untuk array files
        if (isset($_FILES['document_files']['error']) && is_array($_FILES['document_files']['error'])) {
            foreach ($_FILES['document_files']['error'] as $idx => $err) {
                if ($err !== UPLOAD_ERR_OK && $err !== UPLOAD_ERR_NO_FILE) {
                    $fileName = $_FILES['document_files']['name'][$idx] ?? ('Berkas #' . ($idx + 1));
                    $msg = match($err) {
                        UPLOAD_ERR_INI_SIZE => "Ukuran berkas '{$fileName}' terlalu besar melebihi batas kapasitas server.",
                        default             => "Berkas '{$fileName}' gagal diunggah (Kode kesalahan: {$err}).",
                    };
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return redirect()->back()->withErrors(['document_files' => $msg]);
                }
            }
        }

        $validated = $request->validate([
            'document_id'       => 'nullable',
            'stage_number'      => 'nullable|integer|min:1|max:6',
            'stage_name'        => 'nullable|string|max:100',
            'document_category' => 'nullable|string|max:50',
            'document_key'      => 'nullable|string|max:100',
            'document_title'    => 'nullable|string|max:255',
            'document_type'     => 'nullable|string|max:50',
            'document_file'     => 'nullable|file|max:51200', // max 50MB
            'document_files'    => 'nullable|array',
            'document_files.*'  => 'file|max:51200',
            'notes'             => 'nullable|string|max:1000',
        ], [
            'document_file.uploaded'    => 'Berkas gagal diunggah ke server. Pastikan ukuran file tidak melebihi kapasitas server atau ulangi beberapa saat lagi.',
            'document_file.max'         => 'Ukuran berkas tidak boleh melebihi batas maksimal 50MB.',
            'document_file.file'        => 'Berkas yang dipilih tidak valid.',
            'document_files.*.uploaded' => 'Salah satu berkas gagal diunggah ke server.',
            'document_files.*.max'      => 'Ukuran salah satu berkas melebihi batas maksimal 50MB.',
            'document_files.*.file'     => 'Salah satu berkas yang dipilih tidak valid.',
        ]);

        $files = [];
        if ($request->hasFile('document_files')) {
            $files = $request->file('document_files');
        } elseif ($request->hasFile('document_file')) {
            $files = [$request->file('document_file')];
        } elseif ($request->file('document_file')) {
            $files = [$request->file('document_file')];
        }

        if (empty($files)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Silakan pilih berkas untuk diunggah.'], 422);
            }
            return redirect()->back()->with('error', 'Silakan pilih berkas untuk diunggah.');
        }

        $isSalesUpload = $request->input('document_category') === 'sales'
            || $request->input('stage_name') === 'Sales'
            || ($validated['document_key'] ?? '') === 'sales_berkas'
            || str_starts_with($validated['document_key'] ?? '', 'sales_berkas');

        if ($isSalesUpload && !self::canAccessSalesDocs($project)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengunggah Berkas Sales.'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk mengunggah Berkas Sales.');
        }

        $stageNumber = !empty($validated['stage_number']) ? (int)$validated['stage_number'] : 1;
        $documentKey = !empty($validated['document_key']) ? $validated['document_key'] : ($isSalesUpload ? 'sales_berkas' : 'lampiran_pendukung');
        $stages = ProjectDocumentFlowService::getStagesDefinition();
        $stageInfo = $isSalesUpload ? ['stage_name' => 'Sales'] : ($stages[$stageNumber] ?? ['stage_name' => 'Commercial']);

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
            } elseif (count($files) === 1 && !in_array($documentKey, ['lampiran_pendukung', 'sales_berkas']) && !str_starts_with($documentKey, 'lampiran_') && $hasDocKeyCol && $hasStageNumCol) {
                $doc = ProjectDocument::where('project_id', $project->id)
                    ->where('stage_number', $stageNumber)
                    ->where('document_key', $documentKey)
                    ->first();
            }

            if (!$doc) {
                $doc = new ProjectDocument();
                $doc->project_id = $project->id;
                if ($hasDocKeyCol) {
                    $doc->document_key = $isSalesUpload
                        ? (!empty($validated['document_key']) && $validated['document_key'] !== 'sales_berkas'
                            ? ($validated['document_key'] . '_' . uniqid())
                            : ('sales_berkas_' . \Illuminate\Support\Str::slug($cleanTitle) . '_' . uniqid()))
                        : (!empty($validated['document_key']) && !in_array($validated['document_key'], ['lampiran_pendukung'])
                            ? ($validated['document_key'] . '_' . uniqid())
                            : ('attachment_' . \Illuminate\Support\Str::slug($cleanTitle) . '_' . uniqid()));
                }
                if ($hasStageNumCol) {
                    $doc->stage_number = $stageNumber;
                }
                if ($hasStageNameCol) {
                    $doc->stage_name = $isSalesUpload ? 'Sales' : ($stageInfo['stage_name'] ?? 'Commercial');
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

            $storageFolder = $isSalesUpload ? "project_documents/{$project->id}/sales" : "project_documents/{$project->id}/stage_{$stageNumber}";
            $path = \App\Helpers\FileUploadHelper::storePublicly($file, $storageFolder);
            if (!$path) {
                $path = $file->store($storageFolder, 'public');
            }

            $doc->file_path = $path;
            if ($hasNameCol) {
                $doc->name = $originalName;
            }
            if ($hasDocTitleCol) {
                $doc->document_title = $request->input('document_title') ?: ($cleanTitle ?: ('Attachment ' . ($index + 1)));
            }
            if ($hasDocTypeCol) {
                $doc->document_type = $request->input('document_type') ?: ($isSalesUpload ? 'Sales Attachment' : 'Attachment');
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
            ? "{$count} berkas berhasil diunggah!" 
            : "Berkas '{$displayTitle}' berhasil diunggah!";

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
     * Lihat / Preview Berkas Dokumen di Browser (Inline)
     */
    public function view(Project $project, ProjectDocument $document)
    {
        if ($document->project_id !== $project->id || !$document->file_path) {
            abort(404, 'Berkas dokumen tidak ditemukan.');
        }

        $isSalesDoc = ($document->stage_name === 'Sales') || str_starts_with($document->document_key ?? '', 'sales_berkas');
        if ($isSalesDoc && !self::canAccessSalesDocs($project)) {
            abort(403, 'Akses ditolak. Berkas Sales ini bersifat rahasia dan hanya dapat diakses oleh Sales terkait dan Pimpinan.');
        }

        $filePath = $document->file_path;
        $resolvedPath = null;
        if (Storage::disk('public')->exists($filePath)) {
            $resolvedPath = Storage::disk('public')->path($filePath);
        } elseif (file_exists(public_path('storage/' . $filePath))) {
            $resolvedPath = public_path('storage/' . $filePath);
        } elseif (file_exists(storage_path('app/public/' . $filePath))) {
            $resolvedPath = storage_path('app/public/' . $filePath);
        }

        if ($resolvedPath && file_exists($resolvedPath)) {
            $mime = @mime_content_type($resolvedPath) ?: 'application/octet-stream';
            return response()->file($resolvedPath, [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'inline; filename="' . addslashes($document->file_name ?: basename($resolvedPath)) . '"',
            ]);
        }

        abort(404, 'Berkas fisik tidak ditemukan di server.');
    }

    /**
     * Unduh Berkas Dokumen dengan Aman
     */
    public function download(Project $project, ProjectDocument $document)
    {
        if (request()->has('view') || request()->has('inline')) {
            return $this->view($project, $document);
        }

        if ($document->project_id !== $project->id || !$document->file_path) {
            abort(404, 'Berkas dokumen tidak ditemukan.');
        }

        $isSalesDoc = ($document->stage_name === 'Sales') || str_starts_with($document->document_key ?? '', 'sales_berkas');
        if ($isSalesDoc && !self::canAccessSalesDocs($project)) {
            abort(403, 'Akses ditolak. Berkas Sales ini bersifat rahasia dan hanya dapat diakses oleh Sales terkait dan Pimpinan.');
        }

        $filePath = $document->file_path;
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath, $document->file_name);
        }
        $fullPathPublic = public_path('storage/' . $filePath);
        if (file_exists($fullPathPublic)) {
            return response()->download($fullPathPublic, $document->file_name);
        }
        $fullPathApp = storage_path('app/public/' . $filePath);
        if (file_exists($fullPathApp)) {
            return response()->download($fullPathApp, $document->file_name);
        }

        abort(404, 'Berkas fisik tidak ditemukan di server.');
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

        $isSalesDoc = ($document->stage_name === 'Sales') || str_starts_with($document->document_key ?? '', 'sales_berkas');
        if ($isSalesDoc && !self::canAccessSalesDocs($project)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk menghapus Berkas Sales ini.'], 403);
            }
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus Berkas Sales ini.');
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
