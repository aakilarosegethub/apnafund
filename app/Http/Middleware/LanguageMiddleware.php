<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $lang = $this->getCode();

        if (!session()->has('lang')) session()->put('lang', $lang);

        app()->setLocale($lang);

        return $next($request);
    }

    /**
     * Get the language code.
     *
     * @return string
     */
    public function getCode(): string
    {
        return session('lang', 'en');
    }
}
