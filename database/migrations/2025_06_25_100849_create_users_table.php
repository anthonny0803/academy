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
            $table->string('username', 30)->unique();  // Nombre de usuario único, usado para login
            $table->string('password', 255);  // Contraseña hasheada (bcrypt, Argon2, etc)
            $table->unsignedBigInteger('role_id');  // FK a tabla roles (un usuario tiene un solo rol)
            $table->boolean('status')->default(true);  // Estado activo/inactivo, por defecto activo
            $table->timestamp('last_login')->nullable();  // Último login, nullable
            $table->timestamps();  // created_at y updated_at

            // Llave foránea con restricción para mantener integridad
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');
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
