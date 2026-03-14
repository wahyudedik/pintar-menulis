<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('category', 100);
            $table->string('platform', 50)->default('universal');
            $table->string('tone', 50)->default('universal');
            $table->text('template_content');
            $table->text('format_instructions')->nullable();
            $table->json('tags')->nullable();
            
            // Visibility & Status
            $table->boolean('is_public')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->text('rejection_reason')->nullable();
            
            // Marketplace
            $table->boolean('is_premium')->default(false);
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('license_type', ['free', 'personal', 'commercial', 'extended'])->default('free');
            
            // Analytics
            $table->integer('usage_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('total_ratings')->default(0);
            $table->integer('total_sales')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'status']);
            $table->index(['is_public', 'is_approved']);
            $table->index(['category', 'platform']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_templates');
    }
};
