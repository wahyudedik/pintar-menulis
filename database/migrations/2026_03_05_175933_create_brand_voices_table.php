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
        Schema::create('brand_voices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Toko Baju Anak Saya"
            $table->string('industry')->nullable(); // fashion_clothing, food_beverage, etc.
            $table->string('target_market')->nullable(); // ibu_muda, remaja, etc.
            $table->string('tone')->default('casual'); // casual, formal, funny, etc.
            $table->string('platform')->default('instagram'); // instagram, tiktok, etc.
            $table->text('keywords')->nullable(); // comma-separated keywords
            $table->string('local_language')->nullable(); // jawa, sunda, etc.
            $table->text('brand_description')->nullable(); // brief description of brand
            $table->boolean('is_default')->default(false); // auto-apply this voice
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['user_id', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_voices');
    }
};
