<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Division;
use App\Models\Team;
use App\Http\Requests\UserRequest;
use App\Helpers\ScopeHelper;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    private function formatUserResponse(User $user): array
    {
        $certs = $user->certifications->map(function($c) {
            return [
                'id'          => $c->id,
                'name'        => $c->name,
                'file_path'   => $c->file_path,
                'status'      => $c->status,
                'uploaded_at' => $c->uploaded_at ? $c->uploaded_at->format('d M Y H:i') : ($c->created_at ? $c->created_at->format('d M Y H:i') : '-'),
                'is_pdf'      => str_ends_with(strtolower($c->file_path), '.pdf'),
            ];
        })->values()->toArray();

        $totalCerts = count($certs);
        $pendingCount = collect($certs)->where('status', 'pending')->count();
        $approvedCount = collect($certs)->where('status', 'approved')->count();

        $overallStatus = 'none';
        if ($totalCerts > 0) {
            if ($pendingCount > 0) {
                $overallStatus = 'pending';
            } elseif ($approvedCount > 0) {
                $overallStatus = 'approved';
            } else {
                $overallStatus = 'rejected';
            }
        }

        return [
            'id'                        => $user->id,
            'name'                      => $user->name,
            'email'                     => $user->email,
            'phone'                     => $user->phone,
            'position'                  => $user->position,
            'status'                    => $user->status,
            'level'                     => $user->level,
            'division_id'               => $user->division_id,
            'division_name'             => $user->division?->name ?? '-',
            'team_id'                   => $user->team_id,
            'team_name'                 => $user->team?->name ?? '-',
            'role_name'                 => $user->getRoleNames()->first() ?? 'Tidak Ada Role',
            'roles'                     => $user->getRoleNames()->toArray(),
            'certifications'            => $certs,
            'certifications_count'      => $totalCerts,
            'pending_certs_count'       => $pendingCount,
            'approved_certs_count'      => $approvedCount,
            'certification_status'      => $overallStatus,
            'has_certification'         => $totalCerts > 0,
        ];
    }


    public function index()
    {
        $authUser  = auth()->user();
        $scopeIds  = ScopeHelper::getScopeUserIds($authUser);

        // Load users sesuai scope role yang login
        $query = User::with(['roles', 'certifications', 'division', 'team']);

        if ($scopeIds !== null) {
            // Non-global: filter user berdasarkan divisi/tim/diri sendiri
            $query->whereIn('id', $scopeIds);
        }

        $users = $query->get()->map(function($user) {
            return $this->formatUserResponse($user);
        });

        $roles     = Role::all();
        $divisions = Division::orderBy('name')->get();
        $teams     = Team::with('division')->orderBy('name')->get();

        return view('users.index', compact('users', 'roles', 'divisions', 'teams'));
    }


    public function store(UserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        
        if ($request->hasFile('certification_file')) {
            $file = $request->file('certification_file');
            $ext = strtolower($file->getClientOriginalExtension());
            if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'webp'])) {
                $fileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $ext;
                
                $destPublic = public_path('storage/certifications');
                if (!file_exists($destPublic)) @mkdir($destPublic, 0755, true);

                $destAppPublic = storage_path('app/public/certifications');
                if (!file_exists($destAppPublic)) @mkdir($destAppPublic, 0755, true);

                $file->move($destPublic, $fileName);
                @copy($destPublic . '/' . $fileName, $destAppPublic . '/' . $fileName);

                $destPublicHtml = base_path('../public_html/storage/certifications');
                if (is_dir(base_path('../public_html'))) {
                    if (!file_exists($destPublicHtml)) @mkdir($destPublicHtml, 0755, true);
                    @copy($destPublic . '/' . $fileName, $destPublicHtml . '/' . $fileName);
                }

                $data['certification_file'] = 'certifications/' . $fileName;
                $data['certification_status'] = 'pending';
                $data['certification_uploaded_at'] = now();
            }
        }

        $user = User::create($data);
        $user->assignRole($data['role']);
        
        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json($this->formatUserResponse($user));
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    public function update(UserRequest $request, User $user)
    {
        $authUser = auth()->user();
        if (!$authUser->hasAnyRole(['Direktur', 'HD / Direktur']) && $user->hasAnyRole(['Direktur', 'HD / Direktur'])) {
            if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
                return response()->json(['message' => 'Hanya Direktur yang dapat mengubah data akun Direktur.'], 403);
            }
            return redirect()->route('users.index')->with('error', 'Hanya Direktur yang dapat mengubah data akun Direktur.');
        }

        $data = $request->validated();
        
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->hasFile('certification_file')) {
            if ($user->certification_file) {
                $oldPath1 = public_path('storage/' . $user->certification_file);
                $oldPath2 = storage_path('app/public/' . $user->certification_file);
                $oldPath3 = base_path('../public_html/storage/' . $user->certification_file);

                foreach ([$oldPath1, $oldPath2, $oldPath3] as $oldPath) {
                    if (file_exists($oldPath) && is_file($oldPath)) @unlink($oldPath);
                }
            }

            $file = $request->file('certification_file');
            $ext = strtolower($file->getClientOriginalExtension());
            if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'webp'])) {
                $fileName = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $ext;
                
                $destPublic = public_path('storage/certifications');
                if (!file_exists($destPublic)) @mkdir($destPublic, 0755, true);

                $destAppPublic = storage_path('app/public/certifications');
                if (!file_exists($destAppPublic)) @mkdir($destAppPublic, 0755, true);

                $file->move($destPublic, $fileName);
                @copy($destPublic . '/' . $fileName, $destAppPublic . '/' . $fileName);

                $destPublicHtml = base_path('../public_html/storage/certifications');
                if (is_dir(base_path('../public_html'))) {
                    if (!file_exists($destPublicHtml)) @mkdir($destPublicHtml, 0755, true);
                    @copy($destPublic . '/' . $fileName, $destPublicHtml . '/' . $fileName);
                }

                $data['certification_file'] = 'certifications/' . $fileName;
                $data['certification_status'] = 'pending';
                $data['certification_uploaded_at'] = now();
            }
        }
        
        $user->update($data);
        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
        }
        
        if ($request->wantsJson() || $request->isJson() || $request->ajax()) {
            return response()->json($this->formatUserResponse($user));
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $authUser = auth()->user();
        if (!$authUser->hasAnyRole(['Direktur', 'HD / Direktur']) && $user->hasAnyRole(['Direktur', 'HD / Direktur'])) {
            if (request()->wantsJson() || request()->isJson() || request()->ajax()) {
                return response()->json(['error' => 'Hanya Direktur yang dapat menghapus akun Direktur.'], 403);
            }
            return redirect()->route('users.index')->with('error', 'Hanya Direktur yang dapat menghapus akun Direktur.');
        }

        if ($user->id === auth()->id()) {
            if (request()->wantsJson() || request()->isJson() || request()->ajax()) {
                return response()->json(['error' => 'Anda tidak dapat menghapus akun sendiri!'], 403);
            }
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }
        
        $user->delete();

        if (request()->wantsJson() || request()->isJson() || request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus!');
    }

    public function toggleStatus(User $user)
    {
        $authUser = auth()->user();
        if (!$authUser->hasAnyRole(['Direktur', 'HD / Direktur']) && $user->hasAnyRole(['Direktur', 'HD / Direktur'])) {
            return response()->json(['error' => 'Hanya Direktur yang dapat mengubah status akun Direktur.'], 403);
        }

        if ($user->id === auth()->id()) {
            return response()->json([
                'error' => 'Anda tidak dapat mengubah status akun sendiri!'
            ], 403);
        }

        $user->status = $user->status === 'Active' ? 'Inactive' : 'Active';
        $user->save();

        return response()->json([
            'success' => true,
            'status'  => $user->status,
            'message' => 'Status user berhasil diubah menjadi ' . $user->status,
            'user'    => $this->formatUserResponse($user)
        ]);
    }

    public function approveCertification(Request $request, $id)
    {
        $authUser = auth()->user();
        if (!$authUser->hasAnyRole(['Group Leader', 'Lead Divisi', 'Team Leader', 'Lead Maintenance', 'Lead Engineer', 'Direktur', 'HD / Direktur'])) {
            return response()->json([
                'error' => 'Anda tidak memiliki wewenang untuk memverifikasi sertifikasi.'
            ], 403);
        }

        $certification = \App\Models\Certification::find($id);
        if (!$certification) {
            // Fallback: cek jika $id adalah User ID
            $user = User::find($id);
            if ($user) {
                $user->certification_status = 'approved';
                $user->save();
                \App\Models\Certification::where('user_id', $user->id)->update(['status' => 'approved']);
                $user->load(['roles', 'certifications']);

                return response()->json([
                    'success' => true,
                    'message' => 'Sertifikasi berhasil disetujui.',
                    'user'    => $this->formatUserResponse($user),
                ]);
            }
            return response()->json(['error' => 'Data sertifikasi tidak ditemukan.'], 404);
        }

        $certification->status = 'approved';
        $certification->save();

        $user = $certification->user;
        if ($user) {
            $user->certification_status = 'approved';
            $user->save();
            $user->load(['roles', 'certifications']);

            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title'   => 'Sertifikasi Disetujui',
                'message' => 'Selamat! Sertifikasi "' . $certification->name . '" telah diverifikasi dan disetujui oleh ' . $authUser->name . '.',
                'url'     => route('profile.show'),
                'is_read' => false,
            ]);
        }

        return response()->json([
            'success'       => true,
            'message'       => 'Sertifikasi "' . $certification->name . '" berhasil disetujui.',
            'certification' => [
                'id'     => $certification->id,
                'status' => $certification->status,
            ],
            'user'          => $this->formatUserResponse($user),
        ]);
    }

    public function rejectCertification(Request $request, $id)
    {
        $authUser = auth()->user();
        if (!$authUser->hasAnyRole(['Group Leader', 'Lead Divisi', 'Team Leader', 'Lead Maintenance', 'Lead Engineer', 'Direktur', 'HD / Direktur'])) {
            return response()->json([
                'error' => 'Anda tidak memiliki wewenang untuk memverifikasi sertifikasi.'
            ], 403);
        }

        $certification = \App\Models\Certification::find($id);
        if (!$certification) {
            // Fallback: cek jika $id adalah User ID
            $user = User::find($id);
            if ($user) {
                // Hapus semua sertifikat pending milik user
                $certs = \App\Models\Certification::where('user_id', $user->id)->get();
                foreach ($certs as $c) {
                    if ($c->file_path && Storage::disk('public')->exists($c->file_path)) {
                        Storage::disk('public')->delete($c->file_path);
                    }
                    $c->delete();
                }

                $user->certification_status = null;
                $user->certification_file = null;
                $user->save();
                $user->load(['roles', 'certifications']);

                \App\Models\Notification::create([
                    'user_id' => $user->id,
                    'title'   => 'Sertifikasi Ditolak',
                    'message' => 'Pengajuan sertifikasi Anda tidak disetujui / ditolak oleh ' . $authUser->name . '.',
                    'url'     => route('profile.show'),
                    'is_read' => false,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Sertifikasi berhasil ditolak dan dihapus.',
                    'user'    => $this->formatUserResponse($user),
                ]);
            }
            return response()->json(['error' => 'Data sertifikasi tidak ditemukan.'], 404);
        }

        $user = $certification->user;
        $certName = $certification->name;

        // Hapus file fisik jika ada
        if ($certification->file_path && Storage::disk('public')->exists($certification->file_path)) {
            Storage::disk('public')->delete($certification->file_path);
        }

        // Hapus record sertifikasi dari database
        $certification->delete();

        if ($user) {
            $hasOtherApproved = \App\Models\Certification::where('user_id', $user->id)->where('status', 'approved')->exists();
            $hasOtherPending = \App\Models\Certification::where('user_id', $user->id)->where('status', 'pending')->exists();
            $user->certification_status = $hasOtherApproved ? 'approved' : ($hasOtherPending ? 'pending' : null);
            $user->save();
            $user->load(['roles', 'certifications']);

            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title'   => 'Sertifikasi Ditolak',
                'message' => 'Pengajuan sertifikasi "' . $certName . '" tidak disetujui / ditolak oleh ' . $authUser->name . '.',
                'url'     => route('profile.show'),
                'is_read' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sertifikasi "' . $certName . '" ditolak dan dihapus.',
            'user'    => $this->formatUserResponse($user),
        ]);
    }
}