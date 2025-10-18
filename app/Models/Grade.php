<?php

namespace App\Models;

use App\Contracts\HasEntityName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model implements HasEntityName
{
    use HasFactory;

    protected $fillable = [
        'enrollment_id',
        'section_subject_teacher_id',
        'grade_type',
        'grade',
        'observation',
    ];

    protected $casts = [
        'grade' => 'decimal:2',
    ];

    // Contracts Implementation

    public function getEntityName(): string
    {
        return 'Calificación';
    }

    // Relationships

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function sectionSubjectTeacher(): BelongsTo
    {
        return $this->belongsTo(SectionSubjectTeacher::class);
    }

    // Query Scopes

    public function scopeForEnrollment($query, int $enrollmentId)
    {
        return $query->where('enrollment_id', $enrollmentId);
    }

    public function scopeForSubject($query, int $subjectId)
    {
        return $query->whereHas('sectionSubjectTeacher', function ($q) use ($subjectId) {
            $q->where('subject_id', $subjectId);
        });
    }

    public function scopeByGradeType($query, string $gradeType)
    {
        return $query->where('grade_type', $gradeType);
    }

    // Helper Methods

    public function getSubject()
    {
        return $this->sectionSubjectTeacher->subject;
    }

    public function getTeacher()
    {
        return $this->sectionSubjectTeacher->teacher;
    }

    public function getSection()
    {
        return $this->sectionSubjectTeacher->section;
    }

    public function isPassing(float $minGrade = 10.0): bool
    {
        return $this->grade >= $minGrade;
    }

    // Mutators

    protected function setGradeTypeAttribute($value): void
    {
        $this->attributes['grade_type'] = strtoupper(trim($value));
    }

    protected function setObservationAttribute($value): void
    {
        $this->attributes['observation'] = $value ? strtoupper(trim($value)) : null;
    }
}