<?php

namespace App\Models;

use App\Contracts\HasEntityName;
use App\Traits\Activatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicPeriod extends Model implements HasEntityName
{
    use Activatable;
    use HasFactory;

    protected $fillable = [
        'name',
        'notes',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Contracts Implementation

    public function getEntityName(): string
    {
        return 'Período Académico';
    }

    // Relationships

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    // Query Scopes

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('notes', 'like', "%{$term}%");
        });
    }

    // Mutators

    protected function setNameAttribute($value): void
    {
        $this->attributes['name'] = strtoupper(trim($value));
    }

    protected function setNotesAttribute($value): void
    {
        $this->attributes['notes'] = $value ? ucfirst(trim($value)) : null;
    }
}
