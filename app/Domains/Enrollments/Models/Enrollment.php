<?php

namespace App\Domains\Enrollments\Models;

use App\Domains\Academics\Models\Section;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Grades\Models\Grade;
use App\Domains\Shared\Contracts\HasEntityName;
use App\Domains\Students\Models\Student;
use App\Domains\Tenancy\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model implements HasEntityName
{
    use BelongsToTenant;
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'student_id',
        'section_id',
        'status',
        'passed',
    ];

    protected $casts = [
        'status' => 'string',
        'passed' => 'boolean',
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

    /**
     * Every grade the enrollment ever owned, deleted ones included. The
     * `grades.enrollment_id` cascade destroys them all, so the history is what
     * decides whether the enrollment can be deleted.
     */
    public function gradeHistory(): HasMany
    {
        return $this->hasMany(Grade::class)->withTrashed();
    }

    // Query Scopes

    // Diverges from User::scopeSearch on purpose: narrower set (no email) plus the enrolled student_code.
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = strtoupper($term);

        return $query->where(function ($q) use ($term) {
            $q->whereHas('student.user', function ($userQuery) use ($term) {
                $userQuery->where('name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%");
            })->orWhereHas('student', function ($studentQuery) use ($term) {
                $studentQuery->where('student_code', 'like', "%{$term}%");
            });
        });
    }

    public function scopeForStudent(Builder $query, string $studentId): Builder
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForSection(Builder $query, string $sectionId): Builder
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

    // Helper Methods - Estado

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

    public function isTransferred(): bool
    {
        return $this->status === EnrollmentStatus::Transferred->value;
    }

    public function isPromoted(): bool
    {
        return $this->status === EnrollmentStatus::Promoted->value;
    }

    public function hasPassed(): ?bool
    {
        return $this->passed;
    }

    public function hasGrades(): bool
    {
        return $this->gradeHistory()->exists();
    }
}
