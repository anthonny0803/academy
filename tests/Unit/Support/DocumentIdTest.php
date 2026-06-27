<?php

namespace Tests\Unit\Support;

use App\Domains\Shared\Support\DocumentId;
use Tests\TestCase;

class DocumentIdTest extends TestCase
{
    public function test_null_stays_null(): void
    {
        $this->assertNull(DocumentId::normalize(null));
    }

    public function test_empty_string_becomes_null(): void
    {
        $this->assertNull(DocumentId::normalize(''));
    }

    public function test_removes_separators_and_uppercases_dni(): void
    {
        $this->assertSame('12345678A', DocumentId::normalize('12.345.678-a'));
    }

    public function test_normalizes_nie_format(): void
    {
        $this->assertSame('X1234567B', DocumentId::normalize('x-1234567-b'));
    }

    public function test_strips_inner_whitespace(): void
    {
        $this->assertSame('12345678Z', DocumentId::normalize('  12 345 678 z  '));
    }
}
