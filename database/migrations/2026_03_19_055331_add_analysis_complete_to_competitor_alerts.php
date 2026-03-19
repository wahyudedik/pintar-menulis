<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE competitor_alerts MODIFY COLUMN alert_type ENUM('new_post','promo_detected','viral_content','pattern_change','engagement_spike','analysis_complete') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE competitor_alerts MODIFY COLUMN alert_type ENUM('new_post','promo_detected','viral_content','pattern_change','engagement_spike') NOT NULL");
    }
};
