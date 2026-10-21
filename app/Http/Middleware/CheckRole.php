<?php
// app/Http/Middleware/CheckRole.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Extract all roles (support both comma and pipe separators, trim spaces)
        $parsedRoles = [];
        foreach ($roles as $roleGroup) {
            $splitRoles = explode('|', $roleGroup);
            foreach ($splitRoles as $r) {
                $r = trim($r);
                if ($r !== '') {
                    $parsedRoles[] = $r;
                }
            }
        }

        if (empty($parsedRoles) || $user->hasAnyRole($parsedRoles)) {
            return $next($request);
        }

        $email = strtolower($user->email ?? '');
        $pos   = strtolower($user->position ?? '');
        $name  = strtolower($user->name ?? '');

        // Fallback: Check if route requires PMO / Project Manager and user is Rizki or Kuncoro or PM
        $isPmoRoute = collect($parsedRoles)->contains(function ($role) {
            return in_array(strtolower($role), ['pmo', 'project manager']);
        });

        if ($isPmoRoute && (str_contains($email, 'rizki') || str_contains($email, 'kuncoro') || str_contains($pos, 'project manager') || str_contains($pos, 'pmo') || str_contains($name, 'rizki'))) {
            try {
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Project Manager', 'guard_name' => 'web']);
                \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'PMO', 'guard_name' => 'web']);
                $user->assignRole(['PMO', 'Project Manager']);
            } catch (\Throwable $e) {}
            return $next($request);
        }

        // General position fallback: if user's position matches any allowed role
        foreach ($parsedRoles as $role) {
            if (strcasecmp($role, $user->position ?? '') === 0 || 
                (str_contains(strtolower($user->position ?? ''), strtolower($role)) && strlen($role) > 2)) {
                try {
                    \Spatie\Permission\Models\Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
                    $user->assignRole($role);
                } catch (\Throwable $e) {}
                return $next($request);
            }
        }

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}