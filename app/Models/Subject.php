<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
        'description'
    ];

    public function sections()
    {
        return $this->belongsToMany(Section::class);
    }
}