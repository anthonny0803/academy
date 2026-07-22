<?php

namespace Tests\Feature\Grades;

use App\Domains\Grades\Services\Api\PublicGradesService;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Students\Models\Student;
use Closure;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PublicGradesServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_student_path_does_not_load_the_representative_branch(): void
    {
        $service = app(PublicGradesService::class);

        // A dual-role user carries a representative sub-tree; the student path
        // must not touch it, so its query count matches a plain student's.
        $plainStudent = Student::factory()->create()->user;
        $dualRole = $this->dualRoleUser();

        $this->assertSame(
            $this->countQueries(fn () => $service->getStudentGrades(
                $plainStudent->document_id,
                $plainStudent->birth_date->format('Y-m-d'),
            )),
            $this->countQueries(fn () => $service->getStudentGrades(
                $dualRole->document_id,
                $dualRole->birth_date->format('Y-m-d'),
            )),
        );
    }

    public function test_representative_path_does_not_load_the_student_branch(): void
    {
        $service = app(PublicGradesService::class);

        $plainRepresentative = $this->representativeUserOfTwoStudents();
        $dualRole = $this->dualRoleUser();

        $this->assertSame(
            $this->countQueries(fn () => $service->getRepresentativeGrades(
                $plainRepresentative->document_id,
                $plainRepresentative->birth_date->format('Y-m-d'),
            )),
            $this->countQueries(fn () => $service->getRepresentativeGrades(
                $dualRole->document_id,
                $dualRole->birth_date->format('Y-m-d'),
            )),
        );
    }

    private function representativeUserOfTwoStudents(): User
    {
        $representative = Representative::factory()->create();
        Student::factory()->count(2)->create(['representative_id' => $representative->id]);

        return $representative->user;
    }

    private function dualRoleUser(): User
    {
        $user = User::factory()->create();

        $representative = Representative::factory()->create(['user_id' => $user->id]);
        Student::factory()->count(2)->create(['representative_id' => $representative->id]);

        Student::factory()->create(['user_id' => $user->id]);

        return $user->fresh();
    }

    private function countQueries(Closure $callback): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $callback();

        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $queries;
    }
}
