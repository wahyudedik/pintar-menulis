<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keyword Research Data
        Schema::create('keyword_research', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('keyword');
            $table->bigInteger('search_volume')->nullable();
            $table->string('competition')->nullable(); // LOW, MEDIUM, HIGH
            $table->decimal('cpc_low', 10, 2)->nullable();
            $table->decimal('cpc_high', 10, 2)->nullable();
            $table->string('trend_direction', 20)->nullable(); // UP, DOWN, STABLE
            $table->integer('trend_percentage')->nullable();
            $table->json('related_keywords')->nullable();
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();
            
            $table->index('keyword');
            $table->index(['user_id', 'keyword']);
            $table->index('search_volume');
        });

        // Caption Keywords (link keywords to captions)
        Schema::create('caption_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caption_history_id')->constrained('caption_histories')->onDelete('cascade');
            $table->string('keyword');
            $table->bigInteger('search_volume')->nullable();
            $table->string('competition')->nullable();
            $table->decimal('cpc_low', 10, 2)->nullable();
            $table->decimal('cpc_high', 10, 2)->nullable();
            $table->decimal('relevance_score', 3, 2)->nullable(); // 0-1
            $table->timestamps();
            
            $table->index('keyword');
            $table->index('caption_history_id');
        });

        // Trending Hashtags
        Schema::create('trending_hashtags', function (Blueprint $table) {
            $table->id();
            $table->string('hashtag', 100);
            $table->string('platform', 50); // instagram, tiktok, facebook, etc
            $table->integer('trend_score')->default(0);
            $table->bigInteger('usage_count')->default(0);
            $table->decimal('engagement_rate', 5, 2)->nullable();
            $table->string('category', 100)->nullable();
            $table->string('country', 10)->default('ID'); // Indonesia
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();
            
            $table->index(['platform', 'trend_score']);
            $table->index('category');
            $table->index(['country', 'platform']);
        });

        // Keyword Suggestions Cache
        Schema::create('keyword_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('seed_keyword');
            $table->string('language', 10)->default('id'); // Indonesian
            $table->string('location', 10)->default('ID'); // Indonesia
            $table->json('suggestions'); // Array of keyword suggestions
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index('seed_keyword');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keyword_suggestions');
        Schema::dropIfExists('trending_hashtags');
        Schema::dropIfExists('caption_keywords');
        Schema::dropIfExists('keyword_research');
    }
};
