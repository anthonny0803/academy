<?php

namespace App\Domains\Academics\Repositories;

use App\Domains\Academics\Models\Section;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentSectionRepository implements SectionRepository
{
    public function create(array $attributes): Section
    {
        return Section::create($attributes);
    }

    public function update(Section $section, array $attributes): Section
    {
        $section->update($attributes);

        return $section;
    }

    public function delete(Section $section): void
    {
        $section->delete();
    }

    public function find(string $id): ?Section
    {
        return Section::find($id);
    }

    public function paginateForListing(string $search, ?bool $isActive, ?string $academicPeriodId, int $perPage = 6): LengthAwarePaginator
    {
        return Section::query()
            ->with('academicPeriod')
            ->withCount(['enrollments' => fn ($q) => $q->active()])
            ->when($search !== '', fn ($query) => $query->search($search))
            ->when(! is_null($isActive), fn ($query) => $isActive ? $query->active() : $query->inactive())
            ->when($academicPeriodId, fn ($query) => $query->forAcademicPeriod($academicPeriodId))
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function activeForPeriodExcept(string $academicPeriodId, string $exceptSectionId): Collection
    {
        return Section::active()
            ->where('academic_period_id', $academicPeriodId)
            ->where('id', '!=', $exceptSectionId)
            ->orderBy('name')
            ->get();
    }
}
