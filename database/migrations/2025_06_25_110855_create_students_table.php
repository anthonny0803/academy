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
        Schema::create('students', function (Blueprint $table) {
            $table->id();  // PK única para cada estudiante

            $table->unsignedBigInteger('representative_id')->nullable(); // Representante legal, puede no tenerlo aún

            $table->string('name', 30);      // Nombre del estudiante
            $table->string('last_name', 30); // Apellido del estudiante
            $table->date('birth_date');      // Fecha de nacimiento
            $table->string('email', 30)->nullable();   // Email opcional
            $table->string('document', 15)->unique();  // Documento único
            $table->string('address', 255);  // Dirección
            $table->string('phone', 15);     // Teléfono
            $table->string('father_name', 30)->nullable();      // Nombre padre opcional
            $table->string('father_last_name', 30)->nullable(); // Apellido padre opcional
            $table->string('mother_name', 30)->nullable();      // Nombre madre opcional
            $table->string('mother_last_name', 30)->nullable(); // Apellido madre opcional
            $table->unsignedBigInteger('section_id');
            $table->timestamps();     // created_at, updated_at
            $table->timestamp('last_login')->nullable(); // Último acceso
            $table->foreign('representative_id')->references('id')->on('representatives')->onDelete('restrict'); // No borrar representante si tiene estudiantes
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
