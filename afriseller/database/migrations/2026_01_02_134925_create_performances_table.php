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
        Schema::create('performances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('vendor_id')
                ->nullable()
                ->constrained('business_profiles')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Performance metrics with defaults
            $table->unsignedBigInteger('clicks')->default(0);
            $table->unsignedBigInteger('impressions')->default(0);

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better query performance
            $table->index('product_id');
            $table->index('vendor_id');
            $table->index('country_id');
            $table->index(['product_id', 'vendor_id']);
            $table->index(['vendor_id', 'country_id']);
            $table->index(['product_id', 'country_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performances');
    }
};
