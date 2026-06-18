<?php

namespace App\Domains\Academics\Services\Sections;

use App\Domains\Academics\Models\Section;
use Illuminate\Support\Facades\DB;

class DeleteSectionService
{
    public function handle(Section $section): void
    {
        DB::transaction(function () use ($section) {
            $section->delete();
        });
    }
}
