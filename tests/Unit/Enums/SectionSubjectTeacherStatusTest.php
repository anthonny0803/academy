<?php

namespace Tests\Unit\Enums;

use App\Domains\Academics\Enums\SectionSubjectTeacherStatus;
use PHPUnit\Framework\TestCase;

class SectionSubjectTeacherStatusTest extends TestCase
{
    public function test_cases_expose_expected_values(): void
    {
        $this->assertSame('activo', SectionSubjectTeacherStatus::Active->value);
        $this->assertSame('inactivo', SectionSubjectTeacherStatus::Inactive->value);
        $this->assertSame('suplente', SectionSubjectTeacherStatus::Substitute->value);
    }

    public function test_to_array_returns_all_values(): void
    {
        $this->assertSame(
            ['activo', 'inactivo', 'suplente'],
            SectionSubjectTeacherStatus::toArray()
        );
    }
}
