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
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Identificador único (UUID v4) del Estudiante
            $table->foreignUuid('tenant_id')->nullable()->index(); // Tenant propietario (scope inactivo hasta Fase 4)
            // Código del Estudiante, Ej: ADULT001, CHILD001
            // (único por tenant: ver scope_student_code_uniqueness_per_tenant)
            $table->string('student_code')->unique();
            // Clave foránea al Usuario asociado a este perfil de Estudiante
            // (único: ver enforce_one_profile_per_user)
            $table->foreignUuid('user_id')->constrained('users');
            // Clave foránea al Representante legal/académico del Estudiante (obligatorio)
            $table->foreignUuid('representative_id')
                ->constrained('representatives')
                ->onDelete('restrict'); // Impide borrar al Representante si tiene un Estudiante asociado

            // Tipo de relación con el representante (Ej: Padre, Madre, Tutor Legal, Auto-representante)
            $table->string('relationship_type', 30);
            // Estado del Usuario en su rol de Estudiante (activo por defecto)
            $table->boolean('is_active')->default(true);
            $table->timestamps(); // Columnas created_at y updated_at para auditoría
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
