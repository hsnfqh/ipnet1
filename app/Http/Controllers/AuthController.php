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
            
            $user->update(['last_login_at' => now()]);

            return redirect()->intended($this->redirectTo());
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

    protected function redirectTo()
    {
        $user = Auth::user();

        // Admin Support / Admin Logistik -> Dashboard Admin Support
        if ($user->hasAnyRole(['Admin Support', 'Admin Logistik', 'Admin'])) {
            return route('admin_support.dashboard');
        }

        // CRO (Customer Relation Officer) -> Dashboard CRO
        if ($user->hasAnyRole(['CRO', 'Customer Relation Officer', 'Customer Relationship Officer'])) {
            return route('cro.dashboard');
        }

        // BDM & BusDev -> Dashboard BDM
        if ($user->hasAnyRole(['BDM', 'BusDev', 'Business Development'])) {
            return route('dashboard.bdm');
        }

        // Solution Architect -> Dashboard Solution Architect
        if ($user->hasAnyRole(['Solution Architect', 'Solutions Architect', 'SA'])) {
            return route('dashboard.architect');
        }

        // Presales -> Dashboard Presales
        if ($user->hasRole('Presales')) {
            return route('dashboard.presales');
        }

        // Sales -> Dashboard Sales
        if ($user->hasRole('Sales')) {
            return route('dashboard.sales');
        }

        // PMO & Project Manager -> Dashboard PMO
        if ($user->hasAnyRole(['PMO', 'Project Manager'])) {
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