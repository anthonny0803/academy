<?php

namespace App\Models;

use App\Enums\Role;
use App\Traits\Activatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use Activatable;

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'sex',
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
    ];

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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
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

    public function scopeDevelopers($query)
    {
        return $query->where('is_developer', true);
    }

    // Accessors

    public function getFullNameAttribute(): string
    {
        return trim("{$this->name} {$this->last_name}");
    }

    // Status Methods

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isDeveloper(): bool
    {
        return $this->is_developer ?? false;
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
}
