<?php

namespace App\Domains\Tenancy\Models;

use App\Domains\Identity\Models\User;
use App\Domains\Shared\Contracts\HasEntityName;
use App\Domains\Tenancy\Enums\TenantStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Tenant extends Model implements HasEntityName
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'plan',
        'status',
    ];

    // Contracts Implementation

    public function getEntityName(): string
    {
        return 'Organización';
    }

    // Mutators

    public function setSlugAttribute(?string $value): void
    {
        $this->attributes['slug'] = Str::slug((string) $value);
    }

    // Relationships

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // Helpers

    public function isActive(): bool
    {
        return $this->status === TenantStatus::Active->value;
    }
}
