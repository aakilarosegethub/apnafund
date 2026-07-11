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
        if (! Schema::hasTable('header_categories')) {
            Schema::create('header_categories', function (Blueprint $table) {
                $table->id();
                $table->string('label');
                $table->string('slug');
                $table->integer('sort_order')->default(0);
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('header_categories');
    }
};
