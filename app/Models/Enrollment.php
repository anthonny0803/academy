<?php

namespace App\Models;

use App\Contracts\HasEntityName;
use App\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model implements HasEntityName
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'section_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Contracts Implementation

    public function getEntityName(): string
    {
        return 'Inscripción';
    }

    // Relationships

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    // Query Scopes

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->whereHas('student.user', function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%");
        })->orWhereHas('student', function ($q) use ($term) {
            $q->where('student_code', 'like', "%{$term}%");
        });
    }

    public function scopeForStudent(Builder $query, int $studentId): Builder
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForSection(Builder $query, int $sectionId): Builder
    {
        return $query->where('section_id', $sectionId);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', EnrollmentStatus::Active->value);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', EnrollmentStatus::Completed->value);
    }

    public function scopeWithdrawn(Builder $query): Builder
    {
        return $query->where('status', EnrollmentStatus::Withdrawn->value);
    }

    // Helper Methods

    public function isActive(): bool
    {
        return $this->status === EnrollmentStatus::Active->value;
    }

    public function isCompleted(): bool
    {
        return $this->status === EnrollmentStatus::Completed->value;
    }

    public function isWithdrawn(): bool
    {
        return $this->status === EnrollmentStatus::Withdrawn->value;
    }
}
