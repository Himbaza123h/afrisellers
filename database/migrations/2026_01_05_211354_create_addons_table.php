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
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('cascade');
            $table->string('locationX')->comment('Location type: Homepage, About, etc.');
            $table->string('locationY')->comment('Section: herosection, featuredsuppliers, trendingproducts, etc.');
            $table->decimal('price', 15, 2)->default(0.00);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better query performance
            $table->index('country_id');
            $table->index(['locationX', 'locationY']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addons');
    }
};
