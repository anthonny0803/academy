<?php

namespace App\Services\AcademicPeriods;

use App\Models\AcademicPeriod;
use Illuminate\Support\Facades\DB;

class UpdateAcademicPeriodService
{
    public function handle(AcademicPeriod $academicPeriod, array $data): AcademicPeriod
    {
        return DB::transaction(function () use ($academicPeriod, $data) {
            $academicPeriod->update([
                'name' => $data['name'],
                'notes' => $data['notes'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'is_promotable' => $data['is_promotable'] ?? $academicPeriod->is_promotable,
            ]);

            return $academicPeriod->fresh();
        });
    }
}