<?php

namespace App\Domains\Grades\Models;

use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Identity\Models\User;
use App\Domains\Students\Models\Student;
use App\Shared\Contracts\HasEntityName;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model implements HasEntityName
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'enrollment_id',
        'grade_column_id',
        'value',
        'observation',
        'last_modified_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
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

    public function gradeColumn(): BelongsTo
    {
        return $this->belongsTo(GradeColumn::class);
    }

    public function modifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }

    // Query Scopes

    public function scopeForEnrollment($query, string $enrollmentId)
    {
        return $query->where('enrollment_id', $enrollmentId);
    }

    public function scopeForColumn($query, string $columnId)
    {
        return $query->where('grade_column_id', $columnId);
    }

    public function scopeForAssignment($query, string $sstId)
    {
        return $query->whereHas('gradeColumn', function ($q) use ($sstId) {
            $q->where('section_subject_teacher_id', $sstId);
        });
    }

    public function scopeForSubject($query, string $subjectId)
    {
        return $query->whereHas('gradeColumn.sectionSubjectTeacher', function ($q) use ($subjectId) {
            $q->where('subject_id', $subjectId);
        });
    }

    public function scopeForSection($query, string $sectionId)
    {
        return $query->whereHas('gradeColumn.sectionSubjectTeacher', function ($q) use ($sectionId) {
            $q->where('section_id', $sectionId);
        });
    }

    // Helper Methods

    public function getStudent(): Student
    {
        return $this->enrollment->student;
    }

    public function getSection(): Section
    {
        return $this->gradeColumn->sectionSubjectTeacher->section;
    }

    public function getSubject(): Subject
    {
        return $this->gradeColumn->sectionSubjectTeacher->subject;
    }

    public function getTeacher(): Teacher
    {
        return $this->gradeColumn->sectionSubjectTeacher->teacher;
    }

    public function getColumnName(): string
    {
        return $this->gradeColumn->name;
    }

    public function getWeight(): float
    {
        return (float) $this->gradeColumn->weight;
    }

    public function isPassing(): bool
    {
        $passingGrade = $this->getSection()
            ->academicPeriod
            ->passing_grade;

        return $this->value >= $passingGrade;
    }

    public function getWeightedValue(): float
    {
        return ($this->value * $this->getWeight()) / 100;
    }

    // Mutators

    protected function setObservationAttribute($value): void
    {
        $this->attributes['observation'] = $value ? strtoupper(trim($value)) : null;
    }
}
