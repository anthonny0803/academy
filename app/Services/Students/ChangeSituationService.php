<?php

namespace App\Services\Students;

use App\Models\Student;
use App\Enums\StudentSituation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChangeSituationService
{
    public function handle(Student $student, StudentSituation $situation): Student
    {
        return DB::transaction(function () use ($student, $situation) {
            $oldSituation = $student->situation;

            $student->update(['situation' => $situation]);

            if ($oldSituation !== $situation) {
                Log::info('Student situation changed', [
                    'student_id' => $student->id,
                    'student_code' => $student->student_code,
                    'old_situation' => $oldSituation?->value ?? 'N/A',
                    'new_situation' => $situation->value,
                    'performed_by' => Auth::id(),
                    'performed_at' => now(),
                ]);
            }

            return $student->fresh(['user', 'representative']);
        });
    }
}