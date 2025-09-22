<?php

namespace App\Traits;

use Illuminate\Auth\Access\AuthorizationException;

trait AuthorizesRedirect
{
    /**
     * Authorize an action and automatically redirect with an error if unauthorized.
     *
     * @param string $ability The ability or policy method to check
     * @param mixed $model The model or class for authorization
     * @param \Closure $callback The callback to execute if authorized
     * @return mixed Returns the callback result or redirects with an error
     */
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
