<?php

namespace Database\Factories;

use App\Domains\Academics\Models\Section;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Enums\RelationshipType;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Students\Enums\StudentSituation;
use App\Domains\Students\Models\Student;
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
            'student_code' => 'ADULT'.fake()->unique()->numerify('######'),
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
            if (! $student->enrollments()->exists()) {
                Enrollment::create([
                    'student_id' => $student->id,
                    'section_id' => Section::factory()->create()->id,
                    'status' => EnrollmentStatus::Active->value,
                ]);
            }

            // Sync representative status like SyncRepresentativeStatusService does
            $representative = $student->representative;
            if ($representative && ! $representative->is_active) {
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
            'student_code' => 'CHILD'.fake()->unique()->numerify('######'),
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
