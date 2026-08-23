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

        abort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}