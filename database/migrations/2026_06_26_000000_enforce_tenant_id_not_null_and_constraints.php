<?php

use App\Domains\Tenancy\Enums\TenantPlan;
use App\Domains\Tenancy\Enums\TenantStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Default tenant that owns pre-multitenancy rows. Kept self-contained
     * (not referencing the seeder) so this historical migration stays stable
     * even if the seeder's slug later changes.
     */
    private const DEMO_TENANT_SLUG = 'academy-demo';

    /**
     * Domain tables that carry tenant_id and must be isolated per tenant.
     *
     * @var list<string>
     */
    private const TENANT_TABLES = [
        'users',
        'teachers',
        'representatives',
        'students',
        'subjects',
        'academic_periods',
        'sections',
        'section_subject_teacher',
        'enrollments',
        'grade_columns',
        'grades',
        'subject_teacher',
    ];

    /**
     * Backfill orphan rows to the demo tenant, then enforce tenant_id as
     * NOT NULL with a cascading foreign key on every tenant table.
     */
    public function up(): void
    {
        $this->backfillOrphanRows();

        foreach (self::TENANT_TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->uuid('tenant_id')->nullable(false)->change();
                $blueprint->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
            });
        }
    }

    /**
     * Assign orphan rows to the demo tenant. The demo tenant is only
     * resolved (and created if missing) when orphans actually exist, so a
     * fresh database stays untouched instead of being seeded with fixtures.
     */
    private function backfillOrphanRows(): void
    {
        $demoTenantId = null;

        foreach (self::TENANT_TABLES as $table) {
            if (DB::table($table)->whereNull('tenant_id')->doesntExist()) {
                continue;
            }

            $demoTenantId ??= $this->resolveDemoTenantId();
            DB::table($table)->whereNull('tenant_id')->update(['tenant_id' => $demoTenantId]);
        }
    }

    /**
     * Drop the foreign key and relax tenant_id back to nullable. Schema-only:
     * the backfilled tenant_id values are left in place (the original null
     * rows cannot be reconstructed) and any auto-created demo tenant remains.
     */
    public function down(): void
    {
        foreach (self::TENANT_TABLES as $table) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->dropForeign(['tenant_id']);
                $blueprint->uuid('tenant_id')->nullable()->change();
            });
        }
    }

    /**
     * Resolve the demo tenant id, creating it when the schema is enforced
     * over an existing database that was never seeded.
     */
    private function resolveDemoTenantId(): string
    {
        $demoTenantId = DB::table('tenants')->where('slug', self::DEMO_TENANT_SLUG)->value('id');

        if ($demoTenantId !== null) {
            return $demoTenantId;
        }

        $demoTenantId = Str::uuid7()->toString();

        DB::table('tenants')->insert([
            'id' => $demoTenantId,
            'name' => 'Academy Demo',
            'slug' => self::DEMO_TENANT_SLUG,
            'plan' => TenantPlan::Free->value,
            'status' => TenantStatus::Active->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $demoTenantId;
    }
};
