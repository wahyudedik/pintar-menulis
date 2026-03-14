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
        Schema::create('project_content', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('platform')->nullable();
            $table->string('content_type')->default('caption'); // caption, blog, email, etc
            $table->enum('status', ['draft', 'review', 'approved', 'rejected', 'published'])->default('draft');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->json('metadata')->nullable(); // hashtags, target_audience, etc
            $table->integer('version')->default(1);
            $table->boolean('is_current_version')->default(true);
            $table->timestamps();

            $table->index(['project_id', 'status']);
            $table->index(['created_by', 'status']);
            $table->index(['project_id', 'content_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_content');
    }
};