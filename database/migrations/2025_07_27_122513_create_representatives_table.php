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
        Schema::create('representatives', function (Blueprint $table) {
            $table->id(); // Identificador único autoincremental del Representante
            // Clave foránea al Usuario asociado a este perfil de Representante (único)
            $table->foreignId('user_id')->constrained('users')->unique();
            // Estado del Usuario en su rol de Representante (activo por defecto)
            $table->boolean('is_active')->default(true);
            $table->timestamps(); // Columnas created_at y updated_at para auditoría
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('representatives');
    }
};
