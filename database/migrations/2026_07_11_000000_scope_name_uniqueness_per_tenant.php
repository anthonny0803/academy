<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Names must be unique per tenant, not globally: the global unique on
     * subjects.name blocked legitimate reuse across tenants, while
     * academic_periods.name had no database guarantee at all.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->unique(['tenant_id', 'name']);
        });

        Schema::table('academic_periods', function (Blueprint $table) {
            $table->unique(['tenant_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::table('academic_periods', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'name']);
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'name']);
            $table->unique('name');
        });
    }
};
