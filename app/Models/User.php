<?php

namespace App\Models;

use App\Contracts\HasEntityName;
use App\Enums\Role;
use App\Enums\Sex;
use App\Traits\Activatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasEntityName
{
    use Activatable;
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'sex',
        'document_id',
        'birth_date',
        'phone',
        'address',
        'occupation',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_developer' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
    ];

    // Contracts Implementation

    public function getEntityName(): string
    {
        return 'Usuario';
    }

    // Relationships

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function representative(): HasOne
    {
        return $this->hasOne(Representative::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    // Query Scopes

    public function scopeOrderByName($query, string $direction = 'asc')
    {
        return $query->orderBy('name', $direction)->orderBy('last_name', $direction);
    }

    public function scopeWithRole($query, string $role)
    {
        return $query->whereHas('roles', fn($q) => $q->where('name', $role));
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");
        });
    }

    public function scopeEmployees($query)
    {
        return $query->whereHas('roles', function ($roleQuery) {
            $roleQuery->whereIn('name', [
                Role::Supervisor->value,
                Role::Admin->value,
                Role::Teacher->value,
            ]);
        });
    }

    // Accessors

    public function getFullNameAttribute(): string
    {
        return trim("{$this->name} {$this->last_name}");
    }

    public function getAgeAttribute(): ?int
    {
        return $this->birth_date ? $this->birth_date->age : null;
    }

    // Role Verification Methods

    public function isSupervisor(): bool
    {
        return $this->hasRole(Role::Supervisor->value);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::Admin->value);
    }

    public function isTeacher(): bool
    {
        return $this->hasRole(Role::Teacher->value);
    }

    public function isRepresentative(): bool
    {
        return $this->hasRole(Role::Representative->value);
    }

    public function isStudent(): bool
    {
        return $this->hasRole(Role::Student->value);
    }

    // Status Methods

    public function isDeveloper(): bool
    {
        return $this->is_developer ?? false;
    }

    public function isEmployee(): bool
    {
        return $this->isDeveloper()
            || $this->isSupervisor()
            || $this->isAdmin()
            || $this->isTeacher();
    }

    public function isMale(): bool
    {
        return $this->sex === Sex::Male->value;
    }

    public function isFemale(): bool
    {
        return $this->sex === Sex::Female->value;
    }

    // Helper Methods

    public function hasEntity(): bool
    {
        return $this->teacher()->exists()
            || $this->representative()->exists()
            || $this->student()->exists();
    }

    // Mutators

    protected function setEmailAttribute($value): void
    {
        $this->attributes['email'] = !empty($value) ? strtolower(trim($value)) : null;
    }

    protected function setNameAttribute($value): void
    {
        $this->attributes['name'] = strtoupper(trim($value));
    }

    protected function setLastNameAttribute($value): void
    {
        $this->attributes['last_name'] = strtoupper(trim($value));
    }

    protected function setDocumentIdAttribute($value): void
    {
        $this->attributes['document_id'] = $value
            ? strtoupper(preg_replace('/[^A-Z0-9]/i', '', $value))
            : null;
    }

    protected function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = $value
            ? preg_replace('/[^0-9]/', '', $value)
            : null;
    }

    protected function setAddressAttribute($value): void
    {
        $this->attributes['address'] = $value ? strtoupper(trim($value)) : null;
    }

    protected function setOccupationAttribute($value): void
    {
        $this->attributes['occupation'] = $value ? strtoupper(trim($value)) : null;
    }
}
