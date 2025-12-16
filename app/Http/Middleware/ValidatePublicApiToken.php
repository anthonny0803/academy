<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidatePublicApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $validToken = config('services.public_api.token');

        if (!$token || !$validToken || !hash_equals($validToken, $token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token de acceso inválido.',
            ], 401);
        }

        return $next($request);
    }
}