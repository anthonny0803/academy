<?php

namespace App\Services\Students;

use App\Models\Student;
use App\Models\User;
use App\Models\Enrollment;
use App\Models\Representative;
use App\Enums\Role;
use App\Enums\EnrollmentStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StoreStudentService
{
    public function handle(Representative $representative, array $data): Student
    {
        return DB::transaction(function () use ($representative, $data) {
            $isSelfRepresented = $data['is_self_represented'] ?? false;

            if ($isSelfRepresented) {
                $user = $representative->user;

                if (!$user->hasRole(Role::Student->value)) {
                    $user->assignRole(Role::Student->value);
                }
            } else {

                $user = User::create([
                    'name' => $data['name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'] ?? null,
                    'password' => null,
                    'sex' => $data['sex'],
                    'document_id' => $data['document_id'] ?? null,
                    'birth_date' => $data['birth_date'],
                    'is_active' => true,
                ]);

                $user->assignRole(Role::Student->value);
            }

            $isChild = $user->getAge() < 18;
            $studentCode = $this->generateStudentCode($isChild);

            $student = Student::create([
                'user_id' => $user->id,
                'representative_id' => $representative->id,
                'student_code' => $studentCode,
                'relationship_type' => $data['relationship_type'],
                'is_active' => true,
            ]);

            Enrollment::create([
                'student_id' => $student->id,
                'section_id' => $data['section_id'],
                'status' => EnrollmentStatus::Active->value,
            ]);

            return $student->fresh(['user', 'representative', 'enrollments']);
        });
    }

    private function generateStudentCode(bool $isChild): string
    {
        $prefix = $isChild ? 'CHILD' : 'ADULT';

        $lastCode = Student::where('student_code', 'like', "{$prefix}%")
            ->orderBy('student_code', 'desc')
            ->value('student_code');

        $number = $lastCode
            ? (int) substr($lastCode, strlen($prefix)) + 1
            : 1;

        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
