<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\Representative;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'user_id' no se define aquí; se asignará desde el UserFactory.
            // Por defecto, se creará un representante externo si no se especifica.
            'representative_id' => Representative::factory()->create([
                'user_id' => User::factory()->create()->id, // Crea un User para el Representative
                'document_id' => $this->faker->unique()->regexify('[A-Z]{0,1}[0-9]{7,9}[A-Z]{1}'), // Genera un ID de documento único (10 dígitos)
            ])->id, // Obtiene el ID del Representative recién creado

            'student_code' => $this->faker->unique()->numerify('####-####'), // Ejemplo: "1234-5678"
            'document_id' => $this->faker->unique()->regexify('[A-Z]{0,1}[0-9]{7,9}[A-Z]{1}'), // Genera un ID de documento único (10 dígitos)
            'relationship_type' => $this->faker->randomElement(['Padre', 'Madre', 'Tutor Legal', 'Otro Familiar']), // Esto cambiará si es auto-representado
            'birth_date' => $this->faker->dateTimeBetween('-60 years', '-6 years')->format('Y-m-d'), // Edad entre 6 y 60 años
            'is_active' => $this->faker->boolean(95), // 95% de probabilidad de ser activo
        ];
    }

    /**
     * Indicate that the student is their own representative.
     * Indica que el estudiante es su propio representante (mayor de edad).
     *
     * @param \App\Models\User $studentUser El usuario que se está convirtiendo en estudiante.
     * @return static
     */
    public function selfRepresented(User $studentUser): static
    {
        return $this->state(function (array $attributes) use ($studentUser) {
            // Crea el perfil de Representative usando el mismo user_id del estudiante
            $representative = Representative::factory()->create([
                'user_id' => $studentUser->id,
                'phone' => $studentUser->phone_number ?? $this->faker->phoneNumber(), // Usa el teléfono del usuario si existe, sino genera uno
                'address' => $studentUser->address ?? $this->faker->address(), // Usa la dirección del usuario si existe, sino genera una
                'occupation' => $this->faker->jobTitle(),
                'is_active' => $studentUser->is_active,
            ]);

            return [
                'representative_id' => $representative->id,
                'document_id' => $representative->document_id,
                'relationship_type' => 'Auto-representado', // O 'El mismo', 'Self'
            ];
        });
    }
}