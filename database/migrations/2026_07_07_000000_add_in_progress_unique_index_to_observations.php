<?php

use App\Domains\AI\Enums\ObservationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hard guarantee: at most one in-flight generation per student. An application-level
        // check has an inherent race; this partial index is the real serialization point.
        // Raw SQL because the schema builder does not support partial (WHERE) indexes.
        $inFlightStatuses = collect([ObservationStatus::Pending, ObservationStatus::Processing])
            ->map(fn (ObservationStatus $status) => "'{$status->value}'")
            ->implode(', ');

        DB::statement(
            "CREATE UNIQUE INDEX student_perf_obs_in_progress_unique
             ON student_performance_observations (student_id)
             WHERE status IN ({$inFlightStatuses})"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS student_perf_obs_in_progress_unique');
    }
};
