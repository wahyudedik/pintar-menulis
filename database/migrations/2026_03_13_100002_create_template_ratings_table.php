<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('template_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('user_templates')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->comment('1-5 stars');
            $table->text('review')->nullable();
            $table->integer('helpful_count')->default(0);
            $table->timestamps();
            
            $table->unique(['template_id', 'user_id']);
            $table->index('rating');
        });
    }

    public function down()
    {
        Schema::dropIfExists('template_ratings');
    }
};
