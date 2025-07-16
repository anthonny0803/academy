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
        Schema::create('sections', function (Blueprint $table) {
            $table->id(); // clave primaria autoincremental
            $table->string('name', 50)->unique(); // nombre único para la sección
            $table->string('description')->nullable(); // descripción opcional para detalles extra
            $table->boolean('is_active')->default(true); // controla si la sección está activa o no
            $table->timestamps(); // created_at y updated_at para auditoría

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
