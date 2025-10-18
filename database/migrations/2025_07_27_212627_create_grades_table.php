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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->foreignId('section_subject_teacher_id')
                  ->constrained('section_subject_teacher')
                  ->onDelete('restrict');
            
            $table->string('grade_type', 100);
            $table->decimal('grade', 5, 2);
            $table->text('observation')->nullable();
            
            $table->timestamps();
            
            $table->unique(['enrollment_id', 'section_subject_teacher_id', 'grade_type'], 'grades_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
