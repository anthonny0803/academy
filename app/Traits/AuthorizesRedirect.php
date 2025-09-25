<?php

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;

trait AuthorizesRedirect
{
    public function authorizeOrRedirect(string $ability, $model, \Closure $callback)
    {
        try {
            $this->authorize($ability, $model);
            return $callback();
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
