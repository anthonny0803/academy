<?php

namespace Database\Factories;

use App\Models\GradeColumn;
use App\Models\SectionSubjectTeacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeColumnFactory extends Factory
{
    protected $model = GradeColumn::class;

    private static array $evaluations = [
        'Examen parcial', 'Examen final', 'Trabajo practico',
        'Participacion', 'Exposicion', 'Proyecto',
        'Tarea', 'Quiz', 'Laboratorio',
    ];

    public function definition(): array
    {
        return [
            'section_subject_teacher_id' => SectionSubjectTeacher::factory(),
            'name' => fake()->unique()->randomElement(self::$evaluations)
                ?? 'Evaluacion ' . fake()->unique()->numerify('##'),
            'weight' => 100,
            'display_order' => 0,
            'observation' => null,
        ];
    }

    public function withWeight(float $weight): static
    {
        return $this->state(['weight' => $weight]);
    }
}
