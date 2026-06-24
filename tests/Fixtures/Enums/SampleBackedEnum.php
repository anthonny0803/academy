<?php

namespace Tests\Fixtures\Enums;

use App\Domains\Shared\Traits\HasValues;

enum SampleBackedEnum: string
{
    use HasValues;

    case First = 'first';
    case Second = 'second';
    case Third = 'third';
}
