<?php

namespace Tests\Concerns;

use Closure;
use Illuminate\Support\Facades\DB;

trait CountsQueries
{
    /**
     * @return list<array{query: string, bindings: array<int, mixed>, time: float}>
     */
    protected function recordQueries(Closure $callback): array
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        $callback();

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        return $queries;
    }

    protected function countQueries(Closure $callback): int
    {
        return count($this->recordQueries($callback));
    }
}
