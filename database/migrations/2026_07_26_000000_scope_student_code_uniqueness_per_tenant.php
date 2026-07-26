<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Student codes must be unique per tenant, not globally: the sequence is
     * generated within the tenant scope, so the global unique on
     * students.student_code made every tenant but the first collide on its
     * first student. Mirrors the same fix already applied to subjects and
     * academic_periods.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['student_code']);
            $table->unique(['tenant_id', 'student_code']);
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'student_code']);
            $table->unique('student_code');
        });
    }
};
