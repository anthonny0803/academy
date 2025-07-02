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
        Schema::create('teacher_subject_section', function (Blueprint $table) {
            $table->id();

            // El docente (usuario con rol 'teacher')
            $table->unsignedBigInteger('user_id');

            // Materia que imparte
            $table->unsignedBigInteger('subject_id');

            // Sección en la que la imparte
            $table->unsignedBigInteger('section_id');

            $table->timestamps();

            // Relaciones
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('restrict');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('restrict');

            // No permitir que el mismo docente tenga la misma materia en la misma sección más de una vez
            $table->unique(['user_id', 'subject_id', 'section_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_subject_section');
    }
};
