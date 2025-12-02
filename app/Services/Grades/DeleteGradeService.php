<?php

namespace App\Services\Grades;

use App\Models\Grade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteGradeService
{
    public function handle(Grade $grade): void
    {
        DB::transaction(function () use ($grade) {
            // Auditoría: registrar eliminación
            Log::info('Grade deleted', [
                'grade_id' => $grade->id,
                'enrollment_id' => $grade->enrollment_id,
                'grade_column_id' => $grade->grade_column_id,
                'value' => $grade->value,
                'deleted_by' => Auth::id(),
                'deleted_at' => now(),
            ]);

            // Actualizar quién eliminó antes del soft delete
            $grade->update(['last_modified_by' => Auth::id()]);

            // Soft delete
            $grade->delete();
        });
    }

    /**
     * Restaurar nota eliminada (solo Developer)
     */
    public function restore(int $gradeId): Grade
    {
        return DB::transaction(function () use ($gradeId) {
            $grade = Grade::withTrashed()->findOrFail($gradeId);

            Log::info('Grade restored', [
                'grade_id' => $grade->id,
                'restored_by' => Auth::id(),
                'restored_at' => now(),
            ]);

            $grade->restore();
            $grade->update(['last_modified_by' => Auth::id()]);

            return $grade->fresh(['enrollment.student.user', 'gradeColumn']);
        });
    }
}