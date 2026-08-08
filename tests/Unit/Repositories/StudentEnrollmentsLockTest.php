<?php

namespace Tests\Unit\Repositories;

use App\Domains\Enrollments\Repositories\EnrollmentRepository;
use LogicException;
use Tests\TestCase;

/**
 * Deliberately without RefreshDatabase: that trait wraps every test in a
 * transaction, so it is the only way to reach transactionLevel() === 0 and
 * exercise the guard. The guard throws before touching the database.
 */
class StudentEnrollmentsLockTest extends TestCase
{
    public function test_lock_student_enrollments_rejects_being_called_outside_a_transaction(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('DB transaction');

        app(EnrollmentRepository::class)->lockStudentEnrollments('any-student-id');
    }
}
