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
        'KW' => 'KWD',
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
        'KUWAIT' => 'KWD',
        'QATAR' => 'QAR',
        'OMAN' => 'OMR',
        'BAHRAIN' => 'BHD',
        'JORDAN' => 'JOD',
        'LEBANON' => 'LBP',
        'SRI LANKA' => 'LKR',
        'NEPAL' => 'NPR',
        'AFGHANISTAN' => 'AFN',
        'IRAQ' => 'IQD',
        'YEMEN' => 'YER',
        'VIETNAM' => 'VND',
        'SOUTH KOREA' => 'KRW',
        'NORTH KOREA' => 'KPW',
        'TAIWAN' => 'TWD',
        'PALESTINE' => 'ILS',
        'MOROCCO' => 'MAD',
        'TUNISIA' => 'TND',
        'ALGERIA' => 'DZD',
        'ARGENTINA' => 'ARS',
        'CHILE' => 'CLP',
        'COLOMBIA' => 'COP',
        'PERU' => 'PEN',
        'VENEZUELA' => 'VES',
        'UKRAINE' => 'UAH',
        'ROMANIA' => 'RON',
        'POLAND' => 'PLN',
        'CZECH REPUBLIC' => 'CZK',
        'HUNGARY' => 'HUF',
        'CROATIA' => 'EUR',
        'BULGARIA' => 'BGN',
        'SERBIA' => 'RSD',
        'BOSNIA AND HERZEGOVINA' => 'BAM',
        'ICELAND' => 'ISK',
        'LUXEMBOURG' => 'EUR',
        'BELGIUM' => 'EUR',
        'AUSTRIA' => 'EUR',
        'PORTUGAL' => 'EUR',
        'FINLAND' => 'EUR',
        'GREECE' => 'EUR',
        'SLOVAKIA' => 'EUR',
        'SLOVENIA' => 'EUR',
        'ESTONIA' => 'EUR',
        'LATVIA' => 'EUR',
        'LITHUANIA' => 'EUR',
        'MALTA' => 'EUR',
        'CYPRUS' => 'EUR',
        'KAZAKHSTAN' => 'KZT',
        'UZBEKISTAN' => 'UZS',
        'AZERBAIJAN' => 'AZN',
        'ARMENIA' => 'AMD',
        'GEORGIA' => 'GEL',
        'MONGOLIA' => 'MNT',
        'CAMBODIA' => 'KHR',
        'LAOS' => 'LAK',
        'MYANMAR' => 'MMK',
        'ETHIOPIA' => 'ETB',
        'GHANA' => 'GHS',
        'TANZANIA' => 'TZS',
        'UGANDA' => 'UGX',
        'ZAMBIA' => 'ZMW',
        'ZIMBABWE' => 'ZWL',
        'MOZAMBIQUE' => 'MZN',
        'ANGOLA' => 'AOA',
        'SENEGAL' => 'XOF',
        'IVORY COAST' => 'XOF',
        'COSTA RICA' => 'CRC',
        'PANAMA' => 'PAB',
        'URUGUAY' => 'UYU',
        'PARAGUAY' => 'PYG',
        'BOLIVIA' => 'BOB',
        'ECUADOR' => 'USD',
        'EL SALVADOR' => 'USD',
        'GUATEMALA' => 'GTQ',
        'HONDURAS' => 'HNL',
        'NICARAGUA' => 'NIO',
        'JAMAICA' => 'JMD',
        'TRINIDAD AND TOBAGO' => 'TTD',
        'FIJI' => 'FJD',
        'PAPUA NEW GUINEA' => 'PGK',
        'IRAN' => 'IRR',
        'LIBYA' => 'LYD',
        'SUDAN' => 'SDG',
        'SOUTH SUDAN' => 'SSP',
        'SOMALIA' => 'SOS',
        'RWANDA' => 'RWF',
        'MALAWI' => 'MWK',
        'BOTSWANA' => 'BWP',
        'NAMIBIA' => 'NAD',
        'LESOTHO' => 'LSL',
        'ESWATINI' => 'SZL',
        'SWAZILAND' => 'SZL',
        'MALDIVES' => 'MVR',
    ];

    /** ISO 3166-1 alpha-2 → ISO 4217 (merged with COUNTRY_CODE_TO_CURRENCY in currencyForIsoAlpha2). */
    private const EXTRA_ALPHA2_TO_CURRENCY = [
        'GB' => 'GBP',
        'LK' => 'LKR',
        'QA' => 'QAR',
        'OM' => 'OMR',
        'BH' => 'BHD',
        'NP' => 'NPR',
        'AF' => 'AFN',
        'BT' => 'BTN',
        'MV' => 'MVR',
        'MM' => 'MMK',
        'KH' => 'KHR',
        'LA' => 'LAK',
        'IR' => 'IRR',
        'IQ' => 'IQD',
        'SY' => 'SYP',
        'LB' => 'LBP',
        'JO' => 'JOD',
        'YE' => 'YER',
        'IL' => 'ILS',
        'PS' => 'ILS',
        'KZ' => 'KZT',
        'UZ' => 'UZS',
        'TJ' => 'TJS',
        'TM' => 'TMT',
        'KG' => 'KGS',
        'GE' => 'GEL',
        'AM' => 'AMD',
        'AZ' => 'AZN',
        'MN' => 'MNT',
        'KR' => 'KRW',
        'KP' => 'KPW',
        'TW' => 'TWD',
        'VN' => 'VND',
        'MA' => 'MAD',
        'TN' => 'TND',
        'DZ' => 'DZD',
        'AR' => 'ARS',
        'CL' => 'CLP',
        'CO' => 'COP',
        'PE' => 'PEN',
        'VE' => 'VES',
        'UA' => 'UAH',
        'RO' => 'RON',
        'PL' => 'PLN',
        'CZ' => 'CZK',
        'HU' => 'HUF',
        'BA' => 'BAM',
        'RS' => 'RSD',
        'IS' => 'ISK',
        'BG' => 'BGN',
        'AL' => 'ALL',
        'MK' => 'MKD',
        'MD' => 'MDL',
        'BY' => 'BYN',
        'ET' => 'ETB',
        'GH' => 'GHS',
        'TZ' => 'TZS',
        'UG' => 'UGX',
        'ZM' => 'ZMW',
        'ZW' => 'ZWL',
        'MZ' => 'MZN',
        'AO' => 'AOA',
        'SN' => 'XOF',
        'CI' => 'XOF',
        'BF' => 'XOF',
        'ML' => 'XOF',
        'NE' => 'XOF',
        'TG' => 'XOF',
        'BJ' => 'XOF',
        'GW' => 'XOF',
        'CR' => 'CRC',
        'PA' => 'PAB',
        'UY' => 'UYU',
        'PY' => 'PYG',
        'BO' => 'BOB',
        'GT' => 'GTQ',
        'HN' => 'HNL',
        'NI' => 'NIO',
        'JM' => 'JMD',
        'TT' => 'TTD',
        'FJ' => 'FJD',
        'PG' => 'PGK',
    ];

    /** Eurozone + microstates using EUR (ISO 3166-1 alpha-2). */
    private const EUROZONE_ALPHA2 = [
        'AT', 'BE', 'HR', 'CY', 'EE', 'FI', 'FR', 'DE', 'GR', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PT', 'SK', 'SI', 'ES',
        'AD', 'MC', 'SM', 'VA',
    ];

    /**
     * ISO 3166-1 alpha-3 → alpha-2. Stops "PAK"/"IND" being treated as ISO 4217 currency codes.
     */
    private const ISO3166_ALPHA3_TO_ALPHA2 = [
        'PAK' => 'PK',
        'IND' => 'IN',
        'BGD' => 'BD',
        'LKA' => 'LK',
        'NPL' => 'NP',
        'AFG' => 'AF',
        'BTN' => 'BT',
        'MDV' => 'MV',
        'MMR' => 'MM',
        'GBR' => 'GB',
        'USA' => 'US',
        'CAN' => 'CA',
        'AUS' => 'AU',
        'NZL' => 'NZ',
        'DEU' => 'DE',
        'FRA' => 'FR',
        'ITA' => 'IT',
        'ESP' => 'ES',
        'NLD' => 'NL',
        'BEL' => 'BE',
        'CHE' => 'CH',
        'SWE' => 'SE',
        'NOR' => 'NO',
        'DNK' => 'DK',
        'FIN' => 'FI',
        'POL' => 'PL',
        'CZE' => 'CZ',
        'HUN' => 'HU',
        'ROU' => 'RO',
        'UKR' => 'UA',
        'RUS' => 'RU',
        'TUR' => 'TR',
        'SAU' => 'SA',
        'ARE' => 'AE',
        'QAT' => 'QA',
        'OMN' => 'OM',
        'BHR' => 'BH',
        'KWT' => 'KW',
        'JOR' => 'JO',
        'LBN' => 'LB',
        'IRQ' => 'IQ',
        'IRN' => 'IR',
        'YEM' => 'YE',
        'ISR' => 'IL',
        'PSE' => 'PS',
        'EGY' => 'EG',
        'MAR' => 'MA',
        'TUN' => 'TN',
        'DZA' => 'DZ',
        'ZAF' => 'ZA',
        'NGA' => 'NG',
        'KEN' => 'KE',
        'CHN' => 'CN',
        'JPN' => 'JP',
        'KOR' => 'KR',
        'PRK' => 'KP',
        'TWN' => 'TW',
        'VNM' => 'VN',
        'IDN' => 'ID',
        'MYS' => 'MY',
        'PHL' => 'PH',
        'THA' => 'TH',
        'SGP' => 'SG',
        'MEX' => 'MX',
        'BRA' => 'BR',
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
        'KWD' => 3.25,
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
            $currency = $this->currencyForIsoAlpha2($countryCode);
        }

        if (!$currency && $countryName) {
            $currency = $this->resolveCurrencyCodeFromCountry($countryName);
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
            'INR' => '₹', 'SAR' => '﷼', 'AED' => 'د.إ', 'QAR' => 'QR', 'KWD' => 'د.ك', 'TRY' => '₺',
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
            // Country alias, not ISO 4217 (otherwise "USA" breaks USD matching in footer/gateways).
            if ($country === 'USA') {
                return 'USD';
            }
            static $threeLetterCountryAliases = [
                'UAE' => 'AED',
                'KSA' => 'SAR',
            ];
            if (isset($threeLetterCountryAliases[$country])) {
                return $threeLetterCountryAliases[$country];
            }

            // ISO 3166-1 alpha-3 (e.g. PAK = Pakistan → PKR), not ISO 4217
            if (isset(self::ISO3166_ALPHA3_TO_ALPHA2[$country])) {
                $resolved = $this->currencyForIsoAlpha2(self::ISO3166_ALPHA3_TO_ALPHA2[$country]);
                if ($resolved !== null) {
                    return $resolved;
                }
            }

            return $country;
        }

        if (strlen($country) === 2) {
            return $this->currencyForIsoAlpha2($country);
        }

        $fromName = self::COUNTRY_NAME_TO_CURRENCY[$country] ?? null;
        if ($fromName !== null) {
            return $fromName;
        }

        return $this->resolveCurrencyFromCountryJsonLabel($country);
    }

    /**
     * ISO 3166-1 alpha-2 → default circulating ISO 4217 code.
     */
    private function currencyForIsoAlpha2(string $alpha2): ?string
    {
        $alpha2 = strtoupper(trim($alpha2));
        if (strlen($alpha2) !== 2) {
            return null;
        }
        if (in_array($alpha2, self::EUROZONE_ALPHA2, true)) {
            return 'EUR';
        }

        $merged = array_merge(self::COUNTRY_CODE_TO_CURRENCY, self::EXTRA_ALPHA2_TO_CURRENCY);

        return $merged[$alpha2] ?? null;
    }

    /**
     * Build lookup from resource country labels (same file as donate/register) → alpha-2.
     *
     * @return array<string, string>
     */
    private static function countryJsonLabelToAlpha2Map(): array
    {
        static $map = null;
        if ($map !== null) {
            return $map;
        }
        $map = [];
        $path = resource_path('views/partials/country.json');
        if (!is_string($path) || !is_readable($path)) {
            return $map;
        }
        $raw = json_decode((string) file_get_contents($path), true);
        if (!is_array($raw)) {
            return $map;
        }
        foreach ($raw as $alpha2 => $row) {
            if (!is_array($row)) {
                continue;
            }
            $label = isset($row['country']) ? trim((string) $row['country']) : '';
            if ($label === '') {
                continue;
            }
            $a2 = strtoupper((string) $alpha2);
            if (strlen($a2) !== 2) {
                continue;
            }
            $spaced = strtoupper(preg_replace('/\s+/u', ' ', $label));
            $map[$spaced] = $a2;
            $alnum = strtoupper(preg_replace('/[^A-Za-z0-9]/u', '', $label));
            if ($alnum !== '') {
                $map[$alnum] = $a2;
            }
        }

        return $map;
    }

    private function resolveCurrencyFromCountryJsonLabel(string $upperCountryName): ?string
    {
        $lookup = self::countryJsonLabelToAlpha2Map();
        $alpha2 = $lookup[$upperCountryName] ?? null;
        if ($alpha2 === null) {
            $compact = strtoupper(preg_replace('/[^A-Z0-9]/u', '', $upperCountryName));
            $alpha2 = $lookup[$compact] ?? null;
        }
        if ($alpha2 === null || strlen($alpha2) !== 2) {
            return null;
        }

        return $this->currencyForIsoAlpha2($alpha2);
    }

    /**
     * ISO 3166-1 alpha-2 for a country label (e.g. "Pakistan" → "PK").
     * Gateway `countries` JSON often stores codes like PK/US, not full names.
     */
    public static function resolveIsoAlpha2FromCountryLabel(?string $countryName): ?string
    {
        $name = strtoupper(trim(preg_replace('/\s+/u', ' ', (string) $countryName)));
        if ($name === '') {
            return null;
        }
        if (strlen($name) === 2 && ctype_alpha($name)) {
            return $name;
        }
        $lookup = self::countryJsonLabelToAlpha2Map();
        $a2 = $lookup[$name] ?? null;
        if ($a2 === null) {
            $compact = strtoupper(preg_replace('/[^A-Z0-9]/u', '', $name));
            $a2 = $lookup[$compact] ?? null;
        }

        return (is_string($a2) && strlen($a2) === 2) ? $a2 : null;
    }
}
