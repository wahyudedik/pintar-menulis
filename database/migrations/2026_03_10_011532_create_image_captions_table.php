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
        Schema::create('image_captions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->text('detected_objects')->nullable(); // JSON array of detected objects
            $table->text('caption_single')->nullable(); // Single post caption
            $table->text('caption_carousel')->nullable(); // JSON array for carousel (slide 1,2,3)
            $table->text('editing_tips')->nullable(); // JSON array of tips
            $table->string('dominant_colors')->nullable(); // JSON array of colors
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_captions');
    }
};
