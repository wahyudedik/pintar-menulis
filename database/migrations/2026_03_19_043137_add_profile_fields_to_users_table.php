<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('avatar');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('bio');
            }
            if (!Schema::hasColumn('users', 'location')) {
                $table->string('location', 100)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'website')) {
                $table->string('website', 255)->nullable()->after('location');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bio', 'phone', 'location', 'website']);
        });
    }
};
