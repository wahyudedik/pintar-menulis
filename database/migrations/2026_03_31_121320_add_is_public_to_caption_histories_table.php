<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('caption_histories', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->after('feedback');
            $table->unsignedInteger('likes_count')->default(0)->after('is_public');
            $table->index(['is_public', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::table('caption_histories', function (Blueprint $table) {
            $table->dropIndex(['is_public', 'rating']);
            $table->dropColumn(['is_public', 'likes_count']);
        });
    }
};
