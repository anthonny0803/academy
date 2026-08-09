<?php

namespace Tests\Feature\Students;

use App\Domains\Academics\Models\Section;
use App\Domains\Identity\Models\User;
use App\Domains\Representatives\Enums\RelationshipType;
use App\Domains\Representatives\Models\Representative;
use App\Domains\Shared\Enums\Sex;
use App\Domains\Students\Models\Student;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDocumentIdFormatTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The representative form accepts this, so the student forms must too:
     * a self-represented person carries one document across both profiles.
     */
    private const DOCUMENT_WITHOUT_FINAL_LETTER = '12345678';

    private const INVALID_DOCUMENTS = [
        'tooShort' => '123456',
        'twoFinalLetters' => '12345678AB',
        'lettersBeforeTheDigits' => 'ABC1234567',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
    }

    private function studentPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Pedro',
            'last_name' => 'Gomez',
            'email' => 'pedro.gomez@example.com',
            'sex' => Sex::Male->value,
            'document_id' => self::DOCUMENT_WITHOUT_FINAL_LETTER,
            'birth_date' => '2010-03-15',
            'relationship_type' => RelationshipType::Father->value,
            'section_id' => Section::factory()->create()->id,
        ], $overrides);
    }

    private function representativePayload(string $documentId): array
    {
        return [
            'name' => 'Ana',
            'last_name' => 'Perez',
            'email' => "ana.{$documentId}@example.com",
            'sex' => Sex::Female->value,
            'document_id' => $documentId,
            'birth_date' => '1985-06-01',
            'phone' => '123456789',
            'address' => 'Calle Falsa 123',
        ];
    }

    private function representativeWithDocument(string $documentId): Representative
    {
        $representative = Representative::factory()->create();
        $representative->user->update(['document_id' => $documentId]);

        return $representative;
    }

    public function test_a_representative_without_a_final_letter_can_be_registered_as_a_self_represented_student(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $representative = $this->representativeWithDocument(self::DOCUMENT_WITHOUT_FINAL_LETTER);
        $representativeUser = $representative->user;

        $response = $this->actingAs($supervisor)
            ->from(route('representatives.students.create', $representative))
            ->post(route('representatives.students.store', $representative), $this->studentPayload([
                'is_self_represented' => true,
                'relationship_type' => RelationshipType::SelfRepresented->value,
                'email' => $representativeUser->email,
                'document_id' => $representativeUser->document_id,
            ]));

        $response->assertSessionHasNoErrors();

        $student = Student::firstWhere('representative_id', $representative->id);
        $this->assertNotNull($student);
        $this->assertSame($representativeUser->id, $student->user_id);
        $response->assertRedirect(route('students.show', $student));
    }

    public function test_a_student_whose_document_has_no_final_letter_can_be_updated(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $student = Student::factory()->create();
        $student->user->update(['document_id' => self::DOCUMENT_WITHOUT_FINAL_LETTER]);

        $response = $this->actingAs($supervisor)
            ->from(route('students.edit', $student))
            ->patch(route('students.update', $student), [
                'name' => 'Pedro',
                'document_id' => self::DOCUMENT_WITHOUT_FINAL_LETTER,
                'birth_date' => '2010-03-15',
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('students.show', $student));
        $response->assertSessionHas('success', '¡Estudiante actualizado correctamente!');

        $this->assertSame('PEDRO', $student->user->fresh()->name);
    }

    public function test_the_student_store_still_rejects_genuinely_invalid_documents(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $representative = Representative::factory()->create();

        foreach (self::INVALID_DOCUMENTS as $documentId) {
            $response = $this->actingAs($supervisor)
                ->from(route('representatives.students.create', $representative))
                ->post(route('representatives.students.store', $representative), $this->studentPayload([
                    'document_id' => $documentId,
                ]));

            $response->assertSessionHasErrors('document_id');
        }

        $this->assertSame(0, Student::count());
    }

    public function test_the_student_update_still_rejects_genuinely_invalid_documents(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $student = Student::factory()->create();
        $originalDocument = $student->user->document_id;

        foreach (self::INVALID_DOCUMENTS as $documentId) {
            $response = $this->actingAs($supervisor)
                ->from(route('students.edit', $student))
                ->patch(route('students.update', $student), [
                    'document_id' => $documentId,
                    'birth_date' => '2010-03-15',
                ]);

            $response->assertSessionHasErrors('document_id');
        }

        $this->assertSame($originalDocument, $student->user->fresh()->document_id);
    }

    public function test_the_forms_ship_no_client_side_copy_of_the_field_rules(): void
    {
        $supervisor = User::factory()->supervisor()->create();
        $student = Student::factory()->create();
        $student->user->update(['document_id' => self::DOCUMENT_WITHOUT_FINAL_LETTER]);

        $forms = [
            route('students.edit', $student),
            route('representatives.students.create', $student->representative),
            route('representatives.create'),
        ];

        foreach ($forms as $form) {
            $response = $this->actingAs($supervisor)->get($form);

            $response->assertOk();
            $response->assertDontSee('pattern=', false);
        }
    }

    public function test_representative_creation_keeps_accepting_documents_with_and_without_a_final_letter(): void
    {
        $supervisor = User::factory()->supervisor()->create();

        foreach ([self::DOCUMENT_WITHOUT_FINAL_LETTER, '87654321B', 'X1234567C'] as $documentId) {
            $response = $this->actingAs($supervisor)
                ->from(route('representatives.create'))
                ->post(route('representatives.store'), $this->representativePayload($documentId));

            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('users', ['document_id' => $documentId]);
        }
    }
}
