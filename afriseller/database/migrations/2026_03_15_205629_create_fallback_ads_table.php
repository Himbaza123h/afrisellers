<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fallback_ads', function (Blueprint $table) {
            $table->id();
            $table->string('position');          // homepage_header, homepage_right, company_profile, product_detail, article_detail
            $table->string('type');              // image, gif, video, text
            $table->string('media')->nullable(); // URL for image/gif/video
            $table->string('bg')->nullable();    // gradient string for text type
            $table->string('headline');
            $table->string('sub_text')->nullable();
            $table->string('cta_url')->default('#');
            $table->string('badge')->nullable();
            $table->string('overlay')->default('rgba(0,0,0,0.55)');
            $table->string('accent')->default('#ff0808');
            $table->boolean('pattern')->default(false); // dot pattern for text type
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fallback_ads');
    }
};
