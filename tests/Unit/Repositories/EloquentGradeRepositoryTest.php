<?php

namespace Tests\Unit\Repositories;

use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Repositories\EloquentGradeRepository;
use App\Domains\Grades\Repositories\GradeRepository;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentGradeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private GradeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->repository = app(GradeRepository::class);
    }

    public function test_binding_resolves_to_eloquent_implementation(): void
    {
        $this->assertInstanceOf(EloquentGradeRepository::class, $this->repository);
    }

    public function test_create_persists_a_grade(): void
    {
        $enrollment = Enrollment::factory()->create();
        $column = GradeColumn::factory()->create();

        $grade = $this->repository->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
            'value' => 8.5,
        ]);

        $this->assertInstanceOf(Grade::class, $grade);
        $this->assertDatabaseHas('grades', ['id' => $grade->id]);
    }

    public function test_update_changes_value(): void
    {
        $grade = Grade::factory()->create();

        $this->repository->update($grade, ['value' => 7.25]);

        $this->assertEquals(7.25, (float) $grade->fresh()->value);
    }

    public function test_delete_soft_deletes(): void
    {
        $grade = Grade::factory()->create();

        $this->repository->delete($grade);

        $this->assertSoftDeleted('grades', ['id' => $grade->id]);
    }

    public function test_restore_restores_soft_deleted(): void
    {
        $grade = Grade::factory()->create();
        $grade->delete();

        $this->repository->restore($grade);

        $this->assertNotSoftDeleted('grades', ['id' => $grade->id]);
    }

    public function test_find_with_trashed_finds_soft_deleted(): void
    {
        $grade = Grade::factory()->create();
        $grade->delete();

        $found = $this->repository->findWithTrashed($grade->id);

        $this->assertTrue($found->is($grade));
    }

    public function test_for_grade_columns_returns_grades_for_columns(): void
    {
        $grade = Grade::factory()->create();

        $result = $this->repository->forGradeColumns([$grade->grade_column_id]);

        $this->assertTrue($result->contains($grade));
    }
}
