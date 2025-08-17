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
        Schema::create('section_subject_teacher', function (Blueprint $table) {
            // Clave foránea a la Sección
            $table->foreignId('section_id')->constrained('sections')
            ->onDelete('restrict'); // Impide borrar una Sección si esta asignada a alguna Asignación

            // Clave foránea a la Asignación
            $table->foreignId('subject_id')->constrained('subjects')
            ->onDelete('restrict'); // Impide borrar una Asignación si está asignada a alguna Sección

            // Clave foránea al Profesor asignado a esta Asignación en esta Sección
            $table->foreignId('teacher_id')->constrained('teachers')
            ->onDelete('restrict'); // Impide borrar un profesor si está asignado a esta Asignación en esta Sección
            
            // Fecha de inicio de la responsabilidad del Profesor en esta Asignación/Sección
            $table->date('assigned_at');

            // Fecha de fin de la responsabilidad (Nulo si aún está activo)
            $table->date('unassigned_at')->nullable();
            $table->timestamps(); // created_at y updated_at para auditoría

            // Esto asegura que la combinación de estas tres columnas sea única e identifique cada registro.
            $table->primary(['section_id', 'subject_id', 'teacher_id'], 'section_subject_teacher_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_subject_teacher');
    }
};
