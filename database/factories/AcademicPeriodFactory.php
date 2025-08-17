<?php

namespace Database\Factories;

use App\Models\AcademicPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcademicPeriod>
 */
class AcademicPeriodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AcademicPeriod::class;

    /**
     * Define the model's default state.
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $periodNames = [
            'Año Escolar',
            '1er Semestre',
            '2do Semestre',
            '1er Trimestre',
            '2do Trimestre',
            '3er Trimestre'
        ];

        // Generamos un año base, por ejemplo, entre el año actual y 3 años en el futuro.
        $baseYear = $this->faker->numberBetween(now()->year, now()->year + 3);

        // Combinamos un tipo de periodo aleatorio con el año base.
        // Ejemplo: "Año Escolar 2025" o "1er Semestre 2026"
        $namePrefix = $this->faker->randomElement($periodNames);
        $fullName = $namePrefix . ' ' . $baseYear;

        // Si el tipo es "Año Escolar", lo extendemos a un rango de años (ej. "2025-2026")
        if ($namePrefix === 'Año Escolar') {
            $fullName = $baseYear . '-' . ($baseYear + 1);
        }

        // Lógica para fechas (simplificada para propósitos de Factory)
        // Puedes refinar esto para que las fechas sean más precisas según el tipo de periodo
        $startDate = $this->faker->dateTimeBetween($baseYear . '-01-01', $baseYear . '-09-01');
        $endDate = $this->faker->dateTimeBetween($startDate, $startDate->format('Y-m-d') . ' +1 year');

        return [
            'name' => $this->faker->unique()->word() . ' ' . $fullName, 
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'is_active' => $this->faker->boolean(20),
        ];
    }
}
