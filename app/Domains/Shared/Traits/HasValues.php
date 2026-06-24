<?php

namespace App\Domains\Shared\Traits;

trait HasValues
{
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
