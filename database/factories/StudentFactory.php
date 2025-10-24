<?php

namespace Database\Factories;

use App\Enums\RelationshipType;
use App\Models\Representative;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    /**
     * Define the model's default state.
     * Los campos personales (document_id, birth_date) ahora están en User.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // user_id se asigna desde UserFactory con el estado ->student()
            
            // Crear un representante externo por defecto
            'representative_id' => Representative::factory()->create([
                'user_id' => User::factory()->create([
                    'is_active' => false, // Los representatives no acceden al sistema
                    'birth_date' => fake()->dateTimeBetween('-60 years', '-20 years')->format('Y-m-d'),
                ])->id,
            ])->id,
            
            'student_code' => fake()->unique()->numerify('####-####'), // Ejemplo: "1234-5678"
            'relationship_type' => fake()->randomElement([
                RelationshipType::Father->value,
                RelationshipType::Mother->value,
                RelationshipType::LegalGuardian->value,
            ]),
            'is_active' => fake()->boolean(95), // 95% de probabilidad de estar activo
        ];
    }

    /**
     * Indicate that the student is self-represented (mayor de edad).
     * NOTA: Este método se llama desde UserFactory->student(true)
     *
     * @param \App\Models\User $studentUser El usuario del estudiante
     * @return static
     */
    public function selfRepresented(User $studentUser): static
    {
        return $this->state(function (array $attributes) use ($studentUser) {
            // El representante ya fue creado en UserFactory->student(true)
            // Solo necesitamos asignar el representative_id que se pasa
            return [
                'relationship_type' => RelationshipType::SelfRepresented->value,
            ];
        });
    }

    /**
     * Indicate that the student is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the student is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the student's relationship is Father.
     */
    public function withFather(): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship_type' => RelationshipType::Father->value,
        ]);
    }

    /**
     * Indicate that the student's relationship is Mother.
     */
    public function withMother(): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship_type' => RelationshipType::Mother->value,
        ]);
    }

    /**
     * Indicate that the student's relationship is Legal Guardian.
     */
    public function withLegalGuardian(): static
    {
        return $this->state(fn (array $attributes) => [
            'relationship_type' => RelationshipType::LegalGuardian->value,
        ]);
    }
}