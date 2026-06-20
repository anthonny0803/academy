<?php

namespace App\Domains\Representatives\Http\Resources;

use App\Domains\Identity\Http\Resources\UserResource;
use App\Domains\Representatives\Models\Representative;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Representative
 */
class RepresentativeResource extends JsonResource
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
