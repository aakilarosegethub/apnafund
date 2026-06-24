<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    // app/Exceptions/Handler.php

    /**
     * Handle unauthenticated user exceptions
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // For API routes, always return JSON error
        if ($request->is('api/*') || $request->expectsJson()) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthenticated! Please provide a valid token."
            ], 401);
        }

        // For web routes, try to redirect to login if route exists
        try {
            if (\Route::has('user.login')) {
                return redirect()->guest(route('user.login'));
            }
        } catch (\Exception $e) {
            // Route doesn't exist, just redirect to home
        }
        
        return redirect('/');
    }

    public function render($request, Throwable $exception)
    {
        // Handle unauthenticated exceptions for API requests (backup)
        if ($exception instanceof AuthenticationException) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Unauthenticated! Please provide a valid token."
                ], 401);
            }
        }

        if ($this->isHttpException($exception) && $exception->getStatusCode() === 404) {
            // Keep API/JSON clients on proper 404 response.
            if ($request->is('api/*') || $request->expectsJson()) {
                return parent::render($request, $exception);
            }

            return redirect()->to(url('/'));
        }
    
        return parent::render($request, $exception);
    }

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    
}
