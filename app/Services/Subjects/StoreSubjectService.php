<?php

namespace App\Services\Subjects;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class StoreSubjectService
{
    public function handle(array $data): Subject
    {
        return DB::transaction(function () use ($data) {
            return Subject::create([
                'name' => $data['name'],
                'description' => $data['description'],
                'is_active' => true,
            ]);
        });
    }
}
