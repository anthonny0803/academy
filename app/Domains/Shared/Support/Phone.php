<?php

namespace App\Domains\Shared\Support;

class Phone
{
    public static function normalize(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return preg_replace('/[^0-9]/', '', $value);
    }
}
