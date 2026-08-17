<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    private function formatUserResponse(User $user): array
    {
        return [
            'id'                        => $user->id,
            'name'                      => $user->name,
            'email'                     => $user->email,
            'phone'                     => $user->phone,
            'position'                  => $user->position,
            'status'                    => $user->status,
            'role_name'                 => $user->getRoleNames()->first() ?? 'Tidak Ada Role',
            'roles'                     => $user->getRoleNames()->toArray(),
            'certification'             => $user->certification,
            'certification_file'        => $user->certification_file,
            'certification_status'      => $user->certification_status ?? 'pending',
            'certification_uploaded_at' => $user->certification_uploaded_at ? $user->certification_uploaded_at->format('d M Y H:i') : null,
            'has_certification'         => !empty($user->certification_file),
        ];
    }

    public function index()
    {
        // Load users dengan roles menggunakan Spatie
        $users = User::with('roles')->get()->map(function($user) {
            return $this->formatUserResponse($user);
        });
        
        $roles = Role::all();
        
        return view('users.index', compact('users', 'roles'));
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
        if ($user->id === auth()->id()) {
            return response()->json([
                'error' => 'Anda tidak dapat mengubah status akun sendiri!'
            ], 403);
        }

        $user->status = $user->status === 'Active' ? 'Inactive' : 'Active';
        $user->save();

        return response()->json($this->formatUserResponse($user));
    }

    public function approveCertification(User $user)
    {
        $user->certification_status = 'approved';
        $user->save();

        return response()->json($this->formatUserResponse($user));
    }

    public function rejectCertification(User $user)
    {
        $user->certification_status = 'rejected';
        $user->save();

        return response()->json($this->formatUserResponse($user));
    }
}