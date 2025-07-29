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
            $table->id(); // Identificador único autoincremental de la Sección
            // Clave foránea al Período Académico al que pertenece esta Sección
            $table->foreignId('academic_period_id')->constrained('academic_periods')
            ->onDelete('restrict'); // Impide borrar un período si tiene secciones asociadas

            // Nombre de la Sección (Ej: "1ro A", "5to D") "también implica el nivel/grado" (único)
            $table->string('name', 50)->unique();
            $table->string('description')->nullable(); // Descripción de la sección (opcional)
            $table->integer('capacity')->nullable(); // Capacidad de la sección (opcional)
            // Estado de la Sección (Ej: Activa = Inscripción abierta) (activa por defecto)
            $table->boolean('is_active')->default(true);
            $table->timestamps(); // Columnas created_at y updated_at para auditoría
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
