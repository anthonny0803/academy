<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_subject_teacher_id')
                  ->constrained('section_subject_teacher')
                  ->onDelete('cascade');
            $table->string('name', 100);
            $table->decimal('weight', 5, 2);  // Porcentaje (ej: 25.00)
            $table->tinyInteger('display_order')->unsigned()->default(0);
            $table->text('observation')->nullable();
            $table->timestamps();

            // Nombre único por asignación (evita "Parcial 1" duplicado)
            $table->unique(
                ['section_subject_teacher_id', 'name'],
                'grade_columns_sst_name_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_columns');
    }
};