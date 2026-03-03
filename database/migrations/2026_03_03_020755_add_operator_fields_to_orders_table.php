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
        Schema::table('orders', function (Blueprint $table) {
            $table->text('result')->nullable()->after('deadline');
            $table->text('operator_notes')->nullable()->after('result');
            $table->timestamp('completed_at')->nullable()->after('operator_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['result', 'operator_notes', 'completed_at']);
        });
    }
};
