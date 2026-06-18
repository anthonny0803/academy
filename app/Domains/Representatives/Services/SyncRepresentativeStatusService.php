<?php

namespace App\Domains\Representatives\Services;

use App\Domains\Representatives\Repositories\RepresentativeRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncRepresentativeStatusService
{
    public function __construct(
        private RepresentativeRepository $representativeRepository
    ) {}

    // Sync representative status based in if they have active students

    public function handle(string|array|Collection $representativeIds): array
    {
        $ids = collect($representativeIds)->unique()->filter()->values();

        if ($ids->isEmpty()) {
            return ['activated' => 0, 'deactivated' => 0];
        }

        return DB::transaction(function () use ($ids) {
            $results = ['activated' => 0, 'deactivated' => 0];

            $shouldBeActive = $this->representativeRepository->idsToActivate($ids);
            $shouldBeInactive = $this->representativeRepository->idsToDeactivate($ids);

            if ($shouldBeActive->isNotEmpty()) {
                $this->representativeRepository->updateActiveStatus($shouldBeActive, true);

                $results['activated'] = $shouldBeActive->count();

                Log::info('Representatives activated by sync', [
                    'representative_ids' => $shouldBeActive->toArray(),
                ]);
            }

            if ($shouldBeInactive->isNotEmpty()) {
                $this->representativeRepository->updateActiveStatus($shouldBeInactive, false);

                $results['deactivated'] = $shouldBeInactive->count();

                Log::info('Representatives deactivated by sync', [
                    'representative_ids' => $shouldBeInactive->toArray(),
                ]);
            }

            return $results;
        });
    }
}
