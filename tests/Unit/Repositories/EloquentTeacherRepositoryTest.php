<?php

namespace Tests\Unit\Repositories;

use App\Domains\Academics\Models\Teacher;
use App\Domains\Academics\Repositories\EloquentTeacherRepository;
use App\Domains\Academics\Repositories\TeacherRepository;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentTeacherRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TeacherRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->repository = app(TeacherRepository::class);
    }

    public function test_binding_resolves_to_eloquent_implementation(): void
    {
        $this->assertInstanceOf(EloquentTeacherRepository::class, $this->repository);
    }

    public function test_create_persists_a_teacher(): void
    {
        $user = User::factory()->create();

        $teacher = $this->repository->create([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Teacher::class, $teacher);
        $this->assertDatabaseHas('teachers', ['id' => $teacher->id, 'user_id' => $user->id]);
    }

    public function test_paginate_for_listing_matches_by_user_name(): void
    {
        $match = Teacher::factory()->create([
            'user_id' => User::factory()->state(['name' => 'Findable', 'last_name' => 'Teacher']),
        ]);
        $other = Teacher::factory()->create([
            'user_id' => User::factory()->state(['name' => 'Hidden', 'last_name' => 'Person']),
        ]);

        $result = $this->repository->paginateForListing('Findable', null, 10);

        $this->assertTrue($result->contains($match));
        $this->assertFalse($result->contains($other));
    }

    public function test_paginate_for_listing_filters_by_active_status(): void
    {
        $active = Teacher::factory()->create([
            'user_id' => User::factory()->state(['name' => 'Active', 'last_name' => 'Teacher']),
        ]);
        $inactive = Teacher::factory()->inactive()->create([
            'user_id' => User::factory()->state(['name' => 'Inactive', 'last_name' => 'Teacher']),
        ]);

        $activeResult = $this->repository->paginateForListing('Teacher', true, 10);
        $this->assertTrue($activeResult->contains($active));
        $this->assertFalse($activeResult->contains($inactive));

        $inactiveResult = $this->repository->paginateForListing('Teacher', false, 10);
        $this->assertTrue($inactiveResult->contains($inactive));
        $this->assertFalse($inactiveResult->contains($active));
    }
}
