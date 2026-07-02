<?php

namespace App\Domains\AI\Jobs;

use App\Domains\AI\Enums\ObservationStatus;
use App\Domains\AI\Models\StudentPerformanceObservation;
use App\Domains\AI\Services\GenerateStudentPerformanceObservationService;
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

        $content = $service->forStudent($this->observation->student);

        $this->observation->update([
            'status' => ObservationStatus::Completed,
            'content' => $content,
            'generated_at' => now(),
        ]);
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
