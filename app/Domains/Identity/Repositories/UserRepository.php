<?php

namespace App\Domains\Identity\Repositories;

use App\Domains\Identity\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepository
{
    public function paginateEmployees(string $search, ?bool $isActive, ?string $role, int $perPage = 6): LengthAwarePaginator;

    public function paginateForRoleAssignment(string $search, int $perPage = 6): LengthAwarePaginator;

    public function findByCredentials(string $documentId, string $birthDate, array $with = []): ?User;

    public function findByEmail(string $email): ?User;

    public function create(array $attributes): User;

    public function update(User $user, array $attributes): User;

    public function delete(User $user): void;
}
