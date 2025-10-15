<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gateway;
use App\Models\GatewayCurrency;

class MWalletGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if MWallet gateway already exists
        if (Gateway::where('code', 'mwallet')->exists()) {
            $this->command->info('MWallet gateway already exists. Skipping...');
            return;
        }

        // Create MWallet gateway
        $gateway = Gateway::create([
            'code' => 'mwallet',
            'name' => 'MWallet Payment Gateway',
            'alias' => 'mwallet',
            'image' => 'mwallet.png',
            'gateway_parameters' => json_encode([
                'merchant_id' => [
                    'title' => 'Merchant ID',
                    'global' => true,
                    'value' => ''
                ],
                'api_key' => [
                    'title' => 'API Key',
                    'global' => true,
                    'value' => ''
                ],
                'secret_key' => [
                    'title' => 'Secret Key',
                    'global' => true,
                    'value' => ''
                ],
                'sandbox' => [
                    'title' => 'Sandbox Mode',
                    'global' => true,
                    'value' => '0'
                ]
            ]),
            'supported_currencies' => ['USD', 'PKR', 'EUR', 'GBP'],
            'extra' => null,
            'input_form' => null,
            'guideline' => 'Configure your MWallet payment gateway with the provided credentials. Enable sandbox mode for testing. Make sure to set up your webhook URL in MWallet dashboard.',
            'countries' => ['PK', 'US', 'GB', 'CA', 'AU', 'DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'AT', 'CH', 'SE', 'NO', 'DK', 'FI', 'IE', 'PT', 'GR', 'LU', 'CY', 'MT', 'SI', 'SK', 'CZ', 'HU', 'PL', 'LT', 'LV', 'EE', 'BG', 'RO', 'HR'],
            'status' => 1
        ]);

        // Create gateway currencies
        $currencies = [
            [
                'name' => 'MWallet - PKR',
                'currency' => 'PKR',
                'symbol' => 'Rs',
                'min_amount' => 100.00,
                'max_amount' => 1000000.00,
                'fixed_charge' => 0.00,
                'percent_charge' => 2.50,
                'rate' => 1.00
            ],
            [
                'name' => 'MWallet - USD',
                'currency' => 'USD',
                'symbol' => '$',
                'min_amount' => 1.00,
                'max_amount' => 10000.00,
                'fixed_charge' => 0.00,
                'percent_charge' => 2.50,
                'rate' => 1.00
            ],
            [
                'name' => 'MWallet - EUR',
                'currency' => 'EUR',
                'symbol' => '€',
                'min_amount' => 1.00,
                'max_amount' => 10000.00,
                'fixed_charge' => 0.00,
                'percent_charge' => 2.50,
                'rate' => 1.00
            ],
            [
                'name' => 'MWallet - GBP',
                'currency' => 'GBP',
                'symbol' => '£',
                'min_amount' => 1.00,
                'max_amount' => 10000.00,
                'fixed_charge' => 0.00,
                'percent_charge' => 2.50,
                'rate' => 1.00
            ]
        ];

        foreach ($currencies as $currencyData) {
            GatewayCurrency::create([
                'name' => $currencyData['name'],
                'currency' => $currencyData['currency'],
                'method_code' => 'mwallet',
                'gateway_alias' => 'mwallet',
                'gateway_parameter' => json_encode([
                    'merchant_id' => '',
                    'api_key' => '',
                    'secret_key' => '',
                    'sandbox' => '0'
                ]),
                'min_amount' => $currencyData['min_amount'],
                'max_amount' => $currencyData['max_amount'],
                'fixed_charge' => $currencyData['fixed_charge'],
                'percent_charge' => $currencyData['percent_charge'],
                'rate' => $currencyData['rate'],
                'symbol' => $currencyData['symbol'],
                'status' => 1
            ]);
        }

        $this->command->info('MWallet gateway and currencies created successfully!');
    }
}


