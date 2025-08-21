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
            // Add missing columns that are used throughout the application
            $table->string('site_cur')->default('USD')->after('site_phone');
            $table->string('cur_sym')->default('$')->after('site_cur');
            $table->integer('fraction_digit')->default(2)->after('cur_sym');
            $table->string('first_color')->default('00ffff')->after('fraction_digit');
            $table->string('second_color')->default('ffff00')->after('first_color');
            $table->string('active_theme')->default('primary')->after('second_color');
            $table->boolean('signup')->default(true)->after('active_theme');
            $table->boolean('enforce_ssl')->default(false)->after('signup');
            $table->boolean('agree_policy')->default(false)->after('enforce_ssl');
            $table->boolean('strong_pass')->default(false)->after('agree_policy');
            $table->boolean('kc')->default(false)->after('strong_pass');
            $table->boolean('ec')->default(false)->after('kc');
            $table->boolean('ea')->default(false)->after('ec');
            $table->boolean('sc')->default(false)->after('ea');
            $table->boolean('sa')->default(false)->after('sc');
            $table->boolean('language')->default(false)->after('sa');
            $table->integer('per_page_item')->default(20)->after('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'site_cur',
                'cur_sym', 
                'fraction_digit',
                'first_color',
                'second_color',
                'active_theme',
                'signup',
                'enforce_ssl',
                'agree_policy',
                'strong_pass',
                'kc',
                'ec',
                'ea',
                'sc',
                'sa',
                'language',
                'per_page_item'
            ]);
        });
    }
}; 