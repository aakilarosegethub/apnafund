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
        $goalColumn = Schema::hasColumn('campaigns', 'goal_amount') ? 'goal_amount' : (Schema::hasColumn('campaigns', 'target_amount') ? 'target_amount' : null);

        Schema::table('campaigns', function (Blueprint $table) use ($goalColumn) {
            if (!Schema::hasColumn('campaigns', 'goal_amount_usd')) {
                $column = $goalColumn ?: 'id';
                $table->decimal('goal_amount_usd', 15, 2)->default(0)->after($column);
            }
            if (!Schema::hasColumn('campaigns', 'original_goal_amount')) {
                $table->decimal('original_goal_amount', 15, 2)->default(0)->after('goal_amount_usd');
            }
            if (!Schema::hasColumn('campaigns', 'original_currency')) {
                $table->string('original_currency', 10)->default('USD')->after('original_goal_amount');
            }
            if (!Schema::hasColumn('campaigns', 'exchange_rate_used')) {
                $table->decimal('exchange_rate_used', 18, 8)->default(1)->after('original_currency');
            }
        });

        if ($goalColumn) {
            DB::table('campaigns')
                ->whereNull('goal_amount_usd')
                ->orWhere('goal_amount_usd', 0)
                ->update([
                    'goal_amount_usd' => DB::raw($goalColumn),
                    'original_goal_amount' => DB::raw($goalColumn),
                    'original_currency' => 'USD',
                    'exchange_rate_used' => 1,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'goal_amount_usd',
                'original_goal_amount',
                'original_currency',
                'exchange_rate_used',
            ]);
        });
    }
};
