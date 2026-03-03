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
        Schema::create('order_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->integer('revision_number')->default(1);
            $table->text('result'); // Hasil pekerjaan untuk revisi ini
            $table->text('operator_notes')->nullable(); // Catatan operator
            $table->text('revision_request')->nullable(); // Request revisi dari client (untuk revisi berikutnya)
            $table->timestamp('submitted_at'); // Kapan operator submit
            $table->timestamp('revision_requested_at')->nullable(); // Kapan client request revisi
            $table->timestamps();
            
            $table->index(['order_id', 'revision_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_revisions');
    }
};
