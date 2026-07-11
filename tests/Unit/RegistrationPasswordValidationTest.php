<?php

namespace Tests\Unit;

use App\Constants\WeakPasswords;
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../app/Constants/WeakPasswords.php';
require_once __DIR__.'/../../app/Constants/RegistrationLimits.php';
require_once __DIR__.'/../../app/Http/Helpers/helpers.php';

class RegistrationPasswordValidationTest extends TestCase
{
    /** @dataProvider rejectedPasswordProvider */
    public function test_rejects_weak_passwords(string $password): void
    {
        $this->assertNotEmpty(registrationPasswordErrors($password, 'user@example.com', 'testuser', 'Jane Doe'));
    }

    /** @return array<string, array{0: string}> */
    public static function rejectedPasswordProvider(): array
    {
        return [
            'password' => ['password'],
            'password123' => ['password123'],
            'qwerty123' => ['qwerty123'],
            '12345678' => ['12345678'],
            'abc12345' => ['abc12345'],
        ];
    }

    /** @dataProvider acceptedPasswordProvider */
    public function test_accepts_strong_passwords(string $password): void
    {
        $this->assertSame([], registrationPasswordErrors($password, 'user@example.com', 'testuser', 'Jane Doe'));
    }

    /** @return array<string, array{0: string}> */
    public static function acceptedPasswordProvider(): array
    {
        return [
            'MyPass@123' => ['MyPass@123'],
            'Secure#2025' => ['Secure#2025'],
            'CrowdFund!99' => ['CrowdFund!99'],
        ];
    }

    public function test_rejects_password_containing_email_local_part(): void
    {
        $errors = registrationPasswordErrors('Myuser@Example1!', 'user@example.com');

        $this->assertContains('Password must not contain your email address.', $errors);
    }

    public function test_common_password_helper_flags_known_values(): void
    {
        $this->assertTrue(WeakPasswords::isTooCommon('qwerty123'));
        $this->assertFalse(WeakPasswords::isTooCommon('MyPass@123'));
    }
}
