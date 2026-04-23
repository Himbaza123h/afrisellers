<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('user_number')->index(); // For guest users
            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->json('selected_variations')->nullable(); // Store selected variations
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_carts');
    }
};
