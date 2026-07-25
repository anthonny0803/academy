<?php

namespace App\Domains\Grades\Models;

use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Identity\Models\User;
use App\Domains\Shared\Contracts\HasEntityName;
use App\Domains\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model implements HasEntityName
{
    use BelongsToTenant;
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

    public function scopeForAssignment($query, string $sstId)
    {
        return $query->whereHas('gradeColumn', function ($q) use ($sstId) {
            $q->where('section_subject_teacher_id', $sstId);
        });
    }

    // Helper Methods

    public function getColumnName(): string
    {
        return $this->gradeColumn->name;
    }

    public function getWeight(): float
    {
        return (float) $this->gradeColumn->weight;
    }

    // Mutators

    protected function setObservationAttribute($value): void
    {
        $this->attributes['observation'] = $value ? strtoupper(trim($value)) : null;
    }
}
