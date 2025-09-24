<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialLoginController extends Controller
{
    /**
     * Redirect to Facebook
     */
    public function redirectToFacebook()
    {
        try {
            // Check if Facebook credentials are configured
            $clientId = config('services.facebook.client_id');
            $clientSecret = config('services.facebook.client_secret');
            
            if (empty($clientId) || empty($clientSecret) || $clientId === 'disabled') {
                $toast[] = ['error', 'Facebook login is not configured. Please contact administrator.'];
                return redirect()->route('user.login')->withToasts($toast);
            }
            
            return Socialite::driver('facebook')->redirect();
            
        } catch (\Exception $e) {
            $toast[] = ['error', 'Facebook login is not available. Please contact administrator.'];
            return redirect()->route('user.login')->withToasts($toast);
        }
    }

    /**
     * Handle Facebook callback
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            
            // Validate required data
            if (!$facebookUser->getEmail()) {
                $toast[] = ['error', 'Facebook account email is required for registration'];
                return redirect()->route('user.login')->withToasts($toast);
            }
            
            $user = $this->findOrCreateUser($facebookUser, 'facebook');
            
            if (!$user) {
                $toast[] = ['error', 'Failed to create user account. Please check logs for details.'];
                return redirect()->route('user.login')->withToasts($toast);
            }
            
            Auth::login($user);
            
            $toast[] = ['success', 'Successfully logged in with Facebook!'];
            return redirect()->route('user.dashboard')->withToasts($toast);
            
        } catch (\Exception $e) {
            $toast[] = ['error', 'Facebook login failed: ' . $e->getMessage()];
            return redirect()->route('user.login')->withToasts($toast);
        }
    }

    /**
     * Redirect to Google
     */
    public function redirectToGoogle()
    {
        try {
            // Check if Google credentials are configured
            $clientId = config('services.google.client_id');
            $clientSecret = config('services.google.client_secret');
            
            if (empty($clientId) || empty($clientSecret) || $clientId === 'disabled') {
                $toast[] = ['error', 'Google login is not configured. Please contact administrator.'];
                return redirect()->route('user.login')->withToasts($toast);
            }
            
            return Socialite::driver('google')->redirect();
            
        } catch (\Exception $e) {
            $toast[] = ['error', 'Google login is not available. Please contact administrator.'];
            return redirect()->route('user.login')->withToasts($toast);
        }
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Validate required data
            if (!$googleUser->getEmail()) {
                $toast[] = ['error', 'Google account email is required for registration'];
                return redirect()->route('user.login')->withToasts($toast);
            }
            
            $user = $this->findOrCreateUser($googleUser, 'google');
            
            if (!$user) {
                $toast[] = ['error', 'Failed to create user account. Please check logs for details.'];
                return redirect()->route('user.login')->withToasts($toast);
            }
            
            Auth::login($user);
            
            $toast[] = ['success', 'Successfully logged in with Google!'];
            return redirect()->route('user.dashboard')->withToasts($toast);
            
        } catch (\Exception $e) {
            $toast[] = ['error', 'Google login failed: ' . $e->getMessage()];
            return redirect()->route('user.login')->withToasts($toast);
        }
    }

    /**
     * Find or create user based on social provider
     */
    private function findOrCreateUser($socialUser, $provider)
    {
        try {
            // Check if user exists with this social ID
            $user = User::where('provider_id', $socialUser->getId())
                       ->where('provider', $provider)
                       ->first();

            if ($user) {
                // Update last login time
                $user->update(['last_login_at' => now()]);
                return $user;
            }

            // Check if user exists with same email
            $existingUser = User::where('email', $socialUser->getEmail())->first();
            
            if ($existingUser) {
                // Update existing user with social provider info
                $existingUser->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'last_login_at' => now(),
                ]);
                
                return $existingUser;
            }

            // Create new user with all required fields
            $user = User::create([
                'username' => $this->generateUniqueUsername($socialUser->getName()),
                'email' => $socialUser->getEmail(),
                'firstname' => $this->getFirstName($socialUser->getName()),
                'lastname' => $this->getLastName($socialUser->getName()),
                'password' => Hash::make(Str::random(16)), // Random password for social users
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'email_verified_at' => now(), // Social users are pre-verified
                'status' => 1, // Active
                'ec' => 1, // Email confirmed
                'sc' => 1, // SMS confirmed (not required for social login)
                'last_login_at' => now(),
                'tc' => 1, // Terms and conditions accepted
            ]);

            return $user;
            
        } catch (\Exception $e) {
            \Log::error('Social login user creation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Social user data: ' . json_encode([
                'id' => $socialUser->getId(),
                'email' => $socialUser->getEmail(),
                'name' => $socialUser->getName(),
                'avatar' => $socialUser->getAvatar(),
            ]));
            return null;
        }
    }

    /**
     * Generate unique username
     */
    private function generateUniqueUsername($name)
    {
        if (empty($name)) {
            $name = 'user';
        }
        
        $baseUsername = Str::slug($name);
        
        // If slug is empty, use default
        if (empty($baseUsername)) {
            $baseUsername = 'user';
        }
        
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
            
            // Prevent infinite loop
            if ($counter > 1000) {
                $username = $baseUsername . time();
                break;
            }
        }

        return $username;
    }

    /**
     * Get first name from full name
     */
    private function getFirstName($fullName)
    {
        if (empty($fullName)) {
            return '';
        }
        
        $nameParts = explode(' ', trim($fullName));
        return $nameParts[0] ?? '';
    }

    /**
     * Get last name from full name
     */
    private function getLastName($fullName)
    {
        if (empty($fullName)) {
            return '';
        }
        
        $nameParts = explode(' ', trim($fullName));
        return count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';
    }
}
