<?php

namespace App\Traits;

use App\Contracts\HasEntityName;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

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

        $status = $model->isActive() ? 'activado' : 'desactivado';
        $entityName = $this->getEntityName($model);

        return redirect()
            ->route($this->getShowRoute($model), $model)
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
        return str($base)->plural() . '.show';
    }
}
