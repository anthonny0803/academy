<?php

namespace App\Domains\AI\Enums;

use App\Domains\Shared\Traits\HasValues;

enum ObservationStatus: string
{
    use HasValues;

    case Pending = 'pendiente';
    case Processing = 'procesando';
    case Completed = 'completado';
    case Failed = 'fallido';
}
