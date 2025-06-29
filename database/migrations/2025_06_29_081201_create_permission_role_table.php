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
        Schema::create('permission_role', function (Blueprint $table) {
            $table->id(); // Clave primaria (opcional, pero buena práctica para trazabilidad)
            $table->unsignedBigInteger('permission_id'); // ID del permiso
            $table->unsignedBigInteger('role_id');       // ID del rol
            // Claves foráneas para asegurar integridad referencial
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade'); // Si se borra el permiso, se borra la relación
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade'); // Si se borra el rol, también se borra la relación
            // Garantiza que no se duplique una relación entre el mismo rol y permiso
            $table->unique(['permission_id', 'role_id']);
            $table->timestamps(); // Para saber cuándo se creó o actualizó la relación
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
