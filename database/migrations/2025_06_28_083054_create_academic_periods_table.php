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
        Schema::create('academic_periods', function (Blueprint $table) {
            $table->id();  // Clave primaria autoincremental para cada ciclo académico único

            $table->string('name', 20)->unique();
            // Nombre descriptivo del ciclo, por ejemplo "2024-2025" o "2025"

            $table->date('start_date');  // Fecha de inicio del ciclo académico

            $table->date('end_date');    // Fecha de finalización del ciclo académico

            $table->boolean('is_active')->default(false);
            // Marca si este ciclo es el activo actualmente (solo uno debería estar activo)

            $table->timestamps();  // created_at y updated_at para seguimiento


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_periods');
    }
};
