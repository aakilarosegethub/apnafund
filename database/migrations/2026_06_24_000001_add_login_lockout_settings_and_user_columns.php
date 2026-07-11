<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                if (! Schema::hasColumn('settings', 'login_max_attempts')) {
                    $table->unsignedSmallInteger('login_max_attempts')->default(5);
                }
                if (! Schema::hasColumn('settings', 'login_lock_duration')) {
                    $table->unsignedSmallInteger('login_lock_duration')->default(60);
                }
                if (! Schema::hasColumn('settings', 'login_lock_enabled')) {
                    $table->boolean('login_lock_enabled')->default(true);
                }
                if (! Schema::hasColumn('settings', 'login_lock_email_enabled')) {
                    $table->boolean('login_lock_email_enabled')->default(true);
                }
            });

            DB::table('settings')->orderBy('id')->limit(1)->update([
                'login_max_attempts' => 5,
                'login_lock_duration' => 60,
                'login_lock_enabled' => 1,
                'login_lock_email_enabled' => 1,
            ]);
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'failed_login_attempts')) {
                    if (Schema::hasColumn('users', 'last_login_at')) {
                        $table->unsignedSmallInteger('failed_login_attempts')->default(0)->after('last_login_at');
                    } else {
                        $table->unsignedSmallInteger('failed_login_attempts')->default(0);
                    }
                }
                if (! Schema::hasColumn('users', 'blocked_until')) {
                    if (Schema::hasColumn('users', 'failed_login_attempts')) {
                        $table->timestamp('blocked_until')->nullable()->after('failed_login_attempts');
                    } else {
                        $table->timestamp('blocked_until')->nullable();
                    }
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'blocked_until')) {
                    $table->dropColumn('blocked_until');
                }
                if (Schema::hasColumn('users', 'failed_login_attempts')) {
                    $table->dropColumn('failed_login_attempts');
                }
            });
        }

        if (Schema::hasTable('settings')) {
            Schema::table('settings', function (Blueprint $table) {
                foreach (['login_lock_email_enabled', 'login_lock_enabled', 'login_lock_duration', 'login_max_attempts'] as $col) {
                    if (Schema::hasColumn('settings', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
