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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('email_from')->nullable()->after('site_email');
            $table->text('mail_config')->nullable()->after('email_from');
            $table->string('sms_from')->nullable()->after('mail_config');
            $table->text('email_template')->nullable()->after('sms_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['email_from', 'mail_config', 'sms_from', 'email_template']);
        });
    }
};
