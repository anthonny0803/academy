<?php

namespace Tests\Feature\Tenancy;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Models\Subject;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Enums\RelationshipType;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Shared\Enums\Sex;
use App\Domains\Students\Models\Student;
use App\Domains\Students\Services\StoreStudentService;
use App\Domains\Tenancy\Models\Tenant;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantScopedUniqueValidationTest extends TestCase
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

    public function test_api_subject_store_allows_a_name_used_by_another_tenant(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $this->withinTenant($this->otherTenant, fn () => Subject::factory()->create(['name' => 'QUIMICA ORGANICA']));

        $this->withToken($token)
            ->postJson('/api/v1/subjects', [
                'name' => 'QUIMICA ORGANICA',
                'description' => 'Estudio de los compuestos del carbono',
            ])
            ->assertCreated();

        $this->assertDatabaseHas('subjects', [
            'name' => 'QUIMICA ORGANICA',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_api_subject_store_rejects_a_duplicate_name_within_the_tenant(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        Subject::factory()->create(['name' => 'QUIMICA ORGANICA']);

        $this->withToken($token)
            ->postJson('/api/v1/subjects', [
                'name' => 'QUIMICA ORGANICA',
                'description' => 'Estudio de los compuestos del carbono',
            ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonPath('error.fields.name', 'Ya existe una asignatura con este nombre.');
    }

    public function test_api_subject_update_still_ignores_the_current_subject(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $subject = Subject::factory()->create(['name' => 'QUIMICA ORGANICA']);

        $this->withToken($token)
            ->patchJson("/api/v1/subjects/{$subject->id}", [
                'name' => 'QUIMICA ORGANICA',
                'description' => 'Descripción actualizada',
            ])
            ->assertOk();
    }

    public function test_api_academic_period_store_allows_a_name_used_by_another_tenant(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $this->withinTenant($this->otherTenant, fn () => AcademicPeriod::factory()->create(['name' => 'PERIODO 2026']));

        $this->withToken($token)
            ->postJson('/api/v1/academic-periods', [
                'name' => 'PERIODO 2026',
                'start_date' => Carbon::now()->addDays(10)->toDateString(),
                'end_date' => Carbon::now()->addDays(40)->toDateString(),
            ])
            ->assertCreated();

        $this->assertDatabaseHas('academic_periods', [
            'name' => 'PERIODO 2026',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    public function test_api_academic_period_store_rejects_a_duplicate_name_within_the_tenant(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        AcademicPeriod::factory()->create(['name' => 'PERIODO 2026']);

        $this->withToken($token)
            ->postJson('/api/v1/academic-periods', [
                'name' => 'PERIODO 2026',
                'start_date' => Carbon::now()->addDays(10)->toDateString(),
                'end_date' => Carbon::now()->addDays(40)->toDateString(),
            ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonPath('error.fields.name', 'Ya existe un período académico con este nombre.');
    }

    public function test_api_academic_period_update_still_ignores_the_current_period(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $period = AcademicPeriod::factory()->create(['name' => 'PERIODO 2026']);
        Section::factory()->create(['academic_period_id' => $period->id]);

        $this->withToken($token)
            ->patchJson("/api/v1/academic-periods/{$period->id}", [
                'name' => 'PERIODO 2026',
                'notes' => 'Nota revisada',
            ])
            ->assertOk();
    }

    public function test_api_representative_store_still_rejects_an_email_used_by_another_tenant(): void
    {
        $token = $this->tokenFor(User::factory()->supervisor()->create());
        $this->withinTenant(
            $this->otherTenant,
            fn () => User::factory()->create(['email' => 'lucia.ramirez@example.com'])
        );

        // Email is the global login identity: cross-tenant uniqueness is
        // intentional (audit finding 24 scope decision), unlike subject and
        // academic period names.
        $this->withToken($token)
            ->postJson('/api/v1/representatives', [
                'name' => 'Lucia',
                'last_name' => 'Ramirez',
                'email' => 'lucia.ramirez@example.com',
                'sex' => Sex::Female->value,
                'document_id' => '12345678A',
                'birth_date' => '1990-05-20',
                'phone' => '123456789',
                'address' => 'Calle Falsa 123',
                'occupation' => 'Ingeniera',
            ])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonPath('error.fields.email', 'Este correo ya está registrado en el sistema.');
    }

    public function test_student_code_sequence_restarts_for_each_tenant(): void
    {
        $code = $this->storeStudentInCurrentTenant();

        // Everything the other tenant owns must be built inside the closure:
        // a lazy load outside runs with the original tenant restored.
        $otherCode = $this->withinTenant(
            $this->otherTenant,
            fn () => $this->storeStudentInCurrentTenant()
        );

        $this->assertSame('CHILD000001', $code);
        $this->assertSame('CHILD000001', $otherCode);

        $this->assertDatabaseHas('students', [
            'student_code' => 'CHILD000001',
            'tenant_id' => $this->tenant->id,
        ]);
        $this->assertDatabaseHas('students', [
            'student_code' => 'CHILD000001',
            'tenant_id' => $this->otherTenant->id,
        ]);
    }

    public function test_student_code_sequence_keeps_growing_within_the_same_tenant(): void
    {
        $this->assertSame('CHILD000001', $this->storeStudentInCurrentTenant());
        $this->assertSame('CHILD000002', $this->storeStudentInCurrentTenant());
    }

    public function test_student_code_is_still_unique_within_the_same_tenant(): void
    {
        Student::factory()->create(['student_code' => 'CHILD000001']);

        $this->expectException(UniqueConstraintViolationException::class);

        Student::factory()->create(['student_code' => 'CHILD000001']);
    }

    private function storeStudentInCurrentTenant(): string
    {
        $student = app(StoreStudentService::class)->handle(Representative::factory()->create(), [
            'name' => 'Pedro',
            'last_name' => 'Perez',
            'sex' => Sex::Male->value,
            'birth_date' => '2015-03-10',
            'relationship_type' => RelationshipType::Father->value,
            'section_id' => Section::factory()->create()->id,
        ]);

        return $student->student_code;
    }
}
