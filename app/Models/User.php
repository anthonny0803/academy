<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles; // Importar el trait de Spatie para roles
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Attributes that are mass assignable.
     * Atributos que pueden ser asignados masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'sex',
        'is_active',
    ];

    /**
     * Attributes that should be hidden for serialization.
     * Atributos que deben ser ocultados cuando el modelo es serializado a arrays/JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'password' => 'hashed', // Hash de la contraseña del Usuario
        ];
    }

    /*
     * Definición de Relaciones del Modelo
     * Un User es la entidad central y puede tener un perfil de:
     * - Profesor
     * - Representante
     * - Estudiante
     */

    /**
     * Get the teacher profile associated with the user.
     * Obtiene el perfil de Profesor asociado a este Usuario (relación uno a uno).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    /**
     * Get the representative profile associated with the user.
     * Obtiene el perfil de Representante asociado a este Usuario (relación uno a uno).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function representative()
    {
        return $this->hasOne(Representative::class);
    }

    /**
     * Get the student profile associated with the user.
     * Obtiene el perfil de Estudiante asociado a este Usuario (relación uno a uno).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }
}
