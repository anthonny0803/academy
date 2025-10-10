<?php

namespace App\Services\Subjects;

use App\Models\Subject;
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
