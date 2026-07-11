<?php

namespace Tests\Feature;

use App\Constants\RegistrationLimits;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationMaxLengthTest extends TestCase
{
    use RefreshDatabase;

    public function test_otp_signup_rejects_oversized_name_before_otp(): void
    {
        $longName = str_repeat('A', RegistrationLimits::NAME_MAX + 1);

        $email = 'maxlen-name-'.time().'@example.com';

        $response = $this->postJson(route('user.otp.send'), [
            'email' => $email,
            'name' => $longName,
            'password' => 'Valid1!Pass',
            'firstname' => 'User',
            'lastname' => 'User',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonPath('errors.name.0', 'Name must not exceed '.RegistrationLimits::NAME_MAX.' characters.');

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);
    }

    public function test_otp_signup_rejects_oversized_password_before_otp(): void
    {
        $longPassword = str_repeat('aA1!', 20);

        $email = 'maxlen-pass-'.time().'@example.com';

        $response = $this->postJson(route('user.otp.send'), [
            'email' => $email,
            'name' => 'Valid Name',
            'password' => $longPassword,
            'firstname' => 'User',
            'lastname' => 'User',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonPath('errors.password.0', 'Password must not exceed '.RegistrationLimits::PASSWORD_MAX.' characters.');

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);
    }

    public function test_otp_signup_accepts_valid_lengths_and_creates_unverified_user(): void
    {
        $email = 'valid-signup-'.time().'@example.com';

        $response = $this->postJson(route('user.otp.send'), [
            'email' => $email,
            'name' => 'Valid User',
            'password' => 'Valid1!Pass',
            'firstname' => 'User',
            'lastname' => 'User',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'firstname' => 'Valid',
            'lastname' => 'User',
        ]);
    }

    public function test_classic_register_rejects_oversized_first_name(): void
    {
        $response = $this->post(route('user.register'), [
            'firstname' => str_repeat('A', RegistrationLimits::NAME_PART_MAX + 1),
            'lastname' => 'User',
            'email' => 'classic-'.time().'@example.com',
            'mobile' => '3001234567',
            'password' => 'Valid1!Pass',
            'password_confirmation' => 'Valid1!Pass',
            'username' => 'user'.time(),
            'mobile_code' => '92',
            'country_code' => 'PK',
            'country' => 'Pakistan',
        ]);

        $response->assertSessionHasErrors(['firstname']);
        $this->assertGuest();
    }

    public function test_classic_register_rejects_oversized_password(): void
    {
        $longPassword = str_repeat('aA1!', 20);

        $response = $this->post(route('user.register'), [
            'firstname' => 'Valid',
            'lastname' => 'User',
            'email' => 'classic-pass-'.time().'@example.com',
            'mobile' => '3001234567',
            'password' => $longPassword,
            'password_confirmation' => $longPassword,
            'username' => 'user'.time(),
            'mobile_code' => '92',
            'country_code' => 'PK',
            'country' => 'Pakistan',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }
}
