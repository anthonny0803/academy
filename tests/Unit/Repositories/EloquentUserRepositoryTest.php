<?php

namespace Tests\Unit\Repositories;

use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Models\User;
use App\Domains\Identity\Repositories\EloquentUserRepository;
use App\Domains\Identity\Repositories\UserRepository;
use App\Domains\Shared\Enums\Sex;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentUserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->repository = app(UserRepository::class);
    }

    public function test_binding_resolves_to_eloquent_implementation(): void
    {
        $this->assertInstanceOf(EloquentUserRepository::class, $this->repository);
    }

    public function test_create_persists_a_user(): void
    {
        $user = $this->repository->create([
            'name' => 'Carlos',
            'last_name' => 'Garcia',
            'email' => 'carlos@ejemplo.com',
            'sex' => Sex::Male->value,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['email' => 'carlos@ejemplo.com']);
    }

    public function test_update_changes_attributes(): void
    {
        $user = User::factory()->create(['email' => 'old@ejemplo.com']);

        $this->repository->update($user, ['email' => 'new@ejemplo.com']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'new@ejemplo.com',
        ]);
    }

    public function test_delete_removes_the_user(): void
    {
        $user = User::factory()->create();

        $this->repository->delete($user);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_find_by_credentials_matches_document_and_birth_date(): void
    {
        $user = User::factory()->create([
            'document_id' => '12345678A',
            'birth_date' => '2000-05-15',
        ]);

        $found = $this->repository->findByCredentials('12345678A', '2000-05-15');

        $this->assertNotNull($found);
        $this->assertTrue($found->is($user));
    }

    public function test_find_by_credentials_returns_null_when_no_match(): void
    {
        User::factory()->create([
            'document_id' => '12345678A',
            'birth_date' => '2000-05-15',
        ]);

        $this->assertNull($this->repository->findByCredentials('99999999Z', '2000-05-15'));
    }

    public function test_paginate_employees_excludes_non_employee_roles(): void
    {
        $supervisor = User::factory()->supervisor()->create(['name' => 'Carlos']);

        $representative = User::factory()->create(['name' => 'Carlos']);
        $representative->assignRole(Role::Representative->value);

        $result = $this->repository->paginateEmployees('Carlos', null, null, 10);

        $this->assertTrue($result->contains($supervisor));
        $this->assertFalse($result->contains($representative));
    }

    public function test_paginate_employees_respects_active_filter(): void
    {
        $active = User::factory()->supervisor()->create(['name' => 'Carlos']);
        $inactive = User::factory()->supervisor()->inactive()->create(['name' => 'Carlos']);

        $result = $this->repository->paginateEmployees('Carlos', true, null, 10);

        $this->assertTrue($result->contains($active));
        $this->assertFalse($result->contains($inactive));
    }

    public function test_paginate_employees_filters_by_role(): void
    {
        $supervisor = User::factory()->supervisor()->create(['name' => 'Carlos']);

        $teacher = User::factory()->create(['name' => 'Carlos']);
        $teacher->assignRole(Role::Teacher->value);

        $result = $this->repository->paginateEmployees('Carlos', null, Role::Teacher->value, 10);

        $this->assertTrue($result->contains($teacher));
        $this->assertFalse($result->contains($supervisor));
    }

    public function test_paginate_for_role_assignment_includes_non_employees(): void
    {
        $representative = User::factory()->create(['name' => 'Carlos']);
        $representative->assignRole(Role::Representative->value);

        $result = $this->repository->paginateForRoleAssignment('Carlos', 10);

        $this->assertTrue($result->contains($representative));
    }
}
