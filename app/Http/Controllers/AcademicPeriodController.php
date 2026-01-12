<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AcademicPeriod;
use App\Http\Requests\AcademicPeriods\StoreAcademicPeriodRequest;
use App\Http\Requests\AcademicPeriods\UpdateAcademicPeriodRequest;
use App\Services\AcademicPeriods\StoreAcademicPeriodService;
use App\Services\AcademicPeriods\UpdateAcademicPeriodService;
use App\Services\AcademicPeriods\DeleteAcademicPeriodService;
use App\Services\AcademicPeriods\CloseAcademicPeriodService;
use App\Traits\AuthorizesRedirect;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class AcademicPeriodController extends Controller
{
    use AuthorizesRequests;
    use AuthorizesRedirect;

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

    /**
     * Vista de detalle del período académico
     * Muestra estadísticas y permite acceder al cierre
     */
    public function show(
        AcademicPeriod $academicPeriod,
        CloseAcademicPeriodService $closeService
    ): View|RedirectResponse {
        return $this->authorizeOrRedirect('view', $academicPeriod, function () use ($academicPeriod, $closeService) {
            // Cargar relaciones necesarias
            $academicPeriod->load([
                'sections' => fn($q) => $q->withCount([
                    'enrollments',
                    'enrollments as active_enrollments_count' => fn($q) => $q->where('status', 'activo'),
                    'enrollments as completed_enrollments_count' => fn($q) => $q->where('status', 'completado'),
                ]),
            ]);

            // Estadísticas generales
            $stats = [
                'total_sections' => $academicPeriod->sections->count(),
                'active_sections' => $academicPeriod->sections->where('is_active', true)->count(),
                'total_enrollments' => $academicPeriod->sections->sum('enrollments_count'),
                'active_enrollments' => $academicPeriod->sections->sum('active_enrollments_count'),
                'completed_enrollments' => $academicPeriod->sections->sum('completed_enrollments_count'),
            ];

            // Validación para cierre (solo si está activo)
            $closeValidation = null;
            $closePreview = null;
            
            if ($academicPeriod->isActive()) {
                $closeValidation = $closeService->validateForClose($academicPeriod);
                
                // Si puede cerrar, obtener preview
                if ($closeValidation['can_close']) {
                    $closePreview = $closeService->getClosePreview($academicPeriod);
                }
            }

            return view('academic-periods.show', compact(
                'academicPeriod',
                'stats',
                'closeValidation',
                'closePreview'
            ));
        });
    }

    /*public function store(StoreAcademicPeriodRequest $request, StoreAcademicPeriodService $storeService): RedirectResponse
    {
        return $this->authorizeOrRedirect('create', AcademicPeriod::class, function () use ($request, $storeService) {
            $storeService->handle($request->validated());

            return redirect()->route('academic-periods.index')
                ->with('success', '¡Período académico registrado correctamente!');
        });
    }*/

        public function store(StoreAcademicPeriodRequest $request, StoreAcademicPeriodService $storeService): RedirectResponse
{
    // TEST DIRECTO - sin service, sin transacción
    \App\Models\AcademicPeriod::create([
        'name' => 'TEST DIRECTO',
        'start_date' => '2025-01-01',
        'end_date' => '2025-12-31',
        'is_active' => true,
        'is_promotable' => true,
        'is_transferable' => true,
    ]);
    
    dd('Insertado - revisa Neon');
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

    /**
     * Cerrar período académico
     * Acción masiva que completa todas las inscripciones activas
     */
    public function close(
        AcademicPeriod $academicPeriod,
        CloseAcademicPeriodService $closeService
    ): RedirectResponse {
        return $this->authorizeOrRedirect('close', $academicPeriod, function () use ($academicPeriod, $closeService) {
            try {
                $results = $closeService->handle($academicPeriod);

                $message = "¡Período cerrado correctamente! " .
                    "{$results['enrollments_completed']} inscripciones completadas " .
                    "({$results['enrollments_passed']} aprobados, {$results['enrollments_failed']} reprobados). " .
                    "{$results['sections_deactivated']} secciones desactivadas.";

                return redirect()->route('academic-periods.index')
                    ->with('success', $message);
            } catch (\Exception $e) {
                return redirect()->route('academic-periods.show', $academicPeriod)
                    ->with('error', $e->getMessage());
            }
        });
    }
}