<?php

namespace App\Domains\Grades\Http\Controllers\Api;

use App\Domains\Grades\Http\Requests\Api\PublicGradesRequest;
use App\Domains\Grades\Http\Resources\PublicRepresentativeGradesResource;
use App\Domains\Grades\Http\Resources\PublicStudentGradesResource;
use App\Domains\Grades\Services\Api\PublicGradesService;
use App\Domains\Shared\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PublicGradesController extends Controller
{
    public function __construct(
        private PublicGradesService $gradesService
    ) {}

    public function studentGrades(PublicGradesRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $data = $this->gradesService->getStudentGrades(
            $credentials['document_id'],
            $credentials['birth_date']
        );

        if (! $data) {
            return $this->credentialsNotFound();
        }

        return response()->json(['data' => new PublicStudentGradesResource($data)]);
    }

    public function representativeGrades(PublicGradesRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $data = $this->gradesService->getRepresentativeGrades(
            $credentials['document_id'],
            $credentials['birth_date']
        );

        if (! $data) {
            return $this->credentialsNotFound();
        }

        return response()->json(['data' => new PublicRepresentativeGradesResource($data)]);
    }

    private function credentialsNotFound(): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 'NOT_FOUND',
                'message' => 'No se encontraron registros para las credenciales proporcionadas.',
            ],
        ], 404);
    }
}
