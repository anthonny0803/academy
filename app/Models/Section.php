<?php

namespace App\Models;

use App\Contracts\HasEntityName;
use App\Traits\Activatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Section extends Model implements HasEntityName
{
    use Activatable;
    use HasFactory;

    protected $fillable = [
        'academic_period_id',
        'name',
        'description',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    // Contracts Implementation

    public function getEntityName(): string
    {
        return 'Sección';
    }

    // Relationships

    public function academicPeriod(): BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function sectionSubjectTeachers(): HasMany
    {
        return $this->hasMany(SectionSubjectTeacher::class);
    }

    public function students(): HasManyThrough
    {
        return $this->hasManyThrough(
            Student::class,
            Enrollment::class,
            'section_id',
            'id',
            'id',
            'student_id'
        );
    }

    // Query Scopes

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = strtoupper($term);
        
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeForAcademicPeriod(Builder $query, int $academicPeriodId): Builder
    {
        return $query->where('academic_period_id', $academicPeriodId);
    }

    // Helper Methods

    public function isFull(): bool
    {
        $enrolledCount = $this->enrollments()
            ->where('status', 'activo')
            ->count();

        return $enrolledCount >= $this->capacity;
    }

    // Mutators

    protected function setNameAttribute($value): void
    {
        $this->attributes['name'] = strtoupper(trim($value));
    }

    protected function setDescriptionAttribute($value): void
    {
        $this->attributes['description'] = $value ? ucfirst(trim($value)) : null;
    }
}
