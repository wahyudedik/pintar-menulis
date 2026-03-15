<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Track whether each training entry has been counted toward earnings
        Schema::table('ml_training_data', function (Blueprint $table) {
            $table->boolean('earnings_paid')->default(false)->after('metadata');
        });

        // Guru earnings balance (same pattern as operator_profiles.total_earnings)
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('guru_total_earnings', 12, 2)->default(0)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('ml_training_data', function (Blueprint $table) {
            $table->dropColumn('earnings_paid');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('guru_total_earnings');
        });
    }
};
