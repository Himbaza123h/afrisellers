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
        Schema::create('escrows', function (Blueprint $table) {
            $table->id();
            $table->string('escrow_number')->unique();

            // Foreign Keys
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');

            // Financial Details
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('platform_fee', 10, 2)->default(0);
            $table->decimal('vendor_amount', 10, 2);
            $table->decimal('commission_amount', 10, 2)->default(0);

            // Status & Type
            $table->enum('status', [
                'pending',
                'active',
                'released',
                'refunded',
                'disputed',
                'cancelled'
            ])->default('pending');
            $table->enum('escrow_type', [
                'order',
                'service',
                'milestone',
                'custom'
            ])->default('order');

            // Release Conditions
            $table->enum('release_condition', [
                'auto_release',
                'manual_approval',
                'delivery_confirmation',
                'milestone_completion'
            ])->default('delivery_confirmation');
            $table->integer('auto_release_days')->nullable();
            $table->date('release_date')->nullable();

            // Timeline
            $table->timestamp('held_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamp('expected_release_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            // Approval & Verification
            $table->boolean('buyer_approved')->default(false);
            $table->timestamp('buyer_approved_at')->nullable();
            $table->boolean('vendor_confirmed')->default(false);
            $table->timestamp('vendor_confirmed_at')->nullable();
            $table->boolean('admin_approved')->default(false);
            $table->foreignId('admin_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('admin_approved_at')->nullable();

            // Dispute Management
            $table->boolean('disputed')->default(false);
            $table->text('dispute_reason')->nullable();
            $table->timestamp('dispute_opened_at')->nullable();
            $table->timestamp('dispute_resolved_at')->nullable();
            $table->text('dispute_resolution')->nullable();

            // Payment Details
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->string('release_method')->nullable();
            $table->string('release_reference')->nullable();

            // Terms & Conditions
            $table->text('terms')->nullable();
            $table->json('conditions_met')->nullable();
            $table->text('notes')->nullable();

            // Metadata
            $table->json('metadata')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('escrow_number');
            $table->index('status');
            $table->index('escrow_type');
            $table->index('buyer_id');
            $table->index('vendor_id');
            $table->index('transaction_id');
            $table->index('order_id');
            $table->index('disputed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escrows');
    }
};
