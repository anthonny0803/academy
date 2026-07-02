<?php

namespace Tests\Feature\AI;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\AI\Contracts\AiTextGenerator;
use App\Domains\AI\Enums\ObservationStatus;
use App\Domains\AI\Exceptions\AiGenerationException;
use App\Domains\AI\Jobs\GenerateStudentPerformanceObservationJob;
use App\Domains\AI\Models\StudentPerformanceObservation;
use App\Domains\AI\Services\GenerateStudentPerformanceObservationService;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    /**
     * Build a student with one graded subject (single 100% column, value 80)
     * enrolled in the section of an active assignment.
     */
    private function gradedStudent(): Student
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $column = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 100,
        ]);
        $student = Student::factory()->inSection($sst->section)->create();
        $enrollment = $student->enrollments()->where('section_id', $sst->section_id)->first();
        Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
            'value' => 80,
        ]);

        return $student;
    }
}
