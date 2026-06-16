<?php

namespace App\Domains\Academics\Services\Subjects;

use App\Domains\Academics\Models\Subject;
use Illuminate\Support\Facades\DB;

class UpdateSubjectService
{
    public function handle(Subject $subject, array $data): Subject
    {
        return DB::transaction(function () use ($subject, $data) {
            $subject->update([
                'name' => $data['name'],
                'description' => $data['description'],
            ]);

            return $subject->fresh();
        });
    }
}
