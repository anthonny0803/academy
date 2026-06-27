<?php

namespace Tests\Unit\Support;

use App\Domains\Shared\Support\Phone;
use Tests\TestCase;

class PhoneTest extends TestCase
{
    public function test_null_stays_null(): void
    {
        $this->assertNull(Phone::normalize(null));
    }

    public function test_empty_string_becomes_null(): void
    {
        $this->assertNull(Phone::normalize(''));
    }

    public function test_keeps_only_digits(): void
    {
        $this->assertSame('34612345678', Phone::normalize('+34 612 345 678'));
    }

    public function test_strips_separators_and_letters(): void
    {
        $this->assertSame('612345678', Phone::normalize('(612) 345-678 ext'));
    }
}
