<?php

namespace App\Models;

use App\Traits\Activatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Representative extends Model
{
    use HasFactory, Activatable;

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

    // Mutator para el formato de fecha
    public function setBirthDateAttribute($value)
    {
        $this->attributes['birth_date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    /**
     * Build a query filtered by search term and status.
     *
     * Returns a query builder if a search term is provided,
     * applying optional filter for status ('Activo', 'Inactivo').
     * 
     * If no search term or status is given, returns null to prevent
     * showing all representatives.
     *
     * @param string|null $term   Search term for user's name or last name
     * @param string|null $status Status filter ('Activo', 'Inactivo', or 'Todos')
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    public static function searchWithFilters(?string $term, ?string $status)
    {
        $term = trim((string) $term);

        // Solo ejecuta si hay un término de búsqueda
        if ($term === '') {
            return null;
        }

        $query = self::with('user.roles')
            ->whereHas('user', function ($q) use ($term) {
                $q->role('Representante')
                    ->where(
                        fn($q2) =>
                        $q2->where('name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%")
                    );
            });

        // Filtra por estado solo si hay un valor válido
        if ($status !== null && $status !== '' && $status !== 'Todos') {
            $isActive = $status === 'Activo' ? 1 : 0;
            $query->where('representatives.is_active', $isActive);
        }

        $query->join('users', 'representatives.user_id', '=', 'users.id')
            ->orderBy('users.name', 'asc')
            ->select('representatives.*');

        return $query;
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
