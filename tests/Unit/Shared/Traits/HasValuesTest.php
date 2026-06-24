<?php

namespace Tests\Unit\Shared\Traits;

use PHPUnit\Framework\TestCase;
use Tests\Fixtures\Enums\SampleBackedEnum;

class HasValuesTest extends TestCase
{
    public function test_to_array_returns_backing_values_in_declaration_order(): void
    {
        $this->assertSame(
            ['first', 'second', 'third'],
            SampleBackedEnum::toArray()
        );
    }
}
