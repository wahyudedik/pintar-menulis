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
        Schema::table('caption_histories', function (Blueprint $table) {
            $table->tinyInteger('rating')->nullable()->after('last_generated_at')->comment('User rating 1-5 stars');
            $table->text('feedback')->nullable()->after('rating')->comment('User feedback text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('caption_histories', function (Blueprint $table) {
            $table->dropColumn(['rating', 'feedback']);
        });
    }
};
