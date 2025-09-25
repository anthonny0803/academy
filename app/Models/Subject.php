<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('description', 'like', "%{$term}%");
    }

    /**
     * Definitions of relationships with other models:
     */

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'subject_teacher');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function sectionSubjectTeachers()
    {
        return $this->hasMany(SectionSubjectTeacher::class);
    }
}
