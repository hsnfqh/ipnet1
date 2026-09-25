<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $remember = $request->boolean('remember');

        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->status === 'Inactive') {
            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator sistem.',
            ])->onlyInput('email');
        }

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            $user->update(['last_login_at' => now()]);

            // Auto-heal role jika role Spatie belum ter-sync di database hosting
            $this->ensureUserRoles($user);

            return redirect($this->redirectTo());
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    protected function ensureUserRoles($user)
    {
        if (!$user) return;

        $email = strtolower($user->email ?? '');
        $name = strtolower($user->name ?? '');
        $pos = strtolower($user->position ?? '');

        // Jika roles kosong atau akun inti presales/sa/sales/bdm/cro/pmo
        if ($user->roles->isEmpty() || str_contains($email, 'akbar@ipnetsolusindo.com') || str_contains($email, 'aris') || str_contains($email, 'cro')) {
            try {
                if ($email === 'akbar@ipnetsolusindo.com' || str_contains($pos, 'pre-sales') || str_contains($pos, 'presales')) {
                    Role::firstOrCreate(['name' => 'Presales', 'guard_name' => 'web']);
                    Role::firstOrCreate(['name' => 'Pre-Sales', 'guard_name' => 'web']);
                    $user->syncRoles(['Presales', 'Pre-Sales']);
                } elseif (str_contains($email, 'aris') || str_contains($name, 'aris') || str_contains($pos, 'solution architect') || str_contains($pos, 'architect')) {
                    Role::firstOrCreate(['name' => 'Solution Architect', 'guard_name' => 'web']);
                    Role::firstOrCreate(['name' => 'Solutions Architect', 'guard_name' => 'web']);
                    $user->syncRoles(['Solution Architect', 'Solutions Architect']);
                } elseif (str_contains($email, 'cro') || str_contains($pos, 'cro') || str_contains($pos, 'customer relation')) {
                    Role::firstOrCreate(['name' => 'CRO', 'guard_name' => 'web']);
                    Role::firstOrCreate(['name' => 'Customer Relation Officer', 'guard_name' => 'web']);
                    $user->syncRoles(['CRO', 'Customer Relation Officer']);
                } elseif (str_contains($pos, 'business development') || str_contains($pos, 'bdm')) {
                    Role::firstOrCreate(['name' => 'BDM', 'guard_name' => 'web']);
                    Role::firstOrCreate(['name' => 'BusDev', 'guard_name' => 'web']);
                    Role::firstOrCreate(['name' => 'Business Development', 'guard_name' => 'web']);
                    $user->syncRoles(['BDM', 'BusDev', 'Business Development']);
                } elseif (str_contains($pos, 'sales') || str_contains($pos, 'account manager') || str_contains($email, 'raiza')) {
                    Role::firstOrCreate(['name' => 'Sales', 'guard_name' => 'web']);
                    Role::firstOrCreate(['name' => 'Account Manager', 'guard_name' => 'web']);
                    $user->syncRoles(['Sales', 'Account Manager']);
                } elseif (str_contains($pos, 'project manager') || str_contains($email, 'rizki')) {
                    Role::firstOrCreate(['name' => 'Project Manager', 'guard_name' => 'web']);
                    Role::firstOrCreate(['name' => 'PMO', 'guard_name' => 'web']);
                    $user->syncRoles(['PMO', 'Project Manager']);
                } elseif (str_contains($email, 'kuncoro') || str_contains($pos, 'pmo')) {
                    Role::firstOrCreate(['name' => 'PMO', 'guard_name' => 'web']);
                    Role::firstOrCreate(['name' => 'Lead Divisi', 'guard_name' => 'web']);
                    $user->syncRoles(['PMO', 'Project Manager', 'Lead Divisi']);
                } elseif (str_contains($email, 'susanto') || str_contains($name, 'susanto')) {
                    Role::firstOrCreate(['name' => 'Division Head', 'guard_name' => 'web']);
                    $user->syncRoles(['Division Head', 'Group Leader Delivery & Operation', 'Lead Divisi']);
                } elseif (str_contains($email, 'hariyadi') || str_contains($name, 'hariyadi')) {
                    Role::firstOrCreate(['name' => 'Director', 'guard_name' => 'web']);
                    $user->syncRoles(['Director', 'Direktur', 'HD / Direktur']);
                }
                $user->load('roles');
            } catch (\Throwable $e) {
                // Ignore if DB error
            }
        }
    }

    protected function redirectTo()
    {
        $user = Auth::user();
        if (!$user) return '/login';

        $this->ensureUserRoles($user);

        $email = strtolower($user->email ?? '');
        $name  = strtolower($user->name ?? '');
        $pos   = strtolower($user->position ?? '');

        // Engineer (Field / Technical Delivery) selalu ke dashboard engineer
        if ($user->hasRole('Engineer') && !$user->hasAnyRole(['Director', 'Direktur', 'Division Head', 'Group Leader', 'Sales', 'BDM', 'Presales', 'Solution Architect'])) {
            return route('dashboard.engineer');
        }

        // Admin Support / Admin Logistik -> Dashboard Admin Support
        if ($user->hasAnyRole(['Admin Support', 'Admin Logistik', 'Admin']) || str_contains($pos, 'admin support')) {
            return route('admin_support.dashboard');
        }

        // CRO (Customer Relation Officer) -> Dashboard CRO
        if ($user->hasAnyRole(['CRO', 'Customer Relation Officer', 'Customer Relationship Officer']) || str_contains($email, 'cro') || str_contains($pos, 'customer relation')) {
            return route('cro.dashboard');
        }

        // BDM & BusDev -> Dashboard BDM
        if ($user->hasAnyRole(['BDM', 'BusDev', 'Business Development']) || str_contains($pos, 'business development') || str_contains($pos, 'bdm')) {
            return route('dashboard.bdm');
        }

        // Solution Architect -> Dashboard Solution Architect
        if ($user->hasAnyRole(['Solution Architect', 'Solutions Architect', 'SA', 'Tech Develop']) || str_contains($email, 'aris') || str_contains($pos, 'solution architect') || str_contains($pos, 'architect')) {
            return route('dashboard.architect');
        }

        // Presales (Hubungan dengan Sales, BD, SA, Direktur, Head Division) -> Dashboard Presales
        if ($user->hasAnyRole(['Presales', 'Pre-Sales']) || str_contains($pos, 'pre-sales') || str_contains($pos, 'presales')) {
            return route('dashboard.presales');
        }

        // Sales & Account Manager -> Dashboard Sales
        if ($user->hasAnyRole(['Sales', 'Account Manager']) || str_contains($pos, 'sales') || str_contains($pos, 'account manager')) {
            return route('dashboard.sales');
        }

        // PMO & Project Manager -> Dashboard PMO
        if ($user->hasAnyRole(['PMO', 'Project Manager']) || str_contains($email, 'rizki') || str_contains($email, 'kuncoro') || str_contains($pos, 'project manager')) {
            return route('pmo.dashboard');
        }

        // Maintenance & Managed Service -> Dashboard Managed Service (Operate)
        if ($user->hasAnyRole(['Lead Maintenance', 'Maintenance']) || ($user->division && str_contains(strtolower($user->division->name), 'maintenance'))) {
            return route('ms.dashboard');
        }

        // Level manajerial (Direktur, HD, Group Leader, Lead Divisi, Team Leader, Lead Engineer) -> dashboard manajerial
        if (\App\Helpers\ScopeHelper::isManagerial($user)) {
            return route('dashboard.lead');
        }

        // Engineer (semua level) -> dashboard engineer
        return route('dashboard.engineer');
    }

    public function showRegister()
    {
        $roles = Role::where('name', '!=', 'Lead Engineer')->get();
        return view('auth.register', compact('roles'));
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        
        $user = User::create($data);
        $user->assignRole($data['role']);

        Auth::login($user);

        return redirect($this->redirectTo())
            ->with('success', 'Akun berhasil dibuat! Selamat datang di Field System Management.');
    }
}