<?php

namespace App\Http\Controllers;

use App\Helpers\ScopeHelper;
use App\Models\ActivityDocumentSignature;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class ActivitySignatureController extends Controller
{
    /**
     * Ambil status tanda tangan dokumen berdasarkan scope_key (misal proj_5 atau no_proj_1)
     */
    public function getStatus(Request $request): JsonResponse
    {
        $scopeKey = $request->get('scope_key');
        if (!$scopeKey) {
            return response()->json(['error' => 'scope_key parameter is required'], 400);
        }

        // Auto-migrate tabel di hosting jika belum pernah dimigrasikan
        if (!Schema::hasTable('activity_document_signatures')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                // abaikan
            }
        }

        $currentUser = auth()->user();
        $sig = null;
        try {
            $sig = ActivityDocumentSignature::where('scope_key', $scopeKey)->first();
            if (!$sig && str_starts_with($scopeKey, 'proj_')) {
                $pId = (int) substr($scopeKey, 5);
                if ($pId > 0) {
                    $sig = ActivityDocumentSignature::where('project_id', $pId)->first();
                }
            }
        } catch (\Throwable $e) {
            // Tabel belum dimigrate di remote host
        }

        // Tentukan izin TTD untuk user yang sedang login
        $isExecutiveOrGl = $currentUser && (
            $currentUser->hasAnyRole([
                'Director', 'Direktur', 'HD / Direktur', 'Division Head', 
                'Group Leader', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation'
            ]) ||
            str_contains(strtolower($currentUser->name ?? ''), 'susanto') ||
            str_contains(strtolower($currentUser->name ?? ''), 'hariyadi')
        );

        $isLead = $currentUser && (
            ScopeHelper::isTeamLeader($currentUser) || 
            $currentUser->hasAnyRole(['Lead Engineer', 'Team Leader Engineering', 'Team Leader', 'Lead Maintenance', 'Lead Divisi', 'PMO', 'Project Manager'])
        );

        $isEngineer = $currentUser && (
            $currentUser->hasAnyRole([
                'Network Engineer', 'Security Engineer', 'Field Support (EOS)', 'Field Support', 
                'Managed Service', 'Engineer', 'Engineer L1', 'Engineer L2', 'Maintenance'
            ]) ||
            (!$isExecutiveOrGl && !$isLead)
        );

        $docNumber = null;
        try {
            $docNumber = $sig?->document_number ?? ActivityDocumentSignature::generateDocumentNumber();
        } catch (\Throwable $e) {
            $docNumber = 'IPNET-ACT-' . date('Ym') . '-0001';
        }

        return response()->json([
            'exists'          => !empty($sig),
            'document_number' => $docNumber,
            'scope_key'       => $scopeKey,
            'status'          => $sig?->status ?? 'draft',
            'verification_hash' => $sig?->verification_hash,
            'pic' => [
                'signed'    => !empty($sig?->pic_signature),
                'name'      => $sig?->pic_name,
                'title'     => $sig?->pic_title ?? 'PIC Field Engineer',
                'signed_at' => $sig?->pic_signed_at?->format('d M Y, H:i') . ' WIB',
            ],
            'lead' => [
                'signed'    => !empty($sig?->lead_signature),
                'name'      => $sig?->lead_name,
                'title'     => $sig?->lead_title ?? 'Lead Network Engineer',
                'signed_at' => $sig?->lead_signed_at?->format('d M Y, H:i') . ' WIB',
            ],
            'head' => [
                'signed'    => !empty($sig?->head_signature),
                'name'      => $sig?->head_name,
                'title'     => $sig?->head_title ?? 'Head of Division',
                'signed_at' => $sig?->head_signed_at?->format('d M Y, H:i') . ' WIB',
            ],
            'user_permissions' => [
                'can_sign_pic'  => (bool) ($isEngineer || $isLead),
                'can_sign_lead' => (bool) ($isLead || $isExecutiveOrGl),
                'can_sign_head' => (bool) $isExecutiveOrGl,
                'user_name'     => $currentUser?->name,
                'user_role'     => $currentUser?->getRoleNames()->first() ?? 'Staff',
            ]
        ]);
    }

    /**
     * Simpan goresan tanda tangan digital (base64 image)
     */
    public function storeSignature(Request $request): JsonResponse
    {
        $request->validate([
            'scope_key'      => 'required|string',
            'role_type'      => 'required|in:pic,lead,head',
            'signature_data' => 'required|string', // data:image/png;base64,...
            'project_id'     => 'nullable|integer',
            'project_name'   => 'nullable|string',
            'log_ids'        => 'nullable|array',
        ]);

        $currentUser = auth()->user();
        if (!$currentUser) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $scopeKey = $request->input('scope_key');
        $roleType = $request->input('role_type');
        $signatureData = $request->input('signature_data');

        // Validasi format signature PNG base64
        if (!str_starts_with($signatureData, 'data:image/png;base64,')) {
            return response()->json(['error' => 'Format tanda tangan harus berupa gambar PNG transparan.'], 422);
        }

        // Auto-migrate tabel di hosting jika belum ada
        if (!Schema::hasTable('activity_document_signatures')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                // abaikan
            }
        }

        DB::beginTransaction();
        try {
            $sig = ActivityDocumentSignature::firstOrNew(['scope_key' => $scopeKey]);

            if (!$sig->exists) {
                $sig->document_number = ActivityDocumentSignature::generateDocumentNumber();
                $sig->scope_key       = $scopeKey;
                $sig->project_id      = $request->input('project_id');
                $sig->project_name    = $request->input('project_name');
                $sig->log_ids         = $request->input('log_ids', []);
                $sig->status          = 'draft';
            }

            $now = now();

            if ($roleType === 'pic') {
                $sig->pic_user_id   = $currentUser->id;
                $sig->pic_name      = $currentUser->name;
                $sig->pic_title     = $currentUser->position ?: ($currentUser->getRoleNames()->first() ?: 'PIC Field Engineer');
                $sig->pic_signature = $signatureData;
                $sig->pic_signed_at = $now;

                if ($sig->status === 'draft') {
                    $sig->status = 'signed_pic';
                }
            } elseif ($roleType === 'lead') {
                $sig->lead_user_id   = $currentUser->id;
                $sig->lead_name      = $currentUser->name;
                $sig->lead_title     = $currentUser->position ?: ($currentUser->getRoleNames()->first() ?: 'Lead Network Engineer');
                $sig->lead_signature = $signatureData;
                $sig->lead_signed_at = $now;

                if ($sig->status === 'draft' || $sig->status === 'signed_pic') {
                    $sig->status = 'signed_lead';
                }
            } elseif ($roleType === 'head') {
                $sig->head_user_id   = $currentUser->id;
                $sig->head_name      = $currentUser->name;
                $sig->head_title     = $currentUser->position ?: ($currentUser->getRoleNames()->first() ?: 'Head of Division');
                $sig->head_signature = $signatureData;
                $sig->head_signed_at = $now;

                $sig->status = 'fully_approved';
            }

            // Hitung Cryptographic Hash jika sudah fully_approved atau saat ada TTD baru
            $payload = implode('|', [
                $sig->document_number,
                $sig->scope_key,
                $sig->project_name ?? '-',
                $sig->pic_name ?? '-',
                $sig->lead_name ?? '-',
                $sig->head_name ?? '-',
                $now->toIso8601String(),
            ]);
            $sig->verification_hash = hash('sha256', $payload);

            $sig->save();
            DB::commit();

            return response()->json([
                'success'         => true,
                'message'         => 'Tanda tangan digital berhasil disimpan.',
                'document_number' => $sig->document_number,
                'status'          => $sig->status,
                'role_type'       => $roleType,
                'signed_by'       => $currentUser->name,
                'signed_at'       => $now->format('d M Y, H:i') . ' WIB',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error'   => 'Gagal menyimpan tanda tangan.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Halaman Publik Verifikasi Keabsahan Dokumen Digital (Hasil Scan QR Code)
     */
    public function verifyDocument(string $documentNumber)
    {
        // Pastikan tabel ada
        if (!Schema::hasTable('activity_document_signatures')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {}
        }

        $document = null;
        try {
            $document = ActivityDocumentSignature::with(['project', 'picUser', 'leadUser'])
                ->where('document_number', $documentNumber)
                ->first();
        } catch (\Throwable $e) {
            // Document null
        }

        return view('public.verify-document', [
            'documentNumber' => $documentNumber,
            'document'       => $document,
        ]);
    }
}
