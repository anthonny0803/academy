<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')
                  ->constrained('enrollments')
                  ->onDelete('cascade');
            $table->foreignId('grade_column_id')
                  ->constrained('grade_columns')
                  ->onDelete('restrict');  // No eliminar columna con notas
            $table->decimal('value', 5, 2);
            $table->text('observation')->nullable();
            $table->foreignId('last_modified_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Un estudiante, una nota por columna
            $table->unique(
                ['enrollment_id', 'grade_column_id'],
                'grades_enrollment_column_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};