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
        if (! Schema::hasTable('settings') || Schema::hasColumn('settings', 'sms_body')) {
            return;
        }

        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'email_template')) {
                $table->text('sms_body')->nullable()->after('email_template');
            } else {
                $table->text('sms_body')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'sms_body')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('sms_body');
            });
        }
    }
};
