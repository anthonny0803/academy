<?php

namespace Tests\Feature\AI;

use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\AI\Contracts\AiTextGenerator;
use App\Domains\AI\Enums\ObservationStatus;
use App\Domains\AI\Exceptions\AiGenerationException;
use App\Domains\AI\Jobs\GenerateStudentPerformanceObservationJob;
use App\Domains\AI\Models\StudentPerformanceObservation;
use App\Domains\AI\Services\GenerateStudentPerformanceObservationService;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Students\Models\Student;
use Closure;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GenerateStudentPerformanceObservationJobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_completes_the_observation_with_the_generated_text(): void
    {
        $observation = StudentPerformanceObservation::factory()->create([
            'student_id' => $this->gradedStudent()->id,
        ]);

        $this->mock(AiTextGenerator::class, function ($mock): void {
            $mock->shouldReceive('generate')
                ->once()
                ->andReturn('Observación generada.');
        });

        (new GenerateStudentPerformanceObservationJob($observation))
            ->handle(app(GenerateStudentPerformanceObservationService::class));

        $observation->refresh();

        $this->assertSame(ObservationStatus::Completed, $observation->status);
        $this->assertSame('Observación generada.', $observation->content);
        $this->assertNotNull($observation->generated_at);
    }

    public function test_marks_the_observation_as_failed_when_generation_fails(): void
    {
        $observation = StudentPerformanceObservation::factory()->create([
            'student_id' => $this->gradedStudent()->id,
        ]);

        (new GenerateStudentPerformanceObservationJob($observation))
            ->failed(new AiGenerationException('The AI provider request failed with status 401.'));

        $observation->refresh();

        $this->assertSame(ObservationStatus::Failed, $observation->status);
        $this->assertSame(
            'No se pudo generar la observación. Inténtalo de nuevo más tarde.',
            $observation->failure_reason
        );
        $this->assertNull($observation->content);
    }

    public function test_query_count_does_not_grow_with_the_size_of_the_record(): void
    {
        $this->mock(AiTextGenerator::class, function ($mock): void {
            $mock->shouldReceive('generate')
                ->twice()
                ->andReturn('Observación generada.');
        });

        $shortRecord = $this->observationFor($this->gradedStudent());
        $longRecord = $this->observationFor($this->gradedStudent(sections: 3, subjectsPerSection: 4));

        $service = app(GenerateStudentPerformanceObservationService::class);

        $this->assertSame(
            $this->countQueries(fn () => (new GenerateStudentPerformanceObservationJob($shortRecord))->handle($service)),
            $this->countQueries(fn () => (new GenerateStudentPerformanceObservationJob($longRecord))->handle($service)),
        );
    }

    private function observationFor(Student $student): StudentPerformanceObservation
    {
        return StudentPerformanceObservation::factory()->create(['student_id' => $student->id]);
    }

    /**
     * Build a student enrolled in $sections sections, each with
     * $subjectsPerSection graded subjects (single 100% column, value 80).
     */
    private function gradedStudent(int $sections = 1, int $subjectsPerSection = 1): Student
    {
        $student = Student::factory()->create();
        $student->enrollments()->delete();

        for ($section = 0; $section < $sections; $section++) {
            $this->enrollInGradedSection($student, $subjectsPerSection);
        }

        return $student;
    }

    private function enrollInGradedSection(Student $student, int $subjects): void
    {
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'section_id' => Section::factory()->create()->id,
            'status' => EnrollmentStatus::Active->value,
        ]);

        for ($subject = 0; $subject < $subjects; $subject++) {
            $sst = SectionSubjectTeacher::factory()->create(['section_id' => $enrollment->section_id]);
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
