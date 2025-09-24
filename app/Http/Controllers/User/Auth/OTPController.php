<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use App\Models\User;
use App\Constants\ManageStatus;
use App\Notifications\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class OTPController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Show OTP login form
     */
    public function showOTPForm()
    {
        if (auth()->check()) {
            return redirect()->route('user.home');
        }

        $pageTitle = 'Phone Login';
        return view('user.auth.otp-login', compact('pageTitle'));
    }

    /**
     * Send OTP to phone number
     */
    public function sendOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $phoneNumber = $request->phone_number;

        // Validate phone number format
        if (!$this->firebaseService->validatePhoneNumber($phoneNumber)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number format. Please enter a valid phone number.'
            ], 422);
        }

        // Send OTP using Firebase
        $result = $this->firebaseService->sendOTP($phoneNumber);

        if ($result['success']) {
            // Store verification ID in session for verification
            session(['otp_verification_id' => $result['verification_id']]);
            session(['otp_phone_number' => $result['phone_number']]);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to ' . $result['phone_number']
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 400);
    }

    /**
     * Verify OTP and login user
     */
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp_code' => 'required|string|min:4|max:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $verificationId = session('otp_verification_id');
        $phoneNumber = session('otp_phone_number');

        if (!$verificationId || !$phoneNumber) {
            return response()->json([
                'success' => false,
                'message' => 'No OTP session found. Please request OTP again.'
            ], 400);
        }

        // Verify OTP using Firebase
        $result = $this->firebaseService->verifyOTP($verificationId, $request->otp_code, $phoneNumber);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }

        // Find or create user in Laravel
        $user = $this->findOrCreateUser($phoneNumber, $request->all());

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user account'
            ], 500);
        }

        // Login user
        Auth::login($user);

        // Clear OTP session
        session()->forget(['otp_verification_id', 'otp_phone_number']);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'redirect' => route('user.home')
        ]);
    }

    /**
     * Find or create user based on phone number
     */
    private function findOrCreateUser(string $phoneNumber, array $additionalData = [])
    {
        try {
            // Format phone number for database storage
            $formattedPhone = $this->firebaseService->formatPhoneNumber($phoneNumber);
            
            // Find existing user by phone number
            $user = User::where('mobile', $formattedPhone)->first();

            if ($user) {
                // Update last login
                $user->update(['last_login_at' => now()]);
                return $user;
            }

            // Create new user
            $setting = bs();
            $userData = [
                'firstname' => $additionalData['firstname'] ?? 'User',
                'lastname' => $additionalData['lastname'] ?? '',
                'email' => $additionalData['email'] ?? null,
                'username' => $this->generateUsername($phoneNumber),
                'mobile' => $formattedPhone,
                'password' => Hash::make(uniqid()), // Random password for phone auth users
                'country_code' => $additionalData['country_code'] ?? 'US',
                'country_name' => $additionalData['country_name'] ?? 'United States',
                'kc' => $setting->kc ? ManageStatus::NO : ManageStatus::YES,
                'ec' => $setting->ec ? ManageStatus::NO : ManageStatus::YES,
                'sc' => ManageStatus::YES, // Phone verified
                'ts' => ManageStatus::NO,
                'tc' => ManageStatus::YES,
                'phone_verified_at' => now(),
            ];

            $user = User::create($userData);

            // Create admin notification
            $adminNotification = new \App\Models\AdminNotification();
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'New user registered via phone';
            $adminNotification->click_url = urlPath('admin.user.index');
            $adminNotification->save();

            // Send welcome email to new user
            try {
                Log::info('Sending welcome email to new OTP user: ' . $user->email);
                $user->notify(new \App\Notifications\WelcomeNotification($user));
                Log::info('Welcome email sent successfully to: ' . $user->email);
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email to: ' . $user->email . ' - Error: ' . $e->getMessage());
                // Continue with user creation even if email fails
            }

            Log::info('New user created via phone authentication: ' . $user->id);

            return $user;

        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate unique username
     */
    private function generateUsername(string $phoneNumber): string
    {
        $base = 'user' . substr(preg_replace('/[^0-9]/', '', $phoneNumber), -6);
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Resend OTP
     */
    public function resendOTP(Request $request)
    {
        $phoneNumber = session('otp_phone_number');

        if (!$phoneNumber) {
            return response()->json([
                'success' => false,
                'message' => 'No phone number found in session'
            ], 400);
        }

        return $this->sendOTP($request->merge(['phone_number' => $phoneNumber]));
    }

    /**
     * Check if phone number exists
     */
    public function checkPhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number format'
            ], 422);
        }

        $phoneNumber = $this->firebaseService->formatPhoneNumber($request->phone_number);
        $user = User::where('mobile', $phoneNumber)->first();

        return response()->json([
            'success' => true,
            'exists' => $user ? true : false,
            'message' => $user ? 'Phone number is registered' : 'Phone number not found'
        ]);
    }
}
