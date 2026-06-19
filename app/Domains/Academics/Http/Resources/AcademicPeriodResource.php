<?php

namespace App\Domains\Academics\Http\Resources;

use App\Domains\Academics\Models\AcademicPeriod;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin AcademicPeriod
 */
class AcademicPeriodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'notes' => $this->notes,
            'startDate' => $this->start_date->toIso8601String(),
            'endDate' => $this->end_date->toIso8601String(),
            'minGrade' => (float) $this->min_grade,
            'maxGrade' => (float) $this->max_grade,
            'passingGrade' => (float) $this->passing_grade,
            'isPromotable' => $this->is_promotable,
            'isTransferable' => $this->is_transferable,
            'isActive' => $this->is_active,
            'createdAt' => $this->created_at->toIso8601String(),
            'updatedAt' => $this->updated_at->toIso8601String(),
        ];
    }
}
