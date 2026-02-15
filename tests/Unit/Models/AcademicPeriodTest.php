<?php

namespace Tests\Unit\Models;

use App\Models\AcademicPeriod;
use PHPUnit\Framework\TestCase;

class AcademicPeriodTest extends TestCase
{
    private function makePeriod(array $attrs = []): AcademicPeriod
    {
        return new AcademicPeriod(array_merge([
            'name' => 'Curso 2025-2026',
            'min_grade' => 0,
            'max_grade' => 10,
            'passing_grade' => 5,
            'is_promotable' => true,
            'is_transferable' => false,
            'is_active' => true,
        ], $attrs));
    }

    public function test_grade_within_range_is_valid(): void
    {
        $period = $this->makePeriod();

        $this->assertTrue($period->isGradeValid(5));
        $this->assertTrue($period->isGradeValid(0));
        $this->assertTrue($period->isGradeValid(10));
    }

    public function test_grade_below_min_is_invalid(): void
    {
        $period = $this->makePeriod();

        $this->assertFalse($period->isGradeValid(-1));
    }

    public function test_grade_above_max_is_invalid(): void
    {
        $period = $this->makePeriod();

        $this->assertFalse($period->isGradeValid(10.01));
    }

    public function test_grade_equal_to_passing_is_passing(): void
    {
        $period = $this->makePeriod();

        $this->assertTrue($period->isGradePassing(5));
    }

    public function test_grade_above_passing_is_passing(): void
    {
        $period = $this->makePeriod();

        $this->assertTrue($period->isGradePassing(7.5));
    }

    public function test_grade_below_passing_is_not_passing(): void
    {
        $period = $this->makePeriod();

        $this->assertFalse($period->isGradePassing(4.99));
    }

    public function test_get_grade_range_returns_correct_values(): void
    {
        $period = $this->makePeriod();
        $range = $period->getGradeRange();

        $this->assertSame(0.0, $range['min']);
        $this->assertSame(10.0, $range['max']);
        $this->assertSame(5.0, $range['passing']);
    }

    public function test_grade_validation_with_scale_0_to_100(): void
    {
        $period = $this->makePeriod([
            'min_grade' => 1,
            'max_grade' => 100,
            'passing_grade' => 60,
        ]);

        $this->assertTrue($period->isGradeValid(60));
        $this->assertFalse($period->isGradeValid(0));
        $this->assertTrue($period->isGradePassing(60));
        $this->assertFalse($period->isGradePassing(59));
    }

    public function test_is_promotable(): void
    {
        $this->assertTrue($this->makePeriod(['is_promotable' => true])->isPromotable());
        $this->assertFalse($this->makePeriod(['is_promotable' => false])->isPromotable());
    }

    public function test_is_transferable(): void
    {
        $this->assertTrue($this->makePeriod(['is_transferable' => true])->isTransferable());
        $this->assertFalse($this->makePeriod(['is_transferable' => false])->isTransferable());
    }

    public function test_name_mutator_uppercases_and_trims(): void
    {
        $period = new AcademicPeriod();
        $period->name = '  curso 2025-2026  ';

        $this->assertSame('CURSO 2025-2026', $period->name);
    }

    public function test_notes_mutator_applies_ucfirst_and_trims(): void
    {
        $period = new AcademicPeriod();
        $period->notes = '  período de recuperación  ';

        $this->assertSame('Período de recuperación', $period->notes);
    }

    public function test_notes_null_stays_null(): void
    {
        $period = new AcademicPeriod();
        $period->notes = null;

        $this->assertNull($period->notes);
    }

    public function test_is_active_via_activatable_trait(): void
    {
        $this->assertTrue($this->makePeriod(['is_active' => true])->isActive());
        $this->assertFalse($this->makePeriod(['is_active' => false])->isActive());
    }
}
