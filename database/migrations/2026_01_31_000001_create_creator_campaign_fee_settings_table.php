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
        Schema::create('creator_campaign_fee_settings', function (Blueprint $table) {
            $table->id();
            $table->string('platform_fee_type', 20)->default('percentage');
            $table->decimal('platform_fee_value', 15, 2)->default(0);
            $table->decimal('marketing_fee_percent', 5, 2)->default(0);
            $table->decimal('chargeback_withholding_percent', 5, 2)->default(5);
            $table->decimal('fulfillment_withholding_percent', 5, 2)->default(30);
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creator_campaign_fee_settings');
    }
};
