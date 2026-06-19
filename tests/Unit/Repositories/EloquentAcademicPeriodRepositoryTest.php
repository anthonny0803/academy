<?php

namespace Tests\Unit\Repositories;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Repositories\AcademicPeriodRepository;
use App\Domains\Academics\Repositories\EloquentAcademicPeriodRepository;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentAcademicPeriodRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private AcademicPeriodRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->repository = app(AcademicPeriodRepository::class);
    }

    public function test_binding_resolves_to_eloquent_implementation(): void
    {
        $this->assertInstanceOf(EloquentAcademicPeriodRepository::class, $this->repository);
    }

    public function test_create_persists_an_academic_period(): void
    {
        $period = $this->repository->create([
            'name' => 'Curso Test',
            'start_date' => now(),
            'end_date' => now()->addMonths(6),
            'is_promotable' => false,
            'is_transferable' => false,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(AcademicPeriod::class, $period);
        $this->assertDatabaseHas('academic_periods', ['id' => $period->id, 'name' => 'CURSO TEST']);
    }

    public function test_update_changes_attributes(): void
    {
        $period = AcademicPeriod::factory()->create();

        $this->repository->update($period, ['notes' => 'Nota actualizada']);

        $this->assertSame('Nota actualizada', $period->fresh()->notes);
    }

    public function test_delete_removes_the_period(): void
    {
        $period = AcademicPeriod::factory()->create();

        $this->repository->delete($period);

        $this->assertDatabaseMissing('academic_periods', ['id' => $period->id]);
    }

    public function test_paginate_for_listing_matches_by_search(): void
    {
        $match = AcademicPeriod::factory()->create(['name' => 'Curso Matematicas']);
        $other = AcademicPeriod::factory()->create(['name' => 'Curso Historia']);

        $result = $this->repository->paginateForListing('Matematicas', null, 10);

        $this->assertTrue($result->contains($match));
        $this->assertFalse($result->contains($other));
    }

    public function test_paginate_for_listing_filters_by_active_status(): void
    {
        $active = AcademicPeriod::factory()->create();
        $inactive = AcademicPeriod::factory()->inactive()->create();

        $activeResult = $this->repository->paginateForListing('', true, 10);
        $this->assertTrue($activeResult->contains($active));
        $this->assertFalse($activeResult->contains($inactive));

        $inactiveResult = $this->repository->paginateForListing('', false, 10);
        $this->assertTrue($inactiveResult->contains($inactive));
        $this->assertFalse($inactiveResult->contains($active));
    }

    public function test_active_ordered_by_start_date_excludes_inactive_and_sorts_desc(): void
    {
        $older = AcademicPeriod::factory()->create(['start_date' => now()->addMonth()]);
        $newer = AcademicPeriod::factory()->create(['start_date' => now()->addYear()]);
        $inactive = AcademicPeriod::factory()->inactive()->create();

        $result = $this->repository->activeOrderedByStartDate();

        $this->assertFalse($result->contains($inactive));
        $this->assertTrue($result->first()->is($newer));
        $this->assertTrue($result->contains($older));
    }

    public function test_active_with_active_sections_eager_loads_only_active_sections(): void
    {
        $period = AcademicPeriod::factory()->create();
        $activeSection = Section::factory()->create(['academic_period_id' => $period->id]);
        $inactiveSection = Section::factory()->inactive()->create(['academic_period_id' => $period->id]);

        $result = $this->repository->activeWithActiveSections();
        $loaded = $result->firstWhere('id', $period->id);

        $this->assertTrue($loaded->relationLoaded('sections'));
        $this->assertTrue($loaded->sections->contains($activeSection));
        $this->assertFalse($loaded->sections->contains($inactiveSection));
    }
}
