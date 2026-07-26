<?php

namespace Tests\Unit\Repositories;

use App\Domains\Students\Repositories\StudentRepository;
use LogicException;
use Tests\TestCase;

/**
 * Deliberately without RefreshDatabase: that trait wraps every test in a
 * transaction, so it is the only way to reach transactionLevel() === 0 and
 * exercise the guard. The guard throws before touching the database.
 */
class StudentCodeSequenceLockTest extends TestCase
{
    public function test_lock_code_sequence_rejects_being_called_outside_a_transaction(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('DB transaction');

        app(StudentRepository::class)->lockCodeSequence('CHILD');
    }
}
