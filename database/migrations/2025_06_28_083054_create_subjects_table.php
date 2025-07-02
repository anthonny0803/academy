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
            $table->id(); // clave primaria autoincremental
            $table->string('name', 50)->unique(); // nombre único de la materia
            $table->string('code', 10)->unique(); // código único para identificar la materia (ej: MAT101)
            $table->text('description')->nullable(); // descripción opcional para más contexto
            $table->boolean('is_active')->default(true); // para activar o desactivar la materia sin borrar
            $table->timestamps(); // created_at y updated_at para auditoría
            $table->softDeletes(); // para poder "eliminar" sin perder datos (borrado lógico)

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
