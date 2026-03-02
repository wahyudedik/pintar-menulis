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
        Schema::create('copywriting_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['caption', 'product_description', 'email', 'cta', 'headline']);
            $table->string('platform')->nullable();
            $table->text('brief');
            $table->json('product_images')->nullable();
            $table->string('tone')->default('casual');
            $table->text('keywords')->nullable();
            $table->text('ai_generated_content')->nullable();
            $table->text('final_content')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'review', 'revision', 'completed', 'cancelled'])->default('pending');
            $table->integer('revision_count')->default(0);
            $table->text('revision_notes')->nullable();
            $table->decimal('rating', 2, 1)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('copywriting_requests');
    }
};
