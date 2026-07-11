<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            // Drop the existing unique constraint on slug (try different possible names)
            try {
                $table->dropUnique(['slug']);
            } catch (\Exception $e) {
                // Try dropping with explicit constraint name
                try {
                    DB::statement('ALTER TABLE `sub_categories` DROP INDEX `sub_categories_slug_unique`');
                } catch (\Exception $e2) {
                    // Constraint might not exist or have different name, continue
                }
            }

            // Add composite unique constraint on (category_id, slug)
            $table->unique(['category_id', 'slug'], 'sub_categories_category_slug_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_categories', function (Blueprint $table) {
            // Drop the composite unique constraint
            $table->dropUnique('sub_categories_category_slug_unique');

            // Restore the original unique constraint on slug
            $table->unique('slug', 'sub_categories_slug_unique');
        });
    }
};
