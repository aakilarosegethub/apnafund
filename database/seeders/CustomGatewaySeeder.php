<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gateway;
use App\Models\GatewayCurrency;

class CustomGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if gateway already exists
        if (Gateway::where('code', 'custom_gateway')->exists()) {
            return;
        }

        // Create the gateway
        $gateway = Gateway::create([
            'code' => 'custom_gateway',
            'name' => 'Custom Payment Gateway',
            'alias' => 'customgateway',
            'image' => 'custom-gateway.png',
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
            'supported_currencies' => json_encode(['USD', 'EUR', 'GBP', 'PKR']),
            'extra' => null,
            'input_form' => null,
            'guideline' => 'Configure your custom payment gateway with the provided credentials. Enable sandbox mode for testing.',
            'countries' => null, // Available in all countries
            'status' => 1
        ]);

        // Create gateway currency for USD
        GatewayCurrency::create([
            'name' => 'Custom Gateway - USD',
            'currency' => 'USD',
            'method_code' => 'custom_gateway',
            'gateway_parameter' => json_encode([
                'merchant_id' => '',
                'password' => '',
                'hash_key' => '',
                'return_url' => '',
                'sandbox' => '0'
            ]),
            'min_amount' => 1.00,
            'max_amount' => 10000.00,
            'fixed_charge' => 0.00,
            'percent_charge' => 2.50,
            'rate' => 1.00,
            'symbol' => '$',
            'status' => 1
        ]);
    }
}
