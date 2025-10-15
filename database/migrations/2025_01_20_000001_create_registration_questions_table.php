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
        Schema::create('registration_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('step_id')->constrained('registration_steps')->onDelete('cascade');
            $table->string('field_name');
            $table->string('label');
            $table->string('type'); // text, email, select, textarea, tel, password, checkbox
            $table->text('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->boolean('is_required')->default(true);
            $table->integer('order')->default(1);
            $table->json('validation_rules')->nullable();
            $table->json('options')->nullable(); // For select fields
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_questions');
    }
};
