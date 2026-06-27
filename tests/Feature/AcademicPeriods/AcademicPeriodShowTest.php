<?php

namespace Tests\Feature\AcademicPeriods;

use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicPeriodShowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_show_view_receives_stats_with_aggregated_counts(): void
    {
        $developer = User::factory()->developer()->create();
        $academicPeriod = AcademicPeriod::factory()->create();

        $activeSection = Section::factory()->create([
            'academic_period_id' => $academicPeriod->id,
            'is_active' => true,
        ]);
        $inactiveSection = Section::factory()->create([
            'academic_period_id' => $academicPeriod->id,
            'is_active' => false,
        ]);

        Enrollment::factory()->create(['section_id' => $activeSection->id]);
        Enrollment::factory()->completed()->create(['section_id' => $activeSection->id]);
        Enrollment::factory()->create(['section_id' => $inactiveSection->id]);

        $response = $this->actingAs($developer)->get(route('academic-periods.show', $academicPeriod));

        $response->assertOk();
        $response->assertViewIs('academic-periods.show');
        $response->assertViewHas('stats', [
            'total_sections' => 2,
            'active_sections' => 1,
            'total_enrollments' => 3,
            'active_enrollments' => 2,
            'completed_enrollments' => 1,
        ]);
    }
}
