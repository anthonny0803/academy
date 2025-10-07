<?php

namespace App\Services\Teachers;

use App\Enums\Role;
use App\Models\Teacher;
use App\Services\Shared\CreatePersonService;
use Illuminate\Support\Facades\DB;

class StoreTeacherService
{
    public function __construct(
        private CreatePersonService $createPersonService
    ) {}

    public function handle(array $data): Teacher
    {
        return DB::transaction(function () use ($data) {
            $user = $this->createPersonService->handle(
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
