<?php

namespace App\Domains\Grades\Services\Grades;

use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Exceptions\EnrollmentNotGradableException;
use App\Domains\Grades\Exceptions\GradeOutOfRangeException;
use App\Domains\Grades\Exceptions\GradingConfigurationIncompleteException;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Repositories\GradeRepository;
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

            // Validar rango de nota
            $academicPeriod = $sst->section->academicPeriod;
            if (! $academicPeriod->isGradeValid($data['value'])) {
                throw GradeOutOfRangeException::make($academicPeriod->min_grade, $academicPeriod->max_grade);
            }

            $grade = $this->gradeRepository->create([
                'enrollment_id' => $enrollment->id,
                'grade_column_id' => $gradeColumn->id,
                'value' => $data['value'],
                'observation' => $data['observation'] ?? null,
                'last_modified_by' => Auth::id(),
            ]);

            return $grade->fresh(['enrollment.student.user', 'gradeColumn']);
        });
    }
}
