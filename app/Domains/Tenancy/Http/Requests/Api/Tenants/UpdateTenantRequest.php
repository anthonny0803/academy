<?php

namespace App\Domains\Tenancy\Http\Requests\Api\Tenants;

use App\Domains\Tenancy\Enums\TenantPlan;
use App\Domains\Tenancy\Enums\TenantStatus;
use Illuminate\Validation\Rule;

class UpdateTenantRequest extends TenantRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('tenants', 'slug')->ignore($this->getTenantId())],
            'plan' => ['required', Rule::in(TenantPlan::toArray())],
            'status' => ['required', Rule::in(TenantStatus::toArray())],
        ];
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'plan.required' => 'El plan es obligatorio.',
            'status.required' => 'El estado es obligatorio.',
        ]);
    }

    protected function getTenantId(): string
    {
        return $this->route('tenant')->id;
    }
}
