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
            $table->id(); // Identificador único autoincremental de la Calificación
            // Clave foránea a la Asignación a la que corresponde esta Calificación
            $table->foreignId('subject_id')->constrained('subjects')
            ->onDelete('restrict'); // Impide borrar una Asignación si tiene Calificaciones asociadas

            // Clave foránea a la Inscripción a la que pertenece esta Calificación
            $table->foreignId('enrollment_id')->constrained('enrollments')
            ->onDelete('cascade'); // Si la inscripción se elimina, sus notas se eliminan en cascada

            // Nombre/tipo de la evaluación (Ej: 'Examen Parcial 1', 'Tarea 3')
            $table->string('grade_type', 100);
            // Valor de la Calificación (Ej: 9.50, 85.00), hasta 3 dígitos antes del decimal y 2 después
            $table->decimal('score', 5, 2);
            $table->date('grade_date'); // Fecha en que se realizó o corresponde la evaluación
            $table->text('comments')->nullable(); // Comentarios adicionales sobre la Calificación (opcional)
            $table->timestamps(); // Columnas created_at y updated_at para auditoría
            // Restricción de unicidad: Un solo tipo de Calificación por Asignación para una misma Inscripción
            $table->unique(['enrollment_id', 'subject_id', 'grade_type']);
            
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
