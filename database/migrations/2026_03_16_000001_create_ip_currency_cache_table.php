<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Store IP → country, currency, symbol. Refresh every hour.
     */
    public function up(): void
    {
        Schema::create('ip_currency_cache', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45);
            $table->string('country_code', 10)->nullable();
            $table->string('country_name', 100)->nullable();
            $table->string('currency_code', 10)->default('USD');
            $table->string('currency_symbol', 20)->default('$');
            $table->timestamp('refreshed_at');
            $table->timestamps();
            $table->unique('ip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_currency_cache');
    }
};
