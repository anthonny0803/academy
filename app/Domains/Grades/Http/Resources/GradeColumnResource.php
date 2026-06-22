<?php

namespace App\Domains\Grades\Http\Resources;

use App\Domains\Grades\Models\GradeColumn;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GradeColumn
 */
class GradeColumnResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sectionSubjectTeacherId' => $this->section_subject_teacher_id,
            'name' => $this->name,
            'weight' => (float) $this->weight,
            'displayOrder' => $this->display_order,
            'observation' => $this->observation,
        ];
    }
}
