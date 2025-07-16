<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'birth_date',
        'document',
        'address',
        'phone',
        'relationship',
        'father_last_name',
        'mother_name',
        'mother_last_name',
        'representative_id',
        'section_id'
    ];

    //El estudiante solo tiene un representante
    public function representative()
    {
        return $this->belongsTo(Representative::class);
    }

    //El estudiante esta en una seccion
    public function section(){
        return $this->belongsTo(Section::class);
    }

    protected $casts = [
        'birth_date' => 'date',
    ];
}
