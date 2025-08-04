<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grade extends Model
{
    use HasFactory; // Habilita el uso de factories para pruebas y seeders

    /**
     * The attributes that are mass assignable.
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subject_id',
        'enrollment_id',
        'grade_type',
        'grade_date',
        'score',
        'comments',
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
            'grade_date' => 'date', // Conversión a booleano
            'score' => 'decimal:2',  // Asegura que 'score' se trate como un decimal
        ];
    }

    /*
     * Definición de Relaciones del Modelo
     */

    /**
     * Get the subject that the grade belongs to.
     * Obtiene la Asignatura a la que pertenece esta Calificación (muchos a uno).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject()
    {
        // Una Calificación pertenece a una Asignatura
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the enrollment that the grade belongs to.
     * Obtiene la Inscripción a la que pertenece esta Calificación (relación uno a muchos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function enrollment()
    {
        // Una Calificación pertenece a una Inscripción
        return $this->belongsTo(Enrollment::class);
    }
}
