<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'name',
        'is_active',
        'description'
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }
}
