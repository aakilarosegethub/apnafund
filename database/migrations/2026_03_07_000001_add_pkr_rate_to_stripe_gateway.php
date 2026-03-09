<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add PKR conversion rate to Stripe (gateway code 114) so PKR payments work.
     * Rate: 1 PKR = 0.0036 USD (approx 1 USD = 278 PKR)
     */
    public function up(): void
    {
        $updated = DB::table('gateway_currencies')
            ->where('method_code', '114')
            ->where('currency', 'USD')
            ->where('status', 1)
            ->update([
                'input_currency_rates' => json_encode(['PKR' => 0.0036]),
                'updated_at' => now(),
            ]);

        if ($updated) {
            \Log::info('Stripe gateway: Added PKR conversion rate for USD row');
        }
    }

    public function down(): void
    {
        DB::table('gateway_currencies')
            ->where('method_code', '114')
            ->where('currency', 'USD')
            ->update([
                'input_currency_rates' => null,
                'updated_at' => now(),
            ]);
    }
};
