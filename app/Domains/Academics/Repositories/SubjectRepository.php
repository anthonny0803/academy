<?php

namespace App\Domains\Academics\Repositories;

use App\Domains\Academics\Models\Subject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SubjectRepository
{
    public function create(array $attributes): Subject;

    public function update(Subject $subject, array $attributes): Subject;

    public function delete(Subject $subject): void;

    public function paginateForListing(string $search, ?bool $isActive, int $perPage = 6): LengthAwarePaginator;

    public function paginateWithActiveTeachers(string $search, ?string $subjectId, int $perPage = 6): LengthAwarePaginator;

    public function activeOrdered(): Collection;
}
