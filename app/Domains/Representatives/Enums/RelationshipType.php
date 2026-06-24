<?php

namespace App\Domains\Representatives\Enums;

use App\Domains\Shared\Traits\HasValues;

enum RelationshipType: string
{
    use HasValues;

    case Father = 'Padre';
    case Mother = 'Madre';
    case LegalGuardian = 'Tutor Legal';
    case SelfRepresented = 'Auto-representante';
}
