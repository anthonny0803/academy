<?php

namespace App\Domains\Academics\Repositories;

use App\Domains\Academics\Models\Subject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentSubjectRepository implements SubjectRepository
{
    public function create(array $attributes): Subject
    {
        return Subject::create($attributes);
    }

    public function update(Subject $subject, array $attributes): Subject
    {
        $subject->update($attributes);

        return $subject;
    }

    public function delete(Subject $subject): void
    {
        $subject->delete();
    }

    public function paginateForListing(string $search, ?bool $isActive, int $perPage = 6): LengthAwarePaginator
    {
        return Subject::query()
            ->when($search !== '', fn ($query) => $query->search($search))
            ->when(! is_null($isActive), fn ($query) => $isActive ? $query->active() : $query->inactive())
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function paginateWithActiveTeachers(string $search, ?string $subjectId, int $perPage = 6): LengthAwarePaginator
    {
        return Subject::query()
            ->with(['teachers' => fn ($query) => $query->where('is_active', true)->with('user')])
            ->active()
            ->when($search !== '', fn ($query) => $query->search($search))
            ->when($subjectId, fn ($query) => $query->where('id', $subjectId))
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function activeOrdered(): Collection
    {
        return Subject::active()
            ->orderBy('name')
            ->get();
    }
}
