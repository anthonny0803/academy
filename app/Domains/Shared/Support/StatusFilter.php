<?php

namespace App\Domains\Shared\Support;

class StatusFilter
{
    public static function toBool(?string $status): ?bool
    {
        return match ($status) {
            'Activo' => true,
            'Inactivo' => false,
            default => null,
        };
    }
}
