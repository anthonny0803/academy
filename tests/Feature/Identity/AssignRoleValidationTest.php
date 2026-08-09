<?php

namespace Tests\Feature\Identity;

use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Models\User;
use App\Domains\Shared\Support\DocumentId;
use App\Domains\Shared\Support\Phone;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignRoleValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function assignRepresentative(User $actor, User $target, array $payload)
    {
        return $this->actingAs($actor)
            ->from(route('role-management.show-form', [
                'user' => $target,
                'role' => Role::Representative->value,
            ]))
            ->post(route('role-management.assign', [
                'user' => $target,
                'role' => Role::Representative->value,
            ]), $payload);
    }

    public function test_an_invalid_phone_is_rejected_with_the_shared_format_message(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $target = User::factory()->create(['phone' => null]);

        $response = $this->assignRepresentative($supervisor, $target, ['phone' => '12345']);

        $response->assertSessionHasErrors(['phone' => Phone::FORMAT_MESSAGE]);
        $this->assertNull($target->fresh()->phone);
    }

    public function test_a_document_another_user_already_holds_is_rejected_instead_of_crashing(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        User::factory()->create(['document_id' => '12345678A']);
        $target = User::factory()->create(['document_id' => null]);

        $response = $this->assignRepresentative($supervisor, $target, [
            'document_id' => '12345678A',
        ]);

        $response->assertSessionHasErrors(['document_id' => DocumentId::DUPLICATE_MESSAGE]);
        $this->assertNull($target->fresh()->document_id);
        $this->assertFalse($target->fresh()->hasRole(Role::Representative->value));
    }

    public function test_a_document_the_form_normalizes_into_a_collision_is_rejected_too(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        User::factory()->create(['document_id' => '12345678A']);
        $target = User::factory()->create(['document_id' => null]);

        $response = $this->assignRepresentative($supervisor, $target, [
            'document_id' => '12.345.678-a',
        ]);

        $response->assertSessionHasErrors(['document_id' => DocumentId::DUPLICATE_MESSAGE]);
    }

    public function test_a_document_nobody_holds_still_completes_the_assignment(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $target = User::factory()->create(['document_id' => null]);

        $response = $this->assignRepresentative($supervisor, $target, [
            'document_id' => '87654321B',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('87654321B', $target->fresh()->document_id);
        $this->assertTrue($target->fresh()->hasRole(Role::Representative->value));
    }
}
