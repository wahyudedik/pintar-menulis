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
        // Table untuk menyimpan kompetitor yang dimonitor
        Schema::create('competitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('username'); // Username kompetitor (IG/TikTok)
            $table->enum('platform', ['instagram', 'tiktok', 'facebook', 'youtube', 'twitter', 'x', 'linkedin']);
            $table->string('profile_name')->nullable();
            $table->string('profile_picture')->nullable();
            $table->text('bio')->nullable();
            $table->integer('followers_count')->default(0);
            $table->integer('following_count')->default(0);
            $table->integer('posts_count')->default(0);
            $table->string('category')->nullable(); // fashion, food, beauty, etc.
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_analyzed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'username', 'platform']);
        });

        // Table untuk menyimpan post kompetitor
        Schema::create('competitor_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained()->onDelete('cascade');
            $table->string('post_id')->unique(); // ID post dari platform
            $table->text('caption')->nullable();
            $table->string('post_type')->default('image'); // image, video, carousel, reel
            $table->string('post_url')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->decimal('engagement_rate', 5, 2)->default(0);
            $table->json('hashtags')->nullable(); // Array of hashtags
            $table->json('mentions')->nullable(); // Array of mentions
            $table->timestamp('posted_at');
            $table->timestamps();
            
            $table->index(['competitor_id', 'posted_at']);
            $table->index('engagement_rate');
        });

        // Table untuk menyimpan analisis pattern
        Schema::create('competitor_patterns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained()->onDelete('cascade');
            $table->string('pattern_type'); // posting_time, frequency, tone, content_type
            $table->json('pattern_data'); // Data pattern dalam JSON
            $table->text('insights')->nullable(); // AI-generated insights
            $table->date('analysis_date');
            $table->timestamps();
            
            $table->index(['competitor_id', 'pattern_type', 'analysis_date'], 'comp_patterns_comp_type_date_idx');
        });

        // Table untuk menyimpan top performing content
        Schema::create('competitor_top_content', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('competitor_post_id')->constrained('competitor_posts')->onDelete('cascade');
            $table->string('metric_type'); // engagement, likes, comments, shares, views
            $table->integer('metric_value');
            $table->integer('rank'); // 1-10
            $table->text('success_factors')->nullable(); // AI analysis why it performed well
            $table->date('analysis_date');
            $table->timestamps();
            
            $table->index(['competitor_id', 'metric_type', 'rank'], 'comp_top_content_comp_metric_rank_idx');
        });

        // Table untuk menyimpan content gap suggestions
        Schema::create('competitor_content_gaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained()->onDelete('cascade');
            $table->string('gap_type'); // topic, format, timing, tone
            $table->string('gap_title');
            $table->text('gap_description');
            $table->text('opportunity')->nullable(); // Why this is an opportunity
            $table->json('suggested_content')->nullable(); // AI-generated content ideas
            $table->integer('priority')->default(5); // 1-10
            $table->boolean('is_implemented')->default(false);
            $table->date('identified_date');
            $table->timestamps();
            
            $table->index(['competitor_id', 'priority', 'is_implemented'], 'comp_gaps_comp_priority_impl_idx');
        });

        // Table untuk menyimpan alerts
        Schema::create('competitor_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('competitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('competitor_post_id')->nullable()->constrained('competitor_posts')->onDelete('cascade');
            $table->enum('alert_type', ['new_post', 'promo_detected', 'viral_content', 'pattern_change', 'engagement_spike']);
            $table->string('alert_title');
            $table->text('alert_message');
            $table->json('alert_data')->nullable(); // Additional data
            $table->boolean('is_read')->default(false);
            $table->timestamp('triggered_at');
            $table->timestamps();
            
            $table->index(['user_id', 'is_read', 'triggered_at'], 'comp_alerts_user_read_triggered_idx');
        });

        // Table untuk menyimpan analysis summary
        Schema::create('competitor_analysis_summary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competitor_id')->constrained()->onDelete('cascade');
            $table->date('analysis_date');
            $table->integer('total_posts')->default(0);
            $table->decimal('avg_engagement_rate', 5, 2)->default(0);
            $table->integer('avg_likes')->default(0);
            $table->integer('avg_comments')->default(0);
            $table->integer('avg_shares')->default(0);
            $table->json('top_hashtags')->nullable(); // Top 10 hashtags
            $table->json('posting_times')->nullable(); // Best posting times
            $table->string('dominant_tone')->nullable(); // casual, formal, persuasive, etc.
            $table->json('content_types')->nullable(); // Distribution of content types
            $table->text('ai_insights')->nullable(); // AI-generated insights
            $table->timestamps();
            
            $table->unique(['competitor_id', 'analysis_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitor_analysis_summary');
        Schema::dropIfExists('competitor_alerts');
        Schema::dropIfExists('competitor_content_gaps');
        Schema::dropIfExists('competitor_top_content');
        Schema::dropIfExists('competitor_patterns');
        Schema::dropIfExists('competitor_posts');
        Schema::dropIfExists('competitors');
    }
};
