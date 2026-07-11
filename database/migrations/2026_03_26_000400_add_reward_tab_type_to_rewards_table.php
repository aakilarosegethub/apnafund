<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            if (! Schema::hasColumn('rewards', 'reward_tab_type')) {
                $table->string('reward_tab_type', 20)->default('items')->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            if (Schema::hasColumn('rewards', 'reward_tab_type')) {
                $table->dropColumn('reward_tab_type');
            }
        });
    }
};
