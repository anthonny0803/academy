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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Identificador único autoincremental del Usuario
            $table->string('name', 100); // Primer y segundo nombre del Usuario
            $table->string('last_name', 100); // Primer y segundo apellido del Usuario
            $table->string('document_id', 20)->nullable()->unique(); // Dni del Usuario (Para inicio de sesión público)
            $table->string('username', 20)->nullable()->unique(); // Alias del Usuario (Para inicio de sesión de empleado)
            $table->string('email', 100)->nullable()->unique(); // Correo del Usuario
            $table->string('password')->nullable(); // Contraseña del Usuario (Para inicio de sesión de empleado)
            $table->string('sex'); // Sexo del Usuario para auditoría
            $table->date('birth_date'); // Fecha de nacimiento del Usuario
            $table->boolean('is_active')->default(true); // Estado del Usuario
            $table->timestamps(); // Columnas created_at y updated_at para auditoría
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
