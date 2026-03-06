<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add escrow columns to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_status', ['pending_payment', 'paid', 'held', 'released', 'refunded'])
                ->default('pending_payment')
                ->after('status');
            $table->timestamp('approved_at')->nullable()->after('completed_at');
            $table->text('dispute_reason')->nullable()->after('revision_notes');
        });

        // Add escrow columns to payments table
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('escrow_status', ['held', 'released', 'refunded'])
                ->nullable()
                ->after('status');
            $table->timestamp('released_at')->nullable()->after('verified_at');
            $table->timestamp('refunded_at')->nullable()->after('released_at');
            $table->text('refund_reason')->nullable()->after('refunded_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'approved_at', 'dispute_reason']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['escrow_status', 'released_at', 'refunded_at', 'refund_reason']);
        });
    }
};
