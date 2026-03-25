<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\User;

class CurrencyService
{
    public const BASE_CURRENCY = 'USD';
    private const DEFAULT_SOURCE = 'default';

    private const COUNTRY_CODE_TO_CURRENCY = [
        'US' => 'USD',
        'PK' => 'PKR',
        'IN' => 'INR',
        'BD' => 'BDT',
        'AE' => 'AED',
        'SA' => 'SAR',
        'GB' => 'GBP',
        'UK' => 'GBP',
        'CA' => 'CAD',
        'AU' => 'AUD',
        'NZ' => 'NZD',
        'EU' => 'EUR',
        'DE' => 'EUR',
        'FR' => 'EUR',
        'ES' => 'EUR',
        'IT' => 'EUR',
        'NL' => 'EUR',
        'IE' => 'EUR',
        'SE' => 'SEK',
        'NO' => 'NOK',
        'DK' => 'DKK',
        'CH' => 'CHF',
        'JP' => 'JPY',
        'CN' => 'CNY',
        'HK' => 'HKD',
        'SG' => 'SGD',
        'MY' => 'MYR',
        'ID' => 'IDR',
        'TH' => 'THB',
        'PH' => 'PHP',
        'ZA' => 'ZAR',
        'NG' => 'NGN',
        'KE' => 'KES',
        'EG' => 'EGP',
        'BR' => 'BRL',
        'MX' => 'MXN',
        'TR' => 'TRY',
        'RU' => 'RUB',
    ];

    private const COUNTRY_NAME_TO_CURRENCY = [
        'PAKISTAN' => 'PKR',
        'UNITED STATES' => 'USD',
        'UNITED STATES OF AMERICA' => 'USD',
        'UNITED KINGDOM' => 'GBP',
        'GREAT BRITAIN' => 'GBP',
        'INDIA' => 'INR',
        'BANGLADESH' => 'BDT',
        'UNITED ARAB EMIRATES' => 'AED',
        'SAUDI ARABIA' => 'SAR',
        'CANADA' => 'CAD',
        'AUSTRALIA' => 'AUD',
        'NEW ZEALAND' => 'NZD',
        'GERMANY' => 'EUR',
        'FRANCE' => 'EUR',
        'SPAIN' => 'EUR',
        'ITALY' => 'EUR',
        'NETHERLANDS' => 'EUR',
        'IRELAND' => 'EUR',
        'SWEDEN' => 'SEK',
        'NORWAY' => 'NOK',
        'DENMARK' => 'DKK',
        'SWITZERLAND' => 'CHF',
        'JAPAN' => 'JPY',
        'CHINA' => 'CNY',
        'HONG KONG' => 'HKD',
        'SINGAPORE' => 'SGD',
        'MALAYSIA' => 'MYR',
        'INDONESIA' => 'IDR',
        'THAILAND' => 'THB',
        'PHILIPPINES' => 'PHP',
        'SOUTH AFRICA' => 'ZAR',
        'NIGERIA' => 'NGN',
        'KENYA' => 'KES',
        'EGYPT' => 'EGP',
        'BRAZIL' => 'BRL',
        'MEXICO' => 'MXN',
        'TURKEY' => 'TRY',
        'RUSSIA' => 'RUB',
    ];

    public function getOrCreateByCode(?string $code): Currency
    {
        $normalized = $this->normalizeCode($code);

        return Currency::firstOrCreate(
            ['code' => $normalized],
            ['rate_to_usd' => 1, 'source' => self::DEFAULT_SOURCE]
        );
    }

    public function getRateToUsd(Currency $currency): float
    {
        if ($currency->rate_to_usd === null || $currency->rate_to_usd <= 0) {
            $currency->rate_to_usd = 1;
            if (!$currency->source) {
                $currency->source = self::DEFAULT_SOURCE;
            }
            $currency->save();
        }

        return (float) $currency->rate_to_usd ?? 1;
    }

    public function convertToUsd(float $amount, Currency $currency): float
    {
        $rate = $this->getRateToUsd($currency);

        return $amount * $rate;
    }

    /**
     * Approximate rate_to_usd fallbacks when DB has no rate (1 unit = X USD).
     * Used when currencies table is empty; Admin > Currencies > Sync for accurate rates.
     */
    private const FALLBACK_RATE_TO_USD = [
        'PKR' => 0.0036,  'INR' => 0.012,  'BDT' => 0.009,
        'TRY' => 0.029,   'EUR' => 1.05,   'GBP' => 1.27,
        'AED' => 0.27,    'SAR' => 0.27,   'CAD' => 0.72,
        'AUD' => 0.65,    'JPY' => 0.0067, 'CNY' => 0.14,
    ];

