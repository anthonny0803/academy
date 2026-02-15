<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\GradeColumn;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        return [
            'enrollment_id' => Enrollment::factory(),
            'grade_column_id' => GradeColumn::factory(),
            'value' => fake()->randomFloat(2, 0, 10),
            'observation' => null,
            'last_modified_by' => null,
        ];
    }

    public function withValue(float $value): static
    {
        return $this->state(['value' => $value]);
    }

    public function scale100(): static
    {
        return $this->state([
            'value' => fake()->randomFloat(2, 1, 100),
        ]);
    }
}
