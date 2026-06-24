<?php

namespace App\Domains\Students\Enums;

use App\Domains\Shared\Traits\HasValues;

enum StudentSituation: string
{
    use HasValues;

    case Active = 'Cursando';
    case Paused = 'Pausado';
    case MedicalLeave = 'Baja médica';
    case Suspended = 'Suspendido';
    case FamilySituation = 'Situación familiar';
    case Inactive = 'Sin actividad';
}
