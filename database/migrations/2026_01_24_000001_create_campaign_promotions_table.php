<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_promotions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->string('meta_campaign_id')->nullable();
            $table->string('meta_adset_id')->nullable();
            $table->string('meta_ad_id')->nullable();
            $table->string('meta_creative_id')->nullable();
            $table->string('status')->default('pending'); // pending, active, paused, error
            $table->decimal('daily_budget', 10, 2)->default(10.00); // Budget in USD
            $table->text('error_message')->nullable();
            $table->timestamp('promoted_at')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
            
            // Index for faster queries
            $table->index('campaign_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaign_promotions');
    }
};
