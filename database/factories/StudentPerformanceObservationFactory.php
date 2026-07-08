<?php

namespace Database\Factories;

use App\Domains\AI\Enums\ObservationStatus;
use App\Domains\AI\Models\StudentPerformanceObservation;
use App\Domains\Students\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentPerformanceObservationFactory extends Factory
{
    protected $model = StudentPerformanceObservation::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'requested_by_id' => null,
            'status' => ObservationStatus::Pending,
            'content' => null,
            'failure_reason' => null,
            'generated_at' => null,
        ];
    }

    public function processing(): static
    {
        return $this->state(fn () => [
            'status' => ObservationStatus::Processing,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => ObservationStatus::Completed,
            'content' => fake()->paragraph(),
            'generated_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status' => ObservationStatus::Failed,
            'failure_reason' => 'The AI provider returned no text content.',
        ]);
    }
}
