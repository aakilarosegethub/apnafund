<?php

/**
 * Adds `goal_reached_notified_at` to `campaigns` for idempotent “goal reached” notification handling.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('campaigns')) {
            return;
        }
        if (! Schema::hasColumn('campaigns', 'goal_reached_notified_at')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->timestamp('goal_reached_notified_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('campaigns') && Schema::hasColumn('campaigns', 'goal_reached_notified_at')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropColumn('goal_reached_notified_at');
            });
        }
    }
};
