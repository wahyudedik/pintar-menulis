<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['trial', 'active', 'expired', 'cancelled', 'pending_payment'])->default('trial');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->timestamp('trial_starts_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->boolean('trial_used')->default(false);
            $table->integer('ai_quota_used')->default(0);
            $table->integer('ai_quota_limit')->default(0);
            $table->timestamp('quota_reset_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // Add subscription fields to packages
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('ai_quota_monthly')->default(50)->after('price');
            $table->boolean('has_trial')->default(true)->after('ai_quota_monthly');
            $table->integer('trial_days')->default(30)->after('has_trial');
            $table->boolean('is_featured')->default(false)->after('trial_days');
            $table->string('badge_text')->nullable()->after('is_featured');
            $table->string('badge_color')->default('blue')->after('badge_text');
            $table->json('features')->nullable()->after('badge_color');
            $table->integer('sort_order')->default(0)->after('features');
            $table->integer('yearly_price')->nullable()->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'ai_quota_monthly', 'has_trial', 'trial_days', 'is_featured',
                'badge_text', 'badge_color', 'features', 'sort_order', 'yearly_price'
            ]);
        });
    }
};
