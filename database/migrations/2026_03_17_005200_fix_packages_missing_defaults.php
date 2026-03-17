<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('caption_quota')->default(0)->change();
            $table->integer('response_time_hours')->default(24)->change();
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('caption_quota')->nullable()->change();
            $table->integer('response_time_hours')->nullable()->change();
        });
    }
};
