<?php

namespace Tests\Feature\Api\Grades;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicGradesApiTest extends TestCase
{
    use RefreshDatabase;

    private const TOKEN = 'test-token';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        config(['services.public_api.token' => self::TOKEN]);
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

    private function credentials(\App\Domains\Identity\Models\User $user): string
    {
        return http_build_query([
            'document_id' => $user->document_id,
            'birth_date' => $user->birth_date->format('Y-m-d'),
        ]);
    }

    public function test_student_grades_returns_contract_shape(): void
    {
        $student = $this->gradedStudent();

        $response = $this->withToken(self::TOKEN)
            ->getJson('/api/public/student/grades?'.$this->credentials($student->user));

        $response->assertOk()
            ->assertJsonMissingPath('success')
            ->assertJsonStructure([
                'data' => [
                    'student' => ['code', 'name', 'situation', 'relationshipType', 'isActive'],
                    'enrollments' => [[
                        'academicPeriod',
                        'section',
                        'status',
                        'passed',
                        'subjects' => [[
                            'name',
                            'teacher',
                            'evaluations' => [['name', 'weight', 'grade', 'observation']],
                            'weightedAverage',
                            'isPassing',
                        ]],
                    ]],
                ],
            ])
            ->assertJsonPath('data.student.code', $student->student_code)
            ->assertJsonPath('data.student.relationshipType', $student->relationship_type)
            ->assertJsonPath('data.student.isActive', true)
            ->assertJsonPath('data.enrollments.0.subjects.0.evaluations.0.grade', 80);
    }

    public function test_representative_grades_returns_contract_shape(): void
    {
        $student = $this->gradedStudent();
        $representativeUser = $student->representative->user;

        $response = $this->withToken(self::TOKEN)
            ->getJson('/api/public/representative/grades?'.$this->credentials($representativeUser));

        $response->assertOk()
            ->assertJsonMissingPath('success')
            ->assertJsonStructure([
                'data' => [
                    'representative' => ['name'],
                    'students' => [[
                        'student' => ['code', 'name', 'situation', 'relationshipType', 'isActive'],
                        'enrollments',
                    ]],
                ],
            ])
            ->assertJsonCount(1, 'data.students')
            ->assertJsonPath('data.representative.name', $representativeUser->full_name)
            ->assertJsonPath('data.students.0.student.code', $student->student_code);
    }

    public function test_returns_not_found_for_unknown_credentials(): void
    {
        $response = $this->withToken(self::TOKEN)
            ->getJson('/api/public/student/grades?document_id=V99999999&birth_date=1990-01-01');

        $response->assertNotFound()
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_validation_error_uses_contract_envelope(): void
    {
        $response = $this->withToken(self::TOKEN)
            ->getJson('/api/public/student/grades');

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR')
            ->assertJsonStructure([
                'error' => ['code', 'message', 'fields' => ['document_id', 'birth_date']],
            ]);
    }

    public function test_requires_token(): void
    {
        $response = $this->getJson('/api/public/student/grades?document_id=V12345678&birth_date=2000-01-01');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }

    public function test_rejects_invalid_token(): void
    {
        $response = $this->withToken('wrong-token')
            ->getJson('/api/public/student/grades?document_id=V12345678&birth_date=2000-01-01');

        $response->assertStatus(401)
            ->assertJsonPath('error.code', 'UNAUTHENTICATED');
    }
}
