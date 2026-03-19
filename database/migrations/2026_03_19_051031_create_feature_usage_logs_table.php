<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('feature_key');        // e.g. 'ai_generator', 'keyword_research'
            $table->string('feature_label');      // e.g. 'AI Generator', 'Keyword Research'
            $table->string('feature_category');   // e.g. 'AI Tools', 'Analytics', 'Content'
            $table->string('route_name')->nullable();
            $table->string('http_method', 10)->default('GET');
            $table->integer('duration_ms')->nullable(); // response time
            $table->boolean('success')->default(true);
            $table->string('subscription_package')->nullable(); // package name at time of use
            $table->date('usage_date');           // for daily aggregation
            $table->timestamps();

            $table->index(['user_id', 'feature_key']);
            $table->index(['feature_key', 'usage_date']);
            $table->index('usage_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_usage_logs');
    }
};
