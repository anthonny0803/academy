<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'enrollment_id',
        'grade_type',
        'grade_date',
        'score',
        'comments',
    ];

    protected function casts(): array
    {
        return [
            'grade_date' => 'date',
            'score' => 'decimal:2',
        ];
    }

    /**
     * Definitions of relationships with other models:
     */

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }
}
