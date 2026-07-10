<?php

namespace Tests\Unit\Tenancy;

use App\Domains\Academics\Models\Section;
use App\Domains\Tenancy\Models\Tenant;
use App\Domains\Tenancy\Rules\TenantExists;
use App\Domains\Tenancy\Support\CurrentTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TenantExistsRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_passes_for_a_reference_within_the_current_tenant(): void
    {
        $section = Section::factory()->create();

        $this->assertTrue($this->validateSectionId($section->id)->passes());
    }

    public function test_fails_for_a_reference_from_another_tenant(): void
    {
        $otherTenant = Tenant::factory()->create();
        $foreignSection = $this->withinTenant($otherTenant, fn () => Section::factory()->create());

        $validator = $this->validateSectionId($foreignSection->id);

        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('section_id'));
    }

    public function test_fails_closed_when_no_tenant_is_resolved(): void
    {
        $section = Section::factory()->create();
        app(CurrentTenant::class)->forget();

        $this->assertTrue($this->validateSectionId($section->id)->fails());
    }

    private function validateSectionId(string $sectionId): \Illuminate\Validation\Validator
    {
        return Validator::make(
            ['section_id' => $sectionId],
            ['section_id' => [TenantExists::in('sections')]]
        );
    }
}
