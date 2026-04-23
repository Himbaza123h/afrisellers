<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('loads', function (Blueprint $table) {
            $table->id();
            $table->string('load_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // shipper

            // Origin details
            $table->string('origin_address');
            $table->string('origin_city');
            $table->string('origin_state')->nullable();
            $table->foreignId('origin_country_id')->constrained('countries')->onDelete('cascade');
            $table->decimal('origin_latitude', 10, 8)->nullable();
            $table->decimal('origin_longitude', 11, 8)->nullable();

            // Destination details
            $table->string('destination_address');
            $table->string('destination_city');
            $table->string('destination_state')->nullable();
            $table->foreignId('destination_country_id')->constrained('countries')->onDelete('cascade');
            $table->decimal('destination_latitude', 10, 8)->nullable();
            $table->decimal('destination_longitude', 11, 8)->nullable();

            // Load details
            $table->string('cargo_type'); // electronics, perishables, machinery, etc
            $table->text('cargo_description');
            $table->decimal('weight', 10, 2); // in kg
            $table->string('weight_unit')->default('kg');
            $table->decimal('volume', 10, 2)->nullable(); // in cubic meters
            $table->string('volume_unit')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('packaging_type')->nullable(); // pallets, boxes, containers
            $table->json('special_requirements')->nullable(); // refrigeration, hazmat, etc

            // Timing
            $table->timestamp('pickup_date');
            $table->timestamp('pickup_time_start')->nullable();
            $table->timestamp('pickup_time_end')->nullable();
            $table->timestamp('delivery_date');
            $table->timestamp('delivery_time_start')->nullable();
            $table->timestamp('delivery_time_end')->nullable();

            // Pricing
            $table->decimal('budget', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->enum('pricing_type', ['fixed', 'negotiable', 'per_km', 'per_kg']);

            // Status & Assignment
            $table->enum('status', ['posted', 'bidding', 'assigned', 'in_transit', 'delivered', 'cancelled'])->default('posted');
            $table->foreignId('assigned_transporter_id')->nullable()->constrained('transporters')->nullOnDelete();
            $table->foreignId('winning_bid_id')->nullable()->constrained('load_bids')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Documents & Tracking
            $table->json('documents')->nullable(); // shipping docs, insurance
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index('load_number');
            $table->index(['origin_country_id', 'destination_country_id']);
            $table->index(['status', 'pickup_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('loads');
    }
};
