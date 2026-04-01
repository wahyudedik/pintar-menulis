<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('onboarding_completed')->default(false)->after('referral_earnings');
            $table->string('business_type', 100)->nullable()->after('onboarding_completed');
            $table->string('business_name', 150)->nullable()->after('business_type');
            $table->string('primary_platform', 50)->nullable()->after('business_name');
            $table->string('content_goal', 50)->nullable()->after('primary_platform');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['onboarding_completed', 'business_type', 'business_name', 'primary_platform', 'content_goal']);
        });
    }
};
