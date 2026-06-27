<?php

namespace App\Domains\Shared\Support;

class Address
{
    public static function normalize(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return strtoupper(trim($value));
    }
}
