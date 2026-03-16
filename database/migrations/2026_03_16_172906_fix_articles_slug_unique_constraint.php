<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Drop the composite unique (slug + day_number) — same slug on different
            // day cycles would break ArticleController::show() which queries by slug alone
            $table->dropUnique(['slug', 'day_number']);

            // Slug must be globally unique so URLs always resolve to the right article
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->unique(['slug', 'day_number']);
        });
    }
};
