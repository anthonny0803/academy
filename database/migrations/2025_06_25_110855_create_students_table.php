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
            $table->integer('id_representatives')->nullable();
            $table->string('name', 30);
            $table->string('last_name', 30);
            $table->date('birth_date');
            $table->string('email', 30)->nullable();
            $table->string('school_year', 30);
            $table->string('document', 15)->unique();
            $table->boolean('status');
            $table->string('address', 255);
            $table->string('phone', 15);
            $table->float('math_grades', 3)->nullable();
            $table->float('science_grades', 3)->nullable();
            $table->float('spanish_grades', 3)->nullable();
            $table->float('english_grades', 3)->nullable();
            $table->float('history_grades', 3)->nullable();
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
