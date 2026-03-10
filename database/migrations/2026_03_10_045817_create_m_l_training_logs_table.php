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
        Schema::create('ml_training_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamp('trained_at');
            $table->integer('duration_seconds')->default(0);
            $table->integer('hashtags_trained')->default(0);
            $table->integer('keywords_trained')->default(0);
            $table->integer('topics_trained')->default(0);
            $table->integer('hooks_trained')->default(0);
            $table->integer('ctas_trained')->default(0);
            $table->integer('total_trained')->default(0);
            $table->json('errors')->nullable();
            $table->string('status')->default('success'); // success, partial, failed
            $table->timestamps();
            
            // Index
            $table->index('trained_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ml_training_logs');
    }
};
