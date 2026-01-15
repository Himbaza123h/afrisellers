<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('listing_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Vehicle Details
            $table->string('make'); // Toyota, Mercedes, Isuzu, etc.
            $table->string('model'); // Hilux, Sprinter, FTR, etc.
            $table->year('year');
            $table->string('vehicle_type'); // Pickup Truck, Cargo Van, Box Truck, Flatbed, etc.
            $table->string('condition')->default('used'); // new, used, refurbished

            // Specifications
            $table->string('transmission')->nullable(); // Manual, Automatic
            $table->string('fuel_type')->nullable(); // Diesel, Petrol, Electric, Hybrid
            $table->string('engine_capacity')->nullable(); // e.g., 2.8L, 3.0L
            $table->integer('mileage')->nullable(); // in kilometers
            $table->string('color')->nullable();
            $table->string('vin')->nullable(); // Vehicle Identification Number
            $table->integer('seats')->nullable();
            $table->decimal('cargo_capacity', 10, 2)->nullable(); // Load capacity
            $table->string('cargo_capacity_unit')->default('tons'); // tons, cubic_meters, kg

            // Current Location (Where the vehicle is based/available from)
            $table->string('from_city')->nullable();
            $table->string('from_state')->nullable();
            $table->foreignId('from_country_id')->constrained('countries')->onDelete('cascade');
            $table->decimal('from_latitude', 10, 8)->nullable();
            $table->decimal('from_longitude', 11, 8)->nullable();

            // Available Destination (Where the vehicle can go, optional - can be flexible)
            $table->string('to_city')->nullable();
            $table->string('to_state')->nullable();
            $table->foreignId('to_country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->decimal('to_latitude', 10, 8)->nullable();
            $table->decimal('to_longitude', 11, 8)->nullable();

            // Route flexibility
            $table->boolean('flexible_destination')->default(true); // Can go anywhere
            $table->json('preferred_routes')->nullable(); // Array of preferred routes/regions

            // Pricing & Availability
            $table->decimal('price', 15, 2)->nullable(); // Price per trip/km/day
            $table->string('pricing_type')->default('negotiable'); // per_trip, per_km, per_day, per_ton, negotiable
            $table->string('currency', 3)->default('USD');
            $table->boolean('price_negotiable')->default(true);
            $table->date('available_from')->nullable();
            $table->date('available_until')->nullable();

            // Features & Capabilities
            $table->json('features')->nullable(); // GPS, Refrigeration, Tarpaulin, Crane, etc.
            $table->text('description')->nullable();
            $table->json('images')->nullable(); // Array of image URLs
            $table->json('documents')->nullable(); // Registration, insurance, permits

            // Availability Status
            $table->enum('availability_status', ['available', 'on_trip', 'maintenance', 'reserved', 'inactive'])->default('available');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();

            // Insurance & Compliance
            $table->boolean('has_insurance')->default(false);
            $table->date('insurance_expiry')->nullable();
            $table->boolean('has_registration')->default(false);
            $table->date('registration_expiry')->nullable();
            $table->boolean('has_goods_transit_insurance')->default(false);
            $table->json('permits')->nullable(); // Cross-border permits, special cargo permits

            // Driver Information
            $table->boolean('driver_included')->default(true);
            $table->string('driver_experience')->nullable(); // years of experience
            $table->json('driver_languages')->nullable(); // Languages spoken

            // Cargo Specifications
            $table->json('accepted_cargo_types')->nullable(); // General, Refrigerated, Hazardous, Livestock, etc.
            $table->decimal('max_weight', 10, 2)->nullable(); // Maximum weight in tons
            $table->decimal('max_volume', 10, 2)->nullable(); // Maximum volume in cubic meters
            $table->json('dimensions')->nullable(); // Length, width, height

            // Additional Info
            $table->integer('views_count')->default(0);
            $table->integer('inquiries_count')->default(0);
            $table->integer('completed_trips')->default(0);
            $table->decimal('rating', 3, 2)->default(0.00); // Average rating
            $table->integer('reviews_count')->default(0);
            $table->timestamp('listed_at')->nullable();
            $table->timestamp('last_trip_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['availability_status', 'is_verified']);
            $table->index(['from_country_id', 'from_city']);
            $table->index(['to_country_id', 'to_city']);
            $table->index(['make', 'model', 'year']);
            $table->index(['vehicle_type', 'cargo_capacity']);
            $table->index(['price', 'currency', 'pricing_type']);
            $table->index(['available_from', 'available_until']);
            $table->index('user_id');
            $table->index('flexible_destination');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cars');
    }
};
