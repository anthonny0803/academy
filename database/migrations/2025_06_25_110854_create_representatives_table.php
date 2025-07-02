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
        Schema::create('representatives', function (Blueprint $table) {
            $table->id();  // Clave primaria única para cada representante

            $table->string('name', 30);       // Nombre del representante
            $table->string('last_name', 30);  // Apellido del representante

            $table->string('dni', 15)->unique(); // Documento de identidad único (DNI, cédula, etc.)

            $table->string('phone', 15);       // Teléfono de contacto

            $table->string('email', 50)->nullable(); // Email opcional

            $table->string('address', 255);    // Dirección física

            $table->string('relationship', 20)->nullable(); // Relación con el estudiante (ej. padre, madre, tutor)

            $table->timestamps();              // created_at y updated_at

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('representatives');
    }
};
