<?php

namespace App\Domains\Grades\Services\GradeColumns;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Grades\Exceptions\GradeColumnWeightExceededException;
use App\Domains\Grades\Models\GradeColumn;
use Illuminate\Support\Facades\DB;

class StoreGradeColumnService
{
    public function handle(SectionSubjectTeacher $sst, array $data): GradeColumn
    {
        return DB::transaction(function () use ($sst, $data) {
            $sst = SectionSubjectTeacher::query()->lockedById($sst->id)->firstOrFail();

            // Validar que no exceda el 100%
            if (! $sst->canAddColumn($data['weight'])) {
                throw GradeColumnWeightExceededException::remaining($sst->getRemainingWeight());
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
