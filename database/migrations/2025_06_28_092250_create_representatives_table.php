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
            $table->id();
            $table->string('name', 30);
            $table->string('last_name', 30);
            $table->string('dni', 15)->unique(); // Documento de identidad único
            $table->string('phone', 15);
            $table->string('email', 50)->nullable(); // En caso de que usen email
            $table->string('address', 255);
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
