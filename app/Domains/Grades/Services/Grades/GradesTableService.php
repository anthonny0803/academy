<?php

namespace App\Domains\Grades\Services\Grades;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Grades\Repositories\GradeRepository;
use App\Domains\Grades\Support\GradeMatrix;

class GradesTableService
{
    private const DEFAULT_MIN_GRADE = 0;

    private const DEFAULT_MAX_GRADE = 100;

    private const DEFAULT_PASSING_GRADE = 60;

    public function __construct(
        private GradeRepository $gradeRepository
    ) {}

    public function handle(SectionSubjectTeacher $sectionSubjectTeacher): array
    {
        $sectionSubjectTeacher->load([
            'section.academicPeriod',
            'section.enrollments' => fn ($q) => $q->active()->with('student.user'),
            'subject',
            'teacher.user',
            'gradeColumns' => fn ($q) => $q->orderBy('display_order'),
        ]);

        $gradeColumns = $sectionSubjectTeacher->gradeColumns;
        $enrollments = $sectionSubjectTeacher->section->enrollments;
        $academicPeriod = $sectionSubjectTeacher->section->academicPeriod;

        $minGrade = $academicPeriod->min_grade ?? self::DEFAULT_MIN_GRADE;
        $maxGrade = $academicPeriod->max_grade ?? self::DEFAULT_MAX_GRADE;
        $passingGrade = $academicPeriod->passing_grade ?? self::DEFAULT_PASSING_GRADE;

        $isConfigurationComplete = $sectionSubjectTeacher->isConfigurationComplete();

        $gradeMatrix = $this->gradeMatrixFor($gradeColumns->pluck('id')->all());
        $gradesByEnrollment = $gradeMatrix->toArray();
        $averagesByEnrollment = $enrollments
            ->mapWithKeys(fn (Enrollment $enrollment) => [
                $enrollment->id => $gradeMatrix->weightedAverage($enrollment->id, $gradeColumns),
            ])
            ->all();

        return compact(
            'sectionSubjectTeacher',
            'gradeColumns',
            'enrollments',
            'gradesByEnrollment',
            'averagesByEnrollment',
            'isConfigurationComplete',
            'academicPeriod',
            'minGrade',
            'maxGrade',
            'passingGrade'
        );
    }

    private function gradeMatrixFor(array $gradeColumnIds): GradeMatrix
    {
        return GradeMatrix::fromGrades($this->gradeRepository->forGradeColumns($gradeColumnIds));
    }
}
