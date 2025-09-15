<?php

namespace App\Models;

use App\Traits\Activatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Student extends Model
{
    use HasFactory, Activatable; // Habilita el uso de factories para pruebas y seeders

    /**
     * Attributes that are mass assignable.
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'representative_id',
        'student_code',
        'document_id',
        'relationship_type',
        'birth_date',
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
            'birth_date' => 'date', // Conversión a carbon
        ];
    }

    // Mutator para el formato de fecha
    public function setBirthDateAttribute($value)
    {
        $this->attributes['birth_date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    /*
     * Definición de Relaciones del Modelo
     */

    /**
     * Get the user that owns the student profile.
     * Obtiene el Usuario propietario de este perfil de Estudiante (relación uno a uno inversa).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the representative associated to this student profile.
     * Obtiene el Representante asociado a este perfil de Estudiante (relación uno a muchos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    /**
     * Get the enrollments for the student.
     * Obtiene las inscripciones de este Estudiante (relación uno a muchos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get the grades for the student across all their enrollments and subjects.
     * Obtiene todas las calificaciones de este Estudiante (relación uno a muchos a través de inscripciones).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function grades()
    {
        return $this->hasManyThrough(
            Grade::class, // El modelo final al que se apunta (calificaciones)
            Enrollment::class, // El modelo intermedio (inscripciones)
            'student_id', // La clave foránea en el modelo intermedio (Enrollment)
            'enrollment_id', // La clave foránea en el modelo final (Grade)
            'id', // La clave local en el modelo actual (Student)
            'id' // La clave local en el modelo intermedio (Enrollment)
        );
    }
}
