<?php

namespace App\Enums;

enum Role: string
{
    case Supervisor = 'Supervisor';
    case Admin = 'Administrador';
    case Teacher = 'Profesor';
    case Representative = 'Representante';
    case Student = 'Estudiante';
}
