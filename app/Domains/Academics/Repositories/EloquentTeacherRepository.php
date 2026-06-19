<?php

namespace App\Domains\Academics\Repositories;

use App\Domains\Academics\Models\Teacher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentTeacherRepository implements TeacherRepository
{
    public function create(array $attributes): Teacher
    {
        return Teacher::create($attributes);
    }

    public function paginateForListing(string $search, ?bool $isActive, int $perPage = 6): LengthAwarePaginator
    {
        return Teacher::query()
            ->when($search !== '', fn ($query) => $query->search($search))
            ->when(! is_null($isActive), fn ($query) => $isActive ? $query->active() : $query->inactive())
            ->orderByUserName()
            ->with('user')
            ->paginate($perPage);
    }
}
