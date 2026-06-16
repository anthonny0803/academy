<?php

namespace App\Domains\Grades\Services\GradeColumns;

use App\Domains\Grades\Models\GradeColumn;
use Illuminate\Support\Facades\DB;

class DeleteGradeColumnService
{
    public function handle(GradeColumn $gradeColumn): void
    {
        DB::transaction(function () use ($gradeColumn) {
            // Doble verificación: no eliminar si tiene notas
            if ($gradeColumn->hasGrades()) {
                throw new \Exception(
                    'No se puede eliminar esta evaluación porque tiene notas registradas.'
                );
            }

            $gradeColumn->delete();
        });
    }
}
