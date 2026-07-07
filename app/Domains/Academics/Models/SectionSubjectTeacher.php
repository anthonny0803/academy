<?php

namespace App\Domains\Academics\Models;

use App\Domains\Academics\Enums\SectionSubjectTeacherStatus;
use App\Domains\Grades\Models\Grade;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class SectionSubjectTeacher extends Model
{
    use BelongsToTenant;
    use HasFactory;
    use HasUuids;

    protected $table = 'section_subject_teacher';

    protected $fillable = [
        'section_id',
        'subject_id',
        'teacher_id',
        'is_primary',
        'status',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // Relationships

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function gradeColumns(): HasMany
    {
        return $this->hasMany(GradeColumn::class);
    }

    public function grades(): HasManyThrough
    {
        return $this->hasManyThrough(
            Grade::class,
            GradeColumn::class,
            'section_subject_teacher_id',
            'grade_column_id',
            'id',
            'id'
        );
    }

    // Query Scopes

    public function scopeActive($query)
    {
        return $query->where('status', SectionSubjectTeacherStatus::Active->value);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeForTeacher($query, string $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopePrimaryFor($query, string $sectionId, string $subjectId, ?string $exceptId = null)
    {
        return $query->where('section_id', $sectionId)
            ->where('subject_id', $subjectId)
            ->where('is_primary', true)
            ->when($exceptId, fn ($subQuery) => $subQuery->where('id', '!=', $exceptId));
    }

    // Helper Methods - Estado

    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    public function isActive(): bool
    {
        return $this->status === SectionSubjectTeacherStatus::Active->value;
    }

    // Helper Methods - Configuración de Columnas

    public function getTotalWeight(): float
    {
        return (float) $this->gradeColumns()->sum('weight');
    }

    public function isConfigurationComplete(): bool
    {
        return $this->getTotalWeight() === 100.0;
    }

    public function hasGradeColumns(): bool
    {
        return $this->gradeColumns()->exists();
    }

    public function getGradeColumnsOrdered()
    {
        return $this->gradeColumns()->ordered()->get();
    }

    public function canAddColumn(float $weight): bool
    {
        return ($this->getTotalWeight() + $weight) <= 100;
    }

    public function getRemainingWeight(): float
    {
        return 100 - $this->getTotalWeight();
    }

    // Helper Methods - Calificaciones

    public function hasAnyGrades(): bool
    {
        return $this->grades()->exists();
    }

    // Calcula el promedio ponderado de un estudiante

    public function calculateStudentAverage(string $enrollmentId): ?float
    {
        $grades = Grade::where('enrollment_id', $enrollmentId)
            ->whereHas('gradeColumn', function ($q) {
                $q->where('section_subject_teacher_id', $this->id);
            })
            ->with('gradeColumn')
            ->get();

        if ($grades->isEmpty()) {
            return null;
        }

        $totalWeight = $grades->sum(fn ($g) => $g->gradeColumn->weight);

        if ($totalWeight == 0) {
            return null;
        }

        $weightedSum = $grades->sum(fn ($g) => $g->value * $g->gradeColumn->weight);

        return round($weightedSum / $totalWeight, 2);
    }
}
