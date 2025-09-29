<?php

namespace App\Models;

use App\Traits\Activatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory, Activatable;

    protected $fillable = [
        'user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    public static function searchWithFilters(?string $term, ?string $status)
    {
        $term = trim((string) $term);

        // Si no hay término de búsqueda, retornamos null para no exponer info sensible
        if ($term === '') {
            return null;
        }

        $query = self::with('user')
            ->whereHas('user', function ($q) use ($term) {
                $q->where(function ($q2) use ($term) {
                    $q2->where('name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            });

        // Filtro por estado activo/inactivo
        if ($status !== null && $status !== '' && $status !== 'Todos') {
            $isActive = $status === 'Activo' ? 1 : 0;
            $query->where('teachers.is_active', $isActive);
        }

        // Join para ordenar por nombre del usuario
        $query->join('users', 'teachers.user_id', '=', 'users.id')
            ->orderBy('users.name', 'asc')
            ->select('teachers.*');

        return $query;
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Definitions of relationships with other models:
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher');
    }

    public function sectionSubjectTeachers()
    {
        return $this->hasMany(SectionSubjectTeacher::class);
    }
}
