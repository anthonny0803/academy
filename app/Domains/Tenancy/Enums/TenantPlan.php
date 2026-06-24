<?php

namespace App\Domains\Tenancy\Enums;

use App\Domains\Shared\Traits\HasValues;

enum TenantPlan: string
{
    use HasValues;

    case Free = 'free';
    case Pro = 'pro';
    case Enterprise = 'enterprise';
}
