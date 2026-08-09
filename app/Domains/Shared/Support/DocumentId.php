<?php

namespace App\Domains\Shared\Support;

class DocumentId
{
    public const PATTERN = '/^[A-Z]?[0-9]{7,9}[A-Z]?$/';

    public const FORMAT_MESSAGE = 'El documento de identidad debe tener entre 7 y 9 dígitos, con una letra opcional al inicio y al final (ej: 12345678, 12345678A o X1234567B).';

    public const DUPLICATE_MESSAGE = 'Este documento ya está registrado por otra persona en el sistema.';

    public static function normalize(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }

        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', $value));
    }
}
