<?php

namespace App\Services\AcademicPeriods;

use App\Models\AcademicPeriod;
use Illuminate\Support\Facades\DB;

class UpdateAcademicPeriodService
{
    public function handle(AcademicPeriod $academicPeriod, array $data): AcademicPeriod
    {
        return DB::transaction(function () use ($academicPeriod, $data) {
            // Fields always editable
            $updateData = [
                'name' => $data['name'],
                'notes' => $data['notes'] ?? null,
            ];

            // If the Academic Period has no sections, allow editing more fields
            if (!$academicPeriod->hasSections()) {
                // Fechas
                if (isset($data['start_date'])) {
                    $updateData['start_date'] = $data['start_date'];
                }
                if (isset($data['end_date'])) {
                    $updateData['end_date'] = $data['end_date'];
                }

                // Promotable y Transferable
                if (array_key_exists('is_promotable', $data)) {
                    $isPromotable = (bool) $data['is_promotable'];
                    $updateData['is_promotable'] = $isPromotable;
                    
                    // is_transferable only if is_promotable is true
                    $updateData['is_transferable'] = $isPromotable 
                        ? (bool) ($data['is_transferable'] ?? false)
                        : false;
                }

                // Scale of grades
                if (isset($data['min_grade'], $data['passing_grade'], $data['max_grade'])) {
                    $updateData['min_grade'] = $data['min_grade'];
                    $updateData['passing_grade'] = $data['passing_grade'];
                    $updateData['max_grade'] = $data['max_grade'];
                }
            }

            $academicPeriod->update($updateData);

            return $academicPeriod->fresh();
        });
    }
}