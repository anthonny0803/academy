<?php

namespace App\Domains\Academics\Services\Teachers;

use App\Domains\Academics\Models\Teacher;
use App\Domains\Academics\Repositories\TeacherRepository;
use App\Domains\Identity\Enums\Role;
use App\Domains\Shared\Services\CreateEmployeeService;
use Illuminate\Support\Facades\DB;

class StoreTeacherService
{
    public function __construct(
        private CreateEmployeeService $createEmployeeService,
        private TeacherRepository $teacherRepository
    ) {}

    public function handle(array $data): Teacher
    {
        return DB::transaction(function () use ($data) {
            $user = $this->createEmployeeService->handle(
                data: $data,
                role: Role::Teacher->value,
                isActive: false
            );

            return $this->teacherRepository->create([
                'user_id' => $user->id,
                'is_active' => true,
            ]);
        });
    }
}
