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
        Schema::create('hashtag_blacklist', function (Blueprint $table) {
            $table->id();
            $table->string('hashtag', 100)->unique();
            $table->enum('reason', ['spam', 'inappropriate', 'hate_speech', 'violence', 'drugs', 'gambling', 'scam', 'other'])->default('other');
            $table->text('notes')->nullable();
            $table->foreignId('added_by')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('hashtag');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hashtag_blacklist');
    }
};
