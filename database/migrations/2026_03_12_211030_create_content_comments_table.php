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
        Schema::create('content_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('project_content')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->enum('type', ['comment', 'suggestion', 'approval', 'rejection'])->default('comment');
            $table->json('highlighted_text')->nullable(); // For text selection comments
            $table->foreignId('parent_id')->nullable()->constrained('content_comments')->onDelete('cascade');
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();

            $table->index(['content_id', 'created_at']);
            $table->index(['content_id', 'type']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_comments');
    }
};