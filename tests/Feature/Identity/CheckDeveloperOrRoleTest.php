<?php

namespace Tests\Feature\Identity;

use App\Domains\Identity\Http\Middleware\CheckDeveloperOrRole;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CheckDeveloperOrRoleTest extends TestCase
{
    use RefreshDatabase;

    private const PANEL_URI = '/test-panel';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        Route::middleware(['auth', CheckDeveloperOrRole::class.':Supervisor|Administrador'])
            ->get(self::PANEL_URI, fn () => response('ok'));
    }

    public function test_developer_without_roles_can_access_the_panel(): void
    {
        $developer = User::factory()->developer()->create();

        $this->actingAs($developer)
            ->get(self::PANEL_URI)
            ->assertOk()
            ->assertSee('ok');
    }

    public function test_supervisor_can_access_the_panel(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        $this->actingAs($supervisor)
            ->get(self::PANEL_URI)
            ->assertOk();
    }

    public function test_admin_can_access_the_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(self::PANEL_URI)
            ->assertOk();
    }

    public function test_user_without_developer_or_allowed_role_is_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(self::PANEL_URI)
            ->assertForbidden();
    }
}
