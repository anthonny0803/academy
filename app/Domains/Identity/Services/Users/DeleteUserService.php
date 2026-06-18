<?php

namespace App\Domains\Identity\Services\Users;

use App\Domains\Identity\Models\User;
use Illuminate\Support\Facades\DB;

class DeleteUserService
{
    public function handle(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->syncRoles([]);
            $user->delete();
        });
    }
}
