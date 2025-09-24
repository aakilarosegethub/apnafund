<?php

namespace App\Http\Controllers\User;

use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class AuthorizationController extends Controller
{
    function authorizeForm() {
        $user = auth()->user();

        if (!$user->status) {
            $pageTitle = 'Banned';
            $type = 'ban';
        }elseif(!$user->ec) {
            $type = 'email';
            $pageTitle = 'Confirm Email';
            $toastTemplate = 'EVER_CODE';
        }elseif (!$user->sc) {
            $type = 'sms';
            $pageTitle = 'Confirm Mobile Number';
            $toastTemplate = 'SVER_CODE';
        }elseif (!$user->tc) {
            $pageTitle = '2FA Confirmation';
            $type = '2fa';
        }else{
            return to_route('user.home');
        }

        if (!$this->checkCodeValidity($user) && ($type != '2fa') && ($type != 'ban')) {
            $user->ver_code = verificationCode(6);
            $user->ver_code_send_at = now();
            $user->save();

            notify($user, $toastTemplate, [
                'code' => $user->ver_code
            ],[$type]);
        }

        return view($this->activeTheme. 'user.auth.authorization.'.$type, compact('user', 'pageTitle'));
    }

    function sendVerifyCode($type) {
        $user = auth()->user();

        if ($this->checkCodeValidity($user)) {
            $targetTime = $user->ver_code_send_at->addMinutes(2)->timestamp;
            $delay      = $targetTime - time();
            throw ValidationException::withMessages(['resend' => 'Please try after ' . $delay . ' seconds']);
        }

        $user->ver_code         = verificationCode(6);
        $user->ver_code_send_at = now();
        $user->save();

        if ($type == 'email') {
            $type = 'email';
            $toastTemplate = 'EVER_CODE';
        } else {
            $type = 'sms';
            $toastTemplate = 'SVER_CODE';
        }

        notify($user, $toastTemplate, [
            'code' => $user->ver_code
        ],[$type]);

        $toast[] = ['success', 'Verification code send success'];
        return back()->withToasts($toast);
    }

    function emailVerification() {
        $verCode = $this->codeValidation(request());
        $user    = auth()->user();

        if ($user->ver_code == $verCode) {
            $user->ec               = ManageStatus::VERIFIED;
            $user->ver_code         = null;
            $user->ver_code_send_at = null;
            $user->save();

            // Send welcome email after successful verification
            try {
                \Log::info('Email verified successfully for user: ' . $user->email . ' - Sending welcome email');
                
                // Send welcome email using the same method as verification email (admin-configured provider)
                notify($user, 'WELCOME_EMAIL', [
                    'name' => $user->firstname . ' ' . $user->lastname,
                    'username' => $user->username,
                    'email' => $user->email,
                    'mobile' => $user->mobile ?? 'Not provided',
                    'business_name' => $user->business_name ?? '',
                    'business_type' => $user->business_type ?? '',
                    'industry' => $user->industry ?? '',
                ], ['email']);
                
                \Log::info('Welcome email sent successfully to: ' . $user->email);
                
            } catch (\Exception $e) {
                \Log::error('Failed to send welcome email after verification to: ' . $user->email . ' - Error: ' . $e->getMessage());
            }

            // Return JSON response for API calls, redirect for form submissions
            if (request()->expectsJson() || request()->isJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email verified successfully',
                    'redirect' => route('user.home')
                ]);
            }

            return to_route('user.home');
        }

        // Return JSON response for API calls, throw exception for form submissions
        if (request()->expectsJson() || request()->isJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code didn\'t match!',
                'errors' => ['code' => ['Verification code didn\'t match!']]
            ], 422);
        }

        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }

    function emailVerificationApi() {
        // This method handles API calls without CSRF token requirement
        // but still requires authentication through session or token
        
        $verCode = $this->codeValidation(request());
        $user    = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
                'errors' => ['auth' => ['User must be logged in to verify email']]
            ], 401);
        }

        if ($user->ver_code == $verCode) {
            $user->ec               = ManageStatus::VERIFIED;
            $user->ver_code         = null;
            $user->ver_code_send_at = null;
            $user->save();

            // Send welcome email after successful verification
            try {
                \Log::info('Email verified successfully for user: ' . $user->email . ' - Sending welcome email');
                
                // Send welcome email using the same method as verification email (admin-configured provider)
                notify($user, 'WELCOME_EMAIL', [
                    'name' => $user->firstname . ' ' . $user->lastname,
                    'username' => $user->username,
                    'email' => $user->email,
                    'mobile' => $user->mobile ?? 'Not provided',
                    'business_name' => $user->business_name ?? '',
                    'business_type' => $user->business_type ?? '',
                    'industry' => $user->industry ?? '',
                ], ['email']);
                
                \Log::info('Welcome email sent successfully to: ' . $user->email);
                
            } catch (\Exception $e) {
                \Log::error('Failed to send welcome email after verification to: ' . $user->email . ' - Error: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully',
                'redirect' => route('user.home')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Verification code didn\'t match!',
            'errors' => ['code' => ['Verification code didn\'t match!']]
        ], 422);
    }

    function mobileVerification() {
        $verCode = $this->codeValidation(request());
        $user    = auth()->user();

        if ($user->ver_code == $verCode) {
            $user->sc               = ManageStatus::VERIFIED;
            $user->ver_code         = null;
            $user->ver_code_send_at = null;
            $user->save();

            return to_route('user.home');
        }

        throw ValidationException::withMessages(['code' => 'Verification code didn\'t match!']);
    }

    function g2faVerification() {
        $verCode  = $this->codeValidation(request());
        $user     = auth()->user();
        $response = verifyG2fa($user, $verCode);

        if ($response) {
            $toast[] = ['success', 'Verification success'];
        }else{
            $toast[] = ['error', 'Wrong verification code'];
        }

        return back()->withToasts($toast);
    }

    protected function checkCodeValidity($user, $addMin = 2) {
        if (!$user->ver_code_send_at){
            return false;
        }

        if ($user->ver_code_send_at->addMinutes($addMin) < now()) {
            return false;
        }

        return true;
    }

    protected function codeValidation() {
        $request = request();
        
        // Handle both array format (from form) and string format (from JSON API)
        if ($request->has('code') && is_array($request->input('code'))) {
            // Form submission with array format
            $this->validate($request, [
                'code'   => 'required|array|min:6',
                'code.*' => 'required|integer',
            ]);
            return (int)(implode("", $request->input('code')));
        } elseif ($request->has('code') && is_string($request->input('code'))) {
            // JSON API submission with string format
            $this->validate($request, [
                'code' => 'required|string|size:6|regex:/^[0-9]{6}$/',
            ]);
            return (int)$request->input('code');
        } else {
            // Fallback validation
            $this->validate($request, [
                'code' => 'required',
            ]);
            
            $code = $request->input('code');
            if (is_array($code)) {
                return (int)(implode("", $code));
            }
            return (int)$code;
        }
    }
}
