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
        Schema::create('ml_optimized_data', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // hashtag, keyword, topic, hook, cta
            $table->string('industry'); // fashion, food, beauty, etc
            $table->string('platform')->default('instagram'); // instagram, facebook, tiktok
            $table->text('data'); // actual data (hashtag, keyword, etc)
            $table->decimal('performance_score', 8, 2)->default(0); // 0-100
            $table->integer('usage_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_trained_at')->nullable();
            $table->json('metadata')->nullable(); // additional data
            $table->timestamps();
            
            // Indexes
            $table->index(['type', 'industry', 'platform']);
            $table->index('performance_score');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ml_optimized_data');
    }
};
