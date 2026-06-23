<?php

namespace App\Domains\Grades\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicRepresentativeGradesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'representative' => [
                'name' => $this['representative']['name'],
            ],
            'students' => PublicStudentGradesResource::collection($this['students']),
        ];
    }
}
