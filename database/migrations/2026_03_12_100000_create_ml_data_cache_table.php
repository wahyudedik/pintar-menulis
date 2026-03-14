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
        Schema::create('ml_data_cache', function (Blueprint $table) {
            $table->id();
            $table->string('username')->index();
            $table->enum('platform', ['instagram', 'tiktok', 'facebook', 'youtube', 'twitter', 'x', 'linkedin'])->index();
            $table->json('profile_data'); // Cached profile data from API
            $table->timestamp('last_api_call')->nullable(); // When we last called the API
            $table->integer('api_calls_count')->default(0); // Total API calls made
            $table->integer('api_calls_saved')->default(0); // API calls saved by using cache
            $table->float('data_quality_score')->default(0); // Quality score of cached data (0-100)
            $table->json('ml_insights')->nullable(); // AI-generated insights about the profile
            $table->integer('cache_hit_count')->default(0); // How many times cache was used
            $table->timestamp('last_cache_hit')->nullable(); // Last time cache was accessed
            $table->timestamps();

            // Composite index for fast lookups
            $table->unique(['username', 'platform']);
            
            // Index for cleanup queries
            $table->index(['updated_at', 'data_quality_score']);
            
            // Index for platform statistics
            $table->index(['platform', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ml_data_cache');
    }
};