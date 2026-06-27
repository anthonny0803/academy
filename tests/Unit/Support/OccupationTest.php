<?php

namespace Tests\Unit\Support;

use App\Domains\Shared\Support\Occupation;
use Tests\TestCase;

class OccupationTest extends TestCase
{
    public function test_null_stays_null(): void
    {
        $this->assertNull(Occupation::normalize(null));
    }

    public function test_empty_string_becomes_null(): void
    {
        $this->assertNull(Occupation::normalize(''));
    }

    public function test_uppercases_and_trims(): void
    {
        $this->assertSame('INGENIERO DE SOFTWARE', Occupation::normalize('  ingeniero de software  '));
    }
}
