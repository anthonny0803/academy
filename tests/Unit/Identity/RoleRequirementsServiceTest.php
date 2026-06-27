<?php

namespace Tests\Unit\Identity;

use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Models\User;
use App\Domains\Identity\Services\RoleManagement\RoleRequirementsService;
use Tests\TestCase;

class RoleRequirementsServiceTest extends TestCase
{
    private RoleRequirementsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(RoleRequirementsService::class);
    }

    public function test_password_is_required_when_empty_for_any_role(): void
    {
        $user = new User;
        $user->password = null;

        $missing = $this->service->missingFieldsForRole($user, Role::Teacher);

        $this->assertContains('password', $missing);
    }

    public function test_representative_requires_profile_fields(): void
    {
        $user = $this->userWithoutRepresentativeFields();

        $missing = $this->service->missingFieldsForRole($user, Role::Representative);

        $this->assertEqualsCanonicalizing(
            ['document_id', 'birth_date', 'phone', 'address'],
            $missing
        );
        $this->assertTrue($this->service->roleNeedsForm($user, Role::Representative));
    }

    public function test_complete_representative_has_no_missing_fields(): void
    {
        $user = new User;
        $user->password = 'hashed';
        $user->document_id = 'V12345678';
        $user->birth_date = '1990-01-01';
        $user->phone = '04141234567';
        $user->address = 'Caracas';

        $this->assertSame([], $this->service->missingFieldsForRole($user, Role::Representative));
        $this->assertFalse($this->service->roleNeedsForm($user, Role::Representative));
    }

    public function test_non_representative_role_only_checks_password(): void
    {
        $user = $this->userWithoutRepresentativeFields();
        $user->password = 'hashed';

        $this->assertSame([], $this->service->missingFieldsForRole($user, Role::Teacher));
    }

    private function userWithoutRepresentativeFields(): User
    {
        $user = new User;
        $user->password = 'hashed';
        $user->document_id = null;
        $user->birth_date = null;
        $user->phone = null;
        $user->address = null;

        return $user;
    }
}
