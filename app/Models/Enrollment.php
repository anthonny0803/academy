<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enrollment extends Model
{
    use HasFactory; // Habilita el uso de factories para pruebas y seeders

    /**
     * The attributes that are mass assignable.
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'section_id',
        'status',
    ];

    /*
     * Definición de Relaciones del Modelo
     */

    /**
     * Get the academic period that the section belongs to.
     * Obtiene el Estudiante al que pertenece esta Inscripción (relación uno a uno).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        // Una Inscripción pertenece a un Estudiante
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the academic period that the section belongs to.
     * Obtiene el Estudiante al que pertenece esta Inscripción (relación uno a uno).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section()
    {
        // Una Inscripción pertenece a una Sección
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the grades for the enrollment.
     * Obtiene las Calificaciones asociadas a esta Inscripción (relación uno a muchos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function grades()
    {
        // Una inscripción puede tener muchas calificaciones (una por cada materia)
        return $this->hasMany(Grade::class);
    }
}
