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

    public function test_pattern_accepts_the_shortest_and_the_longest_number(): void
    {
        $this->assertMatchesRegularExpression(Phone::PATTERN, '123456789');
        $this->assertMatchesRegularExpression(Phone::PATTERN, '123456789012345');
    }

    public function test_pattern_rejects_lengths_outside_the_range(): void
    {
        $this->assertDoesNotMatchRegularExpression(Phone::PATTERN, '12345678');
        $this->assertDoesNotMatchRegularExpression(Phone::PATTERN, '1234567890123456');
    }

    public function test_pattern_rejects_what_normalize_would_have_stripped(): void
    {
        $this->assertDoesNotMatchRegularExpression(Phone::PATTERN, '+34 612 345 678');
    }
}
