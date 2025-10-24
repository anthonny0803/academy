<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\Sex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests realistas del modelo User basados en la lógica de negocio REAL.
 * 
 * LÓGICA DE ACCESO AL SISTEMA (CheckActiveUser middleware):
 * - Puede acceder si: user.is_active = true OR teacher.is_active = true
 * - User/Admin/Supervisor: user.is_active = true → SÍ acceden
 * - Teacher: user.is_active = false, teacher.is_active = true → SÍ accede (tiene password)
 * - Representative/Student: user.is_active = false → NO acceden al sistema
 */
class UserTest extends TestCase
{
    use RefreshDatabase;

    // ========================================
    // TESTS DE MUTATORS (normalización de datos)
    // ========================================

    /** @test */
    public function it_converts_email_to_lowercase()
    {
        $user = User::factory()->admin()->create([
            'email' => 'TESTUSER@EXAMPLE.COM',
        ]);

        $this->assertEquals('testuser@example.com', $user->email);
    }

    /** @test */
    public function it_converts_name_and_last_name_to_uppercase()
    {
        $user = User::factory()->supervisor()->create([
            'name' => 'maría',
            'last_name' => 'gonzález',
        ]);

        $this->assertEquals('MARÍA', $user->name);
        $this->assertEquals('GONZÁLEZ', $user->last_name);
    }

    /** @test */
    public function it_cleans_document_id_format()
    {
        $user = User::factory()->representative()->create([
            'document_id' => '12345678-Z',
        ]);

        $this->assertEquals('12345678Z', $user->document_id);

        $user2 = User::factory()->representative()->create([
            'document_id' => 'x-1234567-l',
        ]);

        $this->assertEquals('X1234567L', $user2->document_id);
    }

    /** @test */
    public function it_cleans_phone_format()
    {
        $user = User::factory()->representative()->create([
            'phone' => '612 345 678',
        ]);

        $this->assertEquals('612345678', $user->phone);
    }

    /** @test */
    public function it_cleans_address_format()
    {
        $user = User::factory()->representative()->create([
            'address' => '  calle mayor 123  ',
        ]);

        $this->assertEquals('CALLE MAYOR 123', $user->address);
    }

    /** @test */
    public function it_cleans_occupation_format()
    {
        $user = User::factory()->representative()->create([
            'occupation' => '  ingeniero  ',
        ]);

        $this->assertEquals('INGENIERO', $user->occupation);
    }

    // ========================================
    // TESTS DE ACCESSORS
    // ========================================

    /** @test */
    public function it_returns_full_name_correctly()
    {
        $user = User::factory()->admin()->create([
            'name' => 'CARLOS',
            'last_name' => 'RODRIGUEZ',
        ]);

        $this->assertEquals('CARLOS RODRIGUEZ', $user->full_name);
    }

    /** @test */
    public function it_calculates_age_from_birth_date()
    {
        // Representative y Student SÍ tienen birth_date
        $user = User::factory()->representative()->withAge(25)->create();

        $this->assertEquals(25, $user->age);
    }

    /** @test */
    public function it_returns_null_age_when_no_birth_date()
    {
        // User y Teacher NO tienen birth_date
        $user = User::factory()->admin()->withoutBirthDate()->create();

        $this->assertNull($user->age);
    }

    // ========================================
    // TESTS DE ROLES
    // ========================================

    /** @test */
    public function it_identifies_developer_correctly()
    {
        $developer = User::factory()->developer()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue($developer->isDeveloper());
        $this->assertFalse($admin->isDeveloper());
    }

    /** @test */
    public function it_identifies_supervisor_correctly()
    {
        $supervisor = User::factory()->supervisor()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue($supervisor->isSupervisor());
        $this->assertFalse($admin->isSupervisor());
    }

    /** @test */
    public function it_identifies_admin_correctly()
    {
        $admin = User::factory()->admin()->create();
        $supervisor = User::factory()->supervisor()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($supervisor->isAdmin());
    }

