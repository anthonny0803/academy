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
            $table->id(); // Identificador único autoincremental del Estudiante
            // Código del Estudiante, Ej: ADULT001, CHILD001 (único)
            $table->string('student_code')->unique();
            // Dni del Usuario (Para inicio de sesión público)
            $table->string('document_id', 20)->nullable()->unique();
            // Clave foránea al Usuario asociado a este perfil de Estudiante (único)
            $table->foreignId('user_id')->constrained('users')->unique();
            // Clave foránea al Representante legal/académico del Estudiante (obligatorio)
            $table->foreignId('representative_id')
                ->constrained('representatives')
                ->onDelete('restrict'); // Impide borrar al Representante si tiene un Estudiante asociado

            // Tipo de relación con el representante (Ej: Padre, Madre, Tutor Legal, Auto-representante)
            $table->string('relationship_type', 30);
            // Fecha de nacimiento del Usuario
            $table->date('birth_date');
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
