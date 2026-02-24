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
        Schema::create('creator_campaign_payout_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_campaign_payout_id')
                ->constrained('creator_campaign_payouts')
                ->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('action_type', 50);
            $table->decimal('amount', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creator_campaign_payout_actions');
    }
};
