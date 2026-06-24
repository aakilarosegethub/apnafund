<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('campaigns') || Schema::hasColumn('campaigns', 'verification_documents')) {
            return;
        }

        Schema::table('campaigns', function (Blueprint $table) {
            $table->json('verification_documents')->nullable();
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('campaigns') && Schema::hasColumn('campaigns', 'verification_documents')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropColumn('verification_documents');
            });
        }
    }
};
