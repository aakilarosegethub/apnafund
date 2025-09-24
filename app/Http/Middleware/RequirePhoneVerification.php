<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RequirePhoneVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user has phone verification
            if (!$user->phone_verified_at) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Phone verification required',
                        'requires_phone_verification' => true
                    ], 403);
                }
                
                return redirect()->route('user.otp.login')
                    ->with('error', 'Please verify your phone number to continue.');
            }
        }

        return $next($request);
    }
}
