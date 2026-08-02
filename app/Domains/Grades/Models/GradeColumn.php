<?php

namespace App\Domains\Grades\Models;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Shared\Contracts\HasEntityName;
use App\Domains\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GradeColumn extends Model implements HasEntityName
{
    use BelongsToTenant;
    use HasFactory;
    use HasUuids;

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

    /**
     * Every grade the column ever owned, deleted ones included: what
     * `grades.grade_column_id` restricts, and what `hasGrades` answers about.
     */
    public function gradeHistory(): HasMany
    {
        return $this->hasMany(Grade::class)->withTrashed();
    }

    // Query Scopes

    public function scopeForAssignment($query, string $sstId)
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
        if (array_key_exists('grade_history_count', $this->attributes)) {
            return $this->attributes['grade_history_count'] > 0;
        }

        return $this->gradeHistory()->exists();
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
