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
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('minimum_amount', 15, 2);
            $table->integer('quantity')->nullable(); // null means unlimited
            $table->integer('claimed_count')->default(0);
            $table->string('image')->nullable();
            $table->enum('type', ['digital', 'physical'])->default('physical');
            $table->string('color_theme')->default('primary');
            $table->text('terms_conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
