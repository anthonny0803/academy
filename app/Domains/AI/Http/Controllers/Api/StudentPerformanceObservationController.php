<?php

namespace App\Domains\AI\Http\Controllers\Api;

use App\Domains\AI\Enums\ObservationStatus;
use App\Domains\AI\Http\Resources\StudentPerformanceObservationResource;
use App\Domains\AI\Jobs\GenerateStudentPerformanceObservationJob;
use App\Domains\AI\Models\StudentPerformanceObservation;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\RespondsWithResources;
use App\Domains\Students\Models\Student;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StudentPerformanceObservationController extends Controller
{
    use AuthorizesRequests;
    use RespondsWithResources;

    public function index(Request $request, Student $student): JsonResponse
    {
        $this->authorize('viewAny', [StudentPerformanceObservation::class, $student]);

        $observations = StudentPerformanceObservation::query()
            ->where('student_id', $student->id)
            ->latest()
            ->paginate($this->resolvePerPage($request));

        return $this->paginatedResponse($observations, StudentPerformanceObservationResource::class);
    }

    public function store(Request $request, Student $student): JsonResponse
    {
        $this->authorize('create', [StudentPerformanceObservation::class, $student]);

        $observation = StudentPerformanceObservation::create([
            'student_id' => $student->id,
            'requested_by_id' => $request->user()->id,
            'status' => ObservationStatus::Pending,
        ]);

        GenerateStudentPerformanceObservationJob::dispatch($observation);

        return (new StudentPerformanceObservationResource($observation))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function show(StudentPerformanceObservation $performanceObservation): StudentPerformanceObservationResource
    {
        $this->authorize('view', $performanceObservation);

        return new StudentPerformanceObservationResource($performanceObservation);
    }
}
