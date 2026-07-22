<?php

namespace Tests\Feature\Grades;

use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Services\Grades\BatchGradeService;
use App\Domains\Students\Models\Student;
use Closure;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BatchGradeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_query_count_does_not_grow_with_the_number_of_rows(): void
    {
        $service = app(BatchGradeService::class);

        // All rows match an existing grade, so nothing is written: the query
        // count is dominated by the read that used to be N per row.
        [$twoColumn, $twoRows] = $this->skippableBatch(2);
        [$sixColumn, $sixRows] = $this->skippableBatch(6);

        $this->assertSame(
            $this->countQueries(fn () => $service->handle($twoColumn, $twoRows)),
            $this->countQueries(fn () => $service->handle($sixColumn, $sixRows))
        );
    }

    public function test_duplicate_enrollment_in_batch_is_upserted(): void
    {
        $service = app(BatchGradeService::class);
        [$column, $section] = $this->columnWithSection();
        $enrollment = $this->activeEnrollmentIn($section);

        $summary = $service->handle($column, [
            ['enrollment_id' => $enrollment->id, 'value' => 5],
            ['enrollment_id' => $enrollment->id, 'value' => 9],
        ]);

        // First row creates, the duplicate updates the in-memory snapshot.
        $this->assertSame(1, $summary['created']);
        $this->assertSame(1, $summary['updated']);
        $this->assertSame(1, Grade::where('enrollment_id', $enrollment->id)
            ->where('grade_column_id', $column->id)
            ->count());
        $this->assertEquals(9.0, (float) Grade::where('enrollment_id', $enrollment->id)
            ->where('grade_column_id', $column->id)
            ->value('value'));
    }

    public function test_counts_created_updated_and_skipped(): void
    {
        $service = app(BatchGradeService::class);
        [$column, $section] = $this->columnWithSection();
        $toCreate = $this->activeEnrollmentIn($section);
        $toUpdate = $this->activeEnrollmentIn($section);
        $noValue = $this->activeEnrollmentIn($section);

        Grade::factory()->create([
            'enrollment_id' => $toUpdate->id,
            'grade_column_id' => $column->id,
            'value' => 4,
        ]);

        $summary = $service->handle($column, [
            ['enrollment_id' => $toCreate->id, 'value' => 8],
            ['enrollment_id' => $toUpdate->id, 'value' => 9],
            ['enrollment_id' => $noValue->id, 'value' => null],
        ]);

        $this->assertSame(1, $summary['created']);
        $this->assertSame(1, $summary['updated']);
        $this->assertSame(1, $summary['skipped']);
        $this->assertSame(3, $summary['total']);
    }

    /**
     * @return array{0: GradeColumn, 1: array<int, array{enrollment_id: string, value: int}>}
     */
    private function skippableBatch(int $rows): array
    {
        [$column, $section] = $this->columnWithSection();

        $grades = [];
        for ($row = 0; $row < $rows; $row++) {
            $enrollment = $this->activeEnrollmentIn($section);
            Grade::factory()->create([
                'enrollment_id' => $enrollment->id,
                'grade_column_id' => $column->id,
                'value' => 7,
                'observation' => null,
            ]);
            $grades[] = ['enrollment_id' => $enrollment->id, 'value' => 7];
        }

        return [$column, $grades];
    }

    /**
     * @return array{0: GradeColumn, 1: Section}
     */
    private function columnWithSection(): array
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $column = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 100,
        ]);

        return [$column, $sst->section];
    }

    private function activeEnrollmentIn(Section $section): Enrollment
    {
        return Student::factory()->inSection($section)->create()
            ->enrollments()
            ->where('section_id', $section->id)
            ->first();
    }

    private function countQueries(Closure $callback): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $callback();

        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $queries;
    }
}
