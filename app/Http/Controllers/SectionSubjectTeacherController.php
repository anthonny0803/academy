<?php

namespace App\Http\Controllers;

use App\Domains\Academics\Models\SectionSubjectTeacher;
use App\Domains\Academics\Requests\SectionSubjectTeacher\StoreSectionSubjectTeacherRequest;
use App\Domains\Academics\Requests\SectionSubjectTeacher\UpdateSectionSubjectTeacherRequest;
use App\Domains\Academics\Services\SectionSubjectTeacher\DeleteSectionSubjectTeacherService;
use App\Domains\Academics\Services\SectionSubjectTeacher\StoreSectionSubjectTeacherService;
use App\Domains\Academics\Services\SectionSubjectTeacher\UpdateSectionSubjectTeacherService;
use App\Domains\Identity\Models\User;
use App\Shared\Traits\AuthorizesRedirect;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SectionSubjectTeacherController extends Controller
{
    use AuthorizesRedirect;
    use AuthorizesRequests;

    protected function currentUser(): User
    {
        return Auth::user();
    }

    public function store(
        StoreSectionSubjectTeacherRequest $request,
        StoreSectionSubjectTeacherService $storeService
    ): RedirectResponse {
        return $this->authorizeOrRedirect('create', SectionSubjectTeacher::class, function () use ($request, $storeService) {
            $sectionId = $request->validated()['section_id'];
            $storeService->handle($request->validated());

            return redirect()->route('sections.show', $sectionId)
                ->with('success', '¡Materia/Profesor asignado correctamente!');
        });
    }

    public function update(
        UpdateSectionSubjectTeacherRequest $request,
        UpdateSectionSubjectTeacherService $updateService,
        SectionSubjectTeacher $sectionSubjectTeacher
    ): RedirectResponse {
        return $this->authorizeOrRedirect('update', SectionSubjectTeacher::class, function () use ($request, $updateService, $sectionSubjectTeacher) {
            $updateService->handle($sectionSubjectTeacher, $request->validated());

            return redirect()->route('sections.show', $sectionSubjectTeacher->section_id)
                ->with('success', '¡Asignación actualizada correctamente!');
        });
    }

    public function destroy(
        SectionSubjectTeacher $sectionSubjectTeacher,
        DeleteSectionSubjectTeacherService $deleteService
    ): RedirectResponse {
        return $this->authorizeOrRedirect('delete', SectionSubjectTeacher::class, function () use ($sectionSubjectTeacher, $deleteService) {
            $sectionId = $sectionSubjectTeacher->section_id;
            $deleteService->handle($sectionSubjectTeacher);

            return redirect()->route('sections.show', $sectionId)
                ->with('success', '¡Asignación eliminada correctamente!');
        });
    }
}
