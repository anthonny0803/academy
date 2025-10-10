<?php

namespace App\Services\Subjects;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class DeleteSubjectService
{
    public function handle(Subject $subject): void
    {
        DB::transaction(function () use ($subject) {
            $subject->delete();
        });
    }
}
