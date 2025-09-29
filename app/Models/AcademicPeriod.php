<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademicPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'notes',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Definitions of relationships with other models:
     */

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function isCurrent()
    {
        $now = now();
        return $this->is_active && $now->isBetween($this->start_date, $this->end_date, true);
    }
}
