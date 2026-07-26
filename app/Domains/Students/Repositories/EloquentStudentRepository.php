<?php

namespace App\Domains\Students\Repositories;

use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Students\Enums\StudentSituation;
use App\Domains\Students\Models\Student;
use App\Domains\Tenancy\Support\CurrentTenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LogicException;

class EloquentStudentRepository implements StudentRepository
{
    private const CODE_SEQUENCE_LOCK_NAMESPACE = 'students:code_sequence:';

    public function __construct(
        private CurrentTenant $currentTenant
    ) {}

    public function create(array $attributes): Student
    {
        return Student::create($attributes);
    }

    public function update(Student $student, array $attributes): Student
    {
        $student->update($attributes);

        return $student;
    }

    /**
     * Serialize student code generation for the current tenant and prefix.
     *
     * A row lock cannot be used here because the read looks for the highest
     * existing code, and Postgres cannot lock a row that does not exist yet,
     * so two concurrent transactions would read the same maximum and generate
     * the same code.
     *
     * Both preconditions are enforced rather than documented because breaking
     * either one fails silently: outside a transaction the lock is released
     * as soon as the statement ends, and without a tenant every caller would
     * share a single degenerate key.
     */
    public function lockCodeSequence(string $prefix): void
    {
        if (DB::transactionLevel() === 0) {
            throw new LogicException('Student code sequence must be locked inside a DB transaction.');
        }

        $tenantId = $this->currentTenant->id();

        if ($tenantId === null) {
            throw new LogicException('Student code sequence cannot be locked without a resolved tenant.');
        }

        DB::selectOne('SELECT pg_advisory_xact_lock(?)', [
            crc32(self::CODE_SEQUENCE_LOCK_NAMESPACE.$tenantId.':'.$prefix),
        ]);
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
