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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id(); // Identificador único autoincremental de la Inscripción
            // Asegura que un Estudiante no pueda inscribirse más de una vez en la misma Sección
            $table->unique(['student_id', 'section_id'], 'unique_student_section');
            // Clave foránea al Estudiante que se inscribe
            $table->foreignId('student_id')->constrained('students')
                ->onDelete('restrict'); // Impide borrar un Estudiante si tiene Inscripciones

            // Clave foránea a la Sección en la que se inscribe el Estudiante
            $table->foreignId('section_id')->constrained('sections')
                ->onDelete('restrict'); // Impide borrar una Sección si tiene Inscripciones;
            $table->boolean('passed')->nullable();

            // Estado de la Inscripción del Estudiante (Ej: 'activo', 'completado', 'retirado')
            $table->string('status', 100);
            $table->timestamps(); // Columnas created_at y updated_at para auditoría
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
