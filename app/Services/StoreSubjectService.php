<?php

namespace App\Services;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class StoreSubjectService
{
    public function handle(array $data): Subject
    {
        return DB::transaction(function () use ($data) {
            $subject = Subject::create([
                'name' => strtoupper($data['name']),
                'description' => strtoupper($data['description']),
            ]);

            return $subject;
        });
    }
}
