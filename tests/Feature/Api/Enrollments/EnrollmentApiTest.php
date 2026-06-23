<?php

namespace Tests\Feature\Api\Enrollments;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Identity\Models\User;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EnrollmentApiTest extends TestCase
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

    public function test_index_returns_paginated_enrollments(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        Student::factory()->count(2)->create();

        $response = $this->withToken($token)->getJson('/api/v1/enrollments');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'studentId',
                    'sectionId',
                    'status',
                    'passed',
                    'student' => ['id', 'user' => ['id', 'name', 'lastName', 'email']],
                    'section' => ['id', 'name', 'academicPeriodId'],
                    'createdAt',
                    'updatedAt',
                ]],
                'meta' => ['total', 'page', 'perPage'],
            ])
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.page', 1);
    }

    public function test_index_filters_by_status(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        Student::factory()->create();
        Enrollment::factory()->withdrawn()->create();

        $response = $this->withToken($token)->getJson('/api/v1/enrollments?status=retirado');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.status', 'retirado');
    }

    public function test_index_filters_by_section(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $section = Section::factory()->create();
        $student = Student::factory()->create();
        $student->enrollments()->delete();
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'section_id' => $section->id,
        ]);
        Student::factory()->create();

        $response = $this->withToken($token)->getJson("/api/v1/enrollments?sectionId={$section->id}");

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.sectionId', $section->id);
    }

    public function test_show_returns_an_enrollment(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $enrollment = Enrollment::factory()->create();

        $response = $this->withToken($token)->getJson("/api/v1/enrollments/{$enrollment->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $enrollment->id)
            ->assertJsonPath('data.studentId', $enrollment->student_id)
            ->assertJsonPath('data.sectionId', $enrollment->section_id)
            ->assertJsonPath('data.student.id', $enrollment->student_id);
    }

    public function test_show_unknown_enrollment_returns_not_found(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $response = $this->withToken($token)->getJson('/api/v1/enrollments/'.Str::uuid());

        $response->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_store_creates_an_enrollment_nested_under_student(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $student = Student::factory()->create();
        $student->enrollments()->delete();
        $section = Section::factory()->create();

        $response = $this->withToken($token)->postJson(
            "/api/v1/students/{$student->id}/enrollments",
            ['section_id' => $section->id]
        );

        $response->assertCreated()
            ->assertJsonPath('data.studentId', $student->id)
            ->assertJsonPath('data.sectionId', $section->id)
            ->assertJsonPath('data.status', 'activo')
            ->assertJsonPath('data.student.id', $student->id);

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'section_id' => $section->id,
            'status' => 'activo',
        ]);
    }

    public function test_store_rejects_duplicate_active_enrollment_in_period(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $period = AcademicPeriod::factory()->create();
        $sectionA = Section::factory()->create(['academic_period_id' => $period->id]);
        $sectionB = Section::factory()->create(['academic_period_id' => $period->id]);
        $student = Student::factory()->create();
        $student->enrollments()->delete();
        Enrollment::factory()->create([
            'student_id' => $student->id,
            'section_id' => $sectionA->id,
        ]);

        $response = $this->withToken($token)->postJson(
            "/api/v1/students/{$student->id}/enrollments",
            ['section_id' => $sectionB->id]
        );

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure([
                'error' => ['code', 'message', 'fields' => ['section_id']],
            ]);
    }

    public function test_store_validation_error_returns_envelope(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $student = Student::factory()->create();

        $response = $this->withToken($token)->postJson(
            "/api/v1/students/{$student->id}/enrollments",
            []
        );

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure([
                'error' => ['code', 'message', 'fields' => ['section_id']],
            ]);
    }

    public function test_destroy_deletes_an_active_enrollment(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $enrollment = Enrollment::factory()->create();

        $response = $this->withToken($token)->deleteJson("/api/v1/enrollments/{$enrollment->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('enrollments', ['id' => $enrollment->id]);
    }

    public function test_destroy_non_active_enrollment_is_forbidden(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $enrollment = Enrollment::factory()->withdrawn()->create();

        $response = $this->withToken($token)->deleteJson("/api/v1/enrollments/{$enrollment->id}");

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
        $this->assertDatabaseHas('enrollments', ['id' => $enrollment->id]);
    }

    public function test_admin_cannot_delete_enrollment(): void
    {
        $token = $this->tokenFor(User::factory()->admin()->create());
        $enrollment = Enrollment::factory()->create();

        $response = $this->withToken($token)->deleteJson("/api/v1/enrollments/{$enrollment->id}");

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
        $this->assertDatabaseHas('enrollments', ['id' => $enrollment->id]);
    }

    public function test_transfer_marks_enrollment_as_transferred(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $period = AcademicPeriod::factory()->transferable()->create();
        $section = Section::factory()->create(['academic_period_id' => $period->id]);
        $student = Student::factory()->inSection($section)->create();
        $enrollment = $student->enrollments()->where('section_id', $section->id)->first();

        $response = $this->withToken($token)->patchJson(
            "/api/v1/enrollments/{$enrollment->id}/transfer",
            ['reason' => 'Cambio de ciudad']
        );

        $response->assertOk()
            ->assertJsonPath('data.id', $enrollment->id)
            ->assertJsonPath('data.status', 'transferido');

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => 'transferido',
        ]);
    }

    public function test_transfer_requires_reason(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $period = AcademicPeriod::factory()->transferable()->create();
        $section = Section::factory()->create(['academic_period_id' => $period->id]);
        $student = Student::factory()->inSection($section)->create();
        $enrollment = $student->enrollments()->where('section_id', $section->id)->first();

        $response = $this->withToken($token)->patchJson(
            "/api/v1/enrollments/{$enrollment->id}/transfer",
            []
        );

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure(['error' => ['code', 'message', 'fields' => ['reason']]]);
    }

    public function test_transfer_in_non_transferable_period_is_forbidden(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $section = Section::factory()->create();
        $student = Student::factory()->inSection($section)->create();
        $enrollment = $student->enrollments()->where('section_id', $section->id)->first();

        $response = $this->withToken($token)->patchJson(
            "/api/v1/enrollments/{$enrollment->id}/transfer",
            ['reason' => 'Cambio de ciudad']
        );

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => 'activo',
        ]);
    }

    public function test_transfer_non_active_enrollment_is_forbidden(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $period = AcademicPeriod::factory()->transferable()->create();
        $section = Section::factory()->create(['academic_period_id' => $period->id]);
        $student = Student::factory()->create();
        $student->enrollments()->delete();
        $enrollment = Enrollment::factory()->withdrawn()->create([
            'student_id' => $student->id,
            'section_id' => $section->id,
        ]);

        $response = $this->withToken($token)->patchJson(
            "/api/v1/enrollments/{$enrollment->id}/transfer",
            ['reason' => 'Cambio de ciudad']
        );

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => 'retirado',
        ]);
    }

    public function test_transfer_forbidden_for_admin(): void
    {
        $token = $this->tokenFor(User::factory()->admin()->create());
        $period = AcademicPeriod::factory()->transferable()->create();
        $section = Section::factory()->create(['academic_period_id' => $period->id]);
        $student = Student::factory()->inSection($section)->create();
        $enrollment = $student->enrollments()->where('section_id', $section->id)->first();

        $response = $this->withToken($token)->patchJson(
            "/api/v1/enrollments/{$enrollment->id}/transfer",
            ['reason' => 'Cambio de ciudad']
        );

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
    }

    public function test_transfer_requires_authentication(): void
    {
        $enrollment = Enrollment::factory()->create();

        $response = $this->patchJson("/api/v1/enrollments/{$enrollment->id}/transfer", ['reason' => 'x']);

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_promote_creates_new_active_enrollment_in_target_section(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $period = AcademicPeriod::factory()->promotable()->create();
        $origin = Section::factory()->create(['academic_period_id' => $period->id]);
        $target = Section::factory()->create(['academic_period_id' => $period->id]);
        $student = Student::factory()->inSection($origin)->create();
        $enrollment = $student->enrollments()->where('section_id', $origin->id)->first();

        $response = $this->withToken($token)->patchJson(
            "/api/v1/enrollments/{$enrollment->id}/promote",
            ['section_id' => $target->id]
        );

        $response->assertOk()
            ->assertJsonPath('data.studentId', $student->id)
            ->assertJsonPath('data.sectionId', $target->id)
            ->assertJsonPath('data.status', 'activo');

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => 'promovido',
        ]);
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'section_id' => $target->id,
            'status' => 'activo',
        ]);
    }

    public function test_promote_requires_section_id(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $period = AcademicPeriod::factory()->promotable()->create();
        $origin = Section::factory()->create(['academic_period_id' => $period->id]);
        $student = Student::factory()->inSection($origin)->create();
        $enrollment = $student->enrollments()->where('section_id', $origin->id)->first();

        $response = $this->withToken($token)->patchJson(
            "/api/v1/enrollments/{$enrollment->id}/promote",
            []
        );

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure(['error' => ['code', 'message', 'fields' => ['section_id']]]);
    }

    public function test_promote_forbidden_for_admin(): void
    {
        $token = $this->tokenFor(User::factory()->admin()->create());
        $period = AcademicPeriod::factory()->promotable()->create();
        $origin = Section::factory()->create(['academic_period_id' => $period->id]);
        $target = Section::factory()->create(['academic_period_id' => $period->id]);
        $student = Student::factory()->inSection($origin)->create();
        $enrollment = $student->enrollments()->where('section_id', $origin->id)->first();

        $response = $this->withToken($token)->patchJson(
            "/api/v1/enrollments/{$enrollment->id}/promote",
            ['section_id' => $target->id]
        );

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
    }

    public function test_promote_non_active_enrollment_is_forbidden(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $period = AcademicPeriod::factory()->promotable()->create();
        $origin = Section::factory()->create(['academic_period_id' => $period->id]);
        $target = Section::factory()->create(['academic_period_id' => $period->id]);
        $student = Student::factory()->create();
        $student->enrollments()->delete();
        $enrollment = Enrollment::factory()->withdrawn()->create([
            'student_id' => $student->id,
            'section_id' => $origin->id,
        ]);

        $response = $this->withToken($token)->patchJson(
            "/api/v1/enrollments/{$enrollment->id}/promote",
            ['section_id' => $target->id]
        );

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => 'retirado',
        ]);
    }

    public function test_update_route_is_not_registered(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $enrollment = Enrollment::factory()->create();

        $response = $this->withToken($token)->patchJson("/api/v1/enrollments/{$enrollment->id}", []);

        $response->assertStatus(405);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/enrollments');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_forbidden_for_unauthorized_role(): void
    {
        $token = $this->tokenFor(User::factory()->create());

        $response = $this->withToken($token)->getJson('/api/v1/enrollments');

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');
    }
}
