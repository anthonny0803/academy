<?php

namespace App\Domains\Grades\Repositories;

use App\Domains\Grades\Models\Grade;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface GradeRepository
{
    public function paginateForAssignment(string $sstId, int $perPage = 6): LengthAwarePaginator;

    public function create(array $attributes): Grade;

    public function update(Grade $grade, array $attributes): Grade;

    public function delete(Grade $grade): void;

    public function restore(Grade $grade): void;

    public function findWithTrashed(string $id): Grade;

    public function updateOrCreate(array $attributes, array $values): Grade;

    public function forGradeColumns(array $columnIds): Collection;
}
