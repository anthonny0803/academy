<?php

namespace App\Models;

use App\Traits\Activatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Student extends Model
{
    use HasFactory, Activatable;

    protected $fillable = [
        'user_id',
        'representative_id',
        'student_code',
        'document_id',
        'relationship_type',
        'birth_date',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'birth_date' => 'date',
        ];
    }

    public function setBirthDateAttribute($value)
    {
        $this->attributes['birth_date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    /**
     * Definitions of relationships with other models:
     */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function grades()
    {
        return $this->hasManyThrough(
            Grade::class,
            Enrollment::class,
            'student_id',
            'enrollment_id',
            'id',
            'id'
        );
    }
}
