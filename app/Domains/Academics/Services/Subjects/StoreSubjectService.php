<?php

namespace App\Domains\Academics\Services\Subjects;

use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Repositories\SubjectRepository;
use Illuminate\Support\Facades\DB;

class StoreSubjectService
{
    public function __construct(
        private SubjectRepository $subjectRepository
    ) {}

    public function handle(array $data): Subject
    {
        return DB::transaction(function () use ($data) {
            return $this->subjectRepository->create([
                'name' => $data['name'],
                'description' => $data['description'],
                'is_active' => true,
            ]);
        });
    }
}
