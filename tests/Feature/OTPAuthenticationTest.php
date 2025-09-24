<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OTPAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock Firebase service for testing
        $this->mock(FirebaseService::class, function ($mock) {
            $mock->shouldReceive('validatePhoneNumber')
                 ->andReturn(true);
            $mock->shouldReceive('formatPhoneNumber')
                 ->andReturn('+1234567890');
            $mock->shouldReceive('sendOTP')
                 ->andReturn([
                     'success' => true,
                     'message' => 'OTP sent successfully',
                     'verification_id' => 'test-verification-id',
                     'phone_number' => '+1234567890'
                 ]);
            $mock->shouldReceive('verifyOTP')
                 ->andReturn([
                     'success' => true,
                     'message' => 'OTP verified successfully',
                     'user_id' => 'test-user-id',
                     'phone_number' => '+1234567890'
                 ]);
        });
    }

    public function test_can_show_otp_login_form()
    {
        $response = $this->get(route('user.otp.login'));
        
        $response->assertStatus(200);
        $response->assertViewIs('user.auth.otp-login');
    }

    public function test_can_send_otp()
    {
        $response = $this->postJson(route('user.otp.send'), [
            'phone_number' => '+1234567890'
        ]);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_can_verify_otp()
    {
        // Mock session data
        session(['otp_verification_id' => 'test-verification-id']);
        session(['otp_phone_number' => '+1234567890']);
        
        $response = $this->postJson(route('user.otp.verify'), [
            'otp_code' => '123456'
        ]);
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_phone_validation_works()
    {
        $response = $this->postJson(route('user.otp.send'), [
            'phone_number' => 'invalid-phone'
        ]);
        
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['phone_number']);
    }

    public function test_otp_verification_requires_session()
    {
        $response = $this->postJson(route('user.otp.verify'), [
            'otp_code' => '123456'
        ]);
        
        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }
}
