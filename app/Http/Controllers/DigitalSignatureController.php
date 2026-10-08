<?php

namespace App\Http\Controllers;

if (!class_exists(\App\Models\DigitalSignatureDocument::class)) {
    @require_once app_path('Models/DigitalSignatureDocument.php');
}

use App\Models\DigitalSignatureDocument;
use App\Models\Project;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DigitalSignatureController extends Controller
{
    /**
     * Tampilkan daftar seluruh dokumen digital signature
     */
    public function index(Request $request)
    {
        DigitalSignatureDocument::ensureSchemaReady();

        $user = auth()->user();
        $query = DigitalSignatureDocument::with(['creator', 'project'])
            ->orderByDesc('id');

        // Filter status
        $status = $request->get('status');
        if ($status && in_array($status, ['draft', 'internal_in_progress', 'ready_for_client', 'completed'])) {
            $query->where('status', $status);
        }

        // Pencarian keyword
        $q = trim($request->get('q', ''));
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('document_number', 'like', "%{$q}%")
                    ->orWhere('client_name', 'like', "%{$q}%")
                    ->orWhere('client_company', 'like', "%{$q}%")
                    ->orWhere('project_name', 'like', "%{$q}%");
            });
        }

        // Filter kategori
        $category = $request->get('category');
        if ($category) {
            $query->where('category', $category);
        }

        // Pembatasan hak akses:
        // Admin, Executive, PMO, Team Leader dapat melihat semua
        // Engineer biasa melihat dokumen yang dibuatnya atau di mana ia menjadi penandatangan internal
        $isExecutiveOrGl = $user && $user->hasAnyRole([
            'Director', 'Direktur', 'HD / Direktur', 'Division Head', 
            'Group Leader', 'Group Leader Commercial & Solution', 'Group Leader Delivery & Operation',
            'Super Admin', 'Superadmin', 'Admin'
        ]);
        $isPmoOrLead = $user && ($user->hasAnyRole(['PMO', 'Project Manager', 'Lead Engineer', 'Team Leader Engineering', 'Team Leader', 'Lead Maintenance', 'Lead Divisi']) || \App\Helpers\ScopeHelper::isTeamLeader($user));

        if (!$isExecutiveOrGl && !$isPmoOrLead) {
            $query->where(function ($w) use ($user) {
                $w->where('created_by', $user->id)
                  ->orWhere('internal_signers', 'like', '%"user_id":' . $user->id . '%')
                  ->orWhere('internal_signers', 'like', '%"user_id": ' . $user->id . '%');
            });
        }

        $documents = $query->paginate(15)->withQueryString();

        // Hitung statistik
        $totalDocs = DigitalSignatureDocument::count();
        $internalPendingCount = DigitalSignatureDocument::where('status', 'internal_in_progress')->count();
        $readyForClientCount = DigitalSignatureDocument::where('status', 'ready_for_client')->count();
        $completedCount = DigitalSignatureDocument::where('status', 'completed')->count();

        return view('digital_signatures.index', compact(
            'documents',
            'totalDocs',
            'internalPendingCount',
            'readyForClientCount',
            'completedCount',
            'status',
            'q',
            'category'
        ));
    }

    /**
     * Form pembuatan dokumen baru untuk tanda tangan digital
     */
    public function create()
    {
        DigitalSignatureDocument::ensureSchemaReady();

        $projects = Project::orderBy('name')->get(['id', 'name', 'client', 'location']);
        $users = User::orderBy('name')->with('division')->get(['id', 'name', 'position', 'email', 'division_id']);

        return view('digital_signatures.create', compact('projects', 'users'));
    }

    /**
     * Simpan dokumen baru & upload file PDF
     */
    public function store(Request $request)
    {
        DigitalSignatureDocument::ensureSchemaReady();

        $request->validate([
            'title'            => 'required|string|max:255',
            'category'         => 'nullable|string|max:100',
            'project_id'       => 'nullable|exists:projects,id',
            'document_file'    => 'required|file|mimes:pdf|max:25600', // max 25MB
            'workflow_type'    => 'required|in:sequential,parallel',
            'client_name'      => 'required|string|max:255',
            'client_position'  => 'nullable|string|max:255',
            'client_company'   => 'nullable|string|max:255',
            'client_phone'     => 'nullable|string|max:50',
            'client_email'     => 'nullable|email|max:255',
            'description'      => 'nullable|string',
            'internal_signers' => 'required|array|min:1',
        ]);

        $file = $request->file('document_file');
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $storedPath = $file->store('digital_signatures/documents', 'public');

        $projectName = null;
        if ($request->filled('project_id')) {
            $p = Project::find($request->project_id);
            $projectName = $p?->name;
        }

        // Format internal signers
        $signers = [];
        $order = 1;
        foreach ($request->internal_signers as $signerInput) {
            $uId = (int) ($signerInput['user_id'] ?? 0);
            $targetUser = User::find($uId);
            if ($targetUser) {
                $signers[] = [
                    'order'       => $order++,
                    'user_id'     => $targetUser->id,
                    'name'        => $targetUser->name,
                    'role_title'  => trim($signerInput['role_title'] ?? ($targetUser->position ?: 'Engineer Pelaksana')),
                    'email'       => $targetUser->email,
                    'status'      => 'pending',
                    'signature'   => null,
                    'signed_at'   => null,
                    'ip_address'  => null,
                ];
            }
        }

        if (empty($signers)) {
            return back()->withInput()->with('error', 'Silakan pilih setidaknya satu penandatangan internal.');
        }

        $docNumber = DigitalSignatureDocument::generateDocumentNumber();
        $clientToken = DigitalSignatureDocument::generateClientToken();

        $doc = DigitalSignatureDocument::create([
            'document_number'         => $docNumber,
            'title'                   => $request->title,
            'category'                => $request->category ?: 'BAST',
            'project_id'              => $request->project_id,
            'project_name'            => $projectName,
            'file_path'               => $storedPath,
            'file_name'               => $fileName,
            'file_size'               => $fileSize,
            'description'             => $request->description,
            'workflow_type'           => $request->workflow_type,
            'status'                  => 'internal_in_progress',
            'created_by'              => auth()->id(),
            'internal_signers'        => $signers,
            'client_name'             => $request->client_name,
            'client_position'         => $request->client_position,
            'client_company'          => $request->client_company ?: ($projectName ? 'Klien Proyek' : '-'),
            'client_phone'            => $request->client_phone,
            'client_email'            => $request->client_email,
            'client_signing_token'    => $clientToken,
            'client_token_expires_at' => now()->addDays(30),
        ]);

        return redirect()->route('digital_signatures.show', $doc->id)
            ->with('success', "Dokumen #{$docNumber} berhasil dibuat! Silakan lakukan tanda tangan internal.");
    }

    /**
     * Detail dokumen & area tanda tangan internal
     */
    public function show($id)
    {
        DigitalSignatureDocument::ensureSchemaReady();

        $document = DigitalSignatureDocument::with(['creator', 'project'])->findOrFail($id);
        $user = auth()->user();

        $canSign = $document->canUserSign($user->id);
        $isAllInternalSigned = $document->isAllInternalSigned();
        $clientSigningUrl = url('/sign/' . $document->client_signing_token);

        // Siapkan template pesan WhatsApp
        $waMessage = "Yth. {$document->client_name},\n\nBerikut dokumen *{$document->title}* (No: {$document->document_number}) yang telah ditandatangani oleh tim PT IP Network Solusindo.\n\nMohon kesediaannya untuk memeriksa dan menandatangani dokumen melalui tautan resmi berikut:\n{$clientSigningUrl}\n\nTerima kasih,\nPT IP Network Solusindo";
        $waLink = "https://api.whatsapp.com/send?text=" . urlencode($waMessage);
        if ($document->client_phone) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $document->client_phone);
            if (str_starts_with($cleanPhone, '0')) {
                $cleanPhone = '62' . substr($cleanPhone, 1);
            }
            $waLink = "https://api.whatsapp.com/send?phone={$cleanPhone}&text=" . urlencode($waMessage);
        }

        return view('digital_signatures.show', compact(
            'document',
            'canSign',
            'isAllInternalSigned',
            'clientSigningUrl',
            'waLink'
        ));
    }

    /**
     * Proses tanda tangan internal oleh user yang sedang login
     */
    public function signInternal(Request $request, $id)
    {
        $request->validate([
            'signature' => 'required|string', // base64
        ]);

        $document = DigitalSignatureDocument::findOrFail($id);
        $user = auth()->user();

        if (!$document->canUserSign($user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki wewenang untuk menandatangani dokumen ini pada tahap ini.',
            ], 403);
        }

        $signers = $document->internal_signers ?? [];
        $updated = false;

        foreach ($signers as &$s) {
            if ((int)($s['user_id'] ?? 0) === $user->id && ($s['status'] ?? 'pending') !== 'signed') {
                $s['status']     = 'signed';
                $s['signature']  = $request->signature;
                $s['signed_at']  = now()->format('Y-m-d H:i:s');
                $s['ip_address'] = $request->ip();
                $updated = true;
                break;
            }
        }
        unset($s);

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data penandatangan internal.',
            ], 400);
        }

        $document->internal_signers = $signers;

        // Cek apakah seluruh pihak internal sudah menandatangani
        if ($document->isAllInternalSigned()) {
            $document->status = 'ready_for_client';
        }

        $document->save();

        return response()->json([
            'success' => true,
            'message' => 'Tanda tangan internal berhasil disimpan!',
            'status'  => $document->status,
        ]);
    }

    /**
     * Halaman Publik untuk PIC Klien menandatangani dokumen via Link
     */
    public function clientSignShow($token)
    {
        DigitalSignatureDocument::ensureSchemaReady();

        $document = DigitalSignatureDocument::where('client_signing_token', $token)->firstOrFail();

        return view('digital_signatures.client_sign', compact('document'));
    }

    /**
     * Proses submit tanda tangan oleh PIC Klien
     */
    public function clientSignSubmit(Request $request, $token)
    {
        $request->validate([
            'client_name'     => 'required|string|max:255',
            'client_position' => 'required|string|max:255',
            'client_company'  => 'nullable|string|max:255',
            'signature'       => 'required|string', // base64 image
            'agreement'       => 'required',
        ], [
            'signature.required' => 'Silakan bubuhkan tanda tangan Anda terlebih dahulu.',
            'agreement.required' => 'Anda harus mencentang persetujuan keabsahan dokumen.',
        ]);

        $document = DigitalSignatureDocument::where('client_signing_token', $token)->firstOrFail();

        if ($document->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen ini telah selesai ditandatangani sebelumnya.',
            ], 400);
        }

        $document->client_name     = trim($request->client_name);
        $document->client_position = trim($request->client_position);
        if ($request->filled('client_company')) {
            $document->client_company = trim($request->client_company);
        }
        $document->client_signature = $request->signature;
        $document->client_signed_at = now();
        $document->client_ip        = $request->ip();
        $document->status           = 'completed';
        $document->verification_hash = $document->generateVerificationHash();
        $document->save();

        return response()->json([
            'success'  => true,
            'message'  => 'Dokumen resmi berhasil ditandatangani dan diverifikasi!',
            'redirect' => route('public.digital_signature.verify', $document->verification_hash),
        ]);
    }

    /**
     * Halaman verifikasi publik ketika QR Code di-scan oleh siapa saja
     */
    public function verifyPublic($hash)
    {
        DigitalSignatureDocument::ensureSchemaReady();

        $document = DigitalSignatureDocument::where('verification_hash', $hash)
            ->orWhere('document_number', $hash)
            ->first();

        return view('digital_signatures.verify', compact('document', 'hash'));
    }

    /**
     * Download Berkas PDF Asli atau Lembar Pengesahan Resmi (Certificate Sheet)
     */
    public function downloadPdf($id)
    {
        DigitalSignatureDocument::ensureSchemaReady();

        $document = DigitalSignatureDocument::findOrFail($id);

        $verifyUrl = route('public.digital_signature.verify', $document->verification_hash ?: $document->document_number);

        // Generate QR Code Base64
        $qrPngBase64 = '';
        if (extension_loaded('gd') && function_exists('imagecreatetruecolor')) {
            try {
                $qr = \BaconQrCode\Encoder\Encoder::encode($verifyUrl, \BaconQrCode\Common\ErrorCorrectionLevel::M());
                $matrix = $qr->getMatrix();
                $matrixWidth = $matrix->getWidth();
                $matrixHeight = $matrix->getHeight();
                $scale = 3;
                $margin = 1;
                $imgWidth = ($matrixWidth + ($margin * 2)) * $scale;
                $imgHeight = ($matrixHeight + ($margin * 2)) * $scale;

                $image = imagecreatetruecolor($imgWidth, $imgHeight);
                $white = imagecolorallocate($image, 255, 255, 255);
                $black = imagecolorallocate($image, 143, 10, 13);
                imagefill($image, 0, 0, $white);

                for ($r = 0; $r < $matrixHeight; $r++) {
                    for ($c = 0; $c < $matrixWidth; $c++) {
                        if ($matrix->get($c, $r) === 1) {
                            $x1 = ($c + $margin) * $scale;
                            $y1 = ($r + $margin) * $scale;
                            imagefilledrectangle($image, $x1, $y1, $x1 + $scale - 1, $y1 + $scale - 1, $black);
                        }
                    }
                }

                ob_start();
                imagepng($image);
                $qrPngBase64 = 'data:image/png;base64,' . base64_encode(ob_get_clean());
                imagedestroy($image);
            } catch (\Throwable $e) {}
        }

        if (!$qrPngBase64) {
            $qrPngBase64 = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($verifyUrl);
        }

        $logoPath = public_path('images/ipnet1.png');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('images/ipnet.png');
        }
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';

        $pdf = Pdf::loadView('digital_signatures.certificate_pdf', compact('document', 'verifyUrl', 'qrPngBase64', 'logoBase64'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'sans-serif',
            ]);

        $safeTitle = Str::slug($document->title ?: 'Dokumen') . '-' . $document->document_number . '.pdf';
        return $pdf->download($safeTitle);
    }

    /**
     * Download berkas file PDF mentah yang di-upload
     */
    public function downloadOriginalFile($id)
    {
        $document = DigitalSignatureDocument::findOrFail($id);
        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'Berkas dokumen asli tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Hapus dokumen (hanya creator atau admin)
     */
    public function destroy($id)
    {
        $document = DigitalSignatureDocument::findOrFail($id);
        $user = auth()->user();

        if ($document->created_by !== $user->id && !$user->hasAnyRole(['Super Admin', 'Superadmin', 'Admin'])) {
            return back()->with('error', 'Anda tidak memiliki hak untuk menghapus dokumen ini.');
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('digital_signatures.index')->with('success', 'Dokumen berhasil dihapus.');
    }
}
