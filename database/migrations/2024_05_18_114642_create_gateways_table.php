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
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('alias')->nullable();
            $table->string('image')->nullable();
            $table->text('gateway_parameters')->nullable();
            $table->text('supported_currencies')->nullable();
            $table->text('extra')->nullable();
            $table->text('input_form')->nullable();
            $table->text('guideline')->nullable();
            $table->text('countries')->nullable(); // JSON array of allowed countries
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gateways');
    }
};
