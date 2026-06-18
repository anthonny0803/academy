<?php

namespace App\Domains\Identity\Http\Controllers\Api;

use App\Domains\Identity\Http\Requests\Api\Auth\TokenRequest;
use App\Domains\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function token(TokenRequest $request): JsonResponse
    {
        $user = $request->authenticate();

        $token = $user->createToken($request->deviceName())->plainTextToken;

        return response()->json([
            'data' => [
                'token' => $token,
                'tokenType' => 'Bearer',
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }
}
