<?php

namespace App\Services\Students;

use App\Enums\RelationshipType;
use App\Models\Representative;
use App\Models\Student;
use App\Services\Representatives\SyncRepresentativeStatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConvertToSelfRepresentedService
{
    public function __construct(
        private SyncRepresentativeStatusService $syncService
    ) {}

    public function handle(Student $student, ?string $reason = null): Student
    {
        return DB::transaction(function () use ($student, $reason) {
            $oldRepresentativeId = $student->representative_id;
            $oldRelationshipType = $student->relationship_type;

            // Find or create representative record for the student's user
            $selfRepresentative = Representative::firstOrCreate(
                ['user_id' => $student->user_id],
                ['is_active' => false] // Will be activated by sync
            );

            // Update student
            $student->update([
                'representative_id' => $selfRepresentative->id,
                'relationship_type' => RelationshipType::SelfRepresented->value,
            ]);

            // Sync status for old and new representative
            $this->syncService->handle([$oldRepresentativeId, $selfRepresentative->id]);

            Log::info('Student converted to self-represented', [
                'student_id' => $student->id,
                'student_code' => $student->student_code,
                'old_representative_id' => $oldRepresentativeId,
                'new_representative_id' => $selfRepresentative->id,
                'old_relationship_type' => $oldRelationshipType,
                'reason' => $reason ?? 'El estudiante alcanzó la mayoría de edad',
                'performed_by' => Auth::id(),
                'performed_at' => now(),
            ]);

            return $student->fresh(['user', 'representative.user']);
        });
    }
}