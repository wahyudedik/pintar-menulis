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
        Schema::table('ml_data_cache', function (Blueprint $table) {
            // Change platform from enum to string to support e-commerce platforms
            $table->string('platform', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ml_data_cache', function (Blueprint $table) {
            // Revert back to enum (only if needed)
            $table->enum('platform', ['instagram', 'tiktok', 'facebook', 'youtube', 'twitter', 'x', 'linkedin'])->change();
        });
    }
};