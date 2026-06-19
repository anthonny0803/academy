<?php

namespace App\Domains\Academics\Http\Resources;

use App\Domains\Academics\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Section
 */
class SectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'academicPeriodId' => $this->academic_period_id,
            'name' => $this->name,
            'description' => $this->description,
            'capacity' => $this->capacity,
            'isActive' => $this->is_active,
            'academicPeriod' => new AcademicPeriodResource($this->whenLoaded('academicPeriod')),
            'createdAt' => $this->created_at->toIso8601String(),
            'updatedAt' => $this->updated_at->toIso8601String(),
        ];
    }
}
