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
            $table->uuid('id')->primary(); // Identificador único (UUID v4) de la Asignatura
            $table->foreignUuid('tenant_id')->nullable()->index(); // Tenant propietario (scope inactivo hasta Fase 4)
            $table->string('name', 100)->unique(); // Nombre de la Asignatura (Ej: Matemática 1, Física 3)
            $table->string('description'); // Descripción de la Asignatura
            $table->boolean('is_active')->default(true); // Estado de la Asignatura (activa/inactiva)
            $table->timestamps(); // Columnas created_at y updated_at para auditoría
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
