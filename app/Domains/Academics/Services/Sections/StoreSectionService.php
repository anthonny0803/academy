<?php

namespace App\Domains\Academics\Services\Sections;

use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Repositories\SectionRepository;
use Illuminate\Support\Facades\DB;

class StoreSectionService
{
    public function __construct(
        private SectionRepository $sectionRepository
    ) {}

    public function handle(array $data): Section
    {
        return DB::transaction(function () use ($data) {
            return $this->sectionRepository->create([
                'academic_period_id' => $data['academic_period_id'],
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'capacity' => $data['capacity'],
                'is_active' => true,
            ]);
        });
    }
}
