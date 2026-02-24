<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $username;

    function __construct() {
        parent::__construct();
        $this->username = $this->findUsername();
    }

    function loginForm() {
        $pageTitle = 'Login';
        $loginContent = getSiteData('login.content', true);
        $redirectUrl = request()->query('redirect'); // e.g. inbox with start params for "Contact Creator"

        return view($this->activeTheme . 'user.auth.login', compact('pageTitle', 'loginContent', 'redirectUrl'));
    }

    function login() {
        $this->validateLogin(request());

        request()->session()->regenerateToken();

        if(!verifyCaptcha()) {
            $toast[] = ['error', 'Invalid captcha provided'];
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

        $this->incrementLoginAttempts(request());

        return $this->sendFailedLoginResponse(request());
    }

    function findUsername() {
        $login     = request()->input('username');
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        request()->merge([$fieldType => $login]);
        return $fieldType;
    }

    function username() {
        return $this->username;
    }

    protected function validateLogin() {
        $this->validate(request(), [
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    function logout() {
        $this->guard()->logout();
        request()->session()->invalidate();

        $toast[] = ['success', 'Logout success'];
        return back()->withToasts($toast);
    }

    function authenticated(Request $request, $user) {
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
        if (!$url || !is_string($url)) {
            return false;
        }
        $url = trim($url);
        if (str_starts_with($url, '/')) {
            return !str_contains($url, '//');
        }
        $parsed = parse_url($url);
        $appUrl = parse_url(config('app.url'), PHP_URL_HOST);
        return isset($parsed['host']) && $parsed['host'] === $appUrl;
    }
}
