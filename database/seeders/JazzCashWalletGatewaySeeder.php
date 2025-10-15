<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gateway;
use App\Models\GatewayCurrency;

class JazzCashWalletGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Check if gateway already exists
        $existingGateway = Gateway::where('code', 'jazzcash_wallet')->first();
        if ($existingGateway) {
            $this->command->info('JazzCash Wallet gateway already exists. Skipping...');
            return;
        }

        // Create the gateway
        $gateway = Gateway::create([
            'code' => 'jazzcash_wallet',
            'name' => 'JazzCash Wallet Payment',
            'alias' => 'jazzcashwallet',
            'image' => 'jazzcash-wallet.png',
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
                'integrity_salt' => [
                    'title' => 'Integrity Salt',
                    'global' => true,
                    'value' => ''
                ],
                'sandbox' => [
                    'title' => 'Sandbox Mode',
                    'global' => true,
                    'value' => '0'
                ]
            ]),
            'supported_currencies' => json_encode(['PKR']),
            'extra' => null,
            'input_form' => null,
            'guideline' => 'Configure your JazzCash Wallet payment gateway with the provided credentials. Enable sandbox mode for testing. Users will need to enter their phone number and CNIC last 6 digits for payment processing.',
            'countries' => ['PK'],
            'status' => 1
        ]);

        // Create the gateway currency
        GatewayCurrency::create([
            'name' => 'JazzCash Wallet - PKR',
            'currency' => 'PKR',
            'method_code' => 'jazzcash_wallet',
            'gateway_parameter' => json_encode([
                'merchant_id' => '',
                'password' => '',
                'integrity_salt' => '',
                'sandbox' => '0'
            ]),
            'min_amount' => 100.00,
            'max_amount' => 1000000.00,
            'fixed_charge' => 0.00,
            'percent_charge' => 2.50,
            'rate' => 1.00,
            'symbol' => 'Rs',
            'status' => 1
        ]);

        $this->command->info('JazzCash Wallet gateway created successfully!');
    }
}
