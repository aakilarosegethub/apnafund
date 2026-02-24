<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->decimal('original_amount', 15, 2)->default(0)->after('amount');
            $table->string('original_currency', 10)->default('USD')->after('original_amount');
            $table->decimal('usd_amount', 15, 2)->default(0)->after('original_currency');
            $table->decimal('exchange_rate', 18, 8)->default(1)->after('usd_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn(['original_amount', 'original_currency', 'usd_amount', 'exchange_rate']);
        });
    }
};
