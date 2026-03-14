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
        Schema::create('whats_app_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Subscription preferences
            $table->boolean('daily_content')->default(true);
            $table->boolean('weekly_reminder')->default(true);
            $table->boolean('trending_notifications')->default(false);
            $table->boolean('promotional_messages')->default(false);
            
            // Content preferences
            $table->string('business_type')->nullable(); // fashion, food, beauty, etc.
            $table->string('target_audience')->default('Gen Z Indonesia');
            $table->json('content_types')->nullable(); // ['caption', 'video', 'story']
            $table->json('platforms')->nullable(); // ['instagram', 'tiktok', 'facebook']
            
            // Timing preferences
            $table->time('preferred_time')->default('08:00');
            $table->string('timezone')->default('Asia/Jakarta');
            $table->json('preferred_days')->nullable(); // [1,2,3,4,5] for weekdays
            
            // Language and tone
            $table->string('language')->default('bahasa_indonesia');
            $table->string('tone_preference')->default('engaging');
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('last_interaction_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['phone_number', 'is_active']);
            $table->index('business_type');
            $table->index('last_interaction_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_app_subscriptions');
    }
};
