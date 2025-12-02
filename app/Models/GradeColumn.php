<?php

namespace App\Models;

use App\Contracts\HasEntityName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeColumn extends Model implements HasEntityName
{
    use HasFactory;

    protected $fillable = [
        'section_subject_teacher_id',
        'name',
        'weight',
        'display_order',
        'observation',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'display_order' => 'integer',
    ];

    // Contracts Implementation

    public function getEntityName(): string
    {
        return 'Columna de Evaluación';
    }

    // Relationships

    public function sectionSubjectTeacher(): BelongsTo
    {
        return $this->belongsTo(SectionSubjectTeacher::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    // Query Scopes

    public function scopeForAssignment($query, int $sstId)
    {
        return $query->where('section_subject_teacher_id', $sstId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('id');
    }

    // Helper Methods

    public function hasGrades(): bool
    {
        return $this->grades()->exists();
    }

    public function getGradesCount(): int
    {
        return $this->grades()->count();
    }

    public function canBeDeleted(): bool
    {
        return !$this->hasGrades();
    }

    public function getSection(): Section
    {
        return $this->sectionSubjectTeacher->section;
    }

    public function getSubject(): Subject
    {
        return $this->sectionSubjectTeacher->subject;
    }

    public function getTeacher(): Teacher
    {
        return $this->sectionSubjectTeacher->teacher;
    }

    // Mutators

    protected function setNameAttribute($value): void
    {
        $this->attributes['name'] = strtoupper(trim($value));
    }

    protected function setObservationAttribute($value): void
    {
        $this->attributes['observation'] = $value ? trim($value) : null;
    }
}