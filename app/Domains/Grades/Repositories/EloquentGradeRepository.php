<?php

namespace App\Domains\Grades\Repositories;

use App\Domains\Grades\Models\Grade;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentGradeRepository implements GradeRepository
{
    public function paginateForAssignment(string $sstId, int $perPage = 6): LengthAwarePaginator
    {
        return Grade::forAssignment($sstId)
            ->with(['enrollment.student.user', 'gradeColumn'])
            ->latest()
            ->orderBy('id')
            ->paginate($perPage);
    }

    public function create(array $attributes): Grade
    {
        return Grade::create($attributes);
    }

    public function update(Grade $grade, array $attributes): Grade
    {
        $grade->update($attributes);

        return $grade;
    }

    public function delete(Grade $grade): void
    {
        $grade->delete();
    }

    public function restore(Grade $grade): void
    {
        $grade->restore();
    }

    public function findWithTrashed(string $id): Grade
    {
        return Grade::withTrashed()->findOrFail($id);
    }

    public function updateOrCreate(array $attributes, array $values): Grade
    {
        return Grade::updateOrCreate($attributes, $values);
    }

    public function findByEnrollmentAndColumn(string $enrollmentId, string $columnId): ?Grade
    {
        return Grade::where('enrollment_id', $enrollmentId)
            ->where('grade_column_id', $columnId)
            ->first();
    }

    public function forGradeColumns(array $columnIds): Collection
    {
        return Grade::whereIn('grade_column_id', $columnIds)->get();
    }
}
