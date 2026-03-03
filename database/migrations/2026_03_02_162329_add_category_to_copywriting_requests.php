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
        Schema::table('copywriting_requests', function (Blueprint $table) {
            $table->string('category')->default('social_media')->after('type');
            $table->string('subcategory')->nullable()->after('category');
            $table->decimal('price', 10, 2)->nullable()->after('feedback');
            $table->enum('urgency', ['normal', 'urgent', 'super_urgent'])->default('normal')->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('copywriting_requests', function (Blueprint $table) {
            $table->dropColumn(['category', 'subcategory', 'price', 'urgency']);
        });
    }
};
