<?php

namespace Tests\Feature\Academics;

use App\Domains\Academics\Enums\SectionSubjectTeacherStatus;
use App\Domains\Academics\Exceptions\TeacherNotQualifiedForSubjectException;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Models\Teacher;
use App\Domains\Academics\Services\SectionSubjectTeacher\StoreSectionSubjectTeacherService;
use App\Domains\Identity\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreSectionSubjectTeacherQualificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    public function test_store_service_throws_a_domain_exception_when_the_teacher_is_not_qualified(): void
    {
        $section = Section::factory()->create();
        $subject = Subject::factory()->create();
        $unqualifiedTeacher = Teacher::factory()->create();

        try {
            app(StoreSectionSubjectTeacherService::class)->handle([
                'section_id' => $section->id,
                'subject_id' => $subject->id,
                'teacher_id' => $unqualifiedTeacher->id,
                'is_primary' => false,
                'status' => SectionSubjectTeacherStatus::Active->value,
            ]);

            $this->fail('Expected TeacherNotQualifiedForSubjectException was not thrown.');
        } catch (TeacherNotQualifiedForSubjectException $e) {
            $this->assertSame(422, $e->statusCode());
            $this->assertSame('TEACHER_NOT_QUALIFIED_FOR_SUBJECT', $e->errorCode());
            $this->assertSame(
                'El profesor seleccionado no está autorizado para impartir esta materia.',
                $e->getMessage()
            );
        }

        $this->assertSame(0, SectionSubjectTeacher::count());
    }

    public function test_web_store_redirects_back_with_a_field_error_when_the_teacher_is_not_qualified(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $section = Section::factory()->create();
        $subject = Subject::factory()->create();
        $unqualifiedTeacher = Teacher::factory()->create();

        $response = $this->actingAs($supervisor)
            ->from(route('sections.show', $section))
            ->post(route('section-subject-teacher.store'), [
                'section_id' => $section->id,
                'subject_id' => $subject->id,
                'teacher_id' => $unqualifiedTeacher->id,
                'is_primary' => false,
                'status' => SectionSubjectTeacherStatus::Active->value,
            ]);

        $response->assertRedirect(route('sections.show', $section));
        $response->assertSessionHasErrors([
            'teacher_id' => 'El profesor seleccionado no está autorizado para impartir esta materia.',
        ]);
        $this->assertSame(0, SectionSubjectTeacher::count());
    }
}
