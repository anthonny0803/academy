<?php

namespace App\Domains\AI\Jobs;

use App\Domains\AI\Enums\ObservationStatus;
use App\Domains\AI\Models\StudentPerformanceObservation;
use App\Domains\AI\Services\GenerateStudentPerformanceObservationService;
use App\Domains\Grades\Support\StudentPerformanceRelations;
use App\Domains\Students\Models\Student;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenerateStudentPerformanceObservationJob implements ShouldQueue
{
    use Queueable;

    /**
     * User-facing failure message. The technical cause is logged instead of
     * exposed, so provider internals never leak through the API response.
     */
    private const FAILURE_MESSAGE = 'No se pudo generar la observación. Inténtalo de nuevo más tarde.';

    public int $tries = 3;

    public int $timeout = 60;

    public function __construct(
        private StudentPerformanceObservation $observation
    ) {}

    public function handle(GenerateStudentPerformanceObservationService $service): void
    {
        $this->observation->update(['status' => ObservationStatus::Processing]);

        $content = $service->forStudent($this->loadStudentWithPerformanceTree());

        $this->observation->update([
            'status' => ObservationStatus::Completed,
            'content' => $content,
            'generated_at' => now(),
        ]);
    }

    /**
     * The summary walks the whole academic record, so the tree is loaded up
     * front: an N+1 here burns the job timeout and retries the provider call.
     */
    private function loadStudentWithPerformanceTree(): Student
    {
        return $this->observation
            ->student()
            ->with(StudentPerformanceRelations::forStudent())
            ->firstOrFail();
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Student performance observation generation failed.', [
            'observation_id' => $this->observation->id,
            'exception' => $exception?->getMessage(),
        ]);

        $this->observation->update([
            'status' => ObservationStatus::Failed,
            'failure_reason' => self::FAILURE_MESSAGE,
        ]);
    }
}
