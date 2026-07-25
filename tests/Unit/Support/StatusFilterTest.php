<?php

namespace Tests\Unit\Support;

use App\Domains\Shared\Support\StatusFilter;
use Tests\TestCase;

class StatusFilterTest extends TestCase
{
    public function test_activo_maps_to_true(): void
    {
        $this->assertTrue(StatusFilter::toBool('Activo'));
    }

    public function test_inactivo_maps_to_false(): void
    {
        $this->assertFalse(StatusFilter::toBool('Inactivo'));
    }

    public function test_null_maps_to_null(): void
    {
        $this->assertNull(StatusFilter::toBool(null));
    }

    public function test_unknown_value_maps_to_null(): void
    {
        $this->assertNull(StatusFilter::toBool('Todos'));
    }
}
