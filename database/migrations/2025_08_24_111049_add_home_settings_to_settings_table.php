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
        Schema::table('settings', function (Blueprint $table) {
            // Home Page Settings
            $table->string('home_hero_title_1')->nullable()->after('per_page_item');
            $table->string('home_hero_title_2')->nullable()->after('home_hero_title_1');
            $table->text('home_hero_subtitle')->nullable()->after('home_hero_title_2');
            $table->string('home_business_button_text')->nullable()->after('home_hero_subtitle');
            $table->string('home_personal_button_text')->nullable()->after('home_business_button_text');
            $table->string('home_resource_title')->nullable()->after('home_personal_button_text');
            $table->string('home_resource_subtitle')->nullable()->after('home_resource_title');
            $table->text('home_resource_description')->nullable()->after('home_resource_subtitle');
            $table->string('home_resource_button_text')->nullable()->after('home_resource_description');
            $table->string('home_steps_title')->nullable()->after('home_resource_button_text');
            $table->string('home_step_1_title')->nullable()->after('home_steps_title');
            $table->text('home_step_1_description')->nullable()->after('home_step_1_title');
            $table->string('home_step_2_title')->nullable()->after('home_step_1_description');
            $table->text('home_step_2_description')->nullable()->after('home_step_2_title');
            $table->string('home_step_3_title')->nullable()->after('home_step_2_description');
            $table->text('home_step_3_description')->nullable()->after('home_step_3_title');
            $table->string('home_stories_title')->nullable()->after('home_step_3_description');
            $table->text('home_stories_subtitle')->nullable()->after('home_stories_title');
            $table->string('home_faq_title')->nullable()->after('home_stories_subtitle');
            $table->text('home_faq_subtitle')->nullable()->after('home_faq_title');
            $table->string('home_community_title')->nullable()->after('home_faq_subtitle');
            $table->text('home_community_description')->nullable()->after('home_community_title');
            $table->string('home_community_button_text')->nullable()->after('home_community_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'home_hero_title_1',
                'home_hero_title_2',
                'home_hero_subtitle',
                'home_business_button_text',
                'home_personal_button_text',
                'home_resource_title',
                'home_resource_subtitle',
                'home_resource_description',
                'home_resource_button_text',
                'home_steps_title',
                'home_step_1_title',
                'home_step_1_description',
                'home_step_2_title',
                'home_step_2_description',
                'home_step_3_title',
                'home_step_3_description',
                'home_stories_title',
                'home_stories_subtitle',
                'home_faq_title',
                'home_faq_subtitle',
                'home_community_title',
                'home_community_description',
                'home_community_button_text'
            ]);
        });
    }
};
