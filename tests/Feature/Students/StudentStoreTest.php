<?php

namespace Tests\Feature\Students;

use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Enums\RelationshipType;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Shared\Enums\Sex;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentStoreTest extends TestCase
{
    use RefreshDatabase;

    private const DUPLICATE_PROFILE_ERROR = 'Este representante ya tiene un perfil de estudiante registrado, puedes inscribirlo directamente en el módulo de estudiantes.';

    private const POLICY_DENIED_ERROR = 'No tienes autorización para gestionar estudiantes.';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Pedro',
            'last_name' => 'Gomez',
            'email' => 'pedro.gomez@example.com',
            'sex' => Sex::Male->value,
            'document_id' => '12345678A',
            'birth_date' => '2010-03-15',
            'relationship_type' => RelationshipType::Father->value,
            'section_id' => Section::factory()->create()->id,
        ], $overrides);
    }

    private function createUrl(Representative $representative): string
    {
        return route('representatives.students.create', $representative);
    }

    private function storeUrl(Representative $representative): string
    {
        return route('representatives.students.store', $representative);
    }

    public function test_store_registers_a_student_and_redirects_with_a_success_flash(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $representative = Representative::factory()->create();
        $section = Section::factory()->create();

        $response = $this->actingAs($supervisor)
            ->from($this->createUrl($representative))
            ->post($this->storeUrl($representative), $this->validPayload([
                'section_id' => $section->id,
            ]));

        $response->assertSessionHasNoErrors();

        $student = Student::firstWhere('representative_id', $representative->id);
        $this->assertNotNull($student);

        $response->assertRedirect(route('students.show', $student));
        $response->assertSessionHas('success', '¡Estudiante registrado correctamente!');

        $user = $student->user;
        $this->assertSame('PEDRO', $user->name);
        $this->assertSame('GOMEZ', $user->last_name);
        $this->assertFalse($user->is_active);
        $this->assertTrue($user->hasRole(Role::Student->value));

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Active->value,
        ]);

        $this->assertDatabaseHas('representatives', [
            'id' => $representative->id,
            'is_active' => true,
        ]);
    }

    public function test_store_reuses_the_representative_user_when_self_represented(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $representative = Representative::factory()->create();
        $representativeUser = $representative->user;
        $usersBefore = User::count();

        $response = $this->actingAs($supervisor)
            ->from($this->createUrl($representative))
            ->post($this->storeUrl($representative), $this->validPayload([
                'is_self_represented' => true,
                'relationship_type' => RelationshipType::SelfRepresented->value,
                'email' => $representativeUser->email,
                'document_id' => $representativeUser->document_id,
            ]));

        $response->assertSessionHasNoErrors();
        $this->assertSame($usersBefore, User::count());

        $student = Student::firstWhere('representative_id', $representative->id);
        $this->assertNotNull($student);
        $this->assertSame($representativeUser->id, $student->user_id);
        $this->assertTrue($representativeUser->fresh()->hasRole(Role::Student->value));

        $response->assertRedirect(route('students.show', $student));
        $response->assertSessionHas('success', '¡Estudiante registrado correctamente!');
    }

    public function test_store_reports_validation_errors_through_the_session(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $representative = Representative::factory()->create();

        $response = $this->actingAs($supervisor)
            ->from($this->createUrl($representative))
            ->post($this->storeUrl($representative), []);

        $response->assertRedirect($this->createUrl($representative));
        $response->assertSessionHasErrors([
            'name',
            'last_name',
            'sex',
            'document_id',
            'birth_date',
            'relationship_type',
            'section_id',
        ]);

        $this->assertSame(0, Student::count());
    }

    public function test_store_rejects_self_representation_when_the_representative_is_already_a_student(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $representative = Representative::factory()->create();
        Student::factory()->selfRepresented()->create([
            'user_id' => $representative->user_id,
            'representative_id' => $representative->id,
        ]);

        $response = $this->actingAs($supervisor)
            ->from($this->createUrl($representative))
            ->post($this->storeUrl($representative), $this->validPayload([
                'is_self_represented' => true,
                'relationship_type' => RelationshipType::SelfRepresented->value,
            ]));

        $response->assertRedirect($this->createUrl($representative));
        $response->assertSessionHasErrors(['relationship_type' => self::DUPLICATE_PROFILE_ERROR]);

        $this->assertSame(1, Student::count());
    }

    public function test_store_is_forbidden_for_a_role_outside_the_admin_panel(): void
    {
        $teacher = Teacher::factory()->create();
        $representative = Representative::factory()->create();

        $response = $this->actingAs($teacher->user)
            ->from($this->createUrl($representative))
            ->post($this->storeUrl($representative), $this->validPayload());

        // CheckDeveloperOrRole aborts with a plain HttpException, which the web
        // renderable does not translate: the browser gets a 403, not a redirect.
        $response->assertForbidden();
        $this->assertSame(0, Student::count());
    }

    public function test_store_redirects_with_a_flash_error_when_the_policy_denies(): void
    {
        // An inactive user with an active teacher profile still authenticates
        // (canAuthenticate is an OR) and the Supervisor role clears the role
        // middleware, so this is the only combination that reaches the policy.
        $teacher = Teacher::factory()->create();
        $teacher->user->assignRole(Role::Supervisor->value);
        $representative = Representative::factory()->create();

        $response = $this->actingAs($teacher->user)
            ->from($this->createUrl($representative))
            ->post($this->storeUrl($representative), $this->validPayload());

        $response->assertRedirect($this->createUrl($representative));
        $response->assertSessionHas('error', self::POLICY_DENIED_ERROR);

        $this->assertSame(0, Student::count());
    }
}
