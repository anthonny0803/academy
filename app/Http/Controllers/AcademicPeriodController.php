<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AcademicPeriod;
use App\Http\Requests\AcademicPeriods\StoreAcademicPeriodRequest;
use App\Http\Requests\AcademicPeriods\UpdateAcademicPeriodRequest;
use App\Services\AcademicPeriods\StoreAcademicPeriodService;
use App\Services\AcademicPeriods\UpdateAcademicPeriodService;
use App\Services\AcademicPeriods\DeleteAcademicPeriodService;
use App\Traits\AuthorizesRedirect;
use App\Traits\CanToggleActivation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class AcademicPeriodController extends Controller
{
    use AuthorizesRequests;
    use AuthorizesRedirect;
    use CanToggleActivation;

    protected function currentUser(): User
    {
        return Auth::user();
    }

    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', AcademicPeriod::class, function () use ($request) {
            $search = trim((string) $request->input('search', ''));
            $status = $request->input('status');

            $academicPeriods = AcademicPeriod::query()
                ->when($search !== '', fn($q) => $q->search($search))
                ->when($status && $status !== 'Todos', function ($q) use ($status) {
                    $status === 'Activo' ? $q->active() : $q->inactive();
                })
                ->orderBy('start_date', 'desc')
                ->paginate(6)
                ->withQueryString();

            return view('academic-periods.index', compact('academicPeriods'));
        });
    }

    public function store(StoreAcademicPeriodRequest $request, StoreAcademicPeriodService $storeService): RedirectResponse
    {
        return $this->authorizeOrRedirect('create', AcademicPeriod::class, function () use ($request, $storeService) {
            $storeService->handle($request->validated());

            return redirect()->route('academic-periods.index')
                ->with('success', '¡Período académico registrado correctamente!');
        });
    }

    public function update(UpdateAcademicPeriodRequest $request, UpdateAcademicPeriodService $updateService, AcademicPeriod $academicPeriod): RedirectResponse
    {
        return $this->authorizeOrRedirect('update', $academicPeriod, function () use ($request, $updateService, $academicPeriod) {
            $updateService->handle($academicPeriod, $request->validated());

            return redirect()->route('academic-periods.index')
                ->with('success', '¡Período académico actualizado correctamente!');
        });
    }

    public function destroy(AcademicPeriod $academicPeriod, DeleteAcademicPeriodService $deleteService): RedirectResponse
    {
        return $this->authorizeOrRedirect('delete', $academicPeriod, function () use ($academicPeriod, $deleteService) {
            $deleteService->handle($academicPeriod);

            return redirect()->route('academic-periods.index')
                ->with('success', '¡Período académico eliminado correctamente!');
        });
    }

    public function toggleActivation(AcademicPeriod $academicPeriod): RedirectResponse
    {
        return $this->executeToggle($academicPeriod);
    }
}
