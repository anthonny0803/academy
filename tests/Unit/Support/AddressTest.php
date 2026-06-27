<?php

namespace Tests\Unit\Support;

use App\Domains\Shared\Support\Address;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function test_null_stays_null(): void
    {
        $this->assertNull(Address::normalize(null));
    }

    public function test_empty_string_becomes_null(): void
    {
        $this->assertNull(Address::normalize(''));
    }

    public function test_uppercases_and_trims(): void
    {
        $this->assertSame('CALLE MAYOR 10, MADRID', Address::normalize('  calle mayor 10, madrid  '));
    }
}
