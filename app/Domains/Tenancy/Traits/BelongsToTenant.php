<?php

namespace App\Domains\Tenancy\Traits;

use App\Domains\Tenancy\Scopes\TenantScope;
use App\Domains\Tenancy\Support\CurrentTenant;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function (Model $model): void {
            $currentTenant = app(CurrentTenant::class);

            if ($model->tenant_id !== null || ! $currentTenant->has()) {
                return;
            }

            $model->tenant_id = $currentTenant->id();
        });
    }
}
