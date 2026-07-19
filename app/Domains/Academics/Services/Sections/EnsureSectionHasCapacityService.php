<?php

namespace App\Domains\Academics\Services\Sections;

use App\Domains\Academics\Exceptions\SectionFullException;
use App\Domains\Academics\Models\Section;
use App\Domains\Academics\Repositories\SectionRepository;

class EnsureSectionHasCapacityService
{
    public function __construct(
        private SectionRepository $sectionRepository
    ) {}

    /**
     * Must run inside an active DB transaction: the row lock is the
     * serialization point that keeps the enrollment count stable until commit.
     */
    public function handle(string $sectionId): Section
    {
        $section = $this->sectionRepository->findOrFailForUpdate($sectionId);

        if ($section->isFull()) {
            throw SectionFullException::forSection($section);
        }

        return $section;
    }
}
