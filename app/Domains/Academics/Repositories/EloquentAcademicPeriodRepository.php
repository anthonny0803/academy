<?php

namespace App\Domains\Academics\Repositories;

use App\Domains\Academics\Models\AcademicPeriod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentAcademicPeriodRepository implements AcademicPeriodRepository
{
    public function create(array $attributes): AcademicPeriod
    {
        return AcademicPeriod::create($attributes);
    }

    public function update(AcademicPeriod $academicPeriod, array $attributes): AcademicPeriod
    {
        $academicPeriod->update($attributes);

        return $academicPeriod;
    }

    public function delete(AcademicPeriod $academicPeriod): void
    {
        $academicPeriod->delete();
    }

    public function paginateForListing(string $search, ?bool $isActive, int $perPage = 6): LengthAwarePaginator
    {
        return AcademicPeriod::query()
            ->withCount('sections')
            ->when($search !== '', fn ($query) => $query->search($search))
            ->when(! is_null($isActive), fn ($query) => $isActive ? $query->active() : $query->inactive())
            ->orderBy('start_date', 'desc')
            ->paginate($perPage);
    }

    public function activeOrderedByStartDate(): Collection
    {
        return AcademicPeriod::active()
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function activeWithActiveSections(): Collection
    {
        return AcademicPeriod::active()
            ->with(['sections' => fn ($query) => $query->active()->orderBy('name')])
            ->orderBy('start_date', 'desc')
            ->get();
    }
}
