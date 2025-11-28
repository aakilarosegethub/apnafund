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
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->string('act')->unique()->after('id');
            $table->string('name')->after('act');
            $table->string('subj')->after('name');
            $table->longText('email_body')->nullable()->after('subj');
            $table->boolean('email_status')->default(1)->after('email_body');
            $table->longText('sms_body')->nullable()->after('email_status');
            $table->boolean('sms_status')->default(1)->after('sms_body');
            $table->json('shortcodes')->nullable()->after('sms_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->dropColumn(['act', 'name', 'subj', 'email_body', 'email_status', 'sms_body', 'sms_status', 'shortcodes']);
        });
    }
};
