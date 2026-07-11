<?php

namespace App\Domains\Tenancy\Rules;

use App\Domains\Tenancy\Support\CurrentTenant;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class TenantUnique
{
    public static function in(string $table, ?string $column = null): Unique
    {
        return Rule::unique($table, $column ?? 'NULL')->where(static function ($query) {
            $tenantId = app(CurrentTenant::class)->id();

            // Unlike TenantExists, "1 = 0" here would be fail-open: zero
            // matches makes uniqueness pass. Strict fallback = global check.
            return $tenantId === null
                ? $query
                : $query->where('tenant_id', $tenantId);
        });
    }
}
