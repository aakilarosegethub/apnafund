<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gateway;
use App\Models\GatewayCurrency;

class JazzCashGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if gateway already exists
        if (Gateway::where('code', 'jazzcash')->exists()) {
            return;
        }

        // Create the JazzCash mobile wallet gateway
        $gateway = Gateway::create([
            'code' => 'jazzcash',
            'name' => 'JazzCash Mobile Wallet',
            'alias' => 'jazzcash',
            'image' => 'jazzcash-logo.png',
            'gateway_parameters' => json_encode([
                'merchant_id' => [
                    'title' => 'Merchant ID',
                    'global' => true,
                    'value' => ''
                ],
                'password' => [
                    'title' => 'Password',
                    'global' => true,
                    'value' => ''
                ],
                'hash_key' => [
                    'title' => 'Hash Key',
                    'global' => true,
                    'value' => ''
                ],
                'return_url' => [
                    'title' => 'Return URL',
                    'global' => true,
                    'value' => ''
                ],
                'sandbox' => [
                    'title' => 'Sandbox Mode',
                    'global' => true,
                    'value' => '0'
                ]
            ]),
            'supported_currencies' => json_encode(['PKR', 'USD']),
            'extra' => null,
            'input_form' => null,
            'guideline' => 'Configure your JazzCash Mobile Wallet gateway with the provided credentials. JazzCash is Pakistan\'s leading mobile wallet service. Enable sandbox mode for testing.',
            'countries' => ['Pakistan'], // Available in Pakistan
            'status' => 1
        ]);

        // Create gateway currency for PKR (Primary currency for JazzCash)
        GatewayCurrency::create([
            'name' => 'JazzCash - PKR',
            'currency' => 'PKR',
            'method_code' => 'jazzcash',
            'gateway_parameter' => json_encode([
                'merchant_id' => '',
                'password' => '',
                'hash_key' => '',
                'return_url' => '',
                'sandbox' => '0'
            ]),
            'min_amount' => 50.00,
            'max_amount' => 100000.00,
            'fixed_charge' => 0.00,
            'percent_charge' => 1.50,
            'rate' => 1.00,
            'symbol' => 'Rs',
            'status' => 1
        ]);

        // Create gateway currency for USD
        GatewayCurrency::create([
            'name' => 'JazzCash - USD',
            'currency' => 'USD',
            'method_code' => 'jazzcash',
            'gateway_parameter' => json_encode([
                'merchant_id' => '',
                'password' => '',
                'hash_key' => '',
                'return_url' => '',
                'sandbox' => '0'
            ]),
            'min_amount' => 1.00,
            'max_amount' => 1000.00,
            'fixed_charge' => 0.00,
            'percent_charge' => 1.50,
            'rate' => 280.00,
            'symbol' => '$',
            'status' => 1
        ]);
    }
}

