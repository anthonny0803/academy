<?php

namespace App\Services\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteUserService
{
    private function deleteRepresentativeWithoutStudents(User $user): void
    {
        if (!$user->representative->hasStudents()) {
            $user->representative->delete();
        }
    }

    public function handle(User $user): void
    {
        DB::transaction(function () use ($user) {
            if ($user->isRepresentative() && $user->representative) {
                $this->deleteRepresentativeWithoutStudents($user);
            }

            $user->delete();
        });
    }
}
