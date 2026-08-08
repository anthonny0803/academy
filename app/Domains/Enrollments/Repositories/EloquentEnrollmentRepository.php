<?php

namespace App\Domains\Enrollments\Repositories;

use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Tenancy\Support\CurrentTenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use LogicException;

class EloquentEnrollmentRepository implements EnrollmentRepository
{
    private const STUDENT_ENROLLMENTS_LOCK_NAMESPACE = 'enrollments:student:';

    public function __construct(
        private CurrentTenant $currentTenant
    ) {}

    public function create(array $attributes): Enrollment
    {
        return Enrollment::create($attributes);
    }

    public function update(Enrollment $enrollment, array $attributes): Enrollment
    {
        $enrollment->update($attributes);

        return $enrollment;
    }

    public function delete(Enrollment $enrollment): void
    {
        $enrollment->delete();
    }

    public function findOrFail(string $id): Enrollment
    {
        return Enrollment::findOrFail($id);
    }

    public function paginateForListing(string $search, ?string $status, ?string $academicPeriodId, ?string $sectionId, int $perPage = 6): LengthAwarePaginator
    {
        return Enrollment::query()
            ->with(['student.user', 'section.academicPeriod'])
            ->when($search !== '', fn ($query) => $query->search($search))
            ->when($status, fn ($query) => $query->byStatus($status))
            ->when($academicPeriodId, fn ($query) => $query->whereHas('section', fn ($q) => $q->where('academic_period_id', $academicPeriodId)))
            ->when($sectionId, fn ($query) => $query->forSection($sectionId))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Serialize the writes that decide whether a student may hold another
     * active enrollment.
     *
     * A row lock cannot be used here because the rule spans an academic period
     * while the unique index only covers (student, section): two concurrent
     * transactions would target different section rows, lock neither the same
     * row nor a row that exists yet, and both would read "no active enrollment".
     *
     * Both preconditions are enforced rather than documented because breaking
     * either one fails silently: outside a transaction Postgres releases the
     * lock as soon as the statement ends, and without a tenant every caller
     * would share a single degenerate key.
     */
    public function lockStudentEnrollments(string $studentId): void
    {
        if (DB::transactionLevel() === 0) {
            throw new LogicException('Student enrollments must be locked inside a DB transaction.');
        }

        $tenantId = $this->currentTenant->id();

        if ($tenantId === null) {
            throw new LogicException('Student enrollments cannot be locked without a resolved tenant.');
        }

        DB::selectOne('SELECT pg_advisory_xact_lock(?)', [
            crc32(self::STUDENT_ENROLLMENTS_LOCK_NAMESPACE.$tenantId.':'.$studentId),
        ]);
    }

    public function hasActiveEnrollmentInPeriod(string $studentId, string $academicPeriodId): bool
    {
        return Enrollment::where('student_id', $studentId)
            ->where('status', EnrollmentStatus::Active->value)
            ->whereHas('section', fn ($query) => $query->where('academic_period_id', $academicPeriodId))
            ->exists();
    }

    public function studentIdsForActivePeriod(string $academicPeriodId): Collection
    {
        return Enrollment::whereHas('section', fn ($query) => $query->where('academic_period_id', $academicPeriodId))
            ->where('status', EnrollmentStatus::Active->value)
            ->pluck('student_id');
    }
}
