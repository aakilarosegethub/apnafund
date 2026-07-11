<?php

namespace App\Http\Controllers\Api;

use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Public currency snapshot for mobile/web clients.
 * Local currency = TCUR / IP detection (same as site helpers); optional ?currency=PKR override.
 */
class CurrencyInfoController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $service = app(CurrencyService::class);

        $platformCode = getPlatformCurrency();
        $setting = bs();
        $platformSymbol = $setting->cur_sym ?? CurrencyService::getSymbolForCode($platformCode);

        // Query string, form, or JSON body: ?currency= / {"currency":"PKR"}
        $currencyOverride = $request->input('currency');
        if ($currencyOverride !== null && $currencyOverride !== '') {
            $localCode = $service->normalizeCode((string) $currencyOverride);
            $localSymbol = CurrencyService::getSymbolForCode($localCode);
        } else {
            $localCode = getLocalCurrencyCode();
            $localSymbol = getLocalCurrencySymbol();
        }

        $ipData = getOrFetchIpCurrencyData();
        $countryCode = $ipData['country_code'] ?? null;
        $countryName = $ipData['country_name'] ?? null;

        // How many units of local currency = 1 unit of platform (DB) currency
        $exchangeRate = 1.0;
        if (strtoupper($localCode) !== strtoupper($platformCode)) {
            try {
                $exchangeRate = round((float) $service->convertFromPlatform(1.0, $localCode), 6);
            } catch (\Throwable $e) {
                \Log::warning('currency_info convertFromPlatform failed', [
                    'local' => $localCode,
                    'platform' => $platformCode,
                    'error' => $e->getMessage(),
                ]);
                $exchangeRate = 1.0;
            }
        }

        $localModel = $service->getOrCreateByCode($localCode);
        $rateLocalToUsd = round((float) $service->getRateToUsd($localModel), 8);

        return response()->json([
            'ResponseCode' => '200',
            'Result' => 'true',
            'ResponseMsg' => 'Currency information',
            'platform_currency' => strtoupper($platformCode),
            'platform_currency_symbol' => (string) $platformSymbol,
            'local_currency' => strtoupper($localCode),
            'local_currency_symbol' => (string) $localSymbol,
            /** Multiply stored platform amounts by this to show in local_currency */
            'exchange_rate' => $exchangeRate,
            /** Meaning: 1 local_currency = this many USD (internal model; same as currencies.rate_to_usd) */
            'local_rate_to_usd' => $rateLocalToUsd,
            'country_code' => $countryCode,
            'country_name' => $countryName,
        ]);
    }
}
