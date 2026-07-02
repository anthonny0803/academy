<?php

namespace App\Domains\AI\Http\Resources;

use App\Domains\AI\Models\StudentPerformanceObservation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin StudentPerformanceObservation
 */
class StudentPerformanceObservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'studentId' => $this->student_id,
            'status' => $this->status->value,
            // Untrusted AI output (OWASP LLM01): clients must render it as escaped text, never as HTML.
            'content' => $this->content,
            'failureReason' => $this->failure_reason,
            'requestedById' => $this->requested_by_id,
            'generatedAt' => $this->generated_at?->toIso8601String(),
            'createdAt' => $this->created_at->toIso8601String(),
            'updatedAt' => $this->updated_at->toIso8601String(),
        ];
    }
}
