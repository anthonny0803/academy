<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SectionSubjectTeacher extends Model
{
    use HasFactory; // Habilita el uso de factories para pruebas y seeders

    /**
     * The attributes that are mass assignable.
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'section_id',
        'subject_id',
        'teacher_id',
        'assigned_at',
        'unassigned_at',
    ];

    /**
     * The attributes that should be cast.
     * Define cómo ciertos atributos deben ser convertidos a tipos de datos nativos de PHP.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'assigned_at' => 'date', // Conversión a carbon
            'unassigned_at' => 'date', // Conversión a carbon
        ];
    }

    /*
     * Definición de Relaciones del Modelo Pivote
     * Cada registro de SectionSubjectTeacher "pertenece a" una Sección, una Asignatura y un Profesor.
     */

    /**
     * Get the section associated with this assignment.
     * Obtiene la Sección asociada a esta relaciòn.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the subject associated with this assignment.
     * Obtiene la Asignaciòn asociada a esta relación.
     *
     * @return \Illuminate\Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher associated with this assignment.
     * Obtiene el Profesor asociado a esta relación.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
