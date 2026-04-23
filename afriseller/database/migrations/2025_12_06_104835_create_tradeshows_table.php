<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tradeshows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('tradeshow_number')->unique();

            // Basic Information
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('industry')->nullable(); // e.g., Logistics, Automotive, Agriculture
            $table->string('category')->nullable(); // e.g., B2B, B2C, Mixed

            // Location Details
            $table->string('venue_name');
            $table->string('venue_address');
            $table->string('city');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Date & Time
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('timezone')->default('UTC');

            // Capacity & Size
            $table->integer('expected_visitors')->nullable();
            $table->integer('expected_exhibitors')->nullable();
            $table->integer('total_booths')->nullable();
            $table->integer('available_booths')->nullable();
            $table->decimal('venue_size_sqm', 10, 2)->nullable(); // Square meters

            // Pricing
            $table->decimal('booth_price_from', 10, 2)->nullable();
            $table->decimal('booth_price_to', 10, 2)->nullable();
            $table->string('pricing_currency', 3)->default('USD');
            $table->decimal('visitor_ticket_price', 10, 2)->nullable();
            $table->boolean('free_entry')->default(false);

            // Registration
            $table->boolean('registration_required')->default(true);
            $table->date('registration_deadline')->nullable();
            $table->string('registration_url')->nullable();
            $table->string('website_url')->nullable();

            // Media
            $table->json('images')->nullable();
            $table->string('banner_image')->nullable();
            $table->string('logo_image')->nullable();
            $table->json('videos')->nullable();
            $table->json('documents')->nullable(); // Floor plans, brochures, etc.

            // Additional Features
            $table->json('features')->nullable(); // WiFi, Parking, Food, etc.
            $table->json('exhibitor_types')->nullable(); // Manufacturers, Distributors, etc.
            $table->json('target_audience')->nullable();
            $table->text('special_attractions')->nullable(); // Keynotes, demos, etc.

            // Contact Information
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('organizer_name')->nullable();
            $table->string('organizer_website')->nullable();

            // Status & Visibility
            $table->enum('status', ['draft', 'published', 'ongoing', 'completed', 'cancelled'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable(); // Annual, Bi-annual, etc.

            // Analytics
            $table->integer('views_count')->default(0);
            $table->integer('inquiries_count')->default(0);
            $table->integer('bookings_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['country_id', 'city']);
            $table->index('start_date');
            $table->index('status');
            $table->index(['is_featured', 'is_verified']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tradeshows');
    }
};
