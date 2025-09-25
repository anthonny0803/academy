<?php

namespace App\Services;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class UpdateSubjectService
{
    public function handle(Subject $subject, array $data): Subject
    {
        return DB::transaction(function () use ($subject, $data) {

            $subject->update([
                'name'      => strtoupper($data['name']),
                'description' => strtoupper($data['description']),
            ]);

            $subject->save();

            return $subject;
        });
    }
}
