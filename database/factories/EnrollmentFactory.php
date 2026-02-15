<?php

namespace Database\Factories;

use App\Enums\EnrollmentStatus;
use App\Models\Enrollment;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'section_id' => Section::factory(),
            'status' => EnrollmentStatus::Active->value,
            'passed' => null,
        ];
    }

    public function completed(bool $passed = true): static
    {
        return $this->state([
            'status' => EnrollmentStatus::Completed->value,
            'passed' => $passed,
        ]);
    }

    public function withdrawn(): static
    {
        return $this->state([
            'status' => EnrollmentStatus::Withdrawn->value,
        ]);
    }

    public function transferred(): static
    {
        return $this->state([
            'status' => EnrollmentStatus::Transferred->value,
        ]);
    }

    public function promoted(): static
    {
        return $this->state([
            'status' => EnrollmentStatus::Promoted->value,
        ]);
    }
}
