<?php

namespace App\Traits;

use App\Contracts\HasEntityName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

trait CanToggleActivation
{
    protected function executeToggle($model): RedirectResponse
    {
        $response = Gate::inspect('toggle', $model);

        if ($response->denied()) {
            return redirect()
                ->back()
                ->with('error', $response->message());
        }

        $model->toggleActivation();

        $status = $model->isActive() ? 'activado/a' : 'desactivado/a';
        $entityName = $this->getEntityName($model);
        $route = $this->getShowRoute($model);
        $params = $this->routeNeedsParameter($route)
            ? [strtolower(class_basename($model)) => $model]
            : [];

        return redirect()
            ->route($route, $params)
            ->with('success', "¡{$entityName} {$status} correctamente!");
    }

    protected function getEntityName($model): string
    {
        return $model instanceof HasEntityName
            ? $model->getEntityName()
            : 'Registro';
    }

    protected function getShowRoute($model): string
    {
        $base = strtolower(class_basename($model));
        $showRoute = str($base)->plural() . '.show';
        $indexRoute = str($base)->plural() . '.index';

        return Route::has($showRoute) ? $showRoute : $indexRoute;
    }

    protected function routeNeedsParameter(string $route): bool
    {
        return str_ends_with($route, '.show');
    }
}
