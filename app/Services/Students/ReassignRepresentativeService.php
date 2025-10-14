<?php

namespace App\Services\Students;

use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReassignRepresentativeService
{
    public function handle(Student $student, int $newRepresentativeId, string $reason): Student
    {
        return DB::transaction(function () use ($student, $newRepresentativeId, $reason) {
            $oldRepresentativeId = $student->representative_id;
            $student->update([
                'representative_id' => $newRepresentativeId,
            ]);

            Log::info('Student representative reassignment', [
                'student_id' => $student->id,
                'student_code' => $student->student_code,
                'old_representative_id' => $oldRepresentativeId,
                'new_representative_id' => $newRepresentativeId,
                'reason' => $reason,
                'performed_by' => Auth::id(),
                'performed_at' => now(),
            ]);

            return $student->fresh(['user', 'representative']);
        });
    }
}
