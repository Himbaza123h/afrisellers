<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. PRODUCT ANALYTICS ─────────────────────────────
        Schema::create('product_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // who viewed

            // Traffic
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('unique_views')->default(0);
            $table->unsignedBigInteger('impressions')->default(0);    // appeared in listings
            $table->unsignedBigInteger('clicks')->default(0);         // clicked from listing

            // Engagement
            $table->unsignedBigInteger('likes')->default(0);
            $table->unsignedBigInteger('shares')->default(0);
            $table->unsignedBigInteger('wishlist_adds')->default(0);
            $table->unsignedBigInteger('cart_adds')->default(0);
            $table->unsignedBigInteger('rfq_count')->default(0);      // quote requests

            // Orders
            $table->unsignedBigInteger('order_count')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);

            // Reviews
            $table->unsignedBigInteger('review_count')->default(0);
            $table->decimal('avg_rating', 3, 2)->default(0);

            // Video (if product has video)
            $table->unsignedBigInteger('video_views')->default(0);
            $table->unsignedBigInteger('video_watch_time')->default(0); // total seconds watched

            // Date for daily snapshots
            $table->date('recorded_date')->nullable();                  // null = all-time total
            $table->string('period')->default('alltime');               // alltime|daily|weekly|monthly

            $table->timestamps();

            $table->index(['product_id', 'recorded_date']);
            $table->index(['product_id', 'period']);
        });

        // ── 2. PROFILE ANALYTICS ─────────────────────────────
        Schema::create('profile_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_profile_id')->constrained()->cascadeOnDelete();

            // Traffic
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('unique_visitors')->default(0);
            $table->unsignedBigInteger('shares')->default(0);

            // Engagement
            $table->unsignedBigInteger('likes')->default(0);
            $table->unsignedBigInteger('followers')->default(0);
            $table->unsignedBigInteger('contact_clicks')->default(0);   // "Contact" button clicks
            $table->unsignedBigInteger('whatsapp_clicks')->default(0);
            $table->unsignedBigInteger('website_clicks')->default(0);
            $table->unsignedBigInteger('rfq_count')->default(0);

            // Watch time (if profile has videos/intro)
            $table->unsignedBigInteger('video_views')->default(0);
            $table->unsignedBigInteger('video_watch_time')->default(0); // seconds

            // Snapshot
            $table->date('recorded_date')->nullable();
            $table->string('period')->default('alltime');

            $table->timestamps();

            $table->index(['business_profile_id', 'recorded_date']);
            $table->index(['business_profile_id', 'period']);
        });

        // ── 3. ARTICLE ANALYTICS ─────────────────────────────
        Schema::create('article_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->cascadeOnDelete();

            // Traffic
            $table->unsignedBigInteger('views')->default(0);
            $table->unsignedBigInteger('unique_views')->default(0);
            $table->unsignedBigInteger('shares')->default(0);

            // Engagement
            $table->unsignedBigInteger('likes')->default(0);
            $table->unsignedBigInteger('comments_count')->default(0);
            $table->unsignedBigInteger('bookmarks')->default(0);

            // Reading behaviour
            $table->unsignedBigInteger('total_read_time')->default(0);  // total seconds across all readers
            $table->decimal('avg_read_time', 8, 2)->default(0);         // average seconds per read
            $table->decimal('completion_rate', 5, 2)->default(0);       // % who finished reading

            // Snapshot
            $table->date('recorded_date')->nullable();
            $table->string('period')->default('alltime');

            $table->timestamps();

            $table->index(['article_id', 'recorded_date']);
            $table->index(['article_id', 'period']);
        });

        // ── 4. VENDOR ANALYTICS ──────────────────────────────
        Schema::create('vendor_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();

            // Store traffic
            $table->unsignedBigInteger('store_visits')->default(0);
            $table->unsignedBigInteger('unique_visitors')->default(0);

            // Products
            $table->unsignedBigInteger('total_product_views')->default(0);
            $table->unsignedBigInteger('total_impressions')->default(0);

            // Orders & Revenue
            $table->unsignedBigInteger('total_orders')->default(0);
            $table->unsignedBigInteger('completed_orders')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->unsignedBigInteger('total_customers')->default(0);
            $table->unsignedBigInteger('repeat_customers')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);       // visits → orders %

            // Engagement
            $table->unsignedBigInteger('rfq_count')->default(0);
            $table->unsignedBigInteger('total_likes')->default(0);
            $table->unsignedBigInteger('total_shares')->default(0);
            $table->unsignedBigInteger('followers')->default(0);

            // Video / media
            $table->unsignedBigInteger('video_views')->default(0);
            $table->unsignedBigInteger('video_watch_time')->default(0); // seconds

            // Snapshot
            $table->date('recorded_date')->nullable();
            $table->string('period')->default('alltime');

            $table->timestamps();

            $table->index(['vendor_id', 'recorded_date']);
            $table->index(['vendor_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_analytics');
        Schema::dropIfExists('article_analytics');
        Schema::dropIfExists('profile_analytics');
        Schema::dropIfExists('product_analytics');
    }
};
