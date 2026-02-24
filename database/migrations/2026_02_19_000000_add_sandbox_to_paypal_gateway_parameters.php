<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add Sandbox option to PayPal SDK gateway so it appears in Admin and is stored in DB.
     * Sandbox is in gateways.gateway_parameters (defines the field) and in
     * gateway_currencies.gateway_parameter (saved value per currency).
     */
    public function up(): void
    {
        $gateway = DB::table('gateways')
            ->where('alias', 'PaypalSdk')
            ->orWhere('alias', 'paypal-sdk')
            ->first();

        if (!$gateway) {
            return;
        }

        $params = json_decode($gateway->gateway_parameters, true);
        if (!is_array($params)) {
            $params = [];
        }

        if (!isset($params['sandbox'])) {
            $params['sandbox'] = [
                'title'  => 'Sandbox Mode',
                'global' => true,
                'value'  => '0',
            ];
            DB::table('gateways')
                ->where('id', $gateway->id)
                ->update(['gateway_parameters' => json_encode($params)]);
        }

        // Add sandbox value to each gateway_currency for this gateway
        $currencies = DB::table('gateway_currencies')->where('method_code', $gateway->code)->get();
        foreach ($currencies as $row) {
            $param = json_decode($row->gateway_parameter, true);
            if (is_array($param) && !array_key_exists('sandbox', $param)) {
                $param['sandbox'] = '0';
                DB::table('gateway_currencies')
                    ->where('id', $row->id)
                    ->update(['gateway_parameter' => json_encode($param)]);
            }
        }
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        $gateway = DB::table('gateways')
            ->where('alias', 'PaypalSdk')
            ->orWhere('alias', 'paypal-sdk')
            ->first();

        if (!$gateway) {
            return;
        }

        $params = json_decode($gateway->gateway_parameters, true);
        if (is_array($params) && isset($params['sandbox'])) {
            unset($params['sandbox']);
            DB::table('gateways')
                ->where('id', $gateway->id)
                ->update(['gateway_parameters' => json_encode($params)]);
        }

        $currencies = DB::table('gateway_currencies')->where('method_code', $gateway->code)->get();
        foreach ($currencies as $row) {
            $param = json_decode($row->gateway_parameter, true);
            if (is_array($param) && array_key_exists('sandbox', $param)) {
                unset($param['sandbox']);
                DB::table('gateway_currencies')
                    ->where('id', $row->id)
                    ->update(['gateway_parameter' => json_encode($param)]);
            }
        }
    }
};
