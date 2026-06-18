<?php

namespace Tests\Unit\Repositories;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Repositories\EloquentSectionRepository;
use App\Domains\Academics\Repositories\SectionRepository;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class EloquentSectionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private SectionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->repository = app(SectionRepository::class);
    }

    public function test_binding_resolves_to_eloquent_implementation(): void
    {
        $this->assertInstanceOf(EloquentSectionRepository::class, $this->repository);
    }

    public function test_create_persists_a_section(): void
    {
        $period = AcademicPeriod::factory()->create();

        $section = $this->repository->create([
            'academic_period_id' => $period->id,
            'name' => 'Matematicas',
            'capacity' => 30,
            'is_active' => true,
        ]);

        $this->assertInstanceOf(Section::class, $section);
        $this->assertDatabaseHas('sections', ['id' => $section->id, 'name' => 'MATEMATICAS']);
    }

    public function test_update_changes_attributes(): void
    {
        $section = Section::factory()->create();

        $this->repository->update($section, ['capacity' => 99]);

        $this->assertSame(99, $section->fresh()->capacity);
    }

    public function test_delete_removes_the_section(): void
    {
        $section = Section::factory()->create();

        $this->repository->delete($section);

        $this->assertDatabaseMissing('sections', ['id' => $section->id]);
    }

    public function test_find_returns_section_or_null(): void
    {
        $section = Section::factory()->create();

        $this->assertTrue($this->repository->find($section->id)->is($section));
        $this->assertNull($this->repository->find((string) Str::uuid()));
    }

    public function test_paginate_for_listing_matches_by_search(): void
    {
        $section = Section::factory()->create(['name' => 'Matematicas']);

        $result = $this->repository->paginateForListing('Matematicas', null, null, 10);

        $this->assertTrue($result->contains($section));
    }

    public function test_paginate_for_listing_filters_by_period(): void
    {
        $period = AcademicPeriod::factory()->create();
        $inPeriod = Section::factory()->create(['academic_period_id' => $period->id, 'name' => 'Matematicas']);

        $otherPeriod = AcademicPeriod::factory()->create();
        $outPeriod = Section::factory()->create(['academic_period_id' => $otherPeriod->id, 'name' => 'Matematicas']);

        $result = $this->repository->paginateForListing('Matematicas', null, $period->id, 10);

        $this->assertTrue($result->contains($inPeriod));
        $this->assertFalse($result->contains($outPeriod));
    }

    public function test_active_for_period_except_returns_other_active_sections(): void
    {
        $period = AcademicPeriod::factory()->create();
        $current = Section::factory()->create(['academic_period_id' => $period->id]);
        $other = Section::factory()->create(['academic_period_id' => $period->id]);
        $inactive = Section::factory()->inactive()->create(['academic_period_id' => $period->id]);

        $result = $this->repository->activeForPeriodExcept($period->id, $current->id);

        $this->assertTrue($result->contains($other));
        $this->assertFalse($result->contains($current));
        $this->assertFalse($result->contains($inactive));
    }
}
