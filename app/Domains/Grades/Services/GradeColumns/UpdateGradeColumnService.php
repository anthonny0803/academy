<?php

namespace App\Domains\Grades\Services\GradeColumns;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Grades\Exceptions\GradeColumnWeightExceededException;
use App\Domains\Grades\Models\GradeColumn;
use Illuminate\Support\Facades\DB;

class UpdateGradeColumnService
{
    public function handle(GradeColumn $gradeColumn, array $data): GradeColumn
    {
        return DB::transaction(function () use ($gradeColumn, $data) {
            $sst = SectionSubjectTeacher::query()
                ->lockedById($gradeColumn->section_subject_teacher_id)
                ->firstOrFail();

            // El peso propio entra en el cálculo y se leyó antes del bloqueo
            $gradeColumn->refresh();

            // Si cambia el peso, validar que no exceda 100%
            if (isset($data['weight']) && $data['weight'] != $gradeColumn->weight) {
                $currentTotal = $sst->getTotalWeight();
                $newTotal = $currentTotal - $gradeColumn->weight + $data['weight'];

                if ($newTotal > 100) {
                    $maxAllowed = 100 - $currentTotal + $gradeColumn->weight;
                    throw GradeColumnWeightExceededException::maxAllowed($maxAllowed);
                }
            }

            $attributes = [
                'name' => $data['name'],
                'weight' => $data['weight'],
                'display_order' => $data['display_order'] ?? $gradeColumn->display_order,
            ];

            if (array_key_exists('observation', $data)) {
                $attributes['observation'] = $data['observation'];
            }

            $gradeColumn->update($attributes);

            return $gradeColumn->fresh('sectionSubjectTeacher');
        });
    }
}
