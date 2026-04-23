<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('type')->default('image'); // image, gif, video, text
            $table->string('position');
            // positions: homepage_header, homepage_right, homepage_sidebar,
            //            company_profile, product_detail
            $table->string('media_path')->nullable();  // image/gif/video file
            $table->string('media_url')->nullable();   // resolved URL
            $table->string('destination_url')->nullable();
            $table->string('headline')->nullable();
            $table->string('sub_text')->nullable();
            $table->string('badge_text')->nullable();
            $table->string('bg_gradient')->nullable(); // for text ads
            $table->string('accent_color')->default('#ff0808');
            $table->string('overlay_color')->nullable();

            // Sizing reference (stored for reference, actual rendering uses CSS)
            $table->integer('width')->nullable();   // e.g. 800
            $table->integer('height')->nullable();  // e.g. 99

            // Schedule
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('duration_days')->default(30);

            // Pricing
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->timestamp('paid_at')->nullable();

            // Status: draft, pending, approved, running, expired, rejected
            $table->string('status')->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            // Tracking
            $table->unsignedBigInteger('impressions')->default(0);
            $table->unsignedBigInteger('clicks')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['position', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
