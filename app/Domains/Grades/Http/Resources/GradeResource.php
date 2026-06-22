<?php

namespace App\Domains\Grades\Http\Resources;

use App\Domains\Enrollments\Http\Resources\EnrollmentResource;
use App\Domains\Grades\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Grade
 */
class GradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'enrollmentId' => $this->enrollment_id,
            'gradeColumnId' => $this->grade_column_id,
            'value' => (float) $this->value,
            'observation' => $this->observation,
            'lastModifiedBy' => $this->last_modified_by,
            'enrollment' => new EnrollmentResource($this->whenLoaded('enrollment')),
            'gradeColumn' => new GradeColumnResource($this->whenLoaded('gradeColumn')),
            'createdAt' => $this->created_at->toIso8601String(),
            'updatedAt' => $this->updated_at->toIso8601String(),
        ];
    }
}
