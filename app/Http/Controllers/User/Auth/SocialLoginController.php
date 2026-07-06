<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Constants\RegistrationLimits;
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
            
            [$user, $isNew] = $this->findOrCreateUser($facebookUser, 'facebook');
            
            if (!$user) {
                $toast[] = ['error', 'Failed to create user account. Please check logs for details.'];
                return redirect()->route('user.login')->withToasts($toast);
            }
            
            Auth::login($user);

            // First-time social account (or one that never finished accepting):
            // route to the Terms of Use confirmation screen before the dashboard.
            if ($user->needsTermsAcceptance()) {
                session(['requires_terms_accept' => true]);
                return redirect()->route('user.terms.accept.form');
            }
            
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
            
            return Socialite::driver('google')
                ->scopes(['openid', 'profile', 'email'])
                ->with(['prompt' => 'select_account'])
                ->redirect();
            
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
            
            [$user, $isNew] = $this->findOrCreateUser($googleUser, 'google');
            
            if (!$user) {
                $toast[] = ['error', 'Failed to create user account. Please check logs for details.'];
                return redirect()->route('user.login')->withToasts($toast);
            }
            
            Auth::login($user);

            // First-time social account (or one that never finished accepting):
            // route to the Terms of Use confirmation screen before the dashboard.
            if ($user->needsTermsAcceptance()) {
                session(['requires_terms_accept' => true]);
                return redirect()->route('user.terms.accept.form');
            }
            
            $toast[] = ['success', 'Successfully logged in with Google!'];
            return redirect()->route('user.dashboard')->withToasts($toast);
            
        } catch (\Exception $e) {
            $toast[] = ['error', 'Google login failed: ' . $e->getMessage()];
            return redirect()->route('user.login')->withToasts($toast);
        }
    }

    /**
     * Redirect to LinkedIn
     */
    public function redirectToLinkedIn()
    {
        try {
            $clientId = config('services.linkedin.client_id');
            $clientSecret = config('services.linkedin.client_secret');

            if (empty($clientId) || empty($clientSecret) || $clientId === 'disabled') {
                $toast[] = ['error', 'LinkedIn login is not configured. Please contact administrator.'];
                return redirect()->route('user.login')->withToasts($toast);
            }

            return Socialite::driver('linkedin')->redirect();

        } catch (\Exception $e) {
            $toast[] = ['error', 'LinkedIn login is not available. Please contact administrator.'];
            return redirect()->route('user.login')->withToasts($toast);
        }
    }

    /**
     * Handle LinkedIn callback
     */
    public function handleLinkedInCallback()
    {
        try {
            $linkedInUser = Socialite::driver('linkedin')->user();

            if (!$linkedInUser->getEmail()) {
                $toast[] = ['error', 'LinkedIn account email is required for registration'];
                return redirect()->route('user.login')->withToasts($toast);
            }

            [$user, $isNew] = $this->findOrCreateUser($linkedInUser, 'linkedin');

            if (!$user) {
                $toast[] = ['error', 'Failed to create user account. Please check logs for details.'];
                return redirect()->route('user.login')->withToasts($toast);
            }

            Auth::login($user);

            // First-time social account (or one that never finished accepting):
            // route to the Terms of Use confirmation screen before the dashboard.
            if ($user->needsTermsAcceptance()) {
                session(['requires_terms_accept' => true]);
                return redirect()->route('user.terms.accept.form');
            }

            $toast[] = ['success', 'Successfully logged in with LinkedIn!'];
            return redirect()->route('user.dashboard')->withToasts($toast);

        } catch (\Exception $e) {
            $toast[] = ['error', 'LinkedIn login failed: ' . $e->getMessage()];
            return redirect()->route('user.login')->withToasts($toast);
        }
    }

    /**
     * Find or create user based on social provider
     */
    private function findOrCreateUser($socialUser, $provider): array
    {
        try {
            $isNew = false;

            // Check if user exists with this social ID
            $user = User::where('provider_id', $socialUser->getId())
                       ->where('provider', $provider)
                       ->first();

            if ($user) {
                $user->update(['last_login_at' => now()]);
                return [$user, false];
            }

            // Check if user exists with same email
            $existingUser = User::where('email', $socialUser->getEmail())->first();
            
            if ($existingUser) {
                $existingUser->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'last_login_at' => now(),
                ]);
                
                return [$existingUser, false];
            }

            $isNew = true;
            $user = User::create([
                'username' => $this->generateUniqueUsername($socialUser->getName()),
                'email' => $socialUser->getEmail(),
                'firstname' => $this->getFirstName($socialUser->getName()),
                'lastname' => $this->getLastName($socialUser->getName()),
                'password' => Hash::make(Str::random(16)),
                'provider' => $provider,
                'provider_id' => $socialUser->getId(),
                'avatar' => $socialUser->getAvatar(),
                'email_verified_at' => now(),
                'status' => 1,
                'ec' => 1,
                'sc' => 1,
                'last_login_at' => now(),
                'tc' => \App\Constants\ManageStatus::YES,
            ]);

            return [$user, $isNew];
            
        } catch (\Exception $e) {
            \Log::error('Social login user creation failed: ' . $e->getMessage());
            return [null, false];
        }
    }

    /**
     * ?test=1 bypasses auth and validation so the page can be previewed as a guest.
     */
    private function isTermsTestMode(Request $request): bool
    {
        return $request->has('test');
    }

    /**
     * Show the first-login "Confirm Account Creation / Accept Terms of Use" screen.
     * Source of truth is the persisted users.terms_accepted_at column, so the gate
     * cannot be skipped by clearing the session or navigating away.
     */
    public function showTermsAcceptForm(Request $request)
    {
        $termsTestMode = $this->isTermsTestMode($request);
        $user = auth()->user();

        if (!$termsTestMode) {
            if (!$user) {
                return redirect()->route('user.login');
            }

            // Already accepted (or a non-social account) → nothing to confirm.
            if (!$user->needsTermsAcceptance()) {
                session()->forget('requires_terms_accept');
                return redirect()->route('user.dashboard');
            }
        }

        $pageTitle = 'Accept Terms';
        $policyPages = getSiteData('policy_pages.element', false, null, true) ?? [];

        return view($this->activeTheme . 'user.auth.terms-accept', compact('pageTitle', 'policyPages', 'termsTestMode'));
    }

    public function acceptTerms(Request $request)
    {
        $termsTestMode = $this->isTermsTestMode($request);
        $user = auth()->user();

        if (!$termsTestMode) {
            if (!$user) {
                return redirect()->route('user.login');
            }

            $request->validate([
                'agree' => 'required|accepted',
            ]);
        } elseif ($user) {
            $user->forceFill(['terms_accepted_at' => now()])->save();
            session()->forget('requires_terms_accept');

            $toast[] = ['success', 'Test mode: terms accepted.'];
            return redirect()->route('user.dashboard')->withToasts($toast);
        } else {
            $toast[] = ['success', 'Test mode: terms page submitted (guest — no account updated).'];
            return redirect()->route('user.terms.accept.form', ['test' => 1])->withToasts($toast);
        }

        // Persist acceptance so the gate is permanently cleared for this account.
        $user->forceFill(['terms_accepted_at' => now()])->save();
        session()->forget('requires_terms_accept');

        $toast[] = ['success', 'Thank you. Your account is ready.'];
        return redirect()->route('user.dashboard')->withToasts($toast);
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
        return mb_substr($nameParts[0] ?? '', 0, RegistrationLimits::NAME_PART_MAX);
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
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

        return mb_substr($lastName, 0, RegistrationLimits::NAME_PART_MAX);
    }
}
