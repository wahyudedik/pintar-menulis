<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            // gateway_type: manual_transfer | midtrans | xendit
            $table->string('gateway_type')->default('manual_transfer')->after('id');
            // JSON config for gateway credentials
            $table->json('gateway_config')->nullable()->after('qr_code_path');
            // Whether this gateway is currently enabled
            $table->boolean('is_enabled')->default(false)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->dropColumn(['gateway_type', 'gateway_config', 'is_enabled']);
        });
    }
};
