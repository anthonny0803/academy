<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INDEX_NAME = 'grades_enrollment_column_unique';

    /**
     * Grades are soft deleted, so the row survives the deletion and a total
     * unique index kept owning the cell forever: re-grading answered 422
     * through the individual store and 500 through the batch. The rule the
     * domain actually needs is "one LIVE grade per enrollment and column",
     * and only a partial index can express it. Raw SQL because the schema
     * builder does not support partial (WHERE) indexes.
     */
    public function up(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropUnique(self::INDEX_NAME);
        });

        DB::statement(
            'CREATE UNIQUE INDEX '.self::INDEX_NAME.'
             ON grades (enrollment_id, grade_column_id)
             WHERE deleted_at IS NULL'
        );
    }

    /**
     * Schema-only reversal: it fails if any cell already holds a deleted grade
     * plus a live one, which is exactly right — restoring the total index
     * requires restoring the data model that justified it.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS '.self::INDEX_NAME);

        Schema::table('grades', function (Blueprint $table) {
            $table->unique(['enrollment_id', 'grade_column_id'], self::INDEX_NAME);
        });
    }
};
