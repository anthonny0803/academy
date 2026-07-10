<?php

namespace App\Domains\Tenancy\Rules;

use App\Domains\Tenancy\Support\CurrentTenant;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class TenantExists
{
    public static function in(string $table, string $column = 'id'): Exists
    {
        return Rule::exists($table, $column)->where(static function ($query) {
            $tenantId = app(CurrentTenant::class)->id();

            return $tenantId === null
                ? $query->whereRaw('1 = 0')
                : $query->where('tenant_id', $tenantId);
        });
    }
}
