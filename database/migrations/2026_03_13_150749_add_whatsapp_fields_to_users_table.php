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
        Schema::table('users', function (Blueprint $table) {
            $table->string('whatsapp_number')->nullable()->after('email');
            $table->boolean('whatsapp_verified')->default(false)->after('whatsapp_number');
            $table->timestamp('whatsapp_verified_at')->nullable()->after('whatsapp_verified');
            $table->string('whatsapp_verification_code')->nullable()->after('whatsapp_verified_at');
            $table->json('whatsapp_preferences')->nullable()->after('whatsapp_verification_code');
            $table->boolean('whatsapp_notifications_enabled')->default(true)->after('whatsapp_preferences');
            $table->timestamp('last_whatsapp_interaction')->nullable()->after('whatsapp_notifications_enabled');
            
            $table->index('whatsapp_number');
            $table->index(['whatsapp_verified', 'whatsapp_notifications_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['users_whatsapp_number_index']);
            $table->dropIndex(['users_whatsapp_verified_whatsapp_notifications_enabled_index']);
            
            $table->dropColumn([
                'whatsapp_number',
                'whatsapp_verified',
                'whatsapp_verified_at',
                'whatsapp_verification_code',
                'whatsapp_preferences',
                'whatsapp_notifications_enabled',
                'last_whatsapp_interaction'
            ]);
        });
    }
};
