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
        Schema::create('agent_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Premium, Gold, Normal
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->integer('duration_days')->default(30); // Package duration

            // Features
            $table->integer('max_referrals')->default(5); // Maximum referrals allowed
            $table->boolean('allow_rfqs')->default(false); // Can access RFQs
            $table->boolean('priority_support')->default(false); // Priority support
            $table->boolean('advanced_analytics')->default(false); // Advanced reports
            $table->boolean('commission_boost')->default(false); // Higher commission rate
            $table->decimal('commission_rate', 5, 2)->default(5.00); // Commission percentage
            $table->boolean('featured_profile')->default(false); // Featured in listings
            $table->integer('max_payouts_per_month')->default(1); // Payout frequency

            // Status and ordering
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['slug', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_packages');
    }
};
