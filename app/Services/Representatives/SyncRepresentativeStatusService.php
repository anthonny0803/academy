<?php

namespace App\Services\Representatives;

use App\Models\Representative;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncRepresentativeStatusService
{
    // Sync representative status based in if they have active students

    public function handle(int|array|Collection $representativeIds): array
    {
        $ids = collect($representativeIds)->unique()->filter()->values();

        if ($ids->isEmpty()) {
            return ['activated' => 0, 'deactivated' => 0];
        }

        return DB::transaction(function () use ($ids) {
            $results = ['activated' => 0, 'deactivated' => 0];

            // Representatives must be actives if they have active students
            $shouldBeActive = Representative::whereIn('id', $ids)
                ->whereHas('students', fn($q) => $q->active())
                ->where('is_active', false)
                ->pluck('id');

            // Representatives must be inactives if they don't have active students
            $shouldBeInactive = Representative::whereIn('id', $ids)
                ->whereDoesntHave('students', fn($q) => $q->active())
                ->where('is_active', true)
                ->pluck('id');

            if ($shouldBeActive->isNotEmpty()) {
                Representative::whereIn('id', $shouldBeActive)
                    ->update(['is_active' => true]);

                $results['activated'] = $shouldBeActive->count();

                Log::info('Representatives activated by sync', [
                    'representative_ids' => $shouldBeActive->toArray(),
                ]);
            }

            if ($shouldBeInactive->isNotEmpty()) {
                Representative::whereIn('id', $shouldBeInactive)
                    ->update(['is_active' => false]);

                $results['deactivated'] = $shouldBeInactive->count();

                Log::info('Representatives deactivated by sync', [
                    'representative_ids' => $shouldBeInactive->toArray(),
                ]);
            }

            return $results;
        });
    }
}