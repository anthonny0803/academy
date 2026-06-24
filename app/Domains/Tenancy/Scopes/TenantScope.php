<?php

namespace App\Domains\Tenancy\Scopes;

use App\Domains\Tenancy\Support\CurrentTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $currentTenant = app(CurrentTenant::class);

        if (! $currentTenant->has()) {
            return;
        }

        $builder->where($model->getTable().'.tenant_id', $currentTenant->id());
    }
}
