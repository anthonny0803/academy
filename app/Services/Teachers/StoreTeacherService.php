<?php

namespace App\Services\Teachers;

use App\Domains\Identity\Enums\Role;
use App\Models\Teacher;
use App\Shared\Services\CreateEmployeeService;
use Illuminate\Support\Facades\DB;

class StoreTeacherService
{
    public function __construct(
        private CreateEmployeeService $createEmployeeService
    ) {}

    public function handle(array $data): Teacher
    {
        return DB::transaction(function () use ($data) {
            $user = $this->createEmployeeService->handle(
                data: $data,
                role: Role::Teacher->value,
                isActive: false
            );

            $teacher = Teacher::create([
                'user_id' => $user->id,
                'is_active' => true,
            ]);

            return $teacher;
        });
    }
}
