<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds a persistent record of when a user accepted the Terms of Use.
     *
     * This is the source of truth for the first-login "Accept Terms" gate used
     * for social (Google/Facebook/LinkedIn) sign-ups. A session flag alone is
     * not enough because a user could simply navigate straight to the dashboard
     * or close the browser before accepting.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'terms_accepted_at')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('terms_accepted_at')->nullable()->after('tc');
        });

        // Backfill: treat every EXISTING account as already-accepted so current
        // users are never forced through the new gate. Only accounts created
        // AFTER this migration (i.e. brand-new social signups) stay NULL and are
        // therefore prompted to accept. Existing data is preserved.
        DB::table('users')
            ->whereNull('terms_accepted_at')
            ->update(['terms_accepted_at' => DB::raw('COALESCE(created_at, NOW())')]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'terms_accepted_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('terms_accepted_at');
            });
        }
    }
};
