<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('showrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('showroom_number')->unique();

            // Basic Information
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('business_type')->nullable(); // Manufacturer, Dealer, Distributor
            $table->string('industry')->nullable(); // Logistics, Automotive, Construction
            $table->json('product_categories')->nullable(); // Trucks, Parts, Equipment

            // Location Details
            $table->string('address');
            $table->string('city');
            $table->string('state_province')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Operating Hours
            $table->json('operating_hours')->nullable(); // {monday: "9:00-18:00", tuesday: "9:00-18:00"}
            $table->boolean('open_weekends')->default(false);
            $table->boolean('appointment_required')->default(false);
            $table->boolean('walk_ins_welcome')->default(true);

            // Showroom Details
            $table->decimal('showroom_size_sqm', 10, 2)->nullable();
            $table->integer('display_capacity')->nullable(); // Number of vehicles/items on display
            $table->integer('current_inventory')->nullable();
            $table->boolean('has_service_center')->default(false);
            $table->boolean('has_parts_department')->default(false);
            $table->boolean('has_financing')->default(false);

            // Services Offered
            $table->json('services')->nullable(); // Sales, Leasing, Service, Parts, etc.
            $table->json('brands_carried')->nullable(); // Mercedes, Volvo, Scania, etc.
            $table->json('vehicle_types')->nullable(); // Trucks, Trailers, Vans, etc.
            $table->boolean('new_vehicles')->default(true);
            $table->boolean('used_vehicles')->default(true);

            // Media
            $table->json('images')->nullable();
            $table->string('primary_image')->nullable();
            $table->string('logo_image')->nullable();
            $table->json('videos')->nullable();
            $table->string('virtual_tour_url')->nullable();

            // Facilities & Features
            $table->json('facilities')->nullable(); // Parking, WiFi, Cafe, Test Drive, etc.
            $table->boolean('has_parking')->default(false);
            $table->integer('parking_spaces')->nullable();
            $table->boolean('wheelchair_accessible')->default(false);

            // Contact Information
            $table->string('contact_person')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('alternate_phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('website_url')->nullable();

            // Social Media
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('linkedin_url')->nullable();

            // Business Information
            $table->string('business_license')->nullable();
            $table->date('established_date')->nullable();
            $table->integer('years_in_business')->nullable();
            $table->integer('employees_count')->nullable();
            $table->json('certifications')->nullable(); // ISO, Dealer certifications, etc.
            $table->json('languages_spoken')->nullable();

            // Status & Visibility
            $table->enum('status', ['active', 'inactive', 'temporarily_closed', 'permanently_closed'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_authorized_dealer')->default(false);

            // Analytics
            $table->integer('views_count')->default(0);
            $table->integer('inquiries_count')->default(0);
            $table->integer('visits_count')->default(0); // Physical visits logged
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('reviews_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['country_id', 'city']);
            $table->index('status');
            $table->index(['is_featured', 'is_verified']);
            $table->fullText(['name', 'description', 'city']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('showrooms');
    }
};
