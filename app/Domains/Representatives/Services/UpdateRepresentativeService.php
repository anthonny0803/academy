<?php

namespace App\Domains\Representatives\Services;

use App\Domains\Identity\Repositories\UserRepository;
use App\Domains\Representatives\Models\Representative;
use Illuminate\Support\Facades\DB;

class UpdateRepresentativeService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function handle(Representative $representative, array $data): array
    {
        return DB::transaction(function () use ($representative, $data) {
            $user = $representative->user;
            $userFieldsIgnored = false;

            if ($user->isEmployee()) {
                $userFieldsIgnored = isset($data['name'])
                    || isset($data['last_name'])
                    || isset($data['email'])
                    || isset($data['sex']);

                unset($data['name'], $data['last_name'], $data['email'], $data['sex']);
            }

            $userFields = array_intersect_key($data, array_flip([
                'name',
                'last_name',
                'email',
                'sex',
                'document_id',
                'birth_date',
                'phone',
                'address',
                'occupation',
            ]));

            if (! empty($userFields)) {
                $this->userRepository->update($user, $userFields);
            }

            return [
                'representative' => $representative->fresh(['user']),
                'userFieldsIgnored' => $userFieldsIgnored,
            ];
        });
    }
}
