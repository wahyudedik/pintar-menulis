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
        Schema::create('content_calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->enum('duration', ['7_days', '30_days']);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('category')->nullable();
            $table->string('platform')->nullable();
            $table->string('tone')->nullable();
            $table->text('brief')->nullable();
            $table->json('content_items'); // Array of generated content
            $table->enum('status', ['draft', 'active', 'completed'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_calendars');
    }
};
