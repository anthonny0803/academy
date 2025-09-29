<?php

namespace App\Models;

use App\Traits\Activatable;
use App\Enums\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasRoles, Notifiable, HasFactory, Activatable;

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
        'password' => 'hashed',
    ];


    public static function searchWithFilters(?string $term, ?string $status, ?string $role)
    {
        $term = trim((string)$term);

        // If no search term is given, returns null to prevent exposing sensitive data.
        if ($term === '') {
            return null;
        }

        $query = self::with('roles')
            ->where(
                fn($q) =>
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
            );

        if ($status !== null && $status !== '' && $status !== 'Todos') {
            $isActive = $status === 'Activo' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        if ($role !== null && $role !== '' && $role !== 'Todos') {
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        return $query->orderBy('name', 'asc');
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }


    // Relationships:

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function representative()
    {
        return $this->hasOne(Representative::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    // Roles:

    public function isDeveloper(): bool
    {
        return $this->is_developer;
    }

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
