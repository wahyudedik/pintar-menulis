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
        Schema::create('whats_app_messages', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->enum('direction', ['incoming', 'outgoing']);
            $table->enum('message_type', ['text', 'image', 'audio', 'video', 'document']);
            $table->text('message_content')->nullable();
            $table->string('media_url')->nullable();
            $table->string('media_path')->nullable();
            $table->json('metadata')->nullable(); // Store additional data like AI response, processing time, etc.
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('pending');
            $table->string('external_id')->nullable(); // WhatsApp message ID
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('caption_history_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['phone_number', 'created_at']);
            $table->index(['direction', 'status']);
            $table->index('processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_app_messages');
    }
};
