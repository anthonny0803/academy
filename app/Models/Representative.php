<?php

namespace App\Models;

use App\Traits\Activatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Representative extends Model
{
    use HasFactory, Activatable;

    protected $fillable = [
        'user_id',
        'document_id',
        'phone',
        'address',
        'occupation',
        'birth_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'birth_date' => 'date',
        ];
    }

    public function setBirthDateAttribute($value)
    {
        $this->attributes['birth_date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    public static function searchWithFilters(?string $term, ?string $status)
    {
        $term = trim((string) $term);

        // If no search term is given, returns null to prevent exposing sensitive data.
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

        if ($status !== null && $status !== '' && $status !== 'Todos') {
            $isActive = $status === 'Activo' ? 1 : 0;
            $query->where('representatives.is_active', $isActive);
        }

        $query->join('users', 'representatives.user_id', '=', 'users.id')
            ->orderBy('users.name', 'asc')
            ->select('representatives.*');

        return $query;
    }

    // Relationships:

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function hasStudents(): bool
    {
        return $this->students()->exists();
    }
}
