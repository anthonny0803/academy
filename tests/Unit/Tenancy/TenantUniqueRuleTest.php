<?php

namespace Tests\Unit\Tenancy;

use App\Domains\Academics\Models\Subject;
use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Rules\TenantUnique;
use App\Domains\Tenancy\Support\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TenantUniqueRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_passes_when_the_name_exists_only_in_another_tenant(): void
    {
        $otherTenant = Tenant::factory()->create();
        $this->withinTenant($otherTenant, fn () => Subject::factory()->create(['name' => 'MATEMATICAS']));

        $this->assertTrue($this->validateName('MATEMATICAS')->passes());
    }

    public function test_fails_when_the_name_exists_in_the_current_tenant(): void
    {
        Subject::factory()->create(['name' => 'MATEMATICAS']);

        $validator = $this->validateName('MATEMATICAS');

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }

    public function test_ignores_the_given_id_within_the_current_tenant(): void
    {
        $subject = Subject::factory()->create(['name' => 'MATEMATICAS']);

        $validator = Validator::make(
            ['name' => 'MATEMATICAS'],
            ['name' => [TenantUnique::in('subjects')->ignore($subject->id)]]
        );

        $this->assertTrue($validator->passes());
    }

    public function test_checks_globally_when_no_tenant_is_resolved(): void
    {
        Subject::factory()->create(['name' => 'MATEMATICAS']);
        app(CurrentTenant::class)->forget();

        $this->assertTrue($this->validateName('MATEMATICAS')->fails());
    }

    private function validateName(string $name): \Illuminate\Validation\Validator
    {
        return Validator::make(
            ['name' => $name],
            ['name' => [TenantUnique::in('subjects')]]
        );
    }
}
