<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        return view('profile.index', compact('user'));
    }

    public function uploadCertification(Request $request)
    {
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

        // Validasi ekstensi tanpa dependensi PHP finfo
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowedExtensions)) {
            if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json(['message' => 'Format file harus PDF, JPG, JPEG, atau PNG.'], 422);
            }
            return redirect()->back()->with('error', 'Format file harus PDF, JPG, JPEG, atau PNG.');
        }

        $user = auth()->user();

        // Hapus sertifikasi lama jika ada
        if ($user->certification_file) {
            $oldPath1 = public_path('storage/' . $user->certification_file);
            $oldPath2 = storage_path('app/public/' . $user->certification_file);
            $oldPath3 = base_path('../public_html/storage/' . $user->certification_file);

            foreach ([$oldPath1, $oldPath2, $oldPath3] as $oldPath) {
                if (file_exists($oldPath) && is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }
        }

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

        $user->certification_file = $path;
        $user->certification_status = 'pending';
        $user->certification_uploaded_at = now();
        $user->save();

        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Sertifikasi berhasil diupload! Menunggu verifikasi Lead Engineer.',
                'user'    => [
                    'certification_file'        => $user->certification_file,
                    'certification_status'      => $user->certification_status,
                    'certification_uploaded_at' => $user->certification_uploaded_at->format('d M Y H:i'),
                ]
            ]);
        }

        return redirect()->route('profile.show')
            ->with('success', 'Sertifikasi berhasil diupload! Menunggu verifikasi Lead Engineer.');
    }
}
