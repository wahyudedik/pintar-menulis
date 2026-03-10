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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->longText('content');
            $table->enum('category', ['caption', 'quote', 'tips']);
            $table->string('industry')->default('general');
            $table->integer('day_number')->default(1);
            $table->timestamps();
            
            // Unique constraint on slug and day_number
            $table->unique(['slug', 'day_number']);
            
            // Indexes for better query performance
            $table->index('category');
            $table->index('industry');
            $table->index('day_number');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
