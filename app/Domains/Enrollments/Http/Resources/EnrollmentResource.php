<?php

namespace App\Domains\Enrollments\Http\Resources;

use App\Domains\Academics\Http\Resources\SectionResource;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Students\Http\Resources\StudentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Enrollment
 */
class EnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'studentId' => $this->student_id,
            'sectionId' => $this->section_id,
            'status' => $this->status,
            'passed' => $this->passed,
            'student' => new StudentResource($this->whenLoaded('student')),
            'section' => new SectionResource($this->whenLoaded('section')),
            'createdAt' => $this->created_at->toIso8601String(),
            'updatedAt' => $this->updated_at->toIso8601String(),
        ];
    }
}
