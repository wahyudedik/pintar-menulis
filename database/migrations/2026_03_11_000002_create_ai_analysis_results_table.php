<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_analysis_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('analysis_type'); // sentiment, image, quality, campaign, recommendations, article
            $table->text('input_data'); // Original caption/image/article
            $table->longText('analysis_result'); // JSON result from Gemini
            $table->decimal('score', 5, 2)->nullable(); // Overall score if applicable
            $table->string('status')->default('completed'); // completed, failed, pending
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('analysis_type');
            $table->index('created_at');
        });

        Schema::create('caption_quality_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('caption');
            $table->string('platform')->default('instagram'); // instagram, tiktok, facebook, etc
            $table->string('industry')->nullable();
            $table->decimal('overall_score', 5, 2);
            $table->decimal('engagement_score', 5, 2);
            $table->decimal('clarity_score', 5, 2);
            $table->decimal('cta_score', 5, 2);
            $table->decimal('emoji_score', 5, 2);
            $table->longText('analysis_data'); // Full JSON analysis
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('platform');
            $table->index('overall_score');
        });

        Schema::create('sentiment_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->text('caption');
            $table->enum('sentiment', ['positive', 'neutral', 'negative']);
            $table->decimal('sentiment_score', 5, 2);
            $table->json('keywords')->nullable();
            $table->text('explanation')->nullable();
            $table->longText('full_analysis')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('sentiment');
            $table->index('created_at');
        });

        Schema::create('image_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->text('image_description');
            $table->json('suggested_captions')->nullable();
            $table->json('hashtags')->nullable();
            $table->string('best_time_to_post')->nullable();
            $table->string('content_type')->nullable();
            $table->longText('full_analysis')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('created_at');
        });

        Schema::create('campaign_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('campaign_name')->nullable();
            $table->integer('total_captions');
            $table->decimal('average_rating', 5, 2);
            $table->json('top_patterns')->nullable();
            $table->json('weak_areas')->nullable();
            $table->json('recommendations')->nullable();
            $table->json('trending_elements')->nullable();
            $table->longText('full_analysis')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('created_at');
        });

        Schema::create('article_quality_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->nullable()->constrained('articles')->onDelete('cascade');
            $table->decimal('seo_score', 5, 2);
            $table->decimal('readability_score', 5, 2);
            $table->decimal('engagement_score', 5, 2);
            $table->string('keyword_optimization');
            $table->json('strengths')->nullable();
            $table->json('improvements')->nullable();
            $table->text('suggested_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('internal_links')->nullable();
            $table->json('external_links')->nullable();
            $table->string('overall_quality');
            $table->longText('full_analysis')->nullable();
            $table->timestamps();
            
            $table->index('article_id');
            $table->index('seo_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_analysis_results');
        Schema::dropIfExists('caption_quality_scores');
        Schema::dropIfExists('sentiment_analyses');
        Schema::dropIfExists('image_analyses');
        Schema::dropIfExists('campaign_analytics');
        Schema::dropIfExists('article_quality_analyses');
    }
};
