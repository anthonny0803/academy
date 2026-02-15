<?php

namespace Database\Factories;

use App\Models\Section;
use App\Models\SectionSubjectTeacher;
use App\Models\Subject;
use App\Models\SubjectTeacher;
use App\Models\Teacher;
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
            'status' => 'activo',
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (SectionSubjectTeacher $sst) {
            // The teacher must be authorized to teach the subject (like StoreSectionSubjectTeacherService validates)
            $exists = SubjectTeacher::where('teacher_id', $sst->teacher_id)
                ->where('subject_id', $sst->subject_id)
                ->exists();

            if (!$exists) {
                SubjectTeacher::create([
                    'teacher_id' => $sst->teacher_id,
                    'subject_id' => $sst->subject_id,
                ]);
            }
        });
    }

    public function inactive(): static
    {
        return $this->state(['status' => 'inactivo']);
    }

    public function substitute(): static
    {
        return $this->state([
            'is_primary' => false,
            'status' => 'suplente',
        ]);
    }
}
