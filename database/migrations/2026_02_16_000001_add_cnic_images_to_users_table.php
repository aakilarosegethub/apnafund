<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cnic_front_image')->nullable()->after('kyc_data');
            $table->string('cnic_back_image')->nullable()->after('cnic_front_image');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['cnic_front_image', 'cnic_back_image']);
        });
    }
};
