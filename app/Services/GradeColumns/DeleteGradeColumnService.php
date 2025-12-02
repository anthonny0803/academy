<?php

namespace App\Services\GradeColumns;

use App\Models\GradeColumn;
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