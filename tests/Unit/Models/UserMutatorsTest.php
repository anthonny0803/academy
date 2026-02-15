<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class UserMutatorsTest extends TestCase
{
    public function test_name_is_uppercased_and_trimmed(): void
    {
        $user = new User();
        $user->name = '  carlos garcia  ';

        $this->assertSame('CARLOS GARCIA', $user->name);
    }

    public function test_last_name_is_uppercased_and_trimmed(): void
    {
        $user = new User();
        $user->last_name = '  lopez martinez  ';

        $this->assertSame('LOPEZ MARTINEZ', $user->last_name);
    }

    public function test_email_is_lowercased_and_trimmed(): void
    {
        $user = new User();
        $user->email = '  Carlos.Lopez@Gmail.COM  ';

        $this->assertSame('carlos.lopez@gmail.com', $user->email);
    }

    public function test_email_empty_becomes_null(): void
    {
        $user = new User();
        $user->email = '';

        $this->assertNull($user->email);
    }

    public function test_email_null_stays_null(): void
    {
        $user = new User();
        $user->email = null;

        $this->assertNull($user->email);
    }

    public function test_document_id_removes_special_chars_and_uppercases(): void
    {
        $user = new User();
        $user->document_id = '12.345.678-a';

        $this->assertSame('12345678A', $user->document_id);
    }

    public function test_document_id_nie_format(): void
    {
        $user = new User();
        $user->document_id = 'x-1234567-b';

        $this->assertSame('X1234567B', $user->document_id);
    }

    public function test_document_id_null_stays_null(): void
    {
        $user = new User();
        $user->document_id = null;

        $this->assertNull($user->document_id);
    }

    public function test_phone_keeps_only_digits(): void
    {
        $user = new User();
        $user->phone = '+34 612 345 678';

        $this->assertSame('34612345678', $user->phone);
    }

    public function test_phone_null_stays_null(): void
    {
        $user = new User();
        $user->phone = null;

        $this->assertNull($user->phone);
    }

    public function test_address_is_uppercased_and_trimmed(): void
    {
        $user = new User();
        $user->address = '  calle mayor 10, madrid  ';

        $this->assertSame('CALLE MAYOR 10, MADRID', $user->address);
    }

    public function test_address_null_stays_null(): void
    {
        $user = new User();
        $user->address = null;

        $this->assertNull($user->address);
    }

    public function test_occupation_is_uppercased_and_trimmed(): void
    {
        $user = new User();
        $user->occupation = '  ingeniero  ';

        $this->assertSame('INGENIERO', $user->occupation);
    }

    public function test_occupation_null_stays_null(): void
    {
        $user = new User();
        $user->occupation = null;

        $this->assertNull($user->occupation);
    }

    public function test_full_name_accessor(): void
    {
        $user = new User();
        $user->name = 'Carlos';
        $user->last_name = 'Garcia';

        $this->assertSame('CARLOS GARCIA', $user->full_name);
    }

    public function test_full_name_trims_when_last_name_empty(): void
    {
        $user = new User();
        $user->name = 'Carlos';
        $user->last_name = '';

        $this->assertSame('CARLOS', $user->full_name);
    }

    public function test_age_returns_correct_value(): void
    {
        $user = new User();
        $user->birth_date = Carbon::now()->subYears(30);

        $this->assertSame(30, $user->age);
    }

    public function test_age_returns_null_without_birth_date(): void
    {
        $user = new User();

        $this->assertNull($user->age);
    }

    public function test_is_developer_false_by_default(): void
    {
        $user = new User();

        $this->assertFalse($user->isDeveloper());
    }

    public function test_is_developer_true_when_set(): void
    {
        $user = new User();
        $user->is_developer = true;

        $this->assertTrue($user->isDeveloper());
    }

    public function test_is_male(): void
    {
        $user = new User();
        $user->sex = 'Masculino';

        $this->assertTrue($user->isMale());
        $this->assertFalse($user->isFemale());
    }

    public function test_is_female(): void
    {
        $user = new User();
        $user->sex = 'Femenino';

        $this->assertTrue($user->isFemale());
        $this->assertFalse($user->isMale());
    }
}
