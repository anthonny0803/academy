<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\GradeColumn;
use App\Models\SectionSubjectTeacher;
use App\Http\Requests\GradeColumns\StoreGradeColumnRequest;
use App\Http\Requests\GradeColumns\UpdateGradeColumnRequest;
use App\Services\GradeColumns\StoreGradeColumnService;
use App\Services\GradeColumns\UpdateGradeColumnService;
use App\Services\GradeColumns\DeleteGradeColumnService;
use App\Traits\AuthorizesRedirect;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GradeColumnController extends Controller
{
    use AuthorizesRequests;
    use AuthorizesRedirect;

    protected function currentUser(): User
    {
        return Auth::user();
    }

    public function index(SectionSubjectTeacher $sectionSubjectTeacher): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', GradeColumn::class, function () use ($sectionSubjectTeacher) {
            $sectionSubjectTeacher->load([
                'section.academicPeriod',
                'subject',
                'teacher.user',
                'gradeColumns' => fn($q) => $q->orderBy('display_order'),
            ]);

            $totalWeight = $sectionSubjectTeacher->getTotalWeight();
            $remainingWeight = $sectionSubjectTeacher->getRemainingWeight();
            $isConfigurationComplete = $sectionSubjectTeacher->isConfigurationComplete();

            return view('grade-columns.index', compact(
                'sectionSubjectTeacher',
                'totalWeight',
                'remainingWeight',
                'isConfigurationComplete'
            ));
        });
    }

    public function store(
        StoreGradeColumnRequest $request,
        StoreGradeColumnService $storeService,
        SectionSubjectTeacher $sectionSubjectTeacher
    ): RedirectResponse {
        return $this->authorizeOrRedirect('create', [GradeColumn::class, $sectionSubjectTeacher], function () use ($request, $storeService, $sectionSubjectTeacher) {
            try {
                $storeService->handle($sectionSubjectTeacher, $request->validated());

                return redirect()
                    ->route('grade-columns.index', $sectionSubjectTeacher)
                    ->with('success', '¡Evaluación registrada correctamente!');
            } catch (\Exception $e) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', $e->getMessage());
            }
        });
    }

    public function update(
        UpdateGradeColumnRequest $request,
        UpdateGradeColumnService $updateService,
        GradeColumn $gradeColumn
    ): RedirectResponse {
        return $this->authorizeOrRedirect('update', $gradeColumn, function () use ($request, $updateService, $gradeColumn) {
            try {
                $updateService->handle($gradeColumn, $request->validated());

                return redirect()
                    ->route('grade-columns.index', $gradeColumn->section_subject_teacher_id)
                    ->with('success', '¡Evaluación actualizada correctamente!');
            } catch (\Exception $e) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', $e->getMessage());
            }
        });
    }

    public function destroy(
        GradeColumn $gradeColumn,
        DeleteGradeColumnService $deleteService
    ): RedirectResponse {
        return $this->authorizeOrRedirect('delete', $gradeColumn, function () use ($gradeColumn, $deleteService) {
            $sstId = $gradeColumn->section_subject_teacher_id;

            try {
                $deleteService->handle($gradeColumn);

                return redirect()
                    ->route('grade-columns.index', $sstId)
                    ->with('success', '¡Evaluación eliminada correctamente!');
            } catch (\Exception $e) {
                return redirect()
                    ->back()
                    ->with('error', $e->getMessage());
            }
        });
    }
}