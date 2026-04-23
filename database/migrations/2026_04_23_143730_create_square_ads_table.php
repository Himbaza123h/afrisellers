<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('square_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('library_id')->constrained('ad_media')->onDelete('cascade');
            $table->string('type', 80)->nullable();        // free-text label e.g. "promo", "banner"
            $table->string('headline', 255);
            $table->string('sub_text', 255)->nullable();
            $table->string('cta_url', 255)->nullable();
            $table->string('badge', 50)->nullable();
            $table->string('accent', 30)->default('#ff0808');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('square_ads');
    }
};
