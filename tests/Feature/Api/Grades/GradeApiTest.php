<?php

namespace Tests\Feature\Api\Grades;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Identity\Models\User;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class GradeApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function tokenFor(User $user): string
    {
        return $user->createToken('api')->plainTextToken;
    }

    /**
     * Build a fully gradable graph: an active assignment with a complete
     * (100%) configuration and an active enrollment in its section.
     *
     * @return array{0: SectionSubjectTeacher, 1: GradeColumn, 2: \App\Domains\Enrollments\Models\Enrollment}
     */
    private function gradableGraph(): array
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $column = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 100,
        ]);
        $student = Student::factory()->inSection($sst->section)->create();
        $enrollment = $student->enrollments()->where('section_id', $sst->section_id)->first();

        return [$sst, $column, $enrollment];
    }

    public function test_index_returns_paginated_grades_for_assignment(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        [$sst, $column, $enrollment] = $this->gradableGraph();
        Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);

        $response = $this->withToken($token)
            ->getJson("/api/v1/section-subject-teachers/{$sst->id}/grades");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'enrollmentId',
                    'gradeColumnId',
                    'value',
                    'observation',
                    'lastModifiedBy',
                    'enrollment' => ['id', 'student' => ['id', 'user' => ['id']]],
                    'gradeColumn' => ['id', 'name', 'weight'],
                    'createdAt',
                    'updatedAt',
                ]],
                'meta' => ['total', 'page', 'perPage'],
            ])
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.gradeColumnId', $column->id);
    }

    public function test_index_requires_authentication(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();

        $response = $this->getJson("/api/v1/section-subject-teachers/{$sst->id}/grades");

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_index_forbidden_for_unauthorized_role(): void
    {
        $token = $this->tokenFor(User::factory()->create());
        $sst = SectionSubjectTeacher::factory()->create();

        $response = $this->withToken($token)
            ->getJson("/api/v1/section-subject-teachers/{$sst->id}/grades");

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
    }

    public function test_index_allowed_for_owner_teacher(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);
        $token = $this->tokenFor($sst->teacher->user);

        $response = $this->withToken($token)
            ->getJson("/api/v1/section-subject-teachers/{$sst->id}/grades");

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.gradeColumnId', $column->id);
    }

    public function test_index_forbidden_for_non_owner_teacher(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $token = $this->tokenFor(Teacher::factory()->create()->user);

        $response = $this->withToken($token)
            ->getJson("/api/v1/section-subject-teachers/{$sst->id}/grades");

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
    }

    public function test_show_returns_a_grade_with_relations(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $grade = Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);

        $response = $this->withToken($token)->getJson("/api/v1/grades/{$grade->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $grade->id)
            ->assertJsonPath('data.enrollmentId', $enrollment->id)
            ->assertJsonPath('data.gradeColumn.id', $column->id)
            ->assertJsonPath('data.enrollment.id', $enrollment->id);
    }

    public function test_show_unknown_grade_returns_not_found(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->getJson('/api/v1/grades/'.Str::uuid());

        $response->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_store_creates_a_grade_as_owner_teacher(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $token = $this->tokenFor($sst->teacher->user);

        $response = $this->withToken($token)->postJson(
            "/api/v1/grade-columns/{$column->id}/grades",
            ['enrollment_id' => $enrollment->id, 'value' => 8.5]
        );

        $response->assertCreated()
            ->assertJsonPath('data.enrollmentId', $enrollment->id)
            ->assertJsonPath('data.gradeColumnId', $column->id)
            ->assertJsonPath('data.value', 8.5);

        $this->assertDatabaseHas('grades', [
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);
    }

    public function test_store_is_forbidden_for_non_owner_teacher(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $token = $this->tokenFor(Teacher::factory()->create()->user);

        $response = $this->withToken($token)->postJson(
            "/api/v1/grade-columns/{$column->id}/grades",
            ['enrollment_id' => $enrollment->id, 'value' => 8.5]
        );

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
        $this->assertDatabaseMissing('grades', [
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);
    }

    public function test_store_is_forbidden_for_supervisor(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->postJson(
            "/api/v1/grade-columns/{$column->id}/grades",
            ['enrollment_id' => $enrollment->id, 'value' => 8.5]
        );

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
    }

    public function test_store_validation_error_returns_envelope(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $token = $this->tokenFor(User::factory()->developer()->create());

        $response = $this->withToken($token)->postJson(
            "/api/v1/grade-columns/{$column->id}/grades",
            []
        );

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure([
                'error' => ['code', 'message', 'fields' => ['enrollment_id', 'value']],
            ]);
    }

    public function test_store_with_incomplete_configuration_returns_a_domain_conflict(): void
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $column = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 60,
        ]);
        $student = Student::factory()->inSection($sst->section)->create();
        $enrollment = $student->enrollments()->where('section_id', $sst->section_id)->first();
        $token = $this->tokenFor(User::factory()->developer()->create());

        $response = $this->withToken($token)->postJson(
            "/api/v1/grade-columns/{$column->id}/grades",
            ['enrollment_id' => $enrollment->id, 'value' => 8.5]
        );

        $response->assertStatus(409)
            ->assertJsonPath('error.code', 'GRADING_CONFIGURATION_INCOMPLETE')
            ->assertJsonPath(
                'error.message',
                'La configuración de evaluaciones debe sumar 100% antes de calificar.'
            );
        $this->assertDatabaseMissing('grades', [
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);
    }

    public function test_store_batch_upserts_grades(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $token = $this->tokenFor(User::factory()->developer()->create());

        $response = $this->withToken($token)->postJson(
            "/api/v1/grade-columns/{$column->id}/grades/batch",
            ['grades' => [['enrollment_id' => $enrollment->id, 'value' => 7.5]]]
        );

        $response->assertOk()
            ->assertJsonPath('data.created', 1)
            ->assertJsonPath('data.total', 1);

        $this->assertDatabaseHas('grades', [
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);
    }

    public function test_store_batch_allowed_for_owner_teacher(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $token = $this->tokenFor($sst->teacher->user);

        $response = $this->withToken($token)->postJson(
            "/api/v1/grade-columns/{$column->id}/grades/batch",
            ['grades' => [['enrollment_id' => $enrollment->id, 'value' => 7.5]]]
        );

        $response->assertOk()
            ->assertJsonPath('data.created', 1)
            ->assertJsonPath('data.total', 1);

        $this->assertDatabaseHas('grades', [
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);
    }

    public function test_store_batch_is_forbidden_for_non_owner_teacher(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $token = $this->tokenFor(Teacher::factory()->create()->user);

        $response = $this->withToken($token)->postJson(
            "/api/v1/grade-columns/{$column->id}/grades/batch",
            ['grades' => [['enrollment_id' => $enrollment->id, 'value' => 7.5]]]
        );

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
        $this->assertDatabaseMissing('grades', [
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);
    }

    public function test_store_batch_is_forbidden_for_supervisor(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->postJson(
            "/api/v1/grade-columns/{$column->id}/grades/batch",
            ['grades' => [['enrollment_id' => $enrollment->id, 'value' => 7.5]]]
        );

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
    }

    public function test_store_batch_rejects_enrollment_from_other_section(): void
    {
        [$sst, $column] = $this->gradableGraph();
        [, , $foreignEnrollment] = $this->gradableGraph();
        $token = $this->tokenFor($sst->teacher->user);

        $response = $this->withToken($token)->postJson(
            "/api/v1/grade-columns/{$column->id}/grades/batch",
            ['grades' => [['enrollment_id' => $foreignEnrollment->id, 'value' => 7.5]]]
        );

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure([
                'error' => ['code', 'message', 'fields' => ['grades.0.enrollment_id']],
            ]);
        $this->assertDatabaseMissing('grades', [
            'enrollment_id' => $foreignEnrollment->id,
            'grade_column_id' => $column->id,
        ]);
    }

    public function test_store_batch_rejects_inactive_enrollment(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $enrollment->status = EnrollmentStatus::Withdrawn->value;
        $enrollment->save();
        $token = $this->tokenFor($sst->teacher->user);

        $response = $this->withToken($token)->postJson(
            "/api/v1/grade-columns/{$column->id}/grades/batch",
            ['grades' => [['enrollment_id' => $enrollment->id, 'value' => 7.5]]]
        );

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
        $this->assertSame(
            'La inscripción del estudiante no está activa.',
            $response->json('error.fields')['grades.0.enrollment_id']
        );
        $this->assertDatabaseMissing('grades', [
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);
    }

    public function test_store_batch_with_mixed_rows_creates_nothing(): void
    {
        [$sst, $column, $activeEnrollment] = $this->gradableGraph();
        $withdrawnEnrollment = Enrollment::factory()->withdrawn()->create([
            'section_id' => $sst->section_id,
        ]);
        $token = $this->tokenFor($sst->teacher->user);

        $response = $this->withToken($token)->postJson(
            "/api/v1/grade-columns/{$column->id}/grades/batch",
            ['grades' => [
                ['enrollment_id' => $activeEnrollment->id, 'value' => 8.0],
                ['enrollment_id' => $withdrawnEnrollment->id, 'value' => 7.5],
            ]]
        );

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
        $this->assertArrayHasKey('grades.1.enrollment_id', $response->json('error.fields'));
        $this->assertSame(0, Grade::count());
    }

    public function test_store_batch_rejects_non_array_grades_payload(): void
    {
        [$sst, $column] = $this->gradableGraph();
        $token = $this->tokenFor($sst->teacher->user);

        $response = $this->withToken($token)->postJson(
            "/api/v1/grade-columns/{$column->id}/grades/batch",
            ['grades' => 'not-an-array']
        );

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
        $this->assertArrayHasKey('grades', $response->json('error.fields'));
    }

    public function test_update_changes_grade_value(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $grade = Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
            'value' => 8.5,
        ]);
        $token = $this->tokenFor(User::factory()->developer()->create());

        $response = $this->withToken($token)->patchJson(
            "/api/v1/grades/{$grade->id}",
            ['value' => 9.5]
        );

        $response->assertOk()
            ->assertJsonPath('data.id', $grade->id)
            ->assertJsonPath('data.value', 9.5);
        $this->assertDatabaseHas('grades', ['id' => $grade->id, 'value' => 9.5]);
    }

    public function test_destroy_is_forbidden_for_supervisor(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $grade = Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->deleteJson("/api/v1/grades/{$grade->id}");

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
        $this->assertDatabaseHas('grades', ['id' => $grade->id, 'deleted_at' => null]);
    }

    public function test_destroy_deletes_grade_as_developer(): void
    {
        [$sst, $column, $enrollment] = $this->gradableGraph();
        $grade = Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);
        $token = $this->tokenFor(User::factory()->developer()->create());

        $response = $this->withToken($token)->deleteJson("/api/v1/grades/{$grade->id}");

        $response->assertNoContent();
        $this->assertSoftDeleted('grades', ['id' => $grade->id]);
    }
}
