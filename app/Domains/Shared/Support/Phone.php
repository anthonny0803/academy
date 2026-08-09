<?php

namespace App\Domains\Shared\Support;

class Phone
{
    public const PATTERN = '/^[0-9]{9,15}$/';

    public const FORMAT_MESSAGE = 'El teléfono debe tener entre 9 y 15 dígitos.';

    public static function normalize(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return preg_replace('/[^0-9]/', '', $value);
    }
}
