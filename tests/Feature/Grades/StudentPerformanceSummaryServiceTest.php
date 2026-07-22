<?php

namespace Tests\Feature\Grades;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Services\Grades\StudentPerformanceSummaryService;
use App\Domains\Students\Models\Student;
use Closure;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentPerformanceSummaryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_query_count_does_not_grow_with_the_number_of_subjects(): void
    {
        $service = app(StudentPerformanceSummaryService::class);

        $fewSubjects = $this->loadedStudentWithSubjects(1);
        $manySubjects = $this->loadedStudentWithSubjects(4);

        $this->assertSame(
            $this->countQueries(fn () => $service->forStudent($fewSubjects)),
            $this->countQueries(fn () => $service->forStudent($manySubjects))
        );
    }

    public function test_weighted_average_uses_every_column(): void
    {
        $academicPeriod = AcademicPeriod::factory()->create(['passing_grade' => 5]);
        $section = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);

        $sst = SectionSubjectTeacher::factory()->create(['section_id' => $section->id]);
        $lightColumn = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 40,
        ]);
        $heavyColumn = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 60,
        ]);

        $student = Student::factory()->inSection($section)->create();
        $enrollment = $student->enrollments()->where('section_id', $section->id)->first();

        Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $lightColumn->id,
            'value' => 4,
        ]);
        Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $heavyColumn->id,
            'value' => 9,
        ]);

        $summary = app(StudentPerformanceSummaryService::class)
            ->forStudent($this->reloadWithSummaryGraph($student));

        $subject = $summary['enrollments'][0]['subjects'][0];

        // (4*40 + 9*60) / 100 = 7.0: passes despite failing the lighter column
        $this->assertSame(7.0, $subject['weighted_average']);
        $this->assertTrue($subject['is_passing']);
    }

    private function loadedStudentWithSubjects(int $subjects): Student
    {
        $academicPeriod = AcademicPeriod::factory()->create(['passing_grade' => 60]);
        $section = Section::factory()->create(['academic_period_id' => $academicPeriod->id]);

        $student = Student::factory()->inSection($section)->create();
        $enrollment = $student->enrollments()->where('section_id', $section->id)->first();

        for ($subject = 0; $subject < $subjects; $subject++) {
            $sst = SectionSubjectTeacher::factory()->create(['section_id' => $section->id]);
            $column = GradeColumn::factory()->create([
                'section_subject_teacher_id' => $sst->id,
                'weight' => 100,
            ]);
            Grade::factory()->create([
                'enrollment_id' => $enrollment->id,
                'grade_column_id' => $column->id,
                'value' => 80,
            ]);
        }

        return $this->reloadWithSummaryGraph($student);
    }

    private function reloadWithSummaryGraph(Student $student): Student
    {
        return Student::query()
            ->with([
                'user',
                'enrollments.section.academicPeriod',
                'enrollments.section.sectionSubjectTeachers' => fn ($query) => $query->with([
                    'subject',
                    'teacher.user',
                    'gradeColumns',
                ]),
                'enrollments.grades.gradeColumn',
            ])
            ->findOrFail($student->id);
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
