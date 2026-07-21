<?php

namespace App\Domains\Academics\Services\AcademicPeriods;

use App\Domains\Academics\Exceptions\AcademicPeriodNotReadyForCloseException;
use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Repositories\AcademicPeriodRepository;
use App\Domains\Academics\Repositories\SectionRepository;
use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Models\Enrollment;
use App\Domains\Enrollments\Repositories\EnrollmentRepository;
use App\Domains\Grades\Models\GradeColumn;
use App\Domains\Grades\Repositories\GradeRepository;
use App\Domains\Grades\Support\GradeMatrix;
use App\Domains\Representatives\Services\SyncRepresentativeStatusService;
use App\Domains\Students\Repositories\StudentRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CloseAcademicPeriodService
{
    public function __construct(
        private SyncRepresentativeStatusService $syncRepresentativeStatus,
        private StudentRepository $studentRepository,
        private EnrollmentRepository $enrollmentRepository,
        private SectionRepository $sectionRepository,
        private AcademicPeriodRepository $academicPeriodRepository,
        private GradeRepository $gradeRepository
    ) {}

    public function validateForClose(AcademicPeriod $academicPeriod): array
    {
        $issues = [];
        $summary = [
            'total_sections' => 0,
            'total_enrollments' => 0,
            'enrollments_ready' => 0,
            'enrollments_with_issues' => 0,
        ];

        if (now()->lt($academicPeriod->end_date)) {
            $issues['date'] = [
                'type' => 'warning',
                'message' => "El período finaliza el {$academicPeriod->end_date->format('d/m/Y')}. ".
                             'Se recomienda esperar hasta esa fecha para cerrar.',
            ];
        }

        $sections = $this->loadSectionsForClose($academicPeriod);
        $gradeMatrix = $this->buildGradeMatrix($sections);

        $summary['total_sections'] = $sections->count();

        foreach ($sections as $section) {
            $activeEnrollments = $section->enrollments;
            $summary['total_enrollments'] += $activeEnrollments->count();

            $sectionIssues = $this->collectSectionIssues($section, $gradeMatrix);

            if (empty($sectionIssues)) {
                $summary['enrollments_ready'] += $activeEnrollments->count();

                continue;
            }

            $issues['sections'][$section->name] = $sectionIssues;
            $summary['enrollments_with_issues'] += $activeEnrollments->count();
        }

        return [
            'can_close' => empty($issues['sections']),
            'has_date_warning' => isset($issues['date']),
            'issues' => $issues,
            'summary' => $summary,
        ];
    }

    public function getClosePreview(AcademicPeriod $academicPeriod): array
    {
        $preview = [
            'passed' => 0,
            'failed' => 0,
            'sections_to_deactivate' => 0,
            'details' => [],
        ];

        $sections = $this->loadSectionsForClose($academicPeriod);
        $gradeMatrix = $this->buildGradeMatrix($sections);

        $preview['sections_to_deactivate'] = $sections->count();

        foreach ($sections as $section) {
            $sectionDetails = [
                'name' => $section->name,
                'passed' => [],
                'failed' => [],
            ];

            foreach ($section->enrollments as $enrollment) {
                $studentName = $enrollment->student->user->full_name;
                $outcome = $this->hasPassed($academicPeriod, $enrollment, $section->sectionSubjectTeachers, $gradeMatrix)
                    ? 'passed'
                    : 'failed';

                $preview[$outcome]++;
                $sectionDetails[$outcome][] = $studentName;
            }

            $preview['details'][] = $sectionDetails;
        }

        return $preview;
    }

    public function handle(AcademicPeriod $academicPeriod, bool $forceClose = false): array
    {
        $validation = $this->validateForClose($academicPeriod);

        if (! $validation['can_close'] && ! $forceClose) {
            throw AcademicPeriodNotReadyForCloseException::make();
        }

        return DB::transaction(function () use ($academicPeriod) {
            $results = [
                'enrollments_completed' => 0,
                'enrollments_passed' => 0,
                'enrollments_failed' => 0,
                'sections_deactivated' => 0,
                'students_deactivated' => 0,
                'representatives_deactivated' => 0,
            ];

            // Get student IDs affected
            $studentIds = $this->enrollmentRepository->studentIdsForActivePeriod($academicPeriod->id)
                ->unique()
                ->toArray();

            // Get representative IDs associated
            $representativeIds = $this->studentRepository->representativeIdsFor($studentIds)
                ->unique()
                ->toArray();

            $sections = $this->loadSectionsForClose($academicPeriod);
            $gradeMatrix = $this->buildGradeMatrix($sections);

            foreach ($sections as $section) {
                foreach ($section->enrollments as $enrollment) {
                    $passed = $this->hasPassed($academicPeriod, $enrollment, $section->sectionSubjectTeachers, $gradeMatrix);

                    $this->enrollmentRepository->update($enrollment, [
                        'status' => EnrollmentStatus::Completed->value,
                        'passed' => $passed,
                    ]);

                    $results['enrollments_completed']++;
                    $passed ? $results['enrollments_passed']++ : $results['enrollments_failed']++;

                    Log::info('Enrollment completed on period close', [
                        'enrollment_id' => $enrollment->id,
                        'student_id' => $enrollment->student_id,
                        'student_name' => $enrollment->student->user->full_name,
                        'section' => $section->name,
                        'passed' => $passed,
                    ]);
                }

                $this->sectionRepository->update($section, ['is_active' => false]);
                $results['sections_deactivated']++;
            }

            $this->academicPeriodRepository->update($academicPeriod, ['is_active' => false]);

            // BULK UPDATE: Deactivate students without active enrollments
            $studentsDeactivated = $this->studentRepository->deactivateWithoutActiveEnrollments($studentIds);

            $results['students_deactivated'] = $studentsDeactivated;

            if ($studentsDeactivated > 0) {
                Log::info('Students deactivated on period close', [
                    'academic_period_id' => $academicPeriod->id,
                    'academic_period_name' => $academicPeriod->name,
                    'students_deactivated' => $studentsDeactivated,
                    'performed_by' => Auth::id(),
                    'performed_at' => now(),
                ]);
            }

            // Synchronize representative statuses (after student deactivations)
            $syncResults = $this->syncRepresentativeStatus->handle($representativeIds);
            $results['representatives_deactivated'] = $syncResults['deactivated'];

            Log::info('Academic period closed', [
                'academic_period_id' => $academicPeriod->id,
                'academic_period_name' => $academicPeriod->name,
                'results' => $results,
                'performed_by' => Auth::id(),
                'performed_at' => now(),
            ]);

            return $results;
        });
    }

    /**
     * @return Collection<int, Section>
     */
    private function loadSectionsForClose(AcademicPeriod $academicPeriod): Collection
    {
        return $academicPeriod->sections()
            ->with([
                'enrollments' => fn ($q) => $q->active()->with('student.user'),
                'sectionSubjectTeachers' => fn ($q) => $q->active()->with(['subject', 'gradeColumns']),
            ])
            ->get();
    }

    /**
     * @param  Collection<int, Section>  $sections
     */
    private function buildGradeMatrix(Collection $sections): GradeMatrix
    {
        $columnIds = $sections
            ->flatMap(fn (Section $section) => $section->sectionSubjectTeachers)
            ->flatMap(fn (SectionSubjectTeacher $assignment) => $assignment->gradeColumns)
            ->pluck('id')
            ->all();

        return GradeMatrix::fromGrades($this->gradeRepository->forGradeColumns($columnIds));
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function collectSectionIssues(Section $section, GradeMatrix $gradeMatrix): array
    {
        $sectionIssues = [];

        foreach ($section->sectionSubjectTeachers as $assignment) {
            $assignmentIssues = $this->collectAssignmentIssues($assignment, $section->enrollments, $gradeMatrix);

            if (empty($assignmentIssues)) {
                continue;
            }

            $sectionIssues[$assignment->subject->name] = $assignmentIssues;
        }

        return $sectionIssues;
    }

    /**
     * @param  Collection<int, Enrollment>  $enrollments
     * @return array<int, array<string, mixed>>
     */
    private function collectAssignmentIssues(
        SectionSubjectTeacher $assignment,
        Collection $enrollments,
        GradeMatrix $gradeMatrix
    ): array {
        $assignmentIssues = [];

        if (! $assignment->isConfigurationComplete()) {
            $assignmentIssues[] = [
                'type' => 'configuration',
                'message' => "Configuración incompleta: {$assignment->getTotalWeight()}% de 100%",
            ];
        }

        $studentsWithMissingGrades = $this->findStudentsWithMissingGrades($assignment, $enrollments, $gradeMatrix);

        if (! empty($studentsWithMissingGrades)) {
            $assignmentIssues[] = [
                'type' => 'grades',
                'students' => $studentsWithMissingGrades,
            ];
        }

        return $assignmentIssues;
    }

    /**
     * @param  Collection<int, Enrollment>  $enrollments
     * @return array<int, array<string, mixed>>
     */
    private function findStudentsWithMissingGrades(
        SectionSubjectTeacher $assignment,
        Collection $enrollments,
        GradeMatrix $gradeMatrix
    ): array {
        $studentsWithMissingGrades = [];

        foreach ($enrollments as $enrollment) {
            $missingColumns = $assignment->gradeColumns
                ->reject(fn (GradeColumn $column) => $gradeMatrix->has($enrollment->id, $column->id))
                ->pluck('name')
                ->all();

            if (empty($missingColumns)) {
                continue;
            }

            $studentsWithMissingGrades[] = [
                'student' => $enrollment->student->user->full_name,
                'missing' => $missingColumns,
            ];
        }

        return $studentsWithMissingGrades;
    }

    /**
     * @param  Collection<int, SectionSubjectTeacher>  $assignments
     */
    private function hasPassed(
        AcademicPeriod $academicPeriod,
        Enrollment $enrollment,
        Collection $assignments,
        GradeMatrix $gradeMatrix
    ): bool {
        if ($assignments->isEmpty()) {
            return false;
        }

        foreach ($assignments as $assignment) {
            $average = $gradeMatrix->weightedAverage($enrollment->id, $assignment->gradeColumns);

            if ($average === null || ! $academicPeriod->isGradePassing($average)) {
                return false;
            }
        }

        return true;
    }
}
