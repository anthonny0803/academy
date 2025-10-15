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
            $table->string('email', 100)->nullable()->unique(); // Correo del Usuario (Para inicio de sesión de empleado)
            $table->string('password')->nullable(); // Contraseña del Usuario (Para inicio de sesión de empleado)
            $table->string('sex'); // Sexo del Usuario para auditoría
            $table->string('document_id', 20)->nullable()->unique();
            $table->date('birth_date')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('occupation', 100)->nullable();
            $table->boolean('is_active')->default(true); // Estado del Usuario
            $table->boolean('is_developer')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->integer('last_activity')->index();
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
