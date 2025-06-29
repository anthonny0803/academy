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
            $table->id();  // ID autoincremental como clave primaria
            $table->string('username', 30)->unique();  // Nombre de usuario único, usado para iniciar sesión
            $table->string('password', 255);  // Contraseña encriptada (recomendado 255 para hashes como bcrypt o Argon2)
            $table->unsignedBigInteger('role_id');  // Clave foránea hacia la tabla 'roles' (un usuario solo puede tener un rol)
            $table->boolean('status')->default(true);  // Estado del usuario (activo/inactivo) — true por defecto
            $table->timestamp('last_login')->nullable();  // Fecha y hora del último inicio de sesión
            $table->timestamps();  // Fechas de creación y actualización del registro (created_at y updated_at)
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');  // Definimos la clave foránea que apunta a la tabla 'roles'
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
