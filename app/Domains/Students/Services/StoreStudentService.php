<?php

namespace App\Domains\Students\Services;

use App\Domains\Enrollments\Enums\EnrollmentStatus;
use App\Domains\Enrollments\Repositories\EnrollmentRepository;
use App\Domains\Identity\Enums\Role;
use App\Domains\Identity\Repositories\UserRepository;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Representatives\Services\SyncRepresentativeStatusService;
use App\Domains\Students\Enums\StudentSituation;
use App\Domains\Students\Models\Student;
use App\Domains\Students\Repositories\StudentRepository;
use Illuminate\Support\Facades\DB;

class StoreStudentService
{
    public function __construct(
        private SyncRepresentativeStatusService $syncRepresentativeStatus,
        private UserRepository $userRepository,
        private StudentRepository $studentRepository,
        private EnrollmentRepository $enrollmentRepository
    ) {}

    public function handle(Representative $representative, array $data): Student
    {
        return DB::transaction(function () use ($representative, $data) {
            $isSelfRepresented = $data['is_self_represented'] ?? false;

            if ($isSelfRepresented) {
                $user = $representative->user;

                if (! $user->hasRole(Role::Student->value)) {
                    $user->assignRole(Role::Student->value);
                }
            } else {
                $user = $this->userRepository->create([
                    'name' => $data['name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'] ?? null,
                    'password' => null,
                    'sex' => $data['sex'],
                    'document_id' => $data['document_id'] ?? null,
                    'birth_date' => $data['birth_date'],
                    'is_active' => false,
                ]);

                $user->assignRole(Role::Student->value);
            }

            $isChild = $user->age < 18;
            $studentCode = $this->generateStudentCode($isChild);

            $student = $this->studentRepository->create([
                'user_id' => $user->id,
                'representative_id' => $representative->id,
                'student_code' => $studentCode,
                'relationship_type' => $data['relationship_type'],
                'situation' => StudentSituation::Active,
                'is_active' => true,
            ]);

            $this->enrollmentRepository->create([
                'student_id' => $student->id,
                'section_id' => $data['section_id'],
                'status' => EnrollmentStatus::Active->value,
            ]);

            // Synchronize representative status (it is going to be active)
            $this->syncRepresentativeStatus->handle($representative->id);

            return $student->fresh(['user', 'representative', 'enrollments']);
        });
    }

    private function generateStudentCode(bool $isChild): string
    {
        $prefix = $isChild ? 'CHILD' : 'ADULT';

        $lastCode = $this->studentRepository->lastCodeForPrefix($prefix);

        $number = $lastCode
            ? (int) substr($lastCode, strlen($prefix)) + 1
            : 1;

        return $prefix.str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
