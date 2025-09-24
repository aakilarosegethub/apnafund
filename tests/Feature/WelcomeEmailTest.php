<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WelcomeEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_welcome_email_is_sent_after_business_registration()
    {
        Notification::fake();

        // Create a test user with business information
        $userData = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@example.com',
            'username' => 'johndoe',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'mobile' => '1234567890',
            'mobile_code' => '+1',
            'country_code' => 'US',
            'country' => 'United States',
            'business_name' => 'Test Business',
            'business_type' => 'Startup',
            'industry' => 'Technology'
        ];

        // Create user
        $user = User::create([
            'firstname' => $userData['firstname'],
            'lastname' => $userData['lastname'],
            'email' => $userData['email'],
            'username' => $userData['username'],
            'password' => bcrypt($userData['password']),
            'mobile' => $userData['mobile_code'] . $userData['mobile'],
            'country_code' => $userData['country_code'],
            'country_name' => $userData['country'],
            'business_name' => $userData['business_name'],
            'business_type' => $userData['business_type'],
            'industry' => $userData['industry'],
            'kc' => 0,
            'ec' => 0,
            'sc' => 0,
            'ts' => 0,
            'tc' => 1
        ]);

        // Send welcome email
        $user->sendEmailVerificationNotification();

        // Assert that the welcome notification was sent
        Notification::assertSentTo($user, WelcomeNotification::class);
    }

    public function test_welcome_email_contains_business_information()
    {
        $user = new User([
            'firstname' => 'Jane',
            'lastname' => 'Smith',
            'email' => 'jane.smith@example.com',
            'username' => 'janesmith',
            'business_name' => 'Smith Enterprises',
            'business_type' => 'Corporation',
            'industry' => 'Finance'
        ]);

        $notification = new WelcomeNotification($user);
        $mailMessage = $notification->toMail($user);

        // Check if the mail message contains business information
        $this->assertStringContainsString('Smith Enterprises', $mailMessage->render());
        $this->assertStringContainsString('Corporation', $mailMessage->render());
        $this->assertStringContainsString('Finance', $mailMessage->render());
    }
}
