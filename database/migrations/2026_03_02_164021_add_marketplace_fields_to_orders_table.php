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
            $table->foreignId('operator_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->string('category')->nullable()->after('project_id');
            $table->text('brief')->nullable()->after('category');
            $table->integer('budget')->nullable()->after('brief');
            $table->date('deadline')->nullable()->after('budget');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['operator_id']);
            $table->dropColumn(['operator_id', 'category', 'brief', 'budget', 'deadline']);
        });
    }
};
