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
        Schema::table('caption_analytics', function (Blueprint $table) {
            $table->string('industry')->nullable()->after('platform'); // fashion, food, beauty, etc
            $table->index('industry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('caption_analytics', function (Blueprint $table) {
            $table->dropIndex(['industry']);
            $table->dropColumn('industry');
        });
    }
};
