<?php

namespace App\Traits;

use Illuminate\Support\Facades\Gate;

trait AuthorizesRedirect
{
    public function authorizeOrRedirect(string $ability, $model, \Closure $callback)
    {
        $response = Gate::inspect($ability, $model);

        if ($response->denied()) {
            return redirect()->back()->with('error', $response->message());
        }

        return $callback();
    }
}
