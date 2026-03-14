<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('template_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('template_id')->constrained('user_templates')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->decimal('price_paid', 10, 2);
            $table->string('license_type', 50);
            $table->string('transaction_id', 100)->unique();
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('purchased_at');
            $table->timestamps();
            
            $table->index(['buyer_id', 'purchased_at']);
            $table->index('transaction_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('template_purchases');
    }
};
