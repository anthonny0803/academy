<?php

namespace App\Domains\Tenancy\Http\Requests\Api\Tenants;

use App\Domains\Tenancy\Enums\TenantPlan;
use App\Domains\Tenancy\Enums\TenantStatus;
use Illuminate\Validation\Rule;

class StoreTenantRequest extends TenantRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('tenants', 'slug')],
            'plan' => ['nullable', Rule::in(TenantPlan::toArray())],
            'status' => ['nullable', Rule::in(TenantStatus::toArray())],
        ];
    }
}
