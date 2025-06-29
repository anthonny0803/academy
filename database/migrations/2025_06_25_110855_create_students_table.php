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
            $table->unsignedBigInteger('representative_id')->nullable();
            $table->unsignedBigInteger('section_id'); // La sección a la que pertenece el estudiante
            $table->string('name', 30);
            $table->string('last_name', 30);
            $table->date('birth_date');
            $table->string('email', 30)->nullable();
            $table->string('school_year', 30);
            $table->string('document', 15)->unique();
            $table->boolean('status')->default(true);
            $table->string('address', 255);
            $table->string('phone', 15);
            $table->string('father_name', 30)->nullable();
            $table->string('father_last_name', 30)->nullable();
            $table->string('mother_name', 30)->nullable();
            $table->string('mother_last_name', 30)->nullable();
            $table->timestamps();
            $table->timestamp('last_login')->nullable();
            $table->foreign('representative_id')->references('id')->on('representatives')->onDelete('restrict');
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
