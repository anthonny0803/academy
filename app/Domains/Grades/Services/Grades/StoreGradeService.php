<?php

namespace App\Domains\Grades\Services\Grades;

use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Exceptions\EnrollmentNotGradableException;
use App\Domains\Grades\Exceptions\GradeAlreadyExistsException;
use App\Domains\Grades\Exceptions\GradeOutOfRangeException;
use App\Domains\Grades\Exceptions\GradeValueNotNumericException;
use App\Domains\Grades\Exceptions\GradingConfigurationIncompleteException;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Repositories\GradeRepository;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreGradeService
{
    public function __construct(
        private GradeRepository $gradeRepository
    ) {}

    public function handle(GradeColumn $gradeColumn, Enrollment $enrollment, array $data): Grade
    {
        return DB::transaction(function () use ($gradeColumn, $enrollment, $data) {
            $sst = $gradeColumn->sectionSubjectTeacher;

            // Validar que la configuración esté completa
            if (! $sst->isConfigurationComplete()) {
                throw GradingConfigurationIncompleteException::make();
            }

            // Validar que el estudiante pertenece a esta sección
            if ($enrollment->section_id !== $sst->section_id) {
                throw EnrollmentNotGradableException::outsideSection();
            }

            // Validar inscripción activa
            if ($enrollment->status !== EnrollmentStatus::Active->value) {
                throw EnrollmentNotGradableException::inactive();
            }

            if (! is_numeric($data['value'] ?? null)) {
                throw GradeValueNotNumericException::make();
            }

            // Validar rango de nota
            $academicPeriod = $sst->section->academicPeriod;
            if (! $academicPeriod->isGradeValid((float) $data['value'])) {
                throw GradeOutOfRangeException::make($academicPeriod->min_grade, $academicPeriod->max_grade);
            }

            // The partial unique index is the real serialization point for the
            // cell: the form request check can be overtaken by a concurrent write.
            try {
                $grade = $this->gradeRepository->create([
                    'enrollment_id' => $enrollment->id,
                    'grade_column_id' => $gradeColumn->id,
                    'value' => $data['value'],
                    'observation' => $data['observation'] ?? null,
                    'last_modified_by' => Auth::id(),
                ]);
            } catch (UniqueConstraintViolationException) {
                throw GradeAlreadyExistsException::make();
            }

            return $grade->fresh(['enrollment.student.user', 'gradeColumn']);
        });
    }
}