    /** @test */
    public function it_identifies_teacher_correctly()
    {
        $teacher = User::factory()->teacher()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue($teacher->isTeacher());
        $this->assertFalse($admin->isTeacher());
    }

    /** @test */
    public function it_identifies_representative_correctly()
    {
        $representative = User::factory()->representative()->create();
        $teacher = User::factory()->teacher()->create();

        $this->assertTrue($representative->isRepresentative());
        $this->assertFalse($teacher->isRepresentative());
    }

    /** @test */
    public function it_identifies_student_correctly()
    {
        $student = User::factory()->student()->create();
        $teacher = User::factory()->teacher()->create();

        $this->assertTrue($student->isStudent());
        $this->assertFalse($teacher->isStudent());
    }

    /** @test */
    public function it_identifies_employee_correctly()
    {
        // Employee = Developer, Supervisor, Admin, o Teacher
        $developer = User::factory()->developer()->create();
        $supervisor = User::factory()->supervisor()->create();
        $admin = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create();
        $representative = User::factory()->representative()->create();
        $student = User::factory()->student()->create();

        $this->assertTrue($developer->isEmployee());
        $this->assertTrue($supervisor->isEmployee());
        $this->assertTrue($admin->isEmployee());
        $this->assertTrue($teacher->isEmployee());
        $this->assertFalse($representative->isEmployee());
        $this->assertFalse($student->isEmployee());
    }

    // ========================================
    // TESTS DE SCOPES
    // ========================================

    /** @test */
    public function it_can_search_users_by_name()
    {
        User::factory()->admin()->create(['name' => 'CARLOS', 'last_name' => 'RODRIGUEZ']);
        User::factory()->admin()->create(['name' => 'MARIA', 'last_name' => 'GONZALEZ']);
        User::factory()->admin()->create(['name' => 'PEDRO', 'last_name' => 'MARTINEZ']);

        $results = User::search('carlos')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('CARLOS', $results->first()->name);
    }

    /** @test */
    public function it_can_search_users_by_last_name()
    {
        User::factory()->admin()->create(['name' => 'CARLOS', 'last_name' => 'RODRIGUEZ']);
        User::factory()->admin()->create(['name' => 'MARIA', 'last_name' => 'GONZALEZ']);

        $results = User::search('gonzalez')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('GONZALEZ', $results->first()->last_name);
    }

    /** @test */
    public function it_can_search_users_by_email()
    {
        User::factory()->admin()->create(['email' => 'carlos@example.com']);
        User::factory()->admin()->create(['email' => 'maria@example.com']);

        $results = User::search('carlos@')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('carlos@example.com', $results->first()->email);
    }

    /** @test */
    public function it_filters_active_users_with_scope()
    {
        // user.is_active = true: User, Admin, Supervisor
        User::factory()->count(3)->admin()->create();
        
        // user.is_active = false: Teacher, Representative, Student
        User::factory()->count(2)->teacher()->create();

        $activeUsers = User::active()->get();

        $this->assertCount(3, $activeUsers);
        $activeUsers->each(function ($user) {
            $this->assertTrue($user->is_active);
        });
    }

    /** @test */
    public function it_filters_inactive_users_with_scope()
    {
        User::factory()->count(3)->admin()->create(); // is_active = true
        User::factory()->count(2)->teacher()->create(); // is_active = false

        $inactiveUsers = User::inactive()->get();

        $this->assertCount(2, $inactiveUsers);
        $inactiveUsers->each(function ($user) {
            $this->assertFalse($user->is_active);
        });
    }

    /** @test */
    public function it_orders_users_by_name()
    {
        User::factory()->admin()->create(['name' => 'CARLOS']);
        User::factory()->admin()->create(['name' => 'ANA']);
        User::factory()->admin()->create(['name' => 'PEDRO']);

        $users = User::orderByName()->get();

        $this->assertEquals('ANA', $users->first()->name);
        $this->assertEquals('PEDRO', $users->last()->name);
    }

