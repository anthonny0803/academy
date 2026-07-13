<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The public grades API used one shared static token for every tenant,
     * with no tenant resolution on those routes. Each tenant now holds its
     * own token, stored as a sha256 hash (64 hex chars): the middleware
     * resolves the tenant from the bearer token and activates the tenant
     * scope. Nullable because tenants start token-less.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('public_api_token_hash', 64)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('public_api_token_hash');
        });
    }
};
