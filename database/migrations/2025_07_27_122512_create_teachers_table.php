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
        Schema::create('teachers', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Identificador único (UUID v4) del Profesor
            $table->foreignUuid('tenant_id')->nullable()->index(); // Tenant propietario (scope inactivo hasta Fase 4)
            // Clave foránea al Usuario asociado a este perfil de Profesor
            // (único: ver enforce_one_profile_per_user)
            $table->foreignUuid('user_id')->constrained('users');
            // Estado del Usuario en su rol de Profesor (activo por defecto)
            $table->boolean('is_active')->default(true);
            $table->timestamps(); // Columnas created_at y updated_at para auditoría
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
