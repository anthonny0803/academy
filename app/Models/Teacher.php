<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory; // Habilita el uso de factories para pruebas y seeders

    /**
     * Attributes that are mass assignable.
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     * Define cómo ciertos atributos deben ser convertidos a tipos de datos nativos de PHP.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean', // Conversión a booleano
        ];
    }

    /*
     * Definición de Relaciones del Modelo
     */

    /**
     * Get the user that owns the teacher profile.
     * Obtiene el Usuario propietario de este perfil de Profesor (relación uno a uno inversa).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subjects that the teacher is qualified to teach.
     * Obtiene las Asignaciones que este Profesor está cualificado para enseñar (relación muchos a muchos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher');
    }

    /**
     * Get the section-subject assignments where this teacher is assigned.
     * Obtiene las Asignaciones en Secciones donde este Profesor es el instructor.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sectionSubjectTeachers()
    {
        return $this->hasMany(SectionSubjectTeacher::class);
    }
}
