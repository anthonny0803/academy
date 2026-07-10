<?php

namespace Tests\Feature\Tenancy;

use App\Domains\Academics\Enums\SectionSubjectTeacherStatus;
use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Enums\RelationshipType;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Shared\Enums\Sex;
use App\Domains\Students\Models\Student;
use App\Domains\Tenancy\Models\Tenant;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrossTenantWriteValidationTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $otherTenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->otherTenant = Tenant::factory()->create();
    }

    private function tokenFor(User $user): string
    {
        return $user->createToken('api')->plainTextToken;
    }

    public function test_api_enrollment_store_rejects_a_foreign_section(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $student = Student::factory()->create();
        $foreignSection = $this->withinTenant($this->otherTenant, fn () => Section::factory()->create());

        $this->withToken($token)
            ->postJson("/api/v1/students/{$student->id}/enrollments", [
                'section_id' => $foreignSection->id,
            ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonPath('error.fields.section_id', 'La sección seleccionada no existe.');

        $this->assertDatabaseMissing('enrollments', [
            'student_id' => $student->id,
            'section_id' => $foreignSection->id,
        ]);
    }

    public function test_api_enrollment_promote_rejects_a_foreign_target_section(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $promotablePeriod = AcademicPeriod::factory()->promotable()->create();
        $section = Section::factory()->create(['academic_period_id' => $promotablePeriod->id]);
        $enrollment = Enrollment::factory()->create(['section_id' => $section->id]);
        $foreignSection = $this->withinTenant($this->otherTenant, fn () => Section::factory()->create());

        $this->withToken($token)
            ->patchJson("/api/v1/enrollments/{$enrollment->id}/promote", [
                'section_id' => $foreignSection->id,
            ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonPath('error.fields.section_id', 'La sección seleccionada no existe.');
    }

    public function test_api_student_store_rejects_a_foreign_section(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $representative = Representative::factory()->create();
        $foreignSection = $this->withinTenant($this->otherTenant, fn () => Section::factory()->create());

        $this->withToken($token)
            ->postJson("/api/v1/representatives/{$representative->id}/students", [
                'name' => 'Pedro',
                'last_name' => 'Gomez',
                'email' => 'pedro.gomez@example.com',
                'sex' => Sex::Male->value,
                'document_id' => '12345678A',
                'birth_date' => '2010-03-15',
                'relationship_type' => RelationshipType::Father->value,
                'section_id' => $foreignSection->id,
            ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonPath('error.fields.section_id', 'La sección seleccionada no existe.');

        $this->assertDatabaseMissing('users', ['email' => 'pedro.gomez@example.com']);
    }

    public function test_api_section_store_rejects_a_foreign_academic_period(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $foreignPeriod = $this->withinTenant($this->otherTenant, fn () => AcademicPeriod::factory()->create());

        $this->withToken($token)
            ->postJson('/api/v1/sections', [
                'academic_period_id' => $foreignPeriod->id,
                'name' => 'Seccion Cross',
                'capacity' => 30,
            ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonPath('error.fields.academic_period_id', 'El período académico seleccionado no existe.');

        $this->assertDatabaseMissing('sections', ['name' => 'Seccion Cross']);
    }

    public function test_api_section_update_rejects_a_foreign_academic_period(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $section = Section::factory()->create();
        $foreignPeriod = $this->withinTenant($this->otherTenant, fn () => AcademicPeriod::factory()->create());

        $this->withToken($token)
            ->putJson("/api/v1/sections/{$section->id}", [
                'academic_period_id' => $foreignPeriod->id,
                'name' => $section->name,
                'capacity' => $section->capacity,
            ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonPath('error.fields.academic_period_id', 'El período académico seleccionado no existe.');
    }

    public function test_api_grade_store_rejects_a_foreign_enrollment(): void
    {
        $token = $this->tokenFor(User::factory()->developer()->create());
        $column = $this->gradableColumn();
        $foreignEnrollment = $this->withinTenant($this->otherTenant, fn () => Enrollment::factory()->create());

        $this->withToken($token)
            ->postJson("/api/v1/grade-columns/{$column->id}/grades", [
                'enrollment_id' => $foreignEnrollment->id,
                'value' => 5,
            ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonPath('error.fields.enrollment_id', 'El estudiante seleccionado no existe.');

        $this->assertDatabaseMissing('grades', ['enrollment_id' => $foreignEnrollment->id]);
    }

    public function test_api_grade_batch_rejects_a_foreign_enrollment(): void
    {
        $token = $this->tokenFor(User::factory()->developer()->create());
        $column = $this->gradableColumn();
        $foreignEnrollment = $this->withinTenant($this->otherTenant, fn () => Enrollment::factory()->create());

        $response = $this->withToken($token)
            ->postJson("/api/v1/grade-columns/{$column->id}/grades/batch", [
                'grades' => [
                    ['enrollment_id' => $foreignEnrollment->id, 'value' => 5],
                ],
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
        $this->assertArrayHasKey('grades.0.enrollment_id', $response->json('error.fields'));
        $this->assertDatabaseMissing('grades', ['enrollment_id' => $foreignEnrollment->id]);
    }

    public function test_web_reassign_representative_rejects_a_foreign_representative(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $student = Student::factory()->create();
        $foreignRepresentative = $this->withinTenant(
            $this->otherTenant,
            fn () => Representative::factory()->create()
        );

        $this->actingAs($supervisor)
            ->patch(route('students.reassign-representative', $student), [
                'representative_id' => $foreignRepresentative->id,
                'relationship_type' => RelationshipType::Father->value,
                'reason' => 'Cambio de representante',
            ])
            ->assertSessionHasErrors([
                'representative_id' => 'El representante seleccionado no existe.',
            ]);

        $this->assertNotSame($foreignRepresentative->id, $student->fresh()->representative_id);
    }

    public function test_web_subject_teacher_store_rejects_a_foreign_subject(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $teacher = Teacher::factory()->create();
        $foreignSubject = $this->withinTenant($this->otherTenant, fn () => Subject::factory()->create());

        $this->actingAs($supervisor)
            ->post(route('teachers.subjects.store', $teacher), [
                'subjects' => [$foreignSubject->id],
            ])
            ->assertSessionHasErrors([
                'subjects.0' => 'Una de las materias seleccionadas no existe.',
            ]);

        $this->assertDatabaseMissing('subject_teacher', [
            'teacher_id' => $teacher->id,
            'subject_id' => $foreignSubject->id,
        ]);
    }

    public function test_web_section_subject_teacher_store_rejects_foreign_references(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        [$foreignSection, $foreignSubject, $foreignTeacher] = $this->withinTenant(
            $this->otherTenant,
            fn () => [
                Section::factory()->create(),
                Subject::factory()->create(),
                Teacher::factory()->create(),
            ]
        );

        $this->actingAs($supervisor)
            ->post(route('section-subject-teacher.store'), [
                'section_id' => $foreignSection->id,
                'subject_id' => $foreignSubject->id,
                'teacher_id' => $foreignTeacher->id,
                'is_primary' => false,
                'status' => SectionSubjectTeacherStatus::Active->value,
            ])
            ->assertSessionHasErrors(['section_id', 'subject_id', 'teacher_id']);

        $this->assertSame(0, SectionSubjectTeacher::count());
    }

    /**
     * A grade column with a complete (100%) configuration, ready to receive grades.
     */
    private function gradableColumn(): GradeColumn
    {
        $sst = SectionSubjectTeacher::factory()->create();

        return GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 100,
        ]);
    }
}
