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
        Schema::create('content_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('project_content')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->integer('version_number');
            $table->text('content');
            $table->json('metadata')->nullable();
            $table->text('change_notes')->nullable();
            $table->enum('change_type', ['created', 'edited', 'approved', 'rejected', 'restored'])->default('edited');
            $table->timestamps();

            $table->index(['content_id', 'version_number']);
            $table->index(['content_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_versions');
    }
};