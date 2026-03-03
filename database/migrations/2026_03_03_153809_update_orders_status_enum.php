<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update status enum to support marketplace workflow
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('pending', 'accepted', 'in_progress', 'completed', 'rejected', 'cancelled', 'active', 'expired') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE `orders` MODIFY COLUMN `status` ENUM('active', 'expired', 'cancelled') DEFAULT 'active'");
    }
};
