<?php

namespace App\Domains\Academics\Services\Subjects;

use App\Domains\Academics\Models\Subject;
use App\Domains\Academics\Repositories\SubjectRepository;
use Illuminate\Support\Facades\DB;

class UpdateSubjectService
{
    public function __construct(
        private SubjectRepository $subjectRepository
    ) {}

    public function handle(Subject $subject, array $data): Subject
    {
        return DB::transaction(function () use ($subject, $data) {
            return $this->subjectRepository->update($subject, [
                'name' => $data['name'],
                'description' => $data['description'],
            ])->fresh();
        });
    }
}
