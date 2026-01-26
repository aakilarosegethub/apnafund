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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->foreignId('payout_bank_id')->nullable()->after('featured')->constrained('payout_banks')->onDelete('set null');
            $table->string('bank_account_number')->nullable()->after('payout_bank_id');
            $table->string('bank_account_email')->nullable()->after('bank_account_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropForeign(['payout_bank_id']);
            $table->dropColumn(['payout_bank_id', 'bank_account_number', 'bank_account_email']);
        });
    }
};
