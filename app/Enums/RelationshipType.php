<?php

namespace App\Enums;

enum RelationshipType: string
{
    case Father = 'Padre';
    case Mother = 'Madre';
    case LegalGuardian = 'Tutor Legal';
    case SelfRepresented = 'Auto-representante';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}
