<?php

namespace App\Domains\Academics\Services\Sections;

use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Repositories\SubjectRepository;
use Illuminate\Database\Eloquent\Collection;

class SectionAssignmentsService
{
    public function __construct(
        private SubjectRepository $subjectRepository
    ) {}

    public function handle(Section $section): array
    {
        $section->load([
            'academicPeriod',
            'sectionSubjectTeachers.subject',
            'sectionSubjectTeachers.teacher.user',
        ]);

        $subjects = $this->subjectRepository->activeOrderedWithActiveTeachers();

        $teachersBySubject = $this->mapTeachersBySubject($subjects);

        return compact('section', 'subjects', 'teachersBySubject');
    }

    private function mapTeachersBySubject(Collection $subjects): array
    {
        $teachersBySubject = [];

        foreach ($subjects as $subject) {
            $teachersBySubject[$subject->id] = $subject->teachers->map(fn ($teacher) => [
                'id' => $teacher->id,
                'name' => $teacher->user->full_name,
            ])->toArray();
        }

        return $teachersBySubject;
    }
}
