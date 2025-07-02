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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id(); // clave primaria autoincremental
            $table->string('name', 50)->unique(); // nombre del permiso, ej: crear_docentes, ver_notas
            $table->string('description', 255)->nullable(); // descripción opcional para entender mejor
            $table->timestamps(); // created_at y updated_at para control de cambios

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};
