<?php

namespace App\Http\Controllers\Api;

use App\Services\CurrencyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Allowed countries (Admin → Basic → Allowed Countries for Project Location) for mobile/start-project flows.
 * country_id is the 1-based index in getAdminDefaultAllCountryNames() (same order as admin country list).
 */
class AllowedLocationCountriesController extends BaseApiController
{
    public function allowedList(): JsonResponse
    {
        $names = getSiteAllowedCountryNames();
        $all = getAdminDefaultAllCountryNames();
        $countries = [];
        foreach ($names as $name) {
            $idx = array_search($name, $all, true);
            if ($idx === false) {
                continue;
            }
            $countries[] = [
                'country_id' => $idx + 1,
                'country_name' => $name,
            ];
        }

        return response()->json([
            'ResponseCode' => '200',
            'Result' => 'true',
            'ResponseMsg' => 'Allowed countries for project location',
            'countries' => $countries,
        ]);
    }

    public function currencyByCountry(Request $request): JsonResponse
    {
        $countryId = $request->input('country_id');
        if ($countryId === null || $countryId === '') {
            return response()->json([
                'ResponseCode' => '400',
                'Result' => 'false',
                'ResponseMsg' => 'country_id is required',
            ], 400);
        }

        $countryId = (int) $countryId;
        $all = getAdminDefaultAllCountryNames();
        if ($countryId < 1 || $countryId > count($all)) {
            return response()->json([
                'ResponseCode' => '400',
                'Result' => 'false',
                'ResponseMsg' => 'Invalid country_id',
            ], 400);
        }

        $countryName = $all[$countryId - 1];
        $allowed = getSiteAllowedCountryNames();
        if (! in_array($countryName, $allowed, true)) {
            return response()->json([
                'ResponseCode' => '400',
                'Result' => 'false',
                'ResponseMsg' => 'Country is not allowed for project location on this site',
            ], 400);
        }

        $service = app(CurrencyService::class);
        $platformCode = getPlatformCurrency();
        $setting = bs();
        $platformSymbol = $setting->cur_sym ?? CurrencyService::getSymbolForCode($platformCode);

        $localCode = getCurrencyCodeForCountryName($countryName);
        $localSymbol = CurrencyService::getSymbolForCode($localCode);

        $exchangeRate = 1.0;
        if (strtoupper($localCode) !== strtoupper($platformCode)) {
            try {
                $exchangeRate = round((float) $service->convertFromPlatform(1.0, $localCode), 6);
            } catch (\Throwable $e) {
                \Log::warning('currency_info_by_country convertFromPlatform failed', [
                    'local' => $localCode,
                    'platform' => $platformCode,
                    'error' => $e->getMessage(),
                ]);
                $exchangeRate = 1.0;
            }
        }

        $localModel = $service->getOrCreateByCode($localCode);
        $rateLocalToUsd = round((float) $service->getRateToUsd($localModel), 8);

        $countryCode = CurrencyService::resolveIsoAlpha2FromCountryLabel($countryName);

        return response()->json([
            'ResponseCode' => '200',
            'Result' => 'true',
            'ResponseMsg' => 'Currency information',
            'country_id' => $countryId,
            'country_name' => $countryName,
            'platform_currency' => strtoupper($platformCode),
            'platform_currency_symbol' => (string) $platformSymbol,
            'local_currency' => strtoupper($localCode),
            'local_currency_symbol' => (string) $localSymbol,
            'exchange_rate' => $exchangeRate,
            'local_rate_to_usd' => $rateLocalToUsd,
            'country_code' => $countryCode,
        ]);
    }
}
