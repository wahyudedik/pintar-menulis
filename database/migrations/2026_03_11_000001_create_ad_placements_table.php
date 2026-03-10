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
        Schema::create('ad_placements', function (Blueprint $table) {
            $table->id();
            $table->enum('location', [
                'article_list_top',
                'article_list_bottom',
                'article_detail_top',
                'article_detail_middle',
                'article_detail_bottom',
            ])->unique();
            $table->longText('ad_code')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            
            $table->index('location');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_placements');
    }
};
