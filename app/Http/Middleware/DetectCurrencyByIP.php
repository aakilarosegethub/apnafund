<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Sets session currency/country/symbol from IP when `config('app.currency')` is not fixed and the user has not chosen manual currency.
 */
class DetectCurrencyByIP
{
    /**
     * When TCUR is not set in .env: detect currency from visitor IP, store in DB (ip_currency_cache),
     * refresh every hour. Session stores currency, country, symbol for current request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (session('user_currency_manual')) {
            return $next($request);
        }

        if (config('app.currency')) {
            return $next($request);
        }

        $currentIp = request()->ip();
        // Re-detect when IP changed (e.g. user traveled, VPN, different network)
        if (session()->has('user_detected_currency') && session('user_detected_ip') === $currentIp) {
            return $next($request);
        }

        try {
            $data = getOrFetchIpCurrencyData($currentIp);
            if ($data && !empty($data['currency_code'])) {
                session()->put('user_detected_currency', $data['currency_code']);
                session()->put('user_detected_symbol', $data['currency_symbol'] ?? '$');
                session()->put('user_detected_country', $data['country_name'] ?? '');
                session()->put('user_detected_ip', $currentIp);
                session()->put('user_detected_ip', $currentIp);
                session()->put('user_detected_ip', $currentIp);
                session()->put('user_detected_ip', $currentIp);
                \Log::channel('single')->info('DetectCurrencyByIP: set session', ['currency' => $data['currency_code'], 'ip' => $currentIp]);
            } else {
                \Log::channel('single')->info('DetectCurrencyByIP: getOrFetchIpCurrencyData returned null/empty, falling back to DB', ['ip' => request()->ip()]);
            }
        } catch (\Throwable $e) {
            \Log::warning('DetectCurrencyByIP failed', ['error' => $e->getMessage(), 'ip' => request()->ip()]);
        }

        return $next($request);
    }
}
