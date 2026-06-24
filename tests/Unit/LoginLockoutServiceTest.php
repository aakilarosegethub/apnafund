<?php

namespace Tests\Unit;

use App\Models\Setting;
use App\Models\User;
use App\Services\LoginLockoutService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LoginLockoutServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected function setLoginSettings(int $max = 3, int $duration = 30, bool $enabled = true, bool $email = false): void
    {
        if (!Schema::hasColumn('settings', 'login_max_attempts')) {
            $this->markTestSkipped('Login lockout settings columns not present.');
        }

        $setting = Setting::first();
        if (!$setting) {
            $this->markTestSkipped('No settings row in database.');
        }
        $setting->login_max_attempts = $max;
        $setting->login_lock_duration = $duration;
        $setting->login_lock_enabled = $enabled ? 1 : 0;
        $setting->login_lock_email_enabled = $email ? 1 : 0;
        $setting->save();
        Cache::forget('setting');
    }

    public function test_locks_account_after_configured_failed_attempts(): void
    {
        $this->setLoginSettings(3, 30);

        $user = User::factory()->create([
            'email' => 'locktest@example.com',
            'username' => 'locktest',
            'status' => 1,
        ]);

        $service = app(LoginLockoutService::class);
        $request = Request::create('/login', 'POST');

        $service->recordFailedAttempt($user->fresh(), $request);
        $service->recordFailedAttempt($user->fresh(), $request);
        $this->assertFalse($service->isBlocked($user->fresh()));

        $service->recordFailedAttempt($user->fresh(), $request);
        $user->refresh();

        $this->assertNotNull($user->blocked_until);
        $this->assertTrue($service->isBlocked($user));
    }

    public function test_successful_login_clears_lock(): void
    {
        $this->setLoginSettings(2, 60);

        $user = User::factory()->create([
            'email' => 'clear@example.com',
            'username' => 'clearlock',
            'status' => 1,
            'failed_login_attempts' => 2,
            'blocked_until' => now()->addHour(),
        ]);

        $service = app(LoginLockoutService::class);
        $service->clearLock($user->fresh());
        $user->refresh();

        $this->assertSame(0, (int) $user->failed_login_attempts);
        $this->assertNull($user->blocked_until);
        $this->assertFalse($service->isBlocked($user));
    }

    public function test_disabled_lockout_skips_enforcement(): void
    {
        $this->setLoginSettings(2, 60, false);

        $user = User::factory()->create([
            'email' => 'disabled@example.com',
            'username' => 'nolock',
            'status' => 1,
            'failed_login_attempts' => 10,
            'blocked_until' => now()->addHour(),
        ]);

        $service = app(LoginLockoutService::class);
        $this->assertFalse($service->isBlocked($user));
    }
}
