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
        Schema::table('gift_items', function (Blueprint $table) {
            $table->foreignId('gift_category_id')
                ->after('id')
                ->constrained('gift_categories')
                ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gift_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('gift_category_id');
        });
    }
};
