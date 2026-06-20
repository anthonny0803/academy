<?php

namespace App\Domains\Students\Http\Resources;

use App\Domains\Identity\Http\Resources\UserResource;
use App\Domains\Representatives\Http\Resources\RepresentativeResource;
use App\Domains\Students\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Student
 */
class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'studentCode' => $this->student_code,
            'situation' => $this->situation?->value,
            'relationshipType' => $this->relationship_type,
            'isActive' => $this->is_active,
            'representativeId' => $this->representative_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'representative' => new RepresentativeResource($this->whenLoaded('representative')),
            'createdAt' => $this->created_at->toIso8601String(),
            'updatedAt' => $this->updated_at->toIso8601String(),
        ];
    }
}
