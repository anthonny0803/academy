<?php

use App\Domains\AI\Enums\ObservationStatus;
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
        Schema::create('student_performance_observations', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Identificador único (UUID v7) de la Observación
            // Tenant propietario. NOT NULL + FK cascade (esquema post-enforcement, ISSUE-51);
            // lo auto-asigna BelongsToTenant, nunca por mass assignment
            $table->foreignUuid('tenant_id')->constrained('tenants')->cascadeOnDelete();
            // Estudiante sobre el que se redacta la observación (artefacto derivado: cascade)
            $table->foreignUuid('student_id')->constrained('students')->cascadeOnDelete();
            // Usuario que solicitó la generación (auditoría); sobrevive si el usuario se borra
            $table->foreignUuid('requested_by_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('status')->default(ObservationStatus::Pending->value);
            $table->text('content')->nullable(); // Texto generado por la IA (salida no confiable)
            $table->text('failure_reason')->nullable(); // Motivo del fallo cuando el Job falla
            $table->timestamp('generated_at')->nullable(); // Momento en que se completó la generación
            $table->timestamps();

            $table->index(['tenant_id', 'student_id']); // Listado por estudiante dentro del tenant
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_performance_observations');
    }
};
