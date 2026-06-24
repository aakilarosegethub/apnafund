<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('webhook_logs')) {
            return;
        }

        Schema::create('webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('webhook_type', 100);
            $table->text('url')->nullable();
            $table->string('method', 20)->default('POST');
            $table->json('headers')->nullable();
            $table->json('payload')->nullable();
            $table->unsignedInteger('response_status')->nullable();
            $table->longText('response_body')->nullable();
            $table->json('response_headers')->nullable();
            $table->decimal('execution_time', 10, 3)->nullable();
            $table->string('status', 50)->default('pending');
            $table->text('error_message')->nullable();
            $table->unsignedInteger('retry_count')->default(0);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->timestamps();

            $table->index('webhook_type');
            $table->index('status');
            $table->index('created_at');
            $table->index('user_id');
            $table->index('campaign_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_logs');
    }
};
