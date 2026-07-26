<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables holding a profile that belongs to exactly one user.
     *
     * @var list<string>
     */
    private const PROFILE_TABLES = [
        'teachers',
        'representatives',
        'students',
    ];

    /**
     * A user holds at most one profile of each type, which is what the HasOne
     * relations on User already assume. The create migrations chained
     * ->unique() onto ->constrained(): that lands on the foreign key
     * definition instead of the column, so no index was ever created and the
     * only guard left was a check-then-act exists() in the services.
     *
     * The unique is global, not per tenant: users.tenant_id is NOT NULL and
     * membership is 1-to-1, so the user is already confined to one tenant. It
     * doubles as the lookup index the foreign key never got, since Postgres
     * only indexes primary keys and unique constraints on its own.
     */
    public function up(): void
    {
        foreach (self::PROFILE_TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->unique('user_id');
            });
        }
    }

    public function down(): void
    {
        foreach (self::PROFILE_TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropUnique(['user_id']);
            });
        }
    }
};
