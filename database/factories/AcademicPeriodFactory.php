<?php

namespace Database\Factories;

use App\Models\AcademicPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicPeriodFactory extends Factory
{
    protected $model = AcademicPeriod::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+6 months');

        return [
            'name' => 'Curso ' . fake()->unique()->numerify('####-####'),
            'notes' => fake()->optional()->sentence(),
            'start_date' => $startDate,
            'end_date' => fake()->dateTimeBetween($startDate, '+2 years'),
            'min_grade' => 0,
            'max_grade' => 10,
            'passing_grade' => 5,
            'is_promotable' => false,
            'is_transferable' => false,
            'is_active' => true,
        ];
    }

    public function scale100(): static
    {
        return $this->state([
            'min_grade' => 1,
            'max_grade' => 100,
            'passing_grade' => 60,
        ]);
    }

    public function promotable(): static
    {
        return $this->state(['is_promotable' => true]);
    }

    public function transferable(): static
    {
        return $this->state([
            'is_promotable' => true,
            'is_transferable' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
