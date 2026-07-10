<?php

namespace App\Domains\Grades\Services\GradeColumns;

use App\Domains\Grades\Exceptions\GradeColumnHasGradesException;
use App\Domains\Grades\Models\GradeColumn;
use Illuminate\Support\Facades\DB;

class DeleteGradeColumnService
{
    public function handle(GradeColumn $gradeColumn): void
    {
        DB::transaction(function () use ($gradeColumn) {
            // Doble verificación: no eliminar si tiene notas
            if ($gradeColumn->hasGrades()) {
                throw GradeColumnHasGradesException::make();
            }

            $gradeColumn->delete();
        });
    }
}
