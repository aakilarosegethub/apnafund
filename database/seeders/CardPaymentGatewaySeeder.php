<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Gateway;
use App\Models\GatewayCurrency;

class CardPaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if gateway already exists
        $gateway = Gateway::where('code', 'card_payment')->first();
        
        if (!$gateway) {
            // Create the CardPayment gateway
            $gateway = Gateway::create([
            'code' => 'card_payment',
            'name' => 'Card Payment Gateway v1.1',
            'alias' => 'CardPayment',
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
            'supported_currencies' => json_encode(['USD', 'EUR', 'GBP', 'PKR', 'CAD', 'AUD']),
            'crypto' => 0,
            'extra' => null,
            'guideline' => 'Configure your Card Payment Gateway v1.1 with the provided credentials. This gateway supports Page Redirection v1.1 template for card payments. Enable sandbox mode for testing.',
            'countries' => null, // Available in all countries
            'status' => 1
            ]);
        }

        // Check if currencies already exist
        if (GatewayCurrency::where('method_code', 'card_payment')->exists()) {
            return;
        }

        // Create gateway currency for PKR (Primary currency)
        GatewayCurrency::create([
            'name' => 'Card Payment - PKR',
            'currency' => 'PKR',
            'symbol' => 'Rs',
            'method_code' => 'card_payment',
            'gateway_alias' => 'CardPayment',
            'gateway_parameter' => json_encode([
                'merchant_id' => '',
                'password' => '',
                'hash_key' => '',
                'return_url' => '',
                'sandbox' => '0'
            ]),
            'min_amount' => 100.00,
            'max_amount' => 5000000.00,
            'fixed_charge' => 0.00,
            'percent_charge' => 2.90,
            'rate' => 280.00,
            'status' => 1
        ]);

        // Create gateway currency for USD
        GatewayCurrency::create([
            'name' => 'Card Payment - USD',
            'currency' => 'USD',
            'symbol' => '$',
            'method_code' => 'card_payment',
            'gateway_alias' => 'CardPayment',
            'gateway_parameter' => json_encode([
                'merchant_id' => '',
                'password' => '',
                'hash_key' => '',
                'return_url' => '',
                'sandbox' => '0'
            ]),
            'min_amount' => 1.00,
            'max_amount' => 50000.00,
            'fixed_charge' => 0.00,
            'percent_charge' => 2.90,
            'rate' => 1.00,
            'status' => 1
        ]);
    }
}
