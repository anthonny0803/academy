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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id(); // Clave primaria única autoincremental
            $table->string('name', 50)->unique(); // Nombre de la materia (único para evitar duplicados)
            $table->string('code', 10)->unique(); // Código corto identificativo (ej: MATH101)
            $table->text('description')->nullable(); // Descripción opcional de la materia
            $table->boolean('active')->default(true); // Si la materia está activa o inactiva (para no borrar)
            $table->timestamps(); // created_at y updated_at automáticos
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
