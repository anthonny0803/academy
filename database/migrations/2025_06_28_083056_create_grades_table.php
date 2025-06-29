<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();  // Clave primaria autoincremental para cada registro de nota
            $table->unsignedBigInteger('student_id');  // Relaciona la nota con el estudiante (tabla students)
            $table->unsignedBigInteger('subject_id');  // Relaciona la nota con la materia o asignatura (tabla subjects)
            $table->unsignedBigInteger('teacher_id')->nullable();  // Opcional: profesor que asignó la nota (tabla users, puede ser null si no se sabe)
            $table->float('grade', 4, 2);  // Nota con máximo 2 decimales, ej. 95.50. Tamaño total 4 (incluye decimales)
            $table->date('date_recorded');  // Fecha cuando se registró la nota
            $table->timestamps();  // created_at y updated_at para auditoría
            // Integridad referencial
            $table->foreign('student_id')->references('id')->on('students')->onDelete('restrict');  // No se puede borrar estudiante si tiene notas
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('restrict');  // Igual para materia
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('restrict');  // Para el docente asignador, restricción para no borrar docente con notas


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
