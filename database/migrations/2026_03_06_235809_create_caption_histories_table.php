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
        Schema::create('caption_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('caption_text');
            $table->string('category')->nullable();
            $table->string('subcategory')->nullable();
            $table->string('platform')->nullable();
            $table->string('tone')->nullable();
            $table->text('brief_summary')->nullable(); // Summary of the brief used
            $table->string('hash')->index(); // Hash of caption for quick duplicate check
            $table->integer('times_generated')->default(1); // How many times this similar caption was generated
            $table->timestamp('last_generated_at');
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['user_id', 'category', 'platform']);
            $table->index(['user_id', 'last_generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caption_histories');
    }
};
