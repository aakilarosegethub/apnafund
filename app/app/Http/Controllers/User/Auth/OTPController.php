<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class OTPController extends Controller
{
    protected $firebaseService;

    public function __construct()
    {
        // FirebaseService is not injected in constructor
        // It will be resolved lazily only when needed for phone OTP
        // This allows email OTP to work without Firebase configuration
    }

    /**
     * Get FirebaseService instance (lazy load)
     */
    protected function getFirebaseService()
    {
        if (! $this->firebaseService) {
            try {
                $this->firebaseService = app(FirebaseService::class);
            } catch (\Exception $e) {
                Log::error('Firebase service not available: '.$e->getMessage());
                throw new \Exception('Phone OTP service is not available. Please use email registration instead.');
            }
        }

        return $this->firebaseService;
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
     * Send OTP to phone number or email
     */
    public function sendOTP(Request $request)
    {
        // Check if this is email-based registration or phone-based login
        if ($request->has('email') && $request->email) {
            // Email-based registration flow
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:191',
                'name' => 'required|string|max:100',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $email = strtolower(trim($request->email));

            // Check if email already exists
            $user = User::where('email', $email)->first();
            if ($user) {
                // If user exists but email is not verified, resend OTP
                if ($user->ec != ManageStatus::VERIFIED) {
                    try {
                        // Generate new OTP code
                        $otpCode = verificationCode(6);
                        $user->ver_code = $otpCode;
                        $user->ver_code_send_at = now();
                        $user->save();

                        // Send verification email
                        notify($user, 'EVER_CODE', [
                            'code' => $otpCode,
                        ], ['email']);

                        Log::info('Email OTP resent to existing user: '.$email);

                        return response()->json([
                            'success' => true,
                            'message' => 'Verification code has been sent to your email address.',
                            'email' => $email,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to resend email OTP: '.$e->getMessage());

                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to send verification code. Please try again.',
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Email address is already registered. Please use a different email or log in.',
                    ], 422);
                }
            }

            // Parse name into firstname/lastname
            $firstname = $request->firstname ?? 'User';
            $lastname = $request->lastname ?? '';

            // If firstname/lastname are defaults but name is provided, split name
            if (($firstname === 'User' && $lastname === 'User') || ($firstname === 'User' && empty($lastname))) {
                $nameParts = explode(' ', trim($request->name ?? ''), 2);
                if (count($nameParts) >= 1 && ! empty($nameParts[0])) {
                    $firstname = $nameParts[0];
                    $lastname = $nameParts[1] ?? '';
                }
            }

            // Generate username from email
            $username = $request->username ?? $this->generateUsernameFromEmail($email);

            // Check if username exists and make it unique
            while (User::where('username', $username)->exists()) {
                $username = $username.rand(100, 999);
            }

            // Generate OTP code
            $otpCode = verificationCode(6);

            // Get settings to check email verification status
            $setting = bs();
            $ecStatus = $setting->ec ? ManageStatus::UNVERIFIED : ManageStatus::VERIFIED;

            // Create user account immediately (like API approach)
            try {
                $user = User::create([
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'email' => $email,
                    'username' => $username,
                    'password' => Hash::make($request->password),
                    'mobile' => ($request->mobile_code ?? '').($request->mobile ?? ''),
                    'country_code' => $request->country_code ?? 'PK',
                    'country_name' => $request->country ?? 'Pakistan',
                    'kc' => $setting->kc ? ManageStatus::NO : ManageStatus::YES,
                    'ec' => $ecStatus,
                    'sc' => $setting->sc ? ManageStatus::NO : ManageStatus::YES,
                    'ts' => ManageStatus::NO,
                    'tc' => ManageStatus::YES,
                    'status' => ManageStatus::ACTIVE,
                    'ver_code' => $otpCode,
                    'ver_code_send_at' => now(),
                ]);

                // Send email OTP
                notify($user, 'EVER_CODE', [
                    'code' => $otpCode,
                ], ['email']);

                Log::info('Email OTP sent to: '.$email.' for registration (user ID: '.$user->id.')');

                return response()->json([
                    'success' => true,
                    'message' => 'Verification code has been sent to your email address.',
                    'email' => $email,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create user or send email OTP: '.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to register. Please try again.',
                ], 500);
            }
        } else {
            // Phone-based login flow (existing logic)
            try {
                $firebaseService = $this->getFirebaseService();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 503);
            }

            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|min:10|max:15',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $phoneNumber = $request->phone_number;

            // Validate phone number format
            if (! $firebaseService->validatePhoneNumber($phoneNumber)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid phone number format. Please enter a valid phone number.',
                ], 422);
            }

            // Send OTP using Firebase
            $result = $firebaseService->sendOTP($phoneNumber);

            if ($result['success']) {
                // Store verification ID in session for verification
                session(['otp_verification_id' => $result['verification_id']]);
                session(['otp_phone_number' => $result['phone_number']]);

                return response()->json([
                    'success' => true,
                    'message' => 'OTP sent successfully to '.$result['phone_number'],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }
    }

    /**
     * Verify OTP and login/register user
     */
    public function verifyOTP(Request $request)
    {
        // Check if this is email-based registration or phone-based login
        // For email-based, check if email parameter is present
        if ($request->has('email') && $request->email) {
            // Email-based registration flow (using API approach)
            $validator = Validator::make($request->all(), [
                'otp' => 'required|string|size:6',
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $otpCode = $request->otp;
            $email = strtolower(trim($request->email));

            // Find user by email (user should already exist from sendOTP)
            $user = User::where('email', $email)->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found. Please register again.',
                ], 422);
            }

            // Verify OTP code from user model (like API)
            if ($user->ver_code != $otpCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification code. Please try again.',
                ], 422);
            }

            // Check OTP expiry (2 minutes validity like API)
            if ($user->ver_code_send_at) {
                $expiryTime = \Carbon\Carbon::parse($user->ver_code_send_at)->addMinutes(2);
                if (now() > $expiryTime) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Verification code has expired. Please request a new one.',
                    ], 422);
                }
            }

            // Verify email and clear OTP (like API)
            $user->ec = ManageStatus::VERIFIED;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();

            // Login user
            Auth::login($user);

            // Send welcome email
            try {
                notify($user, 'WELCOME_EMAIL', [
                    'name' => $user->firstname.' '.$user->lastname,
                    'username' => $user->username,
                    'email' => $user->email,
                    'mobile' => $user->mobile ?? 'Not provided',
                    'business_name' => $user->business_name ?? '',
                    'business_type' => $user->business_type ?? '',
                    'industry' => $user->industry ?? '',
                    'login_url' => route('user.login'),
                ], ['email']);

                Log::info('Welcome email sent to verified user: '.$user->email);
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email: '.$e->getMessage());
                // Continue even if email fails
            }

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully! Account created.',
                'redirect' => route('user.home') ?? route('home'),
            ]);
        } else {
            // Phone-based login flow (existing logic)
            try {
                $firebaseService = $this->getFirebaseService();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 503);
            }

            $validator = Validator::make($request->all(), [
                'otp' => 'required|string|min:4|max:8',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $verificationId = session('otp_verification_id');
            $phoneNumber = session('otp_phone_number');

            if (! $verificationId || ! $phoneNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'No OTP session found. Please request OTP again.',
                ], 400);
            }

            // Verify OTP using Firebase
            $result = $firebaseService->verifyOTP($verificationId, $request->otp, $phoneNumber);

            if (! $result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 400);
            }

            // Find or create user in Laravel
            $user = $this->findOrCreateUser($phoneNumber, $request->all());

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create user account',
                ], 500);
            }

            // Login user
            Auth::login($user);

            // Clear OTP session
            session()->forget(['otp_verification_id', 'otp_phone_number']);

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => route('user.home'),
            ]);
        }
    }

    /**
     * Create user from registration data
     */
    private function createUserFromRegistration(array $data)
    {
        try {
            $setting = bs();

            // Generate unique username if not provided
            $username = $data['username'] ?? $this->generateUsernameFromEmail($data['email']);

            // Check if username exists and generate unique one
            while (User::where('username', $username)->exists()) {
                $username = $username.rand(100, 999);
            }

            // Prepare user data
            $userData = [
                'firstname' => $data['firstname'] ?? 'User',
                'lastname' => $data['lastname'] ?? '',
                'email' => strtolower($data['email']),
                'username' => $username,
                'password' => Hash::make($data['password']),
                'mobile' => ($data['mobile_code'] ?? '').($data['mobile'] ?? ''),
                'country_code' => $data['country_code'] ?? 'PK',
                'country_name' => $data['country'] ?? 'Pakistan',
                'kc' => $setting->kc ? ManageStatus::NO : ManageStatus::YES,
                'ec' => ManageStatus::VERIFIED, // Email verified via OTP
                'sc' => $setting->sc ? ManageStatus::NO : ManageStatus::YES,
                'ts' => ManageStatus::NO,
                'tc' => ManageStatus::YES,
                'status' => ManageStatus::ACTIVE,
            ];

            $user = User::create($userData);

            // Create admin notification
            $adminNotification = new \App\Models\AdminNotification;
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'New user registered via email';
            $adminNotification->click_url = urlPath('admin.user.index');
            $adminNotification->save();

            Log::info('New user created via email OTP registration: '.$user->id);

            return $user;

        } catch (\Exception $e) {
            Log::error('Error creating user from registration: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());

            return null;
        }
    }

    /**
     * Generate username from email
     */
    private function generateUsernameFromEmail(string $email): string
    {
        $username = explode('@', $email)[0];
        $username = preg_replace('/[^a-z0-9_]/', '', strtolower($username));
        $username = substr($username, 0, 20);

        if (strlen($username) < 6) {
            $username = $username.rand(1000, 9999);
        }

        return $username;
    }

    /**
     * Find or create user based on phone number
     */
    private function findOrCreateUser(string $phoneNumber, array $additionalData = [])
    {
        try {
            // Ensure FirebaseService is available
            if (! $this->firebaseService) {
                try {
                    $this->firebaseService = app(FirebaseService::class);
                } catch (\Exception $e) {
                    Log::error('Firebase service not available: '.$e->getMessage());

                    return null;
                }
            }

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
            $adminNotification = new \App\Models\AdminNotification;
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'New user registered via phone';
            $adminNotification->click_url = urlPath('admin.user.index');
            $adminNotification->save();

            // Send welcome email to new user
            try {
                Log::info('Sending welcome email to new OTP user: '.$user->email);
                $user->notify(new \App\Notifications\WelcomeNotification($user));
                Log::info('Welcome email sent successfully to: '.$user->email);
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email to: '.$user->email.' - Error: '.$e->getMessage());
                // Continue with user creation even if email fails
            }

            Log::info('New user created via phone authentication: '.$user->id);

            return $user;

        } catch (\Exception $e) {
            Log::error('Error creating user: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Generate unique username
     */
    private function generateUsername(string $phoneNumber): string
    {
        $base = 'user'.substr(preg_replace('/[^0-9]/', '', $phoneNumber), -6);
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base.$counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Resend OTP
     */
    public function resendOTP(Request $request)
    {
        // Check if this is email-based or phone-based
        if ($request->has('email') && $request->email) {
            // Email-based resend (using API approach)
            $email = strtolower(trim($request->email));

            // Find user by email
            $user = User::where('email', $email)->first();

            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found. Please register again.',
                ], 422);
            }

            // Generate new OTP code
            $otpCode = verificationCode(6);
            $user->ver_code = $otpCode;
            $user->ver_code_send_at = now();
            $user->save();

            // Send email OTP
            try {
                notify($user, 'EVER_CODE', [
                    'code' => $otpCode,
                ], ['email']);

                Log::info('Email OTP resent to: '.$email.' (user ID: '.$user->id.')');

                return response()->json([
                    'success' => true,
                    'message' => 'Verification code has been resent to your email address.',
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to resend email OTP: '.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to resend verification code. Please try again.',
                ], 500);
            }
        } else {
            // Phone-based resend (existing logic)
            // Check if FirebaseService is available
            if (! $this->firebaseService) {
                try {
                    $this->firebaseService = app(FirebaseService::class);
                } catch (\Exception $e) {
                    Log::error('Firebase service not available: '.$e->getMessage());

                    return response()->json([
                        'success' => false,
                        'message' => 'Phone OTP service is not available.',
                    ], 503);
                }
            }

            $phoneNumber = session('otp_phone_number');

            if (! $phoneNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'No phone number found in session',
                ], 400);
            }

            return $this->sendOTP($request->merge(['phone_number' => $phoneNumber]));
        }
    }

    /**
     * Check if phone number exists
     */
    public function checkPhoneNumber(Request $request)
    {
        // Check if FirebaseService is available
        if (! $this->firebaseService) {
            try {
                $this->firebaseService = app(FirebaseService::class);
            } catch (\Exception $e) {
                Log::error('Firebase service not available: '.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Phone OTP service is not available.',
                ], 503);
            }
        }

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number format',
            ], 422);
        }

        $phoneNumber = $this->firebaseService->formatPhoneNumber($request->phone_number);
        $user = User::where('mobile', $phoneNumber)->first();

        return response()->json([
            'success' => true,
            'exists' => $user ? true : false,
            'message' => $user ? 'Phone number is registered' : 'Phone number not found',
        ]);
    }
}
