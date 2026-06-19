<?php

namespace App\Domains\Identity\Http\Resources;

use App\Domains\Identity\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lastName' => $this->last_name,
            'fullName' => $this->full_name,
            'email' => $this->email,
            'sex' => $this->sex,
            'documentId' => $this->document_id,
            'birthDate' => $this->birth_date?->toIso8601String(),
            'phone' => $this->phone,
            'address' => $this->address,
            'occupation' => $this->occupation,
            'isActive' => $this->is_active,
            'isDeveloper' => $this->isDeveloper(),
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),
            'createdAt' => $this->created_at->toIso8601String(),
            'updatedAt' => $this->updated_at->toIso8601String(),
        ];
    }
}
