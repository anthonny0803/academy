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
            $table->id();
            $table->unsignedBigInteger('representative_id')->nullable(); // Se asocia con un representante
            $table->string('name', 30);
            $table->string('last_name', 30);
            $table->date('birth_date');
            $table->string('email', 30)->nullable();
            $table->string('school_year', 30);
            $table->string('document', 15)->unique(); // Documento como identificador de acceso
            $table->boolean('status')->default(true);
            $table->string('address', 255);
            $table->string('phone', 15);
            $table->string('father_name', 30)->nullable();
            $table->string('father_last_name', 30)->nullable();
            $table->string('mother_name', 30)->nullable();
            $table->string('mother_last_name', 30)->nullable();
            $table->timestamps();
            $table->timestamp('last_login')->nullable();
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
