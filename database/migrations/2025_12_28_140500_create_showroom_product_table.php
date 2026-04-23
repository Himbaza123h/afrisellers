<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('showroom_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('showroom_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('added_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Prevent duplicate products in same showroom
            $table->unique(['showroom_id', 'product_id']);

            // Indexes for better query performance
            $table->index(['user_id', 'showroom_id']);
            $table->index('product_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('showroom_product');
    }
};
