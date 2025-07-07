<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    protected $fillable = [
        'grade',
        'teacher_subject_section_id',
        'enrollment_id'
    ];

    //Las notas pertenecen a una inscripcion
    public function enrollments()
    {
        return $this->belongsTo(Enrollment::class);
    }

    //Las notas pertenecen a teacher_subject_section porque esa relacion define el docente-materia-seccion que estan involucrados
    public function teacherSubjectSection()
    {
        return $this->belongsTo(TeacherSubjectSection::class);
    }
}
