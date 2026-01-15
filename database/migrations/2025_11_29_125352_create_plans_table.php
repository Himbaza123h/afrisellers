<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('price')->nullable();
            $table->string('currency', 3)->default('RWF');
            $table->string('billing_cycle')->default('month');
            $table->text('description')->nullable();
            $table->boolean('featured_products')->default(false);
            $table->integer('product_limit')->nullable();
            $table->integer('buyer_inquiries_limit')->default(5);
            $table->integer('buyer_rfqs_limit')->default(5);
            $table->boolean('has_ads')->default(false);
            $table->boolean('negotiable')->default(false);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
