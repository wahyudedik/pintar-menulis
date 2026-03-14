<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('template_favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->constrained('user_templates')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['user_id', 'template_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('template_favorites');
    }
};
