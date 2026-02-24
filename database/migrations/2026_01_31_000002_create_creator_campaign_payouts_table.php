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
        Schema::create('creator_campaign_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->unique()->constrained()->onDelete('cascade');
            $table->timestamp('success_marked_at')->nullable();
            $table->decimal('total_raised', 15, 2)->default(0);
            $table->string('platform_fee_type', 20)->default('percentage');
            $table->decimal('platform_fee_value', 15, 2)->default(0);
            $table->decimal('platform_fee_amount', 15, 2)->default(0);
            $table->decimal('marketing_fee_percent', 5, 2)->default(0);
            $table->decimal('marketing_fee_amount', 15, 2)->default(0);
            $table->decimal('chargeback_withholding_percent', 5, 2)->default(5);
            $table->decimal('chargeback_withholding_amount', 15, 2)->default(0);
            $table->decimal('fulfillment_withholding_percent', 5, 2)->default(30);
            $table->decimal('fulfillment_withholding_amount', 15, 2)->default(0);
            $table->decimal('net_payable_amount', 15, 2)->default(0);
            $table->decimal('withheld_total_amount', 15, 2)->default(0);
            $table->decimal('released_withheld_amount', 15, 2)->default(0);
            $table->decimal('total_paid_amount', 15, 2)->default(0);
            $table->string('payout_status', 20)->default('pending');
            $table->string('fulfillment_status', 20)->default('pending');
            $table->timestamp('fulfillment_released_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creator_campaign_payouts');
    }
};
