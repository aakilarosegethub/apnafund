<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            if (!Schema::hasColumn('currencies', 'source')) {
                $table->string('source', 20)->default('default')->after('rate_to_usd');
            }
        });

        DB::table('currencies')
            ->whereNull('source')
            ->update(['source' => 'default']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
