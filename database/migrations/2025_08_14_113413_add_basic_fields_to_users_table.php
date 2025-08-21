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
            // Basic user fields
            $table->string('firstname')->nullable()->after('name');
            $table->string('lastname')->nullable()->after('firstname');
            $table->string('username')->unique()->nullable()->after('lastname');
            $table->string('mobile')->nullable()->after('email');
            $table->string('country_code')->nullable()->after('mobile');
            $table->string('country_name')->nullable()->after('country_code');
            $table->json('address')->nullable()->after('country_name');
            
            // Status fields
            $table->boolean('status')->default(1)->after('address');
            $table->boolean('kc')->default(0)->after('status');
            $table->boolean('ec')->default(0)->after('kc');
            $table->boolean('sc')->default(0)->after('ec');
            $table->boolean('ts')->default(0)->after('sc');
            $table->boolean('tc')->default(0)->after('ts');
            
            // Reference fields
            $table->unsignedBigInteger('ref_by')->default(0)->after('tc');
            
            // Verification fields
            $table->string('ver_code')->nullable()->after('ref_by');
            $table->timestamp('ver_code_send_at')->nullable()->after('ver_code');
            
            // Balance field
            $table->decimal('balance', 18, 8)->default(0)->after('ver_code_send_at');
            
            // KYC data
            $table->json('kyc_data')->nullable()->after('balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'firstname',
                'lastname',
                'username',
                'mobile',
                'country_code',
                'country_name',
                'address',
                'status',
                'kc',
                'ec',
                'sc',
                'ts',
                'tc',
                'ref_by',
                'ver_code',
                'ver_code_send_at',
                'balance',
                'kyc_data'
            ]);
        });
    }
};
