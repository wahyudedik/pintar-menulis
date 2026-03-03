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
        // Create ML training data table
        Schema::create('ml_training_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('copywriting_request_id')->nullable()->constrained()->onDelete('set null');
            $table->text('input_prompt');
            $table->text('ai_output');
            $table->text('corrected_output');
            $table->text('feedback_notes')->nullable();
            $table->enum('quality_rating', ['poor', 'fair', 'good', 'excellent']);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Create ML model versions table
        Schema::create('ml_model_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version_name');
            $table->text('description')->nullable();
            $table->integer('training_count')->default(0);
            $table->decimal('accuracy_score', 5, 2)->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamp('trained_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ml_training_data');
        Schema::dropIfExists('ml_model_versions');
    }
};
