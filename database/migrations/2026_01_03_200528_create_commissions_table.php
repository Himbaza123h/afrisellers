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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->onDelete('set null');
            $table->foreignId('buyer_id')->nullable()->constrained('buyers')->onDelete('set null');

            // Financial Columns
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('transaction_amount', 10, 2);
            $table->string('currency', 3)->default('USD');

            // Type & Status Columns
            $table->enum('commission_type', [
                'vendor_sale',
                'referral',
                'regional_admin',
                'platform_fee',
                'affiliate',
                'bonus'
            ]);
            $table->enum('status', [
                'pending',
                'approved',
                'paid',
                'cancelled'
            ])->default('pending');
            $table->enum('payment_status', [
                'unpaid',
                'processing',
                'paid',
                'failed'
            ])->default('unpaid');

            // Payment & Tracking Columns
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('transaction_id');
            $table->index('user_id');
            $table->index('vendor_id');
            $table->index('buyer_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('commission_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
