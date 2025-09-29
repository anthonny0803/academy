<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckActiveUser
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $currentUser = Auth::user();

        if ($currentUser) {
            $teacher = $currentUser->teacher;
            $isAllowedToLogin = $currentUser->isActive() || ($teacher?->isActive() ?? false);

            if (!$isAllowedToLogin) {
                Auth::logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Tu cuenta ha sido desactivada, contacta con el administrador.',
                ]);
            }
        }


        return $next($request);
    }
}
