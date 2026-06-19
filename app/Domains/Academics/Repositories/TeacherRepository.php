<?php

namespace App\Domains\Academics\Repositories;

use App\Domains\Academics\Models\Teacher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TeacherRepository
{
    public function create(array $attributes): Teacher;

    public function paginateForListing(string $search, ?bool $isActive, int $perPage = 6): LengthAwarePaginator;
}
