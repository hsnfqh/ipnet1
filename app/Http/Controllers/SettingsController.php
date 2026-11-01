<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    /**
     * Tampilan Halaman Pengaturan Utama untuk Semua Role
     */
    public function index()
    {
        $user = auth()->user()->load(['roles', 'division', 'team']);
        $user->ensureSignatureQrToken();

        $qrDataUri = $user->signature_qr_data_uri;
        $qrSvg = $user->signature_qr_svg;

        return view('settings.index', compact('user', 'qrDataUri', 'qrSvg'));
    }

    /**
     * Perbarui Informasi Profil Pengguna
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'   => 'required|string|max:255',
            'phone'  => 'nullable|string|max:30',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'avatar.max'    => 'Ukuran foto profil maksimal 2MB.',
        ]);

        $user->name  = trim($request->name);
        $user->phone = $request->phone ? trim($request->phone) : null;

        // Upload Avatar jika ada
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

            $destPublic = public_path('storage/avatars');
            if (!file_exists($destPublic)) {
                @mkdir($destPublic, 0755, true);
            }
            $destApp = storage_path('app/public/avatars');
            if (!file_exists($destApp)) {
                @mkdir($destApp, 0755, true);
            }

            $file->move($destPublic, $fileName);
            @copy($destPublic . '/' . $fileName, $destApp . '/' . $fileName);

            $user->avatar = $fileName;
        }

        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui.',
                'user'    => [
                    'name'       => $user->name,
                    'phone'      => $user->phone,
                    'avatar_url' => $user->avatar_url,
                ]
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Informasi profil Anda berhasil diperbarui.');
    }

    /**
     * Perbarui Password Akun Pengguna
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required|current_password:web',
            'password'         => 'required|string|min:8|confirmed',
        ], [
            'current_password.required'         => 'Masukkan password saat ini.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required'                 => 'Masukkan password baru.',
            'password.min'                      => 'Password baru minimal 8 karakter.',
            'password.confirmed'                => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Password akun berhasil diperbarui!'
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Password akun berhasil diperbarui!');
    }

    /**
     * Simpan / Perbarui Tanda Tangan Digital & Konfigurasi QR Barcode
     */
    public function updateSignature(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'signature_data' => 'nullable|string',
            'signature_file' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:3072',
            'signature_type' => 'nullable|in:signature_qr,signature_only,qr_only',
        ]);

        $signatureData = $request->input('signature_data');

        // Jika upload file gambar tanda tangan
        if ($request->hasFile('signature_file')) {
            $file = $request->file('signature_file');
            $mime = $file->getMimeType();
            $base64 = base64_encode(file_get_contents($file->getRealPath()));
            $signatureData = 'data:' . $mime . ';base64,' . $base64;
        }

        if (empty($signatureData) && empty($user->signature_data)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Silakan gambar tanda tangan pada kanvas atau unggah gambar berkas tanda tangan Anda.'], 422);
            }
            return redirect()->route('settings.index')->with('error', 'Silakan bubuhkan tanda tangan atau unggah berkas terlebih dahulu.');
        }

        if (!empty($signatureData)) {
            $user->signature_data = $signatureData;
        }

        $user->signature_type = $request->input('signature_type', $user->signature_type ?: 'signature_qr');
        $user->signature_updated_at = now();
        $user->ensureSignatureQrToken();
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'                => true,
                'message'                => 'Tanda tangan digital & QR Code berhasil disimpan! Tanda tangan ini akan otomatis digunakan saat Anda menandatangani dokumen.',
                'signature_data'         => $user->signature_data,
                'signature_type'         => $user->signature_type,
                'signature_qr_data_uri'  => $user->signature_qr_data_uri,
                'signature_qr_token'     => $user->signature_qr_token,
                'signature_updated_at'   => $user->signature_updated_at ? $user->signature_updated_at->format('d M Y H:i') : null,
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Tanda tangan digital dan konfigurasi QR berhasil disimpan!');
    }

    /**
     * Hapus Tanda Tangan Tersimpan
     */
    public function deleteSignature(Request $request)
    {
        $user = auth()->user();
        $user->signature_data = null;
        $user->signature_updated_at = null;
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tanda tangan tersimpan berhasil dihapus.'
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Tanda tangan tersimpan berhasil dihapus.');
    }

    /**
     * Perbarui Preferensi Aplikasi & Notifikasi
     */
    public function updatePreferences(Request $request)
    {
        $user = auth()->user();

        $user->notification_web   = $request->boolean('notification_web', true);
        $user->notification_email = $request->boolean('notification_email', false);
        $user->theme_preference   = $request->input('theme_preference', 'system');
        $user->timezone_preference= $request->input('timezone_preference', 'Asia/Jakarta');
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Preferensi aplikasi berhasil diperbarui.'
            ]);
        }

        return redirect()->route('settings.index')->with('success', 'Preferensi aplikasi berhasil diperbarui.');
    }

    /**
     * Portal Verifikasi Publik Tanda Tangan Digital (Bila QR Barcode Di-scan)
     */
    public function verifySignature(string $token)
    {
        $user = User::where('signature_qr_token', $token)->first();

        return view('public.verify-signature', compact('user', 'token'));
    }
}
