<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SectionSubjectTeacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'subject_id',
        'teacher_id',
        'assigned_at',
        'unassigned_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'date',
            'unassigned_at' => 'date',
        ];
    }

    /**
     * Definitions of relationships with other models:
     */

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