    /**
     * Convert USD amount to target currency. DB stores USD; use this for frontend display.
     * rate_to_usd = how many USD per 1 unit of target. So: target_amount = usd_amount / rate_to_usd.
     */
    public function convertUsdTo(float $usdAmount, string $targetCode): float
    {
        $targetCode = $this->normalizeCode($targetCode);
        if ($targetCode === self::BASE_CURRENCY) {
            return $usdAmount;
        }
        $target = $this->getOrCreateByCode($targetCode);
        $rate = $this->getRateToUsd($target);

        // If DB has default rate 1 for non-USD, use fallback
        if ($rate >= 0.999 && $rate <= 1.001 && isset(self::FALLBACK_RATE_TO_USD[$targetCode])) {
            $rate = self::FALLBACK_RATE_TO_USD[$targetCode];
        }

        return $rate > 0 ? $usdAmount / $rate : $usdAmount;
    }

    /**
     * Convert amount from one currency to another (via USD).
     */
    public function convertAmount(float $amount, Currency $from, Currency $to): float
    {
        if ($from->code === $to->code) {
            return $amount;
        }
        $usd = $this->convertToUsd($amount, $from);
        $toRate = $this->getRateToUsd($to);

        return $toRate > 0 ? $usd / $toRate : $usd;
    }

    /**
     * Convert amount from user's currency TO platform currency (for saving to DB).
     * Platform currency = Admin-set currency in which all amounts are stored.
     */
    public function convertToPlatform(float $amount, string $fromCurrencyCode): float
    {
        $platform = strtoupper(trim((string) (getPlatformCurrency())));
        $fromCode = $this->normalizeCode($fromCurrencyCode);
        if ($fromCode === $platform) {
            return $amount;
        }
        $from = $this->getOrCreateByCode($fromCode);
        $to = $this->getOrCreateByCode($platform);
        return $this->convertAmount($amount, $from, $to);
    }

    /**
     * Convert amount FROM platform currency TO display currency (for frontend).
     */
    public function convertFromPlatform(float $amount, string $toCurrencyCode): float
    {
        $platform = strtoupper(trim((string) (getPlatformCurrency())));
        $toCode = $this->normalizeCode($toCurrencyCode);
        if ($toCode === $platform) {
            return $amount;
        }
        $from = $this->getOrCreateByCode($platform);
        $to = $this->getOrCreateByCode($toCode);
        return $this->convertAmount($amount, $from, $to);
    }

    public function normalizeCode(?string $code): string
    {
        $code = trim((string) $code);

        return $code === '' ? self::BASE_CURRENCY : strtoupper($code);
    }

    public function detectCurrencyCode(?User $user = null, ?string $fallbackCountry = null): string
    {
        $countryCode = $user?->country_code ? strtoupper(trim((string) $user->country_code)) : null;
        $countryName = $user?->country_name ? strtoupper(trim((string) $user->country_name)) : null;

        if (!$countryCode && !$countryName) {
            $fallback = $fallbackCountry ?: getUserCountryByIP();
            if ($fallback) {
                $fallback = trim((string) $fallback);
                if (strlen($fallback) === 2) {
                    $countryCode = strtoupper($fallback);
                } else {
                    $countryName = strtoupper($fallback);
                }
            }
        }

        $currency = null;
        if ($countryCode) {
            $currency = self::COUNTRY_CODE_TO_CURRENCY[$countryCode] ?? null;
        }

        if (!$currency && $countryName) {
            $currency = self::COUNTRY_NAME_TO_CURRENCY[$countryName] ?? null;
        }

        return $currency ?: self::BASE_CURRENCY;
    }

    /**
     * Get currency symbol for code. Used by IP cache and Setting.
     */
    public static function getSymbolForCode(?string $code): string
    {
        $map = [
            'USD' => '$', 'PKR' => 'Rs', 'EUR' => '€', 'GBP' => '£',
            'INR' => '₹', 'SAR' => '﷼', 'AED' => 'د.إ', 'QAR' => 'QR', 'TRY' => '₺',
            'CAD' => 'C$', 'AUD' => 'A$', 'NZD' => 'NZ$', 'SEK' => 'kr',
            'NOK' => 'kr', 'DKK' => 'kr', 'CHF' => 'CHF', 'JPY' => '¥',
            'CNY' => '¥', 'HKD' => 'HK$', 'SGD' => 'S$', 'MYR' => 'RM',
            'BDT' => '৳', 'IDR' => 'Rp', 'THB' => '฿', 'PHP' => '₱',
            'ZAR' => 'R', 'NGN' => '₦', 'KES' => 'KSh', 'EGP' => 'E£',
            'BRL' => 'R$', 'MXN' => '$', 'RUB' => '₽',
        ];
        return $map[strtoupper(trim((string) $code ?? ''))] ?? '$';
    }

    public function resolveCurrencyCodeFromCountry(?string $country): ?string
    {
        $country = strtoupper(trim((string) $country));

        if ($country === '') {
            return null;
        }

        if (strlen($country) === 3 && ctype_alpha($country)) {
            return $country;
        }

        if (strlen($country) === 2) {
            return self::COUNTRY_CODE_TO_CURRENCY[$country] ?? null;
        }

        return self::COUNTRY_NAME_TO_CURRENCY[$country] ?? null;
    }
}
