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
            $table->id(); // Clave primaria

            // Referencia a la matrícula del estudiante
            $table->unsignedBigInteger('enrollment_id');

            // Referencia a la asignación docente-materia-sección
            $table->unsignedBigInteger('teacher_subject_section_id');

            // Nota obtenida, máximo 99.99
            $table->float('grade', 4, 2);

            $table->timestamps(); // created_at y updated_at

            // Relaciones
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('restrict');
            $table->foreign('teacher_subject_section_id')->references('id')->on('teacher_subject_section')->onDelete('restrict');

            // Índice único para evitar duplicados
            $table->unique(['enrollment_id', 'teacher_subject_section_id', 'date_recorded'], 'unique_grade_record');
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
