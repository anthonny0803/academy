<?php

namespace App\Domains\Identity\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckDeveloperOrRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $roles)
    {
        $currentUser = Auth::user();
        $allowedRoles = explode('|', $roles);

        if ($currentUser && ($currentUser->isDeveloper() || $currentUser->hasAnyRole($allowedRoles))) {
            return $next($request);
        }

        abort(403, 'No tienes autorización para acceder a esta sección.');
    }
}
