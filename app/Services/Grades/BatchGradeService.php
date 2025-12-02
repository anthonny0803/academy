<?php

namespace App\Services\Grades;

use App\Models\Grade;
use App\Models\GradeColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BatchGradeService
{
    /**
     * Procesa un batch de notas (crear o actualizar)
     * 
     * @param GradeColumn $gradeColumn
     * @param array $gradesData Array de ['enrollment_id' => x, 'value' => y, 'observation' => z]
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
                $existingGrade = Grade::where('enrollment_id', $enrollmentId)
                    ->where('grade_column_id', $gradeColumn->id)
                    ->first();

                if ($existingGrade) {
                    // Actualizar solo si cambió el valor
                    if ((float) $existingGrade->value !== (float) $value 
                        || $existingGrade->observation !== $observation
                    ) {
                        $existingGrade->update([
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
                    Grade::create([
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