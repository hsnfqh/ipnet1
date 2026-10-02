<?php

namespace App\Http\Controllers;

use App\Models\ActivityDocumentSignature;
use App\Models\EngineerActivityLog;
use App\Services\DocumentDiscrepancyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PublicDocumentVerificationController extends Controller
{
    protected DocumentDiscrepancyService $discrepancyService;

    public function __construct(DocumentDiscrepancyService $discrepancyService)
    {
        $this->discrepancyService = $discrepancyService;
    }

    /**
     * Tampilan Portal Publik Verifikasi & Deteksi Selisih Dokumen untuk Klien
     */
    public function index(Request $request)
    {
        $prefillDocNumber = $request->get('doc', '');
        $document = null;
        $result = session('inspection_result') ?? null;

        if ($prefillDocNumber && !$result) {
            $document = ActivityDocumentSignature::with(['project', 'picUser', 'leadUser'])
                ->where('document_number', $prefillDocNumber)
                ->first();
        }

        return view('public.verify-portal', [
            'prefillDocNumber' => $prefillDocNumber ?: ($result['document_number'] ?? ''),
            'document'         => $document ?: ($result['document'] ?? null),
            'result'           => $result,
        ]);
    }

    /**
     * Proses Uji Keaslian Dokumen & Automated Discrepancy Detection
     */
    public function inspect(Request $request)
    {
        // Tangani jika ada request via GET (misal: refresh F5 browser di /verify/inspect)
        if ($request->isMethod('get')) {
            return $this->index($request);
        }

        $request->validate([
            'document_file'   => 'nullable|file|mimes:pdf|max:10240', // Max 10MB
            'pdf_file'        => 'nullable|file|mimes:pdf|max:10240',
            'document_number' => 'nullable|string|max:50',
        ]);

        $result = null;

        $file = $request->file('document_file') ?? $request->file('pdf_file');

        if ($file) {
            $pdfContent       = file_get_contents($file->getRealPath());
            $manualDocNumber  = $request->input('document_number');
            $originalFilename = $file->getClientOriginalName();

            $result = $this->discrepancyService->inspect($pdfContent, $manualDocNumber, $originalFilename);
        } elseif ($request->filled('document_number')) {
            $docNumber = trim($request->input('document_number'));
            $document = ActivityDocumentSignature::with(['project', 'picUser', 'leadUser'])
                ->where('document_number', $docNumber)
                ->first();

            if ($document) {
                $query = EngineerActivityLog::with(['engineer', 'project']);
                if (!empty($document->log_ids)) {
                    $query->whereIn('id', $document->log_ids);
                } elseif ($document->project_id) {
                    $query->where('project_id', $document->project_id);
                }
                $activities = $query->orderBy('activity_date', 'asc')->get();

                $result = [
                    'status'              => $document->status === 'fully_approved' ? 'authentic' : 'pending_signature',
                    'is_authentic'        => $document->status === 'fully_approved',
                    'message'             => $document->status === 'fully_approved' 
                        ? 'Dokumen TERDAFTAR RESMI dan SAH di server PT IP Network Solusindo.' 
                        : 'Dokumen terdaftar namun statusnya masih dalam proses penandatanganan berjenjang.',
                    'document_number'     => $docNumber,
                    'document'            => $document,
                    'official_activities' => $activities,
                    'official_hash'       => $document->verification_hash,
                    'uploaded_hash'       => null,
                    'is_hash_identical'   => null,
                    'discrepancies'       => [],
                    'total_discrepancies' => 0,
                ];
            } else {
                $result = [
                    'status'              => 'not_found',
                    'is_authentic'        => false,
                    'message'             => "Nomor dokumen [{$docNumber}] tidak ditemukan pada arsip resmi PT IP Network Solusindo.",
                    'document_number'     => $docNumber,
                    'document'            => null,
                    'discrepancies'       => [],
                    'total_discrepancies' => 0,
                ];
            }
        } else {
            return redirect()->route('public.verify.index')->with('error', 'Silakan pilih berkas PDF dokumen atau masukkan nomor dokumen untuk diuji keabsahannya.');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        // Simpan ke session flash agar tidak hilang jika di-refresh
        session()->flash('inspection_result', $result);

        return view('public.verify-portal', [
            'prefillDocNumber' => $request->input('document_number', $result['document_number'] ?? ''),
            'document'         => $result['document'] ?? null,
            'result'           => $result,
        ]);
    }
}
