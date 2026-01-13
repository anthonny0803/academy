<?php

namespace App\Services\AcademicPeriods;

use App\Models\AcademicPeriod;
use App\Models\Enrollment;
use App\Models\Student;
use App\Enums\EnrollmentStatus;
use App\Enums\StudentSituation;
use App\Services\Representatives\SyncRepresentativeStatusService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CloseAcademicPeriodService
{
    public function __construct(
        private SyncRepresentativeStatusService $syncRepresentativeStatus
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
                'message' => "El período finaliza el {$academicPeriod->end_date->format('d/m/Y')}. " .
                             "Se recomienda esperar hasta esa fecha para cerrar.",
            ];
        }

        $sections = $academicPeriod->sections()
            ->with([
                'enrollments' => fn($q) => $q->active()->with('student.user'),
                'sectionSubjectTeachers' => fn($q) => $q->active()->with(['subject', 'gradeColumns']),
            ])
            ->get();

        $summary['total_sections'] = $sections->count();

        foreach ($sections as $section) {
            $sectionIssues = [];
            $activeEnrollments = $section->enrollments;
            $summary['total_enrollments'] += $activeEnrollments->count();

            foreach ($section->sectionSubjectTeachers as $sst) {
                $subjectIssues = [];

                if (!$sst->isConfigurationComplete()) {
                    $subjectIssues[] = [
                        'type' => 'configuration',
                        'message' => "Configuración incompleta: {$sst->getTotalWeight()}% de 100%",
                    ];
                }

                $studentsWithMissingGrades = [];
                foreach ($activeEnrollments as $enrollment) {
                    $missingColumns = [];
                    
                    foreach ($sst->gradeColumns as $column) {
                        $hasGrade = $enrollment->grades()
                            ->where('grade_column_id', $column->id)
                            ->exists();

                        if (!$hasGrade) {
                            $missingColumns[] = $column->name;
                        }
                    }

                    if (!empty($missingColumns)) {
                        $studentsWithMissingGrades[] = [
                            'student' => $enrollment->student->user->full_name,
                            'missing' => $missingColumns,
                        ];
                    }
                }

                if (!empty($studentsWithMissingGrades)) {
                    $subjectIssues[] = [
                        'type' => 'grades',
                        'students' => $studentsWithMissingGrades,
                    ];
                }

                if (!empty($subjectIssues)) {
                    $sectionIssues[$sst->subject->name] = $subjectIssues;
                }
            }

            if (!empty($sectionIssues)) {
                $issues['sections'][$section->name] = $sectionIssues;
                $summary['enrollments_with_issues'] += $activeEnrollments->count();
            } else {
                $summary['enrollments_ready'] += $activeEnrollments->count();
            }
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

        $sections = $academicPeriod->sections()
            ->with([
                'enrollments' => fn($q) => $q->active()->with('student.user'),
                'sectionSubjectTeachers' => fn($q) => $q->active(),
            ])
            ->get();

        $preview['sections_to_deactivate'] = $sections->count();

        foreach ($sections as $section) {
            $sectionDetails = [
                'name' => $section->name,
                'passed' => [],
                'failed' => [],
            ];

            foreach ($section->enrollments as $enrollment) {
                $willPass = $enrollment->calculatePassed();
                $studentName = $enrollment->student->user->full_name;

                if ($willPass === true) {
                    $preview['passed']++;
                    $sectionDetails['passed'][] = $studentName;
                } else {
                    $preview['failed']++;
                    $sectionDetails['failed'][] = $studentName;
                }
            }

            $preview['details'][] = $sectionDetails;
        }

        return $preview;
    }

    public function handle(AcademicPeriod $academicPeriod, bool $forceClose = false): array
    {
        $validation = $this->validateForClose($academicPeriod);
        
        if (!$validation['can_close'] && !$forceClose) {
            throw new \Exception(
                'No se puede cerrar el período. Hay inscripciones con datos incompletos.'
            );
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
            $studentIds = Enrollment::whereHas('section', function ($q) use ($academicPeriod) {
                    $q->where('academic_period_id', $academicPeriod->id);
                })
                ->where('status', EnrollmentStatus::Active->value)
                ->pluck('student_id')
                ->unique()
                ->toArray();

            // Get representative IDs associated
            $representativeIds = Student::whereIn('id', $studentIds)
                ->pluck('representative_id')
                ->unique()
                ->toArray();

            $sections = $academicPeriod->sections()
                ->with([
                    'enrollments' => fn($q) => $q->active()->with('student.user'),
                    'sectionSubjectTeachers' => fn($q) => $q->active(),
                ])
                ->get();

            foreach ($sections as $section) {
                foreach ($section->enrollments as $enrollment) {
                    $passed = $enrollment->calculatePassed() ?? false;

                    $enrollment->update([
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

                $section->update(['is_active' => false]);
                $results['sections_deactivated']++;
            }

            $academicPeriod->update(['is_active' => false]);

            // BULK UPDATE: Deactivate students without active enrollments
            $studentsDeactivated = Student::whereIn('id', $studentIds)
                ->where('is_active', true)
                ->whereDoesntHave('enrollments', function ($q) {
                    $q->where('status', EnrollmentStatus::Active->value);
                })
                ->update([
                    'is_active' => false,
                    'situation' => StudentSituation::Inactive,
                ]);

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
}