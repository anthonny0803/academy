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
        'min_grade',
        'max_grade',
        'passing_grade',
        'is_promotable',
        'is_transferable',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_promotable' => 'boolean',
        'is_transferable' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'min_grade' => 'decimal:2',
        'max_grade' => 'decimal:2',
        'passing_grade' => 'decimal:2',
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
        $term = strtoupper($term);
        
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('notes', 'like', "%{$term}%");
        });
    }

    public function scopePromotable(Builder $query): Builder
    {
        return $query->where('is_promotable', true);
    }

    public function scopeTransferable(Builder $query): Builder
    {
        return $query->where('is_transferable', true);
    }

    // Helper Methods - Calificaciones

    public function getGradeRange(): array
    {
        return [
            'min' => (float) $this->min_grade,
            'max' => (float) $this->max_grade,
            'passing' => (float) $this->passing_grade,
        ];
    }

    public function isGradeValid(float $grade): bool
    {
        return $grade >= $this->min_grade && $grade <= $this->max_grade;
    }

    public function isGradePassing(float $grade): bool
    {
        return $grade >= $this->passing_grade;
    }

    // Helper Methods - Promoción y Transferencia

    public function isPromotable(): bool
    {
        return $this->is_promotable;
    }

    public function isTransferable(): bool
    {
        return $this->is_transferable;
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