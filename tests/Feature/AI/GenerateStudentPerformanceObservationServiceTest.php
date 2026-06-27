<?php

namespace Tests\Feature\AI;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\AI\Contracts\AiTextGenerator;
use App\Domains\AI\Services\GenerateStudentPerformanceObservationService;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateStudentPerformanceObservationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_builds_the_prompt_from_the_student_summary_and_returns_the_generated_text(): void
    {
        $student = $this->gradedStudent();

        $this->mock(AiTextGenerator::class, function ($mock) use ($student): void {
            $mock->shouldReceive('generate')
                ->once()
                ->withArgs(function (string $prompt, ?string $system) use ($student): bool {
                    return str_contains($prompt, $student->user->full_name)
                        && str_contains($prompt, 'observación de desempeño')
                        && $system !== null;
                })
                ->andReturn('Observación generada.');
        });

        $observation = app(GenerateStudentPerformanceObservationService::class)->forStudent($student);

        $this->assertSame('Observación generada.', $observation);
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