    /** @test */
    public function it_filters_users_by_role_scope()
    {
        User::factory()->count(3)->admin()->create();
        User::factory()->count(2)->supervisor()->create();
        User::factory()->count(1)->teacher()->create();

        $admins = User::withRole(Role::Admin->value)->get();
        $supervisors = User::withRole(Role::Supervisor->value)->get();

        $this->assertCount(3, $admins);
        $this->assertCount(2, $supervisors);
    }

    // ========================================
    // TESTS DE MÉTODOS HELPER
    // ========================================

    /** @test */
    public function it_identifies_male_users_correctly()
    {
        $maleUser = User::factory()->admin()->male()->create();
        $femaleUser = User::factory()->admin()->female()->create();

        $this->assertTrue($maleUser->isMale());
        $this->assertFalse($femaleUser->isMale());
    }

    /** @test */
    public function it_identifies_female_users_correctly()
    {
        $maleUser = User::factory()->admin()->male()->create();
        $femaleUser = User::factory()->admin()->female()->create();

        $this->assertFalse($maleUser->isFemale());
        $this->assertTrue($femaleUser->isFemale());
    }

    // ========================================
    // TESTS DE RELACIONES
    // ========================================

    /** @test */
    public function it_creates_teacher_profile_when_using_teacher_state()
    {
        $user = User::factory()->teacher()->create();

        $this->assertNotNull($user->teacher);
        $this->assertEquals($user->id, $user->teacher->user_id);
        $this->assertTrue($user->hasRole(Role::Teacher->value));
        
        // Lógica correcta: user.is_active = false, teacher.is_active = true
        // Pero SÍ puede acceder porque CheckActiveUser permite: isActive() || teacher->isActive()
        $this->assertFalse($user->is_active);
        $this->assertTrue($user->teacher->is_active);
        $this->assertNotNull($user->password); // Puede loguearse
    }

    /** @test */
    public function it_creates_representative_profile_when_using_representative_state()
    {
        $user = User::factory()->representative()->create();

        $this->assertNotNull($user->representative);
        $this->assertEquals($user->id, $user->representative->user_id);
        $this->assertTrue($user->hasRole(Role::Representative->value));
        
        // Lógica correcta: user.is_active = false, representative.is_active = true
        $this->assertFalse($user->is_active); // NO accede al sistema
        $this->assertTrue($user->representative->is_active); // Activo como representante
    }

    /** @test */
    public function it_creates_student_profile_when_using_student_state()
    {
        $user = User::factory()->student()->create();

        $this->assertNotNull($user->student);
        $this->assertEquals($user->id, $user->student->user_id);
        $this->assertTrue($user->hasRole(Role::Student->value));
        
        // Lógica correcta: user.is_active = false, student.is_active = true
        $this->assertFalse($user->is_active); // NO accede al sistema
        $this->assertTrue($user->student->is_active); // Activo como estudiante
        $this->assertNull($user->password); // NO puede loguearse
        
        // Tiene un representante externo
        $this->assertNotNull($user->student->representative);
        $this->assertNotEquals($user->id, $user->student->representative->user_id);
    }

    /** @test */
    public function it_creates_self_represented_student_correctly()
    {
        $user = User::factory()->student(selfRepresented: true)->create();

        // El user tiene ambos perfiles
        $this->assertNotNull($user->student);
        $this->assertNotNull($user->representative);
        
        // Ambos perfiles usan el mismo user
        $this->assertEquals($user->id, $user->student->user_id);
        $this->assertEquals($user->id, $user->representative->user_id);
        
        // El representante del estudiante es el mismo user
        $this->assertEquals($user->representative->id, $user->student->representative_id);
        
        // Tipo de relación correcto
        $this->assertEquals('Auto-representante', $user->student->relationship_type);
    }

