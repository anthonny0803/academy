<?php

namespace App\Services\Sections;

use App\Models\Section;
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
