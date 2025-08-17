<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Representative extends Model
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
        'document_id',
        'phone',
        'address',
        'occupation',
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

    /*
     * Definición de Relaciones del Modelo
     */

    /**
     * Get the user that owns the representative profile.
     * Obtiene el Usuario propietario de este perfil de Representante (relación uno a uno inversa).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the students associated with this representative.
     * Obtiene los Estudiantes asociados a este Representante (relación uno a muchos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
