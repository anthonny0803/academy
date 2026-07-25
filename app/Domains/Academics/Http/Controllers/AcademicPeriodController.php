<?php

namespace App\Domains\Academics\Http\Controllers;

use App\Domains\Academics\Http\Requests\AcademicPeriods\StoreAcademicPeriodRequest;
use App\Domains\Academics\Http\Requests\AcademicPeriods\UpdateAcademicPeriodRequest;
use App\Domains\Academics\Models\AcademicPeriod;
use App\Domains\Academics\Repositories\AcademicPeriodRepository;
use App\Domains\Academics\Services\AcademicPeriods\AcademicPeriodStatsService;
use App\Domains\Academics\Services\AcademicPeriods\CloseAcademicPeriodService;
use App\Domains\Academics\Services\AcademicPeriods\DeleteAcademicPeriodService;
use App\Domains\Academics\Services\AcademicPeriods\StoreAcademicPeriodService;
use App\Domains\Academics\Services\AcademicPeriods\UpdateAcademicPeriodService;
use App\Domains\Shared\Contracts\RenderableDomainException;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Support\StatusFilter;
use App\Domains\Shared\Traits\AuthorizesRedirect;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicPeriodController extends Controller
{
    use AuthorizesRedirect;
    use AuthorizesRequests;

    public function __construct(
        private AcademicPeriodRepository $academicPeriodRepository
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', AcademicPeriod::class, function () use ($request) {
            $search = trim((string) $request->input('search', ''));
            $status = $request->input('status');

            $isActive = StatusFilter::toBool($status);

            $academicPeriods = $this->academicPeriodRepository
                ->paginateForListing($search, $isActive, 6)
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
        CloseAcademicPeriodService $closeService,
        AcademicPeriodStatsService $statsService
    ): View|RedirectResponse {
        return $this->authorizeOrRedirect('view', $academicPeriod, function () use ($academicPeriod, $closeService, $statsService) {
            $stats = $statsService->handle($academicPeriod);

            $closeValidation = null;
            $closePreview = null;

            if ($academicPeriod->isActive()) {
                $closeValidation = $closeService->validateForClose($academicPeriod);

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
            $results = $deleteService->handle($academicPeriod);

            $message = '¡Período académico eliminado correctamente!';

            if ($results['sections_deleted'] > 0) {
                $message .= " Se eliminaron {$results['sections_deleted']} secciones";
                if ($results['enrollments_deleted'] > 0) {
                    $message .= ", {$results['enrollments_deleted']} inscripciones";
                }
                if ($results['assignments_deleted'] > 0) {
                    $message .= " y {$results['assignments_deleted']} asignaciones agregadas";
                }
                $message .= ' asociadas.';
            }

            return redirect()->route('academic-periods.index')
                ->with('success', $message);
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

                $message = '¡Período cerrado correctamente! '.
                    "{$results['enrollments_completed']} inscripciones completadas ".
                    "({$results['enrollments_passed']} aprobados, {$results['enrollments_failed']} reprobados). ".
                    "{$results['sections_deactivated']} secciones desactivadas.";

                return redirect()->route('academic-periods.index')
                    ->with('success', $message);
            } catch (RenderableDomainException $e) {
                return redirect()->route('academic-periods.show', $academicPeriod)
                    ->with('error', $e->getMessage());
            }
        });
    }
}
