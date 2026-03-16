<?php

namespace App\Http\Middleware;

use App\Services\CurrencyService;
use Closure;
use Illuminate\Http\Request;

class DetectCurrencyByIP
{
    /**
     * When TCUR is not set, detect currency from visitor IP and store in session.
     * Setting model will use this for site_cur and cur_sym display.
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('app.currency')) {
            return $next($request);
        }

        if (session()->has('user_detected_currency')) {
            return $next($request);
        }

        try {
            $country = getUserCountryByIP();
            if ($country) {
                $currencyService = app(CurrencyService::class);
                $code = $currencyService->resolveCurrencyCodeFromCountry($country);
                if ($code) {
                    session()->put('user_detected_currency', $code);
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('DetectCurrencyByIP failed', ['error' => $e->getMessage()]);
        }

        return $next($request);
    }
}
