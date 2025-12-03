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
            $table->id(); // Identificador único autoincremental del Período Académico
            $table->string('name', 100); // Nombre del período (Ej: 'Ciclo Escolar 2024-2025')
            $table->string('notes')->nullable(); // Notas o comentarios adicionales sobre el período (opcional)
            $table->date('start_date'); // Fecha de inicio del Período Académico
            $table->date('end_date'); // Fecha fin del Período Académico
            // Estado que indica si el Período Académico esta activo (falso por defecto)
            $table->decimal('min_grade', 5, 2)->default(0)->after('end_date');
            $table->decimal('max_grade', 5, 2)->default(100)->after('min_grade');
            $table->decimal('passing_grade', 5, 2)->default(60)->after('max_grade');
            $table->boolean('is_active')->default(false);
            $table->boolean('is_promotable');
            $table->timestamps(); // Columnas created_at y updated_at para auditoría
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
