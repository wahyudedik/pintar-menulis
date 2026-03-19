<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Path to uploaded brief file (PDF, DOC, DOCX, images, etc.)
            $table->string('brief_file')->nullable()->after('brief');
            $table->string('brief_file_original_name')->nullable()->after('brief_file');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['brief_file', 'brief_file_original_name']);
        });
    }
};
