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
            $table->id(); // clave primaria
            $table->string('name', 50)->unique(); // nombre del permiso: crear_docentes, ver_notas, etc.
            $table->string('description', 255)->nullable(); // descripción opcional del permiso
            $table->timestamps(); // para saber cuándo fue creado/modificado
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
