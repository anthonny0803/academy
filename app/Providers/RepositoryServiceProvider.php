<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * All repository contract bindings: interface => implementation.
     *
     * @var array<class-string, class-string>
     */
    public array $bindings = [];

    public function register(): void
    {
        //
    }
}
