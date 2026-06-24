<?php

use App\Domains\Tenancy\Enums\TenantPlan;
use App\Domains\Tenancy\Enums\TenantStatus;
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
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Identificador único (UUID) del Tenant
            $table->string('name'); // Nombre de la organización (Ej: 'Colegio San José')
            $table->string('slug')->unique(); // Identificador legible/subdominio único del Tenant
            $table->string('plan')->default(TenantPlan::Free->value); // Plan de suscripción contratado
            $table->string('status')->default(TenantStatus::Active->value); // Estado del ciclo de vida del Tenant
            $table->timestamps(); // Columnas created_at y updated_at para auditoría
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
