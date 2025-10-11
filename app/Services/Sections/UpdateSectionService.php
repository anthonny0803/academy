<?php

namespace App\Services\Sections;

use App\Models\Section;
use Illuminate\Support\Facades\DB;

class UpdateSectionService
{
    public function handle(Section $section, array $data): Section
    {
        return DB::transaction(function () use ($section, $data) {
            $section->update([
                'academic_period_id' => $data['academic_period_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'capacity' => $data['capacity'],
            ]);

            return $section->fresh();
        });
    }
}
