<?php

namespace App\Domains\Academics\Services\Sections;

use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Repositories\SectionRepository;
use Illuminate\Support\Facades\DB;

class DeleteSectionService
{
    public function __construct(
        private SectionRepository $sectionRepository
    ) {}

    public function handle(Section $section): void
    {
        DB::transaction(function () use ($section) {
            $this->sectionRepository->delete($section);
        });
    }
}
