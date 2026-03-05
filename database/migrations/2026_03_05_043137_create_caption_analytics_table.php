<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caption_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            
            // Caption Details
            $table->text('caption_text');
            $table->string('category')->nullable(); // website_landing, ads, social_media, etc
            $table->string('subcategory')->nullable(); // instagram_caption, hook_opening, etc
            $table->string('platform')->nullable(); // instagram, facebook, tiktok, etc
            $table->string('tone')->nullable(); // casual, formal, persuasive, etc
            
            // Performance Metrics
            $table->integer('likes')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('shares')->default(0);
            $table->integer('saves')->default(0);
            $table->integer('reach')->default(0);
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->decimal('engagement_rate', 5, 2)->default(0); // percentage
            
            // User Feedback
            $table->integer('user_rating')->nullable(); // 1-5 stars
            $table->text('user_notes')->nullable();
            $table->boolean('marked_as_successful')->default(false);
            
            // AI Learning
            $table->boolean('used_for_training')->default(false);
            $table->timestamp('posted_at')->nullable();
            $table->timestamp('metrics_updated_at')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('category');
            $table->index('platform');
            $table->index('marked_as_successful');
            $table->index('engagement_rate');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caption_analytics');
    }
};
