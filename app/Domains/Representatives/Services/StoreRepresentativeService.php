<?php

namespace App\Domains\Representatives\Services;

use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Repositories\UserRepository;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Representatives\Repositories\RepresentativeRepository;
use Illuminate\Support\Facades\DB;

class StoreRepresentativeService
{
    public function __construct(
        private UserRepository $userRepository,
        private RepresentativeRepository $representativeRepository
    ) {}

    public function handle(array $data): Representative
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepository->create([
                'name' => $data['name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'sex' => $data['sex'],
                'document_id' => $data['document_id'],
                'birth_date' => $data['birth_date'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'occupation' => $data['occupation'],
                'is_active' => false,
            ]);

            $user->assignRole(Role::Representative->value);

            $representative = $this->representativeRepository->create([
                'user_id' => $user->id,
                'is_active' => false, // Inactive until store a student associated
            ]);

            return $representative->fresh(['user']);
        });
    }
}
