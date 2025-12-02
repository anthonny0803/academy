<?php

namespace App\Services\Grades;

use App\Models\Grade;
use App\Models\GradeColumn;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreGradeService
{
    public function handle(GradeColumn $gradeColumn, Enrollment $enrollment, array $data): Grade
    {
        return DB::transaction(function () use ($gradeColumn, $enrollment, $data) {
            $sst = $gradeColumn->sectionSubjectTeacher;

            // Validar que la configuración esté completa
            if (!$sst->isConfigurationComplete()) {
                throw new \Exception(
                    'La configuración de evaluaciones debe sumar 100% antes de calificar.'
                );
            }

            // Validar que el estudiante pertenece a esta sección
            if ($enrollment->section_id !== $sst->section_id) {
                throw new \Exception('El estudiante no pertenece a esta sección.');
            }

            // Validar inscripción activa
            if ($enrollment->status !== 'activo') {
                throw new \Exception('La inscripción del estudiante no está activa.');
            }

            // Validar rango de nota
            $academicPeriod = $sst->section->academicPeriod;
            if (!$academicPeriod->isGradeValid($data['value'])) {
                throw new \Exception(
                    "La nota debe estar entre {$academicPeriod->min_grade} y {$academicPeriod->max_grade}."
                );
            }

            $grade = Grade::create([
                'enrollment_id' => $enrollment->id,
                'grade_column_id' => $gradeColumn->id,
                'value' => $data['value'],
                'observation' => $data['observation'] ?? null,
                'last_modified_by' => Auth::id(),
            ]);

            return $grade->fresh(['enrollment.student.user', 'gradeColumn']);
        });
    }

    /**
     * Registro masivo de notas (para agilidad: número y enter)
     */
    public function handleBatch(GradeColumn $gradeColumn, array $grades): array
    {
        return DB::transaction(function () use ($gradeColumn, $grades) {
            $sst = $gradeColumn->sectionSubjectTeacher;

            // Validar configuración completa
            if (!$sst->isConfigurationComplete()) {
                throw new \Exception(
                    'La configuración de evaluaciones debe sumar 100% antes de calificar.'
                );
            }

            $academicPeriod = $sst->section->academicPeriod;
            $results = ['created' => 0, 'updated' => 0, 'errors' => []];

            foreach ($grades as $gradeData) {
                try {
                    // Validar rango
                    if (!$academicPeriod->isGradeValid($gradeData['value'])) {
                        $results['errors'][] = "Enrollment {$gradeData['enrollment_id']}: Nota fuera de rango.";
                        continue;
                    }

                    // Crear o actualizar (upsert)
                    $grade = Grade::updateOrCreate(
                        [
                            'enrollment_id' => $gradeData['enrollment_id'],
                            'grade_column_id' => $gradeColumn->id,
                        ],
                        [
                            'value' => $gradeData['value'],
                            'observation' => $gradeData['observation'] ?? null,
                            'last_modified_by' => Auth::id(),
                        ]
                    );

                    $grade->wasRecentlyCreated 
                        ? $results['created']++ 
                        : $results['updated']++;

                } catch (\Exception $e) {
                    $results['errors'][] = "Enrollment {$gradeData['enrollment_id']}: {$e->getMessage()}";
                }
            }

            return $results;
        });
    }
}