    /** @test */
    public function it_identifies_if_user_has_entity_profile()
    {
        $teacher = User::factory()->teacher()->create();
        $representative = User::factory()->representative()->create();
        $student = User::factory()->student()->create();
        $admin = User::factory()->admin()->create();

        $this->assertTrue($teacher->hasEntity());
        $this->assertTrue($representative->hasEntity());
        $this->assertTrue($student->hasEntity());
        $this->assertFalse($admin->hasEntity());
    }

    // ========================================
    // TESTS DE LÓGICA DE NEGOCIO ESPECÍFICA
    // ========================================

    /** @test */
    public function user_module_creates_user_with_correct_defaults()
    {
        // Módulo User: is_active = true, tiene rol, NO pide birth_date
        $user = User::factory()->admin()->create();

        $this->assertTrue($user->is_active);
        $this->assertTrue($user->hasRole(Role::Admin->value));
        $this->assertNotNull($user->password);
        $this->assertNull($user->birth_date);
    }

    /** @test */
    public function teacher_module_creates_user_with_correct_defaults()
    {
        // Módulo Teacher: user.is_active = false, teacher.is_active = true
        // Puede acceder al sistema (tiene password y teacher.is_active = true)
        // NO pide birth_date
        $user = User::factory()->teacher()->create();

        $this->assertFalse($user->is_active);
        $this->assertTrue($user->teacher->is_active);
        $this->assertNotNull($user->password); // SÍ puede loguearse
        $this->assertNull($user->birth_date);
    }

    /** @test */
    public function representative_module_creates_user_with_all_required_fields()
    {
        // Módulo Representative: TODOS los campos required
        // user.is_active = false, representative.is_active = true
        $user = User::factory()->representative()->create();

        $this->assertNotNull($user->document_id);
        $this->assertNotNull($user->birth_date);
        $this->assertNotNull($user->phone);
        $this->assertNotNull($user->address);
        $this->assertNotNull($user->password);
        $this->assertFalse($user->is_active); // NO accede al sistema
        $this->assertTrue($user->representative->is_active);
    }

    /** @test */
    public function student_module_creates_user_with_birth_date_required()
    {
        // Módulo Student: birth_date required, document_id/email nullable
        // user.is_active = false, student.is_active = true
        $user = User::factory()->student()->create();

        $this->assertNotNull($user->birth_date); // Required
        $this->assertFalse($user->is_active); // NO accede al sistema
        $this->assertTrue($user->student->is_active);
        $this->assertNotNull($user->student->student_code);
        $this->assertNull($user->password); // NO puede loguearse
    }

    /** @test */
    public function it_generates_valid_spanish_documents()
    {
        $representative = User::factory()->representative()->create();

        $this->assertMatchesRegularExpression(
            '/^[A-Z]?[0-9]{7,9}[A-Z]?$/',
            $representative->document_id,
            'El document_id no tiene formato DNI/NIE válido'
        );
    }

    // ========================================
    // TEST DE LÓGICA DE ACCESO AL SISTEMA
    // ========================================

    /** @test */
    public function it_correctly_implements_access_logic()
    {
        // Según CheckActiveUser middleware línea 20:
        // isAllowedToLogin = user.isActive() || teacher->isActive()
        
        // Admin: user.is_active = true → SÍ accede
        $admin = User::factory()->admin()->create();
        $this->assertTrue($admin->is_active);
        
        // Teacher: user.is_active = false, teacher.is_active = true → SÍ accede
        $teacher = User::factory()->teacher()->create();
        $this->assertFalse($teacher->is_active);
        $this->assertTrue($teacher->teacher->is_active);
        $this->assertNotNull($teacher->password);
        
        // Representative: user.is_active = false, representative.is_active = true → NO accede
        $representative = User::factory()->representative()->create();
        $this->assertFalse($representative->is_active);
        $this->assertTrue($representative->representative->is_active);
        
        // Student: user.is_active = false, student.is_active = true → NO accede
        $student = User::factory()->student()->create();
        $this->assertFalse($student->is_active);
        $this->assertTrue($student->student->is_active);
        $this->assertNull($student->password);
    }
}