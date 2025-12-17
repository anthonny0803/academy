<?php

namespace App\Services\AcademicPeriods;

use App\Models\AcademicPeriod;
use Illuminate\Support\Facades\DB;

class StoreAcademicPeriodService
{
    public function handle(array $data): AcademicPeriod
    {
        return DB::transaction(function () use ($data) {
            $isPromotable = $data['is_promotable'] ?? true;

            $isTransferable = $isPromotable
                ? ($data['is_transferable'] ?? true)
                : false;

            return AcademicPeriod::create([
                'name' => $data['name'],
                'notes' => $data['notes'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'is_promotable' => $isPromotable,
                'is_transferable' => $isTransferable,
                'is_active' => true,
            ]);
        });
    }
}