<?php

namespace App\Models;

use App\Contracts\HasEntityName;
use App\Traits\Activatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model implements HasEntityName
{
    use Activatable;
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Contracts Implementation

    public function getEntityName(): string
    {
        return 'Profesor';
    }

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher')
            ->withTimestamps();
    }

    public function sectionSubjectTeachers(): HasMany
    {
        return $this->hasMany(SectionSubjectTeacher::class);
    }

    public function sections(): BelongsToMany
    {
        return $this->belongsToMany(Section::class, 'section_subject_teacher')
            ->withPivot('subject_id', 'is_primary', 'status')
            ->withTimestamps();
    }

    // Query Scopes

    public function scopeSearch($query, string $term)
    {
        $upperTerm = strtoupper($term);
        $lowerTerm = strtolower($term);

        return $query->whereHas('user', function ($q) use ($upperTerm, $lowerTerm) {
            $q->where('name', 'like', "%{$upperTerm}%")
                ->orWhere('last_name', 'like', "%{$upperTerm}%")
                ->orWhere('email', 'like', "%{$lowerTerm}%");
        });
    }

    public function scopeWithUser($query)
    {
        return $query->with('user');
    }

    public function scopeOrderByUserName($query)
    {
        return $query->join('users', 'teachers.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->orderBy('users.last_name')
            ->select('teachers.*');
    }

    // Accessors

    public function getFullNameAttribute(): string
    {
        return $this->user?->full_name ?? '';
    }

    public function getEmailAttribute(): ?string
    {
        return $this->user?->email ?? null;
    }

    public function getNameAttribute(): string
    {
        return $this->user?->name ?? '';
    }

    public function getLastNameAttribute(): string
    {
        return $this->user?->last_name ?? '';
    }

    public function getSexAttribute(): ?string
    {
        return $this->user?->sex ?? null;
    }

    // Helper Methods

    public function hasSubject(int $subjectId): bool
    {
        return $this->sectionSubjectTeachers()
            ->where('subject_id', $subjectId)
            ->exists();
    }

    public function getSubjectTeacher(int $subjectId): ?SectionSubjectTeacher
    {
        return $this->sectionSubjectTeachers()
            ->where('subject_id', $subjectId)
            ->primary()
            ->active()
            ->first();
    }
}
