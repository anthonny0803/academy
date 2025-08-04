<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademicPeriod extends Model
{
    use HasFactory; // Habilita el uso de factories para pruebas y seeders

    /**
     * The attributes that are mass assignable.
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'notes',
        'start_date',
        'end_date',
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
            'start_date' => 'date', // Conversiòn a carbon
            'end_date' => 'date', // Conversiòn a carbon
            'is_active' => 'boolean', // Conversión a booleano
        ];
    }

    /*
     * Definición de Relaciones del Modelo
     */

    /**
     * Get the sections associated with this academic period.
     * Obtiene las Secciones asociadas a este Período Académico (relación uno a muchos).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    /**
     * Check if the academic period is currently active based on dates and status flag.
     * Verifica si el Período Académico está actualmente activo, basándose en las fechas y el estado.
     *
     * @return bool
     */
    public function isCurrent()
    {
        $now = now(); // Obtiene la fecha y hora actual

        // Comprueba si el flag 'is_active' es verdadero Y
        // si la fecha actual está en o después de la start_date Y
        // si la fecha actual está en o antes de la end_date.
        return $this->is_active && $now->isBetween($this->start_date, $this->end_date, true);
    }
}
