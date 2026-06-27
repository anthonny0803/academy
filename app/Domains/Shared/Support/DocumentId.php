<?php

namespace App\Domains\Shared\Support;

class DocumentId
{
    public static function normalize(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', $value));
    }
}
