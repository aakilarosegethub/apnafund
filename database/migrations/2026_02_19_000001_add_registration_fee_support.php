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
        if (!Schema::hasColumn('deposits', 'deposit_type')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->string('deposit_type', 30)->default('donation')->after('campaign_id');
            });
        }

        if (!Schema::hasColumn('transactions', 'campaign_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->unsignedBigInteger('campaign_id')->nullable()->after('user_id');
            });
        }

        if (!Schema::hasColumn('settings', 'registration_fee_enabled')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->boolean('registration_fee_enabled')->default(false)->after('per_page_item');
                $table->decimal('registration_fee_min', 15, 2)->default(1)->after('registration_fee_enabled');
                $table->decimal('registration_fee_max', 15, 2)->default(100)->after('registration_fee_min');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('deposits', 'deposit_type')) {
            Schema::table('deposits', function (Blueprint $table) {
                $table->dropColumn('deposit_type');
            });
        }

        if (Schema::hasColumn('transactions', 'campaign_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropColumn('campaign_id');
            });
        }

        if (Schema::hasColumn('settings', 'registration_fee_enabled')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn(['registration_fee_enabled', 'registration_fee_min', 'registration_fee_max']);
            });
        }
    }
};
