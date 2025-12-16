<?php

namespace App\Enums;

enum StudentSituation: string
{
    case Active = 'Cursando';
    case Paused = 'Pausado';
    case MedicalLeave = 'Baja médica';
    case Suspended = 'Suspendido';
    case FamilySituation = 'Situación familiar';
    case Inactive = 'Sin actividad';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value');
    }
}