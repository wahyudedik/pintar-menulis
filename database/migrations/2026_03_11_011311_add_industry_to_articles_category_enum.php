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
        // Alter ENUM to add 'industry' value
        DB::statement("ALTER TABLE articles MODIFY COLUMN category ENUM('caption', 'quote', 'tips', 'industry') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM values
        DB::statement("ALTER TABLE articles MODIFY COLUMN category ENUM('caption', 'quote', 'tips') NOT NULL");
    }
};
