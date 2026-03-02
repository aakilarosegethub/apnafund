<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * input_currency_rates: JSON e.g. {"PKR": 0.0035} = 1 PKR = 0.0035 gateway_currency
     * When contributor pays in PKR but gateway needs USD, system converts using this rate.
     */
    public function up(): void
    {
        Schema::table('gateway_currencies', function (Blueprint $table) {
            $table->json('input_currency_rates')->nullable()->after('rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gateway_currencies', function (Blueprint $table) {
            $table->dropColumn('input_currency_rates');
        });
    }
};
