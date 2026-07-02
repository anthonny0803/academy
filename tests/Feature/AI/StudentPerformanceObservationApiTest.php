<?php

namespace Tests\Feature\AI;

use App\Domains\AI\Jobs\GenerateStudentPerformanceObservationJob;
use App\Domains\AI\Models\StudentPerformanceObservation;
use App\Domains\Identity\Models\User;
use App\Domains\Students\Models\Student;
use App\Domains\Tenancy\Models\Tenant;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class StudentPerformanceObservationApiTest extends TestCase
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

    public function test_store_dispatches_the_job_and_returns_202_pending(): void
    {
        Queue::fake();
        $user = User::factory()->supervisor()->create();
        $student = Student::factory()->create();

        $response = $this->withToken($this->tokenFor($user))
            ->postJson("/api/v1/students/{$student->id}/performance-observations");

        $response->assertAccepted()
            ->assertJsonPath('data.status', 'pendiente')
            ->assertJsonPath('data.content', null)
            ->assertJsonPath('data.studentId', $student->id)
            ->assertJsonPath('data.requestedById', $user->id);

        Queue::assertPushed(GenerateStudentPerformanceObservationJob::class);

        $this->assertDatabaseHas('student_performance_observations', [
            'student_id' => $student->id,
            'status' => 'pendiente',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_index_returns_paginated_observations(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $student = Student::factory()->create();
        StudentPerformanceObservation::factory()->count(2)->create(['student_id' => $student->id]);
        StudentPerformanceObservation::factory()->create();

        $response = $this->withToken($token)
            ->getJson("/api/v1/students/{$student->id}/performance-observations");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'studentId',
                    'status',
                    'content',
                    'failureReason',
                    'requestedById',
                    'generatedAt',
                    'createdAt',
                    'updatedAt',
                ]],
                'meta' => ['total', 'page', 'perPage'],
            ])
            ->assertJsonPath('meta.total', 2);
    }

    public function test_show_returns_a_completed_observation(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $observation = StudentPerformanceObservation::factory()->completed()->create();

        $response = $this->withToken($token)
            ->getJson("/api/v1/performance-observations/{$observation->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $observation->id)
            ->assertJsonPath('data.status', 'completado')
            ->assertJsonPath('data.content', $observation->content);
    }

    public function test_store_forbidden_for_unauthorized_role(): void
    {
        Queue::fake();
        $token = $this->tokenFor(User::factory()->create());
        $student = Student::factory()->create();

        $response = $this->withToken($token)
            ->postJson("/api/v1/students/{$student->id}/performance-observations");

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'FORBIDDEN');

        Queue::assertNotPushed(GenerateStudentPerformanceObservationJob::class);
        $this->assertDatabaseCount('student_performance_observations', 0);
    }

    public function test_show_of_another_tenant_observation_returns_404(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $otherTenant = Tenant::factory()->create();
        $foreign = $this->withinTenant(
            $otherTenant,
            fn () => StudentPerformanceObservation::factory()->completed()->create()
        );

        $this->withToken($token)
            ->getJson("/api/v1/performance-observations/{$foreign->id}")
            ->assertNotFound()
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_show_unknown_observation_returns_not_found(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());

        $this->withToken($token)
            ->getJson('/api/v1/performance-observations/'.Str::uuid())
            ->assertNotFound()
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_store_requires_authentication(): void
    {
        $student = Student::factory()->create();

        $this->postJson("/api/v1/students/{$student->id}/performance-observations")
            ->assertStatus(401)
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }
}
