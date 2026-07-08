<?php

namespace App\Domains\AI\Services;

use App\Domains\AI\Enums\ObservationStatus;
use App\Domains\AI\Exceptions\ObservationAlreadyInProgressException;
use App\Domains\AI\Models\StudentPerformanceObservation;
use App\Domains\Identity\Models\User;
use App\Domains\Students\Models\Student;
use Illuminate\Database\UniqueConstraintViolationException;

class RequestStudentPerformanceObservationService
{
    public function forStudent(Student $student, User $requestedBy): StudentPerformanceObservation
    {
        $this->guardAgainstInProgress($student);

        try {
            return StudentPerformanceObservation::create([
                'student_id' => $student->id,
                'requested_by_id' => $requestedBy->id,
                'status' => ObservationStatus::Pending,
            ]);
        } catch (UniqueConstraintViolationException) {
            throw ObservationAlreadyInProgressException::make();
        }
    }

    private function guardAgainstInProgress(Student $student): void
    {
        if (StudentPerformanceObservation::inProgressForStudent($student->id)->exists()) {
            throw ObservationAlreadyInProgressException::make();
        }
    }
}
