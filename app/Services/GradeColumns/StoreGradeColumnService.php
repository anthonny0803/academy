<?php

namespace App\Services\GradeColumns;

use App\Models\GradeColumn;
use App\Models\SectionSubjectTeacher;
use Illuminate\Support\Facades\DB;

class StoreGradeColumnService
{
    public function handle(SectionSubjectTeacher $sst, array $data): GradeColumn
    {
        return DB::transaction(function () use ($sst, $data) {
            // Validar que no exceda el 100%
            if (!$sst->canAddColumn($data['weight'])) {
                $remaining = $sst->getRemainingWeight();
                throw new \Exception(
                    "No se puede agregar esta evaluación. Peso restante disponible: {$remaining}%"
                );
            }

            // Calcular display_order si no viene
            $displayOrder = $data['display_order'] 
                ?? ($sst->gradeColumns()->max('display_order') + 1);

            $gradeColumn = GradeColumn::create([
                'section_subject_teacher_id' => $sst->id,
                'name' => $data['name'],
                'weight' => $data['weight'],
                'display_order' => $displayOrder,
                'observation' => $data['observation'] ?? null,
            ]);

            return $gradeColumn->fresh('sectionSubjectTeacher');
        });
    }
}