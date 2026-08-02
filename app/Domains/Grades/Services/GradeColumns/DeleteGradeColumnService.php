<?php

namespace App\Domains\Grades\Services\GradeColumns;

use App\Domains\Grades\Exceptions\GradeColumnHasGradesException;
use App\Domains\Grades\Models\GradeColumn;
use Illuminate\Support\Facades\DB;

class DeleteGradeColumnService
{
    public function handle(GradeColumn $gradeColumn): void
    {
        if ($gradeColumn->hasGrades()) {
            throw GradeColumnHasGradesException::make();
        }

        DB::transaction(function () use ($gradeColumn) {
            $gradeColumn->delete();
        });
    }
}
