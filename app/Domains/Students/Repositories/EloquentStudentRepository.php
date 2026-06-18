<?php

namespace App\Domains\Students\Repositories;

use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Students\Enums\StudentSituation;
use App\Domains\Students\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentStudentRepository implements StudentRepository
{
    public function create(array $attributes): Student
    {
        return Student::create($attributes);
    }

    public function update(Student $student, array $attributes): Student
    {
        $student->update($attributes);

        return $student;
    }

    public function lastCodeForPrefix(string $prefix): ?string
    {
        return Student::where('student_code', 'like', "{$prefix}%")
            ->orderBy('student_code', 'desc')
            ->value('student_code');
    }

    public function paginateForListing(string $search, ?bool $isActive, ?string $academicPeriodId, ?string $sectionId, int $perPage = 6): LengthAwarePaginator
    {
        return Student::query()
            ->with(['user', 'representative.user', 'enrollments.section'])
            ->search($search)
            ->when(! is_null($isActive), fn ($query) => $isActive ? $query->active() : $query->inactive())
            ->when($academicPeriodId, fn ($query) => $query->whereHas('enrollments.section', fn ($q) => $q->where('academic_period_id', $academicPeriodId)))
            ->when($sectionId, fn ($query) => $query->whereHas('enrollments', fn ($q) => $q->where('section_id', $sectionId)))
            ->orderBy('users.name')
            ->orderBy('users.last_name')
            ->select('students.*')
            ->paginate($perPage);
    }

    public function representativeIdsFor(array $studentIds): Collection
    {
        return Student::whereIn('id', $studentIds)->pluck('representative_id');
    }

    public function deactivateWithoutActiveEnrollments(array $studentIds): int
    {
        return Student::whereIn('id', $studentIds)
            ->where('is_active', true)
            ->whereDoesntHave('enrollments', fn ($query) => $query->where('status', EnrollmentStatus::Active->value))
            ->update([
                'is_active' => false,
                'situation' => StudentSituation::Inactive,
            ]);
    }
}
