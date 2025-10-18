<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SectionSubjectTeacher extends Model
{
    use HasFactory;

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

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    // Query Scopes

    public function scopeActive($query)
    {
        return $query->where('status', 'activo');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeForTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForSection($query, int $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    // Helper Methods

    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    public function isActive(): bool
    {
        return $this->status === 'activo';
    }

    public function getStudentCount(): int
    {
        return Enrollment::where('section_id', $this->section_id)
            ->where('status', 'activo')
            ->count();
    }
}