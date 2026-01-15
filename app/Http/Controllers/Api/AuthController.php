<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Constants\ManageStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseApiController
{
    /**
     * User Registration
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $data = $this->getRequestData($request);
            
            if (empty($data['name']) || empty($data['email']) || empty($data['mobile']) || empty($data['password']) || empty($data['ccode'])) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Something Went Wrong!"
                ], 401);
            }

            // Split name into firstname and lastname
            $nameParts = explode(' ', trim($data['name']), 2);
            $firstname = strip_tags($this->h->real_string($nameParts[0]));
            $lastname = isset($nameParts[1]) ? strip_tags($this->h->real_string($nameParts[1])) : '';
            $email = strip_tags($this->h->real_string($data['email']));
            $mobile = strip_tags($this->h->real_string($data['mobile']));
            $ccode = strip_tags($this->h->real_string($data['ccode']));
            $password = strip_tags($this->h->real_string($data['password']));
            
            // Generate username from name
            $usernameBase = strtolower(preg_replace('/[^a-z0-9]/', '', $firstname . ($lastname ? $lastname : '')));
            if (empty($usernameBase)) {
                $usernameBase = 'user' . time();
            }
            $username = $usernameBase;
            $counter = 1;
            // Check if username exists and make it unique
            while (true) {
                $checkUsername = $this->h->queryfire("select * from users where username='" . $username . "'");
                if (!$checkUsername || $checkUsername->num_rows == 0) {
                    break;
                }
                $username = $usernameBase . $counter;
                $counter++;
            }

            // Check if users table exists, if not return helpful error
            $tableCheck = $this->h->queryfire("SHOW TABLES LIKE 'users'");
            if (!$tableCheck || $tableCheck->num_rows == 0) {
                return response()->json([
                    "ResponseCode" => "500",
                    "Result" => "false",
                    "ResponseMsg" => "Users table does not exist. Please run migrations or create the table.",
                    "Error" => "Table 'users' doesn't exist. Run: php artisan migrate OR create table using create_users_table.sql"
                ], 500);
            }

            // Check if mobile already exists
            $checkmob = $this->h->queryfire("select * from users where mobile='" . $mobile . "' and country_code='" . $ccode . "'");
            if ($checkmob && $checkmob->num_rows != 0) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Mobile Number Already Used!"
                ], 401);
            }

            // Check if email already exists
            $checkemail = $this->h->queryfire("select * from users where email='" . $email . "'");
            if ($checkemail && $checkemail->num_rows != 0) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Email Address Already Used!"
                ], 401);
            }

            // Hash password before storing
            $hashedPassword = Hash::make($password);
            
            // Get settings to check if email verification is required
            $set = $this->h->fetchData("SELECT * FROM `settings` LIMIT 1");
            if (!$set) {
                // Try alternative table name
                $set = $this->h->fetchData("SELECT * FROM `tbl_setting` LIMIT 1");
            }
            
            // Determine email verification status (ec)
            // If setting->ec is true, email verification is required (UNVERIFIED = 0)
            // If setting->ec is false, email is auto-verified (VERIFIED = 1)
            $ecStatus = ($set && isset($set['ec']) && $set['ec']) ? ManageStatus::UNVERIFIED : ManageStatus::VERIFIED;
            
            $table = "users";
            // Using users table fields: firstname, lastname, username, email, mobile, password, country_code, status, ec
            // created_at will be automatically set by Laravel
            $field_values = ["firstname", "lastname", "username", "email", "mobile", "password", "country_code", "status", "ec"];
            $data_values = [$firstname, $lastname, $username, $email, $mobile, $hashedPassword, $ccode, 1, $ecStatus];

            $check = $this->h->insertDataId_Api($field_values, $data_values, $table);

            if ($check) {
                // Get user using Laravel model to create token
                $user = User::find($check);
                if ($user) {
                    // Send email OTP if email verification is required
                    if ($ecStatus == ManageStatus::UNVERIFIED) {
                        try {
                            // Generate verification code
                            $user->ver_code = verificationCode(6);
                            $user->ver_code_send_at = now();
                            $user->save();

                            // Send verification email
                            notify($user, 'EVER_CODE', [
                                'code' => $user->ver_code
                            ], ['email']);

                            \Log::info('API Registration - Email verification code sent to: ' . $user->email);
                        } catch (\Exception $e) {
                            \Log::error('API Registration - Failed to send verification code to: ' . $user->email . ' - Error: ' . $e->getMessage());
                            // Continue with registration even if email fails
                        }
                    }
                    
                    // Create token
                    $token = $user->createToken('auth_token')->plainTextToken;
                    
                    $c = $this->h->queryfire("select id, firstname, lastname, email, mobile, password, country_code as ccode, status, created_at as rdate, balance as wallet, image as profile_pic, ec from users where id=" . $check . "");
                    if ($c && $c->num_rows > 0) {
                        $userData = $c->fetch_assoc();
                        // Remove password from response
                        unset($userData['password']);
                        // Combine firstname and lastname as name for API response
                        $userData['name'] = trim(($userData['firstname'] ?? '') . ' ' . ($userData['lastname'] ?? ''));
                        // Format rdate if needed
                        if (isset($userData['rdate']) && $userData['rdate']) {
                            $userData['rdate'] = date("Y-m-d H:i:s", strtotime($userData['rdate']));
                        } else {
                            // If created_at is null, use current timestamp
                            $userData['rdate'] = date("Y-m-d H:i:s");
                        }
                        
                        $currency = '';
                        if ($set) {
                            $currency = $set['currency'] ?? $set['site_currency'] ?? '';
                        }

                        $responseMsg = "Sign Up Done Successfully!";
                        if ($ecStatus == ManageStatus::UNVERIFIED) {
                            $responseMsg = "Sign Up Done Successfully! Please verify your email. OTP has been sent to your email.";
                        }

                        return response()->json([
                            "UserLogin" => $userData,
                            "token" => $token,
                            "currency" => $currency,
                            "email_verified" => ($ecStatus == ManageStatus::VERIFIED) ? true : false,
                            "ResponseCode" => "200",
                            "Result" => "true",
                            "ResponseMsg" => $responseMsg
                        ]);
                    }
                }
            }

            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Registration Failed! Please try again."
            ], 401);
        } catch (\Exception $e) {
            // Log error details
            $errorMsg = $e->getMessage();
            $errorTrace = $e->getTraceAsString();
            
            // Try to log, but don't fail if logging fails
            try {
                \Log::error('Registration Error: ' . $errorMsg . ' | Trace: ' . substr($errorTrace, 0, 500));
            } catch (\Exception $logError) {
                // Logging failed, continue anyway
            }
            
            // Return error response with more details in development
            $responseMsg = "An error occurred during registration. Please try again.";
            if (config('app.debug')) {
                $responseMsg = "Error: " . $errorMsg;
            }
            
            return response()->json([
                "ResponseCode" => "500",
                "Result" => "false",
                "ResponseMsg" => $responseMsg,
                "Error" => config('app.debug') ? $errorMsg : null
            ], 500);
        }
    }

    /**
     * User Login
     */
    public function login(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['mobile']) || empty($data['password']) || empty($data['ccode'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $mobile = strip_tags($this->h->real_string($data['mobile']));
        $password = strip_tags($this->h->real_string($data['password']));
        $ccode = strip_tags($this->h->real_string($data['ccode']));

        // Check if input is email or mobile
        $isEmail = filter_var($mobile, FILTER_VALIDATE_EMAIL);

        // Find user by mobile or email
        $userQuery = User::where(function($query) use ($mobile) {
            $query->where('mobile', $mobile)
                  ->orWhere('email', $mobile);
        });

        // Only check country_code if it's mobile (not email)
        if (!$isEmail) {
            // Remove + sign if present in ccode
            $ccode = ltrim($ccode, '+');
            $userQuery->where('country_code', $ccode);
        }

        $user = $userQuery->where('status', 1)->first();

        if (!$user) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Invalid Email/Mobile No or Password!!!"
            ], 401);
        }

        // Check if password matches (handle both hashed and plain text for backward compatibility)
        $passwordValid = false;
        
        // Try Bcrypt first (Laravel default)
        try {
        if (Hash::check($password, $user->password)) {
            $passwordValid = true;
            }
        } catch (\Exception $e) {
            // Password is not Bcrypt, try other methods
        }
        
        // If Bcrypt failed, try plain text comparison
        if (!$passwordValid && $user->password === $password) {
            // Legacy plain text password - hash it for future use
            $user->password = Hash::make($password);
            $user->save();
            $passwordValid = true;
        }
        
        // If still not valid, try MD5 (common legacy format)
        if (!$passwordValid && md5($password) === $user->password) {
            // MD5 password - convert to Bcrypt
            $user->password = Hash::make($password);
            $user->save();
            $passwordValid = true;
        }
        
        // If still not valid, try SHA1 (another legacy format)
        if (!$passwordValid && sha1($password) === $user->password) {
            // SHA1 password - convert to Bcrypt
            $user->password = Hash::make($password);
            $user->save();
            $passwordValid = true;
        }

        if (!$passwordValid) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Invalid Email/Mobile No or Password!!!"
            ], 401);
        }

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Get user data in the expected format
        $c = $this->h->queryfire("select id, firstname, lastname, email, mobile, country_code as ccode, status, created_at as rdate, balance as wallet, image as profile_pic from users where id=" . $user->id . "");
        $c = $c->fetch_assoc();
        
        // Combine firstname and lastname as name for API response
        if ($c) {
            $c['name'] = trim(($c['firstname'] ?? '') . ' ' . ($c['lastname'] ?? ''));
            // Format rdate if needed
            if (isset($c['rdate'])) {
                $c['rdate'] = date("Y-m-d H:i:s", strtotime($c['rdate']));
            }
        }

        return response()->json([
            "UserLogin" => $c,
            "token" => $token,
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Login successfully!"
        ]);
    }

    /**
     * Forget Password
     */
    public function forgetPassword(Request $request): JsonResponse
    {
        try {
            $data = $this->getRequestData($request);

            if (empty($data['mobile']) || empty($data['password'])) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Something Went wrong try again !"
                ], 401);
            }

            $mobile = strip_tags($this->h->real_string($data['mobile']));
            $password = strip_tags($this->h->real_string($data['password']));
            $ccode = !empty($data['ccode']) ? strip_tags($this->h->real_string($data['ccode'])) : null;

            // Check if input is email or mobile
            $isEmail = filter_var($mobile, FILTER_VALIDATE_EMAIL);

            // Find user by email or mobile
            if ($isEmail) {
                // Find by email
                $user = User::where('email', $mobile)->first();
            } else {
                // Find by mobile and country code
                if (empty($ccode)) {
                    return response()->json([
                        "ResponseCode" => "401",
                        "Result" => "false",
                        "ResponseMsg" => "Country code is required for mobile number!"
                    ], 401);
                }
                $ccode = ltrim($ccode, '+');
                $user = User::where('mobile', $mobile)
                            ->where('country_code', $ccode)
                            ->first();
            }

            if ($user) {
                // Hash password before storing
                $hashedPassword = Hash::make($password);
                
                // Update password
                $user->password = $hashedPassword;
                $user->save();

                // Send email notification about password change
                try {
                    notify($user, 'PASS_RESET_DONE', [
                        'name' => $user->firstname . ' ' . $user->lastname,
                        'email' => $user->email,
                        'time' => now()->format('Y-m-d H:i:s')
                    ], ['email']);
                    
                    \Log::info('Password reset email sent to: ' . $user->email);
                } catch (\Exception $e) {
                    \Log::error('Failed to send password reset email to: ' . $user->email . ' - Error: ' . $e->getMessage());
                }

                return response()->json([
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "ResponseMsg" => "Password Changed Successfully! Email notification sent."
                ]);
            } else {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => ($isEmail ? "Email" : "Mobile") . " Not Matched!!!!"
                ], 401);
            }
        } catch (\Exception $e) {
            \Log::error('Forget Password Error: ' . $e->getMessage());
            
            return response()->json([
                "ResponseCode" => "500",
                "Result" => "false",
                "ResponseMsg" => "An error occurred. Please try again."
            ], 500);
        }
    }

    /**
     * Send Password Reset OTP via Email
     */
    public function sendPasswordResetOTP(Request $request): JsonResponse
    {
        try {
            $data = $this->getRequestData($request);
            
            if (empty($data['email'])) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Email is required!"
                ], 401);
            }

            $email = strip_tags($this->h->real_string($data['email']));

            // Find user by email
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "User not found with this email!"
                ], 401);
            }

            // Check rate limiting - minimum 60 seconds gap between resends
            if ($user->ver_code_send_at) {
                $minWaitTime = 60; // 60 seconds
                $elapsedSeconds = now()->diffInSeconds($user->ver_code_send_at, false);
                
                // Only block if elapsed time is less than 60 seconds and is a valid positive number
                // If elapsed time is >= 60 seconds, allow the request
                if ($elapsedSeconds >= 0 && $elapsedSeconds < $minWaitTime) {
                    $remainingSeconds = (int) ceil($minWaitTime - $elapsedSeconds);
                    return response()->json([
                        "ResponseCode" => "429",
                        "Result" => "false",
                        "ResponseMsg" => "Please wait {$remainingSeconds} seconds before requesting a new OTP."
                    ], 429);
                }
            }

            // Generate new verification code
            $user->ver_code = verificationCode(6);
            $user->ver_code_send_at = now();
            $user->save();

            // Send Email OTP
            try {
                notify($user, 'EVER_CODE', [
                    'code' => $user->ver_code
                ], ['email']);

                \Log::info('Password reset OTP sent to: ' . $user->email);

                return response()->json([
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "ResponseMsg" => "OTP has been sent successfully to your email: " . $email,
                    "email" => $email,
                    "time" => now()->format('Y-m-d H:i:s'),
                    "timestamp" => now()->timestamp
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send password reset OTP to: ' . $email . ' - Error: ' . $e->getMessage());
                
                return response()->json([
                    "ResponseCode" => "500",
                    "Result" => "false",
                    "ResponseMsg" => "Failed to send OTP. Please try again later."
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Send Password Reset OTP Error: ' . $e->getMessage());
            
            return response()->json([
                "ResponseCode" => "500",
                "Result" => "false",
                "ResponseMsg" => "An error occurred. Please try again."
            ], 500);
        }
    }

    /**
     * Verify Password Reset OTP
     */
    public function verifyPasswordResetOTP(Request $request): JsonResponse
    {
        try {
            $data = $this->getRequestData($request);
            
            if (empty($data['email']) || empty($data['code'])) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Email and OTP code are required!"
                ], 401);
            }

            $email = strip_tags($this->h->real_string($data['email']));
            $code = strip_tags($this->h->real_string($data['code']));

            // Find user by email
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "User not found!"
                ], 401);
            }

            // Check if OTP matches
            if ($user->ver_code != $code) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Invalid OTP code!"
                ], 401);
            }

            // Check if OTP is expired (10 minutes validity)
            if ($user->ver_code_send_at) {
                $expiryTime = \Carbon\Carbon::parse($user->ver_code_send_at)->addMinutes(10);
                if (now() > $expiryTime) {
                    return response()->json([
                        "ResponseCode" => "401",
                        "Result" => "false",
                        "ResponseMsg" => "OTP has expired. Please request a new one."
                    ], 401);
                }
            }

            // OTP verified - generate reset token (store in ver_code temporarily)
            $resetToken = bin2hex(random_bytes(32));
            $user->ver_code = $resetToken; // Store reset token
            $user->ver_code_send_at = now(); // Update timestamp
            $user->save();

            \Log::info('Password reset OTP verified for: ' . $email);

            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "OTP verified successfully!",
                "reset_token" => $resetToken,
                "email" => $email
            ]);

        } catch (\Exception $e) {
            \Log::error('Verify Password Reset OTP Error: ' . $e->getMessage());
            
            return response()->json([
                "ResponseCode" => "500",
                "Result" => "false",
                "ResponseMsg" => "An error occurred. Please try again."
            ], 500);
        }
    }

    /**
     * Reset Password (After OTP Verification)
     */
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            $data = $this->getRequestData($request);
            
            if (empty($data['email']) || empty($data['password'])) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Email and password are required!"
                ], 401);
            }

            $email = strip_tags($this->h->real_string($data['email']));
            $password = strip_tags($this->h->real_string($data['password']));

            // Find user by email
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "User not found!"
                ], 401);
            }

            // Hash and update password (no token verification needed)
            $user->password = Hash::make($password);
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();

            // Send email notification about password change
            try {
                notify($user, 'PASS_RESET_DONE', [
                    'name' => $user->firstname . ' ' . $user->lastname,
                    'email' => $user->email,
                    'time' => now()->format('Y-m-d H:i:s')
                ], ['email']);
                
                \Log::info('Password reset successful for: ' . $user->email);
            } catch (\Exception $e) {
                \Log::error('Failed to send password reset confirmation email to: ' . $user->email . ' - Error: ' . $e->getMessage());
            }

            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Password reset successfully! Email notification sent."
            ]);

        } catch (\Exception $e) {
            \Log::error('Reset Password Error: ' . $e->getMessage());
            
            return response()->json([
                "ResponseCode" => "500",
                "Result" => "false",
                "ResponseMsg" => "An error occurred. Please try again."
            ], 500);
        }
    }

    /**
     * Social Login
     */
    public function socialLogin(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['email'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $email = strip_tags($this->h->real_string($data['email']));

        // Find user by email
        $user = User::where('email', $email)->where('status', 1)->first();

        if (!$user) {
            return response()->json([
                "ResponseCode" => "201",
                "Result" => "false",
                "ResponseMsg" => "Account Not Found!!"
            ], 201);
        }

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Get user data
        $c = $this->h->queryfire("select id, firstname, lastname, email, mobile, country_code as ccode, status, created_at as rdate, balance as wallet, image as profile_pic from users where email='" . $email . "'");
        $c = $c->fetch_assoc();
        
        if ($c) {
            $c['name'] = trim(($c['firstname'] ?? '') . ' ' . ($c['lastname'] ?? ''));
        }

        return response()->json([
            "UserLogin" => $c,
            "token" => $token,
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Login successfully!"
        ]);
    }

    /**
     * Check Mobile Number
     */
    public function checkMobile(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['mobile']) || empty($data['ccode'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $mobile = strip_tags($this->h->real_string($data['mobile']));
        $code = strip_tags($this->h->real_string($data['ccode']));

        $chek = $this->h->queryfire("select * from users where mobile='" . $mobile . "' and country_code='" . $code . "'");
        $chekRows = $chek ? $chek->num_rows : 0;

        if ($chekRows != 0) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Already Exist Mobile Number!"
            ], 401);
        } else {
            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "New Number!"
            ]);
        }
    }

    /**
     * Verify Email OTP
     */
    public function verifyEmailOTP(Request $request): JsonResponse
    {
        try {
            $data = $this->getRequestData($request);
            
            if (empty($data['code']) || empty($data['email'])) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Code and Email are required!"
                ], 401);
            }

            $code = strip_tags($this->h->real_string($data['code']));
            $email = strip_tags($this->h->real_string($data['email']));

            // Find user by email
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "User not found!"
                ], 401);
            }

            // Check if OTP matches
            if ($user->ver_code != $code) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Invalid verification code!"
                ], 401);
            }

            // Check if OTP is expired (2 minutes validity)
            if ($user->ver_code_send_at) {
                $expiryTime = \Carbon\Carbon::parse($user->ver_code_send_at)->addMinutes(2);
                if (now() > $expiryTime) {
                    return response()->json([
                        "ResponseCode" => "401",
                        "Result" => "false",
                        "ResponseMsg" => "Verification code has expired. Please request a new one."
                    ], 401);
                }
            }

            // Verify email
            $user->ec = ManageStatus::VERIFIED;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();

            // Send welcome email after successful verification
            try {
                \Log::info('API Email verified successfully for user: ' . $user->email . ' - Sending welcome email');
                
                notify($user, 'WELCOME_EMAIL', [
                    'name' => $user->firstname . ' ' . $user->lastname,
                    'username' => $user->username,
                    'email' => $user->email,
                    'mobile' => $user->mobile ?? 'Not provided',
                    'business_name' => $user->business_name ?? '',
                    'business_type' => $user->business_type ?? '',
                    'industry' => $user->industry ?? '',
                    'login_url' => route('login'),
                ], ['email']);
                
                \Log::info('API Welcome email sent successfully to: ' . $user->email);
                
            } catch (\Exception $e) {
                \Log::error('API Failed to send welcome email after verification to: ' . $user->email . ' - Error: ' . $e->getMessage());
            }

            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Email verified successfully!"
            ]);

        } catch (\Exception $e) {
            \Log::error('Email OTP Verification Error: ' . $e->getMessage());
            
            return response()->json([
                "ResponseCode" => "500",
                "Result" => "false",
                "ResponseMsg" => "An error occurred during verification. Please try again."
            ], 500);
        }
    }


    /**
     * Resend Email OTP
     */
    public function resendEmailOTP(Request $request): JsonResponse
    {
        try {
            $data = $this->getRequestData($request);
            
            if (empty($data['code']) || empty($data['email'])) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Code and Email are required!"
                ], 401);
            }

            $code = strip_tags($this->h->real_string($data['code']));
            $email = strip_tags($this->h->real_string($data['email']));

            // Find user by email
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "User not found!"
                ], 401);
            }

            // Check if OTP matches
            if ($user->ver_code != $code) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Invalid verification code!"
                ], 401);
            }

            // Check if OTP is expired (2 minutes validity)
            if ($user->ver_code_send_at) {
                $expiryTime = \Carbon\Carbon::parse($user->ver_code_send_at)->addMinutes(2);
                if (now() > $expiryTime) {
                    return response()->json([
                        "ResponseCode" => "401",
                        "Result" => "false",
                        "ResponseMsg" => "Verification code has expired. Please request a new one."
                    ], 401);
                }
            }

            // Verify email
            $user->ec = ManageStatus::VERIFIED;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();

            // Send welcome email after successful verification
            try {
                \Log::info('API Email verified successfully for user: ' . $user->email . ' - Sending welcome email');
                
                notify($user, 'WELCOME_EMAIL', [
                    'name' => $user->firstname . ' ' . $user->lastname,
                    'username' => $user->username,
                    'email' => $user->email,
                    'mobile' => $user->mobile ?? 'Not provided',
                    'business_name' => $user->business_name ?? '',
                    'business_type' => $user->business_type ?? '',
                    'industry' => $user->industry ?? '',
                    'login_url' => route('login'),
                ], ['email']);
                
                \Log::info('API Welcome email sent successfully to: ' . $user->email);
                
            } catch (\Exception $e) {
                \Log::error('API Failed to send welcome email after verification to: ' . $user->email . ' - Error: ' . $e->getMessage());
            }

            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Email verified successfully!"
            ]);

        } catch (\Exception $e) {
            \Log::error('Email OTP Verification Error: ' . $e->getMessage());
            
            return response()->json([
                "ResponseCode" => "500",
                "Result" => "false",
                "ResponseMsg" => "An error occurred during verification. Please try again."
            ], 500);
        }
    }

    /**
     * Resend Mobile OTP for Registration
     */
    public function resendMobileOTP(Request $request): JsonResponse
    {
        try {
            $data = $this->getRequestData($request);
            
            // Email is required
            if (empty($data['email'])) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Email is required!"
                ], 401);
            }

            $email = strip_tags($this->h->real_string($data['email']));

            // Find user by email
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "User not found with this email!"
                ], 401);
            }

            // Check rate limiting - minimum 60 seconds gap between resends
            if ($user->ver_code_send_at) {
                $minWaitTime = 60; // 60 seconds
                $elapsedSeconds = now()->diffInSeconds($user->ver_code_send_at);
                
                if ($elapsedSeconds < $minWaitTime) {
                    $remainingSeconds = (int) ceil($minWaitTime - $elapsedSeconds);
                    return response()->json([
                        "ResponseCode" => "429",
                        "Result" => "false",
                        "ResponseMsg" => "Please wait {$remainingSeconds} seconds before requesting a new OTP."
                    ], 429);
                }
            }

            // Generate new verification code
            $user->ver_code = verificationCode(6);
            $user->ver_code_send_at = now();
            $user->save();

            // Send OTP to both SMS and Email
            try {
                $smsSent = false;
                $emailSent = false;
                
                // Send SMS OTP (if mobile exists)
                if ($user->mobile && $user->country_code) {
                    try {
                        notify($user, 'SVER_CODE', [
                            'code' => $user->ver_code
                        ], ['sms']);
                        $smsSent = true;
                        \Log::info('API Mobile OTP resend - SMS sent to: +' . $user->country_code . $user->mobile);
                    } catch (\Exception $smsError) {
                        \Log::error('API Mobile OTP resend - SMS failed for: +' . $user->country_code . $user->mobile . ' - Error: ' . $smsError->getMessage());
                    }
                }

                // Send Email OTP
                if ($user->email) {
                    try {
                        notify($user, 'EVER_CODE', [
                            'code' => $user->ver_code
                        ], ['email']);
                        $emailSent = true;
                        \Log::info('API Mobile OTP resend - Email sent to: ' . $user->email);
                    } catch (\Exception $emailError) {
                        \Log::error('API Mobile OTP resend - Email failed for: ' . $user->email . ' - Error: ' . $emailError->getMessage());
                    }
                }

                $responseMsg = "OTP has been resent successfully";
                $sentTo = [];
                
                if ($smsSent && $user->mobile && $user->country_code) {
                    $sentTo[] = "SMS (+{$user->country_code}{$user->mobile})";
                }
                if ($emailSent) {
                    $sentTo[] = "Email ({$user->email})";
                }
                
                if (count($sentTo) > 0) {
                    $responseMsg .= " to " . implode(" and ", $sentTo);
                } else {
                    return response()->json([
                        "ResponseCode" => "500",
                        "Result" => "false",
                        "ResponseMsg" => "Failed to send OTP. Please try again later."
                    ], 500);
                }

                return response()->json([
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "ResponseMsg" => $responseMsg,
                    "mobile" => ($user->mobile && $user->country_code) ? '+' . $user->country_code . $user->mobile : null,
                    "email" => $user->email,
                    "sent_via" => [
                        "sms" => $smsSent,
                        "email" => $emailSent
                    ]
                ]);
            } catch (\Exception $e) {
                \Log::error('API Mobile OTP resend failed for email: ' . $email . ' - Error: ' . $e->getMessage());
                
                return response()->json([
                    "ResponseCode" => "500",
                    "Result" => "false",
                    "ResponseMsg" => "Failed to send OTP. Please try again later."
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Resend Mobile OTP Error: ' . $e->getMessage());
            
            return response()->json([
                "ResponseCode" => "500",
                "Result" => "false",
                "ResponseMsg" => "An error occurred. Please try again."
            ], 500);
        }
    }

    /**
     * Verify Mobile OTP for Registration
     */
    public function verifyMobileOTP(Request $request): JsonResponse
    {
        try {
            $data = $this->getRequestData($request);
            
            if (empty($data['code']) || empty($data['mobile']) || empty($data['ccode'])) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Code, mobile number and country code are required!"
                ], 401);
            }

            $code = strip_tags($this->h->real_string($data['code']));
            $mobile = strip_tags($this->h->real_string($data['mobile']));
            $ccode = strip_tags($this->h->real_string($data['ccode']));

            // Find user by mobile and country code
            $user = User::where('mobile', $mobile)
                        ->where('country_code', $ccode)
                        ->first();

            if (!$user) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "User not found!"
                ], 401);
            }

            // Check if OTP matches
            if ($user->ver_code != $code) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Invalid verification code!"
                ], 401);
            }

            // Check if OTP is expired (10 minutes validity)
            if ($user->ver_code_send_at) {
                $expiryTime = \Carbon\Carbon::parse($user->ver_code_send_at)->addMinutes(10);
                if (now() > $expiryTime) {
                    return response()->json([
                        "ResponseCode" => "401",
                        "Result" => "false",
                        "ResponseMsg" => "Verification code has expired. Please request a new one."
                    ], 401);
                }
            }

            // Verify mobile
            $user->sc = ManageStatus::VERIFIED;
            $user->ver_code = null;
            $user->ver_code_send_at = null;
            $user->save();

            \Log::info('API Mobile verified successfully for user: +' . $ccode . $mobile);

            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Mobile number verified successfully!"
            ]);

        } catch (\Exception $e) {
            \Log::error('Mobile OTP Verification Error: ' . $e->getMessage());
            
            return response()->json([
                "ResponseCode" => "500",
                "Result" => "false",
                "ResponseMsg" => "An error occurred during verification. Please try again."
            ], 500);
        }
    }
}

