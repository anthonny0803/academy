<?php

namespace App\Domains\Grades\Http\Controllers\Api;

use App\Domains\Grades\Http\Requests\Api\PublicGradesRequest;
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
        $data = $this->gradesService->getStudentGrades(
            $request->validated()['document_id'],
            $request->validated()['birth_date']
        );

        if (! $data) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function representativeGrades(PublicGradesRequest $request): JsonResponse
    {
        $data = $this->gradesService->getRepresentativeGrades(
            $request->validated()['document_id'],
            $request->validated()['birth_date']
        );

        if (! $data) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
