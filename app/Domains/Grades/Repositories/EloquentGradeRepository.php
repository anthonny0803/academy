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

    public function forGradeColumns(array $columnIds): Collection
    {
        return Grade::whereIn('grade_column_id', $columnIds)->get();
    }
}
