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
        Schema::create('section_subject_teacher', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->nullable()->index(); // Tenant propietario (scope inactivo hasta Fase 4)

            $table->foreignUuid('section_id')->constrained('sections')->onDelete('restrict');
            $table->foreignUuid('subject_id')->constrained('subjects')->onDelete('restrict');
            $table->foreignUuid('teacher_id')->constrained('teachers')->onDelete('restrict');

            $table->boolean('is_primary')->default(true);
            $table->enum('status', ['activo', 'inactivo', 'suplente'])->default('activo');

            $table->timestamps();

            $table->unique(['section_id', 'subject_id', 'teacher_id'], 'sst_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_subject_teacher');
    }
};
