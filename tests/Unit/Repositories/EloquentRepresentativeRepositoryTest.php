<?php

namespace Tests\Unit\Repositories;

use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Representatives\Repositories\EloquentRepresentativeRepository;
use App\Domains\Representatives\Repositories\RepresentativeRepository;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentRepresentativeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private RepresentativeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->repository = app(RepresentativeRepository::class);
    }

    public function test_binding_resolves_to_eloquent_implementation(): void
    {
        $this->assertInstanceOf(EloquentRepresentativeRepository::class, $this->repository);
    }

    public function test_create_persists_a_representative(): void
    {
        $user = User::factory()->create();

        $representative = $this->repository->create([
            'user_id' => $user->id,
            'is_active' => false,
        ]);

        $this->assertInstanceOf(Representative::class, $representative);
        $this->assertDatabaseHas('representatives', ['user_id' => $user->id]);
    }

    public function test_first_or_create_for_user_is_idempotent(): void
    {
        $user = User::factory()->create();

        $first = $this->repository->firstOrCreateForUser($user->id);
        $second = $this->repository->firstOrCreateForUser($user->id);

        $this->assertTrue($second->is($first));
        $this->assertSame(1, Representative::where('user_id', $user->id)->count());
    }

    public function test_paginate_for_listing_matches_by_search(): void
    {
        $user = User::factory()->create(['name' => 'Carlos']);
        $representative = Representative::factory()->create(['user_id' => $user->id]);

        $result = $this->repository->paginateForListing('Carlos', null, null, 10);

        $this->assertTrue($result->contains($representative));
    }

    public function test_paginate_for_listing_filters_by_has_students(): void
    {
        $userWith = User::factory()->create(['name' => 'Carlos']);
        $representativeWith = Representative::factory()->create(['user_id' => $userWith->id]);
        Student::factory()->create(['representative_id' => $representativeWith->id]);

        $userWithout = User::factory()->create(['name' => 'Carlos']);
        $representativeWithout = Representative::factory()->create(['user_id' => $userWithout->id]);

        $result = $this->repository->paginateForListing('Carlos', null, true, 10);

        $this->assertTrue($result->contains($representativeWith));
        $this->assertFalse($result->contains($representativeWithout));
    }

    public function test_paginate_for_search_matches_by_search(): void
    {
        $user = User::factory()->create(['name' => 'Carlos']);
        $representative = Representative::factory()->create(['user_id' => $user->id]);

        $result = $this->repository->paginateForSearch('Carlos', 5);

        $this->assertTrue($result->contains($representative));
    }

    public function test_ids_to_activate_returns_inactive_representatives_with_active_students(): void
    {
        $representative = Representative::factory()->create();
        Student::factory()->create(['representative_id' => $representative->id]);

        // The student factory auto-activates the representative; force the out-of-sync state
        $representative->refresh();
        $representative->update(['is_active' => false]);

        $ids = $this->repository->idsToActivate(collect([$representative->id]));

        $this->assertTrue($ids->contains($representative->id));
    }

    public function test_ids_to_deactivate_returns_active_representatives_without_active_students(): void
    {
        $representative = Representative::factory()->active()->create();

        $ids = $this->repository->idsToDeactivate(collect([$representative->id]));

        $this->assertTrue($ids->contains($representative->id));
    }

    public function test_update_active_status_bulk_updates(): void
    {
        $representative = Representative::factory()->create(['is_active' => false]);

        $this->repository->updateActiveStatus(collect([$representative->id]), true);

        $this->assertTrue($representative->fresh()->is_active);
    }
}
