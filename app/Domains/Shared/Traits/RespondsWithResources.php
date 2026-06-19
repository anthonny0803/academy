<?php

namespace App\Domains\Shared\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait RespondsWithResources
{
    private const MAX_PER_PAGE = 100;

    /**
     * Resolve a safe per-page value from the request, clamped to [1, MAX_PER_PAGE].
     */
    protected function resolvePerPage(Request $request, int $default = 6): int
    {
        return min(max($request->integer('perPage', $default), 1), self::MAX_PER_PAGE);
    }

    /**
     * Serialize a paginator into the API contract shape:
     * { "data": [...], "meta": { "total", "page", "perPage" } }.
     *
     * @param  class-string<\Illuminate\Http\Resources\Json\JsonResource>  $resourceClass
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator, string $resourceClass): JsonResponse
    {
        return response()->json([
            'data' => $resourceClass::collection($paginator->items()),
            'meta' => [
                'total' => $paginator->total(),
                'page' => $paginator->currentPage(),
                'perPage' => $paginator->perPage(),
            ],
        ]);
    }
}
