<?php

namespace Tests\Feature\Identity;

use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Models\User;
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
}
