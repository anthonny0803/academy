<?php

namespace Database\Factories;

use App\Domains\Academics\Enums\SectionSubjectTeacherStatus;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Models\SubjectTeacher;
use App\Domains\Academics\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class SectionSubjectTeacherFactory extends Factory
{
    protected $model = SectionSubjectTeacher::class;

    public function definition(): array
    {
        return [
            'section_id' => Section::factory(),
            'subject_id' => Subject::factory(),
            'teacher_id' => Teacher::factory(),
            'is_primary' => true,
            'status' => SectionSubjectTeacherStatus::Active->value,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (SectionSubjectTeacher $sst) {
            // The teacher must be authorized to teach the subject (like StoreSectionSubjectTeacherService validates)
            $exists = SubjectTeacher::where('teacher_id', $sst->teacher_id)
                ->where('subject_id', $sst->subject_id)
                ->exists();

            if (! $exists) {
                SubjectTeacher::create([
                    'teacher_id' => $sst->teacher_id,
                    'subject_id' => $sst->subject_id,
                ]);
            }
        });
    }

    public function inactive(): static
    {
        return $this->state(['status' => SectionSubjectTeacherStatus::Inactive->value]);
    }

    public function substitute(): static
    {
        return $this->state([
            'is_primary' => false,
            'status' => SectionSubjectTeacherStatus::Substitute->value,
        ]);
    }
}
