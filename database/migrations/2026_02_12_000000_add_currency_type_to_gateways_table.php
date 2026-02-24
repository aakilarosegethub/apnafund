<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Gateway operates only in this currency. User amount (from country) is converted to this before processing.
     */
    public function up(): void
    {
        Schema::table('gateways', function (Blueprint $table) {
            $table->string('currency_type', 10)->nullable()->after('countries')->comment('Currency in which this gateway operates (e.g. PKR, USD). User amount is converted from country currency to this.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gateways', function (Blueprint $table) {
            $table->dropColumn('currency_type');
        });
    }
};
