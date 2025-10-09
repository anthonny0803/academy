<?php

namespace App\Services\Teachers;

use App\Models\Teacher;
use App\Services\Shared\UpdateEmployeeService;
use Illuminate\Support\Facades\DB;

class UpdateTeacherService
{
    public function __construct(
        private UpdateEmployeeService $updateEmployeeService
    ) {}

    public function handle(Teacher $teacher, array $data): Teacher
    {
        return DB::transaction(function () use ($teacher, $data) {
            $this->updateEmployeeService->handle($teacher->user, $data);

            return $teacher->fresh('user');
        });
    }
}
