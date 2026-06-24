<?php

namespace App\Domains\Grades\Services\Grades;

use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
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
                throw new \Exception(
                    'La configuración de evaluaciones debe sumar 100% antes de calificar.'
                );
            }

            // Validar que el estudiante pertenece a esta sección
            if ($enrollment->section_id !== $sst->section_id) {
                throw new \Exception('El estudiante no pertenece a esta sección.');
            }

            // Validar inscripción activa
            if ($enrollment->status !== EnrollmentStatus::Active->value) {
                throw new \Exception('La inscripción del estudiante no está activa.');
            }

            // Validar rango de nota
            $academicPeriod = $sst->section->academicPeriod;
            if (! $academicPeriod->isGradeValid($data['value'])) {
                throw new \Exception(
                    "La nota debe estar entre {$academicPeriod->min_grade} y {$academicPeriod->max_grade}."
                );
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
