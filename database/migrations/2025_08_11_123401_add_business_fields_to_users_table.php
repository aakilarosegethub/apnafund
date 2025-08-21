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
        Schema::table('users', function (Blueprint $table) {
            // Business Information Fields
            $table->string('business_type')->nullable()->after('email');
            $table->string('business_name')->nullable()->after('business_type');
            $table->text('business_description')->nullable()->after('business_name');
            $table->string('industry')->nullable()->after('business_description');
            
            // Funding Information Fields
            $table->string('funding_amount')->nullable()->after('industry');
            $table->string('fund_usage')->nullable()->after('funding_amount');
            $table->string('campaign_duration')->nullable()->after('fund_usage');
            
            // Additional Contact Information
            $table->string('phone')->nullable()->after('mobile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'business_type',
                'business_name', 
                'business_description',
                'industry',
                'funding_amount',
                'fund_usage',
                'campaign_duration',
                'phone'
            ]);
        });
    }
};
