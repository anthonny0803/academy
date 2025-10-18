<?php

namespace App\Models;

use App\Contracts\HasEntityName;
use App\Traits\Activatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Subject extends Model implements HasEntityName
{
    use Activatable;
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Contracts Implementation

    public function getEntityName(): string
    {
        return 'Asignatura';
    }

    // Relationships

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'subject_teacher')
            ->withTimestamps();
    }

    public function sectionSubjectTeachers(): HasMany
    {
        return $this->hasMany(SectionSubjectTeacher::class);
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'section_subject_teacher')
            ->withPivot('teacher_id', 'is_primary', 'status')
            ->withTimestamps();
    }

    public function grades(): HasManyThrough
    {
        return $this->hasManyThrough(
            Grade::class,
            SectionSubjectTeacher::class,
            'subject_id',
            'section_subject_teacher_id',
            'id',
            'id'
        );
    }

    // Query Scopes

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    // Mutators

    protected function setNameAttribute($value): void
    {
        $this->attributes['name'] = strtoupper(trim($value));
    }

    protected function setDescriptionAttribute($value): void
    {
        $this->attributes['description'] = $value ? strtoupper(trim($value)) : null;
    }
}
