<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add referral fields to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 10)->nullable()->unique()->after('role');
            $table->foreignId('referred_by_id')->nullable()->constrained('users')->nullOnDelete()->after('referral_code');
            $table->decimal('referral_earnings', 12, 2)->default(0)->after('referred_by_id');
        });

        // Referrals tracking table
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('referred_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('user_subscriptions')->nullOnDelete();
            $table->enum('status', ['pending', 'converted', 'paid'])->default('pending');
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();

            $table->unique('referred_user_id'); // one referral per user
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by_id']);
            $table->dropColumn(['referral_code', 'referred_by_id', 'referral_earnings']);
        });
    }
};
