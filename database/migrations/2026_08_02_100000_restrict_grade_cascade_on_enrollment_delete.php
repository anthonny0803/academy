<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Grades are academic history and no maintenance operation destroys them. The
 * cascade on `enrollment_id` did exactly that, silently and soft-deleted rows
 * included, so the guard against it lived only in application code. Restrict
 * makes the database the backstop, the way `grade_column_id` already is.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
            $table->foreign('enrollment_id')
                ->references('id')
                ->on('enrollments')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
            $table->foreign('enrollment_id')
                ->references('id')
                ->on('enrollments')
                ->onDelete('cascade');
        });
    }
};
