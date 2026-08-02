<?php

namespace Tests\Feature\Enrollments;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Enrollments\Exceptions\EnrollmentHasGradesException;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Enrollments\Services\DeleteEnrollmentService;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Services\Grades\DeleteGradeService;
use App\Domains\Identity\Models\User;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * `grades.enrollment_id` cascades, so deleting an enrollment destroys its
 * grades physically — trashed ones included, and without any error. These
 * tests fix that academic history stops the deletion, and that the rule lives
 * in the service and not only in the policy.
 */
class EnrollmentDeleteHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    /**
     * @return array{0: Enrollment, 1: Grade}
     */
    private function gradedEnrollment(): array
    {
        $sst = SectionSubjectTeacher::factory()->create();
        $column = GradeColumn::factory()->create([
            'section_subject_teacher_id' => $sst->id,
            'weight' => 100,
        ]);
        $student = Student::factory()->inSection($sst->section)->create();
        $enrollment = $student->enrollments()->where('section_id', $sst->section_id)->firstOrFail();

        $grade = Grade::factory()->create([
            'enrollment_id' => $enrollment->id,
            'grade_column_id' => $column->id,
        ]);

        return [$enrollment, $grade];
    }

    private function assertDeletingTheEnrollmentIsRejected(Enrollment $enrollment): void
    {
        try {
            app(DeleteEnrollmentService::class)->handle($enrollment);

            $this->fail('Expected EnrollmentHasGradesException was not thrown.');
        } catch (EnrollmentHasGradesException $e) {
            $this->assertSame(409, $e->statusCode());
            $this->assertSame('ENROLLMENT_HAS_GRADES', $e->errorCode());
            $this->assertSame(
                'No se puede eliminar esta inscripción porque tiene calificaciones en su historial, incluidas las eliminadas.',
                $e->getMessage()
            );
        }

        $this->assertDatabaseHas('enrollments', ['id' => $enrollment->id]);
    }

    public function test_the_delete_service_rejects_an_enrollment_with_grades(): void
    {
        [$enrollment] = $this->gradedEnrollment();

        $this->assertDeletingTheEnrollmentIsRejected($enrollment);
        $this->assertSame(1, Grade::count());
    }

    public function test_the_delete_service_rejects_an_enrollment_whose_only_grade_is_deleted(): void
    {
        [$enrollment, $grade] = $this->gradedEnrollment();
        app(DeleteGradeService::class)->handle($grade);

        $this->assertDeletingTheEnrollmentIsRejected($enrollment);
        $this->assertNotNull(Grade::withTrashed()->find($grade->id));
    }

    public function test_the_delete_endpoint_denies_an_enrollment_whose_only_grade_is_deleted(): void
    {
        [$enrollment, $grade] = $this->gradedEnrollment();
        app(DeleteGradeService::class)->handle($grade);
        $supervisor = User::factory()->supervisor()->create();

        $response = $this->actingAs($supervisor)
            ->delete(route('enrollments.destroy', $enrollment));

        $response->assertRedirect();
        $response->assertSessionHas(
            'error',
            'No puedes eliminar una inscripción con calificaciones en su historial, incluidas las eliminadas.'
        );
        $this->assertDatabaseHas('enrollments', ['id' => $enrollment->id]);
        $this->assertNotNull(Grade::withTrashed()->find($grade->id));
    }

    public function test_the_database_refuses_to_delete_an_enrollment_that_owns_deleted_grades(): void
    {
        [$enrollment, $grade] = $this->gradedEnrollment();
        app(DeleteGradeService::class)->handle($grade);

        // The service guard defends the callers; this is the backstop for a
        // write that races past it, which the cascade used to swallow. Nothing
        // can be asserted afterwards: the violation aborts the transaction
        // RefreshDatabase runs the test in.
        $this->expectException(QueryException::class);
        $this->expectExceptionMessageMatches('/grades_enrollment_id_foreign/');

        $enrollment->delete();
    }

    public function test_the_delete_endpoint_removes_an_enrollment_without_grades(): void
    {
        $student = Student::factory()->create();
        $enrollment = $student->enrollments()->firstOrFail();
        $supervisor = User::factory()->supervisor()->create();

        $response = $this->actingAs($supervisor)
            ->delete(route('enrollments.destroy', $enrollment));

        $response->assertSessionHas('success', '¡Inscripción eliminada correctamente!');
        $this->assertDatabaseMissing('enrollments', ['id' => $enrollment->id]);
    }
}
