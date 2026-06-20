<?php

namespace App\Domains\Academics\Http\Resources;

use App\Domains\Academics\Models\Teacher;
use App\Domains\Identity\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Teacher
 */
class TeacherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'isActive' => $this->is_active,
            'user' => new UserResource($this->whenLoaded('user')),
            'createdAt' => $this->created_at->toIso8601String(),
            'updatedAt' => $this->updated_at->toIso8601String(),
        ];
    }
}
