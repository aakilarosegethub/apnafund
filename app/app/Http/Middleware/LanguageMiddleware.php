<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Sets the application locale from session (`lang`, default `en`).
 */
class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $lang = $this->getCode();

        if (! session()->has('lang')) {
            session()->put('lang', $lang);
        }

        app()->setLocale($lang);

        return $next($request);
    }

    /**
     * Get the language code.
     */
    public function getCode(): string
    {
        return session('lang', 'en');
    }
}
