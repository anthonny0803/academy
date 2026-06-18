<?php

namespace App\Domains\Grades\Services\Grades;

use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Repositories\GradeRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BatchGradeService
{
    public function __construct(
        private GradeRepository $gradeRepository
    ) {}

    /**
     * Procesa un batch de notas (crear o actualizar)
     *
     * @param  array  $gradesData  Array de ['enrollment_id' => x, 'value' => y, 'observation' => z]
     * @return array Resumen de operaciones
     */
    public function handle(GradeColumn $gradeColumn, array $gradesData): array
    {
        return DB::transaction(function () use ($gradeColumn, $gradesData) {
            $created = 0;
            $updated = 0;
            $skipped = 0;
            $userId = Auth::id();

            foreach ($gradesData as $gradeData) {
                $enrollmentId = $gradeData['enrollment_id'];
                $value = $gradeData['value'] ?? null;
                $observation = $gradeData['observation'] ?? null;

                // Si no hay valor, saltar (permite dejar campos vacíos)
                if ($value === null || $value === '') {
                    $skipped++;

                    continue;
                }

                // Buscar nota existente
                $existingGrade = $this->gradeRepository->findByEnrollmentAndColumn($enrollmentId, $gradeColumn->id);

                if ($existingGrade) {
                    // Actualizar solo si cambió el valor
                    if ((float) $existingGrade->value !== (float) $value
                        || $existingGrade->observation !== $observation
                    ) {
                        $this->gradeRepository->update($existingGrade, [
                            'value' => $value,
                            'observation' => $observation,
                            'last_modified_by' => $userId,
                        ]);
                        $updated++;
                    } else {
                        $skipped++;
                    }
                } else {
                    // Crear nueva nota
                    $this->gradeRepository->create([
                        'enrollment_id' => $enrollmentId,
                        'grade_column_id' => $gradeColumn->id,
                        'value' => $value,
                        'observation' => $observation,
                        'last_modified_by' => $userId,
                    ]);
                    $created++;
                }
            }

            return [
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
                'total' => count($gradesData),
            ];
        });
    }
}
