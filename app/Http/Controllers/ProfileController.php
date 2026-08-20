<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Certification;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user()->load('certifications');

        return view('profile.index', compact('user'));
    }

    public function uploadCertification(Request $request)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'certification_file' => 'required',
        ], [
            'name.required'               => 'Silakan masukkan nama sertifikasi.',
            'certification_file.required' => 'Silakan pilih file sertifikat.',
        ]);

        if (!$request->hasFile('certification_file')) {
            if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json(['message' => 'Silakan pilih file sertifikasi untuk diupload.'], 422);
            }
            return redirect()->back()->with('error', 'Silakan pilih file sertifikasi untuk diupload.');
        }

        $file = $request->file('certification_file');

        // Validasi ukuran (max 5MB = 5 * 1024 * 1024 bytes)
        if ($file->getSize() > 5 * 1024 * 1024) {
            if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json(['message' => 'Ukuran file maksimal adalah 5MB.'], 422);
            }
            return redirect()->back()->with('error', 'Ukuran file maksimal adalah 5MB.');
        }

        // Validasi ekstensi
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowedExtensions)) {
            if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json(['message' => 'Format file harus PDF, JPG, JPEG, PNG, atau WEBP.'], 422);
            }
            return redirect()->back()->with('error', 'Format file harus PDF, JPG, JPEG, PNG, atau WEBP.');
        }

        $user = auth()->user();

        // Simpan file baru
        $fileName = time() . '_' . Str::random(10) . '.' . $extension;
        
        $destPublic = public_path('storage/certifications');
        if (!file_exists($destPublic)) {
            @mkdir($destPublic, 0755, true);
        }

        $destAppPublic = storage_path('app/public/certifications');
        if (!file_exists($destAppPublic)) {
            @mkdir($destAppPublic, 0755, true);
        }

        $file->move($destPublic, $fileName);
        @copy($destPublic . '/' . $fileName, $destAppPublic . '/' . $fileName);

        // Jika cPanel menggunakan struktur public_html terpisah
        $destPublicHtml = base_path('../public_html/storage/certifications');
        if (is_dir(base_path('../public_html'))) {
            if (!file_exists($destPublicHtml)) {
                @mkdir($destPublicHtml, 0755, true);
            }
            @copy($destPublic . '/' . $fileName, $destPublicHtml . '/' . $fileName);
        }

        $path = 'certifications/' . $fileName;

        $cert = Certification::create([
            'user_id'     => $user->id,
            'name'        => trim($request->name),
            'file_path'   => $path,
            'status'      => 'pending',
            'uploaded_at' => now(),
        ]);

        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json([
                'success'       => true,
                'message'       => 'Sertifikasi "' . $cert->name . '" berhasil diupload! Menunggu verifikasi Lead Engineer.',
                'certification' => [
                    'id'          => $cert->id,
                    'name'        => $cert->name,
                    'file_path'   => $cert->file_path,
                    'status'      => $cert->status,
                    'uploaded_at' => $cert->uploaded_at->format('d M Y H:i'),
                ]
            ]);
        }

        return redirect()->route('profile.show')
            ->with('success', 'Sertifikasi "' . $cert->name . '" berhasil diupload! Menunggu verifikasi Lead Engineer.');
    }

    public function deleteCertification(Certification $certification)
    {
        if ($certification->user_id !== auth()->id()) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        // Hapus file fisik
        $oldPath1 = public_path('storage/' . $certification->file_path);
        $oldPath2 = storage_path('app/public/' . $certification->file_path);
        $oldPath3 = base_path('../public_html/storage/' . $certification->file_path);

        foreach ([$oldPath1, $oldPath2, $oldPath3] as $oldPath) {
            if (file_exists($oldPath) && is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        $certification->delete();

        if (request()->wantsJson() || request()->isJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Sertifikat berhasil dihapus.']);
        }

        return redirect()->route('profile.show')->with('success', 'Sertifikat berhasil dihapus.');
    }
}

