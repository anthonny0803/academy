<?php

namespace Database\Factories;

use App\Enums\EnrollmentStatus;
use App\Enums\RelationshipType;
use App\Enums\Role;
use App\Enums\StudentSituation;
use App\Models\Enrollment;
use App\Models\Representative;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state([
                'is_active' => false,
                'password' => null,
            ]),
            'representative_id' => Representative::factory(),
            'student_code' => 'ADULT' . fake()->unique()->numerify('######'),
            'relationship_type' => fake()->randomElement([
                RelationshipType::Father->value,
                RelationshipType::Mother->value,
                RelationshipType::LegalGuardian->value,
            ]),
            'situation' => StudentSituation::Active,
            'is_active' => true,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Student $student) {
            // Assign role like StoreStudentService does
            $student->user->assignRole(Role::Student->value);

            // Create initial enrollment like StoreStudentService does
            if (!$student->enrollments()->exists()) {
                Enrollment::create([
                    'student_id' => $student->id,
                    'section_id' => Section::factory()->create()->id,
                    'status' => EnrollmentStatus::Active->value,
                ]);
            }

            // Sync representative status like SyncRepresentativeStatusService does
            $representative = $student->representative;
            if ($representative && !$representative->is_active) {
                $representative->update(['is_active' => true]);
                $representative->user->update(['is_active' => true]);
            }
        });
    }

    public function child(): static
    {
        return $this->state(fn () => [
            'user_id' => User::factory()->minor()->state([
                'is_active' => false,
                'password' => null,
            ]),
            'student_code' => 'CHILD' . fake()->unique()->numerify('######'),
        ]);
    }

    public function selfRepresented(): static
    {
        return $this->state([
            'relationship_type' => RelationshipType::SelfRepresented->value,
        ]);
    }

    public function inactive(): static
    {
        return $this->state([
            'is_active' => false,
            'situation' => StudentSituation::Inactive,
        ]);
    }

    /**
     * Create student with enrollment in a specific section.
     */
    public function inSection(Section $section): static
    {
        return $this->afterCreating(function (Student $student) use ($section) {
            // Remove auto-created enrollment and use the specified section
            $student->enrollments()->delete();
            Enrollment::create([
                'student_id' => $student->id,
                'section_id' => $section->id,
                'status' => EnrollmentStatus::Active->value,
            ]);
        });
    }
}
