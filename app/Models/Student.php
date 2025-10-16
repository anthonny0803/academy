<?php

namespace App\Models;

use App\Contracts\HasEntityName;
use App\Enums\RelationshipType;
use App\Traits\Activatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Student extends Model implements HasEntityName
{
    use Activatable;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'representative_id',
        'student_code',
        'relationship_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Contracts Implementation

    public function getEntityName(): string
    {
        return 'Estudiante';
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function representative(): BelongsTo
    {
        return $this->belongsTo(Representative::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function grades(): HasManyThrough
    {
        return $this->hasManyThrough(
            Grade::class,
            Enrollment::class,
            'student_id',
            'enrollment_id',
            'id',
            'id'
        );
    }

    // Query Scopes

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->whereHas('user', function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('document_id', 'like', "%{$term}%");
        })->orWhere('student_code', 'like', "%{$term}%");
    }

    public function scopeForRepresentative(Builder $query, int $representativeId): Builder
    {
        return $query->where('representative_id', $representativeId);
    }

    // Accessors

    public function getBirthDateAttribute()
    {
        return $this->user->birth_date ?? $this->attributes['birth_date'] ?? null;
    }

    // Helper Methods

    public function isChild(): bool
    {
        return $this->user->getAge() < 18;
    }

    public function isSelfRepresented(): bool
    {
        return $this->relationship_type === RelationshipType::SelfRepresented->value;
    }

    // Mutators

    protected function setStudentCodeAttribute($value): void
    {
        $this->attributes['student_code'] = strtoupper(trim($value));
    }
}
