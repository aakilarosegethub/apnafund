<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LoginLockoutService
{
    public function settings(): array
    {
        return loginLockSettings();
    }

    public function isEnabled(): bool
    {
        return (bool) ($this->settings()['enabled'] ?? false);
    }

    public function findUserByWebLogin(string $login): ?User
    {
        $login = trim($login);
        if ($login === '') {
            return null;
        }

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return User::query()
            ->where($field, $login)
            ->where('status', 1)
            ->first();
    }

    public function findUserByApiLogin(string $mobile, ?string $ccode = null): ?User
    {
        $mobile = trim($mobile);
        if ($mobile === '') {
            return null;
        }

        $isEmail = (bool) filter_var($mobile, FILTER_VALIDATE_EMAIL);

        $query = User::query()->where(function ($q) use ($mobile) {
            $q->where('mobile', $mobile)->orWhere('email', $mobile);
        });

        if (! $isEmail && $ccode !== null && $ccode !== '') {
            $query->where('country_code', ltrim($ccode, '+'));
        }

        return $query->where('status', 1)->first();
    }

    public function isBlocked(?User $user): bool
    {
        if (! $user || ! $this->isEnabled()) {
            return false;
        }

        if (! $user->blocked_until) {
            return false;
        }

        if (Carbon::parse($user->blocked_until)->isFuture()) {
            return true;
        }

        $this->clearLock($user);

        return false;
    }

    public function blockedMessage(User $user): string
    {
        $minutes = max(1, (int) ceil(Carbon::parse($user->blocked_until)->diffInSeconds(now()) / 60));

        return "Your account is temporarily locked due to too many failed login attempts. Please try again in {$minutes} minute(s).";
    }

    public function assertNotBlocked(?User $user): ?string
    {
        if ($this->isBlocked($user)) {
            return $this->blockedMessage($user);
        }

        return null;
    }

    public function recordFailedAttempt(?User $user, ?Request $request = null): void
    {
        if (! $user || ! $this->isEnabled()) {
            return;
        }

        if (! \Illuminate\Support\Facades\Schema::hasColumn('users', 'failed_login_attempts')) {
            return;
        }

        $user->failed_login_attempts = (int) $user->failed_login_attempts + 1;
        $maxAttempts = (int) $this->settings()['max_attempts'];

        if ($user->failed_login_attempts >= $maxAttempts) {
            $duration = (int) $this->settings()['lock_duration'];
            $user->blocked_until = now()->addMinutes($duration);
            $user->save();

            $this->sendSecurityAlert($user, $request);

            return;
        }

        $user->save();
    }

    public function clearLock(?User $user): void
    {
        if (! $user) {
            return;
        }

        if (! \Illuminate\Support\Facades\Schema::hasColumn('users', 'failed_login_attempts')) {
            return;
        }

        $user->failed_login_attempts = 0;
        $user->blocked_until = null;
        $user->save();
    }

    protected function sendSecurityAlert(User $user, ?Request $request = null): void
    {
        if (! ($this->settings()['email_enabled'] ?? false)) {
            return;
        }

        if (empty($user->email) || ! filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        $ipInfo = getIpInfo();
        $osBrowser = osBrowser();
        $settings = $this->settings();

        try {
            notify($user, 'LOGIN_SECURITY_ALERT', [
                'name' => $user->fullname ?: $user->username,
                'attempts' => (string) $settings['max_attempts'],
                'lock_minutes' => (string) $settings['lock_duration'],
                'blocked_until' => showDateTime($user->blocked_until),
                'ip' => $ipInfo['ip'] ?? ($request?->ip() ?? 'N/A'),
                'browser' => $osBrowser['browser'] ?? 'Unknown',
                'operating_system' => $osBrowser['os_platform'] ?? 'Unknown',
            ], ['email']);
        } catch (\Throwable $e) {
            \Log::warning('Login lock security email failed: '.$e->getMessage(), ['user_id' => $user->id]);
        }
    }
}
