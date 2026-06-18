<?php

namespace App\Domains\Representatives\Repositories;

use App\Domains\Representatives\Models\Representative;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentRepresentativeRepository implements RepresentativeRepository
{
    public function create(array $attributes): Representative
    {
        return Representative::create($attributes);
    }

    public function firstOrCreateForUser(string $userId): Representative
    {
        return Representative::firstOrCreate(
            ['user_id' => $userId],
            ['is_active' => false]
        );
    }

    public function paginateForListing(string $search, ?bool $isActive, ?bool $hasStudents, int $perPage = 6): LengthAwarePaginator
    {
        return Representative::query()
            ->join('users', 'representatives.user_id', '=', 'users.id')
            ->select('representatives.*')
            ->search($search)
            ->when(! is_null($isActive), fn ($query) => $isActive ? $query->active() : $query->inactive())
            ->when($hasStudents === true, fn ($query) => $query->hasStudents())
            ->when($hasStudents === false, fn ($query) => $query->withoutStudents())
            ->with(['user', 'students'])
            ->orderBy('users.name')
            ->orderBy('users.last_name')
            ->paginate($perPage);
    }

    public function paginateForSearch(string $search, int $perPage = 5): LengthAwarePaginator
    {
        return Representative::query()
            ->join('users', 'representatives.user_id', '=', 'users.id')
            ->select('representatives.*')
            ->search($search)
            ->with('user')
            ->orderBy('users.name')
            ->orderBy('users.last_name')
            ->paginate($perPage);
    }

    public function idsToActivate(Collection $ids): Collection
    {
        return Representative::whereIn('id', $ids)
            ->whereHas('students', fn ($query) => $query->active())
            ->where('is_active', false)
            ->pluck('id');
    }

    public function idsToDeactivate(Collection $ids): Collection
    {
        return Representative::whereIn('id', $ids)
            ->whereDoesntHave('students', fn ($query) => $query->active())
            ->where('is_active', true)
            ->pluck('id');
    }

    public function updateActiveStatus(Collection $ids, bool $isActive): void
    {
        Representative::whereIn('id', $ids)->update(['is_active' => $isActive]);
    }
}
