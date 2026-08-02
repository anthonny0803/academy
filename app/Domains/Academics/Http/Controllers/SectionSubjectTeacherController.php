<?php

namespace App\Domains\Academics\Http\Controllers;

use App\Domains\Academics\Exceptions\TeacherNotQualifiedForSubjectException;
use App\Domains\Academics\Http\Requests\SectionSubjectTeacher\StoreSectionSubjectTeacherRequest;
use App\Domains\Academics\Http\Requests\SectionSubjectTeacher\UpdateSectionSubjectTeacherRequest;
use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Services\SectionSubjectTeacher\DeleteSectionSubjectTeacherService;
use App\Domains\Academics\Services\SectionSubjectTeacher\StoreSectionSubjectTeacherService;
use App\Domains\Academics\Services\SectionSubjectTeacher\UpdateSectionSubjectTeacherService;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\AuthorizesRedirect;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;

class SectionSubjectTeacherController extends Controller
{
    use AuthorizesRedirect;
    use AuthorizesRequests;

    public function store(
        StoreSectionSubjectTeacherRequest $request,
        StoreSectionSubjectTeacherService $storeService
    ): RedirectResponse {
        return $this->authorizeOrRedirect('create', SectionSubjectTeacher::class, function () use ($request, $storeService) {
            $data = $request->validated();

            try {
                $storeService->handle($data);
            } catch (TeacherNotQualifiedForSubjectException $e) {
                return back()
                    ->withErrors(['teacher_id' => $e->getMessage()])
                    ->withInput();
            }

            return redirect()->route('sections.show', $data['section_id'])
                ->with('success', '¡Materia/Profesor asignado correctamente!');
        });
    }

    public function update(
        UpdateSectionSubjectTeacherRequest $request,
        UpdateSectionSubjectTeacherService $updateService,
        SectionSubjectTeacher $sectionSubjectTeacher
    ): RedirectResponse {
        return $this->authorizeOrRedirect('update', $sectionSubjectTeacher, function () use ($request, $updateService, $sectionSubjectTeacher) {
            $updateService->handle($sectionSubjectTeacher, $request->validated());

            return redirect()->route('sections.show', $sectionSubjectTeacher->section_id)
                ->with('success', '¡Asignación actualizada correctamente!');
        });
    }

    public function destroy(
        SectionSubjectTeacher $sectionSubjectTeacher,
        DeleteSectionSubjectTeacherService $deleteService
    ): RedirectResponse {
        return $this->authorizeOrRedirect('delete', $sectionSubjectTeacher, function () use ($sectionSubjectTeacher, $deleteService) {
            $sectionId = $sectionSubjectTeacher->section_id;
            $deleteService->handle($sectionSubjectTeacher);

            return redirect()->route('sections.show', $sectionId)
                ->with('success', '¡Asignación eliminada correctamente!');
        });
    }
}
