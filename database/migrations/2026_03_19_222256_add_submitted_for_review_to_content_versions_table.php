<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_versions', function (Blueprint $table) {
            $table->dropColumn('change_type');
        });

        Schema::table('content_versions', function (Blueprint $table) {
            $table->enum('change_type', [
                'created',
                'edited',
                'approved',
                'rejected',
                'restored',
                'submitted_for_review',
            ])->default('edited')->after('change_notes');
        });
    }

    public function down(): void
    {
        Schema::table('content_versions', function (Blueprint $table) {
            $table->dropColumn('change_type');
        });

        Schema::table('content_versions', function (Blueprint $table) {
            $table->enum('change_type', ['created', 'edited', 'approved', 'rejected', 'restored'])
                ->default('edited')->after('change_notes');
        });
    }
};
