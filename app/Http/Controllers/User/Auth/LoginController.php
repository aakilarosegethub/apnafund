<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Services\LoginLockoutService;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $username;

    public function __construct()
    {
        parent::__construct();
        $this->username = $this->findUsername();
    }

    public function loginForm()
    {
        $pageTitle = 'Login';
        $loginContent = getSiteData('login.content', true);
        $redirectUrl = request()->query('redirect'); // e.g. inbox with start params for "Contact Creator"

        return view($this->activeTheme.'user.auth.login', compact('pageTitle', 'loginContent', 'redirectUrl'));
    }

    public function login()
    {
        $this->validateLogin(request());

        request()->session()->regenerateToken();

        if (! verifyCaptcha()) {
            $toast[] = ['error', 'Invalid captcha provided'];

            return back()->withToasts($toast);
        }

        $lockout = app(LoginLockoutService::class);
        $user = $lockout->findUserByWebLogin((string) request()->input('username'));

        if ($message = $lockout->assertNotBlocked($user)) {
            $toast[] = ['error', $message];

            return back()->withToasts($toast);
        }

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.

        if ($this->hasTooManyLoginAttempts(request())) {
            $this->fireLockoutEvent(request());

            return $this->sendLockoutResponse(request());
        }

        if ($this->attemptLogin(request())) {
            return $this->sendLoginResponse(request());
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.

        $lockout->recordFailedAttempt($user, request());
        $this->incrementLoginAttempts(request());

        return $this->sendFailedLoginResponse(request());
    }

    public function findUsername()
    {
        $login = request()->input('username');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    public function username()
    {
        return $this->username;
    }

    protected function validateLogin()
    {
        $this->validate(request(), [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user in, honouring the "Remember Me" checkbox.
     *
     * How "Remember Me" works:
     *  - The login form sends `remember=1` only when the checkbox is ticked.
     *  - `$request->boolean('remember')` turns that into a strict true/false.
     *  - When true, Laravel issues a long-lived "remember" cookie and stores a
     *    hashed token in the users.remember_token column. On a later visit (after
     *    the session cookie has expired) Laravel matches that cookie against the
     *    stored token and re-authenticates the user automatically.
     *
     * Security considerations:
     *  - Only a hash of the token lives in the DB, and the token is rotated on
     *    each use, so a stolen cookie has limited value.
     *  - Logging out (see logout()) clears the cookie and rotates the token,
     *    invalidating any previously issued remember cookies.
     *  - The session ID is still regenerated on successful login by the parent
     *    trait's sendLoginResponse(), preventing session-fixation.
     *
     * Note: this mirrors the default AuthenticatesUsers::attemptLogin() behaviour
     * but is declared explicitly here so the remember handling is documented and
     * obvious rather than hidden inside the framework trait.
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->boolean('remember')
        );
    }

    /**
     * Drop any prior "Remember Me" session when the user logs in without the checkbox.
     * Laravel does not remove an existing remember cookie on a non-remember login, so
     * stale cookies could otherwise keep the account auto-authenticated later.
     */
    protected function clearStaleRememberMe($user): void
    {
        $guard = $this->guard();

        if (! empty($user->getRememberToken())) {
            $user->forceFill(['remember_token' => null])->save();
        }

        $guard->getCookieJar()->queue(
            $guard->getCookieJar()->forget($guard->getRecallerName())
        );
    }

    public function logout()
    {
        // Forget the user on the current guard. Because the framework "logout"
        // also clears the remember cookie and cycles users.remember_token, any
        // previously issued "Remember Me" cookie is rendered useless.
        $this->guard()->logout();

        // Invalidate the session data and issue a brand-new CSRF token so the
        // old session can no longer be replayed (defence against fixation/CSRF).
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $toast[] = ['success', 'Logout success'];

        return redirect()->route('user.login.form')
            ->withToasts($toast)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function authenticated(Request $request, $user)
    {
        if (! $request->boolean('remember')) {
            $this->clearStaleRememberMe($user);
        }

        app(LoginLockoutService::class)->clearLock($user);

        $user->tc = $user->ts == ManageStatus::VERIFIED ? ManageStatus::UNVERIFIED : ManageStatus::VERIFIED;
        $user->save();

        $redirect = $request->query('redirect') ?: $request->input('redirect');
        if ($redirect && $this->isSafeRedirectUrl($redirect)) {
            return redirect($redirect);
        }

        return to_route('user.dashboard');
    }

    /** Allow redirect only to same host / relative path (inbox, dashboard, etc.) */
    protected function isSafeRedirectUrl(?string $url): bool
    {
        if (! $url || ! is_string($url)) {
            return false;
        }
        $url = trim($url);
        if (str_starts_with($url, '/')) {
            return ! str_contains($url, '//');
        }
        $parsed = parse_url($url);
        $appUrl = parse_url(config('app.url'), PHP_URL_HOST);

        return isset($parsed['host']) && $parsed['host'] === $appUrl;
    }

    protected function maxAttempts(): int
    {
        $settings = loginLockSettings();

        return $settings['enabled'] ? $settings['max_attempts'] : 999;
    }

    protected function decayMinutes(): int
    {
        $settings = loginLockSettings();

        return $settings['enabled'] ? $settings['lock_duration'] : 1;
    }
}
