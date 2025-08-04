<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory; // Habilita el uso de factories para pruebas y seeders

    /**
     * Attributes that are mass assignable.
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /*
     * Definición de Relaciones del Modelo
     */

    /**
     * Get the teachers who are qualified to teach this subject.
     * Obtiene los Profesores que están cualificados para enseñar esta Materia
     * (relación muchos a muchos a través de la tabla pivote 'subject_teacher').
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teachers()
    {
        // El segundo parámetro es el nombre de la tabla pivote
        return $this->belongsToMany(Teacher::class, 'subject_teacher');
    }

    /**
     * Get the grades associated with this subject.
     * Obtiene las Calificaciones asociadas a esta Materia (relación uno a muchos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get the section-subject-teacher assignments for this subject.
     * Obtiene las asignaciones de esta Asignatura a Secciones con un Profesor específico.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sectionSubjectTeachers()
    {
        return $this->hasMany(SectionSubjectTeacher::class);
    }
}
