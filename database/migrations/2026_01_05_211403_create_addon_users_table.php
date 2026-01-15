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
        Schema::create('addon_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('addon_id')->constrained('addons')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['product', 'supplier', 'loadboad', 'car', 'showroom', 'tradeshow']);
            $table->timestamp('paid_at')->nullable();
            $table->integer('paid_days')->default(0)->comment('Number of days paid for');
            $table->timestamp('ended_at')->nullable();

            // Foreign keys for different types
            $table->foreignId('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->foreignId('supplier_id')->nullable()->constrained('business_profiles')->onDelete('cascade');
            $table->foreignId('loadboad_id')->nullable()->constrained('loads')->onDelete('cascade');
            $table->foreignId('car_id')->nullable()->constrained('cars')->onDelete('cascade');
            $table->foreignId('showroom_id')->nullable()->constrained('showrooms')->onDelete('cascade');
            $table->foreignId('tradeshow_id')->nullable()->constrained('tradeshows')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for better query performance
            $table->index('addon_id');
            $table->index('user_id');
            $table->index('type');
            $table->index(['type', 'product_id']);
            $table->index(['type', 'supplier_id']);
            $table->index(['type', 'loadboad_id']);
            $table->index(['type', 'car_id']);
            $table->index(['type', 'showroom_id']);
            $table->index(['type', 'tradeshow_id']);
            $table->index('paid_at');
            $table->index('ended_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addon_users');
    }
};
