<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45); // IPv6 support
            $table->enum('type', ['click', 'impression']);
            $table->date('tracked_date'); // Only date, not time
            $table->timestamps();

            // Prevent same IP from tracking same product multiple times per day
            $table->unique(['product_id', 'ip_address', 'type', 'tracked_date'], 'unique_daily_tracking');

            $table->index(['product_id', 'tracked_date']);
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_logs');
    }
};
