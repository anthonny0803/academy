<?php

namespace Tests\Unit\Models;

use App\Enums\RelationshipType;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class StudentTest extends TestCase
{
    private function makeStudentWithUser(array $userAttrs = [], array $studentAttrs = []): Student
    {
        $user = new User(array_merge([
            'name' => 'Carlos',
            'last_name' => 'Garcia Lopez',
            'email' => 'carlos@ejemplo.com',
            'sex' => 'Masculino',
        ], $userAttrs));

        $user->birth_date = $userAttrs['birth_date'] ?? Carbon::now()->subYears(16);

        $student = new Student(array_merge([
            'student_code' => 'CHILD000001',
            'relationship_type' => RelationshipType::Father->value,
            'is_active' => true,
        ], $studentAttrs));

        $student->setRelation('user', $user);

        return $student;
    }

    // -- isChild --

    public function test_is_child_returns_true_for_minor(): void
    {
        $student = $this->makeStudentWithUser([
            'birth_date' => Carbon::now()->subYears(15),
        ]);

        $this->assertTrue($student->isChild());
    }

    public function test_is_child_returns_false_for_adult(): void
    {
        $student = $this->makeStudentWithUser([
            'birth_date' => Carbon::now()->subYears(18),
        ]);

        $this->assertFalse($student->isChild());
    }

    public function test_is_child_returns_true_when_no_birth_date(): void
    {
        $student = $this->makeStudentWithUser([
            'birth_date' => null,
        ]);

        $this->assertTrue($student->isChild());
    }

    // -- isSelfRepresented --

    public function test_is_self_represented_true(): void
    {
        $student = $this->makeStudentWithUser([], [
            'relationship_type' => RelationshipType::SelfRepresented->value,
        ]);

        $this->assertTrue($student->isSelfRepresented());
    }

    public function test_is_self_represented_false_for_father(): void
    {
        $student = $this->makeStudentWithUser([], [
            'relationship_type' => RelationshipType::Father->value,
        ]);

        $this->assertFalse($student->isSelfRepresented());
    }

    public function test_is_self_represented_false_for_mother(): void
    {
        $student = $this->makeStudentWithUser([], [
            'relationship_type' => RelationshipType::Mother->value,
        ]);

        $this->assertFalse($student->isSelfRepresented());
    }

    public function test_is_self_represented_false_for_legal_guardian(): void
    {
        $student = $this->makeStudentWithUser([], [
            'relationship_type' => RelationshipType::LegalGuardian->value,
        ]);

        $this->assertFalse($student->isSelfRepresented());
    }

    // -- Mutator --

    public function test_student_code_mutator_uppercases_and_trims(): void
    {
        $student = new Student();
        $student->student_code = '  child000001  ';

        $this->assertSame('CHILD000001', $student->student_code);
    }

    // -- Accessors proxy to User --

    public function test_full_name_proxies_to_user(): void
    {
        $student = $this->makeStudentWithUser([
            'name' => 'Maria',
            'last_name' => 'Fernandez',
        ]);

        $this->assertSame('MARIA FERNANDEZ', $student->full_name);
    }

    public function test_age_proxies_to_user(): void
    {
        $student = $this->makeStudentWithUser([
            'birth_date' => Carbon::now()->subYears(20),
        ]);

        $this->assertSame(20, $student->age);
    }

    public function test_situation_label_returns_default_when_null(): void
    {
        $student = new Student(['situation' => null]);

        $this->assertSame('Cursando', $student->situation_label);
    }

    // -- Activatable --

    public function test_is_active(): void
    {
        $active = new Student(['is_active' => true]);
        $this->assertTrue($active->isActive());

        $inactive = new Student(['is_active' => false]);
        $this->assertFalse($inactive->isActive());
    }
}
