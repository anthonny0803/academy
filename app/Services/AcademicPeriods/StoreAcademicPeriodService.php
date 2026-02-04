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

            $academicPeriodData = [
                'name' => $data['name'],
                'notes' => $data['notes'] ?? null,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'is_promotable' => $isPromotable,
                'is_transferable' => $isTransferable,
                'is_active' => true,
            ];

            // Add grade scale if provided otherwise DB defaults will be used
            if (isset($data['min_grade'], $data['passing_grade'], $data['max_grade'])) {
                $academicPeriodData['min_grade'] = $data['min_grade'];
                $academicPeriodData['passing_grade'] = $data['passing_grade'];
                $academicPeriodData['max_grade'] = $data['max_grade'];
            }

            return AcademicPeriod::create($academicPeriodData);
        });
    }
}