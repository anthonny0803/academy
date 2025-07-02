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
            $table->id(); // clave primaria para trazabilidad (nunca sobra)
            $table->unsignedBigInteger('permission_id'); // FK a permisos
            $table->unsignedBigInteger('role_id');       // FK a roles

            // Integridad referencial para que no queden relaciones huérfanas
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');

            // Evita duplicados exactos de combinación permiso-rol
            $table->unique(['permission_id', 'role_id']);

            $table->timestamps(); // para auditar cambios en relaciones

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
