<?php

namespace App\Services\Sections;

use App\Models\Section;
use Illuminate\Support\Facades\DB;

class StoreSectionService
{
    public function handle(array $data): Section
    {
        return DB::transaction(function () use ($data) {
            return Section::create([
                'academic_period_id' => $data['academic_period_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'capacity' => $data['capacity'],
                'is_active' => true,
            ]);
        });
    }
}
