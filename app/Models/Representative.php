<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Representative extends Model
{
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'birth_date',
        'document',
        'address',
        'phone',
        'relationship'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    //El representante tiene o puede tener muchos estudiantes
    public function students() {
    return $this->hasMany(Student::class);
}
}
