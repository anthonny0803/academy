<?php

namespace Tests\Unit\Factories;

use App\Domains\Academics\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_unique_names_beyond_the_base_list(): void
    {
        $subjects = Subject::factory()->count(50)->create();

        $this->assertCount(50, $subjects->pluck('name')->unique());
    }
}
