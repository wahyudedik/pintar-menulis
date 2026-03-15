<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            // 'signup' = komisi saat register, 'subscription' = komisi saat pertama berlangganan
            $table->enum('type', ['signup', 'subscription'])->default('signup')->after('status');
            // Guard: pastikan komisi subscription hanya diberikan sekali
            $table->boolean('subscription_commission_paid')->default(false)->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn(['type', 'subscription_commission_paid']);
        });
    }
};
