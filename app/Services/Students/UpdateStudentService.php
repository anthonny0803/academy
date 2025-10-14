<?php

namespace App\Services\Students;

use App\Models\Student;
use Illuminate\Support\Facades\DB;

class UpdateStudentService
{
    public function handle(Student $student, array $data): array
    {
        return DB::transaction(function () use ($student, $data) {
            $user = $student->user;
            $userFieldsIgnored = false;

            if ($user->isEmployee()) {
                $userFieldsIgnored = isset($data['name'])
                    || isset($data['last_name'])
                    || isset($data['email'])
                    || isset($data['sex']);

                unset($data['name'], $data['last_name'], $data['email'], $data['sex']);
            }

            $userFields = array_intersect_key($data, array_flip(['name', 'last_name', 'email', 'sex']));
            if (!empty($userFields)) {
                $user->update($userFields);
            }

            $studentFields = array_intersect_key($data, array_flip([
                'document_id',
                'relationship_type',
                'birth_date',
            ]));

            if (!empty($studentFields)) {
                $student->update($studentFields);
            }

            return [
                'student' => $student->fresh(['user', 'representative']),
                'userFieldsIgnored' => $userFieldsIgnored,
            ];
        });
    }
}
