<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory; // Habilita el uso de factories para pruebas y seeders

    /**
     * The attributes that are mass assignable.
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'academic_period_id',
        'name',
        'description',
        'capacity',
        'is_active'
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
            'capacity' => 'integer',  // Asegura que 'capacity' se trate como un entero
        ];
    }

    /*
     * Definición de Relaciones del Modelo
     */

    /**
     * Get the academic period that the section belongs to.
     * Obtiene el Período Académico al que pertenece esta Sección (relación uno a muchos inversa).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicPeriod()
    {
        // Una sección pertenece a un Período Académico
        return $this->belongsTo(AcademicPeriod::class);
    }

    /**
     * Get the section-subject-teacher assignments for this section.
     * Obtiene las Asignaturas y sus Profesores para esta Sección desde SectionSubjectTeacher
     * (relación uno a muchos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sectionSubjectTeachers()
    {
        return $this->hasMany(SectionSubjectTeacher::class);
    }

    /**
     * Get the enrollments for this section.
     * Obtiene las Inscripciones de Estudiantes en esta Sección (relación uno a muchos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the students enrolled in this section through their enrollments.
     * Obtiene los Estudiantes inscritos en esta Sección a través de sus Inscripciones.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function students()
    {
        return $this->hasManyThrough(
            User::class, // Modelo final al que queremos llegar (el Usuario del Estudiante)
            Enrollment::class, // Modelo intermedio (Inscripción)
            'section_id', // Clave foránea en Enrollment que apunta a Section
            'id', // Clave local en User (User ID) referenciada por Student (el User del Student)
            'id', // Clave local en Section
            'user_id' // Clave foránea en Student que apunta a User (la relación entre Student y User)
        )->where('is_active', true); // Opcional: solo estudiantes activos
    }
}