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
        Schema::create('data_logs', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint')->nullable();
            $table->string('method')->nullable(); // GET, POST, PUT, etc.
            $table->json('request_data')->nullable(); // All request data
            $table->json('headers')->nullable(); // Request headers
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->text('raw_input')->nullable(); // Raw input data
            $table->string('transaction_id')->nullable(); // If it's a payment callback
            $table->string('status')->default('received'); // received, processed, failed
            $table->text('response')->nullable(); // Response sent back
            $table->timestamps();
            
            $table->index(['endpoint', 'created_at']);
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_logs');
    }
};
