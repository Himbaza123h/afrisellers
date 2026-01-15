<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_purchase_amount', 10, 2)->nullable();
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable()->comment('Total usage limit, null = unlimited');
            $table->integer('usage_count')->default(0);
            $table->integer('user_usage_limit')->nullable()->comment('Per user limit, null = unlimited');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('applicable_to', ['all', 'specific_products', 'specific_categories'])->default('all');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('currency', 10)->default('USD');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
