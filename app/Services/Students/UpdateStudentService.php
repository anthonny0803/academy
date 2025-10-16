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

            if ($student->isSelfRepresented() && isset($data['relationship_type'])) {
                unset($data['relationship_type']);
            }

            $userFields = array_intersect_key($data, array_flip([
                'name',
                'last_name',
                'email',
                'sex',
                'document_id',
                'birth_date',
            ]));

            if (!empty($userFields)) {
                $user->update($userFields);
            }

            $studentFields = array_intersect_key($data, array_flip([
                'relationship_type',
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
