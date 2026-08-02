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

    private const WEIGHT_PRECISION = 2;

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

    /**
     * Every grade under the assignment, deleted ones included. Its columns
     * cascade with it and `grades.grade_column_id` restricts them, so the
     * history is what decides whether the assignment can be deleted.
     */
    public function gradeHistory(): HasManyThrough
    {
        return $this->grades()->withTrashed();
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

    /**
     * Serialization point for the 100% weighting: a new grade column has no
     * row of its own to lock, so concurrent writers queue on the assignment.
     * Must run inside an active DB transaction, and the instance it returns
     * carries no loaded relations, so getTotalWeight() reads under the lock.
     */
    public function scopeLockedById($query, string $id)
    {
        return $query->whereKey($id)->lockForUpdate();
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

    public function hasGrades(): bool
    {
        return $this->gradeHistory()->exists();
    }

    // Helper Methods - Configuración de Columnas

    public function getTotalWeight(): float
    {
        $total = $this->relationLoaded('gradeColumns')
            ? $this->gradeColumns->sum('weight')
            : $this->gradeColumns()->sum('weight');

        return round((float) $total, self::WEIGHT_PRECISION);
    }

    public function isConfigurationComplete(): bool
    {
        return $this->getTotalWeight() === 100.0;
    }

    public function canAddColumn(float $weight): bool
    {
        return ($this->getTotalWeight() + $weight) <= 100;
    }

    public function getRemainingWeight(): float
    {
        return 100 - $this->getTotalWeight();
    }
}
