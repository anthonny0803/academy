<?php

namespace App\Domains\Identity\Repositories;

use App\Domains\Identity\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentUserRepository implements UserRepository
{
    public function paginateEmployees(string $search, ?bool $isActive, ?string $role, int $perPage = 6): LengthAwarePaginator
    {
        return User::query()
            ->employees()
            ->search($search)
            ->when(! is_null($isActive), fn ($query) => $isActive ? $query->active() : $query->inactive())
            ->when($role, fn ($query) => $query->withRole($role))
            ->with('roles')
            ->orderByName()
            ->paginate($perPage);
    }

    public function paginateForRoleAssignment(string $search, int $perPage = 6): LengthAwarePaginator
    {
        return User::query()
            ->search($search)
            ->with(['roles', 'teacher', 'representative', 'student'])
            ->orderByName()
            ->paginate($perPage);
    }

    public function findByCredentials(string $documentId, string $birthDate, array $with = []): ?User
    {
        return User::where('document_id', $documentId)
            ->whereDate('birth_date', $birthDate)
            ->with($with)
            ->first();
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $attributes): User
    {
        return User::create($attributes);
    }

    public function update(User $user, array $attributes): User
    {
        $user->update($attributes);

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
