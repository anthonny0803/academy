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
            $table->id();  // Clave primaria autoincremental, única para cada inscripción

            $table->unsignedBigInteger('student_id');  // ID del estudiante inscrito

            $table->unsignedBigInteger('section_id');  // ID de la sección (curso/grupo) en la que se inscribe

            $table->unsignedBigInteger('academic_period_id'); //ID del periodo academico en la que se inscribe

            $table->enum('status', ['active', 'withdrawn', 'completed'])->default('active');
            // Estado de la inscripción: activa, retirada o completada

            $table->timestamps();  // created_at y updated_at para auditoría

            // Integridad referencial para evitar inconsistencias
            $table->foreign('student_id')->references('id')->on('students')->onDelete('restrict');
            // No se puede borrar estudiante si tiene inscripciones activas

            $table->foreign('section_id')->references('id')->on('sections')->onDelete('restrict');
            // No se puede borrar sección si tiene inscripciones activas

            $table->foreign('academic_period_id')->references('id')->on('academic_periods')->onDelete('restrict');
            // No se puede borrar sección si tiene inscripciones activas

            // Para evitar que el mismo estudiante se inscriba dos veces en la misma sección y año
            $table->unique(['student_id', 'section_id', 'academic_period_id']);
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
