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
        if (!Schema::hasColumn('transactions', 'reward_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->unsignedBigInteger('reward_id')->nullable()->after('remark');
            });
        }

        if (!Schema::hasColumn('transactions', 'reward_fulfilled')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->boolean('reward_fulfilled')->default(false)->after('reward_id');
            });
        }

        if (!Schema::hasColumn('transactions', 'reward_fulfilled_at')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->timestamp('reward_fulfilled_at')->nullable()->after('reward_fulfilled');
            });
        }

        if (!Schema::hasColumn('transactions', 'reward_fulfillment_note')) {
        Schema::table('transactions', function (Blueprint $table) {
                $table->text('reward_fulfillment_note')->nullable()->after('reward_fulfilled_at');
            });
        }

        // Add foreign key if reward_id column exists and foreign key doesn't exist
        if (Schema::hasColumn('transactions', 'reward_id')) {
            try {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->foreign('reward_id')->references('id')->on('rewards')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['reward_id']);
            $table->dropColumn(['reward_id', 'reward_fulfilled', 'reward_fulfilled_at', 'reward_fulfillment_note']);
        });
    }
};
