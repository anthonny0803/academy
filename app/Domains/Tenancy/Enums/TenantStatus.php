<?php

namespace App\Domains\Tenancy\Enums;

use App\Domains\Shared\Traits\HasValues;

enum TenantStatus: string
{
    use HasValues;

    case Active = 'activo';
    case Suspended = 'suspendido';
    case Cancelled = 'cancelado';
}
