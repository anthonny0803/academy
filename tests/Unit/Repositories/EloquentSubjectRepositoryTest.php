<?php

namespace Tests\Unit\Repositories;

use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Academics\Repositories\EloquentSubjectRepository;
use App\Domains\Academics\Repositories\SubjectRepository;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentSubjectRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private SubjectRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->repository = app(SubjectRepository::class);
    }

    public function test_binding_resolves_to_eloquent_implementation(): void
    {
        $this->assertInstanceOf(EloquentSubjectRepository::class, $this->repository);
    }

    public function test_create_persists_a_subject(): void
    {
        $subject = $this->repository->create([
            'name' => 'Matematicas',
            'description' => 'Algebra y geometria',
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Subject::class, $subject);
        $this->assertDatabaseHas('subjects', ['id' => $subject->id, 'name' => 'MATEMATICAS']);
    }

    public function test_update_changes_attributes(): void
    {
        $subject = Subject::factory()->create();

        $this->repository->update($subject, ['name' => 'Quimica', 'description' => 'Organica']);

        $this->assertSame('QUIMICA', $subject->fresh()->name);
    }

    public function test_delete_removes_the_subject(): void
    {
        $subject = Subject::factory()->create();

        $this->repository->delete($subject);

        $this->assertDatabaseMissing('subjects', ['id' => $subject->id]);
    }

    public function test_paginate_for_listing_matches_by_search(): void
    {
        $match = Subject::factory()->create(['name' => 'Matematicas']);
        $other = Subject::factory()->create(['name' => 'Historia']);

        $result = $this->repository->paginateForListing('Matematicas', null, 10);

        $this->assertTrue($result->contains($match));
        $this->assertFalse($result->contains($other));
    }

    public function test_paginate_for_listing_filters_by_active_status(): void
    {
        $active = Subject::factory()->create();
        $inactive = Subject::factory()->inactive()->create();

        $activeResult = $this->repository->paginateForListing('', true, 10);
        $this->assertTrue($activeResult->contains($active));
        $this->assertFalse($activeResult->contains($inactive));

        $inactiveResult = $this->repository->paginateForListing('', false, 10);
        $this->assertTrue($inactiveResult->contains($inactive));
        $this->assertFalse($inactiveResult->contains($active));
    }

    public function test_paginate_with_active_teachers_excludes_inactive_subjects_and_loads_active_teachers(): void
    {
        $subject = Subject::factory()->create();
        $inactiveSubject = Subject::factory()->inactive()->create();

        $activeTeacher = Teacher::factory()->create();
        $inactiveTeacher = Teacher::factory()->inactive()->create();
        $subject->teachers()->attach([$activeTeacher->id, $inactiveTeacher->id]);

        $result = $this->repository->paginateWithActiveTeachers('', null, 10);

        $this->assertTrue($result->contains($subject));
        $this->assertFalse($result->contains($inactiveSubject));

        $loaded = $result->firstWhere('id', $subject->id);
        $this->assertTrue($loaded->teachers->contains($activeTeacher));
        $this->assertFalse($loaded->teachers->contains($inactiveTeacher));
    }

    public function test_active_ordered_excludes_inactive_and_sorts_by_name(): void
    {
        $beta = Subject::factory()->create(['name' => 'Biologia']);
        $alpha = Subject::factory()->create(['name' => 'Arte']);
        $inactive = Subject::factory()->inactive()->create();

        $result = $this->repository->activeOrdered();

        $this->assertFalse($result->contains($inactive));
        $this->assertTrue($result->first()->is($alpha));
        $this->assertTrue($result->contains($beta));
    }
}
