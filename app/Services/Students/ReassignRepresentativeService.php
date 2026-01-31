<?php

namespace App\Services\Students;

use App\Models\Student;
use App\Services\Representatives\SyncRepresentativeStatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReassignRepresentativeService
{
    public function __construct(
        private SyncRepresentativeStatusService $syncService
    ) {}

    public function handle(Student $student, int $newRepresentativeId, string $relationshipType, string $reason): Student
    {
        return DB::transaction(function () use ($student, $newRepresentativeId, $relationshipType, $reason) {
            $oldRepresentativeId = $student->representative_id;
            $oldRelationshipType = $student->relationship_type;

            $student->update([
                'representative_id' => $newRepresentativeId,
                'relationship_type' => $relationshipType,
            ]);

            $this->syncService->handle([$oldRepresentativeId, $newRepresentativeId]);

            Log::info('Student representative reassignment', [
                'student_id' => $student->id,
                'student_code' => $student->student_code,
                'old_representative_id' => $oldRepresentativeId,
                'new_representative_id' => $newRepresentativeId,
                'old_relationship_type' => $oldRelationshipType,
                'new_relationship_type' => $relationshipType,
                'reason' => $reason,
                'performed_by' => Auth::id(),
                'performed_at' => now(),
            ]);

            return $student->fresh(['user', 'representative.user']);
        });
    }
}