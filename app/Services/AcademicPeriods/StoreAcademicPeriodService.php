<?php

namespace App\Services\AcademicPeriods;

use App\Models\AcademicPeriod;
use Illuminate\Support\Facades\DB;

class StoreAcademicPeriodService
{
    public function handle(array $data): AcademicPeriod
    {
        return DB::transaction(function () use ($data) {
            return AcademicPeriod::create([
                'name' => $data['name'],
                'notes' => $data['notes'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'is_promotable' => $data['is_promotable'] ?? true,
                'is_active' => false,
            ]);
        });
    }
}