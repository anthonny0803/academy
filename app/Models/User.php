<?php

namespace App\Models;

use App\Traits\Activatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin \Spatie\Permission\Traits\HasRoles
 */
class User extends Authenticatable
{
    use HasRoles, Notifiable, HasFactory, Activatable;

    /**
     * Attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'sex',
        'is_active',
    ];

    /**
     * Attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Build a user query filtered by search term, status, and role.
     *
     * This method returns a query builder if a search term is provided,
     * applying optional filters for status (Activo/Inactivo) and role.
     * 
     * If no search term is given, returns null to prevent exposing all users.
     *
     * @param string|null $term   Search term for name or last name.
     * @param string|null $status Status filter ('Activo', 'Inactivo', or 'Todos').
     * @param string|null $role   Role name filter (or 'Todos').
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    public static function searchWithFilters(?string $term, ?string $status, ?string $role)
    {
        $term = trim((string)$term);

        if ($term === '') {
            return null; // Return an empty builder.
        }

        $query = self::with('roles')
            ->where(
                fn($q) =>
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
            );

        // Filter by status if aply.
        if ($status !== null && $status !== '' && $status !== 'Todos') {
            $isActive = $status === 'Activo' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        // Filter by role if aply.
        if ($role !== null && $role !== '' && $role !== 'Todos') {
            $query->whereHas('roles', fn($q) => $q->where('name', $role));
        }

        return $query->orderBy('name', 'asc');
    }

    /**
     * Definitions of relationships with other models:
     * - Profesor
     * - Representante
     * - Estudiante
     */

    /**
     * Get the teacher profile associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Get the representative profile associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function representative()
    {
        return $this->hasOne(Representative::class);
    }

    /**
     * Get the student profile associated with the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Check if the user has a representative role.
     *
     * @return bool
     */
    public function isRepresentative(): bool
    {
        return $this->representative()->exists();
    }

    /**
     * Check if the user has a student role.
     *
     * @return bool
     */
    public function isStudent(): bool
    {
        return $this->student()->exists();
    }
}
