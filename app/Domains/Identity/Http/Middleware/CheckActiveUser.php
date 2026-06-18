<?php

namespace App\Domains\Identity\Http\Middleware;

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

        if (! $currentUser || $currentUser->canAuthenticate()) {
            return $next($request);
        }

        Auth::logout();

        return redirect()->route('login')->withErrors([
            'email' => 'Tu cuenta ha sido desactivada, contacta con el administrador.',
        ]);
    }
}
