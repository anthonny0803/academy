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

            // Si es auto-representado, usa el User del representative
            if ($isSelfRepresented) {
                $user = $representative->user;

                // Asignar rol Student si no lo tiene
                if (!$user->hasRole(Role::Student->value)) {
                    $user->assignRole(Role::Student->value);
                }
            } else {
                // Crear nuevo User para el estudiante
                $user = User::create([
                    'name' => $data['name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'] ?? null,
                    'password' => null,
                    'sex' => $data['sex'],
                    'is_active' => true,
                ]);

                $user->assignRole(Role::Student->value);
            }

            // Generar student_code
            $isChild = Carbon::parse($data['birth_date'])->age < 18;
            $studentCode = $this->generateStudentCode($isChild);

            // Crear Student
            $student = Student::create([
                'user_id' => $user->id,
                'representative_id' => $representative->id,
                'student_code' => $studentCode,
                'document_id' => $data['document_id'] ?? null,
                'relationship_type' => $data['relationship_type'],
                'birth_date' => $data['birth_date'],
                'is_active' => true,
            ]);

            // Crear Enrollment
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
