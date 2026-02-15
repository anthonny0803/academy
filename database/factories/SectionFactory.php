<?php

namespace Database\Factories;

use App\Models\AcademicPeriod;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectionFactory extends Factory
{
    protected $model = Section::class;

    public function definition(): array
    {
        return [
            'academic_period_id' => AcademicPeriod::factory(),
            'name' => fake()->unique()->numerify('Seccion ##'),
            'description' => fake()->optional()->sentence(),
            'capacity' => fake()->numberBetween(20, 40),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function withCapacity(int $capacity): static
    {
        return $this->state(['capacity' => $capacity]);
    }
}
